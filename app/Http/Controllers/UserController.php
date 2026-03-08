<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%")
                        ->orWhere('username', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->where('role', $request->role);
            })
            ->when($request->filled('telegram_status'), function ($q) use ($request) {
                if ($request->telegram_status === 'connected') {
                    $q->whereNotNull('telegram_id');
                } else {
                    $q->whereNull('telegram_id');
                }
            })
            ->latest()
            ->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', Rule::in(['super_admin', 'admin', 'purchase', 'gudang'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', Rule::in(['super_admin', 'admin', 'purchase', 'gudang'])],
        ]);

        $user->name     = $validated['name'];
        $user->username = $validated['username'];
        $user->role     = $validated['role'];

        // Update password hanya jika diisi
        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Reset telegram_id jika diminta
        if ($request->boolean('reset_telegram')) {
            $user->telegram_id = null;
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', "User \"{$user->name}\" berhasil diperbarui.");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Mencegah user menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Cek relasi ke Purchase Orders
        if ($user->purchaseOrders()->exists()) {
            return redirect()->route('users.index')
                ->with('error', "User \"{$user->name}\" tidak dapat dihapus karena memiliki data Purchase Order terkait.");
        }

        // Cek relasi ke Transactions
        if ($user->transactions()->exists()) {
            return redirect()->route('users.index')
                ->with('error', "User \"{$user->name}\" tidak dapat dihapus karena memiliki data Transaksi terkait.");
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User \"{$name}\" berhasil dihapus.");
    }
}

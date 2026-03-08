<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource (with filters).
     */
    public function index(Request $request): View
    {
        $vendors = Vendor::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_vendor', 'like', "%{$request->search}%")
                        ->orWhere('kontak', 'like', "%{$request->search}%")
                        ->orWhere('alamat', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->get();

        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:255'],
            'kontak'      => ['nullable', 'string', 'max:255'],
            'alamat'      => ['nullable', 'string'],
        ]);

        Vendor::create($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor): View
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $request->validate([
            'nama_vendor' => ['required', 'string', 'max:255'],
            'kontak'      => ['nullable', 'string', 'max:255'],
            'alamat'      => ['nullable', 'string'],
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor): RedirectResponse
    {
        try {
            $vendor->delete();

            return redirect()->route('vendors.index')
                ->with('success', 'Vendor berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->with('error', 'Vendor gagal dihapus karena sedang digunakan dalam riwayat purchase order!');
            }

            throw $e;
        }
    }

    /**
     * Export data vendor ke PDF (with active filters).
     */
    public function exportPdf(Request $request)
    {
        $vendors = Vendor::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_vendor', 'like', "%{$request->search}%")
                        ->orWhere('kontak', 'like', "%{$request->search}%")
                        ->orWhere('alamat', 'like', "%{$request->search}%");
                });
            })
            ->orderBy('nama_vendor', 'asc')
            ->get();

        $pdf = Pdf::loadView('vendors.pdf', compact('vendors'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Data_Vendor_PT_MULTYARTHA.pdf');
    }
}

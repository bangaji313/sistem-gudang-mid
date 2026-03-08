<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource (with filters).
     */
    public function index(Request $request): View
    {
        $divisis = Divisi::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_divisi_tukang', 'like', "%{$request->search}%")
                        ->orWhere('keterangan', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->get();

        return view('divisis.index', compact('divisis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('divisis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_divisi_tukang' => ['required', 'string', 'max:255'],
            'keterangan'         => ['nullable', 'string'],
        ]);

        Divisi::create($validated);

        return redirect()->route('divisis.index')
            ->with('success', 'Divisi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Divisi $divisi): View
    {
        return view('divisis.edit', compact('divisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Divisi $divisi): RedirectResponse
    {
        $validated = $request->validate([
            'nama_divisi_tukang' => ['required', 'string', 'max:255'],
            'keterangan'         => ['nullable', 'string'],
        ]);

        $divisi->update($validated);

        return redirect()->route('divisis.index')
            ->with('success', 'Divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Divisi $divisi): RedirectResponse
    {
        try {
            $divisi->delete();

            return redirect()->route('divisis.index')
                ->with('success', 'Divisi berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->with('error', 'Divisi gagal dihapus karena sedang digunakan dalam riwayat transaksi!');
            }

            throw $e;
        }
    }

    /**
     * Export data divisi ke PDF (with active filters).
     */
    public function exportPdf(Request $request)
    {
        $divisis = Divisi::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_divisi_tukang', 'like', "%{$request->search}%")
                        ->orWhere('keterangan', 'like', "%{$request->search}%");
                });
            })
            ->orderBy('nama_divisi_tukang', 'asc')
            ->get();

        $pdf = Pdf::loadView('divisis.pdf', compact('divisis'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Data_Divisi_PT_MULTYARTHA.pdf');
    }
}

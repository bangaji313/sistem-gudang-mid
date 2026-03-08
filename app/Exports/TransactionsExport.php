<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private int $rowNumber = 0;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Transaction::with(['item.uom', 'divisi', 'user'])
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Tipe',
            'Barang',
            'Qty',
            'Divisi',
            'Oleh',
            'Keterangan',
        ];
    }

    /**
     * @param  \App\Models\Transaction  $trx
     * @return array
     */
    public function map($trx): array
    {
        $tipeLabel = match ($trx->tipe_transaksi) {
            'keluar'       => 'Barang Keluar',
            'kembali_sisa' => 'Kembali Sisa',
            'retur_cacat'  => 'Retur Cacat',
            default        => $trx->tipe_transaksi,
        };

        return [
            ++$this->rowNumber,
            Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y H:i'),
            $tipeLabel,
            "{$trx->item->kode_barang} — {$trx->item->nama_barang}",
            $trx->qty,
            $trx->divisi?->nama_divisi_tukang ?? '-',
            $trx->user->name ?? '-',
            $trx->keterangan ?? '-',
        ];
    }

    /**
     * Style header row.
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }
}

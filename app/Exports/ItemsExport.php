<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private int $rowNumber = 0;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Item::with('uom')->orderBy('kode_barang', 'asc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Nama Barang',
            'Merk',
            'Satuan',
            'Stok',
        ];
    }

    /**
     * @param  \App\Models\Item  $item
     * @return array
     */
    public function map($item): array
    {
        return [
            ++$this->rowNumber,
            $item->kode_barang,
            $item->nama_barang,
            $item->merk ?? '-',
            $item->uom->nama_satuan ?? '-',
            $item->stok,
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

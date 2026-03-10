<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PurchaseOrdersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithCustomStartCell,
    WithDrawings,
    WithEvents,
    ShouldAutoSize
{
    private int $rowNumber = 0;

    /**
     * Query semua PO beserta relasi vendor & user (pembuat).
     */
    public function collection()
    {
        return PurchaseOrder::with(['vendor', 'user'])
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Mulai tabel data dari sel A8 — baris 1-7 untuk kop surat.
     */
    public function startCell(): string
    {
        return 'A8';
    }

    /**
     * Sisipkan logo perusahaan di pojok kiri atas (A1).
     */
    public function drawings(): Drawing
    {
        $drawing = new Drawing();
        $drawing->setName('Logo PT MID');
        $drawing->setDescription('Logo PT Multyartha Indo Daya');
        $drawing->setPath(public_path('images/logo.png'));
        $drawing->setHeight(60);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    /**
     * Header kolom tabel data.
     */
    public function headings(): array
    {
        return [
            'No',
            'Nomor PO',
            'Vendor',
            'Tgl PO',
            'Tipe',
            'Pesanan',
            'Bayar',
            'Grand Total',
            'Sisa Tagihan',
            'Dibuat Oleh',
        ];
    }

    /**
     * Mapping setiap baris PO sesuai urutan heading.
     */
    public function map($po): array
    {
        return [
            ++$this->rowNumber,
            $po->nomor_po,
            $po->vendor->nama_vendor ?? '-',
            Carbon::parse($po->tanggal_po)->format('d/m/Y'),
            $po->tipe_po,
            $po->status_pesanan,
            $po->status_pembayaran,
            $po->grand_total,
            $po->sisa_pembayaran,
            $po->user->name ?? '-',
        ];
    }

    /**
     * Event AfterSheet — tulis kop surat & style header tabel.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Kop Surat ──────────────────────────────────────
                $sheet->setCellValue('B1', 'PT MULTYARTHA INDO DAYA');
                $sheet->setCellValue('B2', 'Surya Asri Residence 9A, Cipagalo Bojongsoang, Kabupaten Bandung');
                $sheet->setCellValue('B3', 'Email : multyarthaindodaya@gmail.com');

                // Style nama perusahaan (B1) — bold, size 16
                $sheet->getStyle('B1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                    ],
                ]);

                // Style alamat & email (B2:B3) — size 10
                $sheet->getStyle('B2:B3')->applyFromArray([
                    'font' => [
                        'size' => 10,
                    ],
                ]);

                // ── Info Laporan ────────────────────────────────────
                $sheet->setCellValue('A6', 'Laporan: Data Purchase Order');
                $sheet->setCellValue('A7', 'Tanggal Cetak: ' . now()->format('d F Y - H:i') . ' WIB');

                $sheet->getStyle('A6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                ]);
                $sheet->getStyle('A7')->applyFromArray([
                    'font' => ['size' => 10],
                ]);

                // ── Style Header Tabel (A8:J8) — Dark Blue + White ─
                $headerRange = 'A8:J8';

                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size'  => 11,
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E3A8A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ── Format kolom Grand Total & Sisa Tagihan sebagai angka ─
                $lastRow = $sheet->getHighestRow();
                if ($lastRow > 8) {
                    $dataRange = 'H9:I' . $lastRow;
                    $sheet->getStyle($dataRange)->getNumberFormat()
                          ->setFormatCode('#,##0');
                }

                // ── Set lebar kolom A (No) agar tidak terlalu lebar ─
                $sheet->getColumnDimension('A')->setWidth(5);
            },
        ];
    }
}

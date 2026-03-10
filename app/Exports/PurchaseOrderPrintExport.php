<?php

namespace App\Exports;

use App\Helpers\Terbilang;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PurchaseOrderPrintExport implements WithEvents, WithDrawings
{
    private PurchaseOrder $po;

    public function __construct(PurchaseOrder $po)
    {
        $this->po = $po;
    }

    /**
     * Sisipkan logo perusahaan di sel A1.
     */
    public function drawings(): Drawing
    {
        $drawing = new Drawing();
        $drawing->setName('Logo PT MID');
        $drawing->setDescription('Logo PT Multyartha Indo Daya');
        $drawing->setPath(public_path('images/logo.png'));
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    /**
     * Gunakan AfterSheet event untuk menggambar seluruh layout Kontra Bon.
     */
    public function registerEvents(): array
    {
        $po = $this->po;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($po) {
                $sheet = $event->sheet->getDelegate();

                // ─────────────────────────────────────────────
                // DEFAULT FONT & PAGE SETUP
                // ─────────────────────────────────────────────
                $sheet->getParent()->getDefaultStyle()->getFont()
                    ->setName('Courier New')
                    ->setSize(10);

                $sheet->getPageSetup()
                    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(PageSetup::PAPERSIZE_STATEMENT)
                    ->setFitToWidth(1)
                    ->setFitToHeight(1);

                $sheet->getPageMargins()
                    ->setTop(0.3)
                    ->setRight(0.3)
                    ->setBottom(0.3)
                    ->setLeft(0.3);

                // ─────────────────────────────────────────────
                // LEBAR KOLOM — presisi sesuai spesifikasi
                // ─────────────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(5)->setAutoSize(false);   // No
                $sheet->getColumnDimension('B')->setWidth(15)->setAutoSize(false);  // Tgl PO
                $sheet->getColumnDimension('C')->setWidth(15)->setAutoSize(false);  // Tgl Delivery
                $sheet->getColumnDimension('D')->setWidth(25)->setAutoSize(false);  // No Invoice
                $sheet->getColumnDimension('E')->setWidth(15)->setAutoSize(false);  // Tgl Invoice
                $sheet->getColumnDimension('F')->setWidth(15)->setAutoSize(false);  // Tgl Diterima
                $sheet->getColumnDimension('G')->setWidth(20)->setAutoSize(false);  // Tgl Tempo Pembayaran
                $sheet->getColumnDimension('H')->setWidth(20)->setAutoSize(false);  // Total Harga

                // ─────────────────────────────────────────────
                // KOP SURAT — Baris 1-3 (rata kanan kolom D-H)
                // ─────────────────────────────────────────────
                $sheet->mergeCells('D1:H1');
                $sheet->setCellValue('D1', 'PT MULTYARTHA INDO DAYA');
                $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle('D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->mergeCells('D2:H2');
                $sheet->setCellValue('D2', 'Jl. Telekomunikasi No.1 Kec. Bojongsoang Kab. Bandung');
                $sheet->getStyle('D2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D2')->getFont()->setSize(9);

                $sheet->mergeCells('D3:H3');
                $sheet->setCellValue('D3', '08131438939 / adm@multyarthaindodaya.com');
                $sheet->getStyle('D3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D3')->getFont()->setSize(9);

                // Garis pemisah bawah kop
                $sheet->getStyle('A4:H4')->getBorders()->getBottom()
                    ->setBorderStyle(Border::BORDER_DASHED);

                // ─────────────────────────────────────────────
                // META INFO (Kiri Baris 5-7) + JUDUL (Kanan Baris 5)
                // Label merge A:B, Nilai merge C:F (atau C:H untuk Proyek)
                // ─────────────────────────────────────────────
                $sheet->mergeCells('A5:B5');
                $sheet->setCellValue('A5', 'No. Kontra Bon');
                $sheet->mergeCells('C5:F5');
                $sheet->setCellValue('C5', ':  ' . $po->nomor_po);
                $sheet->getStyle('C5')->getFont()->setBold(true);

                $sheet->mergeCells('A6:B6');
                $sheet->setCellValue('A6', 'Tanggal');
                $sheet->mergeCells('C6:F6');
                $tglKontraBon = $po->tgl_kontra_bon
                    ? Carbon::parse($po->tgl_kontra_bon)->format('d/m/Y')
                    : '-';
                $sheet->setCellValue('C6', ':  ' . $tglKontraBon);

                $sheet->mergeCells('A7:B7');
                $sheet->setCellValue('A7', 'Proyek');
                $sheet->mergeCells('C7:H7');
                $sheet->setCellValue('C7', ':  ' . ($po->proyek ?? '-'));
                $sheet->getStyle('C7:H7')->getAlignment()->setWrapText(true);

                // Judul rata kanan
                $sheet->mergeCells('G5:H5');
                $sheet->setCellValue('G5', 'KONTRA BON');
                $sheet->getStyle('G5')->getFont()->setBold(true)->setSize(18);
                $sheet->getStyle('G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // ─────────────────────────────────────────────
                // INFO VENDOR (Baris 9-11)
                // Label merge A:B, Nilai merge C:H
                // ─────────────────────────────────────────────
                $sheet->mergeCells('A9:B9');
                $sheet->setCellValue('A9', 'Kepada Yth,');
                $sheet->getStyle('A9')->getFont()->setItalic(true);

                $sheet->mergeCells('A10:B10');
                $sheet->setCellValue('A10', 'Nama Vendor');
                $sheet->mergeCells('C10:H10');
                $sheet->setCellValue('C10', ':  ' . $po->vendor->nama_vendor);

                $sheet->mergeCells('A11:B11');
                $sheet->setCellValue('A11', 'Alamat Vendor');
                $sheet->mergeCells('C11:H11');
                $sheet->setCellValue('C11', ':  ' . ($po->vendor->alamat ?? ($po->vendor->kontak ?? '-')));
                $sheet->getStyle('C11:H11')->getAlignment()->setWrapText(true);

                // ─────────────────────────────────────────────
                // TABEL HEADER (Baris 13) — 8 kolom Kontra Bon
                // ─────────────────────────────────────────────
                $headerRow = 13;
                $headers = [
                    'A' => 'No',
                    'B' => 'Tgl PO',
                    'C' => 'Tgl Delivery',
                    'D' => 'No Invoice',
                    'E' => 'Tgl Invoice',
                    'F' => 'Tgl Diterima',
                    'G' => 'Tgl Tempo Pembayaran',
                    'H' => 'Total Harga',
                ];

                foreach ($headers as $col => $label) {
                    $sheet->setCellValue("{$col}{$headerRow}", $label);
                }

                // Style header: bold, center, wrap text
                $headerRange = "A{$headerRow}:H{$headerRow}";
                $sheet->getStyle($headerRange)->getFont()->setBold(true)->setSize(9);
                $sheet->getStyle($headerRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setWrapText(true);

                // Border DASHED hanya ATAS dan BAWAH header
                $sheet->getStyle($headerRange)->getBorders()->getTop()
                    ->setBorderStyle(Border::BORDER_DASHED);
                $sheet->getStyle($headerRange)->getBorders()->getBottom()
                    ->setBorderStyle(Border::BORDER_DASHED);

                // ─────────────────────────────────────────────
                // DATA — 1 baris rangkuman PO (TANPA border)
                // ─────────────────────────────────────────────
                $dataRow = $headerRow + 1;

                // A: No
                $sheet->setCellValue("A{$dataRow}", 1);
                $sheet->getStyle("A{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // B: Tgl PO
                $sheet->setCellValue("B{$dataRow}", Carbon::parse($po->tanggal_po)->format('d/m/Y'));
                $sheet->getStyle("B{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // C: Tgl Delivery
                $tglDelivery = $po->tgl_delivery
                    ? Carbon::parse($po->tgl_delivery)->format('d/m/Y')
                    : '-';
                $sheet->setCellValue("C{$dataRow}", $tglDelivery);
                $sheet->getStyle("C{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // D: No Invoice
                $sheet->setCellValue("D{$dataRow}", $po->no_invoice ?? '-');
                $sheet->getStyle("D{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // E: Tgl Invoice
                $tglInvoice = $po->tgl_invoice
                    ? Carbon::parse($po->tgl_invoice)->format('d/m/Y')
                    : '-';
                $sheet->setCellValue("E{$dataRow}", $tglInvoice);
                $sheet->getStyle("E{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // F: Tgl Diterima
                $tglDiterima = $po->tgl_diterima
                    ? Carbon::parse($po->tgl_diterima)->format('d/m/Y')
                    : '-';
                $sheet->setCellValue("F{$dataRow}", $tglDiterima);
                $sheet->getStyle("F{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // G: Tgl Tempo — Lunas = "-", DP/Tempo = tanggal_jatuh_tempo
                $tglTempo = '-';
                if ($po->status_pembayaran !== 'Lunas' && $po->tanggal_jatuh_tempo) {
                    $tglTempo = Carbon::parse($po->tanggal_jatuh_tempo)->format('d/m/Y');
                }
                $sheet->setCellValue("G{$dataRow}", $tglTempo);
                $sheet->getStyle("G{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // H: Total Harga (Grand Total, format Rupiah)
                $sheet->setCellValue("H{$dataRow}", $po->grand_total);
                $sheet->getStyle("H{$dataRow}")->getNumberFormat()
                    ->setFormatCode('#,##0');
                $sheet->getStyle("H{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // ═══ TIDAK ADA border di bawah baris data ═══

                // ─────────────────────────────────────────────
                // TOTAL, DISKON, GRAND TOTAL (polos, tanpa border)
                // ─────────────────────────────────────────────
                $currentRow = $dataRow + 2; // 1 baris kosong

                // Total Harga
                $sheet->setCellValue("G{$currentRow}", 'Total Harga');
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue("H{$currentRow}", $po->total_harga);
                $sheet->getStyle("H{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;

                // Diskon (jika ada)
                if ($po->diskon > 0) {
                    $sheet->setCellValue("G{$currentRow}", 'Diskon');
                    $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue("H{$currentRow}", $po->diskon);
                    $sheet->getStyle("H{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $currentRow++;
                }

                // Grand Total — Bold, tanpa border
                $sheet->setCellValue("G{$currentRow}", 'Grand Total');
                $sheet->getStyle("G{$currentRow}")->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue("H{$currentRow}", $po->grand_total);
                $sheet->getStyle("H{$currentRow}")->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("H{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $currentRow++;

                // DP & Sisa (jika relevan)
                if ($po->status_pembayaran === 'DP' && $po->nominal_dp > 0) {
                    $sheet->setCellValue("G{$currentRow}", 'Nominal DP');
                    $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue("H{$currentRow}", $po->nominal_dp);
                    $sheet->getStyle("H{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $currentRow++;
                }

                if ($po->sisa_pembayaran > 0) {
                    $sheet->setCellValue("G{$currentRow}", 'Sisa Tagihan');
                    $sheet->getStyle("G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->setCellValue("H{$currentRow}", $po->sisa_pembayaran);
                    $sheet->getStyle("H{$currentRow}")->getFont()->setBold(true);
                    $sheet->getStyle("H{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("H{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $currentRow++;
                }

                // ─────────────────────────────────────────────
                // TERBILANG
                // ─────────────────────────────────────────────
                $currentRow++; // baris kosong
                $terbilang = Terbilang::make($po->grand_total);

                $sheet->mergeCells("A{$currentRow}:H{$currentRow}");
                $sheet->setCellValue("A{$currentRow}", 'Terbilang: ' . $terbilang . ' Rupiah');
                $sheet->getStyle("A{$currentRow}")->getFont()->setItalic(true)->setSize(9);
                $sheet->getStyle("A{$currentRow}:H{$currentRow}")->getBorders()->getTop()
                    ->setBorderStyle(Border::BORDER_DASHED);
                $sheet->getStyle("A{$currentRow}:H{$currentRow}")->getBorders()->getBottom()
                    ->setBorderStyle(Border::BORDER_DASHED);
                $currentRow++;

                // ─────────────────────────────────────────────
                // TANDA TANGAN (3 baris jarak)
                // ─────────────────────────────────────────────
                $currentRow += 3;

                // Label — Diserahkan (kolom B-C) | Diterima (kolom F-G)
                $sheet->mergeCells("B{$currentRow}:C{$currentRow}");
                $sheet->setCellValue("B{$currentRow}", 'Diserahkan Oleh,');
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells("F{$currentRow}:G{$currentRow}");
                $sheet->setCellValue("F{$currentRow}", 'Diterima Oleh,');
                $sheet->getStyle("F{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Nama (4 baris di bawah label)
                $nameRow = $currentRow + 4;

                $sheet->mergeCells("B{$nameRow}:C{$nameRow}");
                $sheet->setCellValue("B{$nameRow}", '( Meisya Auliya )');
                $sheet->getStyle("B{$nameRow}")->getFont()->setBold(true);
                $sheet->getStyle("B{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->mergeCells("F{$nameRow}:G{$nameRow}");
                $sheet->setCellValue("F{$nameRow}", '( ____________________ )');
                $sheet->getStyle("F{$nameRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ─────────────────────────────────────────────
                // PRINT AREA
                // ─────────────────────────────────────────────
                $sheet->getPageSetup()->setPrintArea("A1:H{$nameRow}");
            },
        ];
    }
}

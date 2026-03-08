<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\PurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendDailyGudangReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-gudang-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim laporan harian gudang ke Telegram (PO jatuh tempo H-3 & stok kritis ≤10)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $token  = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (! $token || ! $chatId) {
            $this->error('TELEGRAM_BOT_TOKEN atau TELEGRAM_CHAT_ID belum diatur di .env');
            return self::FAILURE;
        }

        // ── 1) PO yang jatuh tempo dalam 3 hari ke depan ────────
        $hariIni   = now()->startOfDay();
        $tigaHari  = now()->addDays(3)->endOfDay();

        $poJatuhTempo = PurchaseOrder::with('vendor')
            ->whereIn('status_pembayaran', ['DP', 'Tempo'])
            ->where('status_pesanan', '!=', 'Dibatalkan')
            ->whereBetween('tanggal_jatuh_tempo', [$hariIni, $tigaHari])
            ->orderBy('tanggal_jatuh_tempo')
            ->get();

        // ── 2) Item dengan stok kritis (≤ 10) ──────────────────
        $stokKritis = Item::with('uom')
            ->where('stok', '<=', 10)
            ->orderBy('stok')
            ->get();

        // Jika tidak ada data, kirim ringkasan singkat saja
        if ($poJatuhTempo->isEmpty() && $stokKritis->isEmpty()) {
            $message = "📊 <b>LAPORAN HARIAN GUDANG</b>\n"
                . "📅 " . now()->format('d/m/Y') . "\n\n"
                . "✅ Tidak ada PO jatuh tempo dalam 3 hari ke depan.\n"
                . "✅ Semua stok barang dalam kondisi aman.";

            $this->sendTelegram($token, $chatId, $message);
            $this->info('Laporan harian terkirim (tidak ada peringatan).');
            return self::SUCCESS;
        }

        // ── Bangun pesan ────────────────────────────────────────
        $message = "📊 <b>LAPORAN HARIAN GUDANG</b>\n"
            . "🏢 PT MULTYARTHA INDO DAYA\n"
            . "📅 " . now()->format('d/m/Y H:i') . "\n"
            . "━━━━━━━━━━━━━━━━━━━━━\n\n";

        // ── Bagian PO Jatuh Tempo ───────────────────────────────
        if ($poJatuhTempo->isNotEmpty()) {
            $message .= "⚠️ <b>PO JATUH TEMPO (H-3)</b>\n";
            $message .= "Terdapat <b>{$poJatuhTempo->count()}</b> PO mendekati jatuh tempo:\n\n";

            foreach ($poJatuhTempo as $po) {
                $sisaHari   = (int) now()->startOfDay()->diffInDays($po->tanggal_jatuh_tempo, false);
                $labelHari  = $sisaHari <= 0 ? '🔴 HARI INI' : "🟡 H-{$sisaHari}";
                $sisa       = number_format($po->sisa_pembayaran, 0, ',', '.');

                $message .= "📦 <b>{$po->nomor_po}</b> — {$labelHari}\n"
                    . "   Vendor: {$po->vendor->nama_vendor}\n"
                    . "   Jatuh Tempo: {$po->tanggal_jatuh_tempo->format('d/m/Y')}\n"
                    . "   Sisa Bayar: Rp {$sisa}\n"
                    . "   Status: {$po->status_pembayaran}\n\n";
            }
        }

        // ── Bagian Stok Kritis ──────────────────────────────────
        if ($stokKritis->isNotEmpty()) {
            $message .= "🚨 <b>STOK KRITIS (≤ 10)</b>\n";
            $message .= "Terdapat <b>{$stokKritis->count()}</b> barang dengan stok rendah:\n\n";

            foreach ($stokKritis as $item) {
                $uom     = $item->uom->singkatan ?? 'pcs';
                $emoji   = $item->stok == 0 ? '🔴' : '🟡';

                $message .= "{$emoji} <b>{$item->kode_barang}</b> — {$item->nama_barang}\n"
                    . "   Stok: {$item->stok} {$uom} | Merk: {$item->merk}\n\n";
            }
        }

        $message .= "━━━━━━━━━━━━━━━━━━━━━\n"
            . "🤖 <i>Laporan otomatis — Sistem Gudang MID</i>";

        $this->sendTelegram($token, $chatId, $message);
        $this->info("Laporan harian terkirim — {$poJatuhTempo->count()} PO jatuh tempo, {$stokKritis->count()} stok kritis.");

        return self::SUCCESS;
    }

    /**
     * Kirim pesan ke Telegram.
     */
    private function sendTelegram(string $token, string $chatId, string $message): void
    {
        try {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram daily report failed: ' . $e->getMessage());
            $this->error('Gagal kirim Telegram: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Token & base URL Telegram API.
     */
    private string $token;
    private string $apiUrl;

    public function __construct()
    {
        $this->token  = env('TELEGRAM_BOT_TOKEN', '');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    // ================================================================
    // WEBHOOK ENTRY POINT
    // ================================================================

    /**
     * Handle incoming Telegram Webhook update.
     */
    public function handle(Request $request): JsonResponse
    {
        $update = $request->all();

        Log::info('Telegram Webhook Received', $update);

        // ── 1) Handle Callback Query (Inline Keyboard button press) ──
        if (isset($update['callback_query'])) {
            return $this->handleCallbackQuery($update['callback_query']);
        }

        // ── 2) Handle Text Message ──────────────────────────────────
        $message = $update['message'] ?? null;

        if (! $message || ! isset($message['text'])) {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) $message['chat']['id'];
        $text   = trim($message['text']);

        // Cek apakah user sudah ter-binding (sudah login sebelumnya)
        $user = User::where('telegram_id', $chatId)->first();

        // ── Command: /start ─────────────────────────────────────────
        if ($text === '/start') {
            return $this->handleStart($chatId, $user);
        }

        // ── Command: /menu ──────────────────────────────────────────
        if ($text === '/menu') {
            if (! $user) {
                return $this->sendMessage($chatId, "⚠️ Anda belum login.\nSilakan ketik /start untuk memulai.");
            }
            return $this->sendMainMenu($chatId, $user);
        }

        // ── Command: /logout ────────────────────────────────────────
        if ($text === '/logout') {
            return $this->handleLogout($chatId, $user);
        }

        // ── Login Attempt: LOGIN#username#password ──────────────────
        if (str_starts_with(strtoupper($text), 'LOGIN#')) {
            return $this->handleLogin($chatId, $text);
        }

        // ── Conversation Wizard: Cek apakah ada step aktif ─────────
        if ($user) {
            $step = Cache::get("tx_step_{$chatId}");
            if ($step) {
                return $this->handleConversationStep($chatId, $user, $text, $step);
            }
        }

        // ── Command: /batal (batalkan wizard yang sedang berjalan) ──
        if ($text === '/batal' && $user) {
            Cache::forget("tx_step_{$chatId}");
            Cache::forget("tx_data_{$chatId}");
            return $this->sendMessage($chatId, "🚫 Proses input transaksi dibatalkan.\nKetik /menu untuk kembali.");
        }

        // ── Default: User sudah login tapi kirim teks biasa ────────
        if ($user) {
            return $this->sendMessage($chatId, "💡 Ketik /menu untuk melihat Menu Utama.");
        }

        // ── Default: Belum login ────────────────────────────────────
        return $this->sendMessage($chatId, "⚠️ Anda belum login.\nSilakan ketik /start untuk memulai.");
    }

    // ================================================================
    // COMMAND HANDLERS
    // ================================================================

    /**
     * Handle /start command.
     */
    private function handleStart(string $chatId, ?User $user): JsonResponse
    {
        if ($user) {
            $roleBadge = $this->roleBadge($user->role);
            $this->sendMessage($chatId,
                "👋 Halo kembali, <b>{$user->name}</b>! ({$roleBadge})\n\n"
                . "Anda sudah login. Ketik /menu untuk melihat Menu Utama.\n"
                . "Ketik /logout jika ingin keluar."
            );
            return response()->json(['ok' => true]);
        }

        $welcome = "🏢 <b>Selamat datang di PT MULTYARTHA INDO DAYA</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "🤖 <b>Sistem Gudang — ChatOps Bot</b>\n\n"
            . "Silakan login dengan format:\n"
            . "<code>LOGIN#username#password</code>\n\n"
            . "📌 Contoh:\n"
            . "<code>LOGIN#mey#ptmid2026</code>\n\n"
            . "⚠️ <i>Pesan login Anda akan segera dihapus secara otomatis demi keamanan.</i>";

        return $this->sendMessage($chatId, $welcome);
    }

    /**
     * Handle LOGIN#username#password attempt.
     */
    private function handleLogin(string $chatId, string $text): JsonResponse
    {
        $parts = explode('#', $text, 3);

        if (count($parts) !== 3) {
            return $this->sendMessage($chatId,
                "⚠️ Format salah.\n\nGunakan format:\n<code>LOGIN#username#password</code>"
            );
        }

        [, $username, $password] = $parts;
        $username = trim($username);
        $password = trim($password);

        // Cari user berdasarkan username
        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return $this->sendMessage($chatId,
                "❌ <b>Kredensial salah!</b>\n\nUsername atau password tidak cocok.\nSilakan coba lagi."
            );
        }

        // Cek apakah akun ini sudah di-bind ke Telegram lain
        if ($user->telegram_id && $user->telegram_id !== $chatId) {
            return $this->sendMessage($chatId,
                "⚠️ Akun <b>{$user->name}</b> sudah terhubung ke perangkat Telegram lain.\n"
                . "Hubungi Super Admin untuk me-reset binding."
            );
        }

        // ── Bind telegram_id ────────────────────────────────────
        $user->update(['telegram_id' => $chatId]);

        $roleBadge = $this->roleBadge($user->role);

        $this->sendMessage($chatId,
            "✅ <b>Login berhasil!</b>\n\n"
            . "👤 Nama: <b>{$user->name}</b>\n"
            . "🔑 Role: {$roleBadge}\n"
            . "🆔 Chat ID: <code>{$chatId}</code>\n\n"
            . "Memuat Menu Utama..."
        );

        // Kirim Menu Utama sesuai role
        return $this->sendMainMenu($chatId, $user);
    }

    /**
     * Handle /logout command.
     */
    private function handleLogout(string $chatId, ?User $user): JsonResponse
    {
        if (! $user) {
            return $this->sendMessage($chatId, "⚠️ Anda belum login.");
        }

        $user->update(['telegram_id' => null]);

        return $this->sendMessage($chatId,
            "👋 <b>Logout berhasil.</b>\n\n"
            . "Terima kasih, <b>{$user->name}</b>.\n"
            . "Ketik /start untuk login kembali."
        );
    }

    // ================================================================
    // MENU UTAMA (INLINE KEYBOARDS)
    // ================================================================

    /**
     * Kirim Menu Utama berdasarkan role user.
     * - gudang: Cek Stok, Cek PO, Input Transaksi
     * - purchase: Cek Stok, Cek PO
     * - super_admin: Cek Stok, Cek PO, Input Transaksi
     */
    private function sendMainMenu(string $chatId, User $user): JsonResponse
    {
        $roleBadge = $this->roleBadge($user->role);

        $text = "🏠 <b>MENU UTAMA</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "👤 {$user->name} — {$roleBadge}\n\n"
            . "Pilih menu di bawah ini:";

        // Tombol default (Read-Only — semua role bisa akses)
        $keyboard = [
            [
                ['text' => '📦 Cek Stok Barang', 'callback_data' => 'menu_cek_stok'],
            ],
            [
                ['text' => '📄 Cek Status PO', 'callback_data' => 'menu_cek_po'],
            ],
        ];

        // Tombol Input Transaksi — hanya untuk role gudang & super_admin
        if (in_array($user->role, ['gudang', 'super_admin'])) {
            $keyboard[] = [
                ['text' => '🛠️ Input Transaksi Gudang', 'callback_data' => 'menu_input_transaksi'],
            ];
        }

        // Tombol Logout
        $keyboard[] = [
            ['text' => '🚪 Logout', 'callback_data' => 'menu_logout'],
        ];

        return $this->sendMessageWithKeyboard($chatId, $text, $keyboard);
    }

    // ================================================================
    // CALLBACK QUERY HANDLER (Inline Keyboard presses)
    // ================================================================

    /**
     * Handle ketika user menekan Inline Keyboard button.
     */
    private function handleCallbackQuery(array $callbackQuery): JsonResponse
    {
        $chatId       = (string) $callbackQuery['message']['chat']['id'];
        $callbackData = $callbackQuery['data'] ?? '';
        $callbackId   = $callbackQuery['id'];

        // Answer callback query (hilangkan loading spinner di Telegram)
        $this->answerCallbackQuery($callbackId);

        // Cek user sudah login
        $user = User::where('telegram_id', $chatId)->first();

        if (! $user) {
            return $this->sendMessage($chatId, "⚠️ Sesi Anda telah berakhir.\nSilakan ketik /start untuk login kembali.");
        }

        return match ($callbackData) {
            'menu_cek_stok'        => $this->handleCekStok($chatId, $user),
            'menu_cek_po'          => $this->handleCekPo($chatId, $user),
            'menu_input_transaksi' => $this->handleInputTransaksi($chatId, $user),
            'menu_logout'          => $this->handleLogout($chatId, $user),
            'menu_utama'           => $this->cancelWizardAndShowMenu($chatId, $user),
            'tx_tipe_keluar'       => $this->handleTipeSelected($chatId, $user, 'keluar', 'Barang Keluar'),
            'tx_tipe_sisa'         => $this->handleTipeSelected($chatId, $user, 'kembali_sisa', 'Kembali Sisa'),
            'tx_tipe_retur'        => $this->handleTipeSelected($chatId, $user, 'retur_cacat', 'Retur Cacat'),
            'tx_cancel'            => $this->cancelWizard($chatId),
            default                => $this->sendMessage($chatId, "⚠️ Menu tidak dikenali.\nKetik /menu untuk kembali."),
        };
    }

    // ================================================================
    // MENU ACTION HANDLERS (Placeholder — Fase 2)
    // ================================================================

    /**
     * Cek Stok Barang — Read-Only (semua role).
     * Menampilkan stok kritis (≤ 10) dan 5 barang stok terbanyak.
     */
    private function handleCekStok(string $chatId, User $user): JsonResponse
    {
        // ── Stok Kritis (≤ 10) ──────────────────────────────────
        $stokKritis = Item::with('uom')
            ->where('stok', '<=', 10)
            ->orderBy('stok', 'asc')
            ->get();

        // ── 5 Barang Stok Terbanyak ─────────────────────────────
        $stokTerbanyak = Item::with('uom')
            ->orderBy('stok', 'desc')
            ->limit(5)
            ->get();

        // ── Build message ───────────────────────────────────────
        $text = "📦 <b>INFORMASI STOK BARANG</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        // Bagian Stok Kritis
        $text .= "� <b>Stok Kritis (≤ 10):</b>\n";
        if ($stokKritis->isEmpty()) {
            $text .= "   ✅ Semua stok aman, tidak ada yang kritis.\n\n";
        } else {
            foreach ($stokKritis as $i => $item) {
                $uom   = $item->uom->singkatan ?? 'pcs';
                $emoji = $item->stok == 0 ? '⛔' : '⚠️';
                $text .= "   {$emoji} <b>{$item->kode_barang}</b> — {$item->nama_barang}\n"
                    . "        Stok: <b>{$item->stok}</b> {$uom} | Merk: {$item->merk}\n";
            }
            $text .= "\n";
        }

        // Bagian Stok Terbanyak
        $text .= "🟢 <b>Stok Terbanyak (Top 5):</b>\n";
        if ($stokTerbanyak->isEmpty()) {
            $text .= "   Belum ada data barang.\n";
        } else {
            foreach ($stokTerbanyak as $i => $item) {
                $uom    = $item->uom->singkatan ?? 'pcs';
                $medal  = match ($i) {
                    0 => '🥇',
                    1 => '🥈',
                    2 => '🥉',
                    default => '   ',
                };
                $stokFormatted = number_format($item->stok, 0, ',', '.');
                $text .= "   {$medal} <b>{$item->kode_barang}</b> — {$item->nama_barang}\n"
                    . "        Stok: <b>{$stokFormatted}</b> {$uom}\n";
            }
        }

        $text .= "\n📊 Total SKU: <b>" . Item::count() . "</b> barang";

        $keyboard = [
            [['text' => '🔄 Refresh Data', 'callback_data' => 'menu_cek_stok']],
            [['text' => '🔙 Kembali ke Menu Utama', 'callback_data' => 'menu_utama']],
        ];

        return $this->sendMessageWithKeyboard($chatId, $text, $keyboard);
    }

    /**
     * Cek Status PO — Read-Only (semua role).
     * Menampilkan PO aktif (Menunggu Kedatangan / DP / Tempo).
     */
    private function handleCekPo(string $chatId, User $user): JsonResponse
    {
        $poAktif = PurchaseOrder::with('vendor')
            ->where(function ($q) {
                $q->where('status_pesanan', 'Menunggu Kedatangan')
                  ->orWhereIn('status_pembayaran', ['DP', 'Tempo']);
            })
            ->where('status_pesanan', '!=', 'Dibatalkan')
            ->orderBy('tanggal_po', 'desc')
            ->limit(5)
            ->get();

        $text = "📄 <b>STATUS PO AKTIF</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

        if ($poAktif->isEmpty()) {
            $text .= "✅ Tidak ada PO yang tertunda.\n"
                . "Semua pesanan sudah selesai dan lunas.";
        } else {
            foreach ($poAktif as $po) {
                // Badge status pesanan
                $badgePesanan = match ($po->status_pesanan) {
                    'Menunggu Kedatangan' => '� Menunggu',
                    'Selesai'             => '🟢 Selesai',
                    'Dibatalkan'          => '🔴 Batal',
                    default               => $po->status_pesanan,
                };

                // Badge status pembayaran
                $badgeBayar = match ($po->status_pembayaran) {
                    'Lunas'          => '🟢 Lunas',
                    'DP'             => '🟡 DP',
                    'Tempo'          => '🔴 Tempo',
                    'Void / Refund'  => '⚫ Void',
                    default          => $po->status_pembayaran,
                };

                $grandTotal = number_format($po->grand_total, 0, ',', '.');
                $sisa       = number_format($po->sisa_pembayaran, 0, ',', '.');
                $vendor     = $po->vendor->nama_vendor ?? '-';
                $tanggalPo  = $po->tanggal_po->format('d/m/Y');

                // Hitung sisa hari jatuh tempo jika ada
                $jatuhTempoInfo = '';
                if ($po->tanggal_jatuh_tempo) {
                    $sisaHari = (int) now()->startOfDay()->diffInDays($po->tanggal_jatuh_tempo, false);
                    if ($sisaHari < 0) {
                        $jatuhTempoInfo = "   ⏰ Jatuh Tempo: <b>LEWAT " . abs($sisaHari) . " hari!</b>\n";
                    } elseif ($sisaHari === 0) {
                        $jatuhTempoInfo = "   ⏰ Jatuh Tempo: <b>HARI INI!</b>\n";
                    } else {
                        $jatuhTempoInfo = "   ⏰ Jatuh Tempo: H-{$sisaHari} ({$po->tanggal_jatuh_tempo->format('d/m/Y')})\n";
                    }
                }

                $text .= "📦 <b>{$po->nomor_po}</b>\n"
                    . "   🏢 Vendor: {$vendor}\n"
                    . "   📅 Tanggal: {$tanggalPo}\n"
                    . "   📋 Pesanan: {$badgePesanan}\n"
                    . "   💳 Bayar: {$badgeBayar}\n"
                    . "   💰 Total: Rp {$grandTotal}\n"
                    . ($po->sisa_pembayaran > 0 ? "   💸 Sisa: Rp {$sisa}\n" : "")
                    . $jatuhTempoInfo
                    . "\n";
            }

            // Hitung summary
            $totalMenunggu = PurchaseOrder::where('status_pesanan', 'Menunggu Kedatangan')->count();
            $totalBelumLunas = PurchaseOrder::whereIn('status_pembayaran', ['DP', 'Tempo'])
                ->where('status_pesanan', '!=', 'Dibatalkan')->count();

            $text .= "━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                . "📊 Ringkasan:\n"
                . "   🟡 Menunggu Kedatangan: <b>{$totalMenunggu}</b>\n"
                . "   🔴 Belum Lunas (DP/Tempo): <b>{$totalBelumLunas}</b>";
        }

        $keyboard = [
            [['text' => '🔄 Refresh Data', 'callback_data' => 'menu_cek_po']],
            [['text' => '🔙 Kembali ke Menu Utama', 'callback_data' => 'menu_utama']],
        ];

        return $this->sendMessageWithKeyboard($chatId, $text, $keyboard);
    }

    /**
     * Input Transaksi — Hanya gudang & super_admin.
     * Menampilkan pilihan tipe transaksi (Inline Keyboard).
     */
    private function handleInputTransaksi(string $chatId, User $user): JsonResponse
    {
        // Cek hak akses
        if (! in_array($user->role, ['gudang', 'super_admin'])) {
            return $this->sendMessage($chatId, "⛔ Maaf, fitur ini khusus untuk divisi Lapangan/Gudang.");
        }

        // Reset wizard sebelumnya jika ada
        Cache::forget("tx_step_{$chatId}");
        Cache::forget("tx_data_{$chatId}");

        $text = "�️ <b>INPUT TRANSAKSI GUDANG</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "Pilih tipe transaksi:";

        $keyboard = [
            [['text' => '📤 Barang Keluar', 'callback_data' => 'tx_tipe_keluar']],
            [['text' => '♻️ Kembali Sisa', 'callback_data' => 'tx_tipe_sisa']],
            [['text' => '🔴 Retur Cacat', 'callback_data' => 'tx_tipe_retur']],
            [['text' => '🔙 Kembali ke Menu Utama', 'callback_data' => 'menu_utama']],
        ];

        return $this->sendMessageWithKeyboard($chatId, $text, $keyboard);
    }

    // ================================================================
    // CONVERSATION WIZARD — INPUT TRANSAKSI (State via Cache)
    // ================================================================

    /**
     * STATE 1: User memilih tipe transaksi dari Inline Keyboard.
     * Simpan tipe ke Cache, minta kode barang.
     */
    private function handleTipeSelected(string $chatId, User $user, string $tipeValue, string $tipeLabel): JsonResponse
    {
        // Cek hak akses
        if (! in_array($user->role, ['gudang', 'super_admin'])) {
            return $this->sendMessage($chatId, "⛔ Maaf, fitur ini khusus untuk divisi Lapangan/Gudang.");
        }

        Cache::put("tx_step_{$chatId}", 'waiting_kode_barang', 900);
        Cache::put("tx_data_{$chatId}", [
            'tipe'       => $tipeValue,
            'tipe_label' => $tipeLabel,
        ], 900);

        $text = "🛠️ <b>INPUT TRANSAKSI — {$tipeLabel}</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "� Tipe dipilih: <b>{$tipeLabel}</b>\n\n"
            . "Silakan ketik <b>Kode Barang</b>:\n"
            . "<i>(Contoh: MID-0001)</i>\n\n"
            . "💡 Ketik /batal untuk membatalkan.";

        $keyboard = [
            [['text' => '❌ Batalkan', 'callback_data' => 'tx_cancel']],
        ];

        return $this->sendMessageWithKeyboard($chatId, $text, $keyboard);
    }

    /**
     * Handler utama Conversation Wizard.
     * Dipanggil ketika ada pesan teks biasa dan Cache step aktif.
     */
    private function handleConversationStep(string $chatId, User $user, string $text, string $step): JsonResponse
    {
        // Command /batal kapan saja selama wizard
        if ($text === '/batal') {
            return $this->cancelWizard($chatId);
        }

        return match ($step) {
            'waiting_kode_barang' => $this->stepKodeBarang($chatId, $user, $text),
            'waiting_qty'         => $this->stepQty($chatId, $user, $text),
            'waiting_divisi'      => $this->stepDivisi($chatId, $user, $text),
            'waiting_keterangan'  => $this->stepKeterangan($chatId, $user, $text),
            default               => $this->cancelWizard($chatId),
        };
    }

    /**
     * STEP: Validasi Kode Barang.
     */
    private function stepKodeBarang(string $chatId, User $user, string $text): JsonResponse
    {
        $kode = strtoupper(trim($text));
        $item = Item::with('uom')->where('kode_barang', $kode)->first();

        if (! $item) {
            return $this->sendMessage($chatId,
                "❌ Kode barang <b>{$kode}</b> tidak ditemukan.\n\n"
                . "Pastikan format benar (misal: <code>MID-0001</code>).\nSilakan coba lagi:"
            );
        }

        $txData = Cache::get("tx_data_{$chatId}", []);

        // Cek stok cukup untuk tipe keluar & retur_cacat
        if (in_array($txData['tipe'] ?? '', ['keluar', 'retur_cacat']) && $item->stok <= 0) {
            return $this->sendMessage($chatId,
                "⚠️ Stok <b>{$item->nama_barang}</b> saat ini <b>0</b>.\n"
                . "Tidak bisa melakukan transaksi {$txData['tipe_label']}.\n\n"
                . "Ketik kode barang lain atau /batal untuk membatalkan."
            );
        }

        $uom = $item->uom->singkatan ?? 'pcs';

        // Simpan item ke data wizard
        $txData['item_id']      = $item->id;
        $txData['kode_barang']  = $item->kode_barang;
        $txData['nama_barang']  = $item->nama_barang;
        $txData['stok_sekarang'] = $item->stok;
        $txData['uom']          = $uom;

        Cache::put("tx_data_{$chatId}", $txData, 900);
        Cache::put("tx_step_{$chatId}", 'waiting_qty', 900);

        $text = "✅ Barang ditemukan!\n\n"
            . "📦 <b>{$item->kode_barang}</b> — {$item->nama_barang}\n"
            . "📊 Stok saat ini: <b>{$item->stok}</b> {$uom}\n"
            . "🏷️ Merk: {$item->merk}\n\n"
            . "Berapa <b>Quantity</b>-nya?\n"
            . "<i>(Ketik angka saja, misal: 5)</i>";

        return $this->sendMessage($chatId, $text);
    }

    /**
     * STEP: Validasi Quantity.
     */
    private function stepQty(string $chatId, User $user, string $text): JsonResponse
    {
        if (! is_numeric($text) || (int) $text < 1) {
            return $this->sendMessage($chatId,
                "❌ Quantity harus berupa angka positif (minimal 1).\nSilakan coba lagi:"
            );
        }

        $qty    = (int) $text;
        $txData = Cache::get("tx_data_{$chatId}", []);

        // Cek stok cukup untuk tipe keluar & retur_cacat
        if (in_array($txData['tipe'] ?? '', ['keluar', 'retur_cacat'])) {
            $stokSekarang = $txData['stok_sekarang'] ?? 0;
            if ($qty > $stokSekarang) {
                return $this->sendMessage($chatId,
                    "⚠️ Stok tidak cukup!\n"
                    . "Stok tersedia: <b>{$stokSekarang}</b> {$txData['uom']}\n"
                    . "Qty diminta: <b>{$qty}</b>\n\n"
                    . "Masukkan quantity yang lebih kecil:"
                );
            }
        }

        $txData['qty'] = $qty;
        Cache::put("tx_data_{$chatId}", $txData, 900);
        Cache::put("tx_step_{$chatId}", 'waiting_divisi', 900);

        return $this->sendMessage($chatId,
            "✅ Qty: <b>{$qty}</b> {$txData['uom']}\n\n"
            . "👷 Silakan ketik <b>Nama Divisi / Tukang</b>:\n"
            . "<i>(Contoh: Divisi Listrik, Tukang Andi, dll.)</i>\n"
            . "<i>Ketik <b>-</b> jika tidak ada divisi.</i>"
        );
    }

    /**
     * STEP: Simpan Divisi.
     */
    private function stepDivisi(string $chatId, User $user, string $text): JsonResponse
    {
        $txData = Cache::get("tx_data_{$chatId}", []);
        $txData['divisi'] = trim($text) === '-' ? null : trim($text);

        Cache::put("tx_data_{$chatId}", $txData, 900);
        Cache::put("tx_step_{$chatId}", 'waiting_keterangan', 900);

        $divisiLabel = $txData['divisi'] ?? '(tidak ada)';

        return $this->sendMessage($chatId,
            "✅ Divisi: <b>{$divisiLabel}</b>\n\n"
            . "📝 Ketik <b>Keterangan</b>:\n"
            . "<i>(Atau ketik <b>-</b> jika tidak ada keterangan)</i>"
        );
    }

    /**
     * STEP FINAL: Simpan Keterangan & Create Transaction.
     */
    private function stepKeterangan(string $chatId, User $user, string $text): JsonResponse
    {
        $txData = Cache::get("tx_data_{$chatId}", []);
        $keterangan = trim($text) === '-' ? null : trim($text);

        // ── Resolve Divisi (firstOrCreate jika ada) ─────────────
        $divisiId = null;
        if ($txData['divisi'] ?? null) {
            $divisi   = Divisi::firstOrCreate(['nama_divisi_tukang' => $txData['divisi']]);
            $divisiId = $divisi->id;
        }

        // ── Re-check stok terkini (safety net, bisa berubah saat wizard) ──
        $item = Item::find($txData['item_id']);
        if (! $item) {
            Cache::forget("tx_step_{$chatId}");
            Cache::forget("tx_data_{$chatId}");
            return $this->sendMessage($chatId, "❌ Barang tidak ditemukan. Transaksi dibatalkan.\nKetik /menu untuk kembali.");
        }

        if (in_array($txData['tipe'], ['keluar', 'retur_cacat']) && $item->stok < $txData['qty']) {
            Cache::forget("tx_step_{$chatId}");
            Cache::forget("tx_data_{$chatId}");
            return $this->sendMessage($chatId,
                "⚠️ Stok berubah saat proses input!\n"
                . "Stok tersedia: <b>{$item->stok}</b>, Qty diminta: <b>{$txData['qty']}</b>\n\n"
                . "Transaksi dibatalkan. Silakan ulangi dari /menu."
            );
        }

        // ── Create Transaction (Observer auto-adjust stok) ──────
        $transaction = Transaction::create([
            'tipe_transaksi'    => $txData['tipe'],
            'item_id'           => $txData['item_id'],
            'qty'               => $txData['qty'],
            'tanggal_transaksi' => now(),
            'divisi_id'         => $divisiId,
            'user_id'           => $user->id,
            'keterangan'        => $keterangan,
        ]);

        // ── Ambil stok terbaru setelah observer ─────────────────
        $item->refresh();

        // ── Bersihkan Cache ─────────────────────────────────────
        Cache::forget("tx_step_{$chatId}");
        Cache::forget("tx_data_{$chatId}");

        // ── Kirim konfirmasi sukses ─────────────────────────────
        $tipeEmoji = match ($txData['tipe']) {
            'keluar'       => '📤',
            'kembali_sisa' => '♻️',
            'retur_cacat'  => '🔴',
            default        => '📋',
        };

        $stokFormatted = number_format($item->stok, 0, ',', '.');

        $text = "✅ <b>TRANSAKSI BERHASIL DISIMPAN!</b>\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"
            . "{$tipeEmoji} Tipe: <b>{$txData['tipe_label']}</b>\n"
            . "📦 Barang: <b>{$txData['kode_barang']}</b> — {$txData['nama_barang']}\n"
            . "🔢 Qty: <b>{$txData['qty']}</b> {$txData['uom']}\n"
            . "👷 Divisi: <b>" . ($txData['divisi'] ?? '-') . "</b>\n"
            . "📝 Keterangan: " . ($keterangan ?? '-') . "\n"
            . "📊 Sisa Stok: <b>{$stokFormatted}</b> {$txData['uom']}\n\n"
            . "👤 Oleh: {$user->name}\n"
            . "🕐 Waktu: " . now()->format('d/m/Y H:i');

        $keyboard = [
            [['text' => '🛠️ Input Transaksi Lagi', 'callback_data' => 'menu_input_transaksi']],
            [['text' => '📦 Cek Stok Barang', 'callback_data' => 'menu_cek_stok']],
            [['text' => '🔙 Kembali ke Menu Utama', 'callback_data' => 'menu_utama']],
        ];

        return $this->sendMessageWithKeyboard($chatId, $text, $keyboard);
    }

    // ================================================================
    // WIZARD HELPER METHODS
    // ================================================================

    /**
     * Batalkan wizard & kembali ke menu utama.
     */
    private function cancelWizardAndShowMenu(string $chatId, User $user): JsonResponse
    {
        Cache::forget("tx_step_{$chatId}");
        Cache::forget("tx_data_{$chatId}");

        return $this->sendMainMenu($chatId, $user);
    }

    /**
     * Batalkan wizard saja (tanpa menu).
     */
    private function cancelWizard(string $chatId): JsonResponse
    {
        Cache::forget("tx_step_{$chatId}");
        Cache::forget("tx_data_{$chatId}");

        return $this->sendMessage($chatId,
            "🚫 Proses input transaksi dibatalkan.\n\nKetik /menu untuk kembali ke Menu Utama."
        );
    }

    // ================================================================
    // TELEGRAM API HELPERS
    // ================================================================

    /**
     * Kirim pesan teks biasa (HTML parse_mode).
     */
    private function sendMessage(string $chatId, string $text): JsonResponse
    {
        Http::post("{$this->apiUrl}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Kirim pesan dengan Inline Keyboard.
     */
    private function sendMessageWithKeyboard(string $chatId, string $text, array $keyboard): JsonResponse
    {
        Http::post("{$this->apiUrl}/sendMessage", [
            'chat_id'      => $chatId,
            'text'          => $text,
            'parse_mode'    => 'HTML',
            'reply_markup'  => json_encode([
                'inline_keyboard' => $keyboard,
            ]),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Answer Callback Query (menghilangkan loading spinner di tombol).
     */
    private function answerCallbackQuery(string $callbackQueryId, string $text = ''): void
    {
        Http::post("{$this->apiUrl}/answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
            'text'              => $text,
        ]);
    }

    // ================================================================
    // ROLE HELPERS
    // ================================================================

    /**
     * Badge role yang human-readable.
     */
    private function roleBadge(string $role): string
    {
        return match ($role) {
            'gudang'      => '🏗️ Gudang',
            'purchase'    => '🛒 Purchase',
            'admin'       => '🛡️ Admin',
            'super_admin' => '👑 Super Admin',
            default       => $role,
        };
    }
}

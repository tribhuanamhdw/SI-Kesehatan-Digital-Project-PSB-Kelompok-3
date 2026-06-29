<?php
require '../config/database.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$dataPasien = null;
$daftarKronis = null;

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id, nama_lengkap, golongan_darah, kontak_darurat_nama, kontak_darurat_nomor FROM users WHERE token_qr = ? AND status_akun = 'aktif'");
    $stmt->execute([$token]);
    $dataPasien = $stmt->fetch();

    if ($dataPasien) {
        // Tarik penyakit kronis dan resep kritis milik pasien
        $stmtMedis = $pdo->prepare("
            SELECT rp.gejala_kambuh, rp.catatan_obat, mp.nama_penyakit 
            FROM riwayat_penyakit rp
            JOIN master_penyakit mp ON rp.master_penyakit_id = mp.id
            WHERE rp.user_id = ? AND rp.status_kronis = 1
        ");
        $stmtMedis->execute([$dataPasien['id']]);
        $daftarKronis = $stmtMedis->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEDICAL EMERGENCY ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#212529] text-white min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl bg-white text-[#212529] rounded-2xl border-4 border-[#DC3545] shadow-2xl overflow-hidden">
        
        <div class="bg-[#DC3545] text-white text-center p-6">
            <h1 class="text-2xl md:text-3xl font-black tracking-wider uppercase">🚨 IDENTITAS MEDIS DARURAT</h1>
            <p class="text-xs text-red-100 mt-1 font-medium">Data ini disajikan secara sah oleh pasien untuk keperluan tindakan pertolongan pertama.</p>
        </div>

        <?php if (!$dataPasien): ?>
            <div class="p-8 text-center text-[#6C757D]">
                Data darurat tidak ditemukan atau token tidak valid.
            </div>
        <?php else: ?>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-[#DEE2E6] pb-4">
                    <div class="md:col-span-2">
                        <span class="text-xs font-bold uppercase text-[#6C757D]">Nama Lengkap Pasien:</span>
                        <p class="text-xl md:text-2xl font-bold text-[#1C4580]"><?php echo htmlspecialchars($dataPasien['nama_lengkap']); ?></p>
                    </div>
                    <div class="bg-[#F8F9FA] p-3 rounded-lg border border-[#DEE2E6] text-center">
                        <span class="text-xs font-bold uppercase text-[#6C757D] block">Golongan Darah:</span>
                        <p class="text-3xl font-black text-[#DC3545]"><?php echo htmlspecialchars($dataPasien['golongan_darah']); ?></p>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase text-[#6C757D] block mb-2">Kondisi Kronis & Obat Rutin (Wajib Diperhatikan):</span>
                    <?php if (count($daftarKronis) === 0): ?>
                        <p class="text-sm bg-gray-50 p-3 rounded border text-[#6C757D]">Tidak ada riwayat penyakit kronis mendadak yang dilaporkan.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($daftarKronis as $kronis): ?>
                                <div class="bg-red-50/50 border border-red-200 p-4 rounded-xl">
                                    <h3 class="font-bold text-[#842029] text-base">⚠️ <?php echo htmlspecialchars($kronis['nama_penyakit']); ?></h3>
                                    <p class="text-sm text-[#212529] mt-1"><b class="text-xs text-gray-500 uppercase block">Kondisi/Gejala Saat Kambuh:</b> <?php echo htmlspecialchars($kronis['gejala_kambuh']); ?></p>
                                    <p class="text-sm text-[#212529] mt-2 bg-white p-2 rounded border border-red-100 font-mono text-xs"><b class="text-xs text-red-700 font-sans font-bold uppercase block mb-0.5">Daftar Kebutuhan Obat:</b> <?php echo htmlspecialchars($kronis['catatan_obat']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bg-[#F8F9FA] p-4 rounded-xl border border-[#DEE2E6] flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-[#6C757D] block">Hubungi Wali / Kontak Darurat:</span>
                        <p class="text-lg font-bold text-[#212529]"><?php echo htmlspecialchars($dataPasien['kontak_darurat_nama']); ?></p>
                    </div>
                    <a href="tel:<?php echo $dataPasien['kontak_darurat_nomor']; ?>" class="w-full md:w-auto h-12 px-6 bg-[#00A14B] text-white font-bold rounded-lg flex items-center justify-center space-x-2 shadow hover:bg-[#008A42] active:scale-95 transition duration-150">
                        <span>📞 Hubungi Sekarang</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
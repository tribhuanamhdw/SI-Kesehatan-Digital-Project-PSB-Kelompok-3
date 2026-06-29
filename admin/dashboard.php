<?php
require_once '../config/database.php';
require_once '../components/header.php';
require_once '../components/sidebar.php';

// 1. Statistik Total Pasien
$stmtUser = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pengguna'");
$totalPasien = $stmtUser->fetchColumn();

// 2. Statistik Kondisi Darurat (Status Kronis = 1)
$stmtKronis = $pdo->query("SELECT COUNT(*) FROM riwayat_penyakit WHERE status_kronis = 1");
$totalKronis = $stmtKronis->fetchColumn();

// 3. Statistik Penyakit Tertinggi
$stmtPenyakit = $pdo->query("
    SELECT mp.nama_penyakit, COUNT(rp.id) as jumlah 
    FROM riwayat_penyakit rp 
    JOIN master_penyakit mp ON rp.master_penyakit_id = mp.id 
    GROUP BY mp.nama_penyakit 
    ORDER BY jumlah DESC 
    LIMIT 1
");
$penyakitTop = $stmtPenyakit->fetch();
?>

<main class="flex-1 p-4 md:p-6 bg-[#F8F9FA]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1C4580]">Statistik Utama Faskes</h1>
        <p class="text-xs text-[#6C757D]">Selamat datang di panel kendali medis UriMed Digital Health.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-[#DEE2E6] shadow-sm">
            <span class="text-xs font-bold text-[#6C757D] uppercase">Total Pasien Terdaftar</span>
            <h3 class="text-3xl font-black text-[#1C4580] mt-1"><?php echo number_format($totalPasien); ?></h3>
            <span class="text-[10px] text-[#6C757D]">Pengguna terdaftar di sistem</span>
        </div>
        
        <div class="bg-white p-4 rounded-xl border border-[#DEE2E6] shadow-sm">
            <span class="text-xs font-bold text-[#6C757D] uppercase">Kondisi Kronis / Darurat</span>
            <h3 class="text-3xl font-black <?php echo $totalKronis > 0 ? 'text-[#DC3545]' : 'text-[#00A14B]'; ?> mt-1">
                <?php echo $totalKronis; ?>
            </h3>
            <span class="text-[10px] text-[#6C757D]">
                <?php echo $totalKronis > 0 ? 'Perlu tindakan segera!' : 'Semua pasien dalam kondisi aman' ?>
            </span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#DEE2E6] shadow-sm sm:col-span-2 lg:col-span-1">
            <span class="text-xs font-bold text-[#6C757D] uppercase">Penyakit Terbanyak</span>
            <?php if ($penyakitTop): ?>
                <h3 class="text-xl font-bold text-[#212529] mt-2"><?php echo htmlspecialchars($penyakitTop['nama_penyakit']); ?></h3>
                <span class="text-[10px] text-[#0D6EFD] font-medium"><?php echo $penyakitTop['jumlah']; ?> kasus tercatat</span>
            <?php else: ?>
                <p class="text-xs text-gray-400 mt-2">Belum ada data penyakit.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
require_once '../components/footer.php';
?>
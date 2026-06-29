<?php
require '../config/database.php';
include '../components/header.php';
include '../components/sidebar.php';

$user_id = $_SESSION['user_id'];

$stmtUser = $pdo->prepare("SELECT token_qr, nama_lengkap FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$userData = $stmtUser->fetch();

$stmtPenyakit = $pdo->prepare("
    SELECT rp.*, mp.nama_penyakit 
    FROM riwayat_penyakit rp 
    JOIN master_penyakit mp ON rp.master_penyakit_id = mp.id 
    WHERE rp.user_id = ? 
    ORDER BY rp.status_kronis DESC, rp.created_at DESC
");
$stmtPenyakit->execute([$user_id]);
$daftarPenyakit = $stmtPenyakit->fetchAll();
?>

<main class="flex-1 p-4 md:p-8 max-w-[1440px] mx-auto w-full">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-[#1C4580]">Dashboard Kesehatan Anda</h1>
        <p class="text-sm text-[#6C757D]">Pantau kondisi tubuh mandiri dan manajemen ID Darurat.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-4 order-2 lg:order-1">
            <h2 class="text-lg font-semibold text-[#212529]">Riwayat Kondisi Medis</h2>
            
            <?php if (count($daftarPenyakit) === 0): ?>
                <div class="bg-white p-6 rounded-lg border border-[#DEE2E6] text-center text-sm text-[#6C757D]">
                    Belum ada catatan penyakit mandiri. Klik menu "Catatan Penyakit" untuk menambahkan.
                </div>
            <?php else: ?>
                <?php foreach ($daftarPenyakit as $penyakit): ?>
                    <div class="bg-white p-5 rounded-lg border border-[#DEE2E6] shadow-sm relative overflow-hidden">
                        <div class="absolute top-4 right-4">
                            <?php if ($penyakit['status_kronis'] == 1): ?>
                                <span class="bg-[#F8D7DA] text-[#842029] text-xs font-medium px-3 py-1 rounded-full">🚨 Butuh Perhatian Khusus</span>
                            <?php else: ?>
                                <span class="bg-[#D1E7DD] text-[#0F5132] text-xs font-medium px-3 py-1 rounded-full">🟢 Terkontrol</span>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-lg font-bold text-[#1C4580] pr-32"><?php echo htmlspecialchars($penyakit['nama_penyakit']); ?></h3>
                        <p class="text-xs text-[#6C757D] mb-3">Diperbarui: <?php echo date('d M Y', strtotime($penyakit['created_at'])); ?></p>
                        
                        <div class="space-y-2 text-sm text-[#212529]">
                            <div>
                                <span class="font-semibold block text-xs uppercase text-[#6C757D]">Gejala & Pemicu:</span>
                                <p class="bg-[#F8F9FA] p-2 rounded border border-gray-100"><?php echo htmlspecialchars($penyakit['gejala_kambuh']); ?></p>
                            </div>
                            <div>
                                <span class="font-semibold block text-xs uppercase text-[#6C757D]">Catatan Obat & Dosis:</span>
                                <p class="bg-[#F8F9FA] p-2 rounded border border-gray-100 font-mono text-xs"><?php echo htmlspecialchars($penyakit['catatan_obat']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="order-1 lg:order-2">
            <div class="bg-gradient-to-br from-[#1C4580] to-[#1D4580] text-white p-6 rounded-2xl shadow-md text-center">
                <h2 class="text-lg font-bold mb-1">Emergency QR Code</h2>
                <p class="text-xs text-blue-100 mb-4">Gunakan di layar kunci HP atau cetak sebagai ID Card saat beraktivitas padat.</p>
                
                <div class="bg-white p-3 inline-block rounded-xl mx-auto shadow-inner mb-4">
                    <div id="qrcode"></div>
                </div>

                <div class="text-center">
                    <span class="block text-xs text-blue-200">ID Token Bypass:</span>
                    <span class="font-mono text-sm bg-black/20 px-3 py-1 rounded inline-block mt-1"><?php echo $userData['token_qr']; ?></span>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const bypassUrl = window.location.origin + "/urimed/emergency/index.php?token=<?php echo $userData['token_qr']; ?>";
    
    new QRCode(document.getElementById("qrcode"), {
        text: bypassUrl,
        width: 160,
        height: 160,
        colorDark : "#1C4580", // Navy solid
        colorLight : "#FFFFFF",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>

<?php include '../components/footer.php'; ?>
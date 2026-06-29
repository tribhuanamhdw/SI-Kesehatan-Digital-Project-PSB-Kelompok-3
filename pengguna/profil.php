<?php
require_once '../config/database.php';
include '../components/header.php';
include '../components/sidebar.php';

$user_id = $_SESSION['user_id'];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $goldar = $_POST['golongan_darah'];
    $wali = trim($_POST['kontak_darurat_nama']);
    $hp_wali = trim($_POST['kontak_darurat_nomor']);

    $stmt = $pdo->prepare("UPDATE users SET golongan_darah = ?, kontak_darurat_nama = ?, kontak_darurat_nomor = ? WHERE id = ?");
    $stmt->execute([$goldar, $wali, $hp_wali, $user_id]);
    $success = true;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$u = $stmt->fetch();

// Deteksi apakah sedang dalam mode edit
$isEditMode = isset($_GET['mode']) && $_GET['mode'] === 'edit';
?>

<main class="flex-1 p-4 md:p-8 max-w-[1440px] mx-auto w-full flex justify-center items-center">
    <div class="bg-white p-6 md:p-8 rounded-xl border border-[#DEE2E6] shadow-sm w-full max-w-xl">
        
        <?php if($success): ?>
            <div id="alert" class="p-3 mb-4 rounded text-sm bg-[#D1E7DD] text-[#0F5132] border border-[#A3CFBB] flex justify-between items-center">
                <span>✅ Profil berhasil diperbarui!</span>
                <button onclick="document.getElementById('alert').remove()" class="font-bold ml-4 hover:text-[#0F5132]/70">&times;</button>
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-bold text-[#1C4580]">Profil & Kontak Darurat</h1>
                <p class="text-xs text-[#6C757D]">Kelola data wali Anda untuk situasi kritis.</p>
            </div>
            <?php if (!$isEditMode): ?>
                <a href="?mode=edit" class="text-xs bg-[#1C4580] text-white px-4 py-2 rounded font-bold hover:bg-[#153460]">EDIT PROFIL</a>
            <?php endif; ?>
        </div>

        <form action="" method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" disabled value="<?php echo htmlspecialchars($u['nama_lengkap'] ?? ''); ?>" class="w-full h-10 px-3 bg-gray-50 border rounded text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">NIK</label>
                    <input type="text" disabled value="<?php echo htmlspecialchars($u['nik'] ?? ''); ?>" class="w-full h-10 px-3 bg-gray-50 border rounded text-sm cursor-not-allowed">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#6C757D] uppercase mb-1">Golongan Darah</label>
                <select name="golongan_darah" <?= !$isEditMode ? 'disabled' : '' ?> class="w-full h-10 px-2 bg-white border rounded text-sm focus:border-[#0D6EFD]">
                    <?php foreach(['-', 'A', 'B', 'AB', 'O'] as $g): ?>
                        <option value="<?= $g ?>" <?= ($u['golongan_darah'] ?? '-') === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="border-t pt-4">
                <h3 class="text-sm font-bold text-[#DC3545] mb-3">Wali Kunci Darurat</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#6C757D] uppercase mb-1">Nama Wali</label>
                        <input type="text" name="kontak_darurat_nama" <?= !$isEditMode ? 'disabled' : '' ?> required value="<?php echo htmlspecialchars($u['kontak_darurat_nama'] ?? ''); ?>" class="w-full h-10 px-3 border rounded text-sm focus:border-[#0D6EFD] <?= !$isEditMode ? 'bg-gray-50' : '' ?>">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#6C757D] uppercase mb-1">Nomor HP</label>
                        <input type="text" name="kontak_darurat_nomor" <?= !$isEditMode ? 'disabled' : '' ?> required value="<?php echo htmlspecialchars($u['kontak_darurat_nomor'] ?? ''); ?>" class="w-full h-10 px-3 border rounded text-sm focus:border-[#0D6EFD] <?= !$isEditMode ? 'bg-gray-50' : '' ?>">
                    </div>
                </div>
            </div>

            <?php if ($isEditMode): ?>
                <div class="flex gap-2 mt-4">
                    <button type="submit" name="update_profile" class="flex-1 h-11 bg-[#00A14B] text-white font-bold rounded hover:bg-[#008A42]">SIMPAN</button>
                    <a href="profil.php" class="h-11 px-6 bg-gray-200 text-gray-700 font-bold rounded flex items-center hover:bg-gray-300">BATAL</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</main>
<?php include '../components/footer.php'; ?>
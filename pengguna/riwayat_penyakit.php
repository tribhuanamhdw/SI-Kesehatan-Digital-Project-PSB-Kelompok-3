<?php
require_once '../config/database.php';
include '../components/header.php';
include '../components/sidebar.php';

$user_id = $_SESSION['user_id'];

// --- PROSES CRUD (Tambah/Edit/Hapus) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $m_id = $_POST['master_penyakit_id'];
    $gejala = trim($_POST['gejala_kambuh']);
    $obat = trim($_POST['catatan_obat']);
    $kronis = isset($_POST['status_kronis']) ? 1 : 0;

    if (isset($_POST['tambah'])) {
        $stmt = $pdo->prepare("INSERT INTO riwayat_penyakit (user_id, master_penyakit_id, gejala_kambuh, catatan_obat, status_kronis) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $m_id, $gejala, $obat, $kronis]);
    } elseif (isset($_POST['update'])) {
        $stmt = $pdo->prepare("UPDATE riwayat_penyakit SET master_penyakit_id=?, gejala_kambuh=?, catatan_obat=?, status_kronis=? WHERE id=? AND user_id=?");
        $stmt->execute([$m_id, $gejala, $obat, $kronis, $_POST['id'], $user_id]);
    }
}

if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM riwayat_penyakit WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete_id'], $user_id]);
    header("Location: riwayat_penyakit.php"); exit;
}

// --- DATA UNTUK FORM & TABEL ---
$editData = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM riwayat_penyakit WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['edit_id'], $user_id]);
    $editData = $stmt->fetch();
}

$masters = $pdo->query("SELECT * FROM master_penyakit ORDER BY nama_penyakit ASC")->fetchAll();
$search = $_GET['search'] ?? '';
$stmtData = $pdo->prepare("SELECT rp.*, mp.nama_penyakit FROM riwayat_penyakit rp JOIN master_penyakit mp ON rp.master_penyakit_id = mp.id WHERE rp.user_id = ? AND mp.nama_penyakit LIKE ? ORDER BY rp.id DESC");
$stmtData->execute([$user_id, "%$search%"]);
$riwayat = $stmtData->fetchAll();
?>

<main class="flex-1 p-4 md:p-8 max-w-[1440px] mx-auto w-full">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#1C4580]">Catatan Penyakit Mandiri</h1>
        <p class="text-sm text-[#6C757D]">Kelola riwayat kesehatan dan detail pengobatan Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border border-[#DEE2E6] shadow-sm h-fit">
            <h2 class="text-base font-bold text-[#212529] mb-4"><?= $editData ? 'Edit Catatan' : 'Tambah Catatan' ?></h2>
            <form action="" method="POST" class="space-y-4">
                <?php if ($editData): ?><input type="hidden" name="id" value="<?= $editData['id'] ?>"><?php endif; ?>
                <div>
                    <label class="block text-xs font-bold text-[#6C757D] uppercase mb-1">Penyakit</label>
                    <select name="master_penyakit_id" required class="w-full h-10 px-2 border rounded text-sm">
                        <?php foreach($masters as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($editData && $editData['master_penyakit_id'] == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['nama_penyakit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#6C757D] uppercase mb-1">Gejala & Pemicu</label>
                    <textarea name="gejala_kambuh" required rows="2" class="w-full p-2 border rounded text-sm"><?= $editData['gejala_kambuh'] ?? '' ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#6C757D] uppercase mb-1">Catatan Obat</label>
                    <textarea name="catatan_obat" required rows="2" class="w-full p-2 border rounded text-sm"><?= $editData['catatan_obat'] ?? '' ?></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="status_kronis" value="1" class="w-4 h-4" <?= ($editData && $editData['status_kronis'] == 1) ? 'checked' : '' ?>>
                    <label class="ml-2 text-sm text-[#DC3545]">Kondisi Kronis</label>
                </div>
                <button type="submit" name="<?= $editData ? 'update' : 'tambah' ?>" class="w-full h-11 bg-[#1C4580] text-white rounded font-bold"><?= $editData ? 'Simpan Perubahan' : 'Simpan' ?></button>
                <?php if($editData): ?><a href="riwayat_penyakit.php" class="block text-center text-xs text-gray-500 mt-2">Batal</a><?php endif; ?>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-[#DEE2E6] shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-[#F8F9FA] text-[#6C757D] text-xs uppercase border-b">
                            <th class="p-4">Info</th>
                            <th class="p-4">Detail Rekam Medis</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach($riwayat as $r): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="p-4 align-top">
                                    <div class="font-bold text-[#1C4580]"><?= htmlspecialchars($r['nama_penyakit']) ?></div>
                                    <div class="text-[10px] text-gray-400"><?= date('d M Y', strtotime($r['created_at'] ?? 'now')) ?></div>
                                    <div class="mt-2"><?= $r['status_kronis'] == 1 ? '<span class="bg-red-100 text-red-800 text-[10px] px-2 py-0.5 rounded-full">KRONIS</span>' : '<span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full">AMAN</span>' ?></div>
                                </td>
                                <td class="p-4 align-top text-xs text-gray-600">
                                    <div class="mb-2"><strong>Gejala:</strong> <?= htmlspecialchars($r['gejala_kambuh']) ?></div>
                                    <div class="bg-gray-50 p-2 rounded border border-gray-100 font-mono"><strong>Obat:</strong> <?= htmlspecialchars($r['catatan_obat']) ?></div>
                                </td>
                                <td class="p-4 text-center align-top space-y-2">
                                    <a href="?edit_id=<?= $r['id'] ?>" class="block text-[10px] bg-blue-100 text-blue-800 py-1 rounded">EDIT</a>
                                    <a href="?delete_id=<?= $r['id'] ?>" onclick="return confirm('Hapus?')" class="block text-[10px] bg-red-100 text-red-800 py-1 rounded">HAPUS</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php include '../components/footer.php'; ?>
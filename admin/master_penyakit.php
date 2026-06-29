<?php
require_once '../config/database.php';
require_once '../components/header.php';
require_once '../components/sidebar.php';

$notif = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_penyakit'])) {
    $nama = trim($_POST['nama_penyakit']);
    $desc = trim($_POST['deskripsi_umum']);
    
    if(!empty($nama)) {
        $cek = $pdo->prepare("
            SELECT nama_penyakit FROM master_penyakit 
            WHERE LOWER(nama_penyakit) LIKE LOWER(?) 
               OR LOWER(?) LIKE CONCAT('%', LOWER(nama_penyakit), '%')
            LIMIT 1
        ");
        $cek->execute(["%$nama%", $nama]);
        $existing = $cek->fetch();
        
        if ($existing) {
            $notif = [
                'status' => 'gagal', 
                'pesan' => "⚠️ Gagal Input! Nama penyakit mirip dengan yang sudah terdaftar, yaitu: <strong>" . htmlspecialchars($existing['nama_penyakit']) . "</strong>. Mohon sesuaikan atau gunakan data yang ada."
            ];
        } else {
            $stmt = $pdo->prepare("INSERT INTO master_penyakit (nama_penyakit, deskripsi_umum) VALUES (?, ?)");
            $stmt->execute([$nama, $desc]);
            $notif = ['status' => 'sukses', 'pesan' => "Data penyakit berhasil disuntikkan ke sistem."];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_penyakit'])) {
    $id = $_POST['id_penyakit'];
    $nama = trim($_POST['nama_penyakit']);
    $desc = trim($_POST['deskripsi_umum']);
    
    if(!empty($nama) && !empty($id)) {
        $cek = $pdo->prepare("
            SELECT nama_penyakit FROM master_penyakit 
            WHERE (LOWER(nama_penyakit) LIKE LOWER(?) OR LOWER(?) LIKE CONCAT('%', LOWER(nama_penyakit), '%'))
              AND id != ?
            LIMIT 1
        ");
        $cek->execute(["%$nama%", $nama, $id]);
        $existing = $cek->fetch();
        
        if ($existing) {
            $notif = [
                'status' => 'gagal', 
                'pesan' => "⚠️ Gagal Update! Perubahan nama mirip dengan data lain yang sudah ada: <strong>" . htmlspecialchars($existing['nama_penyakit']) . "</strong>."
            ];
        } else {
            $stmt = $pdo->prepare("UPDATE master_penyakit SET nama_penyakit = ?, deskripsi_umum = ? WHERE id = ?");
            $stmt->execute([$nama, $desc, $id]);
            $notif = ['status' => 'sukses', 'pesan' => "Perubahan data penyakit berhasil disimpan."];
        }
    }
}

if (isset($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM master_penyakit WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: master_penyakit.php?delete_success=1");
    exit;
}

if (isset($_GET['delete_success'])) {
    $notif = ['status' => 'sukses', 'pesan' => "Data penyakit telah dihapus dari sistem."];
}

$editData = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM master_penyakit WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $editData = $stmt->fetch();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_az = isset($_GET['filter_az']) ? $_GET['filter_az'] : '';

$query_str = "SELECT * FROM master_penyakit WHERE 1=1";
$params = [];

if ($search !== '') {
    $query_str .= " AND (nama_penyakit LIKE ? OR deskripsi_umum LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter_az === 'az') {
    $query_str .= " ORDER BY nama_penyakit ASC";
} elseif ($filter_az === 'za') {
    $query_str .= " ORDER BY nama_penyakit DESC";
} else {
    $query_str .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query_str);
$stmt->execute($params);
$listPenyakit = $stmt->fetchAll();
?>

<main class="flex-1 p-4 md:p-6 bg-[#F8F9FA] w-full overflow-hidden">
    <?php if ($notif): ?>
        <div id="system-alert" class="mb-4 p-4 rounded-lg border text-sm flex items-center justify-between shadow-sm transition-all duration-300 <?php echo $notif['status'] === 'sukses' ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200'; ?>">
            <div class="flex items-center">
                <span class="mr-2"><?php echo $notif['status'] === 'sukses' ? '✅' : '⚠️'; ?></span>
                <div><?php echo $notif['pesan']; ?></div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="ml-4 font-bold text-lg leading-none opacity-60 hover:opacity-100 transition focus:outline-none px-1" title="Tutup Notifikasi">
                &times;
            </button>
        </div>
    <?php endif; ?>

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#1C4580]">Master Data Penyakit</h2>
            <p class="text-xs text-[#6C757D] mt-1">Kelola acuan data medis terintegrasi sistem darurat UriMed.</p>
        </div>
        
        <form method="GET" action="" class="flex flex-wrap items-center gap-2 bg-white p-2 rounded-lg border border-[#DEE2E6] shadow-sm">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama penyakit..." class="h-9 px-3 border border-[#DEE2E6] rounded text-xs focus:outline-none focus:border-[#0D6EFD] min-w-[180px]">
            
            <select name="filter_az" class="h-9 px-2 border border-[#DEE2E6] rounded text-xs bg-white focus:outline-none">
                <option value="">Urutan Default</option>
                <option value="az" <?php echo $filter_az === 'az' ? 'selected' : ''; ?>>Alfabet: A ke Z</option>
                <option value="za" <?php echo $filter_az === 'za' ? 'selected' : ''; ?>>Alfabet: Z ke A</option>
            </select>
            
            <button type="submit" class="h-9 px-4 bg-[#1C4580] hover:bg-[#14325c] text-white font-bold text-xs rounded uppercase tracking-wider transition">Cari</button>
            <?php if ($search !== '' || $filter_az !== ''): ?>
                <a href="master_penyakit.php" class="h-9 px-3 bg-gray-100 text-gray-600 border border-gray-200 rounded flex items-center text-xs font-semibold hover:bg-gray-200">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-5 rounded-xl border border-[#DEE2E6] h-fit shadow-sm">
            <?php if ($editData): ?>
                <h2 class="font-bold text-sm text-[#0D6EFD] uppercase mb-4">Edit Master Penyakit (#<?php echo $editData['id']; ?>)</h2>
                <form action="" method="POST" class="space-y-4">
                    <input type="hidden" name="id_penyakit" value="<?php echo $editData['id']; ?>">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Penyakit</label>
                        <input type="text" name="nama_penyakit" value="<?php echo htmlspecialchars($editData['nama_penyakit']); ?>" required class="w-full h-10 px-3 border border-[#0D6EFD] rounded text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Umum</label>
                        <textarea name="deskripsi_umum" rows="4" class="w-full p-3 border border-[#0D6EFD] rounded text-sm focus:outline-none"><?php echo htmlspecialchars($editData['deskripsi_umum'] ?? ''); ?></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" name="update_penyakit" class="flex-1 h-11 bg-[#0D6EFD] text-white text-xs font-bold rounded uppercase tracking-wider hover:bg-[#0b5ed7] transition">Simpan</button>
                        <a href="master_penyakit.php" class="h-11 px-4 bg-gray-200 text-gray-700 text-xs font-bold rounded flex items-center justify-center uppercase hover:bg-gray-300 transition">Batal</a>
                    </div>
                </form>
            <?php else: ?>
                <h2 class="font-bold text-sm text-[#1C4580] uppercase mb-4">Tambah Master Penyakit</h2>
                <form action="" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Penyakit</label>
                        <input type="text" name="nama_penyakit" required class="w-full h-10 px-3 border border-[#DEE2E6] rounded text-sm focus:outline-none focus:border-[#0D6EFD]" placeholder="Contoh: Gastritis (Maag)">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Umum</label>
                        <textarea name="deskripsi_umum" rows="4" class="w-full p-3 border border-[#DEE2E6] rounded text-sm focus:outline-none focus:border-[#0D6EFD]" placeholder="Deskripsi ringkas edukatif medis..."></textarea>
                    </div>
                    <button type="submit" name="tambah_penyakit" class="w-full h-11 bg-[#00A14B] text-white text-xs font-bold rounded uppercase tracking-wider hover:bg-[#008A42] transition">Tambah Data</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-[#DEE2E6] overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F8F9FA] border-b border-[#DEE2E6] text-xs font-bold text-[#6C757D] uppercase">
                        <tr>
                            <th class="p-4 w-16">ID</th>
                            <th class="p-4 w-1/3">Nama Penyakit</th>
                            <th class="p-4">Deskripsi Medis</th>
                            <th class="p-4 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DEE2E6]">
                        <?php if (count($listPenyakit) > 0): ?>
                            <?php foreach($listPenyakit as $p): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 font-mono text-xs text-gray-500">#<?php echo $p['id']; ?></td>
                                    <td class="p-4 font-bold text-[#1C4580]"><?php echo htmlspecialchars($p['nama_penyakit']); ?></td>
                                    <td class="p-4 text-xs text-[#6C757D] leading-relaxed"><?php echo htmlspecialchars($p['deskripsi_umum'] ?? '-'); ?></td>
                                    <td class="p-4 text-center whitespace-nowrap space-x-2">
                                        <a href="?edit_id=<?php echo $p['id']; ?>" class="text-xs text-[#0D6EFD] hover:underline font-semibold">Edit</a>
                                        <span class="text-gray-300">|</span>
                                        <a href="?delete_id=<?php echo $p['id']; ?>" onclick="return confirm('Yakin ingin menghapus data penyakit ini?')" class="text-xs text-[#DC3545] hover:underline font-semibold">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="p-8 text-center text-xs text-[#6C757D]">Data penyakit tidak ditemukan atau belum ada data.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php 
require_once '../components/footer.php'; 
?>
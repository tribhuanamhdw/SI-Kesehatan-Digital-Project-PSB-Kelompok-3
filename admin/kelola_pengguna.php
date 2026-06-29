<?php
require_once '../config/database.php';
require_once '../components/header.php';
require_once '../components/sidebar.php';

if(isset($_GET['toggle_id']) && isset($_GET['status'])) {
    $newStatus = $_GET['status'] === 'aktif' ? 'nonaktif' : 'aktif';
    $stmt = $pdo->prepare("UPDATE users SET status_akun = ? WHERE id = ? AND role = 'pengguna'");
    $stmt->execute([$newStatus, $_GET['toggle_id']]);
    header("Location: kelola_pengguna.php");
    exit;
}

$mahasiswa = $pdo->query("SELECT * FROM users WHERE role = 'pengguna' ORDER BY id DESC")->fetchAll();
?>

<main class="flex-1 p-4 md:p-6 bg-[#F8F9FA] w-full overflow-hidden">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#1C4580]">Daftar Manajemen Pengguna</h2>
        <p class="text-xs text-[#6C757D] mt-1">Otorisasi penuh verifikasi status rekam medis pengguna.</p>
    </div>

    <div class="bg-white rounded-xl border border-[#DEE2E6] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#F8F9FA] border-b border-[#DEE2E6] text-xs font-bold text-[#6C757D] uppercase">
                    <tr>
                        <th class="p-4">Nama Lengkap / NIK</th>
                        <th class="p-4">Email & Akun</th>
                        <th class="p-4 text-center">Goldar</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Tindakan Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DEE2E6]">
                    <?php foreach($mahasiswa as $m): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <p class="font-bold text-[#1C4580]"><?php echo htmlspecialchars($m['nama_lengkap']); ?></p>
                                <p class="text-xs font-mono text-[#6C757D]"><?php echo htmlspecialchars($m['nik']); ?></p>
                            </td>
                            <td class="p-4">
                                <p class="text-sm"><?php echo htmlspecialchars($m['email']); ?></p>
                                <p class="text-xs bg-gray-100 px-1.5 py-0.5 rounded inline-block text-gray-600 font-mono mt-1">@<?php echo htmlspecialchars($m['username']); ?></p>
                            </td>
                            <td class="p-4 text-center font-bold text-gray-700"><?php echo htmlspecialchars($m['golongan_darah']); ?></td>
                            <td class="p-4 text-center">
                                <?php echo $m['status_akun'] === 'aktif' ? '<span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded-full">AKTIF</span>' : '<span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-1 rounded-full">SUSPENDED</span>'; ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="?toggle_id=<?php echo $m['id']; ?>&status=<?php echo $m['status_akun']; ?>" class="text-xs font-semibold border px-3 py-1.5 rounded transition inline-block <?php echo $m['status_akun'] === 'aktif' ? 'border-[#DC3545] text-[#DC3545] hover:bg-red-50' : 'border-[#00A14B] text-[#00A14B] hover:bg-green-50'; ?>">
                                    <?php echo $m['status_akun'] === 'aktif' ? 'Bekukan Akun' : 'Aktifkan Kembali'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php 
require_once '../components/footer.php'; 
?>
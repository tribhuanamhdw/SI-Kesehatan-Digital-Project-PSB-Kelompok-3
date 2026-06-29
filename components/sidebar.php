<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'pengguna';
$base_url = (strpos($_SERVER['REQUEST_URI'], '/UriMed') !== false) ? '/UriMed' : '';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-full md:w-auto md:min-w-[250px] bg-[#1E293B] text-[#94A3B8] flex flex-col border-b md:border-b-0 md:border-r border-[#334155] shadow-md shrink-0">
    
    <div class="p-4 bg-[#0F172A] border-b border-[#334155] hidden md:block">
        <p class="text-[10px] uppercase tracking-wider text-[#64748B] font-bold">Log Masuk Sebagai</p>
        <h4 class="text-sm font-bold text-white truncate mt-0.5"><?php echo htmlspecialchars($_SESSION['nama'] ?? 'Pengguna'); ?></h4>
        <span class="inline-block bg-[#0284C7] text-white text-[10px] font-bold px-2 py-0.5 rounded-full mt-1.5 uppercase">
            <?php echo htmlspecialchars($current_role); ?>
        </span>
    </div>

    <nav class="flex flex-row md:flex-col p-2 md:p-4 gap-1 overflow-x-auto md:overflow-x-visible">
        <?php if($current_role === 'admin'): ?>
            <a href="<?php echo $base_url; ?>/admin/dashboard.php" 
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium transition duration-150 shrink-0 md:shrink-1
               <?php echo ($current_page == 'dashboard.php') ? 'bg-[#0284C7] text-white font-semibold' : 'hover:bg-[#334155] hover:text-white'; ?>">
                <span>📊</span> <span class="whitespace-nowrap">Statistik Utama</span>
            </a>
            <a href="<?php echo $base_url; ?>/admin/master_penyakit.php" 
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium transition duration-150 shrink-0 md:shrink-1
               <?php echo ($current_page == 'master_penyakit.php') ? 'bg-[#0284C7] text-white font-semibold' : 'hover:bg-[#334155] hover:text-white'; ?>">
                <span>🦠</span> <span class="whitespace-nowrap">Master Penyakit</span>
            </a>
            <a href="<?php echo $base_url; ?>/admin/kelola_pengguna.php" 
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium transition duration-150 shrink-0 md:shrink-1
               <?php echo ($current_page == 'kelola_pengguna.php') ? 'bg-[#0284C7] text-white font-semibold' : 'hover:bg-[#334155] hover:text-white'; ?>">
                <span>👥</span> <span class="whitespace-nowrap">Kelola Pengguna</span>
            </a>
        <?php else: ?>
            <a href="<?php echo $base_url; ?>/pengguna/dashboard.php" 
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium transition duration-150 shrink-0 md:shrink-1
               <?php echo ($current_page == 'dashboard.php') ? 'bg-[#0284C7] text-white font-semibold' : 'hover:bg-[#334155] hover:text-white'; ?>">
                <span>🏠</span> <span class="whitespace-nowrap">ID Darurat QR</span>
            </a>
            <a href="<?php echo $base_url; ?>/pengguna/riwayat_penyakit.php" 
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium transition duration-150 shrink-0 md:shrink-1
               <?php echo ($current_page == 'riwayat_penyakit.php') ? 'bg-[#0284C7] text-white font-semibold' : 'hover:bg-[#334155] hover:text-white'; ?>">
                <span>📝</span> <span class="whitespace-nowrap">Riwayat Medis</span>
            </a>
            <a href="<?php echo $base_url; ?>/pengguna/profil.php" 
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium transition duration-150 shrink-0 md:shrink-1
               <?php echo ($current_page == 'profil.php') ? 'bg-[#0284C7] text-white font-semibold' : 'hover:bg-[#334155] hover:text-white'; ?>">
                <span>⚙️</span> <span class="whitespace-nowrap">Profil & Wali</span>
            </a>
        <?php endif; ?>
    </nav>
</aside>
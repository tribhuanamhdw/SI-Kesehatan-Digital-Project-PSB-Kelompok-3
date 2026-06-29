<?php
$base_path = isset($base_asset) ? $base_asset : '';
?>
<nav class="flex justify-between items-center px-4 md:px-8 h-16 bg-white shadow-sm sticky top-0 z-50 w-full border-b border-gray-100">
    <div class="flex items-center space-x-2">
        <img src="<?php echo $base_path; ?>assets/img/Logo UriMed.jpg" alt="Logo UriMed" class="h-9 w-9 rounded-full object-cover border border-gray-200 shadow-sm">
        <span class="text-xl font-black text-[#1C4580] tracking-wider">UriMed</span>
    </div>
    
    <div class="flex items-center space-x-4">
        <a href="<?php echo $base_path; ?>login.php" class="text-sm font-bold text-[#1C4580] hover:text-[#14325c] hover:underline transition">
            Masuk
        </a>
        <a href="<?php echo $base_path; ?>register.php" class="bg-[#00A14B] hover:bg-[#008A42] text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
            Daftar
        </a>
    </div>
</nav>
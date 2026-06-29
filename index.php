<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: pengguna/dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UriMed - Sistem Rekam Medis Mandiri & ID Darurat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>
<body class="bg-[#F8F9FA] text-[#212529] min-h-screen flex flex-col justify-between">
    
    <header class="bg-white border-b border-[#DEE2E6] h-16 flex items-center justify-between px-4 md:px-6 shadow-sm sticky top-0 z-50">
        <div class="flex items-center space-x-2">
            <img src="assets/img/Logo UriMed.jpg" alt="Logo UriMed" class="h-9 w-9 rounded-full object-cover border border-gray-200 shadow-sm">
            <span class="text-xl font-black text-[#1C4580] tracking-wider">UriMed</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="login.php" class="text-sm font-bold text-[#1C4580] hover:text-[#14325c] hover:underline transition">
                Masuk
            </a>
            <a href="register.php" class="bg-[#00A14B] hover:bg-[#008A42] text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition">
                Daftar
            </a>
        </div>
    </header>

    <main class="flex-1 flex flex-col items-center justify-center text-center px-4 max-w-3xl mx-auto py-12">
        <div class="bg-emerald-50 text-[#00A14B] text-xs font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
            <span>🏥</span> Sistem Terintegrasi Fasilitas Kesehatan
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-[#1C4580] tracking-tight leading-tight mb-4">
            Akses Data Medis Darurat Anda Lewat Sekali Pindai.
        </h1>
        <p class="text-sm md:text-base text-[#6C757D] mb-8 max-w-xl leading-relaxed">
            UriMed membantu Anda mencatat riwayat penyakit mandiri, dosis obat harian, dan menyediakan QR Code darurat yang dapat menyelamatkan nyawa saat kondisi kritis.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto max-w-sm sm:max-w-none">
            <a href="register.php" class="h-12 px-8 bg-[#00A14B] hover:bg-[#008A42] text-white font-bold text-sm rounded-xl flex items-center justify-center shadow-md transition transform hover:-translate-y-0.5 tracking-wide">
                Mulai Registrasi Mandiri
            </a>
            <a href="login.php" class="h-12 px-8 bg-white text-[#1C4580] border-2 border-[#1C4580] hover:bg-blue-50 font-bold text-sm rounded-xl flex items-center justify-center transition transform hover:-translate-y-0.5 tracking-wide">
                Masuk ke Dashboard
            </a>
        </div>
    </main>

    <footer class="bg-white border-t border-[#DEE2E6] py-4 text-center text-xs text-[#6C757D]">
        &copy; <?php echo date('Y'); ?> UriMed Digital Health System. All Rights Reserved.
    </footer>
    <script src="assets/js/main.js"></script>
</body>
</html>
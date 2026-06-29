<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php"); 
    exit;
}

$base_asset = "/"; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard UriMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo $base_asset; ?>assets/css/tailwind.css">
</head>
<body class="bg-[#F8F9FA] text-[#212529] min-h-screen flex flex-col">
    
    <header class="bg-[#1C4580] text-white h-16 flex items-center justify-between px-4 md:px-6 shadow-md sticky top-0 z-50 w-full">
        <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
                <img src="<?php echo $base_asset; ?>assets/img/Logo UriMed.jpg" alt="Logo UriMed" class="h-9 w-9 rounded-full object-cover border border-white/20 shadow-sm">
                <span class="text-xl font-black tracking-wider text-white">UriMed</span>
            </div>
            
            <span class="bg-[#00A14B] text-[10px] uppercase font-bold px-2 py-0.5 rounded-sm">
                <?php echo htmlspecialchars($_SESSION['role'] ?? 'guest'); ?> Mode
            </span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm hidden sm:inline-block font-medium">Halo, <?php echo htmlspecialchars($_SESSION['nama'] ?? 'User'); ?></span>
            <a href="<?php echo $base_asset; ?>logout.php" class="bg-[#DC3545] hover:bg-[#BB2D3B] text-xs font-bold px-3 py-2 rounded transition">
                Keluar
            </a>
        </div>
    </header>

    <div class="flex flex-col md:flex-row flex-1 w-full relative">
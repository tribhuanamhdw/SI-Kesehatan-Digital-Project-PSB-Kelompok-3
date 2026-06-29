<?php
require 'config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

function kirimEmail($nama, $email, $token_qr)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('EMAIL_USER');
        $mail->Password   = getenv('SMTP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom(getenv('EMAIL_USER'), 'UriMed Health System');
        $mail->addAddress($email, $nama);

        $mail->isHTML(true);
        $mail->Subject = 'Konfirmasi Pendaftaran Pasien UriMed';
        
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #DEE2E6; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #1C4580; padding: 20px; text-align: center; color: white;'>
                    <h2>UriMed Digital Health</h2>
                </div>
                <div style='padding: 24px; color: #212529; line-height: 1.6;'>
                    <p>Halo, <b>{$nama}</b></p>
                    <p>Terima kasih telah melakukan pendaftaran. Data rekam medis mandiri Anda berhasil diterima oleh sistem.</p>
                    <p><b>ID Darurat/Bypass Anda:</b> <span style='font-family:monospace; background:#f4f4f4; padding:2px 6px; border-radius:4px;'>{$token_qr}</span></p>
                    <br>
                    <p style='color: #6C757D; font-size: 12px;'>Ini adalah email otomatis dari sistem UriMed, mohon untuk tidak membalas pesan ini.</p>
                </div>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $nik = trim($_POST['nik']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    
    $token_qr = 'user_' . bin2hex(random_bytes(3));

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, nama_lengkap, nik, tanggal_lahir, token_qr, role, status_akun) VALUES (?, ?, ?, ?, ?, ?, ?, 'pengguna', 'aktif')");
        $stmt->execute([$username, $email, $password, $nama_lengkap, $nik, $tanggal_lahir, $token_qr]);

        kirimEmail($nama_lengkap, $email, $token_qr);

        $status = 'success';
        $message = "Akun sukses dibuat! Silakan cek email Anda dan login ke aplikasi.";
    } catch (\PDOException $e) {
        $status = 'error';
        $message = "Gagal: Identitas (Username/NIK/Email) sudah pernah didaftarkan sebelumnya!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pasien - UriMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>
<body class="bg-[#F8F9FA] text-[#212529] min-h-screen flex flex-col justify-between p-4 py-8">

    <div class="w-full max-w-lg mx-auto mb-4 px-2 md:px-0">
        <a href="index.php" class="inline-flex items-center text-xs md:text-sm font-bold text-[#6C757D] hover:text-[#1C4580] hover:underline transition-all duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Beranda
        </a>
    </div>

    <main class="flex-1 flex flex-col items-center justify-center w-full">
        <div class="bg-white p-6 md:p-8 rounded-2xl border border-[#DEE2E6] shadow-sm w-full max-w-lg text-center">
            
            <div class="flex flex-col items-center mb-6">
                <img src="assets/img/Logo UriMed.jpg" alt="Logo UriMed" class="h-12 w-12 rounded-full object-cover border border-gray-100 shadow-sm mb-2">
                <h2 class="text-xl font-black text-[#1C4580]">Registrasi Pasien Mandiri</h2>
                <p class="text-xs text-[#6C757D]">Lengkapi identitas Anda secara valid</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg text-xs font-medium flex items-start gap-3 <?php echo $status === 'success' ? 'bg-[#D1E7DD] text-[#0F5132] border border-[#A3CFBB]' : 'bg-[#F8D7DA] text-[#842029] border border-[#F5C2C7]'; ?>">
                    <span class="text-lg"><?php echo $status === 'success' ? '✅' : '⚠️'; ?></span>
                    <div><?php echo $message; ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Nama Lengkap Sesuai KTP</label>
                    <input type="text" name="nama_lengkap" required placeholder="Nama lengkap Anda" 
                           class="w-full h-11 px-4 bg-gray-50 border border-[#DEE2E6] rounded-xl text-sm focus:outline-none focus:border-[#00A14B] focus:ring-2 focus:ring-green-50 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">NIK</label>
                    <input type="text" name="nik" required placeholder="5103xxxxxxxxxxxx" 
                           class="w-full h-11 px-4 bg-gray-50 border border-[#DEE2E6] rounded-xl text-sm focus:outline-none focus:border-[#00A14B] focus:ring-2 focus:ring-green-50 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" required 
                           class="w-full h-11 px-4 bg-gray-50 border border-[#DEE2E6] rounded-xl text-sm focus:outline-none focus:border-[#00A14B] focus:ring-2 focus:ring-green-50 transition">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Alamat Email</label>
                    <input type="email" name="email" required placeholder="example@gmail.com" 
                           class="w-full h-11 px-4 bg-gray-50 border border-[#DEE2E6] rounded-xl text-sm focus:outline-none focus:border-[#00A14B] focus:ring-2 focus:ring-green-50 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Username</label>
                    <input type="text" name="username" required placeholder="exampleuser" 
                           class="w-full h-11 px-4 bg-gray-50 border border-[#DEE2E6] rounded-xl text-sm focus:outline-none focus:border-[#00A14B] focus:ring-2 focus:ring-green-50 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full h-11 px-4 bg-gray-50 border border-[#DEE2E6] rounded-xl text-sm focus:outline-none focus:border-[#00A14B] focus:ring-2 focus:ring-green-50 transition">
                </div>
                
                <div class="md:col-span-2 mt-2">
                    <button type="submit" class="w-full h-11 bg-[#00A14B] hover:bg-[#008A42] text-white font-bold text-sm rounded-xl shadow-md active:scale-[0.99] transition duration-150">
                        Daftar
                    </button>
                </div>
            </form>

            <div class="mt-6 border-t border-gray-100 pt-4 text-center">
                <p class="text-xs text-[#6C757D]">Sudah memiliki akun? <a href="login.php" class="text-[#1C4580] font-bold hover:underline">Masuk di sini</a></p>
            </div>
        </div>
    </main>

    <footer class="py-4 text-center text-[10px] text-[#6C757D] uppercase tracking-wider">
        &copy; 2026 UriMed Digital Health System. All Rights Reserved.
    </footer>

</body>
</html>
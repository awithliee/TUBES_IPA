<?php
/**
 * File Proses Insert Data Barang
 * Menggunakan Prepared Statements untuk Keamanan Level 3
 * 
 * KEAMANAN:
 * - Prepared Statements mencegah SQL Injection
 * - Validasi server-side untuk semua input
 * - Error handling yang aman (tidak expose informasi sensitif)
 */

// Include konfigurasi database
require_once 'config.php';

// Cek apakah form dikirim dengan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Ambil dan bersihkan data dari form
$nama_barang = trim($_POST['nama_barang']);
$stok = (int)$_POST['stok'];
$harga = (float)$_POST['harga'];

// ======================================================
// VALIDASI SERVER-SIDE (Level 3 Security)
// ======================================================
$errors = [];

// Validasi Nama Barang
if (empty($nama_barang)) {
    $errors[] = "Nama barang tidak boleh kosong";
} elseif (strlen($nama_barang) < 3) {
    $errors[] = "Nama barang minimal 3 karakter";
} elseif (strlen($nama_barang) > 100) {
    $errors[] = "Nama barang maksimal 100 karakter";
}

// Validasi Stok
if ($stok < 0) {
    $errors[] = "Stok tidak boleh negatif";
}

// Validasi Harga
if ($harga <= 0) {
    $errors[] = "Harga harus lebih dari 0";
}

// Jika ada error, tampilkan dan stop proses
if (!empty($errors)) {
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error Validasi</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='bg-light'>
        <div class='container mt-5'>
            <div class='alert alert-danger'>
                <h4><i class='bi bi-exclamation-triangle'></i> Error Validasi!</h4>
                <ul class='mb-0'>";
    
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    
    echo "      </ul>
            </div>
            <a href='tambah.php' class='btn btn-primary'>
                <i class='bi bi-arrow-left'></i> Kembali ke Form
            </a>
        </div>
    </body>
    </html>";
    exit();
}

// ======================================================
// INSERT DATA DENGAN PREPARED STATEMENTS (Level 3 Security)
// ======================================================

/**
 * PENJELASAN PREPARED STATEMENTS:
 * 
 * Prepared Statements adalah cara AMAN untuk memasukkan data ke database.
 * 
 * Metode Lama (BAHAYA - SQL Injection):
 * $query = "INSERT INTO barang VALUES ('$nama', $stok, $harga)";
 * 
 * Jika user input: ' OR '1'='1
 * Query menjadi: INSERT INTO barang VALUES ('' OR '1'='1', ...)
 * Ini bisa merusak database!
 * 
 * Metode Baru (AMAN - Prepared Statements):
 * Data dipisahkan dari query, sehingga tidak bisa dimanipulasi.
 */

// Step 1: Buat prepared statement dengan placeholder (?)
$stmt = mysqli_prepare($conn, "INSERT INTO barang (nama_barang, stok, harga) VALUES (?, ?, ?)");

if (!$stmt) {
    // Log error untuk admin (jangan tampilkan ke user)
    error_log("Prepare Statement Failed: " . mysqli_error($conn));
    
    // Tampilkan pesan user-friendly
    die("
    <div style='padding: 20px; background: #f44336; color: white; text-align: center;'>
        <h2>⚠️ Terjadi Kesalahan</h2>
        <p>Gagal memproses data. Silakan coba lagi atau hubungi administrator.</p>
        <a href='tambah.php' style='color: white; text-decoration: underline;'>Kembali ke Form</a>
    </div>
    ");
}

// Step 2: Bind parameter ke statement
// "sid" = string, integer, double (tipe data masing-masing parameter)
mysqli_stmt_bind_param($stmt, "sid", $nama_barang, $stok, $harga);

// Step 3: Eksekusi statement
$success = mysqli_stmt_execute($stmt);

// Step 4: Cek hasil eksekusi
if ($success) {
    // Ambil ID data yang baru diinsert
    $inserted_id = mysqli_insert_id($conn);
    
    // Redirect dengan pesan sukses
    header("Location: index.php?status=success&id=" . $inserted_id);
    exit();
} else {
    // Log error
    error_log("Insert Failed: " . mysqli_stmt_error($stmt));
    
    // Tampilkan error page
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error Insert</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="alert alert-danger">
                <h4><i class="bi bi-x-circle"></i> Gagal Menyimpan Data!</h4>
                <p>Terjadi kesalahan saat menyimpan data ke database.</p>
                <hr>
                <small class="text-muted">Error Code: DB_INSERT_FAILED</small>
            </div>
            <a href="tambah.php" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Kembali ke Form
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-house"></i> Ke Halaman Utama
            </a>
        </div>
    </body>
    </html>
    <?php
}

// Step 5: Tutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($conn);

/**
 * CATATAN UNTUK PRESENTASI VIDEO:
 * 
 * 1. Tunjukkan kode Prepared Statements ini sebagai fitur keamanan Level 3
 * 2. Jelaskan perbedaan dengan query biasa (vulnerable vs secure)
 * 3. Demo: coba input dengan karakter khusus (', ", <, >) - tetap aman!
 * 4. Highlight validasi server-side yang mencegah data invalid
 * 5. Tunjukkan error handling yang tidak expose informasi sensitif
 */
?>
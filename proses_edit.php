<?php
/**
 * File Proses Update Data Barang
 * Menggunakan Prepared Statements untuk Keamanan Level 3
 * 
 * KEAMANAN:
 * - Prepared Statements mencegah SQL Injection
 * - Validasi server-side untuk semua input
 * - Verifikasi ID sebelum update
 */

// Include konfigurasi database
require_once 'config.php';

// Cek apakah form dikirim dengan method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// Ambil dan bersihkan data dari form
$id = (int)$_POST['id'];
$nama_barang = trim($_POST['nama_barang']);
$stok = (int)$_POST['stok'];
$harga = (float)$_POST['harga'];

// ======================================================
// VALIDASI SERVER-SIDE (Level 3 Security)
// ======================================================
$errors = [];

// Validasi ID
if ($id <= 0) {
    $errors[] = "ID tidak valid";
}

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
    $error_message = implode(", ", $errors);
    header("Location: edit.php?id=$id&status=error&message=" . urlencode($error_message));
    exit();
}

// ======================================================
// CEK APAKAH DATA DENGAN ID TERSEBUT ADA
// ======================================================
$check_query = "SELECT id FROM barang WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "i", $id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) === 0) {
    mysqli_stmt_close($check_stmt);
    mysqli_close($conn);
    header("Location: index.php?status=error&message=" . urlencode("Data tidak ditemukan"));
    exit();
}
mysqli_stmt_close($check_stmt);

// ======================================================
// UPDATE DATA DENGAN PREPARED STATEMENTS (Level 3 Security)
// ======================================================

/**
 * PENJELASAN PREPARED STATEMENTS UNTUK UPDATE:
 * 
 * UPDATE dengan Prepared Statements memastikan:
 * 1. Data yang diupdate tidak bisa dimanipulasi untuk SQL Injection
 * 2. Tipe data terjaga (string, integer, double)
 * 3. WHERE clause aman dari serangan
 * 
 * Format: UPDATE table SET col1=?, col2=? WHERE id=?
 */

// Step 1: Buat prepared statement dengan placeholder
$stmt = mysqli_prepare($conn, "UPDATE barang SET nama_barang = ?, stok = ?, harga = ? WHERE id = ?");

if (!$stmt) {
    // Log error untuk admin
    error_log("Prepare Statement Failed: " . mysqli_error($conn));
    
    // Redirect dengan pesan error
    header("Location: edit.php?id=$id&status=error&message=" . urlencode("Gagal mempersiapkan query"));
    exit();
}

// Step 2: Bind parameter ke statement
// "sidi" = string, integer, double, integer (tipe data parameter)
mysqli_stmt_bind_param($stmt, "sidi", $nama_barang, $stok, $harga, $id);

// Step 3: Eksekusi statement
$success = mysqli_stmt_execute($stmt);

// Step 4: Cek hasil eksekusi
if ($success) {
    // Cek apakah ada data yang benar-benar berubah
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    if ($affected_rows > 0) {
        // Data berhasil diupdate
        header("Location: index.php?status=success&message=" . urlencode("Data berhasil diperbarui!"));
    } else {
        // Tidak ada perubahan (data sama dengan sebelumnya)
        header("Location: index.php?status=warning&message=" . urlencode("Tidak ada perubahan data"));
    }
    exit();
} else {
    // Log error
    error_log("Update Failed: " . mysqli_stmt_error($stmt));
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // Redirect dengan pesan error
    header("Location: edit.php?id=$id&status=error&message=" . urlencode("Gagal mengupdate data"));
    exit();
}

/**
 * CATATAN UNTUK PRESENTASI VIDEO:
 * 
 * 1. Tunjukkan bahwa UPDATE juga menggunakan Prepared Statements
 * 2. Jelaskan keamanan WHERE clause yang menggunakan parameter binding
 * 3. Demo: edit data dengan input normal → berhasil
 * 4. Demo: coba manipulasi ID di URL → sistem menolak karena validasi
 * 5. Highlight affected_rows untuk deteksi perubahan data
 */
?>
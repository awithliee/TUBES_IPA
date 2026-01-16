<?php
// Include konfigurasi database
require_once 'config.php';

// Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?status=error&message=" . urlencode("ID tidak ditemukan"));
    exit();
}

$id = (int)$_GET['id'];

// ======================================================
// VALIDASI ID
// ======================================================
if ($id <= 0) {
    header("Location: index.php?status=error&message=" . urlencode("ID tidak valid"));
    exit();
}

// ======================================================
// CEK APAKAH DATA DENGAN ID TERSEBUT ADA
// ======================================================
$check_query = "SELECT id, nama_barang FROM barang WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_query);

if (!$check_stmt) {
    error_log("Prepare Statement Failed: " . mysqli_error($conn));
    header("Location: index.php?status=error&message=" . urlencode("Gagal memverifikasi data"));
    exit();
}

mysqli_stmt_bind_param($check_stmt, "i", $id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) === 0) {
    mysqli_stmt_close($check_stmt);
    mysqli_close($conn);
    header("Location: index.php?status=error&message=" . urlencode("Data tidak ditemukan"));
    exit();
}

// Ambil nama barang untuk notifikasi
$data = mysqli_fetch_assoc($check_result);
$nama_barang = $data['nama_barang'];
mysqli_stmt_close($check_stmt);

// ======================================================
// HAPUS DATA DENGAN PREPARED STATEMENTS (Level 3 Security)
// ======================================================


// Step 1: Buat prepared statement
$stmt = mysqli_prepare($conn, "DELETE FROM barang WHERE id = ?");

if (!$stmt) {
    error_log("Prepare Statement Failed: " . mysqli_error($conn));
    header("Location: index.php?status=error&message=" . urlencode("Gagal mempersiapkan query hapus"));
    exit();
}

// Step 2: Bind parameter
mysqli_stmt_bind_param($stmt, "i", $id);

// Step 3: Eksekusi statement
$success = mysqli_stmt_execute($stmt);

// Step 4: Cek hasil eksekusi
if ($success) {
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    if ($affected_rows > 0) {
        // Data berhasil dihapus
        $success_message = "Data '{$nama_barang}' berhasil dihapus!";
        header("Location: index.php?status=success&message=" . urlencode($success_message));
    } else {
        // Tidak ada data yang terhapus (seharusnya tidak terjadi karena sudah dicek)
        header("Location: index.php?status=warning&message=" . urlencode("Data tidak dapat dihapus"));
    }
    exit();
} else {
    // Log error
    error_log("Delete Failed: " . mysqli_stmt_error($stmt));
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // Redirect dengan pesan error
    header("Location: index.php?status=error&message=" . urlencode("Gagal menghapus data"));
    exit();
}
?>
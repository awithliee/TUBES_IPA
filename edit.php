<?php

// Include konfigurasi database
require_once 'config.php';

// Cek apakah ID ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?status=error&message=" . urlencode("ID tidak ditemukan"));
    exit();
}

$id = (int)$_GET['id'];

// Query untuk mengambil data berdasarkan ID
$query = "SELECT * FROM barang WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Cek apakah data ditemukan
if (mysqli_num_rows($result) === 0) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: index.php?status=error&message=" . urlencode("Data tidak ditemukan"));
    exit();
}

$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - <?= htmlspecialchars($data['nama_barang']) ?></title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-box-seam-fill"></i> Inventaris Barang
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house-fill"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-pencil-square"></i> Edit Barang
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Header -->
                <div class="text-center text-white mb-4">
                    <h2 class="fw-bold">
                        <i class="bi bi-pencil-square"></i> Edit Data Barang
                    </h2>
                    <p class="mb-0">Perbarui informasi barang dengan data yang benar</p>
                </div>

                <!-- Form Card -->
                <div class="card shadow-lg">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle"></i> Form Edit Barang (ID: <?= $data['id'] ?>)
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Form Edit dengan validasi HTML5 -->
                        <form action="proses_edit.php" method="POST" id="formEdit">
                            <!-- Hidden Input untuk ID -->
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            
                            <!-- Nama Barang -->
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label fw-bold">
                                    <i class="bi bi-tag"></i> Nama Barang <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control form-control-lg" 
                                    id="nama_barang" 
                                    name="nama_barang" 
                                    value="<?= htmlspecialchars($data['nama_barang']) ?>"
                                    required
                                    maxlength="100"
                                    autocomplete="off"
                                >
                                <small class="text-muted">Maksimal 100 karakter</small>
                            </div>

                            <!-- Stok -->
                            <div class="mb-3">
                                <label for="stok" class="form-label fw-bold">
                                    <i class="bi bi-boxes"></i> Stok (Unit) <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    class="form-control form-control-lg" 
                                    id="stok" 
                                    name="stok" 
                                    value="<?= $data['stok'] ?>"
                                    required
                                    min="0"
                                    max="999999"
                                >
                                <small class="text-muted">Jumlah barang yang tersedia</small>
                            </div>

                            <!-- Harga -->
                            <div class="mb-4">
                                <label for="harga" class="form-label fw-bold">
                                    <i class="bi bi-currency-dollar"></i> Harga (Rp) <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    class="form-control form-control-lg" 
                                    id="harga" 
                                    name="harga" 
                                    value="<?= $data['harga'] ?>"
                                    required
                                    min="0"
                                    step="0.01"
                                >
                                <small class="text-muted">Harga per unit dalam Rupiah</small>
                            </div>

                            <!-- Info Tanggal Input Awal -->
                            <div class="alert alert-info">
                                <i class="bi bi-calendar-check"></i> 
                                <small>
                                    <strong>Data Pertama Kali Diinput:</strong><br>
                                    <?= date('d F Y, H:i:s', strtotime($data['created_at'])) ?>
                                </small>
                            </div>

                            <!-- Tombol Action -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold">
                                    <i class="bi bi-save"></i> Simpan Perubahan
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Batal & Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>
                            <i class="bi bi-info-circle"></i> Semua field bertanda (*) wajib diisi
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Validasi Form Client-Side -->
    <script>
        document.getElementById('formEdit').addEventListener('submit', function(e) {
            const nama = document.getElementById('nama_barang').value.trim();
            const stok = parseInt(document.getElementById('stok').value);
            const harga = parseFloat(document.getElementById('harga').value);
            
            // Validasi tambahan
            if (nama.length < 3) {
                e.preventDefault();
                alert('⚠️ Nama barang minimal 3 karakter!');
                return false;
            }
            
            if (stok < 0) {
                e.preventDefault();
                alert('⚠️ Stok tidak boleh negatif!');
                return false;
            }
            
            if (harga <= 0) {
                e.preventDefault();
                alert('⚠️ Harga harus lebih dari 0!');
                return false;
            }

            // Konfirmasi sebelum submit
            if (!confirm('Apakah Anda yakin ingin menyimpan perubahan data ini?')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
<?php
// Tutup koneksi database
mysqli_close($conn);
?>
<?php

/**
 * Halaman Utama - CRUD Lengkap Sistem Inventaris Barang
 * Fitur: Create, Read, Update, Delete dengan tampilan modern
 */

// Include konfigurasi database
require_once 'config.php';

// Query untuk mengambil semua data barang
$query = "SELECT * FROM barang ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

// Ambil pesan notifikasi dari URL (jika ada)
$status = isset($_GET['status']) ? $_GET['status'] : '';
$message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Navbar Modern -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-box-seam-fill"></i> Inventaris Barang
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Notifikasi Alert -->
        <?php if ($status && $message): ?>
            <?php
            $alert_class = '';
            $alert_icon = '';
            switch ($status) {
                case 'success':
                    $alert_class = 'alert-success';
                    $alert_icon = 'check-circle-fill';
                    break;
                case 'error':
                    $alert_class = 'alert-danger';
                    $alert_icon = 'x-circle-fill';
                    break;
                case 'warning':
                    $alert_class = 'alert-warning';
                    $alert_icon = 'exclamation-triangle-fill';
                    break;
                default:
                    $alert_class = 'alert-info';
                    $alert_icon = 'info-circle-fill';
            }
            ?>
            <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= $alert_icon ?>"></i>
                <strong><?= htmlspecialchars($message) ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-white fw-bold mb-1">
                    <i class="bi bi-archive"></i> Daftar Inventaris
                </h2>
            </div>
            <a href="tambah.php" class="btn btn-primary btn-lg shadow">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </a>
        </div>

        <!-- Card Tabel Data -->
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Data Barang
                </h5>
                <span class="badge bg-light text-primary">
                    Total: <?= mysqli_num_rows($result) ?> Item
                </span>
            </div>
            <div class="card-body p-0">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <!-- Tabel Responsif -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="30%">Nama Barang</th>
                                    <th width="12%" class="text-center">Stok</th>
                                    <th width="18%" class="text-end">Harga</th>
                                    <th width="18%">Tanggal Input</th>
                                    <th width="17%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)):
                                    // Format harga ke Rupiah
                                    $harga_format = "Rp " . number_format($row['harga'], 0, ',', '.');

                                    // Format tanggal
                                    $tanggal = date('d M Y, H:i', strtotime($row['created_at']));

                                    // Badge warna berdasarkan stok
                                    $badge_class = $row['stok'] > 20 ? 'bg-success' : ($row['stok'] > 10 ? 'bg-warning text-dark' : 'bg-danger');
                                ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $no++ ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['nama_barang']) ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $badge_class ?> px-3 py-2">
                                                <i class="bi bi-boxes"></i> <?= $row['stok'] ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-primary"><?= $harga_format ?></strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3"></i> <?= $tanggal ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <!-- Tombol Edit -->
                                                <a href="edit.php?id=<?= $row['id'] ?>"
                                                    class="btn btn-warning btn-sm me-1"
                                                    title="Edit Data">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>

                                                <!-- Tombol Hapus dengan Modal -->
                                                <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal<?= $row['id'] ?>"
                                                    title="Hapus Data">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Konfirmasi Hapus -->
                                    <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">
                                                        <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-2">Apakah Anda yakin ingin menghapus barang ini?</p>
                                                    <div class="alert alert-warning">
                                                        <strong>Nama Barang:</strong> <?= htmlspecialchars($row['nama_barang']) ?><br>
                                                        <strong>Stok:</strong> <?= $row['stok'] ?> unit<br>
                                                        <strong>Harga:</strong> <?= $harga_format ?>
                                                    </div>
                                                    <p class="text-danger mb-0">
                                                        <i class="bi bi-info-circle"></i>
                                                        <small>Data yang dihapus tidak dapat dikembalikan!</small>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle"></i> Batal
                                                    </button>
                                                    <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger">
                                                        <i class="bi bi-trash"></i> Ya, Hapus!
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- Jika data kosong -->
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Belum Ada Data</h4>
                        <p class="text-muted">Klik tombol di bawah untuk menambah barang baru</p>
                        <a href="tambah.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Barang Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-muted">
                <div class="row align-items-center">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-hide alert setelah 5 detik -->
    <script>
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);
    </script>
</body>

</html>
<?php
// Tutup koneksi database
mysqli_close($conn);
?>
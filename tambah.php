<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - Sistem Inventaris</title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS (File Terpisah) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Header -->
                <div class="text-center text-white mb-4">
                    <h2 class="fw-bold">
                        <i class="bi bi-plus-circle"></i> Tambah Barang Baru
                    </h2>
                    <p>Masukkan data barang dengan lengkap dan benar</p>
                </div>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Form Input Barang</h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Form dengan validasi HTML5 -->
                        <form action="proses.php" method="POST" id="formTambah">
                            
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
                                    placeholder="Contoh: Laptop ASUS ROG Strix G15"
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
                                    placeholder="0"
                                    required
                                    min="0"
                                    max="999999"
                                    value="0"
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
                                    placeholder="0"
                                    required
                                    min="0"
                                    step="0.01"
                                    value="0"
                                >
                                <small class="text-muted">Harga per unit dalam Rupiah</small>
                            </div>
                            <!-- Tombol Action -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-save"></i> Simpan Data Barang
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small><i class="bi bi-info-circle"></i> Semua field bertanda (*) wajib diisi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Validasi Form Client-Side (Bonus) -->
    <script>
        document.getElementById('formTambah').addEventListener('submit', function(e) {
            const nama = document.getElementById('nama_barang').value.trim();
            const stok = document.getElementById('stok').value;
            const harga = document.getElementById('harga').value;
            
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
        });
    </script>
</body>
</html>
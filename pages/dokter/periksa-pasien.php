<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_dokter = $_SESSION['user_id'];

// Endpoint untuk mengecek data baru
if(isset($_GET['check_new_data'])) {
    $last_count = isset($_GET['last_count']) ? $_GET['last_count'] : 0;
    
    $count_query = "SELECT COUNT(*) as total 
                    FROM daftar_poli dp
                    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                    WHERE jp.id_dokter = '$id_dokter'
                    AND dp.status != 'selesai'";
    $count_result = mysqli_query($koneksi, $count_query);
    $current_count = mysqli_fetch_assoc($count_result)['total'];
    
    if($current_count != $last_count) {
        echo "refresh";
    } else {
        echo "no_change";
    }
    exit;
}

// Ambil daftar pasien yang mendaftar di poli dokter tersebut
$query = "SELECT dp.*, p.nama as nama_pasien, p.no_rm 
          FROM daftar_poli dp
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN pasien p ON dp.id_pasien = p.id
          WHERE jp.id_dokter = '$id_dokter'
          AND dp.status != 'selesai'
          ORDER BY dp.no_antrian";
$result = mysqli_query($koneksi, $query);

// Update status menjadi 'diperiksa'
if(isset($_POST['mulai_periksa'])) {
    $id_daftar_poli = $_POST['id_daftar_poli'];
    $query_update = "UPDATE daftar_poli SET status = 'diperiksa' 
                    WHERE id = '$id_daftar_poli'";
    mysqli_query($koneksi, $query_update);
    
    echo "<script>
        alert('Status pasien diupdate!');
        window.location.href='periksa-pasien.php';
    </script>";
}

// Proses pemeriksaan
if(isset($_POST['periksa'])) {
    $id_daftar_poli = $_POST['id_daftar_poli'];
    $tgl_periksa = date('Y-m-d H:i:s');
    $catatan = $_POST['catatan'];
    
    // Biaya periksa dasar (jasa dokter)
    $biaya_periksa = 150000;
    $total_biaya = $biaya_periksa;
    
    // Insert ke tabel periksa
    $query = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) 
              VALUES ('$id_daftar_poli', '$tgl_periksa', '$catatan', '$biaya_periksa')";
    
    if(mysqli_query($koneksi, $query)) {
        $id_periksa = mysqli_insert_id($koneksi);
        
        // Jika ada obat yang dipilih
        if(isset($_POST['obat']) && !empty($_POST['obat'])) {
            $obat = $_POST['obat']; // array id obat
            $total_biaya_obat = 0;
            
            // Insert detail obat dan hitung total
            foreach($obat as $id_obat) {
                // Ambil harga obat
                $query_harga = "SELECT harga FROM obat WHERE id = '$id_obat'";
                $result_harga = mysqli_query($koneksi, $query_harga);
                $data_obat = mysqli_fetch_assoc($result_harga);
                $harga_obat = $data_obat['harga'];
                $total_biaya_obat += $harga_obat;

                // Insert detail_periksa
                $query_detail = "INSERT INTO detail_periksa (id_periksa, id_obat) 
                               VALUES ('$id_periksa', '$id_obat')";
                mysqli_query($koneksi, $query_detail);
            }
            
            // Update total biaya termasuk obat
            $total_biaya = $biaya_periksa + $total_biaya_obat;
            $query_update = "UPDATE periksa 
                            SET biaya_periksa = '$total_biaya'
                            WHERE id = '$id_periksa'";
            mysqli_query($koneksi, $query_update);
        }

        // Update status menjadi selesai
        $query_status = "UPDATE daftar_poli 
                        SET status = 'selesai' 
                        WHERE id = '$id_daftar_poli'";
        mysqli_query($koneksi, $query_status);
        
        echo "<script>
            alert('Pemeriksaan berhasil disimpan!');
            window.location.href='periksa-pasien.php';
        </script>";
    }
}
?>

<style>
/* Hapus semua animasi dan loading */
* {
    -webkit-transition: none !important;
    -moz-transition: none !important;
    -o-transition: none !important;
    transition: none !important;
    -webkit-animation: none !important;
    -moz-animation: none !important;
    -o-animation: none !important;
    animation: none !important;
}

/* Hapus loading indicators */
.spinner-border,
.spinner-grow,
.loading,
.preloader,
.overlay {
    display: none !important;
}

/* Modal tanpa animasi */
.modal {
    display: none;
    background: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: block;
}

.modal-backdrop {
    display: none;
}

.modal-backdrop.show {
    display: block;
    opacity: 0.5;
}

/* Hapus efek hover */
.btn:hover,
.form-control:hover,
.select2-container:hover {
    transform: none !important;
    box-shadow: none !important;
}

.obat-container {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
}

.obat-item {
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.obat-item:last-child {
    border-bottom: none;
}

.obat-item:hover {
    background-color: #f8f9fa;
}

.obat-item label {
    margin-left: 10px;
    cursor: pointer;
}

.card-body p {
    margin-bottom: 10px;
}
</style>

<script>
$(document).ready(function() {
    let updateInterval;
    let isModalOpen = false;

    // Konfigurasi DataTable
    const table = $('#tabelPasien').DataTable({
        "processing": false,
        "serverSide": false,
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Fungsi untuk update data
    function checkForUpdates() {
        if (!isModalOpen) {
            $.ajax({
                url: 'check_updates.php',
                method: 'GET',
                data: { last_update: <?php echo $_SESSION['last_update']; ?> },
                success: function(response) {
                    if (response.needsUpdate) {
                        window.location.reload();
                    }
                }
            });
        }
    }

    // Event handlers untuk modal
    $(document).on('show.bs.modal', '.modal', function() {
        isModalOpen = true;
        clearInterval(updateInterval);
    });

    $(document).on('hidden.bs.modal', '.modal', function() {
        isModalOpen = false;
        updateInterval = setInterval(checkForUpdates, 30000);
    });

    // Inisialisasi interval update
    updateInterval = setInterval(checkForUpdates, 30000);
});
</script>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Periksa Pasien</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No RM</th>
                                        <th>Nama Pasien</th>
                                        <th>No Antrian</th>
                                        <th>Keluhan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    mysqli_data_seek($result, 0); // Reset pointer
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($result)) { 
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['no_rm']; ?></td>
                                        <td><?php echo $row['nama_pasien']; ?></td>
                                        <td><?php echo $row['no_antrian']; ?></td>
                                        <td><?php echo $row['keluhan']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $row['status'] == 'menunggu' ? 'warning' : 
                                                    ($row['status'] == 'diperiksa' ? 'primary' : 'success'); 
                                            ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($row['status'] == 'menunggu'): ?>
                                                <form method="post" style="display: inline;">
                                                    <input type="hidden" name="id_daftar_poli" value="<?php echo $row['id']; ?>">
                                                    <button type="submit" name="mulai_periksa" class="btn btn-info btn-sm">
                                                        Mulai Periksa
                                                    </button>
                                                </form>
                                            <?php elseif($row['status'] == 'diperiksa'): ?>
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        data-toggle="modal" 
                                                        data-target="#periksaModal<?php echo $row['id']; ?>">
                                                    Periksa
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- Modal untuk setiap baris -->
                                    <?php if($row['status'] == 'diperiksa'): ?>
                                    <div class="modal fade" id="periksaModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Periksa Pasien: <?php echo $row['nama_pasien']; ?></h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_daftar_poli" value="<?php echo $row['id']; ?>">
                                                        
                                                        <!-- Catatan Pemeriksaan -->
                                                        <div class="form-group">
                                                            <label>Catatan Pemeriksaan</label>
                                                            <textarea class="form-control" name="catatan" rows="3" required></textarea>
                                                        </div>

                                                        <!-- Daftar Obat dengan Checkbox -->
                                                        <div class="form-group">
                                                            <label>Pilih Obat</label>
                                                            <div class="obat-container">
                                                                <?php
                                                                $query_obat = "SELECT * FROM obat ORDER BY nama_obat";
                                                                $result_obat = mysqli_query($koneksi, $query_obat);
                                                                while($obat = mysqli_fetch_assoc($result_obat)) {
                                                                ?>
                                                                    <div class="obat-item">
                                                                        <input type="checkbox" 
                                                                               class="obat-checkbox" 
                                                                               name="obat[]" 
                                                                               value="<?php echo $obat['id']; ?>"
                                                                               data-harga="<?php echo $obat['harga']; ?>"
                                                                               id="obat<?php echo $obat['id']; ?>">
                                                                        <label for="obat<?php echo $obat['id']; ?>">
                                                                            <?php echo $obat['nama_obat']; ?> - 
                                                                            Rp <?php echo number_format($obat['harga'], 0, ',', '.'); ?>
                                                                        </label>
                                                                    </div>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>

                                                        <!-- Ringkasan Biaya -->
                                                        <div class="card mt-3">
                                                            <div class="card-header">
                                                                <h5 class="card-title">Ringkasan Biaya</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-8">
                                                                        <p>Biaya Jasa Dokter:</p>
                                                                        <p>Total Biaya Obat:</p>
                                                                        <h5 class="mt-2">Total Keseluruhan:</h5>
                                                                    </div>
                                                                    <div class="col-md-4 text-right">
                                                                        <p>Rp 150.000</p>
                                                                        <p id="totalBiayaObat">Rp 0</p>
                                                                        <h5 class="mt-2" id="totalKeseluruhan">Rp 150.000</h5>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                        <button type="submit" name="periksa" class="btn btn-primary">Simpan & Selesai</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Script untuk modal dan Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    // Inisialisasi Select2 dengan opsi animasi dimatikan
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        minimumResultsForSearch: 10,
        dropdownCssClass: 'select2-dropdown-nostyle',
        containerCssClass: 'select2-container-nostyle',
        // Matikan animasi Select2
        animation: false
    });

    // Matikan hover effect pada tombol
    $('.btn').off('mouseenter mouseleave');
    
    // Matikan animasi modal
    $('.modal').on('show.bs.modal', function() {
        $(this).show();
        $('.modal-backdrop').addClass('show');
        return false;
    });

    // Fungsi untuk update data tanpa animasi
    function checkNewData() {
        $.ajax({
            url: 'periksa-pasien.php',
            method: 'GET',
            data: {
                check_new_data: true,
                last_count: lastCount
            },
            success: function(response) {
                if(response.trim() === "refresh") {
                    window.location.reload();
                }
            }
        });
    }

    // Set interval untuk pengecekan data baru (30 detik)
    setInterval(checkNewData, 30000);
});

// Fungsi hitung total tanpa animasi
function hitungTotal(id) {
    const biayaDokter = 150000;
    let totalObat = 0;
    
    // Hitung total obat
    $('#obat' + id + ' option:selected').each(function() {
        totalObat += parseInt($(this).data('harga'));
    });
    
    // Update tampilan
    const totalKeseluruhan = biayaDokter + totalObat;
    document.getElementById('totalObat' + id).innerHTML = formatRupiah(totalObat);
    document.getElementById('totalKeseluruhan' + id).innerHTML = formatRupiah(totalKeseluruhan);
}
</script>

<!-- Notifikasi Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1500;">
    <div id="notificationToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-bell me-2"></i>
                <span id="notificationMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'primary') {
    const toast = document.getElementById('notificationToast');
    const messageElement = document.getElementById('notificationMessage');
    
    // Set pesan
    messageElement.textContent = message;
    
    // Set warna berdasarkan tipe
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    
    // Tampilkan toast
    const bsToast = new bootstrap.Toast(toast, {
        animation: true,
        autohide: true,
        delay: 3000
    });
    bsToast.show();
    
    // Mainkan suara notifikasi
    playNotificationSound();
}

// Fungsi untuk memainkan suara notifikasi
function playNotificationSound() {
    const audio = new Audio('../../assets/notification.mp3');
    audio.play();
}

// Fungsi untuk mengecek data baru
function checkNewData() {
    $.ajax({
        url: window.location.href,
        method: 'GET',
        data: { check_updates: true },
        success: function(response) {
            const data = JSON.parse(response);
            if(data.new_patients > 0) {
                showNotification(`${data.new_patients} pasien baru telah mendaftar!`, 'primary');
                updateTable(data.patients);
            }
            if(data.status_changes > 0) {
                showNotification('Status pasien telah diperbarui', 'success');
                updateTable(data.patients);
            }
        }
    });
}

// CSS untuk animasi dan styling
const style = document.createElement('style');
style.textContent = `
    .toast {
        opacity: 0;
        transition: all 0.3s ease-in-out;
    }
    .toast.show {
        opacity: 1;
    }
    .toast-container {
        margin-top: 20px;
    }
    .toast-body {
        display: flex;
        align-items: center;
        font-size: 14px;
    }
    .toast-body i {
        margin-right: 8px;
        font-size: 16px;
    }
    @keyframes slideIn {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(0);
        }
    }
    .toast.show {
        animation: slideIn 0.3s ease-in-out;
    }
    .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    /* Animasi untuk baris baru */
    .table-row-new {
        animation: highlightNew 2s ease-in-out;
    }
    @keyframes highlightNew {
        0% { background-color: rgba(0, 123, 255, 0.1); }
        100% { background-color: transparent; }
    }
    
    /* Animasi untuk perubahan status */
    .table-row-updated {
        animation: highlightUpdate 2s ease-in-out;
    }
    @keyframes highlightUpdate {
        0% { background-color: rgba(40, 167, 69, 0.1); }
        100% { background-color: transparent; }
    }
`;
document.head.appendChild(style);

// Inisialisasi notifikasi
$(document).ready(function() {
    // Cek data baru setiap 10 detik
    setInterval(checkNewData, 10000);
    
    // Notifikasi awal
    showNotification('Sistem monitoring pasien aktif', 'info');
});

// Fungsi untuk update tabel dengan animasi
function updateTable(data) {
    const tbody = $('#tabelPasien tbody');
    data.forEach((patient, index) => {
        const existingRow = tbody.find(`tr[data-id="${patient.id}"]`);
        const rowHtml = createRowHtml(patient, index + 1);
        
        if (existingRow.length) {
            if (existingRow.data('status') !== patient.status) {
                existingRow.replaceWith(rowHtml);
            }
        } else {
            tbody.append(rowHtml);
        }
    });
}

// Fungsi untuk membuat HTML baris tabel
function createRowHtml(patient, index) {
    return `
        <tr data-id="${patient.id}" data-status="${patient.status}">
            <td>${index}</td>
            <td>${patient.no_rm}</td>
            <td>${patient.nama_pasien}</td>
            <td>${patient.no_antrian}</td>
            <td>
                <span class="badge badge-${getStatusClass(patient.status)}">
                    <i class="fas fa-${getStatusIcon(patient.status)}"></i>
                    ${capitalizeFirst(patient.status)}
                </span>
            </td>
            <td>${patient.actions}</td>
        </tr>
    `;
}
</script>

<script>
$(document).ready(function() {
    // Fungsi untuk memperbarui data
    function updateData() {
        $.ajax({
            url: 'periksa-pasien.php?check_new_data=true',
            success: function(response) {
                if(response === 'refresh') {
                    location.reload();
                }
            }
        });
    }

    // Set interval untuk auto reload setiap 30 detik
    setInterval(updateData, 30000);
});
</script>

<script>
$(document).ready(function() {
    // Event handler untuk checkbox obat
    $('.obat-checkbox').on('change', function() {
        updateTotal();
    });
});

function updateTotal() {
    const biayaDokter = 150000;
    let totalObat = 0;
    
    // Hitung total dari obat yang dicentang
    $('.obat-checkbox:checked').each(function() {
        totalObat += parseInt($(this).data('harga'));
    });

    const totalKeseluruhan = biayaDokter + totalObat;
    
    // Update tampilan
    $('#totalBiayaObat').text('Rp ' + totalObat.toLocaleString('id-ID'));
    $('#totalKeseluruhan').text('Rp ' + totalKeseluruhan.toLocaleString('id-ID'));
}
</script>

<?php include_once("layouts/footer.php"); ?> 
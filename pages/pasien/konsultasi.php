<?php
include_once("layouts/header.php");
include_once("layouts/sidebar.php");
include_once("../../config/koneksi.php");

$id_pasien = $_SESSION['user_id'];

// Ambil daftar dokter
$query_dokter = "SELECT d.*, p.nama_poli 
                FROM dokter d 
                JOIN poli p ON d.id_poli = p.id 
                ORDER BY p.nama_poli, d.nama";
$result_dokter = mysqli_query($koneksi, $query_dokter);

// Ambil riwayat konsultasi
$query_konsultasi = "SELECT DISTINCT d.id as id_dokter, d.nama as nama_dokter, 
                     p.nama_poli, MAX(k.waktu) as last_chat
                     FROM konsultasi k
                     JOIN dokter d ON k.id_dokter = d.id
                     JOIN poli p ON d.id_poli = p.id
                     WHERE k.id_pasien = '$id_pasien'
                     GROUP BY d.id
                     ORDER BY last_chat DESC";
$result_konsultasi = mysqli_query($koneksi, $query_konsultasi);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Konsultasi Online</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Daftar Riwayat Konsultasi -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Riwayat Konsultasi</h3>
                            <button type="button" class="btn btn-primary btn-sm float-right" 
                                    data-toggle="modal" data-target="#modalKonsultasiBaru">
                                <i class="fas fa-plus"></i> Konsultasi Baru
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group">
                                <?php while($row = mysqli_fetch_assoc($result_konsultasi)) { ?>
                                    <a href="#" class="list-group-item list-group-item-action chat-item" 
                                       data-dokter-id="<?php echo $row['id_dokter']; ?>">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo $row['nama_dokter']; ?></h6>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($row['last_chat'])); ?>
                                            </small>
                                        </div>
                                        <small class="text-muted"><?php echo $row['nama_poli']; ?></small>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Area Chat -->
                <div class="col-md-8">
                    <div class="card direct-chat direct-chat-primary">
                        <div class="card-header">
                            <h3 class="card-title">Chat dengan Dokter</h3>
                        </div>
                        <div class="card-body">
                            <div class="direct-chat-messages" id="chatArea">
                                <!-- Pesan chat akan dimuat di sini -->
                                <div class="text-center text-muted mt-5">
                                    Pilih dokter untuk memulai chat
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <form id="formChat" style="display: none;">
                                <div class="input-group">
                                    <input type="text" id="pesanChat" class="form-control" 
                                           placeholder="Ketik pesan...">
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Kirim</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Konsultasi Baru -->
<div class="modal fade" id="modalKonsultasiBaru" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konsultasi Baru</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formKonsultasiBaru">
                    <div class="form-group">
                        <label>Pilih Dokter</label>
                        <select class="form-control select2" id="dokterBaru" required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php 
                            mysqli_data_seek($result_dokter, 0);
                            while($dokter = mysqli_fetch_assoc($result_dokter)) { 
                            ?>
                                <option value="<?php echo $dokter['id']; ?>">
                                    <?php echo $dokter['nama'] . ' - ' . $dokter['nama_poli']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pesan Awal</label>
                        <textarea class="form-control" id="pesanAwal" rows="3" required
                                  placeholder="Tuliskan keluhan atau pertanyaan Anda..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnMulaiKonsultasi">
                    Mulai Konsultasi
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.direct-chat-messages {
    height: 400px;
}

.direct-chat-msg {
    margin-bottom: 1rem;
}

.direct-chat-text {
    border-radius: 0.5rem;
    position: relative;
    padding: 0.5rem 0.75rem;
    margin: 5px 0 0 50px;
    display: inline-block;
}

.right .direct-chat-text {
    margin-right: 50px;
    margin-left: 0;
}

.direct-chat-timestamp {
    font-size: 0.75rem;
    color: #6c757d;
}

.chat-item {
    transition: all 0.3s ease;
}

.chat-item:hover {
    background-color: #f8f9fa;
}

.chat-item.active {
    background-color: #007bff;
    color: white;
}

.chat-item.active small {
    color: rgba(255,255,255,0.8) !important;
}
</style>

<script>
let activeDokter = null;

$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Handler untuk memilih chat
    $('.chat-item').click(function(e) {
        e.preventDefault();
        const dokterId = $(this).data('dokter-id');
        loadChat(dokterId);
        
        $('.chat-item').removeClass('active');
        $(this).addClass('active');
        
        $('#formChat').show();
    });

    // Handler untuk mengirim pesan
    $('#formChat').submit(function(e) {
        e.preventDefault();
        if(!activeDokter) return;
        
        const pesan = $('#pesanChat').val().trim();
        if(!pesan) return;
        
        sendMessage(activeDokter, pesan);
        $('#pesanChat').val('');
    });

    // Handler untuk konsultasi baru
    $('#btnMulaiKonsultasi').click(function() {
        const dokterId = $('#dokterBaru').val();
        const pesanAwal = $('#pesanAwal').val().trim();
        
        if(!dokterId || !pesanAwal) {
            Swal.fire('Error', 'Mohon lengkapi semua field', 'error');
            return;
        }
        
        $.ajax({
            url: 'konsultasi_handler.php',
            type: 'POST',
            data: {
                action: 'mulai_konsultasi',
                id_dokter: dokterId,
                pesan: pesanAwal
            },
            success: function(response) {
                if(response.success) {
                    $('#modalKonsultasiBaru').modal('hide');
                    loadChat(dokterId);
                    location.reload(); // Refresh untuk memperbarui daftar chat
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });
});

function loadChat(dokterId) {
    activeDokter = dokterId;
    
    $.ajax({
        url: 'konsultasi_handler.php',
        type: 'POST',
        data: {
            action: 'get_chat',
            id_dokter: dokterId
        },
        success: function(response) {
            if(response.success) {
                displayChat(response.messages);
            }
        }
    });
}

function displayChat(messages) {
    const chatArea = $('#chatArea');
    chatArea.empty();
    
    messages.forEach(msg => {
        const isPasien = msg.pengirim === 'pasien';
        const html = `
            <div class="direct-chat-msg ${isPasien ? 'right' : ''}">
                <div class="direct-chat-text ${isPasien ? 'bg-primary' : 'bg-light'}">
                    ${msg.pesan}
                    <br>
                    <span class="direct-chat-timestamp">
                        ${new Date(msg.waktu).toLocaleString()}
                    </span>
                </div>
            </div>
        `;
        chatArea.append(html);
    });
    
    chatArea.scrollTop(chatArea[0].scrollHeight);
}

function sendMessage(dokterId, pesan) {
    $.ajax({
        url: 'konsultasi_handler.php',
        type: 'POST',
        data: {
            action: 'send_message',
            id_dokter: dokterId,
            pesan: pesan
        },
        success: function(response) {
            if(response.success) {
                loadChat(dokterId);
            }
        }
    });
}

// Auto refresh chat setiap 5 detik
setInterval(() => {
    if(activeDokter) {
        loadChat(activeDokter);
    }
}, 5000);
</script>

<?php include_once("layouts/footer.php"); ?> 
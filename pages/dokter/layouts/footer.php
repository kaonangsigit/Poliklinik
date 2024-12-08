<div class="wrapper">
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="../../assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="../../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../../assets/dist/js/adminlte.min.js"></script>
  <!-- DataTables -->
  <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

  <script>
      // Fungsi ini dijalankan ketika dokumen HTML sudah siap
      $(function () {
          // Inisialisasi DataTable pada elemen dengan id "example1"
          // DataTable adalah plugin jQuery untuk membuat tabel HTML menjadi lebih interaktif
          $("#example1").DataTable({
              "responsive": true,      // Membuat tabel responsif/menyesuaikan ukuran layar
              "lengthChange": false,   // Menonaktifkan opsi untuk mengubah jumlah data per halaman
              "autoWidth": false       // Menonaktifkan pengaturan lebar kolom otomatis
          });
      });
  </script>
  </script>
  </body>
  </html>
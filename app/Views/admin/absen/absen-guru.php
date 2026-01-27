<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="card">
         <div class="card-body">
            <div class="row px-3 pt-3 pb-2">
               <div class="col-md-3">
                  <h4><b>Tanggal</b></h4>
                  <input class="form-control" type="date" name="tangal" id="tanggal" value="<?= date('Y-m-d'); ?>" onchange="getGuru()">
               </div>
               <div class="col-md-4">
                  <h4><b>Filter nama kelas</b></h4>
                  <select class="form-control" id="filterKelas" onchange="getGuru()">
                     <option value="all">Semua kelas</option>
                     <?php foreach (($kelas ?? []) as $value) : ?>
                        <option value="<?= $value['id_kelas']; ?>">
                           <?= $value['kelas']; ?> <?= $value['jurusan']; ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="col-md-4">
                  <h4><b>Filter kehadiran</b></h4>
                  <select class="form-control" id="filterKehadiran" onchange="getGuru()">
                     <option value="all">Semua kehadiran</option>
                     <?php foreach (($listKehadiran ?? []) as $kehadiran) : ?>
                        <option value="<?= $kehadiran['id_kehadiran']; ?>">
                           <?= $kehadiran['kehadiran']; ?>
                        </option>
                     <?php endforeach; ?>
                     <option value="5">Belum tersedia</option>
                  </select>
               </div>
            </div>
         </div>
      </div>
      <div class="card primary">
         <div class="card-body">
            <div class="row justify-content-between">
               <div class="col">
                  <div class="pt-3 pl-3">
                     <h4><b>Absen </b></h4>
                     <p>Data Non Staff MK muncul disini</p>
                  </div>
               </div>
               <div class="col-sm-auto">
                  <a href="#" class="btn btn-success pl-3 mr-3 mt-3" onclick="kelas = getGuru()" data-toggle="tab">
                     <i class="material-icons mr-2">refresh</i> Refresh
                  </a>
               </div>
            </div>

            <div id="dataGuru">

            </div>
         </div>
      </div>
   </div>

   <!-- Modal -->
   <div class="modal fade" id="ubahModal" tabindex="-1" aria-labelledby="modalUbahKehadiran" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modalUbahKehadiran">Ubah kehadiran</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div id="modalFormUbahGuru"></div>
         </div>
      </div>
   </div>
</div>
<script>
   getGuru();

   function getGuru() {
      var tanggal = $('#tanggal').val();
      var idKelas = $('#filterKelas').val();
      var idKehadiran = $('#filterKehadiran').val();

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-guru'); ?>",
         type: 'post',
         data: {
            'tanggal': tanggal,
            'id_kelas': idKelas,
            'id_kehadiran': idKehadiran
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataGuru').html(response);

            $('html, body').animate({
               scrollTop: $("#dataGuru").offset().top
            }, 500);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataGuru').html(thrown);
         }
      });
   }

   function ubahKehadiran() {
      var tanggal = $('#tanggal').val();

      var form = $('#formUbah').serializeArray();

      form.push({
         name: 'tanggal',
         value: tanggal
      });

      jQuery.ajax({
         url: "<?= base_url('/admin/absen-guru/edit'); ?>",
         type: 'post',
         data: form,
         success: function(response, status, xhr) {
            // console.log(status);

            if (response['status']) {
               alert('Berhasil ubah kehadiran : ' + response['nama_guru']);
            } else {
               alert('Gagal ubah kehadiran : ' + response['nama_guru']);
            }

            getGuru();
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            alert('Gagal ubah kehadiran\n' + thrown);
         }
      });
   }

   function getDataKehadiran(idPresensi, idGuru) {
      jQuery.ajax({
         url: "<?= base_url('/admin/absen-guru/kehadiran'); ?>",
         type: 'post',
         data: {
            'id_presensi': idPresensi,
            'id_guru': idGuru
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#modalFormUbahGuru').html(response);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#modalFormUbahGuru').html(thrown);
         }
      });
   }
</script>
<?= $this->endSection() ?>

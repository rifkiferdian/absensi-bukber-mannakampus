<?= $this->extend('templates/admin_page_layout') ?>
<?= $this->section('content') ?>
<div class="content">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <?php if (session()->getFlashdata('msg')) : ?>
               <div class="pb-2 px-3">
                  <div class="alert alert-<?= session()->getFlashdata('error') == true ? 'danger' : 'success'  ?> ">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="material-icons">close</i>
                     </button>
                     <?= session()->getFlashdata('msg') ?>
                  </div>
               </div>
            <?php endif; ?>
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/siswa/create'); ?>">
               <i class="material-icons mr-2">add</i> Tambah data Tamu
            </a>
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/siswa/bulk'); ?>">
               <i class="material-icons mr-2">add</i> Import CSV/Excel
            </a>
            <button class="btn btn-danger ml-3 pl-3 py-3 btn-table-delete" onclick="deleteSelectedSiswa('Data yang sudah dihapus tidak bisa kembalikan');"><i class="material-icons mr-2">delete_forever</i>Bulk Delete</button>
            <div class="card">
               <div class="card-header card-header-tabs card-header-primary">
                  <div class="nav-tabs-navigation">
                     <div class="row">
                        <div class="col-md-2">
                           <h4 class="card-title"><b>Daftar Tamu</b></h4>
                           <p class="card-category">Angkatan <?= $generalSettings->school_year; ?></p>
                        </div>
                        <div class="col-md-4">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Agenda:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="kelas = null; trig()" href="#" data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <?php
                                 $tempKelas = [];
                                 foreach ($kelas as $value) : ?>
                                    <?php if (!in_array($value['kelas'], $tempKelas)) : ?>
                                       <li class="nav-item">
                                          <a class="nav-link" onclick="kelas = '<?= $value['kelas']; ?>'; trig()" href="#" data-toggle="tab">
                                             <i class="material-icons">school</i> <?= $value['kelas']; ?>
                                             <div class="ripple-container"></div>
                                          </a>
                                       </li>
                                       <?php array_push($tempKelas, $value['kelas']) ?>
                                    <?php endif; ?>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="nav-tabs-wrapper">
                              <span class="nav-tabs-title">Waktu:</span>
                              <ul class="nav nav-tabs" data-tabs="tabs">
                                 <li class="nav-item">
                                    <a class="nav-link active" onclick="Waktu = null; trig()" href="#" data-toggle="tab">
                                       <i class="material-icons">check</i> Semua
                                       <div class="ripple-container"></div>
                                    </a>
                                 </li>
                                 <?php foreach ($jurusan as $value) : ?>
                                    <li class="nav-item">
                                       <a class="nav-link" onclick="jurusan = '<?= $value['jurusan']; ?>'; trig();" href="#" data-toggle="tab">
                                          <i class="material-icons">work</i> <?= $value['jurusan']; ?>
                                          <div class="ripple-container"></div>
                                       </a>
                                    </li>
                                 <?php endforeach; ?>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="dataSiswa">
                  <p class="text-center mt-3">Daftar siswa muncul disini</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   var kelas = null;
   var jurusan = null;

   getDataSiswa(kelas, jurusan, 1);

   function trig() {
      getDataSiswa(kelas, jurusan, 1);
   }

   function getDataSiswa(_kelas = null, _jurusan = null, _page = 1) {
      jQuery.ajax({
         url: "<?= base_url('/admin/siswa'); ?>",
         type: 'post',
         data: {
            'kelas': _kelas,
            'jurusan': _jurusan,
            'page_siswa': _page
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataSiswa').html(response);

            $('html, body').animate({
               scrollTop: $("#dataSiswa").offset().top
            }, 500);
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataSiswa').html(thrown);
         }
      });
   }

   document.addEventListener('DOMContentLoaded', function() {
      $("#checkAll").click(function(e) {
         console.log(e);
         $('input:checkbox').not(this).prop('checked', this.checked);
      });

      $('#dataSiswa').on('click', '.pagination a', function(e) {
         e.preventDefault();
         var href = $(this).attr('href');
         if (!href) {
            return;
         }
         var page = 1;
         try {
            var url = new URL(href, window.location.origin);
            page = url.searchParams.get('page_siswa') || 1;
         } catch (err) {
            var match = href.match(/[?&]page_siswa=(\d+)/);
            page = match ? match[1] : 1;
         }
         getDataSiswa(kelas, jurusan, page);
      });
   });
</script>
<?= $this->endSection() ?>

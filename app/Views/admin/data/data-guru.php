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
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/guru/create'); ?>">
               <i class="material-icons mr-2">add</i> Data Non Staff MK
            </a>
            <a class="btn btn-primary ml-3 pl-3 py-3" href="<?= base_url('admin/guru/bulk'); ?>">
               <i class="material-icons mr-2">add</i> Import CSV/Excel
            </a>
            <div class="row">
               <div class="col-12 col-xl-12">
                  <div class="card">
                     <div class="card-header card-header-tabs card-header-success">
                        <div class="nav-tabs-navigation">
                           <div class="row">
                              <div class="col-md-3 col-lg-3">
                                 <h4 class="card-title"><b>Data Non Staff MK</b></h4>
                                 <p class="card-category">Angkatan <?= $generalSettings->school_year; ?></p>
                              </div>
                              <div class="col-md-6">
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
                              <div class="ml-md-auto col-auto row">
                                 <div class="col-12 col-sm-auto nav nav-tabs">
                                    <div class="nav-item">
                                       <a class="nav-link" id="tabBtn" onclick="removeHover()" href="<?= base_url('admin/guru/create'); ?>">
                                          <i class="material-icons">add</i> Data Non Staff MK
                                          <div class="ripple-container"></div>
                                       </a>
                                    </div>
                                 </div>
                                 <div class="col-12 col-sm-auto nav nav-tabs">
                                    <div class="nav-item">
                                       <a class="nav-link" id="refreshBtn" onclick="getDataGuru(kelas)" href="#" data-toggle="tab">
                                          <i class="material-icons">refresh</i> Refresh
                                          <div class="ripple-container"></div>
                                       </a>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div id="dataGuru">
                        <p class="text-center mt-3">Daftar guru muncul disini</p>
                     </div>
                  </div>
               </div>
            </div>

         </div>
      </div>
   </div>
</div>
<script>
   var kelas = null;

   getDataGuru(kelas);

   function trig() {
      getDataGuru(kelas);
   }

   function getDataGuru(_kelas = null) {
      jQuery.ajax({
         url: "<?= base_url('/admin/guru'); ?>",
         type: 'post',
         data: {
            'kelas': _kelas
         },
         success: function(response, status, xhr) {
            // console.log(status);
            $('#dataGuru').html(response);

            $('html, body').animate({
               scrollTop: $("#dataGuru").offset().top
            }, 500);
            $('#refreshBtn').removeClass('active show');
         },
         error: function(xhr, status, thrown) {
            console.log(thrown);
            $('#dataGuru').html(thrown);
            $('#refreshBtn').removeClass('active show');
         }
      });
   }

   function removeHover() {
      setTimeout(() => {
         $('#tabBtn').removeClass('active show');
      }, 250);
   }
</script>
<?= $this->endSection() ?>

 <?= $this->extend('templates/starting_page_layout'); ?>

 <?= $this->section('navaction') ?>
 <a href="<?= base_url('/'); ?>" class="btn btn-primary pull-right pl-3">
    <i class="material-icons mr-2">qr_code</i>
    Scan QR
 </a>
 <?= $this->endSection() ?>

 <?= $this->section('content'); ?>

 <style>
   /* CSS untuk logo responsif */
   .responsive-logo {
      max-width: 100%;
      height: auto;
      display: block;
      margin: 0 auto;
      transition: all 0.3s ease; /* Animasi halus saat resize */
   }

   /* Desktop/Laptop besar (≥1200px) */
   @media (min-width: 1200px) {
      .responsive-logo {
         max-width: 280px; /* Ukuran kecil untuk laptop besar */
      }
   }

   /* Laptop standar (992px - 1199px) */
   @media (min-width: 992px) and (max-width: 1199px) {
      .responsive-logo {
         max-width: 270px; /* Lebih kecil */
      }
   }

   /* Tablet landscape (768px - 991px) */
   @media (min-width: 768px) and (max-width: 991px) {
      .responsive-logo {
         max-width: 250px;
      }
   }

   /* Tablet portrait (576px - 767px) */
   @media (min-width: 576px) and (max-width: 767px) {
      .responsive-logo {
         max-width: 220px;
      }
   }

   /* Handphone (≤575px) */
   @media (max-width: 575px) {
      .responsive-logo {
         max-width: 250px;
      }
   }

   /* Handphone sangat kecil (≤375px) */
   @media (max-width: 375px) {
      .responsive-logo {
         max-width: 200px;
      }
   }
 </style>

 <div class="main-panel">
    <div class="content">
       <center><img class="responsive-logo" src="<?php echo base_url('uploads/logo/logo_6951e9280661f8-73637506.png'); ?>"></center><br>
       <div class="container-fluid">
          <div class="row">
             <div class="col-md-4 m-auto">
                <div class="card">
                   <div class="card-header card-header-warning mb-48">
                      <h4 class="card-title">Login petugas</h4>
                      <p class="card-category">Silahkan masukkan username dan password anda</p>
                   </div>

                   <div class="card-body mx-5 my-3">
                      <?= view('\App\Views\admin\_message_block') ?>
                      <form action="<?= url_to('login') ?>" method="post">
                         <?= csrf_field() ?>
                         <div class="row">
                            <div class="col-md-12">
                               <?php if ($config->validFields === ['email']) : ?>
                                  <div class="form-group">
                                     <label class="bmd-label-floating"><?= lang('Auth.email') ?></label>
                                     <input type="email" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>" name="login" autofocus>
                                     <div class="invalid-feedback">
                                        <?= session('errors.login') ?>
                                     </div>
                                  </div>
                               <?php else : ?>
                                  <div class="form-group">
                                     <label class="bmd-label-floating"><?= lang('Auth.emailOrUsername') ?></label>
                                     <input type="text" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>" name="login" autofocus>
                                     <div class="invalid-feedback">
                                        <?= session('errors.login') ?>
                                     </div>
                                  </div>
                               <?php endif; ?>
                            </div>
                         </div>
                         <div class="row mt-3">
                            <div class="col-md-12">
                               <div class="form-group">
                                  <label class="bmd-label-floating">Password</label>
                                  <input type="password" name="password" class="form-control  <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>">
                                  <div class="invalid-feedback">
                                     <?= session('errors.password') ?>
                                  </div>
                               </div>
                            </div>
                         </div>
                         <!-- <button type="submit" class="btn btn-primary col-md-12">Login</button> -->
                         <?php if ($config->allowRemembering) : ?>
                            <div class="form-check">
                               <label class="form-check-label">
                                  <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')) : ?> checked <?php endif ?>>
                                  <?= lang('Auth.rememberMe') ?>
                               </label>
                            </div>
                         <?php endif; ?>

                         <br>

                         <button type="submit" class="btn btn-warning btn-block"><?= lang('Auth.loginAction') ?></button>

                         <?php if ($config->activeResetter) : ?>
                            <p><a href="<?= url_to('forgot') ?>"><?= lang('Auth.forgotYourPassword') ?></a></p>
                         <?php endif; ?>
                         <div class="clearfix"></div>
                      </form>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
 <?= $this->endSection(); ?>
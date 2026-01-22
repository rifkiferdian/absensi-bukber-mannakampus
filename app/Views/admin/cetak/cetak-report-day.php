<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Laporan Kehadiran Harian</title>

  <link rel="stylesheet" href="<?= base_url('assets/paper/normalize.min.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/paper/paper.css'); ?>">

  <style>
     @page {
        size: A4;
        margin: 10mm;
     }

     body {
        font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        color: #111827;
        background: #f7f7f7;
     }

     .sheet {
        background: #fff;
     }

     .kop {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 2px solid #111827;
     }

     .kop img {
        width: 70px;
        height: 70px;
        object-fit: contain;
     }

     .kop h2 {
        margin: 0;
        font-size: 20px;
        letter-spacing: 0.3px;
     }

     .kop .subtitle {
        margin: 0;
        font-size: 13px;
        color: #374151;
        letter-spacing: 0.5px;
     }

     .meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 8px;
        font-size: 12px;
     }

     .meta-label {
        color: #6b7280;
        display: block;
        margin-bottom: 2px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
     }

     .meta-value {
        color: #111827;
        font-weight: 600;
        font-size: 14px;
     }

     table.report {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
        font-size: 10px;
     }

     table.report th,
     table.report td {
        border: 1px solid #d1d5db;
        padding: 5px 8px;
        vertical-align: middle;
     }

     table.report th {
        background: #f3f4f6;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        text-align: left;
     }

     .status-hadir {
        color: #111827;
     }

     .status-sakit {
        color: #854d0e;
     }

     .status-izin {
        color: #38bdf8;
     }

     .status-tanpa {
        color: #b91c1c;
     }

     .status-belum {
        color: #4b5563;
     }

     .status-text {
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
     }

     .text-center {
        text-align: center;
     }

     .text-right {
        text-align: right;
     }

     .empty-state {
        margin-top: 12px;
        padding: 14px;
        border: 1px dashed #d1d5db;
        color: #6b7280;
        text-align: center;
        background: #f9fafb;
        border-radius: 6px;
     }
  </style>
</head>

<body class="A4">
  <?php if (!empty($data)) : ?>
     <?php $chunks = array_chunk($data, 40); ?>
     <?php $no = 1; ?>
     <?php foreach ($chunks as $chunk) : ?>
        <section class="sheet padding-10mm">
           <div class="kop">
              <div class="logo-wrap">
                 <img src="<?= getLogo(); ?>" style="width: 145px;" alt="Logo sekolah">
              </div>
              <div>
                 <h2><?= esc($generalSettings->school_name); ?></h2>
                 <p class="subtitle">Laporan Kehadiran <?= esc($kelasInfo['jurusan'] ?? '-'); ?></p>
                 <p class="subtitle"><?= esc($kelasInfo['kelas'] ?? '-'); ?></p>
              </div>
           </div>

           <div class="meta-row">
              <!-- <div class="meta-block">
                 <span class="meta-label">Tanggal</span>
                 <span class="meta-value"><?= date('d M Y', strtotime($tanggal)); ?></span>
              </div> -->
              <!-- <div class="meta-block">
                 <span class="meta-label">Kelas</span>
                 <span class="meta-value"><?= esc($kelasInfo['kelas'] ?? ($data[0]['id_kelas'] ?? $idKelas)); ?></span>
              </div>
              <div class="meta-block">
                 <span class="meta-label">Jurusan</span>
                 <span class="meta-value"><?= esc($kelasInfo['jurusan'] ?? '-'); ?></span>
              </div> -->
           </div>

           <table class="report">
              <thead>
                 <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th>Status Hadir</th>
                    <th>Waktu Absen</th>
                    <th>Keterangan Absen</th>
                 </tr>
              </thead>
              <tbody>
                 <?php foreach ($chunk as $row) : ?>
                    <?php $status = statusKehadiran($row['id_kehadiran'] ?? null); ?>
                    <tr>
                       <td class="text-center"><?= $no++; ?></td>
                       <td><?= esc($row['nama_siswa'] ?? '-'); ?></td>
                       <td><?= !empty($row['keterangan_siswa']) ? esc($row['keterangan_siswa']) : '-'; ?></td>
                    <td class="status-text <?= $status['class']; ?>">
                       <?= $status['label']; ?>
                    </td>
                       <td><?= formatWaktu($row['jam_masuk'] ?? null, $row['jam_keluar'] ?? null); ?></td>
                       <td><?= !empty($row['keterangan']) ? esc($row['keterangan']) : '-'; ?></td>
                    </tr>
                 <?php endforeach; ?>
              </tbody>
           </table>
        </section>
     <?php endforeach; ?>
  <?php else : ?>
     <section class="sheet padding-10mm">
        <div class="kop">
           <div class="logo-wrap">
              <img src="<?= getLogo(); ?>" style="width: 145px;" alt="Logo sekolah">
           </div>
           <div>
              <h2><?= esc($generalSettings->school_name); ?></h2>
              <p class="subtitle">Laporan Kehadiran <?= esc($kelasInfo['kelas'] ?? '-'); ?></p>
              <p class="subtitle"><?= esc($kelasInfo['jurusan'] ?? '-'); ?></p>
           </div>
        </div>

        <div class="meta-row">
           <!-- <div class="meta-block">
              <span class="meta-label">Tanggal</span>
              <span class="meta-value"><?= date('d M Y', strtotime($tanggal)); ?></span>
           </div> -->
        </div>

        <div class="empty-state">
           Tidak ada data kehadiran pada tanggal ini.
        </div>
     </section>
  <?php endif; ?>

  <?php
  function statusKehadiran($kehadiran): array
  {
     switch ($kehadiran) {
        case 1:
           return ['label' => 'Hadir', 'class' => 'status-hadir'];
        case 2:
           return ['label' => 'Sakit', 'class' => 'status-sakit'];
        case 3:
           return ['label' => 'Izin', 'class' => 'status-izin'];
        case 4:
           return ['label' => 'Tanpa Keterangan', 'class' => 'status-tanpa'];
        default:
           return ['label' => '-', 'class' => 'status-belum'];
     }
  }

  function formatWaktu($masuk, $keluar): string
  {
     if (!empty($masuk) && !empty($keluar)) {
        return $masuk . ' - ' . $keluar;
     }

     if (!empty($masuk)) {
        return $masuk;
     }

     if (!empty($keluar)) {
        return $keluar;
     }

     return '-';
  }
  ?>
</body>

</html>

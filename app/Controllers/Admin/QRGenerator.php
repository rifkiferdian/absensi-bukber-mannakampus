<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Font\Font;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WriterInterface;

class QRGenerator extends BaseController
{
   protected QrCode $qrCode;
   protected WriterInterface $writer;
   protected ?Logo $logo = null;
   protected Label $label;
   protected Font $labelFont;
   protected Color $foregroundColor;
   protected Color $foregroundColor2;
   protected Color $backgroundColor;

   protected string $qrCodeFilePath;

   const UPLOADS_PATH = FCPATH . 'uploads' . DIRECTORY_SEPARATOR;

   public function __construct()
   {
      $this->setQrCodeFilePath(self::UPLOADS_PATH);

      $this->writer = new PngWriter();

      $this->labelFont = new Font(FCPATH . 'assets/fonts/Roboto-Medium.ttf', 14);

      $this->foregroundColor = new Color(44, 73, 162);
      $this->foregroundColor2 = new Color(28, 101, 90);
      $this->backgroundColor = new Color(255, 255, 255);

      if (boolval(env('QR_LOGO'))) {
         // Create logo
         $logo = (new \Config\School)::$generalSettings->logo;
         if (empty($logo) || !file_exists(FCPATH . $logo)) {
            $logo = 'assets/img/logo_sekolah.jpg';
         }
         if (file_exists(FCPATH . $logo)) {
            $fileExtension = pathinfo(FCPATH . $logo, PATHINFO_EXTENSION);
            if ($fileExtension === 'svg') {
               $this->writer = new SvgWriter();
               $this->logo = Logo::create(FCPATH . $logo)
                  ->setResizeToWidth(75)
                  ->setResizeToHeight(75);
            } else {
               $this->logo = Logo::create(FCPATH . $logo)
                  ->setResizeToWidth(75);
            }
         }
      }

      $this->label = Label::create('')
         ->setFont($this->labelFont)
         ->setTextColor($this->foregroundColor);

      // Create QR code
      $this->qrCode = QrCode::create('')
         ->setEncoding(new Encoding('UTF-8'))
         ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
         ->setSize(300)
         ->setMargin(10)
         ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
         ->setForegroundColor($this->foregroundColor)
         ->setBackgroundColor($this->backgroundColor);
   }

   public function setQrCodeFilePath(string $qrCodeFilePath)
   {
      $this->qrCodeFilePath = $qrCodeFilePath;
      if (!file_exists($this->qrCodeFilePath)) mkdir($this->qrCodeFilePath, recursive: true);
   }

   public function generateQrSiswa()
   {
      $kelas = $this->getKelasJurusanSlug($this->request->getVar('id_kelas'));
      if (!$kelas) {
         return $this->response->setJSON(false);
      }

      $this->qrCodeFilePath .= "qr-siswa/$kelas/";

      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath, recursive: true);
      }

      $this->generate(
         unique_code: $this->request->getVar('unique_code'),
         nama: $this->request->getVar('nama'),
         nomor: $this->request->getVar('nomor')
      );

      return $this->response->setJSON(true);
   }

   public function generateQrGuru()
   {
      $this->qrCode->setForegroundColor($this->foregroundColor2);
      $this->label->setTextColor($this->foregroundColor2);

      $this->qrCodeFilePath .= 'qr-guru/';

      if (!file_exists($this->qrCodeFilePath)) {
         mkdir($this->qrCodeFilePath, recursive: true);
      }

      $this->generate(
         unique_code: $this->request->getVar('unique_code'),
         nama: $this->request->getVar('nama'),
         nomor: $this->request->getVar('nomor')
      );

      return $this->response->setJSON(true);
   }

   public function generate($nama, $nomor, $unique_code)
   {
      $fileExt = $this->writer instanceof SvgWriter ? 'svg' : 'png';
      $filename = url_title($nama, lowercase: true) . "_" . url_title($nomor, lowercase: true) . ".$fileExt";

      // set qr code data
      $this->qrCode->setData($unique_code);

      // $this->label->setText($nama);

      // Save it to a file
      $this->writer
         ->write(
            qrCode: $this->qrCode,
            logo: $this->logo,
            // label: $this->label
         )
         ->saveToFile(
            path: $this->qrCodeFilePath . $filename
         );

      return $this->qrCodeFilePath . $filename;
   }

   public function downloadQrSiswa($idSiswa = null)
   {
      $siswa = (new SiswaModel)->find($idSiswa);
      if (!$siswa) {
         session()->setFlashdata([
            'msg' => 'Siswa tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }
      
      try {
         $kelas = $this->getKelasJurusanSlug($siswa['id_kelas']) ?? 'tmp';
         $this->qrCodeFilePath .= "qr-siswa/$kelas/";

         if (!file_exists($this->qrCodeFilePath)) {
            mkdir($this->qrCodeFilePath, recursive: true);
         }

         return $this->response->download(
            $this->generate(
               nama: $siswa['nama_siswa'],
               nomor: $siswa['nis'],
               unique_code: $siswa['unique_code'],
            ),
            null,
            true,
         );
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadQrSiswaWithTemplate($idSiswa = null)
   {
      $siswa = (new SiswaModel)->find($idSiswa);
      if (!$siswa) {
         session()->setFlashdata([
            'msg' => 'Siswa tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      $templatePath = FCPATH . 'assets/img/template-qr/template-qr.JPG';
      $fontPath = FCPATH . 'assets/fonts/Arial.ttf';
      if (!file_exists($fontPath)) {
         $fontPath = FCPATH . 'assets/fonts/Roboto-Medium.ttf';
      }

      if (!file_exists($templatePath) || !file_exists($fontPath)) {
         session()->setFlashdata([
            'msg' => 'Template QR atau font tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $kelas = $this->getKelasJurusanSlug($siswa['id_kelas']) ?? 'tmp';
         $this->qrCodeFilePath .= "qr-siswa/$kelas/";

         if (!file_exists($this->qrCodeFilePath)) {
            mkdir($this->qrCodeFilePath, recursive: true);
         }

         $qrPath = $this->generate(
            nama: $siswa['nama_siswa'],
            nomor: $siswa['nis'],
            unique_code: $siswa['unique_code'],
         );

         // Bersihkan buffer output jika ada
         while (ob_get_level()) {
            ob_end_clean();
         }

         // Siapkan gambar template dan QR
         $image = imagecreatefromjpeg($templatePath);
         $qr = imagecreatefrompng($qrPath);
         if (!$image || !$qr) {
            throw new \RuntimeException('Gagal memuat template atau QR Code');
         }

         imagealphablending($qr, true);
         imagesavealpha($qr, true);

         $width = imagesx($image);
         $height = imagesy($image);

         // Tulis nama siswa di tengah dengan bayangan
         $text = $siswa['nama_siswa'];
         $fontSize = 27;
         $angle = 0;
         $textColor = imagecolorallocate($image, 0, 0, 0);
         $shadowColor = imagecolorallocate($image, 160, 160, 160);

         $box = imagettfbbox($fontSize, $angle, $fontPath, $text);
         $textWidth = abs($box[4] - $box[0]);
         $textHeight = abs($box[5] - $box[1]);

         $xText = ($width / 2) - ($textWidth / 2);
         $yText = ($height / 2) + ($textHeight / 2) + 270;

         imagettftext($image, $fontSize, $angle, $xText + 2, $yText + 2, $shadowColor, $fontPath, $text);
         imagettftext($image, $fontSize, $angle, $xText, $yText, $textColor, $fontPath, $text);

         // Tempel QR di tengah template
         $qrSize = 400;
         $qrW = imagesx($qr);
         $qrH = imagesy($qr);

         $xQr = ($width - $qrSize) / 2;
         $yQr = ($height - $qrSize) / 2 - 80;

         imagecopyresampled(
            $image,
            $qr,
            $xQr,
            $yQr,
            0,
            0,
            $qrSize,
            $qrSize,
            $qrW,
            $qrH
         );

         ob_start();
         imagejpeg($image, null, 90);
         imagedestroy($image);
         imagedestroy($qr);
         $data = ob_get_clean();

         $downloadName = 'qr-tamu-' . url_title($siswa['nama_siswa'], lowercase: true) . '.jpg';

         return $this->response
            ->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $downloadName . '"')
            ->setHeader('Content-Length', (string) strlen($data))
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($data);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadQrGuru($idGuru = null)
   {
      $guru = (new GuruModel)->find($idGuru);
      if (!$guru) {
         session()->setFlashdata([
            'msg' => 'Data tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }
      try {
         $this->qrCode->setForegroundColor($this->foregroundColor2);
         $this->label->setTextColor($this->foregroundColor2);

         $this->qrCodeFilePath .= 'qr-guru/';

         if (!file_exists($this->qrCodeFilePath)) {
            mkdir($this->qrCodeFilePath, recursive: true);
         }

         return $this->response->download(
            $this->generate(
               nama: $guru['nama_guru'],
               nomor: $guru['nuptk'],
               unique_code: $guru['unique_code'],
            ),
            null,
            true,
         );
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadQrGuruWithTemplate($idGuru = null)
   {
      $guru = (new GuruModel)->find($idGuru);
      if (!$guru) {
         session()->setFlashdata([
            'msg' => 'Data tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      $templatePath = FCPATH . 'assets/img/template-qr/template-qr.JPG';
      $fontPath = FCPATH . 'assets/fonts/Arial.ttf';
      if (!file_exists($fontPath)) {
         $fontPath = FCPATH . 'assets/fonts/Roboto-Medium.ttf';
      }

      if (!file_exists($templatePath) || !file_exists($fontPath)) {
         session()->setFlashdata([
            'msg' => 'Template QR atau font tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         // set warna guru
         $this->qrCode->setForegroundColor($this->foregroundColor2);
         $this->label->setTextColor($this->foregroundColor2);

         $this->qrCodeFilePath .= 'qr-guru/';

         if (!file_exists($this->qrCodeFilePath)) {
            mkdir($this->qrCodeFilePath, recursive: true);
         }

         $qrPath = $this->generate(
            nama: $guru['nama_guru'],
            nomor: $guru['nuptk'],
            unique_code: $guru['unique_code'],
         );

         while (ob_get_level()) {
            ob_end_clean();
         }

         $image = imagecreatefromjpeg($templatePath);
         $qr = imagecreatefrompng($qrPath);
         if (!$image || !$qr) {
            throw new \RuntimeException('Gagal memuat template atau QR Code');
         }

         imagealphablending($qr, true);
         imagesavealpha($qr, true);

         $width = imagesx($image);
         $height = imagesy($image);

         $text = $guru['nama_guru'];
         $fontSize = 27;
         $angle = 0;
         $textColor = imagecolorallocate($image, 0, 0, 0);
         $shadowColor = imagecolorallocate($image, 160, 160, 160);

         $box = imagettfbbox($fontSize, $angle, $fontPath, $text);
         $textWidth = abs($box[4] - $box[0]);
         $textHeight = abs($box[5] - $box[1]);

         $xText = ($width / 2) - ($textWidth / 2);
         $yText = ($height / 2) + ($textHeight / 2) + 270;

         imagettftext($image, $fontSize, $angle, $xText + 2, $yText + 2, $shadowColor, $fontPath, $text);
         imagettftext($image, $fontSize, $angle, $xText, $yText, $textColor, $fontPath, $text);

         $qrSize = 400;
         $qrW = imagesx($qr);
         $qrH = imagesy($qr);

         $xQr = ($width - $qrSize) / 2;
         $yQr = ($height - $qrSize) / 2 - 80;

         imagecopyresampled(
            $image,
            $qr,
            $xQr,
            $yQr,
            0,
            0,
            $qrSize,
            $qrSize,
            $qrW,
            $qrH
         );

         ob_start();
         imagejpeg($image, null, 90);
         imagedestroy($image);
         imagedestroy($qr);
         $data = ob_get_clean();

         $downloadName = 'qr-panitia-' . url_title($guru['nama_guru'], lowercase: true) . '.jpg';

         return $this->response
            ->setHeader('Content-Type', 'image/jpeg')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $downloadName . '"')
            ->setHeader('Content-Length', (string) strlen($data))
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setBody($data);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadAllQrSiswa()
   {
      $kelas = null;
      if ($idKelas = $this->request->getVar('id_kelas')) {
         $kelas = $this->getKelasJurusanSlug($idKelas);
         if (!$kelas) {
            session()->setFlashdata([
               'msg' => 'Kelas tidak ditemukan',
               'error' => true
            ]);
            return redirect()->back();
         }
      }

      $this->qrCodeFilePath .= "qr-siswa/" . ($kelas ? "{$kelas}/" : '');

      if (!file_exists($this->qrCodeFilePath) || count(glob($this->qrCodeFilePath . '*')) === 0) {
         session()->setFlashdata([
            'msg' => 'QR Code tidak ditemukan, generate qr terlebih dahulu',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $output = self::UPLOADS_PATH . 'qrcode-tamu' . ($kelas ? "_{$kelas}.zip" : '.zip');

         $this->zipFolder($this->qrCodeFilePath, $output);

         return $this->response->download($output, null,  true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadAllQrSiswaWithTemplate()
   {
      $templatePath = FCPATH . 'assets/img/template-qr/template-qr.JPG';
      $fontPath = FCPATH . 'assets/fonts/Arial.ttf';
      if (!file_exists($fontPath)) {
         $fontPath = FCPATH . 'assets/fonts/Roboto-Medium.ttf';
      }
      if (!file_exists($templatePath) || !file_exists($fontPath)) {
         session()->setFlashdata([
            'msg' => 'Template QR atau font tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      $kelasSlug = null;
      $idKelas = $this->request->getVar('id_kelas');
      if ($idKelas) {
         $kelasSlug = $this->getKelasJurusanSlug($idKelas);
         if (!$kelasSlug) {
            session()->setFlashdata([
               'msg' => 'Kelas tidak ditemukan',
               'error' => true
            ]);
            return redirect()->back();
         }
      }

      $siswaModel = new SiswaModel();
      $siswas = $idKelas ? $siswaModel->where('id_kelas', $idKelas)->findAll() : $siswaModel->findAll();
      if (!$siswas) {
         session()->setFlashdata([
            'msg' => 'Data siswa tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $qrDir = self::UPLOADS_PATH . 'qr-siswa/' . ($kelasSlug ? "{$kelasSlug}/" : '');
         $outputDir = self::UPLOADS_PATH . 'qr-siswa-template/' . ($kelasSlug ? "{$kelasSlug}/" : '');
         if (!file_exists($qrDir)) {
            mkdir($qrDir, recursive: true);
         }
         if (!file_exists($outputDir)) {
            mkdir($outputDir, recursive: true);
         }

         $this->setQrCodeFilePath($qrDir);

         foreach ($siswas as $siswa) {
            $qrPath = $this->generate(
               nama: $siswa['nama_siswa'],
               nomor: $siswa['nis'],
               unique_code: $siswa['unique_code'],
            );

            // echo '<pre>'.print_r($siswa['nama_siswa'],1).'</pre>'; die();

            $fileName = url_title($siswa['nama_siswa'], lowercase: true) . '.jpg';
            $this->createTemplateImage($templatePath, $fontPath, $qrPath, $siswa['nama_siswa'], $outputDir . $fileName);
         }

         $zipOutput = self::UPLOADS_PATH . 'qrcode-tamu-template' . ($kelasSlug ? "_{$kelasSlug}.zip" : '.zip');
         $this->zipFolder($outputDir, $zipOutput);

         return $this->response->download($zipOutput, null, true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadAllQrGuru()
   {
      $this->qrCodeFilePath .= 'qr-guru/';

      if (!file_exists($this->qrCodeFilePath) || count(glob($this->qrCodeFilePath . '*')) === 0) {
         session()->setFlashdata([
            'msg' => 'QR Code tidak ditemukan, generate qr terlebih dahulu',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $output = self::UPLOADS_PATH . DIRECTORY_SEPARATOR . 'qrcode-panitia.zip';

         $this->zipFolder($this->qrCodeFilePath, $output);

         return $this->response->download($output, null,  true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   public function downloadAllQrGuruWithTemplate()
   {
      $templatePath = FCPATH . 'assets/img/template-qr/template-qr.JPG';
      $fontPath = FCPATH . 'assets/fonts/Arial.ttf';
      if (!file_exists($fontPath)) {
         $fontPath = FCPATH . 'assets/fonts/Roboto-Medium.ttf';
      }
      if (!file_exists($templatePath) || !file_exists($fontPath)) {
         session()->setFlashdata([
            'msg' => 'Template QR atau font tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      $guruModel = new GuruModel();
      $gurus = $guruModel->findAll();
      if (!$gurus) {
         session()->setFlashdata([
            'msg' => 'Data guru tidak ditemukan',
            'error' => true
         ]);
         return redirect()->back();
      }

      try {
         $qrDir = self::UPLOADS_PATH . 'qr-guru/';
         $outputDir = self::UPLOADS_PATH . 'qr-guru-template/';
         if (!file_exists($qrDir)) {
            mkdir($qrDir, recursive: true);
         }
         if (!file_exists($outputDir)) {
            mkdir($outputDir, recursive: true);
         }

         $this->setQrCodeFilePath($qrDir);
         $this->qrCode->setForegroundColor($this->foregroundColor2);
         $this->label->setTextColor($this->foregroundColor2);

         foreach ($gurus as $guru) {
            $qrPath = $this->generate(
               nama: $guru['nama_guru'],
               nomor: $guru['nuptk'],
               unique_code: $guru['unique_code'],
            );

            $fileName = url_title($guru['nama_guru'], lowercase: true) . '.jpg';
            $this->createTemplateImage($templatePath, $fontPath, $qrPath, $guru['nama_guru'], $outputDir . $fileName);
         }

         $zipOutput = self::UPLOADS_PATH . 'qrcode-guru-template.zip';
         $this->zipFolder($outputDir, $zipOutput);

         return $this->response->download($zipOutput, null, true);
      } catch (\Throwable $th) {
         session()->setFlashdata([
            'msg' => $th->getMessage(),
            'error' => true
         ]);
         return redirect()->back();
      }
   }

   private function zipFolder(string $folder, string $output)
   {
      $normalizedFolder = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $folder), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
      $basePath = realpath($normalizedFolder) ?: $normalizedFolder;

      $zip = new \ZipArchive;
      $zip->open($output, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

      $files = new \RecursiveIteratorIterator(
         new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS),
         \RecursiveIteratorIterator::LEAVES_ONLY
      );

      foreach ($files as $file) {
         if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($basePath));
            $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

            $zip->addFile($filePath, $relativePath);
         }
      }
      $zip->close();
   }

   protected function kelas(string $unique_code)
   {
      return self::UPLOADS_PATH . DIRECTORY_SEPARATOR . "qr-siswa/{$unique_code}.png";
   }

   protected function getKelasJurusanSlug(string $idKelas)
   {
      $kelas = (new KelasModel)->getKelas($idKelas);;
      if ($kelas) {
         return url_title($kelas->kelas . ' ' . $kelas->jurusan, lowercase: true);
      } else {
         return false;
      }
   }

   private function createTemplateImage(string $templatePath, string $fontPath, string $qrPath, string $text, string $outputPath): void
   {
      // bersihkan buffer
      while (ob_get_level()) {
         ob_end_clean();
      }

      $image = imagecreatefromjpeg($templatePath);
      $qr = imagecreatefrompng($qrPath);
      if (!$image || !$qr) {
         throw new \RuntimeException('Gagal memuat template atau QR Code');
      }

      imagealphablending($qr, true);
      imagesavealpha($qr, true);

      $width = imagesx($image);
      $height = imagesy($image);

      $fontSize = 27;
      $angle = 0;
      $textColor = imagecolorallocate($image, 0, 0, 0);
      $shadowColor = imagecolorallocate($image, 160, 160, 160);

      // echo '<pre>'.print_r($text,1).'</pre>'; die();

      $box = imagettfbbox($fontSize, $angle, $fontPath, $text);
      $textWidth = abs($box[4] - $box[0]);
      $textHeight = abs($box[5] - $box[1]);

      $xText = ($width / 2) - ($textWidth / 2);
      $yText = ($height / 2) + ($textHeight / 2) + 270;

      imagettftext($image, $fontSize, $angle, $xText + 2, $yText + 2, $shadowColor, $fontPath, $text);
      imagettftext($image, $fontSize, $angle, $xText, $yText, $textColor, $fontPath, $text);

      $qrSize = 400;
      $qrW = imagesx($qr);
      $qrH = imagesy($qr);

      $xQr = ($width - $qrSize) / 2;
      $yQr = ($height - $qrSize) / 2 - 80;

      imagecopyresampled(
         $image,
         $qr,
         $xQr,
         $yQr,
         0,
         0,
         $qrSize,
         $qrSize,
         $qrW,
         $qrH
      );

      imagejpeg($image, $outputPath, 90);
      imagedestroy($image);
      imagedestroy($qr);
   }
}

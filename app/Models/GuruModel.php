<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GuruModel extends Model
{
   protected $allowedFields = [
      'nuptk',
      'nama_guru',
      'id_kelas',
      'jenis_kelamin',
      'alamat',
      'no_hp',
      'unique_code'
   ];

   protected $table = 'tb_guru';

   protected $primaryKey = 'id_guru';

   public function cekGuru(string $unique_code)
   {
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getAllGuru()
   {
      return $this->orderBy('nama_guru')->findAll();
   }

   public function getGuruById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function createGuru($nuptk, $nama, $idKelas, $jenisKelamin, $alamat, $noHp)
   {
      return $this->save([
         'nuptk' => $nuptk,
         'nama_guru' => $nama,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
         'unique_code' => sha1($nama . md5($nuptk . $nama . $noHp)) . substr(sha1($nuptk . rand(0, 100)), 0, 24)
      ]);
   }

   public function updateGuru($id, $nuptk, $nama, $idKelas, $jenisKelamin, $alamat, $noHp)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nuptk' => $nuptk,
         'nama_guru' => $nama,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'alamat' => $alamat,
         'no_hp' => $noHp,
      ]);
   }

   private function normalizeCSVHeader($value)
   {
      $value = trim((string) $value);
      if ($value === '') {
         return '';
      }
      $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);
      $value = strtolower($value);
      $value = str_replace(' ', '_', $value);
      return $value;
   }

   private function writeArrayToTmpFile($array, $filePath)
   {
      if (!empty($array)) {
         $txtFile = fopen(FCPATH . 'uploads/tmp/' . $filePath, 'w');
         fwrite($txtFile, serialize($array));
         fclose($txtFile);
         $obj = new \stdClass();
         $obj->numberOfItems = countItems($array);
         $obj->txtFileName = $filePath;
         return $obj;
      }
      return false;
   }

   public function generateCSVObject($filePath)
   {
      $array = array();
      $fields = array();
      $txtName = uniqid() . '.txt';
      $i = 0;
      $handle = fopen($filePath, 'r');
      if ($handle) {
         while (($row = fgetcsv($handle)) !== false) {
            if (empty($fields)) {
               $fields = array_map([$this, 'normalizeCSVHeader'], $row);
               continue;
            }
            $rowData = [];
            $hasValue = false;
            foreach ($row as $k => $value) {
               $fieldKey = $fields[$k] ?? '';
               if ($fieldKey === '') {
                  continue;
               }
               if (!empty($value)) {
                  $hasValue = true;
               }
               $rowData[$fieldKey] = is_string($value) ? trim($value) : $value;
            }
            if ($hasValue) {
               $array[$i] = $rowData;
               $i++;
            }
         }
         if (!feof($handle)) {
            return false;
         }
         fclose($handle);
         $obj = $this->writeArrayToTmpFile($array, $txtName);
         @unlink($filePath);
         return $obj;
      }
      return false;
   }

   public function generateSpreadsheetObject($filePath, $ext)
   {
      $ext = strtolower((string) $ext);
      if ($ext === 'csv') {
         return $this->generateCSVObject($filePath);
      }

      $fullPath = FCPATH . $filePath;
      if (!file_exists($fullPath)) {
         return false;
      }

      try {
         $spreadsheet = IOFactory::load($fullPath);
      } catch (\Throwable $e) {
         return false;
      }

      $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
      if (empty($rows)) {
         return false;
      }

      $fieldsRow = array_shift($rows);
      $fields = [];
      foreach ($fieldsRow as $col => $value) {
         $fields[$col] = $this->normalizeCSVHeader($value);
      }

      $array = [];
      $i = 0;
      foreach ($rows as $row) {
         $rowData = [];
         $hasValue = false;
         foreach ($fields as $col => $fieldKey) {
            if ($fieldKey === '') {
               continue;
            }
            $cellValue = $row[$col] ?? '';
            if ($cellValue !== null && $cellValue !== '') {
               $hasValue = true;
            }
            $rowData[$fieldKey] = is_string($cellValue) ? trim($cellValue) : $cellValue;
         }
         if ($hasValue) {
            $array[$i] = $rowData;
            $i++;
         }
      }

      $txtName = uniqid() . '.txt';
      $obj = $this->writeArrayToTmpFile($array, $txtName);
      @unlink($fullPath);
      return $obj;
   }

   public function importCSVItem($txtFileName, $index)
   {
      $filePath = FCPATH . 'uploads/tmp/' . $txtFileName;
      $file = fopen($filePath, 'r');
      $content = fread($file, filesize($filePath));
      $array = @unserialize($content);
      if (!empty($array)) {
         $i = 1;
         foreach ($array as $item) {
            if ($i == $index) {
               $data = array();
               $data['nuptk'] = getCSVInputValue($item, 'nuptk');
               $data['nama_guru'] = getCSVInputValue($item, 'nama_guru');
               $data['id_kelas'] = getCSVInputValue($item, 'id_kelas', 'int');
               $data['jenis_kelamin'] = getCSVInputValue($item, 'jenis_kelamin');
               $data['alamat'] = getCSVInputValue($item, 'alamat');
               $data['no_hp'] = getCSVInputValue($item, 'no_hp');
               $data['unique_code'] = sha1($data['nama_guru'] . md5($data['nuptk'] . $data['nama_guru'] . $data['no_hp'])) . substr(sha1($data['nuptk'] . rand(0, 100)), 0, 24);

               if (empty($data['nuptk']) || empty($data['nama_guru']) || empty($data['id_kelas']) || empty($data['jenis_kelamin'])) {
                  return ['error' => 'Kolom wajib: nuptk, nama_guru, id_kelas, jenis_kelamin'];
               }

               $kelasExists = $this->db->table('tb_kelas')->where('id_kelas', $data['id_kelas'])->countAllResults();
               if ($kelasExists < 1) {
                  return ['error' => 'ID agenda tidak valid. Cek List Agenda.'];
               }

               try {
                  $this->insert($data);
               } catch (\Throwable $e) {
                  return ['error' => 'Gagal menyimpan data.'];
               }
               return $data;
            }
            $i++;
         }
      }
   }
}

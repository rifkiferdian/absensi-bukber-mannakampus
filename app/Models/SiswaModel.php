<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaModel extends Model
{
   protected function initialize()
   {
      $this->allowedFields = [
      'nis',
      'nama_siswa',
      'id_kelas',
      'jenis_kelamin',
      'no_hp',
      'keterangan',
      'unique_code'
   ];
   }

   protected $table = 'tb_siswa';

   protected $primaryKey = 'id_siswa';

   public function cekSiswa(string $unique_code)
   {
      $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_jurusan.id = tb_kelas.id_jurusan',
         'LEFT'
      );
      return $this->where(['unique_code' => $unique_code])->first();
   }

   public function getSiswaById($id)
   {
      return $this->where([$this->primaryKey => $id])->first();
   }

   public function getAllSiswaWithKelas($kelas = null, $jurusan = null)
   {
      $query = $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_kelas.id_jurusan = tb_jurusan.id',
         'LEFT'
      );

      if (!empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas, 'jurusan' => $jurusan]);
      } else if (empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['jurusan' => $jurusan]);
      } else if (!empty($kelas) && empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas]);
      } else {
         $query = $this;
      }

      return $query->orderBy('nama_siswa')->findAll();
   }

   public function getAllSiswaWithKelasPaginated($kelas = null, $jurusan = null, $perPage = 50, $group = 'siswa', $search = null, $page = 1)
   {
      $query = $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )->join(
         'tb_jurusan',
         'tb_kelas.id_jurusan = tb_jurusan.id',
         'LEFT'
      );

      if (!empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas, 'jurusan' => $jurusan]);
      } else if (empty($kelas) && !empty($jurusan)) {
         $query = $this->where(['jurusan' => $jurusan]);
      } else if (!empty($kelas) && empty($jurusan)) {
         $query = $this->where(['kelas' => $kelas]);
      } else {
         $query = $this;
      }

      if (!empty($search)) {
         $query = $query->like('tb_siswa.nama_siswa', $search);
      }

      return $query->orderBy('nama_siswa')->paginate($perPage, $group, $page);
   }

   public function getSiswaByKelas($id_kelas)
   {
      return $this->join(
         'tb_kelas',
         'tb_kelas.id_kelas = tb_siswa.id_kelas',
         'LEFT'
      )
         ->join('tb_jurusan', 'tb_kelas.id_jurusan = tb_jurusan.id', 'left')
         ->where(['tb_siswa.id_kelas' => $id_kelas])
         ->orderBy('nama_siswa')
         ->findAll();
   }

   public function createSiswa($nis, $nama, $idKelas, $jenisKelamin, $noHp, $keterangan = null)
   {
      return $this->save([
         'nis' => $nis,
         'nama_siswa' => $nama,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'keterangan' => $keterangan,
         'unique_code' => generateToken()
      ]);
   }

   public function updateSiswa($id, $nis, $nama, $idKelas, $jenisKelamin, $noHp, $keterangan = null)
   {
      return $this->save([
         $this->primaryKey => $id,
         'nis' => $nis,
         'nama_siswa' => $nama,
         'id_kelas' => $idKelas,
         'jenis_kelamin' => $jenisKelamin,
         'no_hp' => $noHp,
         'keterangan' => $keterangan,
      ]);
   }

   public function getSiswaCountByKelas($kelasId)
   {
      $tree = array();
      $kelasId = cleanNumber($kelasId);
      if (!empty($kelasId)) {
         array_push($tree, $kelasId);
      }

      $kelasIds = $tree;
      if (countItems($kelasIds) < 1) {
         return array();
      }

      return $this->whereIn('tb_siswa.id_kelas', $kelasIds, false)->countAllResults();
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

   //generate CSV object
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

   //generate CSV/XLS/XLSX object
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
         log_message('error', 'Spreadsheet load failed: ' . $e->getMessage());
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

   //import csv item
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
               $data['nis'] = getCSVInputValue($item, 'nis', 'int');
               $data['nama_siswa'] = getCSVInputValue($item, 'nama_siswa');
               $data['id_kelas'] = getCSVInputValue($item, 'id_kelas', 'int');
               $data['jenis_kelamin'] = getCSVInputValue($item, 'jenis_kelamin');
               $data['no_hp'] = getCSVInputValue($item, 'no_hp');
               $data['keterangan'] = getCSVInputValue($item, 'keterangan');
               $data['unique_code'] = generateToken();

               if (empty($data['nis']) || empty($data['nama_siswa']) || empty($data['id_kelas']) || empty($data['jenis_kelamin'])) {
                  return ['error' => 'Kolom wajib: nis, nama_siswa, id_kelas, jenis_kelamin'];
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

   public function getSiswa($id)
   {
      return $this->where('id_siswa', cleanNumber($id))->get()->getRow();
   }

   //delete post
   public function deleteSiswa($id)
   {
      $siswa = $this->getSiswa($id);
      if (!empty($siswa)) {
         //delete siswa
         return $this->where('id_siswa', $siswa->id_siswa)->delete();
      }
      return false;
   }

   //delete multi post
   public function deleteMultiSelected($siswaIds)
   {
      if (!empty($siswaIds)) {
         foreach ($siswaIds as $id) {
            $this->deleteSiswa($id);
         }
      }
   }
}

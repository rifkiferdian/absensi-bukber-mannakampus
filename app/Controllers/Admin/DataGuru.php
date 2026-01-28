<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;
use App\Models\KelasModel;

use App\Controllers\BaseController;
use App\Models\UploadModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataGuru extends BaseController
{
   protected GuruModel $guruModel;
   protected KelasModel $kelasModel;

   protected $guruValidationRules = [
      'nuptk' => [
         'rules' => 'required|max_length[10]|min_length[3]',
         'errors' => [
            'required' => 'NUPTK harus diisi.',
            'is_unique' => 'NUPTK ini telah terdaftar.',
            'min_length[3]' => 'Panjang NUPTK minimal 3 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'id_kelas' => [
         'rules' => 'required',
         'errors' => [
            'required' => 'Agenda harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'permit_empty|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->guruModel = new GuruModel();
      $this->kelasModel = new KelasModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Panitia',
         'ctx' => 'guru',
         'kelas' => $this->kelasModel->getDataKelas(),
      ];

      return view('admin/data/data-guru', $data);
   }

   public function ambilDataGuru()
   {
      $kelas = $this->request->getVar('kelas') ?? null;
      $search = $this->request->getVar('search') ?? null;
      $result = $this->guruModel->getAllGuru($kelas, $search);

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-guru', $data);
   }

   public function formTambahGuru()
   {
      $data = [
         'ctx' => 'guru',
         'title' => 'Tambah Data',
         'kelas' => $this->kelasModel->getDataKelas()
      ];

      return view('admin/data/create/create-data-guru', $data);
   }

   public function saveGuru()
   {
      // validasi
      if (!$this->validate($this->guruValidationRules)) {
         $data = [
            'ctx' => 'guru',
            'title' => 'Tambah Data',
            'kelas' => $this->kelasModel->getDataKelas(),
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-guru', $data);
      }

      // simpan
      $result = $this->guruModel->createGuru(
         nuptk: $this->request->getVar('nuptk'),
         nama: $this->request->getVar('nama'),
         idKelas: intval($this->request->getVar('id_kelas')),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru/create/');
   }

   public function formEditGuru($id)
   {
      $guru = $this->guruModel->getGuruById($id);

      if (empty($guru)) {
         throw new PageNotFoundException('Data guru dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $guru,
         'ctx' => 'guru',
         'title' => 'Edit Data Guru',
         'kelas' => $this->kelasModel->getDataKelas(),
      ];

      return view('admin/data/edit/edit-data-guru', $data);
   }

   public function updateGuru()
   {
      $idGuru = $this->request->getVar('id');

      // validasi
      if (!$this->validate($this->guruValidationRules)) {
         $data = [
            'data' => $this->guruModel->getGuruById($idGuru),
            'ctx' => 'guru',
            'title' => 'Edit Data Guru',
            'kelas' => $this->kelasModel->getDataKelas(),
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-guru', $data);
      }

      // update
      $result = $this->guruModel->updateGuru(
         id: $idGuru,
         nuptk: $this->request->getVar('nuptk'),
         nama: $this->request->getVar('nama'),
         idKelas: intval($this->request->getVar('id_kelas')),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru/edit/' . $idGuru);
   }

   public function delete($id)
   {
      $result = $this->guruModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru');
   }

   /*
    *-------------------------------------------------------------------------------------------------
    * IMPORT GURU
    *-------------------------------------------------------------------------------------------------
    */

   public function bulkPostGuru()
   {
      $data['title'] = 'Import Panitia';
      $data['ctx'] = 'guru';
      $data['kelas'] = $this->kelasModel->getDataKelas();

      return view('/admin/data/import-guru', $data);
   }

   public function generateCSVObjectPost()
   {
      $uploadModel = new UploadModel();
      $files = glob(FCPATH . 'uploads/tmp/*.txt');
      if (!empty($files)) {
         foreach ($files as $item) {
            @unlink($item);
         }
      }
      $file = $uploadModel->uploadCSVFile('file');
      if (!empty($file) && !empty($file['path'])) {
         $obj = $this->guruModel->generateSpreadsheetObject($file['path'], $file['ext'] ?? '');
         if (!empty($obj)) {
            $data = [
               'result' => 1,
               'numberOfItems' => $obj->numberOfItems,
               'txtFileName' => $obj->txtFileName,
            ];
            echo json_encode($data);
            exit();
         }
      }
      echo json_encode(['result' => 0]);
   }

   public function importCSVItemPost()
   {
      $txtFileName = inputPost('txtFileName');
      $index = inputPost('index');
      $guru = $this->guruModel->importCSVItem($txtFileName, $index);
      if (!empty($guru) && empty($guru['error'])) {
         $data = [
            'result' => 1,
            'guru' => $guru,
            'index' => $index
         ];
         echo json_encode($data);
      } else {
         $data = [
            'result' => 0,
            'index' => $index,
            'error' => $guru['error'] ?? ''
         ];
         echo json_encode($data);
      }
   }

   public function downloadCSVFilePost()
   {
      $submit = inputPost('submit');
      $response = \Config\Services::response();
      if ($submit == 'xlsx_guru_template') {
         return $response->download(FCPATH . 'assets/file/xlsx_guru_template.xlsx', null);
      } elseif ($submit == 'csv_guru_template') {
         return $response->download(FCPATH . 'assets/file/csv_guru_template.csv', null);
      }
   }

   function dataQrGuruPublic()
   {
      $data['title'] = 'Data Guru';
      $data['guru'] = $this->guruModel->getAllGuru();
      return view('/templates/data-guru-public', $data);
   }
}

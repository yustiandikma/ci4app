<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
    protected $komikModel;

    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }

    public function index()
    {
        // $komik = $this->komikModel->findAll();

        $data =
            [
                'title' => 'Daftar Komik',
                'komik' => $this->komikModel->getKomik()
            ];

        // $komikModel = new \App\Models\KomikModel();
        // $komikModel = new KomikModel();

        return view('komik/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik($slug)
        ];

        //jika komik tidak ada di tabel
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Komik ' . $slug . ' Tidak Ditemukan');
        }

        return view('komik\detail', $data);
    }

    public function create()
    {
        // session();
        $data = [
            'title' => 'Form Tambah Data Komik',
            'validation' => \Config\Services::validation()
        ];
        return view('komik/create', $data);
    }

    public function save()
    {

        // validasi input
        if (!$this->validate(
            [
                'judul' => [
                    'rules' => 'required|is_unique[komik.judul]',
                    'errors' => [
                        'required' => '{field} Komik Harus Diisi',
                        'is_unique' => '{field} Komik Sudah Terdaftar'
                    ]
                ],
                'sampul' => [
                    'rules' => 'max_size[sampul,2000]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'Ukuran Gambar Terlalu Besar',
                        'is_image' => 'File Yang Anda Pilih Bukan Gambar',
                        'mime_in' => 'File Yang Anda Pilih Bukan Gambar'
                    ]
                ]
            ]
        )) {

            // $validation = \Config\Services::validation();

            // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);

            return redirect()->to('/komik/create')->withInput();
        }

        //ambil gambar
        $fileSampul = $this->request->getFile('sampul');
        //apakah ada atau tidak gambar yg di upload
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'default.jpg';
        } else {

            //generate nama sampul random
            $namaSampul = $fileSampul->getRandomName();
            //pindahkan file ke folder img
            $fileSampul->move('img', $namaSampul);
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan.');

        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        $this->komikModel->delete($id);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus.');
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        $data = [
            'title' => 'Form Ubah Data Komik',
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];
        return view('komik/edit', $data);
    }

    public function update($id)
    {
        //cek judul apakah sudah ada apa belum,
        $komikLama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if ($komikLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        if (!$this->validate(
            [
                'judul' => [
                    'rules' => $rule_judul,
                    'errors' => [
                        'required' => '{field} Komik Harus Diisi',
                        'is_unique' => '{field} Komik Sudah Terdaftar'
                    ]
                ]
            ]
        )) {

            $validation = \Config\Services::validation();

            return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
        }
        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->komikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);

        session()->setFlashdata('pesan', 'Data Berhasil Diubah.');

        return redirect()->to('/komik');
    }
}

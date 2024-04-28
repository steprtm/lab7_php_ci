<?php
namespace App\Controllers;
use App\Models\ArtikelModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;

class Artikel extends BaseController
{
    public function index()
    {
        $title = 'Daftar Artikel';
        $model = new ArtikelModel();
        $artikel = $model->findAll();
        return view('artikel/index', compact('artikel', 'title'));
    }

    public function view($slug)
    {
        $model = new ArtikelModel();
        $artikel = $model->where(['slug' => $slug])->first();

        if (!$artikel) {
            throw PageNotFoundException::forPageNotFound();
        }

        $title = $artikel['judul'];
        return view('artikel/detail', compact('artikel', 'title'));
    }

    public function admin_index()
    {
        $title = 'Daftar Artikel';
        $model = new ArtikelModel();
        $artikel = $model->findAll();
        return view('artikel/admin_index', compact('artikel', 'title'));
    }

    public function add()
    {
        $validation = Services::validation();
        $validation->setRules([
            'judul' => 'required|string',
            'isi' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $judul = $this->request->getPost('judul');
        $judul = is_array($judul) ? implode(' ', $judul) : $judul;  // Convert array to string if needed.
        $judul = $judul ?? 'Default Title';  // Provide a default if null.

        $artikel = new ArtikelModel();
        $artikel->insert([
            'judul' => $judul,
            'isi' => $this->request->getPost('isi'),
            'slug' => url_title($judul),
        ]);

        return redirect('admin/artikel');
    }
    public function edit($id)
{
    $artikel = new ArtikelModel();
// validasi data.
        $validation = \Config\Services::validation();
        $validation->setRules(['judul' => 'required']);
        $isDataValid = $validation->withRequest($this->request)->run();
            if ($isDataValid) {
            $artikel->update($id, [
'judul' => $this->request->getPost('judul'),
'isi' => $this->request->getPost('isi'),
            ]);
            return redirect('admin/artikel');
        }
// ambil data lama
        $data = $artikel->where('id', $id)->first();
        $title = "Edit Artikel";
        return view('artikel/form_edit', compact('title', 'data'));
}
public function delete($id){
    $artikel = new ArtikelModel();
    $artikel->delete($id);
return redirect('admin/artikel');
}
}

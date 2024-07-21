<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductModel;
use Dompdf\Dompdf;

class ProdukController extends BaseController
{
    protected $produk;
    protected $validation;

    function __construct()
    {
        $this->produk = new ProductModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $produk = $this->produk->findAll();
        $data['produk'] = $produk;
        return view('v_produk', $data);
    }

    public function create()
    {
        $dataFoto = $this->request->getFile('foto');

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah'),
            'created_at' => date("Y-m-d H:i:s")
        ];

        // Validation rules
        $this->validation->setRules([
            'nama' => 'required|min_length[5]',
            'harga' => 'required|numeric',
            'jumlah' => 'required|numeric'
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('failed', $this->validation->getErrors());
        }

        if ($dataFoto->isValid()) {
            $fileName = $dataFoto->getRandomName();
            $dataForm['foto'] = $fileName;
            $dataFoto->move('img/', $fileName);
        }

        $this->produk->insert($dataForm);

        return redirect('produk')->with('success', 'Data Berhasil Ditambah');
    }

    public function edit($id)
    {
        $dataProduk = $this->produk->find($id);

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah'),
            'updated_at' => date("Y-m-d H:i:s")
        ];

        // Validation rules
        $this->validation->setRules([
            'nama' => 'required|min_length[5]',
            'harga' => 'required|numeric',
            'jumlah' => 'required|numeric'
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('failed', $this->validation->getErrors());
        }

        if ($this->request->getPost('check') == 1) {
            if ($dataProduk['foto'] != '' and file_exists("img/" . $dataProduk['foto'] . "")) {
                unlink("img/" . $dataProduk['foto']);
            }

            $dataFoto = $this->request->getFile('foto');

            if ($dataFoto->isValid()) {
                $fileName = $dataFoto->getRandomName();
                $dataFoto->move('img/', $fileName);
                $dataForm['foto'] = $fileName;
            }
        }

        $this->produk->update($id, $dataForm);

        return redirect('produk')->with('success', 'Data Berhasil Diubah');
    }

    public function delete($id)
    {
        $dataProduk = $this->produk->find($id);

        if ($dataProduk['foto'] != '' and file_exists("img/" . $dataProduk['foto'] . "")) {
            unlink("img/" . $dataProduk['foto']);
        }

        $this->produk->delete($id);

        return redirect('produk')->with('success', 'Data Berhasil Dihapus');
    }

    public function download()
    {
        $produk = $this->produk->findAll();

        $html = view('v_produkPDF', ['product' => $produk]);

        $filename = date('y-m-d-H-i-s') . '-produk';

        // instantiate and use the dompdf class
        $dompdf = new Dompdf();

        // load HTML content
        $dompdf->loadHtml($html);

        // (optional) setup the paper size and orientation
        $dompdf->setPaper('A4', 'potrait');

        // render html as PDF
        $dompdf->render();

        // output the generated pdf
        $dompdf->stream($filename);
    }
}

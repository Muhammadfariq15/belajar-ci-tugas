<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;

class AuthController extends BaseController
{
    protected $user;

    function __construct()
    {
        helper('form');
        $this->user = new UserModel();
    }

    public function login()
    {
    if ($this->request->getPost()) {
        $rules = [
            'username' => 'required|min_length[6]',
            'password' => 'required|min_length[7]|numeric',
        ];

        if ($this->validate($rules)) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            $dataUser = $this->user->where(['username' => $username])->first(); //pasw 1234567

            if ($dataUser) {
                if (password_verify($password, $dataUser['password'])) {
                    session()->set([
                        'username' => $dataUser['username'],
                        'role' => $dataUser['role'],
                        'isLoggedIn' => TRUE
                    ]);

                    return redirect()->to(base_url('/'));
                } else {
                    session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('failed', $this->validator->listErrors());
            return redirect()->back();
        }
    }

    return view('v_login');
    }

        public function logout()
        {
            session()->destroy();
            return redirect()->to('login');
        }

        public function register() {
            // Pastikan metode request adalah POST
            if ($this->request->getMethod() === 'post') {
                // Ambil data dari POST
                $username = $this->request->getPost('username');
                $password = $this->request->getPost('password');
                $email = $this->request->getPost('email');
                $role = 'user';  // Atur peran default sebagai 'user'
    
                // Validasi input
                $validation = \Config\Services::validation();
                $validation->setRules([
                    'username' => 'required',
                    'email' => 'required|valid_email',
                    'password' => 'required|min_length[6]'
                ]);
    
                if ($validation->withRequest($this->request)->run()) {
                    // Lakukan validasi dan simpan data ke database
                    $userModel = new UserModel();
    
                    $data = [
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'role' => $role
                    ];
    
                    if ($userModel->insert($data)) {
                        return redirect()->to('/success'); // Atau beri respon sesuai kebutuhan
                    } else {
                        return redirect()->back()->withInput()->with('error', 'Registrasi gagal, coba lagi.');
                    }
                } else {
                    // Jika validasi gagal, kembali ke form dengan pesan error
                    return redirect()->back()->withInput()->with('errors', $validation->getErrors());
                }
            }
            return view('v_regis');
        }
}

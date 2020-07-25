<?php

class Mahasiswa extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mahasiswa_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['judul'] = "Daftar Mahasiswa";

        $this->load->model('Mahasiswa_model', 'mahasiswa');
        
        $this->load->library('pagination');

        if($this->input->post('submit')){
            $data['keyword'] = $this->input->post('keyword');
            $this->session->set_userdata('keyword', $data['keyword']);
        }else{
            $data['keyword'] = $this->session->userdata('keyword');
        }
        
        $this->db->like('nama', $data['keyword']);
        $this->db->or_like('nim', $data['keyword']);
        $this->db->from('mahasiswa');
        
        $config['base_url'] = 'http://localhost/proyekKP/mahasiswa/index';
        $config['total_rows'] = $this->db->count_all_results();
        $data['total_rows'] = $config['total_rows'];
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        $data['start'] = $this->uri->segment(3);
        $data['mahasiswa'] = $this->mahasiswa->GetMahasiswa($config['per_page'], $data['start'], $data['keyword']);

        $this->load->view('templates/header', $data);
        $this->load->view('mahasiswa/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah()
    {
        $data['judul'] = "Tambah Data Mahasiswa";
        
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('nim', 'NIM', 'required|numeric');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        if( $this->form_validation->run() == FALSE ){
            $this->load->view('templates/header', $data);
            $this->load->view('mahasiswa/tambah', $data);
            $this->load->view('templates/footer');
        }else{
            $this->Mahasiswa_model->tambahDataMahasiswa();
            $this->session->set_flashdata('flash', 'Ditambahkan');
            redirect('mahasiswa');
        }
        
    }

    public function hapus($id)
    {
        $this->Mahasiswa_model->hapusDataMahasiswa($id);
        $this->session->set_flashdata('flash', 'Dihapus');
        redirect('mahasiswa');
    }

    public function detail($id)
    {
        $data['judul'] = 'Detail Data Mahasiswa';
        $data['mahasiswa'] = $this->Mahasiswa_model->getMahasiswaByID($id);
        $this->load->view('templates/header', $data);
        $this->load->view('mahasiswa/detail', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $data['judul'] = "Edit Data Mahasiswa";
        $data['mahasiswa'] = $this->Mahasiswa_model->getMahasiswaByID($id);
        $data['jurusan'] = ['Informatika', 'Matematika', 'Teknik Mesin', 'Teknik Elektro'];

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('nim', 'NIM', 'required|numeric');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        
        if( $this->form_validation->run() == FALSE ){
            $this->load->view('templates/header', $data);
            $this->load->view('mahasiswa/edit', $data);
            $this->load->view('templates/footer');
        }else{
            $this->Mahasiswa_model->editDataMahasiswa();
            $this->session->set_flashdata('flash', 'Diubah');
            redirect('mahasiswa');
        }
    }
}
<?php

class Dosen extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dosen_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['judul'] = "Daftar Dosen";

        $this->load->model('Dosen_model', 'dosen');
        
        $this->load->library('pagination');

        if($this->input->post('submit')){
            $data['keyword'] = $this->input->post('keyword');
            $this->session->set_userdata('keyword', $data['keyword']);
        }else{
            $data['keyword'] = $this->session->userdata('keyword');
        }
        
        $this->db->like('nama', $data['keyword']);
        $this->db->or_like('npp', $data['keyword']);
        $this->db->from('dosen');
        
        $config['base_url'] = 'http://localhost/proyekKP/dosen/index';
        $config['total_rows'] = $this->db->count_all_results();
        $data['total_rows'] = $config['total_rows'];
        $config['per_page'] = 20;

        $this->pagination->initialize($config);

        $data['start'] = $this->uri->segment(3);
        $data['dosen'] = $this->dosen->GetDosen($config['per_page'], $data['start'], $data['keyword']);

        $this->load->view('templates/header', $data);
        $this->load->view('dosen/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah()
    {
        $data['judul'] = "Tambah Data Dosen";

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('npp', 'NPP', 'required');
        
        if( $this->form_validation->run() == FALSE ){
            $this->load->view('templates/header', $data);
            $this->load->view('dosen/tambah');
            $this->load->view('templates/footer');
        }else{
            $this->Dosen_model->tambahDataDosen();
            $this->session->set_flashdata('flash', 'Ditambahkan');
            redirect('dosen');
        }
        
    }

    public function hapus($id)
    {
        $this->Dosen_model->hapusDataDosen($id);
        $this->session->set_flashdata('flash', 'Dihapus');
        redirect('dosen');
    }

    public function detail($id)
    {
        $data['judul'] = 'Detail Data Dosen';
        $data['dosen'] = $this->Dosen_model->getDosenByID($id);
        $this->load->view('templates/header', $data);
        $this->load->view('dosen/detail', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $data['judul'] = "Edit Data Dosen";
        $data['dosen'] = $this->Dosen_model->getDosenByID($id);

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('nip', 'NIP', 'required');
        
        if( $this->form_validation->run() == FALSE ){
            $this->load->view('templates/header', $data);
            $this->load->view('dosen/edit', $data);
            $this->load->view('templates/footer');
        }else{
            $this->Dosen_model->editDataDosen();
            $this->session->set_flashdata('flash', 'Diubah');
            redirect('dosen');
        }
    }
}
<?php

class Kolokium extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Kolokium_model');
        $this->load->model('Pendadaran_model');
        $this->load->model('Mahasiswa_model');
        $this->load->model('Dosen_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['title'] = "Jadwal Kolokium";

        $this->load->model('Kolokium_model', 'kolokium');

        $this->load->library('pagination');

        if ($this->input->post('submit')) {
            $data['keyword'] = $this->input->post('keyword');
            $this->session->set_userdata('keyword', $data['keyword']);
        } else {
            $data['keyword'] = $this->session->userdata('keyword');
        }

        $this->db->like('nama', $data['keyword']);
        $this->db->or_like('nim', $data['keyword']);
        $this->db->from('kolokium');

        $config['base_url'] = 'http://localhost/informatika/kolokium/index';
        $config['total_rows'] = $this->db->count_all_results();
        $data['total_rows'] = $config['total_rows'];
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        $data['start'] = $this->uri->segment(3);
        $data['kolokium'] = $this->kolokium->GetKolokium($config['per_page'], $data['start'], $data['keyword']);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('kolokium/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah($nim) {
        if ($this->Kolokium_model->cekStatusKolokium($nim) == NULL) {
            $data['title'] = "Tambah Jadwal Kolokium";
            $data['jam'] = ['07.00-08.00', '08.00-09.00', '09.00-10.00', '10.00-11.00', '11.00-12.00', '12.00-13.00', '13.00-14.00', '14.00-15.00', '15.00-16.00', '16.00-17.00'];
            $data['ruang'] = $this->db->get('ruangan')->result_array();
            $data['dosen'] = $this->Dosen_model->getAllDosen();
            $data['mahasiswa'] = $this->Mahasiswa_model->getMahasiswaByNIM($nim);

            $this->form_validation->set_rules('nama', 'Nama Mahasiswa', 'required');
            $this->form_validation->set_rules('nim', 'NIM Mahasiswa', 'required|numeric');
            $this->form_validation->set_rules('dosen1', 'Dosen Pembimbing 1', 'required');
            $this->form_validation->set_rules('dosen2', 'Dosen Pembimbing 2');
            $this->form_validation->set_rules('judul', 'Judul Tugas Akhir', 'required');
            $this->form_validation->set_rules('reviewer', 'Reviewer', 'required');
            $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('templates/header', $data);
                $this->load->view('templates/sidebar', $data);
                $this->load->view('templates/topbar', $data);
                $this->load->view('kolokium/tambah', $data);
                $this->load->view('templates/footer');
            } else {
                $postData = $this->input->post();
                $dosen1 = $postData['dosen1'];
                $dosen2 = $postData['dosen2'];
                $reviewer = $postData['reviewer'];
                $ruang = $postData['ruang'];
                $tanggal = format_indo($postData['tanggal']);
                $durasi = $postData['durasi'];
                $cekDosen = $this->cekInputKolokium($dosen1, $dosen2, $reviewer);
                switch ($cekDosen) {
                    case 0:
                        $this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('samaSemua', 'Dosen Pembimbing 1, 2, maupun reviewer tidak boleh sama');
                        redirect('kolokium/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 1:
                        $this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('dosenReviewerSama', 'Dosen pembimbing tidak boleh sama dengan Dosen reviewer');
                        redirect('kolokium/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 2:
                        $this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('dosen2Sama', 'Dosen pembimbing 2 tidak boleh sama dengan Dosen reviewer');
                        redirect('kolokium/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 3:
                        $this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('dosen1Sama', 'Dosen pembimbing 1 tidak boleh sama dengan Dosen pembimbing 2');
                        redirect('kolokium/tambah/' . $this->session->userdata('nim'));
                        break;
                    default :
                        if ($dosen2 == '') {
                            $hasil = $this->cekBentrok2($dosen1, $reviewer, $ruang, $tanggal, $durasi);
                        } else {
                            $hasil = $this->cekBentrok($dosen1, $dosen2, $reviewer, $ruang, $tanggal, $durasi);
                        }
                        break;
                }
                if ($hasil != NULL) {
//                    var_dump($durasi);
                    $this->session->set_userdata('nim', $nim);
                    $this->session->set_flashdata('bentrok', $hasil);
                    redirect('kolokium/tambah/' . $this->session->userdata('nim'));
                } else {
//                    var_dump($durasi);
                    $this->Kolokium_model->tambahJadwalKolokium();
                    $this->session->set_flashdata('flash', 'Ditambahkan');
                    redirect('kolokium');
                }
            }
        } else {
            $this->session->set_flashdata('terdaftar', 'Mahasiswa Telah terdaftar Kolokium');
            redirect('kolokium');
        }
    }

    public function cekBentrok($dosen1, $dosen2, $reviewer, $ruang, $tanggal, $durasi) {
        $detail = NULL;
        $dataRuang = $this->Kolokium_model->cekStatusRuang($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal kolokium Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $reviewer ||
                    $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $reviewer ||
                    $dr['reviewer'] == $dosen1 || $dr['reviewer'] == $dosen2 || $dr['reviewer'] == $reviewer) {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                    . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } elseif ($dr['ruang'] != $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                    . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                            $detail = $detailBentrok;
                        }
                    }
                }
            } else {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                    . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        } elseif ($dr['ruang'] != $ruang) {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
//                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrok2($dosen1, $reviewer, $ruang, $tanggal, $durasi) {
        $detail = NULL;
        $dataRuang = $this->Kolokium_model->cekStatusRuang($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal kolokium Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $reviewer || $dr['reviewer'] == $dosen1 || $dr['reviewer'] == $reviewer) {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                    . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } else {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                    . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                            $detail = $detailBentrok;
                        }
                    }
                }
            } else {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                    . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
//                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekInputKolokium($dosen1, $dosen2, $reviewer) {
        if ($dosen1 == $reviewer && $dosen2 == $reviewer && $dosen1 == $dosen2) {
            return 0;
        } elseif ($dosen1 == $reviewer) {
            return 1;
        } elseif ($dosen2 == $reviewer) {
            return 2;
        } elseif ($dosen1 == $dosen2) {
            return 3;
        } else {
            return 4;
        }
    }

    public function inputNim() {
        $data['title'] = "Tambah Jadwal Kolokium";
        $data['form'] = 'Form Tambah Jadwal Kolokium';
        $this->form_validation->set_rules('nim', 'NIM Mahasiswa', 'required|numeric');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('templates/inputNim', $data);
            $this->load->view('templates/footer');
        } else {
            $postData = $this->input->post();
            $nim = $postData['nim'];
            if ($this->Mahasiswa_model->getMahasiswaByNIM($nim) != null) {
                $this->tambah($nim);
            } else {
                $this->session->set_flashdata('KolokiumtidakAda', 'Mahasiswa tidak ditemukan');
                redirect('kolokium/inputNim');
            }
        }
    }

    public function hapus($id) {
        $data['kolokium'] = $this->Kolokium_model->getKolokiumByID($id);
        $pendadaran = $this->Pendadaran_model->getPendadaranByNIM($data['kolokium']['nim']);
        if ($pendadaran != null) {
            $this->session->set_flashdata('gagal', 'Telah terdaftar untuk pendadaran');
        } else {
            $this->Kolokium_model->hapusJadwalKolokium($id);
            $this->session->set_flashdata('flash', 'Dihapus');
        }
        redirect('kolokium');
    }

    public function detail($id) {
        $data['title'] = 'Detail  Jadwal Kolokium';
        $data['kolokium'] = $this->Kolokium_model->getKolokiumByID($id);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('kolokium/detail', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['title'] = "Edit Jadwal Kolokium";
        $data['jam'] = ['07.00-08.00', '08.00-09.00', '09.00-10.00', '10.00-11.00', '11.00-12.00', '12.00-13.00', '13.00-14.00', '14.00-15.00', '15.00-16.00', '16.00-17.00'];
        $data['ruang'] = $this->db->get('ruangan')->result_array();
        $data['dosen'] = $this->Dosen_model->getAllDosen();
        $data['kolokium'] = $this->Kolokium_model->getKolokiumByID($id);
        $data['kolokium']['tanggal'] = format_back($data['kolokium']['tanggal']);

        $this->form_validation->set_rules('nama', 'Nama Mahasiswa', 'required');
        $this->form_validation->set_rules('nim', 'NIM Mahasiswa', 'required|numeric');
        $this->form_validation->set_rules('dosen1', 'Dosen Pembimbing 1', 'required');
        $this->form_validation->set_rules('dosen2', 'Dosen Pembimbing 2');
        $this->form_validation->set_rules('judul', 'Judul Tugas Akhir', 'required');
        $this->form_validation->set_rules('reviewer', 'Reviewer', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('kolokium/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $postData = $this->input->post();
            $nim = $postData['nim'];
            $dosen1 = $postData['dosen1'];
            $dosen2 = $postData['dosen2'];
            $reviewer = $postData['reviewer'];
            $ruang = $postData['ruang'];
            $tanggal = format_indo($postData['tanggal']);
            $durasi = $postData['durasi'];
            $cekDosen = $this->cekInputKolokium($dosen1, $dosen2, $reviewer);
            switch ($cekDosen) {
                case 0:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('samaSemua', 'Dosen Pembimbing 1, 2, maupun reviewer tidak boleh sama');
                    redirect('kolokium/edit/' . $this->session->userdata('id'));
                    break;
                case 1:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('dosenReviewerSama', 'Dosen pembimbing tidak boleh sama dengan Dosen reviewer');
                    redirect('kolokium/edit/' . $this->session->userdata('id'));
                    break;
                case 2:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('dosen2Sama', 'Dosen pembimbing 2 tidak boleh sama dengan Dosen reviewer');
                    redirect('kolokium/edit/' . $this->session->userdata('id'));
                    break;
                case 3:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('dosen1Sama', 'Dosen pembimbing 1 tidak boleh sama dengan Dosen pembimbing 2');
                    redirect('kolokium/edit/' . $this->session->userdata('id'));
                    break;
                default :
                    if ($dosen2 == '') {
                        $hasil = $this->cekBentrokEdit2($nim, $dosen1, $reviewer, $ruang, $tanggal, $durasi);
                    } else {
                        $hasil = $this->cekBentrokEdit($nim, $dosen1, $dosen2, $reviewer, $ruang, $tanggal, $durasi);
                    }
                    break;
            }
            if ($hasil != NULL) {
//                    var_dump($hasil);
                $this->session->set_userdata('id', $id);
                $this->session->set_flashdata('bentrok', $hasil);
                redirect('kolokium/edit/' . $this->session->userdata('id'));
            } else {
//                    var_dump($hasil);
                $this->Kolokium_model->editJadwalKolokium();
                $this->session->set_flashdata('flash', 'Diubah');
                redirect('kolokium');
            }
        }
    }

    public function cekBentrokEdit($nim, $dosen1, $dosen2, $reviewer, $ruang, $tanggal, $durasi) {
        $detail = NULL;
        $dataRuang = $this->Kolokium_model->cekStatusRuangEdit($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal kolokium Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['nim'] == $nim) {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $reviewer ||
                        $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $reviewer ||
                        $dr['reviewer'] == $dosen1 || $dr['reviewer'] == $dosen2 || $dr['reviewer'] == $reviewer) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } elseif ($dr['ruang'] != $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            } elseif ($dr['ruang'] != $ruang) {
//                                if ($dr['durasi'] == $durasi) {
//                                    $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
//                                            . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                                    $detail = $detailBentrok;
//                                }
                            }
                        }
                    }
                }
            } else {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $reviewer ||
                        $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $reviewer ||
                        $dr['reviewer'] == $dosen1 || $dr['reviewer'] == $dosen2 || $dr['reviewer'] == $reviewer) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } elseif ($dr['ruang'] != $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            } elseif ($dr['ruang'] != $ruang) {
//                                if ($dr['durasi'] == $durasi) {
//                                    $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
//                                            . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                                    $detail = $detailBentrok;
//                                }
                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrokEdit2($nim, $dosen1, $reviewer, $ruang, $tanggal, $durasi) {
        $detail = NULL;
        $dataRuang = $this->Kolokium_model->cekStatusRuangEdit($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal kolokium Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['nim'] == $nim) {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $reviewer || $dr['reviewer'] == $dosen1 || $dr['reviewer'] == $reviewer) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } elseif ($dr['ruang'] != $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            } elseif ($dr['ruang'] != $ruang) {
//                                if ($dr['durasi'] == $durasi) {
//                                    $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
//                                            . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                                    $detail = $detailBentrok;
//                                }
                            }
                        }
                    }
                }
            } else {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $reviewer || $dr['reviewer'] == $dosen1 || $dr['reviewer'] == $reviewer) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } elseif ($dr['ruang'] != $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
                                        . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            } elseif ($dr['ruang'] != $ruang) {
//                                if ($dr['durasi'] == $durasi) {
//                                    $detailBentrok = $detailBentrok . " dengan NIM " . $dr['nim'] . " dosbing 1 = " . $dr['dosen1'] . " reviewer = "
//                                            . $dr['reviewer'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                                    $detail = $detailBentrok;
//                                }
                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function pdf($id) {
        $this->load->library('dompdf_gen');
        $data['kolokium'] = $this->Kolokium_model->getKolokiumByID($id);
        $mahasiswa = $data['kolokium']['nim'];
        $filename = 'Detail_Jadwal_Kolokium_' . $mahasiswa . '.pdf';
        $this->load->view('kolokium/detail_pdf', $data);

        $paper_size = 'A4';
        $oreintation = 'potrait';
        $html = $this->output->get_output();
        $this->dompdf->set_paper($paper_size, $oreintation);

        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream($filename, array('Attachment' => 0));
    }

    public function undangan($id) {
        $this->load->library('dompdf_gen');
        $data['kolokium'] = $this->Kolokium_model->getKolokiumByID($id);
        $mahasiswa = $data['kolokium']['nim'];
        $filename = 'Undangan_Kolokium_' . $mahasiswa . '.pdf';
        $this->load->view('kolokium/undangan_pdf', $data);

        $paper_size = 'A4';
        $oreintation = 'potrait';
        $html = $this->output->get_output();
        $this->dompdf->set_paper($paper_size, $oreintation);

        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream($filename, array('Attachment' => 0));
    }

    public function undangantxt($id) {
        $data['kolokium'] = $this->Kolokium_model->getKolokiumByID($id);
        $mahasiswa = $data['kolokium']['nim'];
        $filename = 'Undangan_Kolokium_' . $mahasiswa . '.txt';

        header('Content-type:text/plain');
        header('COntent-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: no-store, no-chace, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Expires:0');

        $handle = fopen('php://output', 'w');

        $data['undangan'] = $this->load->view('kolokium/undangan_txt', $data);
    }

    public function report() {
        if ($this->input->post() == NULL) {
            $data['title'] = 'Report Jadwal Kolokium';
            $data['bulan'] = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',];
            $data['jam'] = ['07.00-08.00', '08.00-09.00', '09.00-10.00', '10.00-11.00', '11.00-12.00', '12.00-13.00', '13.00-14.00', '14.00-15.00', '15.00-16.00', '16.00-17.00'];
            $data['ruang'] = $this->db->get('ruangan')->result_array();
            $data['dosen'] = $this->Dosen_model->getAllDosen();
            $data['kolokium'] = NULL;
            $data['jumlahData'] = 0;
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('kolokium/report', $data);
            $this->load->view('templates/footer');
        } else {
            $data['title'] = 'Report Jadwal Kolokium';
            $data['bulan'] = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',];
            $data['jam'] = ['07.00-08.00', '08.00-09.00', '09.00-10.00', '10.00-11.00', '11.00-12.00', '12.00-13.00', '13.00-14.00', '14.00-15.00', '15.00-16.00', '16.00-17.00'];
            $data['ruang'] = $this->db->get('ruangan')->result_array();
            $data['dosen'] = $this->Dosen_model->getAllDosen();

            $postData = $this->input->post();

            $statement = '';
            if ($postData['bulan'] != '' && $statement == '') {
                $statement = $statement . " tanggal LIKE '%" . $postData['bulan'] . "%'";
            } elseif ($postData['bulan'] != '' && $statement != '') {
                $statement = $statement . " AND tanggal LIKE '%" . $postData['bulan'] . "%'";
            }
            if ($postData['dosen1'] != '' && $statement == '') {
                $statement = $statement . " dosen1 = '" . $postData['dosen1'] . "'";
            } elseif ($postData['dosen1'] != '' && $statement != '') {
                $statement = $statement . " AND dosen1 = '" . $postData['dosen1'] . "'";
            }
            if ($postData['dosen2'] != '' && $statement == '') {
                $statement = $statement . " dosen2='" . $postData['dosen2'] . "'";
            } elseif ($postData['dosen2'] != '' && $statement != '') {
                $statement = $statement . " AND dosen2='" . $postData['dosen2'] . "'";
            }
            if ($postData['reviewer'] != '' && $statement == '') {
                $statement = $statement . " reviewer = '" . $postData['reviewer'] . "'";
            } elseif ($postData['reviewer'] != '' && $statement != '') {
                $statement = $statement . " AND reviewer = '" . $postData['reviewer'] . "'";
            }
            if ($postData['jam'] != '' && $statement == '') {
                $statement = $statement . " durasi = '" . $postData['jam'] . "'";
            } elseif ($postData['jam'] != '' && $statement != '') {
                $statement = $statement . " AND durasi = '" . $postData['jam'] . "'";
            }
            if ($postData['ruang'] != '' && $statement == '') {
                $statement = $statement . " ruang = '" . $postData['ruang'] . "'";
            } elseif ($postData['ruang'] != '' && $statement != '') {
                $statement = $statement . " AND ruang = '" . $postData['ruang'] . "'";
            }

            $this->session->set_userdata('statement', $statement);
            $data['statement'] = $statement;
            $data['kolokium'] = $this->Kolokium_model->getKolokiumReport($statement);
            $data['jumlahData'] = $this->Kolokium_model->getJumlahReport($statement);
            if ($data['kolokium'] == NULL) {
                $this->session->set_flashdata('reportKolokium', 'Data Mahasiwa Tidak Ada');
                redirect('kolokium/report');
            }
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('kolokium/report', $data);
            $this->load->view('templates/footer');
        }
    }

    public function excel() {
        $statement = $this->session->userdata('statement');
        $data['mahasiswa'] = $this->Kolokium_model->getKolokiumReport($statement);
        require (APPPATH . 'PHPExcel-1.8/Classes/PHPExcel.php');
        require (APPPATH . 'PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php');

        $object = new PHPExcel;
        $object->getProperties()->setCreator("Informatika");
        $object->getProperties()->setLastModifiedBy("Informatika");
        $object->getProperties()->setTitle("Jadwal Kolokium");

        $object->setActiveSheetIndex(0);

        $object->getActiveSheet()->setCellValue('A1', 'NO');
        $object->getActiveSheet()->setCellValue('B1', 'NIM');
        $object->getActiveSheet()->setCellValue('C1', 'NAMA');
        $object->getActiveSheet()->setCellValue('D1', 'DOSEN PEMBIMBING 1');
        $object->getActiveSheet()->setCellValue('E1', 'DOSEN PEMBIMBING 2');
        $object->getActiveSheet()->setCellValue('F1', 'JUDUL TUGAS AKHIR');
        $object->getActiveSheet()->setCellValue('G1', 'REVIEWER');
        $object->getActiveSheet()->setCellValue('H1', 'TANGGAL');
        $object->getActiveSheet()->setCellValue('I1', 'JAM');
        $object->getActiveSheet()->setCellValue('J1', 'RUANGAN');
        $object->getActiveSheet()->setCellValue('K1', 'KETERANGAN');

        $baris = 2;
        $no = 1;

        foreach ($data['mahasiswa'] as $mhs) {
            $object->getActiveSheet()->setCellValue('A' . $baris, $no++);
            $object->getActiveSheet()->setCellValue('B' . $baris, $mhs['nim']);
            $object->getActiveSheet()->setCellValue('C' . $baris, $mhs['nama']);
            $object->getActiveSheet()->setCellValue('D' . $baris, $mhs['dosen1']);
            $object->getActiveSheet()->setCellValue('E' . $baris, $mhs['dosen2']);
            $object->getActiveSheet()->setCellValue('F' . $baris, $mhs['judul']);
            $object->getActiveSheet()->setCellValue('G' . $baris, $mhs['reviewer']);
            $object->getActiveSheet()->setCellValue('H' . $baris, $mhs['tanggal']);
            $object->getActiveSheet()->setCellValue('I' . $baris, $mhs['durasi']);
            $object->getActiveSheet()->setCellValue('J' . $baris, $mhs['ruang']);
            $object->getActiveSheet()->setCellValue('K' . $baris, $mhs['keterangan']);

            $baris++;
        }

        $filename = 'Jadwal_Kolokium_' . date("d-m-Y") . '.xlsx';
        $object->getActiveSheet()->setTitle("Jadwal Kolokium");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createwriter($object, 'Excel2007');
        $writer->save('php://output');
    }

}

<?php

class Pendadaran extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Pendadaran_model');
        $this->load->model('Kolokium_model');
        $this->load->model('Dosen_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['judul'] = "Jadwal Pendadaran";

        $this->load->model('Pendadaran_model', 'pendadaran');

        $this->load->library('pagination');

        if ($this->input->post('submit')) {
            $data['keyword'] = $this->input->post('keyword');
            $this->session->set_userdata('keyword', $data['keyword']);
        } else {
            $data['keyword'] = $this->session->userdata('keyword');
        }

        $this->db->like('nama', $data['keyword']);
        $this->db->or_like('nim', $data['keyword']);
        $this->db->from('pendadaran');

        $config['base_url'] = 'http://localhost/informatika/pendadaran/index';
        $config['total_rows'] = $this->db->count_all_results();
        $data['total_rows'] = $config['total_rows'];
        $config['per_page'] = 10;

        $this->pagination->initialize($config);

        $data['start'] = $this->uri->segment(3);
        $data['pendadaran'] = $this->pendadaran->GetPendadaran($config['per_page'], $data['start'], $data['keyword']);

        $this->load->view('templates/header', $data);
        $this->load->view('pendadaran/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah($nim) {
        if ($this->Pendadaran_model->cekStatusPendadaran($nim) == NULL) {
            $data['judul'] = "Tambah Jadwal Pendadaran";
            $data['jam'] = ['07.00-09.00', '09.00-11.00', '11.00-13.00', '13.00-15.00', '15.00-17.00'];
            $data['ruang'] = $this->db->get('ruangan')->result_array();
            $data['dosen'] = $this->Dosen_model->getAllDosen();
            $data['mahasiswa'] = $this->Kolokium_model->getKolokiumByNIM($nim);

            $this->form_validation->set_rules('nama', 'Nama Mahasiswa', 'required');
            $this->form_validation->set_rules('nim', 'NIM Mahasiswa', 'required|numeric');
            $this->form_validation->set_rules('dosen1', 'Dosen Pembimbing 1', 'required');
            $this->form_validation->set_rules('dosen2', 'Dosen Pembimbing 2');
            $this->form_validation->set_rules('judul', 'Judul Tugas Akhir', 'required');
            $this->form_validation->set_rules('reviewer', 'Reviewer', 'required');
            $this->form_validation->set_rules('ketuaPenguji', 'Ketua Penguji', 'required');
            $this->form_validation->set_rules('sekretarisPenguji', 'Sekretaris Penguji', 'required');
            $this->form_validation->set_rules('anggotaPenguji', 'Anggota Penguji');
            $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('templates/header', $data);
                $this->load->view('pendadaran/tambah', $data);
                $this->load->view('templates/footer');
            } else {
                $postData = $this->input->post();
                $dosen1 = $postData['dosen1'];
                $dosen2 = $postData['dosen2'];
                $reviewer = $postData['reviewer'];
                $ketuaPenguji = $postData['ketuaPenguji'];
                $sekretarisPenguji = $postData['sekretarisPenguji'];
                $anggotaPenguji = $postData['anggotaPenguji'];
                $ruang = $postData['ruang'];
                $tanggal = format_indo($postData['tanggal']);
                $durasi = $postData['durasi'];
                $cekInputDosen = $this->cekInputDosen($reviewer, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji);

                switch ($cekInputDosen) {
                    case 0:
                        $this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rks', 'Reviewer tidak bisa menjadi Ketua Penguji dan Sekretaris Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 1:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rka', 'Reviewer tidak bisa menjadi Ketua Penguji dan Anggota Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 2:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rksa', 'Reviewer tidak bisa menjadi Ketua Penguji, Sekretaris Penguji, dan Anggota Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 3:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rks', 'Reviewer tidak bisa menjadi Ketua Penguji dan Sekretaris Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 4:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rsa', 'Reviewer tidak bisa menjadi Sekretaris Penguji dan Anggota Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 5:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rksa', 'Reviewer tidak bisa menjadi Ketua Penguji, Sekretaris Penguji, dan Anggota Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 6:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rka', 'Reviewer tidak bisa menjadi Ketua Penguji dan Anggota Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 7:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rka', 'Reviewer tidak bisa menjadi Sekretaris Penguji dan Anggota Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    case 8:$this->session->set_userdata('nim', $nim);
                        $this->session->set_flashdata('rksa', 'Reviewer tidak bisa menjadi Ketua Penguji, Sekretaris Penguji, dan Anggota Penguji disaat bersama');
                        redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                        break;
                    default:
                        if ($dosen2 == '' && $anggotaPenguji == '') {
                            $hasil = $this->cekBentrok2($dosen1, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi);
                        } elseif ($dosen2 != '' && $anggotaPenguji == '') {
                            $hasil = $this->cekBentrok($dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi);
                        } elseif ($dosen2 == '' && $anggotaPenguji != '') {
                            $hasil = $this->cekBentrok3($dosen1, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi);
                        } elseif ($dosen2 != '' && $anggotaPenguji != '') {
                            $hasil = $this->cekBentrok4($dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi);
                        }

                        break;
                }
                if ($hasil != NULL) {
//                    var_dump($hasil);
                    $this->session->set_userdata('nim', $nim);
                    $this->session->set_flashdata('bentrok', $hasil);
                    redirect('pendadaran/tambah/' . $this->session->userdata('nim'));
                } else {
//                    var_dump($hasil);
                    $this->Pendadaran_model->tambahJadwalPendadaran();
                    $this->session->set_flashdata('flash', 'Ditambahkan');
                    redirect('pendadaran');
                }
            }
        } else {
            $this->session->set_flashdata('terdaftar', 'Mahasiswa telah terdaftar untuk Pendadaran');
            redirect('pendadaran');
        }
    }

    public function cekBentrok($dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuang($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji ||
                    $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $ketuaPenguji || $dr['dosen2'] == $sekretarisPenguji ||
                    $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $dosen2 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji ||
                    $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $dosen2 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji) {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } else {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                            $detail = $detailBentrok;
                        }
                    }
                }
            } else {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } else {
//                        if ($dr['durasi'] == $durasi) {
//                            $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                            $detail = $detailBentrok;
//                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrok2($dosen1, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuang($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji ||
                    $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji ||
                    $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji) {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } else {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                            $detail = $detailBentrok;
                        }
                    }
                }
            } else {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } else {
//                        if ($dr['durasi'] == $durasi) {
//                            $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
//                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                            $detail = $detailBentrok;
//                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrok3($dosen1, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuang($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji || $dr['dosen1'] == $anggotaPenguji ||
                    $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji || $dr['ketuaPenguji'] == $anggotaPenguji ||
                    $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji || $dr['sekretarisPenguji'] == $anggotaPenguji ||
                    $dr['anggotaPenguji'] == $dosen1 || $dr['anggotaPenguji'] == $ketuaPenguji || $dr['anggotaPenguji'] == $sekretarisPenguji || $dr['anggotaPenguji'] == $anggotaPenguji) {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } else {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
                            $detail = $detailBentrok;
                        }
                    }
                }
            } else {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } else {
//                        if ($dr['durasi'] == $durasi) {
//                            $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
//                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " di ruang " . $dr['ruang'] . "";
//                            $detail = $detailBentrok;
//                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrok4($dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuang($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji || $dr['dosen1'] == $anggotaPenguji ||
                    $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $ketuaPenguji || $dr['dosen2'] == $sekretarisPenguji || $dr['dosen2'] == $anggotaPenguji ||
                    $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $dosen2 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji || $dr['ketuaPenguji'] == $anggotaPenguji ||
                    $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $dosen2 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji || $dr['sekretarisPenguji'] == $anggotaPenguji ||
                    $dr['anggotaPenguji'] == $dosen1 || $dr['anggotaPenguji'] == $dosen2 || $dr['anggotaPenguji'] == $ketuaPenguji || $dr['anggotaPenguji'] == $sekretarisPenguji || $dr['anggotaPenguji'] == $anggotaPenguji) {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        } else {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        }
                    } elseif ($dr['ruang'] != $ruang) {
//                        if ($dr['durasi'] == $durasi) {
//                            $detailBentrok = $detailBentrok . " bentrok dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " ruang = " . $dr['ruang'] . "";
//                            $detail = $detailBentrok;
//                        } else {
//                            $detailBentrok = $detailBentrok . " bentrok dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " ruang = " . $dr['ruang'] . "";
//                            $detail = $detailBentrok;
//                        }
                    }
                }
            } else {
                if ($dr['tanggal'] == $tanggal) {
                    if ($dr['ruang'] == $ruang) {
                        if ($dr['durasi'] == $durasi) {
                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                            $detail = $detailBentrok;
                        } else {
//                            $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
//                            $detail = $detailBentrok;
                        }
                    } elseif ($dr['ruang'] != $ruang) {
//                        if ($dr['durasi'] == $durasi) {
//                            $detailBentrok = $detailBentrok . " bentrok dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                    . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " ruang = " . $dr['ruang'] . "";
//                            $detail = $detailBentrok;
//                        } 
                    }
                }
            }
        }
        return $detail;
    }

    public function cekInputDosen($reviewer, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji) {
        if ($reviewer == $ketuaPenguji) {
            if ($reviewer == $sekretarisPenguji && $reviewer == $anggotaPenguji) {
                return 0;
            } elseif ($reviewer == $anggotaPenguji) {
                return 1;
            } elseif ($reviewer == $sekretarisPenguji) {
                return 2;
            } else {
                return 9;
            }
        } elseif ($reviewer == $sekretarisPenguji) {
            if ($reviewer == $ketuaPenguji && $reviewer == $anggotaPenguji) {
                return 3;
            } elseif ($reviewer == $anggotaPenguji) {
                return 4;
            } elseif ($reviewer == $ketuaPenguji) {
                return 5;
            } else {
                return 9;
            }
        } elseif ($reviewer == $anggotaPenguji) {
            if ($reviewer == $ketuaPenguji && $reviewer == $sekretarisPenguji) {
                return 6;
            } elseif ($reviewer == $sekretarisPenguji) {
                return 7;
            } elseif ($reviewer == $ketuaPenguji) {
                return 8;
            } else {
                return 9;
            }
        }
    }

    public function inputNim() {
        $data['judul'] = "Tambah Jadwal Pendadaran";
        $data['form'] = 'Form Tambah Jadwal Pendadaran';
        $this->form_validation->set_rules('nim', 'NIM Mahasiswa', 'required|numeric');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/inputNim', $data);
            $this->load->view('templates/footer');
        } else {
            $postData = $this->input->post();
            $nim = $postData['nim'];
            if ($this->Kolokium_model->getKolokiumByNIM($nim) != null) {
                $this->tambah($nim);
            } else {
                $this->session->set_flashdata('PendadarantidakAda', 'Mahasiswa belum melakukan Kolokium');
                redirect('pendadaran/inputNim');
            }
        }
    }

    public function hapus($id) {
        $this->Pendadaran_model->hapusJadwalPendadaran($id);
        $this->session->set_flashdata('flash', 'Dihapus');
        redirect('pendadaran');
    }

    public function detail($id) {
        $data['judul'] = 'Detail Jadwal Pendadaran';
        $data['pendadaran'] = $this->Pendadaran_model->getPendadaranByID($id);


        $this->load->view('templates/header', $data);
        $this->load->view('pendadaran/detail', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id) {
        $data['judul'] = "Edit Jadwal Pendadaran";
        $data['jam'] = ['07.00-09.00', '09.00-11.00', '11.00-13.00', '13.00-15.00', '15.00-17.00'];
        $data['ruang'] = $this->db->get('ruangan')->result_array();
        $data['pendadaran'] = $this->Pendadaran_model->getPendadaranByID($id);
        $data['dosen'] = $this->Dosen_model->getAllDosen();
        $data['pendadaran']['tanggal'] = format_back($data['pendadaran']['tanggal']);

        $this->form_validation->set_rules('nama', 'Nama Mahasiswa', 'required');
        $this->form_validation->set_rules('nim', 'NIM Mahasiswa', 'required|numeric');
        $this->form_validation->set_rules('dosen1', 'Dosen Pembimbing 1', 'required');
        $this->form_validation->set_rules('dosen2', 'osen Pembimbing 1');
        $this->form_validation->set_rules('judul', 'Judul Tugas Akhir', 'required');
        $this->form_validation->set_rules('reviewer', 'Reviewer', 'required');
        $this->form_validation->set_rules('ketuaPenguji', 'Ketua Penguji', 'required');
        $this->form_validation->set_rules('sekretarisPenguji', 'Sekretaris Penguji', 'required');
        $this->form_validation->set_rules('anggotaPenguji', 'Anggota Penguji');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');


        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('pendadaran/edit', $data);
            $this->load->view('templates/footer');
        } else {

            $postData = $this->input->post();
            $nim = $postData['nim'];
            $dosen1 = $postData['dosen1'];
            $dosen2 = $postData['dosen2'];
            $reviewer = $postData['reviewer'];
            $ketuaPenguji = $postData['ketuaPenguji'];
            $sekretarisPenguji = $postData['sekretarisPenguji'];
            $anggotaPenguji = $postData['anggotaPenguji'];
            $tanggal = format_indo($postData['tanggal']);
            $durasi = $postData['durasi'];
            $ruang = $postData['ruang'];
            $cekInputDosen = $this->cekInputDosen($reviewer, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji);

            switch ($cekInputDosen) {
                case 0:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rks', 'Reviewer tidak bisa menjadi Ketua Penguji dan Sekretaris Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 1:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rka', 'Reviewer tidak bisa menjadi Ketua Penguji dan Anggota Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 2:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rksa', 'Reviewer tidak bisa menjadi Ketua Penguji, Sekretaris Penguji, dan Anggota Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 3:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rks', 'Reviewer tidak bisa menjadi Ketua Penguji dan Sekretaris Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 4:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rsa', 'Reviewer tidak bisa menjadi Sekretaris Penguji dan Anggota Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 5:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rksa', 'Reviewer tidak bisa menjadi Ketua Penguji, Sekretaris Penguji, dan Anggota Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 6:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rka', 'Reviewer tidak bisa menjadi Ketua Penguji dan Anggota Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 7:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rka', 'Reviewer tidak bisa menjadi Sekretaris Penguji dan Anggota Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                case 8:
                    $this->session->set_userdata('id', $id);
                    $this->session->set_flashdata('rksa', 'Reviewer tidak bisa menjadi Ketua Penguji, Sekretaris Penguji, dan Anggota Penguji disaat bersama');
                    redirect('pendadaran/edit/' . $this->session->userdata('id'));
                    break;
                default:
                    if ($dosen2 == '' && $anggotaPenguji == '') {
                        $hasil = $this->cekBentrokEdit2($nim, $dosen1, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi);
                    } elseif ($dosen2 != '' && $anggotaPenguji == '') {
                        $hasil = $this->cekBentrokEdit($nim, $dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi);
                    } elseif ($dosen2 == '' && $anggotaPenguji != '') {
                        $hasil = $this->cekBentrokEdit3($nim, $dosen1, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi);
                    } elseif ($dosen2 != '' && $anggotaPenguji != '') {
                        $hasil = $this->cekBentrokEdit4($nim, $dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi);
                    }

                    break;
            }
            if ($hasil != NULL) {
                $this->session->set_userdata('id', $id);
                $this->session->set_flashdata('bentrok', $hasil);
                redirect('pendadaran/edit/' . $this->session->userdata('id'));
            } else {
                $this->Pendadaran_model->editJadwalPendadaran();
                $this->session->set_flashdata('flash', 'Diubah');
                redirect('pendadaran');
            }
        }
    }

    public function cekBentrokEdit($nim, $dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuangEdit($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['nim'] == $nim) {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji ||
                        $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $ketuaPenguji || $dr['dosen2'] == $sekretarisPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $dosen2 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $dosen2 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            } else {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji ||
                        $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $ketuaPenguji || $dr['dosen2'] == $sekretarisPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $dosen2 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $dosen2 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrok2Edit($nim, $dosen1, $ketuaPenguji, $sekretarisPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuangEdit($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['nim'] == $nim) {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            } else {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrokEdit3($nim, $dosen1, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuangEdit($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['nim'] == $nim) {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji || $dr['dosen1'] == $anggotaPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji || $dr['ketuaPenguji'] == $anggotaPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji || $dr['sekretarisPenguji'] == $anggotaPenguji ||
                        $dr['anggotaPenguji'] == $dosen1 || $dr['anggotaPenguji'] == $ketuaPenguji || $dr['anggotaPenguji'] == $sekretarisPenguji || $dr['anggotaPenguji'] == $anggotaPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            } else {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji || $dr['dosen1'] == $anggotaPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji || $dr['ketuaPenguji'] == $anggotaPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji || $dr['sekretarisPenguji'] == $anggotaPenguji ||
                        $dr['anggotaPenguji'] == $dosen1 || $dr['anggotaPenguji'] == $ketuaPenguji || $dr['anggotaPenguji'] == $sekretarisPenguji || $dr['anggotaPenguji'] == $anggotaPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['jam'] . " di ruang " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function cekBentrokEdit4($nim, $dosen1, $dosen2, $ketuaPenguji, $sekretarisPenguji, $anggotaPenguji, $ruang, $tanggal, $durasi) {//ruang masih belum bisa dipakai
        $detail = NULL;
        $dataRuang = $this->Pendadaran_model->cekStatusRuangEdit($tanggal, $durasi);
        $detailBentrok = "Ada bentrok jadwal pendadaran Mahasiswa";
        foreach ($dataRuang as $dr) {
            if ($dr['nim'] == $nim) {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji || $dr['dosen1'] == $anggotaPenguji ||
                        $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $ketuaPenguji || $dr['dosen2'] == $sekretarisPenguji || $dr['dosen2'] == $anggotaPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $dosen2 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji || $dr['ketuaPenguji'] == $anggotaPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $dosen2 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji || $dr['sekretarisPenguji'] == $anggotaPenguji ||
                        $dr['anggotaPenguji'] == $dosen1 || $dr['anggotaPenguji'] == $dosen2 || $dr['anggotaPenguji'] == $ketuaPenguji || $dr['anggotaPenguji'] == $sekretarisPenguji || $dr['anggotaPenguji'] == $anggotaPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " bentrok dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " ruang = " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " bentrok dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " ruang = " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            } else {
                if ($dr['dosen1'] == $dosen1 || $dr['dosen1'] == $dosen2 || $dr['dosen1'] == $ketuaPenguji || $dr['dosen1'] == $sekretarisPenguji || $dr['dosen1'] == $anggotaPenguji ||
                        $dr['dosen2'] == $dosen1 || $dr['dosen2'] == $dosen2 || $dr['dosen2'] == $ketuaPenguji || $dr['dosen2'] == $sekretarisPenguji || $dr['dosen2'] == $anggotaPenguji ||
                        $dr['ketuaPenguji'] == $dosen1 || $dr['ketuaPenguji'] == $dosen2 || $dr['ketuaPenguji'] == $ketuaPenguji || $dr['ketuaPenguji'] == $sekretarisPenguji || $dr['ketuaPenguji'] == $anggotaPenguji ||
                        $dr['sekretarisPenguji'] == $dosen1 || $dr['sekretarisPenguji'] == $dosen2 || $dr['sekretarisPenguji'] == $ketuaPenguji || $dr['sekretarisPenguji'] == $sekretarisPenguji || $dr['sekretarisPenguji'] == $anggotaPenguji ||
                        $dr['anggotaPenguji'] == $dosen1 || $dr['anggotaPenguji'] == $dosen2 || $dr['anggotaPenguji'] == $ketuaPenguji || $dr['anggotaPenguji'] == $sekretarisPenguji || $dr['anggotaPenguji'] == $anggotaPenguji) {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " bentrok dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " ruang = " . $dr['ruang'] . "";
                                $detail = $detailBentrok;
                            }
                        }
                    }
                } else {
                    if ($dr['tanggal'] == $tanggal) {
                        if ($dr['ruang'] == $ruang) {
                            if ($dr['durasi'] == $durasi) {
                                $detailBentrok = $detailBentrok . " karena " . $dr['ruang'] . " dipakai oleh NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . "";
                                $detail = $detailBentrok;
                            }
                        } else {
//                            if ($dr['durasi'] == $durasi) {
//                                $detailBentrok = $detailBentrok . " bentrok dengan NIM =" . $dr['nim'] . " dosbing1 = " . $dr['dosen1'] . " dosbing2 = " . $dr['dosen2'] . " Ketua Penguji = "
//                                        . $dr['ketuaPenguji'] . " Sekrterais Penguji = " . $dr['sekretarisPenguji'] . " Anggota Penguji = " . $dr['anggotaPenguji'] . " pada tanggal " . $dr['tanggal'] . " Jam = " . $dr['durasi'] . " ruang = " . $dr['ruang'] . "";
//                                $detail = $detailBentrok;
//                            }
                        }
                    }
                }
            }
        }
        return $detail;
    }

    public function pdf($id) {
        $this->load->library('dompdf_gen');
        $data['pendadaran'] = $this->Pendadaran_model->getPendadaranById($id);
        $mahasiswa = $data['pendadaran']['nim'];
        $filename = 'Detail_Jadwal_Pendadaran_' . $mahasiswa . '.pdf';
        $this->load->view('pendadaran/detail_pdf', $data);
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
        $data['pendadaran'] = $this->Pendadaran_model->getPendadaranById($id);
        $mahasiswa = $data['pendadaran']['nim'];
        $filename = 'Undangan_Pendadaran_' . $mahasiswa . '.pdf';
        $this->load->view('pendadaran/undangan_pdf', $data);

        $paper_size = 'A4';
        $oreintation = 'potrait';
        $html = $this->output->get_output();
        $this->dompdf->set_paper($paper_size, $oreintation);
        $this->dompdf->load_html($html);
        $this->dompdf->render();
        $this->dompdf->stream($filename, array('Attachment' => 0));
    }

    public function undangantxt($id) {
        $data['pendadaran'] = $this->Pendadaran_model->getPendadaranByID($id);
        $mahasiswa = $data['pendadaran']['nim'];
        $filename = 'Undangan_Pendadaran_' . $mahasiswa . '.txt';

        header('Content-type:text/plain');
        header('COntent-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: no-store, no-chace, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Expires:0');

        $handle = fopen('php://output', 'w');

        $data['undangan'] = $this->load->view('pendadaran/undangan_txt', $data);
    }

    public function report() {
        if ($this->input->post() == NULL) {
            $data['judul'] = 'Report Jadwal Pendadaran';
            $data['bulan'] = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',];
            $data['jam'] = ['07.00-09.00', '09.00-11.00', '11.00-13.00', '13.00-15.00', '15.00-17.00'];
            $data['ruang'] = $this->db->get('ruangan')->result_array();
            $data['dosen'] = $this->Dosen_model->getAllDosen();
            $data['pendadaran'] = NULL;
            $data['jumlahData'] = 0;
            $this->load->view('templates/header', $data);
            $this->load->view('pendadaran/report', $data);
            $this->load->view('templates/footer');
        } else {
            $data['judul'] = 'Report Jadwal Pendadaran';
            $data['bulan'] = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',];
            $data['jam'] = ['07.00-09.00', '09.00-11.00', '11.00-13.00', '13.00-15.00', '15.00-17.00'];
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
            if ($postData['ketuaPenguji'] != '' && $statement == '') {
                $statement = $statement . " ketuaPenguji = '" . $postData['ketuaPenguji'] . "'";
            } elseif ($postData['ketuaPenguji'] != '' && $statement != '') {
                $statement = $statement . " AND ketuaPenguji = '" . $postData['ketuaPenguji'] . "'";
            }
            if ($postData['sekretarisPenguji'] != '' && $statement == '') {
                $statement = $statement . " sekretarisPenguji = '" . $postData['sekretarisPenguji'] . "'";
            } elseif ($postData['sekretarisPenguji'] != '' && $statement != '') {
                $statement = $statement . " AND sekretarisPenguji = '" . $postData['sekretarisPenguji'] . "'";
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
            $data['pendadaran'] = $this->Pendadaran_model->getPendadaranReport($statement);
            $data['jumlahData'] = $this->Pendadaran_model->getJumlahReport($statement);
            if ($data['pendadaran'] == NULL) {
                $this->session->set_flashdata('reportPendadaran', 'Data Mahasiwa Tidak Ada');
                redirect('pendadaran/report');
            }
            $this->load->view('templates/header', $data);
            $this->load->view('pendadaran/report', $data);
            $this->load->view('templates/footer');
        }
    }

    public function excel() {
        $statement = $this->session->userdata('statement');
        $data['mahasiswa'] = $this->Pendadaran_model->getPendadaranReport($statement);
        require (APPPATH . 'PHPExcel-1.8/Classes/PHPExcel.php');
        require (APPPATH . 'PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php');

        $object = new PHPExcel;
        $object->getProperties()->setCreator("Informatika");
        $object->getProperties()->setLastModifiedBy("Informatika");
        $object->getProperties()->setTitle("Jadwal Pendadaran");

        $object->setActiveSheetIndex(0);

        $object->getActiveSheet()->setCellValue('A1', 'NO');
        $object->getActiveSheet()->setCellValue('B1', 'NIM');
        $object->getActiveSheet()->setCellValue('C1', 'NAMA');
        $object->getActiveSheet()->setCellValue('D1', 'DOSEN PEMBIMBING 1');
        $object->getActiveSheet()->setCellValue('E1', 'DOSEN PEMBIMBING 2');
        $object->getActiveSheet()->setCellValue('F1', 'JUDUL TUGAS AKHIR');
        $object->getActiveSheet()->setCellValue('G1', 'REVIEWER');
        $object->getActiveSheet()->setCellValue('H1', 'KETUA PENGUJI');
        $object->getActiveSheet()->setCellValue('I1', 'SEKRETARIS PENGUJI');
        $object->getActiveSheet()->setCellValue('J1', 'TANGGAL');
        $object->getActiveSheet()->setCellValue('K1', 'JAM');
        $object->getActiveSheet()->setCellValue('L1', 'RUANGAN');
        $object->getActiveSheet()->setCellValue('M1', 'KETERANGAN');

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
            $object->getActiveSheet()->setCellValue('H' . $baris, $mhs['ketuaPenguji']);
            $object->getActiveSheet()->setCellValue('I' . $baris, $mhs['sekretarisPenguji']);
            $object->getActiveSheet()->setCellValue('J' . $baris, $mhs['tanggal']);
            $object->getActiveSheet()->setCellValue('K' . $baris, $mhs['durasi']);
            $object->getActiveSheet()->setCellValue('L' . $baris, $mhs['ruang']);
            $object->getActiveSheet()->setCellValue('M' . $baris, $mhs['keterangan']);

            $baris++;
        }

        $filename = 'Jadwal_Pendadaran_' . date("d-m-Y") . '.xlsx';
        $object->getActiveSheet()->setTitle("Jadwal Pendadaran");

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer = PHPExcel_IOFactory::createwriter($object, 'Excel2007');
        $writer->save('php://output');
    }

}

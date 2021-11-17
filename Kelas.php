<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('id_user')) {
            redirect('authentication');
        }
        $this->API = 'http://localhost/project_sia_api/index.php/api/';
    }

    public function index() {
        if ($this->session->userdata('level') == 'Siswa') {
            $id_siswa = $this->session->userdata('profile')->id_siswa;
            $data['kelas'] = json_decode($this->curl->simple_get($this->API . '/kelas/siswa?id_siswa=' . $id_siswa));
        } else if ($this->session->userdata('level') == 'Pengajar') {
            $id_pengajar = $this->session->userdata('profile')->id_pengajar;
            $data['kelas'] = json_decode($this->curl->simple_get($this->API . '/kelas/pengajar?id_pengajar=' . $id_pengajar));
        } else {
            $data['kelas'] = json_decode($this->curl->simple_get($this->API . '/kelas'));
        }

//        print_r($data);
//        die();
        $data['konten'] = 'kelas/kelas';
        $this->load->view('template/template', $data);
    }

    function form($id_kelas = 0) {
        if ($id_kelas == 0) {
            //add
            $obj = new stdClass();
            $obj->id_kelas = $id_kelas;
            $obj->nama_kelas = '';
            $obj->id_tahun_ajaran = 0;
            $obj->id_pengajar = 0;
        } else {
//            edit
            $obj = json_decode($this->curl->simple_get($this->API . '/kelas?id=' . $id_kelas));
        }
        $data['data'] = $obj;
        $data['tahun_ajaran'] = json_decode($this->curl->simple_get($this->API . '/tahun_ajaran'));
        $data['pengajar'] = json_decode($this->curl->simple_get($this->API . '/pengajar'));

        $data['konten'] = 'kelas/form';
        $this->load->view('template/template', $data);
    }

    function save() {
        $data = array(
            'id_kelas' => $this->input->post('id_kelas'),
            'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
            'id_pengajar' => $this->input->post('id_pengajar'),
            'nama_kelas' => $this->input->post('nama_kelas'));
        if ($data['id_kelas'] == 0) {
            $save = $this->curl->simple_post($this->API . '/kelas', $data, array(CURLOPT_BUFFERSIZE => 10));
        } else {
            $save = $this->curl->simple_put($this->API . '/kelas', $data, array(CURLOPT_BUFFERSIZE => 10));
        }
        redirect('kelas');
    }

    public function detail($id_kelas = 0) {
        $data['kelas_siswa'] = json_decode($this->curl->simple_get($this->API . '/kelas_siswa?id_kelas=' . $id_kelas));
        $data['kelas'] = json_decode($this->curl->simple_get($this->API . '/kelas?id=' . $id_kelas));
//        $data['siswa'] = $this->Msiswa->get_all();
//        echo '<pre>';
//        print_r($data);
//        die();
        $data['konten'] = 'kelas/detail_kelas';
        $this->load->view('template/template', $data);
    }

    function form_kelas_siswa($id_kelas = 0, $id_kelas_siswa = 0) {
        if ($id_kelas_siswa == 0) {
            //add
            $obj = new stdClass();
            $obj->id_kelas_siswa = $id_kelas_siswa;
            $obj->id_siswa = 0;
            $obj->id_kelas = $id_kelas;
        } else {
//            edit
            $obj = json_decode($this->curl->simple_get($this->API . '/kelas_siswa?id=' . $id_kelas_siswa));
        }
        $data['data'] = $obj;

        $data['siswa'] = json_decode($this->curl->simple_get($this->API . '/siswa'));
        $data['kelas'] = json_decode($this->curl->simple_get($this->API . '/kelas'));

        $data['konten'] = 'kelas/form_kelas_siswa';
        $this->load->view('template/template', $data);
    }

    function save_siswa() {
        $id_kelas = $this->input->post('id_kelas');
        $obj = json_decode($this->curl->simple_get($this->API . '/kelas?id=' . $id_kelas));
        $id_tahun_ajaran = $obj->id_tahun_ajaran;

//        cek di th tersebut
        $id_siswa = $this->input->post('id_siswa');
        $cek = json_decode($this->curl->simple_get($this->API . '/kelas_siswa/tahun?id_siswa=' . $id_siswa . '&id_tahun_ajaran=' . $id_tahun_ajaran));

        if ($cek) {
            redirect('kelas/detail/' . $id_kelas);
        }

        $data = array(
            'id_kelas_siswa' => $this->input->post('id_kelas_siswa'),
            'id_kelas' => $this->input->post('id_kelas'),
            'id_siswa' => $this->input->post('id_siswa')
        );
        if ($data['id_kelas_siswa'] == 0) {
            $save = $this->curl->simple_post($this->API . '/kelas_siswa', $data, array(CURLOPT_BUFFERSIZE => 10));
        } else {
            $save = $this->curl->simple_put($this->API . '/kelas_siswa', $data, array(CURLOPT_BUFFERSIZE => 10));
        }
        redirect('kelas/detail/' . $data['id_kelas']);
    }

    function delete_siswa($id = 0, $id_kelas_siwa = 0) {

        $data = array(
            'id' => $id_kelas_siwa);
        $save = $this->curl->simple_delete($this->API . '/kelas_siswa', $data, array(CURLOPT_BUFFERSIZE => 10));
        redirect('kelas/detail/' . $id);
    }

    public function penilaian($id_kelas = 0, $id_kelas_siswa = 0) {
        //handling filter combo
        if ($this->input->get('semester')) {
            $semester = $this->input->get('semester');
            //sample api filter
            $data['nilai_siswa'] = json_decode($this->curl->simple_get($this->API . '/nilai_siswa?id_kelas_siswa=' . $id_kelas_siswa . '&semester=' . $semester));
        } else {
            $data['nilai_siswa'] = array();
            $semester = '';
        }
//        $data['siswa'] = $this->Mnilai->get_all();
        $data['siswa'] = json_decode($this->curl->simple_get($this->API . '/kelas_siswa?id=' . $id_kelas_siswa));

//        $data['siswa'] = json_decode($this->curl->simple_get($this->API . '/pelajaran'));

        $data['semester'] = $semester;

        if ($this->session->userdata('level') == 'Siswa') {
            $data['konten'] = 'kelas/nilai';
        } else {
            $data['nilai'] = json_decode($this->curl->simple_get($this->API . '/nilai'));
            $data['konten'] = 'kelas/nilai_input';
        }

//                        echo '<pre>';
//        print_r($data);
//        die();
        $this->load->view('template/template', $data);
    }

    function save_nilai() {
//        echo '<pre>';
//        print_r($this->input->post());
//        die;
        $master_nilai = json_decode($this->curl->simple_get($this->API . '/nilai'));


        $id_siswa = $this->input->post('id_siswa');
        $id_kelas = $this->input->post('id_kelas');
        $id_kelas_siswa = $this->input->post('id_kelas_siswa');

        $nilai = $this->input->post('nilai_aktual');

        foreach ($nilai as $id_jadwal => $nilai_aktual) {
//            if ($nilai_aktual != NULL) {
//                
//            }
            foreach ($master_nilai as $key2 => $value2) {

                if ($value2->range_nilai_awal <= $nilai_aktual && $nilai_aktual <= $value2->range_nilai_akhir) {
                    $id_nilai = $value2->id_nilai;
                }
            }
  
//            die;
            //del
            $data = array(
                'id_jadwal' => $id_jadwal,
                'id_siswa' => $id_siswa
            );

            $save = $this->curl->simple_delete($this->API . '/nilai_siswa', $data, array(CURLOPT_BUFFERSIZE => 10));

            //post
            if ($nilai_aktual) {
                $data = array(
                    'id_jadwal' => $id_jadwal,
                    'id_nilai' => $id_nilai,
                    'id_siswa' => $id_siswa,
                    'nilai_aktual' => $nilai_aktual
                );

                $save = $this->curl->simple_post($this->API . '/nilai_siswa', $data, array(CURLOPT_BUFFERSIZE => 10));
            }
        }

        redirect('kelas/detail/' . $id_kelas);
    }

    function delete($id = 0) {

        $data = array(
            'id' => $id);
        $save = $this->curl->simple_delete($this->API . '/kelas', $data, array(CURLOPT_BUFFERSIZE => 10));
        redirect('kelas');
    }

}

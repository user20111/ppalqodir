<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Nilai_siswa extends REST_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();

        $this->load->model('mnilaisiswa', 'nilaisiswa');
    }

    public function index_get() {


        $id = $this->get('id');
        $id_kelas_siswa = $this->get('id_kelas_siswa');
        $semester = $this->get('semester');
        // If the id parameter doesn't exist return all the goldar

        if ($id === NULL && $id_kelas_siswa === NULL && $semester == NULL) {


            $data = $this->nilaisiswa->all();

            if ($data) {
                // Set the response and exit
                $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No Data were found'
                        ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        } else if ($id_kelas_siswa && $semester) {
//benerin
            $data = $this->nilaisiswa->by_kelas_siswa($id_kelas_siswa, $semester);
//            echo '<pre>';
//            print_r($data);
//            die;
            if (!empty($data)) {
                $this->set_response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Data could not be found'
                        ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        } else {

            $data = $this->nilaisiswa->by_id($id);

            if (!empty($data)) {
                $this->set_response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } else {
                $this->set_response([
                    'status' => FALSE,
                    'message' => 'Data could not be found'
                        ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
    }

    public function index_post() {
        $data = array(
            'id_siswa' => $this->post('id_siswa'),
            'id_jadwal' => $this->post('id_jadwal'),
            'id_nilai' => $this->post('id_nilai'),
            'nilai_aktual' => $this->post('nilai_aktual'));

        $save = $this->nilaisiswa->save_data($data, 0);

        if ($save) {
            $this->response($save, 200);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }

    public function index_put() {
        if (!empty($this->put('id_nilaisiswa'))) {
            $data = array(
                'id_siswa' => $this->put('id_siswa'),
                'id_jadwal' => $this->put('id_jadwal'),
                'id_nilai' => $this->put('id_nilai'));

            $save = $this->nilaisiswa->save_data($data, $this->put('id_nilaisiswa'));

            if ($save) {
                $this->response($save, 200);
            } else {
                $this->response(array('status' => 'fail', 502));
            }
        } else {
            $this->response("Provide Data.", REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    function index_delete() {
        $id_siswa = $this->delete('id_siswa');
        $id_jadwal = $this->delete('id_jadwal');


        $return = $this->nilaisiswa->delete($id_siswa, $id_jadwal);


        if ($return) {
            $this->response(array('status' => TRUE), 201);
        } else {
            $this->response(array('status' => 'fail', 502));
        }
    }

}

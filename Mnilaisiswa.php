<?php

class Mnilaisiswa extends CI_Model {

    public function all() {
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran', 'LEFT');
        $this->db->join('pengajar', 'pengajar.id_pengajar = kelas.id_pengajar', 'LEFT');
        return $this->db->get('nilai')->result();
    }

    public function by_id($id) {
        $this->db->where('id_nilai', $id);
        return $this->db->get('nilai')->row();
    }

    public function by_kelas_siswa($id, $semester) {
        
        $sql = "SELECT * FROM jadwal "
                . " JOIN semester ON semester.id_semester =jadwal.id_semester "
                . " JOIN (SELECT id_tahun_ajaran,kelas_siswa.id_kelas_siswa, kelas_siswa.id_siswa FROM kelas_siswa JOIN kelas ON kelas.id_kelas = kelas_siswa.id_kelas WHERe kelas_siswa.id_kelas_siswa = $id) as kls ON kls.id_tahun_ajaran = semester.id_tahun_ajaran "
                . "LEFT JOIN nilai_siswa ON nilai_siswa.id_siswa = kls.id_siswa AND nilai_siswa.id_jadwal = jadwal.id_jadwal "
                . "LEFT JOIN mata_pelajaran ON mata_pelajaran.id_mapel = jadwal.id_mapel "
                . "LEFT JOIN nilai ON nilai.id_nilai = nilai_siswa.id_nilai "
                . "WHERE semester.semester = '$semester' "
                . "ORDER BY mata_pelajaran.nama_mapel ASC";
        $query = $this->db->query($sql);
        
        return $query->result();
    }

    function insert($data) {
        if ($this->db->insert('nilai_siswa', $data)) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    function update($data, $id) {

        $this->db->where('id_nilai', $id);
        if ($this->db->update('nilai_siswa', $data)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function delete($id_siswa, $id_jadwal) {
        if ($this->db->delete('nilai_siswa', array('id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal))) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function save_data($data, $id = 0) {
        if ($id != 0) {
            return $this->update($data, $id);
        } else {
            return $this->insert($data);
        }
    }

}

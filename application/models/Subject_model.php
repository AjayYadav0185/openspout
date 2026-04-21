<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subject_model extends CI_Model
{

    public function get_all()
    {
        return $this->db->get('subjects')->result();
    }

    public function get($id)
    {
        return $this->db->get('subjects')->result();
    }

    public function insert($data)
    {
        return $this->db->get('subjects')->result();
    }

    public function update($id, $data)
    {
        return $this->db->get('subjects')->result();
    }

    public function delete($id)
    {
        return $this->db->get('subjects')->result();
    }
}

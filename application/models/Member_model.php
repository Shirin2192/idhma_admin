<?php
class Member_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

    public function get_membership_planes() {
        $this->db->select('tbl_membership_types.*, tbl_currency.code, tbl_currency.symbol, tbl_membership_categories.category_name');
        $this->db->from('tbl_membership_types');
        $this->db->join('tbl_currency', 'tbl_currency.id = tbl_membership_types.fk_currency_id', 'left');
        $this->db->join('tbl_membership_categories', 'tbl_membership_categories.id = tbl_membership_types.fk_category_id', 'left'); 
        $this->db->where('tbl_membership_types.is_delete', '1');
        $this->db->order_by('tbl_membership_types.id', 'desc');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_membership_plans_by_id($id) {
        $this->db->select('tbl_membership_types.*, tbl_currency.code, tbl_currency.symbol, tbl_membership_categories.category_name');
        $this->db->from('tbl_membership_types');
        $this->db->join('tbl_currency', 'tbl_currency.id = tbl_membership_types.fk_currency_id', 'left');
        $this->db->join('tbl_membership_categories', 'tbl_membership_categories.id = tbl_membership_types.fk_category_id', 'left'); 
        $this->db->where('tbl_membership_types.is_delete', '1');
        $this->db->where('tbl_membership_types.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }
     public function get_email_recipients() {
        $this->db->select('email');
        $this->db->from('tbl_members');
        $this->db->where('email IS NOT NULL');
        return $this->db->get()->result_array();
    }

    public function get_whatsapp_recipients() {
        $this->db->select('phone');
        $this->db->from('tbl_members');
        $this->db->where('phone IS NOT NULL');
        return $this->db->get()->result_array();
    }
}

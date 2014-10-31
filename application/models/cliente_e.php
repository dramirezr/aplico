<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cliente_e extends CI_Model {

	function __construct(){
		parent::__construct();
	}
	
	function create($data){
		
		if(!$this->db->insert('cliente_e', $data))
			return false;
		
		return $this->db->insert_id(); 
	}

	function get_by_id($id){
		$result = $this->db->get_where('cliente_e', array('id' => $id))->result();
		if(!count($result))
			return null;
		return $result[0];		
	}


	function update($id, $data){
		return $this->db->update('cliente_e', $data, array('id' => $id));
	}

		
}
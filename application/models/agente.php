<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agente extends CI_Model {

	function __construct(){
		parent::__construct();
	}
	
	function create($data){
				
		if(!$this->db->insert('agente', $data))
			return false;
		
		return $this->db->insert_id(); 
	}

	function get_by_id($id){
		$agente = $this->db->get_where('agente', array('id' => $id))->result();
		if(!count($agente))
			return null;
		return $agente[0];		
	}

	function update($id, $data){
		return $this->db->update('agente', $data, array('id' => $id));
	}
		
}
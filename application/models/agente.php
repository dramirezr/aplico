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
	
	function get_for_login($code, $pass){
		
		$pass = md5($pass);
		
		$agente = $this->db->get_where('agente', array('codigo' => $code, 'clave' => $pass))->result();
		if(!count($agente))
			return null;
		return $agente[0];		
	}
	
	function get_nearest_request($id){
		
		//TODO: OJO hacer aca el SQL para cargar la solicitud pendiente mas cercana.
		$sql = "SELECT * FROM solicitud WHERE estado = 'P' AND idagente IS NULL LIMIT 1";
		$sol = $this->db->query($sql)->result();
		
		//Esto no debe cambiar
		if(!count($sol))
			return null;
		
		return $sol[0];
	}
	
	function confirm_request($id, $request_id){
		
		$this->db->where("id = $request_id AND idagente IS NULL");
		$this->db->update('solicitud', array('idagente' => $id));
		
		return ($this->db->affected_rows() > 0);
	}
}
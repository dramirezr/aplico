<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cliente_loc extends CI_Model {

	function __construct(){
		parent::__construct();
	}
	
	function create($data){
				
		if(!$this->db->insert('cliente_loc', $data))
			return false;
		
		return $this->db->insert_id(); 
	}

	function get_by_id($id){
		$usuarios = $this->db->get_where('cliente_loc', array('id' => $id))->result();
		if(!count($usuarios))
			return null;
		return $usuarios[0];		
	}


	function update($id, $data){
		return $this->db->update('cliente_loc', $data, array('id' => $id));
	}

	function get_by_telefono($telefono){
		
		$sql = 	" SELECT c.nombre, cl.* ";
		$sql .= " FROM cliente_loc cl, cliente c";
 		$sql .= " WHERE ((cl.telefono='$telefono')or(cl.celular='$telefono')) and cl.idcliente=c.id ";
		$result = $this->db->query($sql)->result();
		if(!count($result))
			return null;
		return $result;		
	}

	

	
}
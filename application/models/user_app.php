<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_app extends CI_Model {

	function __construct(){
		parent::__construct();
	}
	
	function create($data){
		if(!$this->db->insert('user_app', $data))
			return false;
		
		return $this->db->insert_id(); 
	}

	function get_by_id($id){
		$usuarios = $this->db->get_where('user_app', array('id' => $id))->result();
		if(!count($usuarios))
			return null;

		return $usuarios[0];		
	}


	function update($id, $data){
		return $this->db->update('user_app', $data, array('id' => $id));
	}
	
	function get_by_uuid($uuid){
		$user = $this->db->get_where('user_app', array('uuid' => $uuid))->result();
		if(!count($user))
			return null;

		return $user[0];	
	}
	
	
}
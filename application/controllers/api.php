<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	
	public function call(){
		
		$this->load->model('solicitud');
		
		
		$data['ubicacion'] = $this->input->post('address', TRUE);
		$data['latitud'] = $this->input->post('lat', TRUE);
		$data['longitud'] = $this->input->post('lng', TRUE);
		
		$queryId = $this->solicitud->create($data);
		
		
		die(json_encode(array('state' => 'ok', 'queryId' => $queryId)) );
	}
	
	function verify_call(){
		
		$this->lang->load('dashboard');
		
		$attempt = $this->input->get('atttempt');
		$queryId = $this->input->get('queryId');
		
		$attempts = $this->session->userdata('verification_attps') ? $this->session->userdata('verification_attps') : 0;
		$attempts ++;
		
		$this->session->set_userdata('verification_attps', $attempts);
		
		if($attempts > ci_config('max_verification_attemps')){
			$this->session->unset_userdata('verification_attps');
			die(json_encode(array('state' => 'error', 'msg' => lang('dashboard.error.attempts'))) );
			
		}
		
		//TODO: Validar si ya ha sido asinado el agente
		
		die(json_encode(array('state' => 0, 'queryId' => $queryId)) );
	}
	
}
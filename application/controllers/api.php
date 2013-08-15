<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	
	public function call(){
		
		$this->load->model('solicitud');
		
		
		$data['ubicacion'] = $this->input->get_post('address');
		$data['latitud'] = $this->input->get_post('lat');
		$data['longitud'] = $this->input->get_post('lng');
		$data['sector'] = $this->input->get_post('zone');
		$data['ciudad'] = $this->input->get_post('city');
		$data['pais'] = $this->input->get_post('country');
		$data['departamento'] = $this->input->get_post('state_c');
				
		//$data['idagente'] = 1;
		
		$queryId = $this->solicitud->create($data);
		
		
		die(json_encode(array('state' => 'ok', 'queryId' => $queryId)) );
	}
	
	function verify_service_status(){
		$this->load->model('solicitud');
		$this->lang->load('dashboard');

		$queryId = $this->input->get_post('queryId');

		$inquiry = $this->solicitud->get_by_id($queryId);
		
		if($inquiry->estado == 'E'){
			die(json_encode(array('state' => 'delivered',  'msg' => lang('dashboard.error.canceled_service'))));
		}

		if($inquiry->estado == 'C'){
			die(json_encode(array('state' => 'error', 'msg' => lang('dashboard.error.canceled_service'))));
		}
		
		if($inquiry->agente_arribo == 1){
			$this->solicitud->update($queryId, array('agente_arribo' => 0));
			die(json_encode(array('state' => 'arrival')));
		}
		
		die(json_encode(array('state' => 1)));
	}
	
	function verify_call(){
		$this->load->model('solicitud');
		$this->load->model('agente');
		
		$this->lang->load('dashboard');
		
		$attempt = $this->input->get('atttempt');
		$queryId = $this->input->get('queryId');
		
		$attempts = $this->session->userdata('verification_attps') ? $this->session->userdata('verification_attps') : 0;
		$attempts ++;
		
		$this->session->set_userdata('verification_attps', $attempts);
		
		if($attempts > ci_config('max_verification_attemps')){
			$this->session->unset_userdata('verification_attps');
			//cancel the request
			$this->solicitud->update($queryId, array('estado' => 'C'));
			
			die(json_encode(array('state' => 'error', 'msg' => lang('dashboard.error.attempts'))) );	
		}
		
		//TODO: Validar si ya ha sido asignado el agente
		$inquiry = $this->solicitud->get_by_id($queryId);
		if($inquiry->idagente && $inquiry->estado == 'P'){
			$agente = $this->agente->get_by_id($inquiry->idagente);
			$data['foto'] = base_url().'assets/images/agents/'.$agente->foto;
			$data['nombre'] = $agente->nombre;
			$data['codigo'] = $agente->codigo;
			$data['telefono'] = $agente->telefono;
			$data['codigo2'] = $agente->codigo2;
			$data['id'] = $inquiry->idagente;			
			
			$this->agent_accept();
			
			die(json_encode(array('state' => 1, 'queryId' => $queryId, 'agent' => $data)) );
		}
		
		die(json_encode(array('state' => 0, 'queryId' => $queryId)) );
	}
	
	function agent_accept(){
		$this->load->model('solicitud');
		$queryId = $this->input->get('queryId');
		$this->solicitud->update($queryId, array('estado' => 'A'));
	}

	function request_cancel(){
		$this->load->model('solicitud');
		$queryId = $this->input->get('queryId');
		$this->solicitud->update($queryId, array('estado' => 'C'));
	}
	
	function scode(){
		if($this->input->is_ajax_request()){
			$scode = md5(uniqid());
			$this->session->set_userdata('scode', $scode);
			die(json_encode(array('state' => 'ok', 'code' => $scode)));
//			die(json_encode(array('state' => 'ok', 'code' => $this->security->get_csrf_hash())));
		}else{
			die(json_encode(array('state' => 'error')));	
		}
	}
	
	function login(){
		
		$username = $this->input->get_post('username'); 
		$password = $this->input->get_post('password');
		 
		$this->load->model('agente');
		$this->lang->load('dashboard');
		
		if(!$agente = $this->agente->get_for_login($username, $password)){
			die(json_encode(array('state' => 'error', 'msg' => lang('login.error.noauth'))));
		}
		 
		//arranca el agente en estado del servicio libre y estado pendiente
		$idagente = $agente->id;
		$this->agente->update($idagente, array('estado_servicio' => 'LIBRE','estado' => 'P'));

		//Create the session
		$agente->clave = NULL;
		$this->session->set_userdata('agente', $agente);
		
		$session_data = $this->session->all_userdata();
		
		die(json_encode(array('state' => 'ok', 'data' => $agente)));
		
	}
	
	function get_taxi_location(){
		$this->load->model('agente');
		$agent_id = $this->input->get_post('agent_id');
		$agente = $this->agente->get_by_id($agent_id);
		
		die(json_encode(array('state' => 'ok', 'lat' => $agente->latitud, 'lng' => $agente->longitud)));
	}
	
}
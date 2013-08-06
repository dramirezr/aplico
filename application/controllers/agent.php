<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Agent extends CI_Controller {

	var $title = 'Login';
	var $error = NULL;
	var $enterprise = NULL;
	var $agent = NULL;
	
	function __construct(){
		parent::__construct();
		
		if(!$this->agent = $this->session->userdata('agente')){
			redirect($user->lang.'/login'); 
		}
		
		// load language file
		$this->lang->load('dashboard');
		
		$this->load->model('agente');
			
	}
	
	public function index(){
		$this->load_view();
	}
	
	private function load_view(){
		
		$this->load->view('private/agent', array(
					'title' => $this->title,
					'error' => $this->error
					));
	}
	
	function update_location(){
		$lat = $this->input->get_post('lat');
		$lng = $this->input->get_post('lng');
		$id = $this->input->get_post('id');
		
		$id = $id ? $id : $this->agent->id;
		
		$this->agente->update($id, array('latitud' => $lat, 'longitud' => $lng));
		die(json_encode(array('state' => 'ok')));
	}
	
	function switch_to_busy(){
		$id = $this->input->get_post('id');
		
		$id = $id ? $id : $this->agent->id;
		
		$this->agente->update($id, array('estado_servicio' => 'OCUPADO'));
		die(json_encode(array('state' => 'ok')));
				
	}

	function switch_to_free(){
		$id = $this->input->get_post('id');	
		$id = $id ? $id : $this->agent->id;
		
		$this->agente->update($id, array('estado_servicio' => 'LIBRE'));
		die(json_encode(array('state' => 'ok')));
				
	}
	
	function delivered_service(){
		$request_id = $this->input->get_post('request_id');
		
		$this->load->model('solicitud');
		
		$this->solicitud->update($request_id, array('estado' => 'E'));
		
		die(json_encode(array('state' => 'ok')));
	}
	
	function get_service(){
		
		$id = $this->input->get_post('id');	
		$id = $id ? $id : $this->agent->id;

		$request = $this->agente->get_nearest_request($id);
		
		if(!$request){
			die(json_encode(array('state' => '')));
		}
		
		$response = array(
			'state' => 'ok',
			'ubicacion' => $request->ubicacion,
			'ubicacion_corta' => $request->ubicacion,
			'latitud' => $request->latitud,
			'longitud' => $request->longitud,
			'request' => $request->id
		);
		
		$this->agente->update($id, array('estado_servicio' => 'OCUPADO'));
		
		die(json_encode($response));
	}
	
	function confirm(){
		
		$request_id = $this->input->get_post('request_id');		
		$id = $this->input->get_post('id');	
		$id = $id ? $id : $this->agent->id;
		
		if($this->agente->confirm_request($id, $request_id)){
			die(json_encode(array('state' => 'ok')));
		}else{
			die(json_encode(array('state' => '')));
		}
	}
	
} 
 
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
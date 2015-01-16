<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	
	public function call(){
		$this->load->model('solicitud');
				
		$data['ubicacion'] 		= $this->input->get_post('address');
		$data['latitud'] 		= $this->input->get_post('lat');
		$data['longitud'] 		= $this->input->get_post('lng');
		$data['sector'] 		= $this->input->get_post('zone');
		$data['ciudad'] 		= $this->input->get_post('city');
		$data['pais'] 			= $this->input->get_post('country');
		$data['departamento'] 	= $this->input->get_post('state_c');
		$data['nombre'] 		= $this->input->get_post('name');
		$data['telefono'] 		= $this->input->get_post('phone');
		$data['celular'] 		= $this->input->get_post('cell');
		$data['medio'] 			= $this->input->get_post('average');
		$data['uuid'] 			= $this->input->get_post('uuid');
		$data['idcall'] 		= $this->input->get_post('idcall');

				
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
			die(json_encode(array('state' => 'arrival', 'msg' => lang('dashboard.error.arrival_service'))));
		}
		
		die(json_encode(array('state' => 1,  'arribo' =>$inquiry->id)));
	}

	function updateStatusArribo(){
		$this->load->model('solicitud');
		$queryId = $this->input->get_post('queryId');
		$this->solicitud->update($queryId, array('agente_arribo' => 0));
		die(json_encode(array('state' => 'ok')));
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
		//$nom=$inquiry->nombre;

		if($inquiry->idagente && $inquiry->estado == 'P'){

			$agente = $this->agente->get_by_id($inquiry->idagente);
			$data['foto'] = base_url().'assets/images/agents/'.$agente->foto;
			$data['nombre'] = $agente->nombre;
			$data['codigo'] = $agente->codigo;
			$data['telefono'] = $agente->telefono;
			$data['codigo2'] = $agente->codigo2;
			$data['id'] = $inquiry->idagente;			
			$this->load->model('vehiculos');
			$vehiculo = $this->vehiculos->get_by_id($agente->vehiculo);
			$data['placa'] = $vehiculo->placa;
			$data['unidad'] = $vehiculo->unidad;
			$sol = $this->solicitud->get_by_id($queryId);
			$data['direccion'] = $sol->ubicacion;
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
	
	function agent_init(){
		if($this->input->is_ajax_request()){
			$scode = md5(uniqid());
			$this->session->set_userdata('scode', $scode);
			$data = array(
				'state' => 'ok',
				'code' => $scode,
				'verification_interval' => ci_config('agent_verification_interval'),
				'updatelocation_interval' => ci_config('agent_updatelocation_interval'),
				'message_interval' => ci_config('agent_message_inverval'),
				'app_name' => ci_config('app_name'),
				'app_icon' => ci_config('app_icon'),
				'copyright' => ci_config('copyright')
			);
			
			die(json_encode($data));
//			die(json_encode(array('state' => 'ok', 'code' => $this->security->get_csrf_hash())));
		}else{
			die(json_encode(array('state' => 'error')));	
		}
	}
	
	function login(){
		
		$username = $this->input->get_post('username'); 
		$password = $this->input->get_post('password');
		 
		$this->load->model('agente');
		$this->load->model('vehiculos');
		$this->lang->load('dashboard');
		
		if(!$agente = $this->agente->get_for_login($username, $password)){
			die(json_encode(array('state' => 'error', 'msg' => lang('login.error.noauth'))));
		}
		 
		//arranca el agente en estado del servicio libre y estado pendiente
		$idagente = $agente->id;
		$this->agente->update($idagente, array('estado_servicio' => 'LIBRE','estado' => 'P'));

		//Create the session
		$vehiculo = $this->vehiculos->get_by_id($agente->vehiculo);
		$agente->clave = NULL;
		$agente->placa = $vehiculo->placa;
		$agente->unidad = $vehiculo->unidad;
		
		$this->session->set_userdata('agente', $agente);
		
		$session_data = $this->session->all_userdata();
		
		die(json_encode(array('state' => 'ok', 'data' => $agente)));
		
	}

	function relogin(){
		
		$username = $this->input->get_post('username'); 
		$password = $this->input->get_post('password');
		 
		$this->load->model('agente');
		$this->load->model('vehiculos');
		$this->lang->load('dashboard');
		
		if(!$agente = $this->agente->get_for_login($username, $password)){
			die(json_encode(array('state' => 'error', 'msg' => lang('login.error.noauth'))));
		}
		 
		//arranca el agente en estado del servicio libre y estado pendiente
		$idagente = $agente->id;
		$this->agente->update($idagente, array('estado_servicio' => 'LIBRE','estado' => 'P'));

		//Create the session
		$vehiculo = $this->vehiculos->get_by_id($agente->vehiculo);
		$agente->clave = NULL;
		$agente->placa = $vehiculo->placa;
		$agente->unidad = $vehiculo->unidad;
		
		$this->session->set_userdata('agente', $agente);
		
		$session_data = $this->session->all_userdata();
		
		die(json_encode(array('state' => 'ok')));
		
	}
	
	function get_taxi_location(){
		$this->load->model('agente');
		$agent_id = $this->input->get_post('agent_id');
		$agente = $this->agente->get_by_id($agent_id);
		
		die(json_encode(array('state' => 'ok', 'lat' => $agente->latitud, 'lng' => $agente->longitud)));
	}

	function get_agets_location(){
		$office = $this->input->get_post('office'); 
		$unit = $this->input->get_post('unit');
		$plate = $this->input->get_post('plate');
		$agent = $this->input->get_post('agent');

		$this->load->model('agente');
		$userconfig = $this->session->userdata('userconfig');
		$cust_id = $userconfig->id;
		$cust_perfil = $userconfig->perfil;
		$cust_idsucursal = $userconfig->idsucursal;
		$agente = $this->agente->get_by_cust_id($cust_id,$cust_perfil,$cust_idsucursal,$office,$unit,$plate,$agent);
		
		die(json_encode(array('state' => 'ok','agent' => $agente)));
	}


	function get_cust_location(){
		$telefono = $this->input->get_post('phone');
		$this->load->model('cliente_loc');
		$result = $this->cliente_loc->get_by_telefono($telefono);
		
		die(json_encode(array('state' => 'ok', 'result' => $result )));
	}

	function saveClientLoc(){
		$this->load->model('cliente_loc');

		//echo "<pre>";
		//print_r($_REQUEST);
		//echo "</pre>";
		$idcliente				= $this->input->get_post('idclient');
		$id  					= $this->input->get_post('id');
		$data['telefono'] 		= $this->input->get_post('phone');
		$data['celular'] 		= $this->input->get_post('cell');		
		$data['direccion'] 		= $this->input->get_post('address');
		$data['latitud'] 		= $this->input->get_post('lat');
		$data['longitud'] 		= $this->input->get_post('lng');
		$data['sector'] 		= $this->input->get_post('zone');
		$data['ciudad'] 		= $this->input->get_post('city');
		$data['pais'] 			= $this->input->get_post('country');
		$data['departamento'] 	= $this->input->get_post('state_c');
		
		$this->load->model('cliente');
		$dataE['nombre'] 		= $this->input->get_post('name');
		if($idcliente=='-1'){
			$dataE['idcliente_e']	= '-1';
			$idcliente 				= $this->cliente->create($dataE);			
		}else{
			$this->cliente->update($idcliente,$dataE);			
		}

		$data['idcliente']	= $idcliente;
		$this->load->model('cliente_loc');
		if($id=='-1'){
			$idlocalizacion = $this->cliente_loc->create($data);			
		}else{
			$this->cliente_loc->update($id,$data);	
			$idlocalizacion =	$id;	
		}

		die(json_encode(array('state' => 'ok', 'idlocation' => $idlocalizacion, 'idclient' => $idcliente)) );
		
	}

	function deleteClientLoc(){
		$this->load->model('cliente_loc');

		$id = $this->input->get_post('id');
		$this->cliente_loc->delete($id);	

		die(json_encode(array('state' => 'ok')) );
		
	}

	function get_user_app(){
		$uuid = $this->input->get_post('uuid');

		$this->load->model('user_app');
		$user_app = $this->user_app->get_by_uuid($uuid);
		if(!count($user_app)){
			$data['uuid'] 			= $uuid;
			$data['nombre'] 		= '';
			$data['telefono'] 		= '';
			$data['email'] 			= '';
			$data['modelo'] 		= $this->input->get_post('model');
			$data['plataforma'] 	= $this->input->get_post('platform');
			$data['version'] 		= $this->input->get_post('version');
			$data['fecha'] 			= date("Y-m-d H:i:s");
			$data['fecha_log']		= date("Y-m-d H:i:s");
			$data['tyc'] 			= 'N';
			$data['id']				= $this->user_app->create($data);

			die(json_encode(array('state' => 'ok', 'user' => $data)) );
		}
		
		$this->user_app->update($user_app->id, array('fecha_log' => date("Y-m-d H:i:s")));
		
		die(json_encode(array('state' => 'ok', 'user' => $user_app)) );

	}


	function insert_user_app(){
		$this->load->model('user_app');
		$data['uuid'] 			= $this->input->get_post('uuid');
		$data['nombre'] 		= $this->input->get_post('name');
		$data['telefono'] 		= $this->input->get_post('phone');
		$data['email'] 			= $this->input->get_post('email');
		$data['modelo'] 		= $this->input->get_post('model');
		$data['plataforma'] 	= $this->input->get_post('platform');
		$data['version'] 		= $this->input->get_post('version');
		$data['fecha'] 			= date("Y-m-d H:i:s");
		$id 					= $this->user_app->create($data);
	
		die(json_encode(array('state' => 'ok', 'id' => $id)) );
		
	}

	function update_user_app(){
		$this->load->model('user_app');
		$id 					= $this->input->get_post('id');
		$data['uuid'] 			= $this->input->get_post('uuid');
		$data['nombre'] 		= $this->input->get_post('name');
		$data['telefono'] 		= $this->input->get_post('phone');
		$data['email'] 			= $this->input->get_post('email');
		$data['modelo'] 		= $this->input->get_post('model');
		$data['plataforma'] 	= $this->input->get_post('platform');
		$data['version'] 		= $this->input->get_post('version');
		$data['tyc'] 			= 'S';
		//$data['fecha'] 			= date("Y-m-d H:i:s");
		
		$this->user_app->update($id,$data);
	
		die(json_encode(array('state' => 'ok')) );
		
	}


	function get_tyc(){
		$this->load->model('sqlexteded');
		$tyc = $this->sqlexteded->getConfiguracion('terminos');
		die(json_encode(array('state' => 'ok', 'result' => $tyc)) );

	}

	function save_user_app(){
		$this->load->model('user_app');

		$id 					= $this->input->get_post('id');
		$data['uuid'] 			= $this->input->get_post('uuid');
		$data['nombre'] 		= $this->input->get_post('name',true);
		$data['telefono'] 		= $this->input->get_post('phone',true);
		$data['email'] 			= $this->input->get_post('email',true);
		//$data['nombre'] 		= $this->db->escape(trim($this->input->get_post('name')));
		//$data['telefono'] 		= $this->db->escape(trim($this->input->get_post('phone')));
		//$data['email'] 			= $this->db->escape(trim($this->input->get_post('email')));
		//$data['modelo'] 		= $this->input->get_post('model');
		//$data['plataforma'] 	= $this->input->get_post('platform');
		//$data['version'] 		= $this->input->get_post('version');
		$data['tyc'] 			= 'S';
		$data['fecha'] 			= date("Y-m-d H:i:s");
		
		$this->user_app->update($id,$data);
	
		die(json_encode(array('state' => 'ok')) );

	}

	function get_banner(){
		$this->load->model('sqlexteded');
		$banner = $this->sqlexteded->getBanner();
		if(!$banner)
			die(json_encode(array('state' => 'no')) );	
		else
			die(json_encode(array('state' => 'ok', 'result' => $banner)) );
			

	}

	function select_sucursales(){
		$this->load->model('sqlexteded');
		$userconfig 		= $this->session->userdata('userconfig');
		$cust_id 			= $userconfig->id;
		$cust_perfil 		= $userconfig->perfil;
		$cust_idsucursal 	= $userconfig->idsucursal;
		$result = $this->sqlexteded->get_all_sucursal($cust_perfil,$cust_idsucursal);
				
		die(json_encode(array('state' => 'ok', 'result' => $result)));
	}

	function get_all_units(){
		$idsucursal 			= $this->input->get_post('idsucursal');
		$this->load->model('sqlexteded');
		$userconfig 		= $this->session->userdata('userconfig');
		$cust_id 			= $userconfig->id;
		$cust_perfil 		= $userconfig->perfil;
		$cust_idsucursal 	= $userconfig->idsucursal;
		$result = $this->sqlexteded->get_all_units($cust_perfil,$cust_idsucursal,$idsucursal);
				
		die(json_encode(array('state' => 'ok', 'result' => $result)));
	}

	function send_sms(){
		
		$idagent = $this->input->get_post('idagent');
		
		$this->load->model('sqlexteded');
		$agente  = $this->sqlexteded->get_agent_id($idagent);
		$mensaje1 = 'Gracias por utilizar el sevicio de taxis JJ, en '.
					$this->input->get_post('time').' la unidad '.$agente->unidad.
					' de placa '.$agente->placa.
					' y conductor '.$agente->nombre.
					' con movil '.$agente->telefono.' lo estará recogiendo.';
					
		$mensaje2 = 'Solicitud de seevicio en '.
					$this->input->get_post('address').', '.
					$this->input->get_post('name').', '.
					$this->input->get_post('phone').', '.
					$this->input->get_post('cell');
		//sms usuario 

		$userconfig 			= $this->session->userdata('userconfig');
		$data['idsucursal'] 	= $userconfig->idsucursal;
		$data['fecha'] 			= date("Y-m-d H:i:s");
		$data['enviado'] 		= 'N';
		$data['descripcion'] 	= $mensaje1;
		$data['telefono'] 		= $this->input->get_post('cell');
		if((($this->input->get_post('destination')=='USER')  or 
			($this->input->get_post('destination')=='ALL') ) and 
			($this->input->get_post('cell')!=''))
			$idsms_u = $this->sqlexteded->create_sms($data);
		else
			$idsms_u = 0;

		//sms taxista 
		$data['descripcion'] 	= $mensaje2;
		$data['telefono'] 		= $agente->telefono;
		if( (($this->input->get_post('destination')=='AGENT') or
			($this->input->get_post('destination')=='ALL') ) and 
			($agente->telefono!=''))
			$idsms_a = $this->sqlexteded->create_sms($data);
		else
			$idsms_a = 0;

		die(json_encode(array('state' => 'ok', 'idu' => $idsms_u, 'ida' => $idsms_a)));

	}



		
}
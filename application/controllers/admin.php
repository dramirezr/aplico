<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
	var $title = 'Login';
	var $error = NULL;
	var $enterprise = NULL;
	var $userconfig = NULL;

	function __construct()
	{
		parent::__construct();
		
		if(!$this->userconfig = $this->session->userdata('userconfig')){
			redirect($user->lang.'login'); 
		}
		
		// load language file
		$this->lang->load('dashboard');
		$this->load->model('usuarios');
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('grocery_CRUD');	
	}
	
	function _admin_output($output = null)
	{
		if($this->userconfig->perfil=='ADMIN')
			$this->load->view('private/admin.php',$output);	
		else
			if($this->userconfig->perfil=='CALL')
				//$this->callService();
				$this->load->view('private/callcenter.php',$output);	
			else
				if($this->userconfig->perfil=='CUST')
					$this->showAgentCust();
	}
	
	
	
	function index()
	{
		if($this->userconfig->perfil=='ADMIN')
			$this->user_management();
		else
			if($this->userconfig->perfil=='CALL')
				$this->_admin_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => '' ));
			else
				if($this->userconfig->perfil=='CUST')
					$this->_admin_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => '' ));
	}	
	
	function encrypt_password_callback($post_array) {

		if(!empty($post_array['clave']))
		{
		    $post_array['clave'] = md5($post_array['clave']);
		}
		else
		{
		    unset($post_array['clave']);
		}
	    return $post_array;

    }  

    function set_password_input_to_empty() {
    	return "<input type='password' name='clave' value='' />";
	}

	function set_user_call() {
    	return "<input type='hidden' name='perfil' value='CALL' />";
	}
	
	function set_user_cust() {
    	return "<input type='hidden' name='perfil' value='CUST' />";
	}

	function user_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('usuarios');
			$crud->set_subject('Usuarios del sistema');
			$crud->columns('nombre','codigo','pais','departamento','ciudad','direccion','telefono');
			$crud->fields('nombre','codigo','clave','pais','departamento','ciudad','direccion','telefono');
			$crud->required_fields('nombre','codigo','pais','departamento','ciudad','direccion','telefono');
			$crud->display_as('codigo', 'Login');
			
			$crud->change_field_type('clave', 'password');
			
        	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
    		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));

    		
    		$crud->callback_before_update(array($this,'encrypt_password_callback'));
    		$crud->callback_before_insert(array($this,'encrypt_password_callback'));

			$crud->where('perfil =', 'ADMIN');
			
			$output = $crud->render();
			$output -> op = 'user_management';
			//$output -> perfil = 'ADMIN';
			
			
			$this->_admin_output($output);
	}



	function user_callcenter()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('usuarios');
			$crud->set_subject('Centro de atención');
			$crud->columns('nombre','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
			$crud->fields('nombre','codigo','clave','pais','departamento','ciudad','direccion','telefono','perfil');
			$crud->required_fields('nombre','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
			$crud->display_as('codigo', 'Login');
			
			$crud->change_field_type('clave', 'password');
			$crud->change_field_type('perfil', 'hidden');
			
        	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
    		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
 			
 			$crud->callback_edit_field('perfil',array($this,'set_user_call'));
    		$crud->callback_add_field('perfil',array($this,'set_user_call'));
 			
    		$crud->callback_before_update(array($this,'encrypt_password_callback'));
    		$crud->callback_before_insert(array($this,'encrypt_password_callback'));

			$crud->where('perfil =','CALL');
			
			$output = $crud->render();
			$output -> op = 'user_management';
			
			
			$this->_admin_output($output);
	}


	function user_managervehicle()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_table('usuarios');
		$crud->set_subject('Dueños de vehiculos');
		$crud->columns('nombre','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
		$crud->fields('nombre','codigo','clave','pais','departamento','ciudad','direccion','telefono','perfil');
		$crud->required_fields('nombre','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
		$crud->display_as('codigo', 'Login');
		
		$crud->change_field_type('clave', 'password');
		$crud->change_field_type('perfil', 'hidden');
		
    	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
			
		$crud->callback_edit_field('perfil',array($this,'set_user_cust'));
		$crud->callback_add_field('perfil',array($this,'set_user_cust'));
			
		$crud->callback_before_update(array($this,'encrypt_password_callback'));
		$crud->callback_before_insert(array($this,'encrypt_password_callback'));

		$crud->where('perfil =', 'CUST');
		$output = $crud->render();
		$output -> op = 'user_management';
		$this->_admin_output($output);
	}

	function vehicle_management()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_table('vehiculos');
		$crud->set_subject('Taxis');
		$crud->columns('placa','modelo','marca','propietario');
		$crud->fields('placa','modelo','marca','propietario');
		$crud->required_fields('placa','propietario');
		$crud->set_relation('propietario', 'usuarios', 'nombre','perfil IN ("CUST")');
		
		$output = $crud->render();
		$output -> op = 'user_management';
		$this->_admin_output($output);
	}



	function agent_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('agente');
			$crud->set_subject('Taxistas');
			$crud->columns('nombre','codigo','vehiculo','pais','departamento','ciudad','direccion','telefono');
			$crud->fields('nombre','codigo','clave','vehiculo','pais','departamento','ciudad','direccion','telefono','foto');
			$crud->required_fields('nombre','codigo','vehiculo','pais','departamento','ciudad','direccion','telefono');
			$crud->display_as('codigo', 'Cedula');
			$crud->display_as('vehiculo', 'Placa');
			$crud->set_field_upload('foto','assets/images/agents');
			$crud->change_field_type('clave', 'password');
			$crud->set_relation('vehiculo', 'vehiculos', 'placa');
			
        	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
    		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
 
    		$crud->callback_before_update(array($this,'encrypt_password_callback'));
    		$crud->callback_before_insert(array($this,'encrypt_password_callback'));
    		
					
			//$crud->where('codigo =', 1);
			$output = $crud->render();
			$output -> op = 'agent_management';

			$this->_admin_output($output);
	}


	function solicitude_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('solicitud');
			$crud->set_subject('Solicitudes');
			$crud->columns('id','ubicacion','pais','departamento','ciudad','fecha_solicitud','estado','idagente');
			$crud->display_as('idagente', 'Taxista');
			$crud->display_as('id', 'Código');
			$crud->set_relation('idagente', 'agente', 'nombre');
			$crud->order_by('id','asc');
			

			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_edit();
			//$crud->where('fecha_solicitud =', 1);
						
			
			$filtro = $this->input->get('fechaini');
			if ($filtro!=''){
				$fi = $this->input->get('fechaini');
				$ff = $this->input->get('fechafin');
			}else{
				$fi = date('Y-m-01 00:00:00');
        		$ultimodia = date("d",(mktime(0,0,0,date('m')+1,1,date('Y'))-1));
        		$ff = date('Y').'-'.date('m').'-'.$ultimodia.' 23:59:59';
			}
		    
		    $where = array('fecha_solicitud >=' => $fi, 'fecha_solicitud <=' => $ff);
		 	$crud->where($where);  
		   
			$output = $crud->render();
			$output -> fechaini = $fi;
			$output -> fechafin = $ff;
			$output -> op = 'solicitude_management';

			$this->_admin_output($output);
			
	}



	function service_agent()
	{

		$crud = new grocery_CRUD();

		$this->db
		->select('codigo,solicitud.idagente,solicitud.departamento,solicitud.ciudad,sum(solicitud.idagente) servicios')
		->group_by('codigo,idagente,departamento,ciudad')
		->order_by('codigo,idagente,departamento,ciudad', 'desc');
		$crud
		->set_table('solicitud')
		->columns('codigo','idagente', 'servicios','departamento','ciudad');
		
		$crud->set_theme('datatables');
		$crud->set_subject('Solicitudes');
		$crud->display_as('idagente', 'Taxista');
		$crud->set_relation('idagente', 'agente', 'nombre');
		//$crud->set_relation('idagente', 'agente', 'codigo');
				

		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_edit();
		//$crud->where('fecha_solicitud =', 1);
					
		
		$filtro = $this->input->get('fechaini');
		if ($filtro!=''){
			$fi = $this->input->get('fechaini');
			$ff = $this->input->get('fechafin');
		}else{
			$fi = date('Y-m-01 00:00:00');
    		$ultimodia = date("d",(mktime(0,0,0,date('m')+1,1,date('Y'))-1));
    		$ff = date('Y').'-'.date('m').'-'.$ultimodia.' 23:59:59';
		}
	    
	    $where = array('idagente >' => 0,'fecha_solicitud >=' => $fi, 'fecha_solicitud <=' => $ff);

	 	$crud->where($where);  
	   
		$output = $crud->render();
		$output -> fechaini = $fi;
		$output -> fechafin = $ff;
		$output -> op = 'service_agent';

		$this->_admin_output($output);
		
	}

	function callService()
	{
		$this->load->view('private/callcenter.php',array('op' => ''));
	}

	
	
	function showAgent()
	{
		$this->load->view('private/callcenter.php',array('op' => '/admin/underConstuction'));
	}
	
	function showAgentCust()
	{
		$this->load->view('private/customer.php',array('op' => '/admin/viewAgent'));
	}
	
	function viewAgent()
	{
		$this->load->view('private/viewAgent',array('op' => '/admin/viewAgent'));
	}
	
	function underConstuction()
	{
		$this->load->view('public/underconstuction',array('op' => ''));
	}


	public function close()
    {
    	//cerrar sesión
    	$this->session->sess_destroy();
    	redirect($user->lang.'/login'); 

    }
	
}
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
			redirect($user->lang.'/login'); 
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
				$this->callService();
	}
	
	
	
	function index()
	{
		if($this->userconfig->perfil=='ADMIN')
			$this->user_management();
		else
			if($this->userconfig->perfil=='CALL')
				//$this->load->view('private/callcenter.php',$output);
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

	function user_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('usuarios');
			$crud->set_subject('Usuarios del sistema');
			$crud->columns('nombre','codigo','perfil','pais','departamento','ciudad','direccion','telefono');
			$crud->fields('nombre','codigo','clave','perfil','pais','departamento','ciudad','direccion','telefono');
			$crud->required_fields('nombre','codigo','perfil','pais','departamento','ciudad','direccion','telefono');
			$crud->display_as('codigo', 'Loguin');
			
			$crud->change_field_type('clave', 'password');
			
        	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
    		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
 
    		$crud->callback_before_update(array($this,'encrypt_password_callback'));

			//$crud->where('codigo =', 1);
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
			$crud->columns('nombre','codigo','codigo2','pais','departamento','ciudad','direccion','telefono');
			$crud->fields('nombre','codigo','clave','codigo2','pais','departamento','ciudad','direccion','telefono','foto');
			$crud->required_fields('nombre','codigo','pais','departamento','ciudad','direccion','telefono');
			$crud->display_as('codigo', 'Loguin');
			$crud->display_as('codigo2', 'Placa');
			$crud->set_field_upload('foto','assets/images/agents');
			$crud->change_field_type('clave', 'password');
			
        	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
    		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
 
    		$crud->callback_before_update(array($this,'encrypt_password_callback'));

					
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
			$crud->columns('ubicacion','pais','departamento','ciudad','fecha_solicitud','estado','idagente');
			$crud->display_as('idagente', 'Taxista');
			$crud->set_relation('idagente', 'agente', 'nombre');
			

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
		->select('solicitud.idagente,solicitud.departamento,solicitud.ciudad,sum(idagente) servicios')
		->group_by('idagente,departamento,ciudad')
		->order_by('idagente,departamento,ciudad', 'desc');
		$crud
		->set_table('solicitud')
		->columns('idagente', 'servicios','departamento','ciudad');
		
		$crud->set_theme('datatables');
		$crud->set_subject('Solicitudes');
		$crud->display_as('idagente', 'Taxista');
		$crud->set_relation('idagente', 'agente', 'nombre');
		

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
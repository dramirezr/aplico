<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		$this->load->helper('url');
		
		$this->load->library('grocery_CRUD');	
	}
	
	function _admin_output($output = null)
	{
		$this->load->view('private/admin.php',$output);	
	}
	
	
	
	function index()
	{
		$this->_admin_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}	
	
	function encrypt_password_callback($post_array) {
           
            
            $post_array['clave'] = md5($post_array['clave']);

            return $post_array;
    }  

	function agent_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('agente');
			$crud->set_subject('Taxistas');
			$crud->columns('nombre','codigo','pais','departamento','ciudad','direccion','telefono');
			$crud->fields('nombre','codigo','clave','pais','departamento','ciudad','direccion','telefono','foto');
			$crud->required_fields('nombre','codigo','clave','pais','departamento','ciudad','direccion','telefono');
			$crud->set_field_upload('foto','assets/images/agents');
			$crud->change_field_type('clave', 'password');
			//$crud->set_rules('clave','Contraseña','required|min_length[8]');
			$crud->callback_before_insert(array($this,'encrypt_password_callback'));
        	$crud->callback_before_update(array($this,'encrypt_password_callback'));

					
			//$crud->where('codigo =', 1);
			$output = $crud->render();

			$this->_admin_output($output);
	}


	function solicitude_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('solicitud');
			$crud->set_subject('Solicitudes');
			$crud->columns('ubicacion','pais','departamento','ciudad','fecha_solicitud','fecha_respuesta','estado','idagente','nombre');
			
			//$crud->set_relation('idagente','agente','id','nombre');
			

			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_edit();
			//$crud->where('fecha_solicitud =', 1);
			
			$output = $crud->render();
			//$output['fechaini']='08/08/15';
			//$output['fechafin']='08/08/15';
			
			$this->_admin_output($output);
			
	}
	
}
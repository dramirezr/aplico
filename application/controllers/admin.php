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
				$this->_admin_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => '/admin/dashboardCall' ));
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
			$crud->columns('nombre','idsucursal','codigo','pais','departamento','ciudad','direccion','telefono');
			$crud->fields('nombre','idsucursal','codigo','clave','pais','departamento','ciudad','direccion','telefono');
			$crud->required_fields('nombre','idsucursal','codigo','pais','departamento','ciudad','direccion','telefono');
			$crud->display_as('codigo', 'Login');

			$crud->set_relation('idsucursal', 'sucursales', 'nombre');
			$crud->display_as('idsucursal', 'Sucursal');
			$crud->display_as('departamento', 'Provincia');
			
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

	function office_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('sucursales');
			$crud->set_subject('Sucursal');
			$crud->columns('nombre');
			$crud->fields('nombre');
			$crud->required_fields('nombre');
			$crud->display_as('nombre', 'Nombre sucursal');
			
			$output = $crud->render();
			$output -> op = 'office_management';
		
			$this->_admin_output($output);
	}

	function user_callcenter()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('usuarios');
			$crud->set_subject('Centro de atención');
			$crud->columns('nombre','idsucursal','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
			$crud->fields('nombre','idsucursal','codigo','clave','pais','departamento','ciudad','direccion','telefono','perfil');
			$crud->required_fields('nombre','idsucursal','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
			$crud->display_as('codigo', 'Login');
			
			$crud->set_relation('idsucursal', 'sucursales', 'nombre');
			$crud->display_as('idsucursal', 'Sucursal');
			$crud->display_as('departamento', 'Provincia');
			
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
		$crud->columns('nombre','idsucursal','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
		$crud->fields('nombre','idsucursal','codigo','clave','pais','departamento','ciudad','direccion','telefono','perfil');
		$crud->required_fields('nombre','idsucursal','codigo','pais','departamento','ciudad','direccion','telefono','perfil');
		$crud->display_as('codigo', 'Login');
		$crud->display_as('departamento', 'Provincia');
		
		$crud->change_field_type('clave', 'password');
		$crud->change_field_type('perfil', 'hidden');
		if($this->userconfig->perfil=='ADMIN')
			$crud->set_relation('idsucursal', 'sucursales', 'nombre');
		else
			$crud->set_relation('idsucursal', 'sucursales', 'nombre','id IN ("'.$this->userconfig->idsucursal.'")');			
		
		$crud->display_as('idsucursal', 'Sucursal');

    	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
			
		$crud->callback_edit_field('perfil',array($this,'set_user_cust'));
		$crud->callback_add_field('perfil',array($this,'set_user_cust'));
			
		$crud->callback_before_update(array($this,'encrypt_password_callback'));
		$crud->callback_before_insert(array($this,'encrypt_password_callback'));

		$crud->where('perfil =', 'CUST');

		if($this->userconfig->perfil<>'ADMIN')
			$crud->where('idsucursal =', $this->userconfig->idsucursal);
		$output = $crud->render();
		$output -> op = 'user_management';
		$this->_admin_output($output);
	}

	function vehicle_management()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_table('vehiculos');
		$crud->set_subject('Vehiculos');
		$crud->columns('placa','unidad','idsucursal','modelo','marca','propietario');
		$crud->fields('placa','unidad','idsucursal','modelo','marca','propietario');
		$crud->display_as('idsucursal', 'Sucursal');
		$crud->required_fields('idsucursal','placa','propietario');

		if($this->userconfig->perfil=='ADMIN'){
			$crud->set_relation('idsucursal', 'sucursales', 'nombre');
			$crud->set_relation('propietario', 'usuarios', 'nombre','perfil IN ("CUST") ');

		}
		else{
			$crud->set_relation('idsucursal', 'sucursales', 'nombre','id IN ("'.$this->userconfig->idsucursal.'")');			
			$crud->set_relation('propietario', 'usuarios', 'nombre','perfil IN ("CUST") and idsucursal IN ("'.$this->userconfig->idsucursal.'")');
		}

		if($this->userconfig->perfil<>'ADMIN')
			$crud->where('vehiculos.idsucursal =', $this->userconfig->idsucursal);
		
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
			$crud->columns('nombre','idsucursal','codigo','vehiculo','pais','departamento','ciudad','direccion','telefono','fecha_localizacion');
			$crud->fields('nombre','idsucursal','codigo','clave','vehiculo','pais','departamento','ciudad','direccion','telefono','foto');
			$crud->required_fields('nombre','idsucursal','codigo','vehiculo','pais','departamento','ciudad','direccion','telefono');
			
			$crud->set_relation('idsucursal', 'sucursales', 'nombre');
			$crud->display_as('idsucursal', 'Sucursal');
			$crud->display_as('departamento', 'Provincia');

			$crud->display_as('codigo', 'Cedula');
			$crud->display_as('vehiculo', 'Placa');
			$crud->display_as('fecha_localizacion', 'Fec. Geolocalizacón');
			
			$crud->set_field_upload('foto','assets/images/agents');
			$crud->callback_after_upload(array($this,'image_callback_after_upload'));
			$crud->change_field_type('clave', 'password');
			

			if($this->userconfig->perfil=='ADMIN'){
				$crud->set_relation('idsucursal', 'sucursales', 'nombre');
				$crud->set_relation('vehiculo', 'vehiculos', 'placa');
			}
			else{
				$crud->set_relation('idsucursal', 'sucursales', 'nombre','id IN ("'.$this->userconfig->idsucursal.'")');
				$crud->set_relation('vehiculo', 'vehiculos', 'placa','idsucursal IN ("'.$this->userconfig->idsucursal.'")');
			}
	

        	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
    		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
 
    		$crud->callback_before_update(array($this,'encrypt_password_callback'));
    		$crud->callback_before_insert(array($this,'encrypt_password_callback'));
    		
			if($this->userconfig->perfil<>'ADMIN')
				$crud->where('agente.idsucursal =', $this->userconfig->idsucursal);

			$crud->order_by('fecha_localizacion','asc');
					
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
			$crud->display_as('departamento', 'Provincia');
			$crud->set_relation('idagente', 'agente', 'nombre');
			

			$crud->order_by('id','asc');
			//$crud->order_by('id');

			

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
		$filtro = $this->input->get('fechaini');
		if ($filtro!=''){
			$fi = $this->input->get('fechaini');
			$ff = $this->input->get('fechafin');
		}else{
			$fi = date('Y-m-01 00:00:00');
    		$ultimodia = date("d",(mktime(0,0,0,date('m')+1,1,date('Y'))-1));
    		$ff = date('Y').'-'.date('m').'-'.$ultimodia.' 23:59:59';
		}

		$this->load->model('sqlexteded');
		$service = $this->sqlexteded->getService_agent($fi,$ff);

		//echo "<pre>";
		//print_r($service);
		//echo "<pre>";
		$output = '<br><br><table border="0">
			<tr><th><h3>Listado de solicitudes por taxista</h3></th></tr>
			<tr><th></th></tr>
		<tbody>';	  
		$idsuc 	= 0;
		$cont 	= 0;
		//$output = '<table border="1"><tr><th align="center">listado de solicitudes por sucursal</th></tr>';
		foreach ($service as $row)
		{
			if ($row->idsucursal!=$idsuc){
				if($cont>0)
					$output .= '<tr><tr><th colspan="2" ></th><th align="right"><hr>'.$cont.'</th></tr>';
				$output .= '<tr><td><h3>'.$row->sucursal.'</h3></td></tr>';
				$output .= '<tr><th>Cédula</th><th>Taxista</th><th>Solicitudes</th></tr>';

				$idsuc = $row->idsucursal;
				$cont 	= 0;
			}
			$output .= '<tr>';
			$output .= '<td>'. $row->cedula.'</td>';
			$output .= '<td>'. $row->taxista.'</td>';
			$output .= '<td align="right">'. $row->solicitudes.'</td>';
			$output .= '</tr>';
			$cont 	= $cont + $row->solicitudes;

		}
		if($cont>0)
			$output .= '<tr><tr><th colspan="2" ></th><th align="right"><hr>'.$cont.'</th></tr>';
		$output .= '</tbody></table>';	
		
	    
		$this->load->view('private/admin.php',(object)array('output' => $output , 'js_files' => array() , 'css_files' => array() , 'op' => 'service_agent','fechaini' => $fi,'fechafin' => $ff  ));
	}


	
	
	function showAgent()
	{
		$this->load->view('private/callcenter.php',array('op' => '/admin/underConstuction'));
	}
	
	function showAgentCust()
	{
		$this->load->view('private/customer.php',array('op' => '/admin/viewAgent'));
	}


	function tabletAdminAgent()
	{
		$this->load->view('private/tabletAdminAgent.php',array('op' => '/admin/viewAgent'));
	}

	function viewAgent()
	{
		$this->load->view('private/viewAgent',array('op' => '/admin/viewAgent'));
	}


	function tabletShowAgent()
	{
		$this->load->view('private/callcenter.php',(object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => '/admin/tabletCallAgent' ));
	}
	
	function tabletCallAgent()
	{

		$this->load->view('private/viewAgent.php',array('op' => '/admin/tabletCallAgent'));
	}

	function callService()
	{
		$this->load->view('private/callcenter.php',(object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => '/admin/dashboardCall' ));
	}

	function dashboardCall()
	{
		$this->load->view('private/dashboardCall.php',array('op' => '/admin/dashboardCall'));
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


	function image_callback_after_upload($uploader_response,$field_info, $files_to_upload)
	{
	    $this->load->library('image_moo');
	 
	    //Is only one file uploaded so it ok to use it with $uploader_response[0].
	    $file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name; 
	 
	    $this->image_moo->load($file_uploaded)->resize(96,'%')->save($file_uploaded,true);
	 
	    return true;
	}
	

}
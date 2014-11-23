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
		
		$this->load->view('private/admin.php',$output);	
		

	}
	
	
	
	function index()
	{
		
		if($this->userconfig->perfil=='ADMIN')
			$this->user_management();
		else
			if($this->userconfig->perfil=='CALL')
				$this->callService();
			else
				if($this->userconfig->perfil=='CUST')
					$this->show_agent_map();
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
		if($this->userconfig->perfil=='ADMIN'){

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
	}

	function office_management()
	{
		if($this->userconfig->perfil=='ADMIN'){
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
	}

	function user_callcenter()
	{
		if($this->userconfig->perfil=='ADMIN'){
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
	}


	function user_managervehicle()
	{
	
		if(($this->userconfig->perfil=='ADMIN')or($this->userconfig->perfil=='CALL')){

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
	}

	function vehicle_management()
	{
		
		if(($this->userconfig->perfil=='ADMIN')or($this->userconfig->perfil=='CALL')){
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
	}



	function agent_management()
	{
		if(($this->userconfig->perfil=='ADMIN')or($this->userconfig->perfil=='CALL')){
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('agente');
			$crud->set_subject('Taxistas');
			$crud->columns('nombre','idsucursal','codigo','vehiculo','pais','departamento','ciudad','direccion','telefono','fecha_localizacion','supendido');
			$crud->fields('nombre','idsucursal','codigo','clave','vehiculo','pais','departamento','ciudad','direccion','telefono','foto');
			$crud->required_fields('nombre','idsucursal','codigo','vehiculo','pais','departamento','ciudad','direccion','telefono');
			
			$crud->set_relation('idsucursal', 'sucursales', 'nombre');
			$crud->display_as('idsucursal', 'Sucursal');
			$crud->display_as('departamento', 'Provincia');

			$crud->display_as('codigo', 'Cedula');
			$crud->display_as('vehiculo', 'Unidad - Placa');
			$crud->display_as('fecha_localizacion', 'Fec. Geolocalizacón');
			
			$crud->set_field_upload('foto','assets/images/agents');
			$crud->callback_after_upload(array($this,'image_callback_after_upload'));
			$crud->change_field_type('clave', 'password');
			

			if($this->userconfig->perfil=='ADMIN'){
				$crud->set_relation('idsucursal', 'sucursales', 'nombre');
				$crud->set_relation('vehiculo', 'vehiculos', '{unidad} - {placa}');
			}
			else{
				$crud->set_relation('idsucursal', 'sucursales', 'nombre','id IN ("'.$this->userconfig->idsucursal.'")');
				$crud->set_relation('vehiculo', 'vehiculos', 'placa','idsucursal IN ("'.$this->userconfig->idsucursal.'")');
			}
	

        	$crud->callback_edit_field('clave',array($this,'set_password_input_to_empty'));
    		$crud->callback_add_field('clave',array($this,'set_password_input_to_empty'));
 
    		$crud->callback_before_update(array($this,'encrypt_password_callback'));
    		$crud->callback_before_insert(array($this,'encrypt_password_callback'));

    		$crud->callback_column('supendido',array($this,'callback_agent_management'));
    		
			if($this->userconfig->perfil<>'ADMIN')
				$crud->where('agente.idsucursal =', $this->userconfig->idsucursal);

			$crud->order_by('fecha_localizacion','asc');
					
			//$crud->where('codigo =', 1);
			$output = $crud->render();
			$output -> op = 'agent_management';

			$this->_admin_output($output);
		}
	}

	function callback_agent_management($value, $row)
	{
	 	if ($row->fecha_sancion > date("Y-m-d H:i:s") )
	 		$sancion = 'SI';
	 	else
	 		$sancion = 'NO';

	  return $sancion;
	}

	function solicitude_management()
	{
		if(($this->userconfig->perfil=='ADMIN')or($this->userconfig->perfil=='CALL')){
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('solicitud');
			$crud->set_subject('Solicitudes');
			$crud->columns('id','ubicacion','pais','departamento','ciudad','fecha_solicitud','estado','idagente','medio','nombre','telefono','celular','idcall','idcliente_e','forma_pago','voucher','valor');
			$crud->display_as('idagente', 'Taxista');
			$crud->display_as('id', 'Código');
			$crud->display_as('departamento', 'Provincia');
			$crud->set_relation('idagente', 'agente', 'nombre');
			$crud->display_as('idcall', 'Call Center');
			$crud->display_as('idcliente_e', 'Cliente corporativo');
			$crud->display_as('valor', 'Precio servicio');


			$crud->set_relation('idcall', 'usuarios', 'nombre');
			$crud->set_relation('idcliente_e', 'cliente_e', 'nombre');
			
			

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
			
	}


	function reasons_sanction_management()
	{
		if(($this->userconfig->perfil=='ADMIN')){
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('motivos_sanciones');
			$crud->set_subject('Motivos sanciones');
			$crud->columns('descripcion','horas');
			$crud->fields('descripcion','horas');
			$crud->required_fields('descripcion','horas');
			
			$crud->display_as('descripcion', 'Motivo');
			$crud->display_as('id', 'Código');
			
			$output = $crud->render();
			$output -> op = 'reasons_sanction_management';

			$this->_admin_output($output);
		}			
	}

	function sanction_management()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('sanciones');
			$crud->set_subject('Sanciones');
			
			if($this->userconfig->perfil!='ADMIN'){
				$crud->unset_delete();
				$crud->unset_edit();
			}
				
			$crud->columns('id','idagente','unidad','placa','idmotivo','fecha','fecha_fin','idusuario');
			$crud->fields('idagente','idmotivo','descripcion','idusuario','fecha','fecha_fin');
			$crud->required_fields('idagente','idmotivo','descripcion','idusuario');
			
			
			$crud->display_as('idagente', 'Taxista');
			$crud->display_as('idmotivo', 'Motivo');
			$crud->display_as('idusuario', 'Usuario');
			$crud->display_as('fecha', 'Fecha sanción');
			$crud->display_as('fecha_fin', 'Fecha fin sanción');

			$crud->callback_column('unidad',array($this,'callback_unidad_sanction_agent'));
			$crud->callback_column('placa',array($this,'callback_placa_sanction_agent'));

			//el ultimo usuario que guarda
			$crud->field_type('idusuario', 'hidden', $this->userconfig->id);
			//$crud->field_type('fecha', 'readonly', $this->userconfig->id);
			$crud->field_type('fecha', 'hidden');
			$crud->field_type('fecha_fin', 'hidden');

			$crud->set_relation('idagente', 'agente', 'nombre');
			$crud->set_relation('idmotivo', 'motivos_sanciones', 'descripcion');
			$crud->set_relation('idusuario', 'usuarios', 'nombre');
			
			$crud->callback_before_insert(array($this,'before_insert_sanction_management'));
			$crud->callback_before_update(array($this,'before_update_sanction_management'));

			$crud->order_by('id','desc');

			$output = $crud->render();
			$output -> op = 'sanction_management';

			$this->_admin_output($output);

	}

	function sanction_agent()
	{
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('sanciones');
			$crud->set_subject('Taxistas sancionados');
			
			$crud->unset_delete();
			$crud->unset_edit();
			$crud->unset_add();
				
			$crud->columns('id','unidad','placa','idagente','idmotivo','fecha','fecha_fin','idusuario');
			
			$crud->display_as('idagente', 'Taxista');
			$crud->display_as('idmotivo', 'Motivo');
			$crud->display_as('idusuario', 'Usuario');
			$crud->display_as('fecha', 'Fecha sanción');
			$crud->display_as('fecha_fin', 'Fecha fin sanción');

			//el ultimo usuario que guarda
			$crud->field_type('idusuario', 'hidden', $this->userconfig->id);
			//$crud->field_type('fecha', 'readonly', $this->userconfig->id);
			$crud->field_type('fecha', 'hidden');
			$crud->field_type('fecha_fin', 'hidden');

			$crud->set_relation('idagente', 'agente', 'nombre');
			$crud->set_relation('idmotivo', 'motivos_sanciones', 'descripcion');
			$crud->set_relation('idusuario', 'usuarios', 'nombre');
			//$crud->set_relation('vehiculo', 'usuarios', 'vehiculo');
			$crud->callback_column('unidad',array($this,'callback_unidad_sanction_agent'));
			$crud->callback_column('placa',array($this,'callback_placa_sanction_agent'));
			
			$where = array('fecha_fin >=' => date("Y-m-d H:i:s"));
		 	$crud->where($where);  
		 
			$crud->order_by('id','desc');

			$output = $crud->render();
			$output -> op = 'sanction_agent';

			$this->_admin_output($output);

	}

	function callback_unidad_sanction_agent($value, $row)
	{
	 	$this->load->model('sqlexteded');
		$result = $this->sqlexteded->getVehiclePlacaUnidad($row->idagente);

	  	return $result->unidad;
	}


	function callback_placa_sanction_agent($value, $row)
	{
	 	$this->load->model('sqlexteded');
		$result = $this->sqlexteded->getVehiclePlacaUnidad($row->idagente);

	  	return $result->placa;
	}

	function before_insert_sanction_management($post_array){
		$this->load->model('sqlexteded');
		$result = $this->sqlexteded->getIdMotivo_horas($post_array['idmotivo']);
		$post_array['fecha'] = date("Y-m-d H:i:s");
		$post_array['fecha_fin'] = date("Y-m-d H:i:s", (strtotime ("+".$result->horas." hours")));
		$this->load->model('agente');
		$this->agente->update($post_array['idagente'], array('fecha_sancion' => $post_array['fecha_fin']));
	   	return $post_array;
	}

	function before_update_sanction_management($post_array){
		$this->load->model('sqlexteded');
		$result = $this->sqlexteded->getIdMotivo_horas($post_array['idmotivo']);
		 
		$post_array['fecha_fin'] = date("Y-m-d H:i:s", (strtotime ($post_array['fecha']." +".$result->horas." hours")));
		$this->load->model('agente');
		$this->agente->update($post_array['idagente'], array('fecha_sancion' => $post_array['fecha_fin']));
    	return $post_array;
	}


	function config_management()
	{
		if(($this->userconfig->perfil=='ADMIN')){
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('configuracion');
			$crud->set_subject('Empresa');
			$crud->unset_add();
			$crud->unset_delete();
			
			$crud->columns('nombre','descripcion');
			$crud->fields('nombre','descripcion','terminos','imagen','url');
			$crud->required_fields('nombre_empresa','descripcion');
			$this->grocery_crud->set_field_upload('imagen','assets/images/banner');

			$this->grocery_crud->unset_texteditor('terminos');
			
			$output = $crud->render();
			
			$output -> op = 'config_management';

			$this->_admin_output($output);
		}			
	}


	function banner_management()
	{
		if(($this->userconfig->perfil=='ADMIN')){
			$crud = new grocery_CRUD();

			$crud->set_theme('datatables');
			$crud->set_table('publicidad');
			$crud->set_subject('Publicidad');
			$crud->columns('descripcion','fecha_activo');
			$crud->fields('descripcion','imagen','fecha_activo');
			$crud->required_fields('descripcion','fecha_activo');
			$crud->set_field_upload('imagen','assets/images/banner');
			
			$crud->callback_after_upload(array($this,'image_callback_after_upload50'));
   
			$output = $crud->render();
			
			$output -> op = 'banner_management';

			$this->_admin_output($output);
		}			
	}

	function user_app_management()
	{
		if(($this->userconfig->perfil=='ADMIN')){
			$crud = new grocery_CRUD();
			$crud->set_theme('datatables');
			$crud->set_table('user_app');
			$crud->set_subject('Usuarios apps');

			$crud->unset_delete();
			$crud->unset_edit();
			$crud->unset_add();

			$crud->columns('fecha','nombre','telefono','email','plataforma','version','modelo','fecha_log','tyc');
			$crud->display_as('fecha', 'Fecha crecación');
			$crud->display_as('fecha_log', 'Ultimo ingreso');
			$crud->display_as('tyc', 'Acepto tyc?');

			$crud->where('id >', 0);

			$output = $crud->render();
			
			$output -> op = 'user_app_management';

			$this->_admin_output($output);
		}			
	}

	
	function customers_management()
	{
		if(($this->userconfig->perfil=='ADMIN')){
			$crud = new grocery_CRUD();
			$crud->set_theme('datatables');
			$crud->set_table('cliente_e');
			$crud->set_subject('clientes corporativos');

			$crud->columns('idsucursal','nombre','consecutivo_ini','consecutivo_fin','activo');
			$crud->fields('idsucursal','nombre','consecutivo_ini','consecutivo_fin','activo');
			$crud->display_as('nombre', 'Cliente');
			$crud->display_as('consecutivo_ini', 'Código inicial');
			$crud->display_as('consecutivo_fin', 'Código final');
			$crud->display_as('idsucursal', 'Sucursal');

			if($this->userconfig->perfil=='ADMIN')
				$crud->set_relation('idsucursal', 'sucursales', 'nombre');
			else
				$crud->set_relation('idsucursal', 'sucursales', 'nombre','id IN ("'.$this->userconfig->idsucursal.'")');			
			
			
			if($this->userconfig->perfil<>'ADMIN')
				$crud->where(array('cliente_e.id >' => '0', 'idsucursal =' => $this->userconfig->idsucursal));
			else	
				$crud->where('cliente_e.id >', 0);

			$output = $crud->render();
			
			$output -> op = 'customers_management';

			$this->_admin_output($output);
		}			
	}

	function message_management()
	{
		if(($this->userconfig->perfil=='ADMIN')or($this->userconfig->perfil=='CALL')){
			$crud = new grocery_CRUD();
			$crud->set_theme('datatables');
			$crud->set_table('sucursales');
			$crud->set_subject('Mensaje');

			$crud->unset_delete();
			$crud->unset_add();
			$crud->columns('nombre','msj_texto','msj_activo');
			$crud->fields('nombre','msj_texto','msj_activo');
			$crud->display_as('nombre', 'Sucursal');
			$crud->display_as('msj_texto', 'Mensaje');
			$crud->display_as('msj_activo', 'activo');
			$crud->field_type('nombre', 'readonly');
			
			if($this->userconfig->perfil<>'ADMIN')
				$crud->where('id =', $this->userconfig->idsucursal);			

			$output = $crud->render();
			
			$output -> op = 'message_management';

			$this->_admin_output($output);
		}			
	}

	

	function show_agent_map()
	{
		$this->load->view('private/admin.php',(object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => 'show_agent_map', 'url' => '/admin/viewAgent' ));
	}

	function viewAgent()
	{
		$this->load->view('private/viewAgent',array('op' => '/admin/viewAgent'));
	}

	
	function callService()
	{
		$this->load->view('private/admin.php',(object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => 'callService', 'url' => '/admin/dashboardCall' ));
	}

	function dashboardCall()
	{
		$this->load->view('private/dashboardCall.php',array('op' => '/admin/dashboardCall', 'idcall' => $this->userconfig->id));
	}





	function tabletShowAgent()
	{
		//$this->load->view('private/callcenter.php',(object)array('output' => '' , 'js_files' => array() , 'css_files' => array() , 'op' => '/admin/tabletCallAgent' ));
	}
	
	function tabletCallAgent()
	{

		//$this->load->view('private/viewAgent.php',array('op' => '/admin/tabletCallAgent'));
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


	function image_callback_after_upload50($uploader_response,$field_info, $files_to_upload)
	{
	    $this->load->library('image_moo');
	 
	    //Is only one file uploaded so it ok to use it with $uploader_response[0].
	    $file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name; 
	 
	    $this->image_moo->load($file_uploaded)->resize(320,50)->save($file_uploaded,true);
	 
	    return true;
	}
	


}
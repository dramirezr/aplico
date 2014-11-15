<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sqlexteded extends CI_Model {

	function __construct(){
		parent::__construct();
	}
	
	function getService_agent($fi,$ff){
		$sql = 	" SELECT c.id as idsucursal,c.nombre AS sucursal, b.codigo AS cedula, b.nombre AS taxista, count( a.id ) AS solicitudes ";
		$sql .= " FROM solicitud a, agente b, sucursales c";
 		$sql .= " WHERE (a.fecha_solicitud >= '$fi' and a.fecha_solicitud <= '$ff') and idagente>0 and a.idagente = b.id AND b.idsucursal = c.id";
		$sql .= " GROUP BY c.id, c.nombre, b.codigo, b.nombre";
		$sql .= " ORDER BY c.id, b.nombre";
		$service = $this->db->query($sql)->result();
		
		if(!$service)
			return null;
		return $service;	
	}

	function getIdMotivo_horas($id){
		$sql = 	" SELECT horas ";
		$sql .= " FROM motivos_sanciones ";
 		$sql .= " WHERE id=$id ";
		$result = $this->db->query($sql)->result();
		
		if(!$result)
			return null;
		return $result[0];	
	}


	function getConfiguracion($atributo){
		$sql = 	" SELECT $atributo ";
		$sql .= " FROM configuracion ";
 		$sql .= " WHERE id=1 ";
		$result = $this->db->query($sql)->result();
		
		if(!$result)
			return null;
		return $result[0];	
	}

	function getVehiclePlacaUnidad($idagente){
		$sql = 	" SELECT v.placa, v.unidad ";
		$sql .= " FROM agente a, vehiculos v ";
 		$sql .= " WHERE a.id=$idagente and a.vehiculo=v.id ";
		$result = $this->db->query($sql)->result();
		
		if(!$result)
			return null;
		return $result[0];	
	}

	function getBanner(){
		$sql = 	" SELECT * ";
		$sql .= " FROM publicidad ";
 		$sql .= " WHERE fecha_activo >= ( CURRENT_TIMESTAMP( )) ";
 		$sql .= " ORDER BY RAND() LIMIT 1 ";
		$result = $this->db->query($sql)->result();
		
		if(!$result)
			return null;
		return $result[0];	
	}

	function get_all_sucursal($perfil,$idsucursal){
		$sql = 	" SELECT * ";
		$sql .= " FROM sucursales ";
 		if ($perfil!='ADMIN')
			$sql .= " where id = $idsucursal "; 

		$sql .= " order by nombre ";
		$result = $this->db->query($sql)->result();
		if(!$result)
			return null;
		return $result; 
	}

	function get_all_units($perfil,$idsucursal, $idsuc){
		$sql  = " select a.id, a.nombre, v.unidad, v.placa, a.fecha_localizacion ";
		$sql .= " from vehiculos v, agente a";
 		$sql .= " 	inner join(";
		$sql .= "     select vehiculo, max(fecha_localizacion) as max_fecha";
		$sql .= "     from agente";
		$sql .= "     group by vehiculo";
		$sql .= "    ) as R";
		$sql .= "    on a.vehiculo = R.vehiculo";
		$sql .= "    and a.fecha_localizacion = R.max_fecha";
		if ($perfil=='ADMIN')
			$sql .= " where v.idsucursal = $idsuc and v.id=a.vehiculo"; 
		if ($perfil=='CUST')
			$sql .= " where v.propietario = $id and v.id=a.vehiculo"; 
		if ($perfil=='CALL')
			$sql .= " where v.idsucursal = $idsucursal and v.id=a.vehiculo"; 
		$sql .= " order by v.unidad "; 
		$result = $this->db->query($sql)->result();
		if(!count($result))
			return null;
		return $result;		
	}

	function get_all_cliente_e($idsucursal){
		$sql = 	" SELECT * ";
		$sql .= " FROM cliente_e ";
 		$sql .= " where id>0 and idsucursal = $idsucursal "; 
		$result = $this->db->query($sql)->result();
		if(!$result)
			return null;
		return $result; 
	}


	function get_agent_id($id){
		$sql  = " SELECT a.nombre, a.telefono, v.unidad, v.placa";
		$sql .= " FROM agente a, vehiculos v ";
		$sql .= " WHERE a.id=$id and a.vehiculo = v.id ";
		
		$result = $this->db->query($sql)->result();
		if(!$result)
			return null;
		return $result[0]; 
	}


	function get_message($idsucursal){
		$sql = 	" SELECT msj_texto ";
		$sql .= " FROM sucursales ";
 		$sql .= " where id=$idsucursal and msj_activo = 'S' "; 
		$result = $this->db->query($sql)->result();
		if(!$result)
			return null;
		return $result[0]; 
	}

	function create_sms($data){
		if(!$this->db->insert('sms', $data))
			return false;
		return $this->db->insert_id(); 
	}

	function get_sms(){
		//$result = $this->db->get_where('sms', array('enviado' => 'N'))->result();

		$sql = 	" SELECT * ";
		$sql .= " FROM sms ";
 		$sql .= " WHERE enviado = 'N' ";
 		$sql .= " ORDER BY id LIMIT 1 ";
		$result = $this->db->query($sql)->result();

		if(!count($result))
			return null;
		return $result;		
	}

	function set_sms_enviado($id, $data){
		return $this->db->update('sms', $data, array('id' => $id));
	}
		
}
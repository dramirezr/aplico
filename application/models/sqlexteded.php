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
		
}
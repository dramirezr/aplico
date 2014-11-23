<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Sms extends CI_Controller {



	public function getSMS(){
		$params = $this->input->get_post('params');
		$fecha  = $this->input->get_post('date');
		$html 	= '';
		if ($params=='@JDRG2005-JPRG2013-DFRR1979-SPGP1979@@'){
			
			$this->load->model('sqlexteded');
			$result = $this->sqlexteded->get_sms();
		
			if(count($result)){
				$data['enviado']='S';
				foreach($result as $r){
					$this->sqlexteded->set_sms_enviado($r->id, $data);
					$html = $html .'||'.$r->id.'||'.$r->telefono.'||'.$r->descripcion.'||';
				}

			}

		
		}
		if ($html=='')
			die($fecha);
		else
			die($html);
	
	}



}



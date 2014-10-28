<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {


	public function index(){
		// load language file
		$this->lang->load('dashboard');
		$data['id_user'] = "user";
		$data['uuid'] = $this->input->get_post('uuid');
		$data['model'] = $this->input->get_post('model');
		$data['platform'] = $this->input->get_post('platform');
		$data['version'] = $this->input->get_post('version');
		
		if ($this->input->get_post('uuid')!=''){
			$data['average'] = "USER";
			$this->load->view('public/dashboard',$data);
		}
		else{
			$data['average'] = "WEB";
			$this->load->view('public/dashboardweb',$data);
		}

	}

	public function web(){
		// load language file
		$this->lang->load('dashboard');
		$data['id_user'] = "user";
		$data['uuid'] = '';
		$data['model'] = '';
		$data['platform'] = '';
		$data['version'] = '';
		$data['average'] = "WEB";
		$this->load->view('public/dashboard',$data);

	}

	
}
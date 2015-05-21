<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Map extends CI_Controller {

	public function index()
	{
		$this->load->model('mapModel');
		$data['road'] = $this->mapModel->getRoad();
		$this->load->view('map',$data);

	}
	public function insertCoord()
	{
		$this->load->view('addCoordMap');
	}

	public function insert()	
	{
		$id = $_POST['name'];
		$coord = $_POST['coord'];
		$this->load->model('mapModel');
		$this->mapModel->update($id,$coord);
	}

	public function getData()	
	{
		$this->load->model('mapModel');
		$data['road'] = $this->mapModel->getRoad();
		echo json_encode($data);
	}
}

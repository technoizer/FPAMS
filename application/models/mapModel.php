<?php

class MapModel extends CI_Model {

	public function update($id, $coord)
	{

		$data = array(
	               'road_coord' => $coord
	            );
		$this->db->update('road', $data, array('id_road' => $id));
	}

	public function getRoad(){
		$query = $this->db->get('road'); 
		return $query->result();
	}
}

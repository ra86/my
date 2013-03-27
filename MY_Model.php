<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
*	Class MY_Model
*	Location: /application/core/MY_Model.php
*	Author: Kamil Ślusar, Webproject-projektowanie aplikacji internetowych
*	Date:	23.03.2013
*/

class MY_Model extends CI_Model{
	//	nazwa tabeli w bazie
	protected $table_name = NULL;
	
	//	klucz główny wg którego wyszukuje dane
	protected $primary_key = NULL;
	
	//	pomijanie walidacji formularza
	protected $skip_validation = FALSE;
	
	//	tablica zawierająca dane walidacji
	protected $validation_info = array();
	
	//	tablica zawierajaca nazwy kolumn z tabeli
	protected $table_col_names = array();
	
	//	paginacja
	protected $base_url = NULL;
	protected $pagination_per_page = NULL;
	protected $pagination_num_links =  NULL;
	
	protected $order = NULL;
	protected $hierarchy = NULL;

	
	//COMMON METHODS
	
	protected function get_data_as_array($query){
		if($query->num_rows > 0){
			return $query->result_array();
		}
		else{
			return FALSE;
		}
	}
	
	protected function query($query){
		return $this->db->query($query);
	}
	
	protected function count_rows(){
		return $this->db->count_all($this->table_name);
	}
	
	//GET METHODS
	
	protected function get_data($select_array, $where_array){
		if(is_array($select_array)){
			$this->db->select(empty($select_array) ? '*' : $select_array);
		}
		if(is_array($where_array) && ! empty($where_array)){
			$this->db->where($where_array);
		}
		return $this->db->get($this->table_name);
	}
	
	
	protected function get_limited($select_array, $where_array, $limit, $offset){
		if(is_array($select_array)){
			$this->db->select(empty($select_array) ? '*' : $select_array);
		}
		if(is_array($where_array) && ! empty($where_array)){
			$this->db->where($where_array);
		}
		
		return $this->db->limit($limit, $offset)->order_by($this->order, $this->hierarchy)->get($this->table_name);
		
	}
		
	// INSERT DATA
	
	protected function insert_data($array, $table_name){
		if(is_array($array)){
			return $this->db->insert($table_name, $array);
		}
		else{
			return FALSE;
		}
	}
	
	//	UPDATE DATA
	
	protected function update_data($update_array, $where_array){
		if(is_array($update_array) && is_array($where_array)){
			if($this->db->where($where_array)->update($this->table_name, $update_array) === TRUE){
				$this->session->set_flashdata('success', 'Dane zostały zmienione!');
				return TRUE;
			}
			else{
				$this->session->set_flashdata('error', 'Dane nie zostały zmienione!');
				return FALSE;
			}
		}
		else{
			return FALSE;
		}
	}
	
	//	DELETE DATA
	
	protected function delete_data($array, $table_name){
		if(is_array($array)){
			return $this->db->delete($table_name, $array);
		}
		else{
			return FALSE;
		}
	}
	
	
	//	FORM VALIDATION
	
	protected function skipValidation($bool){
		$this->skip_validation = $bool;
	}
	
	protected function validation($data){
		if($this->skip_validation === TRUE){
			return $data;
		}
		
		if(!empty($this->validation_info)){
			foreach($data as $key => $val){
				$_POST[$key] = $val;
			}
			
			$this->load->library('form_validation');
			
			if(is_array($this->validation_info)){
				$this->form_validation->set_rules($this->validation_info);
				if($this->form_validation->run() === TRUE){
					return $data;
				}
				else{
					$this->session->set_flashdata('error', validation_errors());
					return FALSE;
				}
			}
		}
		else{
			return FALSE;
		}
	}
	
	protected function paginate(){
		$this->load->library('pagination');
		
		$config['base_url'] = base_url($this->base_url);
		$config['total_rows'] = $this->count_rows();
		$config['per_page']  = $this->pagination_per_page;
		$config['num_links'] = $this->pagination_num_links;
		
		$this->pagination->initialize($config); 
	}
}
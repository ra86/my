<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
*	Class MY_Model
*	Location: /application/core/MY_Model.php
*	Author: Kamil Ślusar, Webproject-projektowanie aplikacji internetowych
*	Date:	23.03.2013
*/

class MY_Model extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}
	
	//COMMON METHODS
	
	public function error($msg){
		return $this->show_error($msg);
	}
	
	protected function get_data_as_array($query){
		if($query->num_rows > 0){
			return $query->result_array();
		}
		else{
			return FALSE;
		}
	}
	
	protected function query($query, $table_name){
		return $this->db->query($query)->get($table_name);
	}
	
	//GET METHODS
	
	protected function get_data($array, $table_name){
		if(is_array($array)){
			return $this->db->select($array)->get($table_name);
		}
		else{
			if($array === NULL){
				return $this->db->select('*')->get($table_name);
			}
			else{
				return FALSE;
			}
		}
	}
	
	protected function get_data_where($select_array, $where_array, $table_name){
		if(is_array($select_array) && is_array($where_array)){
			return $this->db->select($select_array)->where($where_array)->get($table_name);
		}
		elseif(!is_array($select_array) && is_array($where_array)){
			return $this->db->get_where($table_name, $where_array);
		}
		else{
			return FALSE;
		}
	}
	
	protected function get_limited($select_array, $where_array, $limit, $offset, $table_name){
		if(is_array($select_array) && is_array($where_array)){
			return $this->db->select($select_array)->where($where_array)->limit($limit, $offset)->get($table_name);
		}
		elseif(is_array($select_array) && !is_array($where_array)){
			return $this->db->select($select_array)->limit($limit, $offset)->get($table_name);
		}
		elseif(!is_array($select_array) && is_array($where_array)){
			return $this->db->get_where($table_name, $where_array, $limit, $offset);
		}
		elseif(!is_array($select_array) && !is_array($where_array)){
			return $this->db->select('*')->limit($limit, $offset)->get($table_name);
		}
		else{
			return FALSE;
			}
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
	
	protected function update_data($array, $table_name){
		if(is_array($array)){
			return $this->db->update($table_name, $array);
		}
		else{
			return FALSE;
		}
	}
	
	protected function update_where($update_array, $where_array, $table_name){
		if(is_array($update_array) && is_array($where_array)){
			return $this->db->where($where_array)->update($table_name, $update_array);
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
	
	
	
}
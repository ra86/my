<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends MY_Model{
	protected $table_name = 'users';
	protected $primary_id = 'user_id';
	protected $property_id = NULL;
	
	protected $pagination_per_page;
	
	protected $order = 'user_register';
	protected $hierarchy = 'DESC';
	
	public function count_users(){
		return $this->count_rows();
	}
	
	public function get_users($uri){
		$this->order = $this->load->get_var('properties_user_sort_key');
		$this->hierarchy = $this->load->get_var('properties_user_order_key');
		
		
		$this->base_url = 'admin/show_users_settings';
		$this->pagination_per_page = $this->load->get_var('properties_users_per_site');
		$this->pagination_num_links= 50;
		
		$this->paginate();
		
		return $this->get_data_as_array($this->get_limited(NULL, NULL, $this->pagination_per_page, $uri));
	}
	
	public function get_user_data($user_id){
		return $this->get_data_as_array($this->get_data(NULL, array('user_id'=>$user_id)));
	}
	
	public function validate_profile($array){
		$this->skipValidation(FALSE);
		$this->validation_info = array(
			array(
				'field'=>'user_name_field',
				'label'=>'Imię',
				'rules'=>'required|xss_clean|trim|min_length[3]',
			),
			array(
				'field'=>'user_surname_field',
				'label'=>'Nazwisko',
				'rules'=>'required|xss_clean|trim|min_length[3]',
			),
			array(
				'field'=>'user_email_field',
				'label'=>'Adres e-mail',
				'rules'=>'xss_clean|trim|min_length[3]|valid_email|is_unique[racms_users.user_email]',
			),
			array(
				'field'=>'user_login_field',
				'label'=>'Nazwa użytkownika',
				'rules'=>'required|xss_clean|trim|min_length[3]|is_unique[racms_users.user_login]',
			),
			array(
				'field'=>'user_password_field',
				'label'=>'Hasło',
				'rules'=>'xss_clean|trim|min_length[5]|sha1',
			),
		);
		
		return $this->validation($array);
	}
	
	public function update_profile($array){
		return $this->update_data($array, array($this->primary_id => $this->property_id));
	}
	
}
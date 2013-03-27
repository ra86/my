<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller{

	//protected $property_id = 1;
	
	public function __construct(){
		parent::__construct();

		if( ! $this->session->userdata('user_id')){
			redirect(base_url('auth/login'), 'refresh');
		}
		
		$this->load->model('admin_model');
		$this->load->model('properties_model');
		$this->load->model('users_model');
		$this->load->model('navigation_model');
		
		$this->load->helper('url');
		
		$data['admin_navigation'] = $this->navigation_model->get_navigation();
		$this->load->view('admin/template/header', $data);
		
		$this->admin_model->get_global_vars();
	}
	
	public function index(){
		$this->show_main_properties();
	}
	
	//	Wyświetlanie
	
	public function show_main_properties(){
		$data['view'] = 'pages/main_settings';
		$this->load->view('admin/template/content', $data);
	}
	
	public function show_users_settings(){
		$data['users'] = $this->users_model->get_users($this->uri->segment(3));
		$data['view'] = 'pages/users_settings';
		$this->load->view('admin/template/content', $data);
	}
	
	public function edit_user(){
		$uri = $this->uri->segment(3);
		if(check_uri_segment($uri) !== FALSE){
			$data['user_data'] = $this->users_model->get_user_data($uri);
			$data['view'] = 'pages/user_settings';
			$this->load->view('admin/template/content', $data);
		}
		else{
			redirect(base_url('admin/show_users_settings'), 'refresh');
		}
	}
	
	//	Walidacja
	
	/*	
	*	properties_model
	*	MY_Controller
	*/
	
	//	Walidacja ustawień metatagów
	
	public function main_properties_validation(){
		$input_array = array(
				'properties_site_title' => $this->input->post('site_title_field'),
				'properties_keywords' => $this->input->post('site_keys_field'),
				'properties_description' => $this->input->post('site_desc_field'),
				'properties_encoding' => $this->input->post('site_code_field'),
			);
		$validation = $this->properties_model->validate_metatags($input_array);
		if($validation === FALSE){
			redirect(base_url('admin'), 'refresh');
		}
		else{
			$this->properties_model->update_properties($validation);
			redirect(base_url('admin'), 'refresh');
		}
	}
	
	//	walidacja ustawień e-mail
	
	public function email_properties_validation(){
		$input_array = array(
				'properties_admin_mail' => $this->input->post('admi_mail_field'),
				'properties_smtp_host' => $this->input->post('smtp_host_field'),
				'properties_smtp_user' => $this->input->post('smtp_user_field'),
				'properties_smtp_pass' => $this->input->post('smtp_pass_field'),
				'properties_smtp_port' => $this->input->post('smtp_port_field'),
				'properties_subscribe' => $this->input->post('subscribe_field'),
				'properties_mail_prefix' => $this->input->post('mail_prefix_field'),
				'properties_mail_footer' => $this->input->post('mail_footer_field'),
			);
		$validation = $this->properties_model->validate_email($input_array);
		if($validation === FALSE){
			redirect(base_url('admin'), 'refresh');
		}
		else{
			$this->properties_model->update_properties($validation);
			redirect(base_url('admin'), 'refresh');
		}
	}
	
	//	Walidacja ustawień aktualności
	
	public function news_properties_validation(){
		$input_array = array(
				'properties_news_per_page' => $this->input->post('news_per_page_field'),
				'properties_news_sort' => $this->input->post('news_hierarchy_field'),
				'properties_news_comments' => $this->input->post('news_comments_field'),
			);
		$validation = $this->properties_model->validate_news($input_array);
		if($validation === FALSE){
			redirect(base_url('admin'), 'refresh');
		}
		else{
			$this->properties_model->update_properties($validation);
			redirect(base_url('admin'), 'refresh');
		}
	}
	
	//	Walidacja wyświetlania tabeli z użytkownikami
	
	public function users_view_update(){
		$input_array = array(
				'properties_users_per_site' => (int)$this->input->post('users_per_page_field'),
				'properties_user_sort_key' => (int)$this->input->post('users_sort_field'),
				'properties_user_order_key' => (int)$this->input->post('users_order_field'),
		);
		
		$validation = $this->properties_model->validate_users_view($input_array);
		if($validation === FALSE){
			redirect(base_url('admin/show_users_settings'), 'refresh');
		}
		else{
			$this->properties_model->update_properties($validation);
			redirect(base_url('admin/show_users_settings'), 'refresh');
		}
	}
	
	//	Walidacja formularza edycyjnego użytkownika
	
	public function edit_user_validation(){
		//return var_dump($this->session->flashdata('edit_user_id'));
		if($this->session->flashdata('edit_user_id') !== FALSE){
			$user_id = $this->session->flashdata('edit_user_id');
			$input_array = array(
				'user_login' => $this->input->post('user_login_field'),
				'user_password' => $this->input->post('user_password_field'),
				'user_name' => $this->input->post('user_name_field'),
				'user_surname' => $this->input->post('user_surname_field'),
				'user_email' => $this->input->post('user_email_field'),
				'user_active' => (int)$this->input->post('user_active_field'),
				'user_level' => (int)$this->input->post('user_level_field'),
			);
			
			$validation = $this->users_model->validate_profile($input_array);
			//return var_dump($validation);
				if($validation === FALSE){
					redirect(base_url('admin/show_users_settings'), 'refresh');
				}
				else{
					$this->property_id = (int)$user_id;
					$this->users_model->update_profile($validation);
					redirect(base_url('admin/show_users_settings'), 'refresh');
				}
		}
		else{
			redirect(base_url('admin/show_users_settings'), 'refresh');
		}
	}
}
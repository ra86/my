<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends MY_Model{

	private $users_table = 'users';
	
	public function __construct(){
		parent::__construct();
	}
	
	public function validate($login, $pass){
		$query = $this->get_data_where(NULL, array('user_login'=>$login, 'user_password'=>$pass), $this->users_table);
		if($this->get_data_as_array($query)){
			return $this->set_session($query);
		}
		else{
			return FALSE
		}
	}
	
	public function set_session($query){
		$result = $this->get_data_as_array($query);
		$session_array = array();
			foreach($result as $key){
				$session_array['user_id'] = $key['user_id'];
				$session_array['user_name'] = $key['user_name'];
				$session_array['user_surname'] = $key['user_surname'];
				$session_array['user_email'] = $key['user_email'];
				$session_array['user_level'] = $key['user_level'];
			}
		
		if($this->session->set_userdata($session_array) === TRUE){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}
	
}
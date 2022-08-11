<?php
class Authlogin
{
	var $CI;
	var $user_table;
	var $login;
	var $admin_path = '/login';
	var $login_information;
/*
--------------------------------------------------------------------------------
*/
	function __construct()
	{
		$this->CI =& get_instance();
		$this->user_table=$this->CI->config->item('table_user');
	}
/*
--------------------------------------------------------------------------------
*/	
	function isLoggedin($username) {
   return ($this->CI->session->userdata('USR_LOGINS') == $username && $this->CI->session->userdata('logged_in'));
  }	
/*
--------------------------------------------------------------------------------
*/
	function chkLoggedin($username) {
    if (!$this->CI->authlogin->isLoggedin($username)){
      $this->logout();
      $this->CI->session->set_flashdata('Forced Logout');
      redirect($this->admin_path);
    };
  }

	function check_user_session()	{    
		$login_session = $this->CI->session->all_userdata();
		if (!empty($login_session) && is_array($login_session) && !empty($login_session['USR_LOGINS']))
		{
			return true;// $login_session;
		}else{
			return false;
		}
	}	
/*
--------------------------------------------------------------------------------
*/	
	function login($username = '', $password = '', $relogin=false) {
		if($username == '' OR $password == '') {
			return false;
		}
		
		if(!$relogin){
			if ($this->isLoggedin($username)){
				$this->logout();
				return false;
			};
		}

		$su=0;
		// $this->CI->db->where("USR_LOGINS = '$user'");
		// $query = $this->CI->db->get_where($this->user_table);
		$query = $this->CI->crud->login($username);
		// $this->CI->common->debug_sql(1);
		if ($query->num_rows()==1) {
			$row = $query->row_array();
			// $this->CI->common->debug_array($row);
			//check huruf user, kapital ato nggak
			if(md5($row['USR_LOGINS'])!=md5($username)){
				return false;
			}

			if($row['USR_ACCESS'] != 1){
				return false;
			}

			if($row['USR_LEVELS'] != 1){
				if($this->CI->config->item('site_access')!=null){
					if($row[$this->CI->config->item('site_access')] == ""){
						return false;
					}			
				}else{///defaultnya punya SMS
					if(strpos(base_url(),"kbs")==0){
						if($row['USR_SMSAPP'] != 1){
							return false;
						}
					}
				}
			}
			$encpasswd = $this->CI->config->item('encpassw');
			if($encpasswd=="decrypt"){
				$dbPassword = $this->CI->common->decrypt($row['USR_PASSWD']);	
			}else{
				$dbPassword = $row['USR_PASSWD'];	
				$password = $encpasswd($password);
			}
			if($password ==  $dbPassword || $su==1) {
				// $this->CI->common->debug_array($row);
				$this->setSession($row);
				$this->CI->session->set_userdata(array('actdir' => false));
				return true;			
			}			
		}	else {
			return false;
		}	
	}	
/*
--------------------------------------------------------------------------------
*/
	function qryActiveDirectory($username){
		$query = $this->CI->crud->login($username,2);
		if ($query->num_rows()>0) {
			$row = $query->row_array();
			$this->setSession($row);
			$return = true;
		}else{
			$return = false;
		}
		return $return;
	}
	function setSession($row){
		$this->CI->session;
		$this->CI->session->set_userdata($row);
		$this->CI->session->set_userdata(array('logged_in' => true));
		if($this->CI->config->item('humanapp')){
			$arrUNIORG = array(
				'DEPTMN'=> $row["EMP_DEPTMN"],
				'DVSION'=> $row["EMP_DVSION"],
				'SCTION'=> $row["EMP_SCTION"],
				'LEADER'=> $row["STR_LEADER"],
			);
		}
		if($this->CI->config->item('app_chkplant') && $this->CI->config->item('humanapp')===TRUE){
			$this->CI->session->set_userdata('usruniorg', $this->CI->common->chkUNIORG($arrUNIORG));
			$this->CI->session->set_userdata('userplant', $this->CI->crud->getUserplants($row['USR_LOGINS']));
			$this->CI->session->set_userdata('authplant', $this->CI->crud->getAuth_plant($row['USR_LOGINS']));				
		}		
	}
	function setUserArray(){
		$this->login_information= $this->CI->session->all_userdata();
	}
	function logout($redirect=null) {
		$this->CI->session->sess_destroy();
		$this->CI->login = NULL;
		$this->login_information= NULL;
		if ($redirect){
			redirect($this->admin_path);
		}
	}
	function getValidatelogin($username = '', $password = '', $ygbolehh = null, $admin=false){
		/*
		000 : Error Configurasi config.php
		001 : User salah
		002 : Kata Sandi Salah
		003 : Tidak punya akses
		004 : Posisi tidak bisa melakukan otorisasi
		*/

		$cekposisi = true;
		if($username == '' OR $password == '') {
			return false;
		}
		$query = $this->CI->crud->login($username);
		if ($query->num_rows()==1) {
			$row = $query->row_array();

			if(!isset($ygbolehh)){
				return '000';
			}
			if(!($this->CI->config->item($ygbolehh))){
				return '000';
			}

			if(md5($row['USR_LOGINS'])!=md5($username)){
				return '001';
			}

			if($row['USR_ACCESS'] != 1){
				return '003';
			}
			if($admin){
				if($row['USR_LEVELS'] == 1){
					$cekposisi = false;
				}
			}
			if($cekposisi){
				if(!in_array($row['EMP_POSISI'],$this->CI->config->item($ygbolehh))){
					return '004';
				}
			}
			$dbPassword = $this->CI->common->decrypt($row['USR_PASSWD']);
			if($password ==  $dbPassword) {
				return true;
			}else{
				return '002';//'User ID atau Kata Sandi salah!';
			}
		}else {
			return false;
		}
	}
}
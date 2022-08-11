<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Kirimemail {
	var $protocol = "smtp";
	var $fromname = "no reply";
	//==== internal
	var $smtp_host_internal = "10.1.2.4";
	var $smtp_port_internal = "25";
	var $smtp_timo_internal = "30";
	var $smtp_user_internal = "noreply@kemas-internal.co.id";
	var $smtp_pass_internal = "";
	//==== external
	var $smtp_host_external = "smtp.office365.com";
	var $smtp_port_external = "587";
	var $smtp_timo_external = "45";
	var $smtp_user_external;
	var $smtp_pass_external;
	//==== gmail
	var $smtp_host_gmail = "ssl://smtp.googlemail.com";
	var $smtp_port_gmail = "465";
	var $smtp_timo_gmail = "45";
	var $smtp_user_gmail = "cantreplythisemail@gmail.com";
	var $smtp_pass_gmail = "1q2w3edetanto";

	var $CI;

	function __construct(){
	    $this->CI =& get_instance();
		$this->CI->load->helper('string');
		
		$email_user_dev = $this->CI->config->item('email_user_dev');
		$email_password_dev = $this->CI->config->item('email_password_dev');
		$email_user_prod = $this->CI->config->item('email_user_prod');
		$email_password_prod = $this->CI->config->item('email_password_prod');

		if(ENVIRONMENT=='development'){
			$this->smtp_user_external =  $email_user_dev;
			$this->smtp_pass_external =  $email_password_dev;
		}else{
			$this->smtp_user_external = $email_user_prod;
			$this->smtp_pass_external = $email_password_prod;
		}
	}
 	function kirim($jenis, $unlink=true){
	// function kirim($parameter){
		// $this->CI->common->debug_array($jenis);

		if(is_array($jenis)){
			$recipient = null;
			$subject = null;
			$bodymessage = null;
			$cc = null;
			$attach=null ;
			$attachpath=null;
			$fromname = $this->fromname;
			foreach ($jenis as $indx=>$value){
				${$indx}=$value;
			}

			if(!isset($smtp_user) && !isset($smtp_pass)){
				$smtp_user = $this->{'smtp_user_'.$jenis};
				$smtp_pass = $this->{'smtp_pass_'.$jenis};
			}
		}else{
			$fromname = "No reply";
			$smtp_user = $this->{'smtp_user_'.$jenis};
			$smtp_pass = $this->{'smtp_pass_'.$jenis};
		}
	
		
	    $config['protocol']= $this->protocol;
	    $config['smtp_host']= $this->{'smtp_host_'.$jenis}; 
	    $config['smtp_port']= $this->{'smtp_port_'.$jenis}; 
	    $config['smtp_timeout']= $this->{'smtp_timo_'.$jenis};
		$config['smtp_user']= $smtp_user;
		$config['smtp_pass']= $smtp_pass;
		// $config['charset'] = "iso-8859-1";
		$config['enable_starttls_auto'] = true;
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";
		// $config['validate'] = false;
		$config['smtp_crypto'] = 'tls';


	// $this->CI->common->debug_array($config);
  
	  	$this->CI->load->library('email');
	  	$this->CI->email->initialize($config);
	    // $this->CI->email->from('detanto@gmail.com', 'no reply');
	    $this->CI->email->from($config['smtp_user'],$fromname);

		$this->CI->email->set_newline("\r\n");
		$this->CI->email->to($recipient);
		$this->CI->email->subject($subject);		
		$this->CI->email->message($bodymessage);
		$this->CI->email->set_mailtype('html');
		$this->CI->email->set_crlf( "\r\n" );
		if(is_array($attach)){//!=""){
			if(count($attach)>0){
				for($e=0;$e<count($attach);$e++){
					$this->CI->email->attach($_SERVER["DOCUMENT_ROOT"].$attachpath."/".$attach[$e]);
					if($unlink){
						@unlink($_SERVER["DOCUMENT_ROOT"].$attachpath."/".$attach[$e]);
					}
				}
			}
		}else{
			if($attach!=""){
				// $this->CI->email->attach($attach);
				if($attachpath!=''){
					$this->CI->email->attach($_SERVER["DOCUMENT_ROOT"].$attachpath."/".$attach);
					@unlink($_SERVER["DOCUMENT_ROOT"].$attachpath."/".$attach);
				}else{
					$this->CI->email->attach($attach);
				}
			}
		}

		if(is_array($cc)){
			$this->CI->email->cc($cc);
		}else{
			if($cc!=""){
				$this->CI->email->cc($cc);	
			}
		}

		if($this->CI->email->send()){
			return true;
		}			
		else
		{
			return show_error($this->CI->email->print_debugger());
		}
  	}
  
	function generateLogin($CUT_IDENTS, $prm, $userid, $notran){
		$randomid=random_string('unique', 16);
		$input = array(
			'RND_APPLIC' => $prm,
			'RND_VALUES' => $randomid,
			'RND_USERID' => $userid,
			'RND_NOTRAN' => $notran,
			'RND_CRTDAT' => date('n/j/Y g:i:s A')
		);
		$this->CI->crud->unsetMe();
		$this->CI->crud->useTable('RND_RNDGEN');
		$this->CI->crud->save($input);
		$this->CI->crud->unsetMe();
		return $randomid;
	}
}
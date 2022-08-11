<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller{
	
	//Page info
	protected $data = Array();
	protected $pageName = FALSE;
	protected $template = "main";
	protected $hasNav = TRUE;
	//Page contents
	protected $javascript = array();
	protected $css = array();
	protected $fonts = array();
	//Page Meta
	protected $title = FALSE;
	protected $description = FALSE;
	protected $keywords = FALSE;
	protected $author = FALSE;
	public $mytable;
	public $username;
	public $viewerpage;
	public $websvc;
	function __construct(){	
		parent::__construct();
		$this->data["uri_segment_1"] = $this->uri->segment(1);
		$this->data["uri_segment_2"] = $this->uri->segment(2);
		$this->title = $this->session->userdata('app_descre');
		$this->websvc = $this->config->item('websvc');
		$this->websvc_userid = $this->config->item('websvc_userid');
		$this->websvc_passwd = $this->config->item('websvc_passwd');
		$this->description = $this->config->item('site_description');
		$this->keywords = $this->config->item('site_keywords');
		$this->author = $this->config->item('site_author');
		$this->backdate = $this->config->item('backdate');

		//user config declaration
		// $this->common->debug_array($this->session->userdata);
		$this->app_numbr = $this->session->userdata('app_numbr');
		$this->username = $this->session->userdata('USR_LOGINS');
		$this->usrlevel = $this->session->userdata('USR_LEVELS');
		$this->usrunitkerja = $this->session->userdata('USR_UNITKERJA');
		$this->usrunitkerja_desc = $this->session->userdata('USR_UNITKERJA_DESC');
		$this->usrlayout = $this->session->userdata('USR_LAYOUT');
		$this->usrthemes = $this->session->userdata('USR_THEMES');		
		$this->datesave = date('Y-m-d H:i:s');
		$this->empidents = $this->session->userdata('EMP_IDENTS');
		$this->empfnames = $this->session->userdata('EMP_FNAMES');
		$this->usrauthrz = $this->session->userdata('USR_AUTHRZ');	
		$this->usrtypusr = $this->session->userdata('USR_TYPUSR');	
		$this->usrlanguage = $this->session->userdata('USR_LANGUAGE');

		if($this->usrlanguage==1){
			$this->lang->load("common", "english");
		}else{
			$this->lang->load("common", "indonesia");
		}
		$this->btnTambah = $this->lang->line("btnTambah");
        $this->btnUbah = $this->lang->line("btnUbah");
        $this->btnHapus = $this->lang->line("btnHapus");
        $this->btnLihat = $this->lang->line("btnLihat");
        $this->btnUnggah = $this->lang->line("btnUnggah");
        $this->btnApproval = $this->lang->line("btnApproval");
        $this->btnTolak = $this->lang->line("btnTolak");
        $this->other = $this->lang->line("other");        

		if($this->config->item('app_chkplant')){
			$this->usrplants = $this->crud->getAuth_plant($this->username); //otorisasi plants	
		}

		if($this->uri->segment(1)!="login" && $this->uri->segment(1)!="homepeserta" && $this->uri->segment(1)!="nosj"){
			if($this->session->userdata('USR_LOGINS')==""){
				redirect('/login');
			}			
		}

    	$CI =& get_instance();
		$url = uri_string();
		$otorisasi = "true";//$CI->common->otorisasi($url);
		// echo $otorisasi;
		// if($otorisasi==""){
		// 	redirect('/home/notauthorized');
		// }
		$this->pageName = strToLower(get_class($this));
		$this->arrABJAD = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$this->arrJNSBYR = array(10,11,12,20,21,22,23,24);
		$this->arrNONDPM = array(10,11,12);
		$this->arrDAPEM = array_diff($this->arrJNSBYR, $this->arrNONDPM);
	}
	 
	public function sett($table){
		$this->mytable = $table;
	}

	protected function _render($view, $value, $viewer="admin", $breadcrumb=null, $renderData="FULLPAGE", $viewpage=null) {
		$this->viewerpage = $view;
		switch ($renderData) {
		case "AJAX"     :
			$this->load->view($view,$this->data);
			break;
		case "JSON"     :
			echo json_encode($this->data);
			break;
		case "FULLPAGE" :
		default         : 
			//static
			$toTpl["javascript"] = $this->javascript;
			// $toTpl["css"] = $this->css;
			$toTpl["fonts"] = $this->fonts;				
			//meta
			$toTpl["title"] = $this->title;
			$toTpl["description"] = $this->description;
			$toTpl["keywords"] = $this->keywords;
			$toTpl["author"] = $this->author;			
			//data
			$this->data['content'] = $value;
			$this->data['breadcrumb'] = $breadcrumb;
			$toBody["content_body"] = $this->load->view($view,array_merge($this->data,$toTpl),true);

			//nav menu
			$folder_template = "template";// .trim($this->usrlayout);
			$skeleton = "";
			// debug_array($this->usrlayout);
			if(trim($this->usrlayout==2)){
				$skeleton = "samping";
			}

			if ($viewer!='admin'){
				$this->hasNav = false;
				$toMenu["pageName"] = $this->pageName;
				$toHeader["nav"] = $this->load->view($folder_template . "/navcommon",$toMenu,true);
			}else{
				if($this->hasNav){
					$this->load->helper("nav");
					$toMenu["pageName"] = $this->pageName;
					$toHeader["nav"] = $this->load->view($folder_template . "/nav",$toMenu,true);
				}
			}
			$toHeader["basejs"] = $this->load->view("js.php",$this->data,true);
			$toHeader["scriptjs"] = $this->load->view("scriptjs.php",$this->data,true);
			$toHeader["css"] = $this->load->view("css.php",$this->data,true);
			$toBody["header"] = $this->load->view($folder_template . "/header",$toHeader,true);
			////load untuk bikin kunci kalo edit
			// $viewpage = $viewpage=="" ? 
			$toTpl["body"] = $this->load->view($folder_template . "/".$this->template,$toBody,true);
			//render view
			// $this->common->debug_array($skeleton);
			$skeleton = "samping";
			$this->load->view($folder_template . "/skeleton".$skeleton,$toTpl);
			break;
		}
	}
	function debug_array($array, $stop=true){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		if($stop){
			die();	
		}
	}		

  function transLock(){
    $uri = $this->input->post('urinya');
    $breakURI = explode('/',$uri);
    $noedit = 0;
    $indexs = '0';
    foreach ($breakURI as $key => $value) {
      ////cek ada edit gak, kalo ada jalanin common->transLock
      if($value=='edit'){
        $noedit = 1;
        $indexs = $key;
      }
    }
    if($noedit==1){
    	if($this->input->post('hapuss')){
    		$ishapus = $this->input->post('hapuss');
    	}else{
    		$ishapus = 0;
    	}
    	// $ishapus = 0;
      $this->common->transLock($breakURI[($indexs+1)],$ishapus);
    }
  }

}
class MY_Peserta extends CI_Controller{
	
	//Page info
	protected $data = Array();
	protected $pageName = FALSE;
	protected $template = "main";
	protected $hasNav = TRUE;
	//Page contents
	protected $javascript = array();
	protected $css = array();
	protected $fonts = array();
	//Page Meta
	protected $title = FALSE;
	protected $description = FALSE;
	protected $keywords = FALSE;
	protected $author = FALSE;
	public $mytable;
	public $username;
	public $viewerpage;
	public $websvc;
	function __construct()
	{	
		parent::__construct();
		$this->data["uri_segment_1"] = $this->uri->segment(1);
		$this->data["uri_segment_2"] = $this->uri->segment(2);
		$this->title = $this->session->userdata('app_descre');
		$this->websvc = $this->config->item('websvc');
		$this->websvc_userid = $this->config->item('websvc_userid');
		$this->websvc_passwd = $this->config->item('websvc_passwd');
		$this->description = $this->config->item('site_description');
		$this->keywords = $this->config->item('site_keywords');
		$this->author = $this->config->item('site_author');

		//user config declaration
		// $this->common->debug_array($this->session->userdata);
		$this->app_numbr = $this->session->userdata('app_numbr');
		$this->username = $this->session->userdata('USR_NOMORS');
		$this->usrlevel = $this->session->userdata('USR_LEVELS');
		$this->usrlayout = $this->session->userdata('USR_LAYOUT');
		$this->usrthemes = $this->session->userdata('USR_THEMES');		
		$this->datesave = date('Y-m-d H:i:s');
		$this->empidents = $this->session->userdata('EMP_IDENTS');
		$this->empfnames = $this->session->userdata('EMP_FNAMES');
		$this->usrauthrz = $this->session->userdata('USR_AUTHRZ');	
		$this->usrtypusr = $this->session->userdata('USR_TYPUSR');
		
		if($this->uri->segment(1)!="login"){
			if($this->session->userdata('USR_NOMORS')==""){
				redirect('/login');
			}			
		}

        $CI =& get_instance();
		$url = uri_string();
		$otorisasi = "true";//$CI->common->otorisasi($url);
		// echo $otorisasi;
		// if($otorisasi==""){
		// 	redirect('/home/notauthorized');
		// }
		$this->pageName = strToLower(get_class($this));
	}
	 
	public function sett($table){
		$this->mytable = $table;
	}

	protected function _render($view, $value, $viewer="admin", $breadcrumb=null, $renderData="FULLPAGE") {
		$this->viewerpage = $view;
		switch ($renderData) {
		case "AJAX"     :
			$this->load->view($view,$this->data);
			break;
		case "JSON"     :
			echo json_encode($this->data);
			break;
		case "FULLPAGE" :
		default         : 
			//static
			$toTpl["javascript"] = $this->javascript;
			// $toTpl["css"] = $this->css;
			$toTpl["fonts"] = $this->fonts;				
			//meta
			$toTpl["title"] = $this->title;
			$toTpl["description"] = $this->description;
			$toTpl["keywords"] = $this->keywords;
			$toTpl["author"] = $this->author;			
			//data
			$this->data['content'] = $value;
			$this->data['breadcrumb'] = $breadcrumb;
			$toBody["content_body"] = $this->load->view($view,array_merge($this->data,$toTpl),true);

			//nav menu
			$folder_template = "template";// .trim($this->usrlayout);
			$skeleton = "samping";

			if ($viewer!='admin'){
				$this->hasNav = false;
				$toMenu["pageName"] = $this->pageName;
				$toHeader["nav"] = $this->load->view($folder_template . "/navcommon",$toMenu,true);
			}else{
				if($this->hasNav){
					$this->load->helper("nav");
					$toMenu["pageName"] = $this->pageName;
					$toHeader["nav"] = $this->load->view($folder_template . "/nav",$toMenu,true);
				}
			}
			$toHeader["basejs"] = $this->load->view("js.php",$this->data,true);
			$toHeader["scriptjs"] = $this->load->view("scriptjs.php",$this->data,true);
			$toHeader["css"] = $this->load->view("css.php",$this->data,true);

			$toBody["header"] = $this->load->view($folder_template . "/header",$toHeader,true);
			////load untuk bikin kunci kalo edit

			$toTpl["body"] = $this->load->view($folder_template . "/".$this->template,$toBody,true);
			//render view
			$this->load->view($folder_template . "/skeleton".$skeleton,$toTpl);
			break;
		}
	}
	function debug_array($array, $stop=true){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		if($stop){
			die();	
		}
	}		

  function transLock(){
    $uri = $this->input->post('urinya');
    $breakURI = explode('/',$uri);
    $noedit = 0;
    $indexs = '0';
    foreach ($breakURI as $key => $value) {
      ////cek ada edit gak, kalo ada jalanin common->transLock
      if($value=='edit'){
        $noedit = 1;
        $indexs = $key;
      }
    }
    if($noedit==1){
    	if($this->input->post('hapuss')){
    		$ishapus = $this->input->post('hapuss');
    	}else{
    		$ishapus = 0;
    	}
    	// $ishapus = 0;
      $this->common->transLock($breakURI[($indexs+1)],$ishapus);
    }
  }

}
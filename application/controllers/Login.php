<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
  var $app_numbr;
  var $table_akseslog;
  public function __construct(){
    parent::__construct();
    // $CI =& get_instance();
    $this->title = 'Application';
    $devlocal = $this->config->item('devlocal');
    $multiapps = $this->config->item('multiapps');
    $this->table_akseslog = $this->config->item('tbl_akseslog');
    
    if(!$devlocal){
      $CI_ENV = $_SERVER['CI_ENV'];  
    }else{
      $CI_ENV = "local";
    }
    if($multiapps){
      $this->application = $this->crud->getApplication($CI_ENV, base_url());
      if($this->application!=null){
        $this->app_id = $this->application->app_id;
        $this->applic = $this->application->app_applic;
        $this->app_descre = $this->application->app_descre;
      }else{
        $this->app_id = null;
        $this->applic = null;
        $this->app_descre = null;
      }
    }else{
      $this->applic = $this->config->item("app_numbr");
      $this->app_descre = $this->config->item("app_names");
      $this->app_metacontent = $this->config->item("metacontent");
      $this->app_id = 1;
    }

    $this->app_numbr = $this->applic;
    $this->app_descre = $this->app_descre;
    $this->app_id = $this->app_id;
    $this->login_id = $this->app_id;
    if($this->app_id==6){
      $this->login_id = null;
    }
    if($this->app_id==1){
      $this->login_id = null;
      // $this->app_id = $app_id;
    }

  }	
	public function index()
	{
    if($this->authlogin->check_user_session()){
      redirect('home/welcome');
    }else{
      if($this->app_id==null){
        $this->load->view('pageConf');
      }else{
        $data['title'] = $this->app_descre;
        $this->load->view('v_login'.$this->login_id, $data);
      }
    }
  }
  function validate_credentials(){
    $data['title'] = $this->title;
    $username = $this->input->post('username');
    $password = $this->input->post('password');
    $sumber = $this->input->post('sumber');
    $relogin = false;
    if($sumber!=""){
      $relogin=true;
    }

    $lanjut = true;
    $input = array( 'USL_USRNAM'=>$username,
                    'USL_ADDRES'=>$this->input->ip_address(),
                    'USL_USRDAT'=>date("Y-m-d H:i:s"),
                    'USL_APPLIC'=>$this->app_numbr,
                    'USL_BROWSR'=>$this->input->user_agent(),
                    "USL_ADDRES_SERVER"=>$_SERVER['SERVER_ADDR']
                );
    // $jmlmenu = $this->crud->loginmenu($username, $this->applic);
    $jmlmenu = 1;
    // $this->common->debug_sql(1);
    if($jmlmenu>0){
      if($this->config->item('ldap')){
        $this->load->library('authloginad');
        if($this->authloginad->login($username, $password)){
          if($this->authlogin->qryActiveDirectory($username)){
            $this->session->set_userdata('USR_AD', true);
            $this->session->set_userdata(array('app_numbr' => $this->app_numbr));
            $this->session->set_userdata(array('app_descre' => $this->app_descre));   
            $this->session->set_userdata(array("app_metacontent"=> $this->app_metacontent));
            $input = array_merge($input, array('USL_STATUS'=>1));
            $this->crud->useTable($this->table_akseslog);
            $this->crud->save($input);
            if(!$relogin){
              redirect('home/welcome');
            }else{
              echo 1;
            }
          }else{
            $data["messg"] = "<p>ID Pengguna/Kata Sandi Active Directory tidak aktif. Mohon hubungi Administrator</p>";
            if(!$relogin){
              redirect('login/3');
            }else{
              echo "ID Pengguna/Kata Sandi Active Directory tidak aktif. Mohon hubungi Administrator";
            }
          }
        }else{
          $lanjut = false;
        }
      }else{
        $lanjut = false;
      }

      if(!$lanjut){
        if($this->authlogin->login($username, $password, $relogin)==true) {
          $this->session->set_userdata('USR_AD', false);
          $this->session->set_userdata(array('app_numbr' => $this->app_numbr));
          $this->session->set_userdata(array('app_descre' => $this->app_descre));  
          $this->session->set_userdata(array("app_metacontent"=> $this->app_metacontent));
          $input = array_merge($input, array('USL_STATUS'=>1));
          $this->crud->useTable($this->table_akseslog);
          $this->crud->save($input);
          if(!$relogin){
            redirect('home/welcome');
          }else{
            echo 1;
          }        
        }else{
          $input = array_merge($input, array('USL_STATUS'=>0));
          $this->crud->useTable($this->table_akseslog);
          $this->crud->save($input);
          $data["messg"] = "<p>Kombinasi ID Pengguna/Kata Sandi anda tidak tepat. Silahkan coba lagi</p>";
          
          if(!$relogin){
            redirect('login/2');
          }else{
            echo "Kombinasi ID Pengguna/Kata Sandi anda tidak tepat. Silahkan coba lagi";
          }
        }
      }    
    }else{
      $input = array_merge($input, array('USL_STATUS'=>0));
      $this->crud->useTable($this->table_akseslog);
      $this->crud->save($input);
      $data["messg"] = "<p>Kombinasi ID Pengguna/Kata Sandi anda tidak tepat. Silahkan coba lagi</p>";
      
      if(!$relogin){
        redirect('login/2');
      }else{
        echo "Kombinasi ID Pengguna/Kata Sandi anda tidak tepat. Silahkan coba lagi";
      }
    }
  }
  function failed($messg=null){
    $data['title'] = $this->title;
    if(isset($messg)){
      switch ($messg) {
        case '1':
          $data["messg"] = "<p>Login failed, another session is active</p><a href=/login/destroy_session>Destroy Session</a>?";
          break;
        case '2':
          $data["messg"] = "<p>Kombinasi ID Pengguna/Kata Sandi anda tidak tepat. Silahkan coba lagi</p>";
          break;
        case '3':
          $data["messg"] = "<p>ID Pengguna/Kata Sandi Active Directory tidak aktif. Mohon hubungi Administrator</p>";
          break;
        case '4':
          $data["messg"] = "<p>Anda tidak diizinkan untuk mengakses aplikasi ini</p>";
          break;
          
      }
    }
    $data['title'] = $this->app_descre;
    $this->load->view('v_login'.$this->login_id, $data);
  }
  function bye() {
    // $this->common->rowLock_logout();
    $this->authlogin->logout('login');
  }

  function checkSessiontime(){
    echo $this->config->item("sess_expiration");
  }  
  function getValidate(){
    // $this->common->debug_post();
    $cnfVALIDS = $this->input->post('valids');////oper nama confignya, untuk nilai-->ambil dari config
    $cnfADMINS = $this->input->post('admins');
    $cnfACTION = $this->input->post('action');
    $cnfNOTRAN = $this->input->post('notran');
    $cnfREASON = $this->input->post('alasanss');

    $arrACTION = explode("-", $cnfACTION);
    $cnfAPPLIC = $arrACTION[0];
    $cnfACTION = $arrACTION[1];
    // $this->common->debug_array($arrACTION);
    $valid = $this->authlogin->getValidatelogin($this->input->post('username'), $this->input->post('password'),$cnfVALIDS, $cnfADMINS);

    if($valid==1){
      $message = 1;
      $errno = 0;
    }else{
      switch ($valid) {
        case '000' : //Error Configurasi config.php
          $message = "";
          break;
        case '001' : //User salah
        case '002' : //Kata Sandi Salah
          $message = "ID Pengguna atau Kata Sandi salah!";
          break;
        case '003' : //
          $message = "Anda tidak bisa melakukan otorisasi!";
          break;
        case '004' : //
          $message = "Posisi anda tidak mempunyai otorisasi untuk merubah data!";
          break;
        default:
          $message = "Periksa ID Pengguna atau Kata Sandi anda!";
          break;
      }
    }
    /*
    //2::edit
    //3::hapus
    */
    $this->crud->useTable("AUTLOG");
    $input = array(
        "AUT_APPLIC"=>$cnfAPPLIC,
        "AUT_ACTION"=>$cnfACTION,
        "AUT_USRNAM"=>$this->session->userdata('USR_LOGINS'),
        "AUT_APRNAM"=>$this->input->post('username'),
        "AUT_ERRNUM"=>($valid==1 ? "999" : $valid),
        "AUT_DESCRE"=>$cnfREASON,
        "AUT_NOTRAN"=>$cnfNOTRAN,
    );
    $this->crud->save($input);    
    echo $message;
  }
}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 ** C-R-U-D Model
 ** @pacge
 ** CodeIgniter
 *** @subpackage Model
 * @category	 Create-Retrieve-Update-Delete
 * *
 * @author		 detanto / detanto[at]gmail.com
 * @project owner  lppm
 *
 * Modified October 2013.
 */
 
class M_common extends CI_Model {
 	function __construct() {
		parent::__construct();
	    $this->username = $this->session->userdata('USR_LOGINS');
	    $this->datesave = date('Y-m-d H:i:s');
	    // $this->app_numbr = $this->config->item('app_numbr');
		$this->app_numbr = $this->session->userdata('app_numbr');
	    $this->usrlevel = $this->session->userdata('USR_LEVELS');
		$this->table_user = $this->config->item('tbl_user');
		$this->table_menu = $this->config->item('tbl_menu');
		$this->table_common = $this->config->item('tbl_common');
		$this->table_usermenu = $this->config->item('tbl_usermenu');
		$this->table_akseslog = $this->config->item('tbl_akseslog');
        $this->security_level = $this->config->item('security_level');

        if($this->security_level!="level"){
            $this->fieldFKUSER = "MNU_FKUSER";
			$this->fieldLOGIN = "USR_LOGINS";
        }else{
            $this->fieldFKUSER = "MNU_LEVELS";
			$this->fieldLOGIN = "USR_LEVELS";
        }
 	}

	function getCommon_idents($type, $idents=null, $headcd=null){
		$data = "";
		$this->db->select('COM_HEADCD, COM_TYPECD, COM_DESCR1, COM_DESCR2, COM_IDENTS');
		$this->db->from($this->table_common);
		if($idents!=""){
		$this->db->where('COM_IDENTS',$idents);  
		}

		if($headcd!=""){
		$this->db->where('COM_HEADCD',$headcd);
		$this->db->where("COM_TYPECD <> 0");
		}    
		$query = $this->db->get();
		switch($type){
		case "1" :
			$data = ($this->returnArray) ? $query->result_array() : $query->result();
			break;
		case "2" :
			if($query->num_rows()>0){
			$data = $query->row()->COM_DESCR1;  
			}else{
			$data = "Tidak Ada";
			}
			break;
		case "3" :
			$data[''] = '';
			foreach($query->result() as $row){
				$data[$row->COM_TYPECD] = $row->COM_DESCR1;
			}   
			break; 
		case "4" :
			$data[''] = '';
			foreach($query->result() as $row){
				$data[$row->COM_DESCR2] = $row->COM_DESCR1;
			}   
			break;
		}
		return $data;   
	}
	function getAksesistem_list($type){
	    if($type=="gagal"){
	      $this->db->where('USL_STATUS = 0');
	      $this->db->where("USL_USRDAT between '" . date("Y-m-d H:i:s", strtotime("-30 days")) . "' and '" . date("Y-m-d H:i:s"). "'");
	    }
	    if($type=="berhasil"){
	      $this->db->where('USL_STATUS = 1');
	      $this->db->where("USL_USRDAT between '" . date("Y-m-d H:i:s", strtotime("-30 days")) . "' and '" . date("Y-m-d H:i:s"). "'");
	    }    
	    $this->db->select('USL_IDENTS, USL_USRNAM,USL_USRDAT,USL_ADDRES');
	    $this->db->from($this->table_akseslog);
		$result = $this->db->get();
		return $result;		
	}
	function getCountakses($type){
	    if($type=="gagal"){
	      $this->db->where('USL_STATUS = 0');
	      $this->db->where("USL_USRDAT between '" . date("Y-m-d H:i:s", strtotime("-30 days")) . "' and '" . date("Y-m-d H:i:s"). "'");
	    }
	    if($type=="berhasil"){
	      $this->db->where('USL_STATUS = 1');
	      $this->db->where("USL_USRDAT between '" . date("Y-m-d H:i:s", strtotime("-30 days")) . "' and '" . date("Y-m-d H:i:s"). "'");
	    }
	    $this->db->from($this->table_akseslog);
	    return $this->db->count_all_results();
	}
  	function getRiwayatakses_list(){
	    $this->db->select("USL_IDENTS, USL_USRNAM, USL_ADDRES,USL_BROWSR");//
	    $this->db->select($this->common->formatdatedb(1, "USL_USRDAT", "DD Mon YY, HH24:MI:SS") . " USL_USRDAT", false);
	    $this->db->select("USL_LATITU,USL_LONGTI");
	    $this->db->select("CASE WHEN USL_STATUS = 1 THEN 'SUKSES' ELSE 'GAGAL' END USL_STATUS");
	    $this->db->from($this->table_akseslog . " a");
	    $this->db->join($this->table_user . " b","a.USL_USRNAM = b.USR_LOGINS","LEFT OUTER");
	    if($this->config->item('humanapp')){
	      $this->db->select("EMP_FNAMES USR_FNAMES");
	      $this->db->join("HRD_EMPLOY c","b.USR_FKEMPL = c.EMP_IDENTS","LEFT OUTER");
	    }else{
	      $this->db->select("USR_FNAMES");
	    }
		if($this->usrlevel>2){
			if($this->usrlevel==3){
				$this->db->where("b.USR_USRNAM", $this->username);
				$this->db->or_where("USL_USRNAM", $this->username);
			}else{
				$this->db->where("USL_USRNAM", $this->username);
			}
		}
	    $this->db->where('USL_APPLIC', $this->app_numbr);
		$hasil = $this->crud->returnforjson(array('order_by'=>'USL_IDENTS desc'));			
	    return $hasil;  	
	} 
  	function getUseraccess_list($USER=null){
	    $this->db->select($this->common->formatdatedb(1, "USL_USRDAT", "Mon") . " USL_USRDAT", false);
	    $this->db->select('COUNT(*) USL_TOTAL, MONTH(USL_USRDAT) BULAN');
	    $this->db->from($this->table_akseslog);
	    if($USER!=""){
	      if($this->usrlevel!=1){
	        $this->db->where("USL_USRNAM", $USER);
	      }
	    }
	    $this->db->where($this->common->formatdatedb(1, "USL_USRDAT", "YYYY") . " = '" . DATE('Y') . "'");
	    $this->db->where('USL_APPLIC', $this->app_numbr);
		$this->db->group_by($this->common->formatdatedb(1, "USL_USRDAT", "Mon"), false);
		$this->db->group_by("MONTH(USL_USRDAT) ");
		$this->db->order_by("MONTH(USL_USRDAT)");
	    $query = $this->db->get();
	    return $query;
	}
    function getAktivitas_edit($log_idents){
		$this->db->from("t_log_aktivitas");
		$this->db->where("log_idents", $log_idents);
		$query = $this->db->get();
		$row = $query->row();
		return $row;
	}
	  
    function getAktivitas_list($from=null){
		$this->db->select("app_descre, log_idents USL_IDENTS, log_usrnam USL_USRNAM, log_address USL_ADDRES");
		$this->db->select($this->common->formatdatedb(1, "log_usrdat", "DD Mon YYYY, HH24:MI:SS") . " USL_USRDAT", false);
		$this->db->select("log_from USL_MODULE, log_action USL_ACTION, null USL_NOKTPA, null bp_name, log_table");
		$this->db->from("t_log_aktivitas a", false);
		$this->db->join("m_application b","a.log_appnumbr = b.app_applic", "INNER", false);
	    $this->db->join($this->table_user . " c","a.log_usrnam = c.USR_LOGINS","LEFT OUTER");
		if($this->usrlevel>2){
			if($this->usrlevel==3){
				$this->db->where("c.USR_USRNAM", $this->username);
				$this->db->or_where("log_usrnam", $this->username);
			}else{
				$this->db->where("log_usrnam", $this->username);
			}			
		}
		if($from!=null){
			if(strpos("A".$from, "~")>0){
				$from = explode("~", $from); 
				$this->db->where_in("log_from", $from);
			}else{
				$this->db->where("log_from", $from);
			}
		}
		if($this->app_numbr!="9999" && $this->app_numbr!="03013NH"){
			$this->db->where("log_appnumbr", $this->app_numbr);
		}
		// $this->db->get_compiled_select(1,1);
		$hasil = $this->crud->returnforjson(array('order_by'=>'USL_IDENTS desc'));
		return $hasil;
    }
  	function getUsersuccess_list(){
	    $this->db->select('USL_STATUS USL_STATUS');
	    $this->db->select('COUNT(*) USL_TOTAL');
	    $this->db->from($this->table_akseslog);
		// $this->db->where($this->common->formatdatedb(1, "USL_USRDAT", "YYYY") . " = '" . DATE('Y') . "'");
	    $this->db->where('USL_APPLIC', $this->app_numbr);
	    $this->db->group_by('USL_STATUS');
	    $query = $this->db->get();
		return $query;
  	}
  	function getUsercompare_list(){
	    $this->db->distinct();
	    $this->db->select($this->fieldFKUSER . " MNU_FKUSER");
	    $this->db->from($this->table_usermenu);
	    $this->db->where('MNU_APPLIC', $this->app_numbr);
	    $sql = $this->db->get_compiled_select();

	    $this->db->select('COM_DESCR1 USR_ACCESS');
	    $this->db->select('COUNT(*) USR_TOTAL');
	    $this->db->from($this->table_user . ' a');
	    $this->db->join($this->table_common . ' b', 'a.USR_LEVELS = b.COM_TYPECD AND b.COM_HEADCD = 9','LEFT OUTER');
	    $this->db->join("(".$sql.") c", "a." . $this->fieldLOGIN . " = c.MNU_FKUSER","INNER");
	    $this->db->group_by('COM_DESCR1');

		// $this->common->debug_sql(1,1);
	    $query = $this->db->get();
	    return $query;
  	}
	function getCommon_list($HEADCD){
		$this->db->select("CONCAT(COM_HEADCD, '-', COM_TYPECD) COM_IDENTS, COM_HEADCD, COM_TYPECD, COM_DESCR1, COM_DESCR2, COM_USRNAM, COM_USRDAT, COM_UPDNAM, COM_UPDDAT, COM_is_deleted");
		$this->db->from($this->table_common);
		$this->db->where("COM_HEADCD", $HEADCD);
		$this->db->where("COM_TYPECD <> '0'");
		$this->db->where("IFNULL(COM_is_deleted,0) <> 1");
		$hasil = $this->crud->returnforjson(array("order_by"=>"COM_IDENTS"));
		return $hasil;
	}	
}
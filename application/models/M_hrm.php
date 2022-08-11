<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_hrm extends CI_Model{
	var $username;
    var $empidents;
    var $hasil;
    var $isAdmin;
    var $deptmn;
    var $usrtypusr;
    var $usrauthrz;
    var $table_common;
	function __construct(){
        parent::__construct();
        $this->username = $this->session->userdata('USR_LOGINS');
        $this->usrlevel = $this->session->userdata('USR_LEVELS');
        $this->usrauthrz = $this->session->userdata('USR_AUTHRZ');
        $this->app_numbr = $this->session->userdata('app_numbr');
        $this->usrtypusr = $this->session->userdata('USR_TYPUSR');

		$this->table_user = $this->config->item('tbl_user');
		$this->table_menu = $this->config->item('tbl_menu');
		$this->table_common = $this->config->item('tbl_common');
		$this->table_usermenu = $this->config->item('tbl_usermenu');      
    }
    function getEmployee_list($status=null){
        $this->db->select("a.*");
        $this->db->select("b.COM_DESCR1 emp_workbased_desc, c.COM_DESCR1 emp_position_desc, d.COM_DESCR1 emp_program_desc");
        $this->db->from("t_hrd_employee a");
        $this->db->join("t_mas_common b","a.emp_workbased = b.COM_TYPECD AND b.COM_HEADCD = 19 AND b.COM_TYPECD <> 0","LEFT OUTER");
        $this->db->join("t_mas_common c","a.emp_position = c.COM_TYPECD AND c.COM_HEADCD = 20 AND b.COM_TYPECD <> 0","LEFT OUTER");
        $this->db->join("t_mas_common d","a.emp_program = d.COM_TYPECD AND d.COM_HEADCD = 22 AND b.COM_TYPECD <> 0","LEFT OUTER");
        if($status!=null){
            $this->db->where("emp_employment", $status);
        }
        // $this->db->join("t_mas_program d","a.emp_program = d.prg_idents","LEFT OUTER");
        $hasil = $this->crud->returnforjson(array('order_by'=>'emp_usrdat desc'));
        return $hasil;
    }
    function getEmployeesalary_list($emp_idents){
        $this->db->select("ems_idents, ems_grantid, grn_code ems_grant_code, grn_shortname ems_grant_shortname");
        $this->db->select("ems_wbscode, ems_refkey3salary, ems_refkey3benefit");
        $this->db->select("ems_percentage, emp_funds, ems_busarea");
        $this->db->from("t_hrd_employee_salary a");
        $this->db->join("t_mas_grant b","a.ems_grantid = b.grn_idents","INNER");
        $this->db->where("IFNULL(ems_is_deleted,0) <> 1");
        $this->db->where("ems_empidents", $emp_idents);
        $this->db->order_by('ems_idents desc');
        $result = $this->db->get();
        $hasil['type'] = 'cmb';
        $hasil['Hasil'] = $result->result();
        return $hasil;
    }
    function getEmployeeexperience_list($emp_idents){
        $this->db->select("emx_idents, emx_empidents, emx_year, emx_company, emx_position, emx_notes");
        $this->db->from("t_hrd_employee_experience a");
        $this->db->where("IFNULL(emx_is_deleted,0) <> 1");
        $this->db->where("emx_empidents", $emp_idents);
        $this->db->order_by('emx_idents desc');
        $result = $this->db->get();
        $hasil['type'] = 'cmb';
        $hasil['Hasil'] = $result->result();
        return $hasil;
    }
    function getEmployeeexperience_edit($emx_idents){
        $this->db->from("t_hrd_employee_experience");
        $this->db->where("emx_idents", $emx_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;
    }
    function getEmployee_edit($emp_idents, $opt=1){
        $this->db->from("t_hrd_employee");
        if($opt==1){
            $this->db->where("emp_idents", $emp_idents);
            $result = $this->db->get();
            if($result->num_rows()>0){
                $result = $result->row();
            }else{
                $result = null;
            }
        }else{
            $this->db->where("emp_wcsid", $emp_idents);
            switch($opt){
                case "2":
                    $result = $this->db->count_all_results();
                    break;
                case "3":
                    $result = $this->db->get();
                    if($result->num_rows()>0){
                        $result = $result->row();
                    }else{
                        $result = null;
                    }
                    break;
            }
        }
        return $result;
    }
    function getEmployeesalary_edit($ems_idents){
        $this->db->from("t_hrd_employee_salary");
        $this->db->where("ems_idents", $ems_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;
    }
    function getEmployeeskill_list($emp_idents){
        $this->db->select("a.*, b.COM_DESCR1 emk_proficiency_desc");
        $this->db->from("t_hrd_employee_skill a");
        $this->db->join("t_mas_common b","a.emk_proficiency = b.COM_TYPECD AND b.COM_HEADCD = 23 AND COM_TYPECD <> 0","LEFT OUTER");
        $this->db->where("IFNULL(emk_is_deleted,0) <> 1");
        $this->db->where("emk_empidents", $emp_idents);
        $this->db->order_by('emk_idents desc');
        $result = $this->db->get();
        $hasil['type'] = 'cmb';
        $hasil['Hasil'] = $result->result();
        return $hasil;
    }    

    function getEmployeeskill_edit($emk_idents){
        $this->db->from("t_hrd_employee_skill");
        $this->db->where("emk_idents", $emk_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;
    }
    function getEmployeetraining_list($emp_idents){
        $this->db->from("t_hrd_employee_training a");
        $this->db->where("IFNULL(emt_is_deleted,0) <> 1");
        $this->db->where("emt_empidents", $emp_idents);
        $this->db->order_by('emt_idents desc');
        $result = $this->db->get();
        $hasil['type'] = 'cmb';
        $hasil['Hasil'] = $result->result();
        return $hasil;
    }    

    function getEmployeetraining_edit($emt_idents){
        $this->db->from("t_hrd_employee_training");
        $this->db->where("emt_idents", $emt_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;
    }
}
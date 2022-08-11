<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_analisa extends CI_Model{
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
    }
    function getExpensedetail_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2, $anaRefkey, $refkeydetail=null, $grid=true){

        // $this->common->debug_array($from .">>" . $anaKind .">>" . $anaGrant .">>" . $anaYear .">>" . $anaMonth1 .">>" . $anaMonth2 .">>" . $anaRefkey .">>" . $refkeydetail);

		if($anaYear!=0){
			if($anaMonth1==0){
				$anaMonth1 = 1;
			}
			$period_1 = $anaYear . substr("00" . $anaMonth1,-2) . "01";
			// 
			$date_1=date_create($period_1);

			if($anaMonth2==0){
				$anaMonth2 = 12;
			}
			$period_2 = $anaYear . substr("00" . $anaMonth2,-2) . "01";
			// $this->common->debug_array("asdfasdf" . substr("00" . $anaMonth2,-2));
			$date_2=date_create($period_2);
	
			date_add($date_2,date_interval_create_from_date_string("1 month"));
			date_add($date_2,date_interval_create_from_date_string("-1 day"));
	
			$period_1 = date_format($date_1,"Y-m-d");
			$period_2 = date_format($date_2,"Y-m-d");
		}
        $orderbynya = "sad_grant";
        $this->db->select("sad_idents, sad_documenttype, sad_documentnumber, sad_glshorttext, sad_account,");
        $this->db->select("sad_debitcreditind, sad_postingkey, sad_amountindoccurr, sad_documentcurrency, sad_effexchangerate, sad_amountinlocalcurrency,");
        $this->db->select("sad_localcurrency, sad_costcenter, sad_wbselement, sad_businessarea, sad_fund, sad_grant,");
        $this->db->select("sad_assignment, sad_text, sad_referencekey1, sad_referencekey2, sad_referencekey3, sad_valuedate,");
        $this->db->select("sad_documentdate, sad_postingdate, sad_fiscalyear, sad_postingperiod, sad_reference,");
        $this->db->select("sad_username, sad_offsettaccounttype, sad_offsettingacctno");
        $this->db->from("t_sap_data a");
        $this->db->join("t_sap_data_detail b", "a.sap_idents = b.sad_sapidents", "INNER");
		$this->db->join("t_mas_grant c", "b.sad_grant = c.grn_code", "INNER");

		if($anaRefkey==1){
			$this->db->join("t_mas_grant_allocation d", "c.grn_idents = d.gra_grnidents AND b.sad_referencekey3 = d.gra_refkey3", "INNER");
            if($refkeydetail!="0"){
                $this->db->where("b.sad_referencekey3", $refkeydetail);
            }
		}
		if($anaKind!=0){
			$this->db->where("c.grn_kind", $anaKind);
		}

		if($anaGrant!=0){
            if($from=="graph"){
                $this->db->where("c.grn_code", $anaGrant);
            }else{
                $this->db->where("c.grn_idents", $anaGrant);    
            }
		}
		if($anaYear!=0){
            if($from=="bva"){
                $this->db->where("sad_postingdate <= '" . $period_1 . "'");
            }else{
                $this->db->where("sad_postingdate between '". $period_1 . "' and '" . $period_2 ."'");
            }
		}
        $this->db->where("IFNULL(sap_is_deleted,0) <> 1");
        $this->db->where("sad_referencekey3 <> 'NOT-RELEVANT'");
        // $this->db->limit(10);
        // $this->common->debug_sql(1,1);
        if($grid){
            $hasil = $this->crud->returnforjson(array('order_by'=>$orderbynya));
        }else{
            $this->db->order_by($orderbynya);
            $hasil = $this->db->get();
        }
        
        return $hasil;
    }
    function getExpensesummary_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2, $anaRefkey, $refkeydetail=null, $grid=true){
		$period_1 = $anaYear . $anaMonth1 . "01";
		$date_1=date_create($period_1);

		$period_2 = $anaYear . $anaMonth2 . "01";
		$date_2=date_create($period_2);

		date_add($date_2,date_interval_create_from_date_string("1 month"));
		date_add($date_2,date_interval_create_from_date_string("-1 day"));

		$period_1 = date_format($date_1,"Y-m-d");
		$period_2 = date_format($date_2,"Y-m-d");

        $this->db->from("t_sap_data a");
        $this->db->join("t_sap_data_detail b", "a.sap_idents = b.sad_sapidents", "INNER");
        $this->db->join("t_mas_grant c", "b.sad_grant = c.grn_code", "INNER");
        $this->db->join("t_mas_sponsor d", "c.grn_sponsor = d.spn_nomor", "INNER");
        $this->db->join("t_mas_common x", "c.grn_kind = x.COM_TYPECD AND x.COM_HEADCD = 98", "LEFT");

		if($anaGrant!=0){            
            $this->db->join("t_mas_grant_allocation e", "c.grn_idents = e.gra_grnidents AND b.sad_referencekey3 = e.gra_refkey3", "INNER");
            $this->db->select("gra_itemdescription, sad_referencekey3, SUM(sad_amountindoccurr) sad_expense");
            if($from=="graph"){
                $this->db->where("c.grn_code", $anaGrant);
            }else{
                $this->db->where("c.grn_idents", $anaGrant);    
            }
            $this->db->group_by("gra_itemdescription, sad_referencekey3");

            $order_by = "gra_itemdescription";
		}else{
            if($anaRefkey==1){
                $this->db->join("t_mas_grant_allocation e", "c.grn_idents = e.gra_grnidents AND b.sad_referencekey3 = e.gra_refkey3", "INNER");
            }            
            $this->db->select("grn_idents, sad_grant, grn_kind, grn_shortname, grn_sponsor");
            $this->db->select("spn_name grn_sponsor_name, grn_datestart, grn_dateend");
            $this->db->select("SUM(sad_amountindoccurr) sad_expense, x.COM_DESCR1 grn_kind_desc");
            $this->db->group_by("grn_idents, sad_grant, grn_kind, grn_shortname, grn_sponsor, spn_name, grn_datestart, grn_dateend, x.COM_DESCR1");

            $order_by = "grn_sponsor";
        }

		if($anaYear!=0){
			$this->db->where("sad_postingdate between '". $period_1 . "' and '" . $period_2 ."'");
		}
		if($anaKind!=0){
			$this->db->where("c.grn_kind", $anaKind);
		}
        $this->db->where("IFNULL(sap_is_deleted,0) <> 1");
        $this->db->where("sad_referencekey3 <> 'NOT-RELEVANT'");
        // $this->common->debug_sql(1,1);
        if($grid){
            $hasil = $this->crud->returnforjson(array('order_by'=>$order_by));
        }else{
            $this->db->order_by($order_by);
            $hasil = $this->db->get();
        }
        
        return $hasil;
    }
    function getBVAdetail_list($anaKind, $anaGrant, $anaYear, $anaMonth1, $grid=true){

		$period_1 = $anaYear . $anaMonth1 . "01";
		$date_1=date_create($period_1);
		$period_1 = date_format($date_1,"Y-m-d");

		$this->db->select("sad_grant, sad_referencekey3, SUM(sad_amountinlocalcurrency) sad_amountinlocalcurrency");
		$this->db->from("t_sap_data a");
		$this->db->join("t_sap_data_detail b","a.sap_idents = b.sad_sapidents","INNER");
		$this->db->join("t_mas_grant c","b.sad_grant = c.grn_code","INNER");
		$this->db->where("sad_postingdate <= '" . $period_1 . "'");
        $this->db->where("IFNULL(sap_is_deleted,0) <> 1");
        $this->db->where("sad_referencekey3 <> 'NOT-RELEVANT'");
		if($anaKind!=0){
			$this->db->where("c.grn_kind", $anaKind);
		}
		if($anaGrant!=0){
			$this->db->where("c.grn_idents", $anaGrant);
		}
		$this->db->group_by("sad_grant, sad_referencekey3");

		$sqlExpense = $this->db->get_compiled_select();

        $this->db->select("grn_code, gra_idents, gra_grnidents, gra_itemdescription, gra_refkey3, gra_budget, 0 gra_budget_direct, 0 gra_budget_icr");
        $this->db->from("t_mas_grant_allocation a");
        $this->db->join("t_mas_grant b", "a.gra_grnidents = b.grn_idents", "INNER");
        $this->db->where("IFNULL(gra_is_deleted,0) <> 1");

        $sql1 = $this->db->get_compiled_select();

        $this->db->select("grn_code, gra_idents, gra_grnidents, gra_itemdescription, gra_refkey3, 0 gra_budget, gra_budget gra_budget_direct, 0 gra_budget_icr");
        $this->db->from("t_mas_grant_allocation a");
        $this->db->join("t_mas_grant b", "a.gra_grnidents = b.grn_idents", "INNER");
        $this->db->where("IFNULL(gra_is_deleted,0) <> 1");
        $this->db->where("gra_type = 1");

        $sql2 = $this->db->get_compiled_select();

        $this->db->select("grn_code, gra_idents, gra_grnidents, gra_itemdescription, gra_refkey3, 0 gra_budget, 0 gra_budget_direct, gra_budget gra_budget_icr");
        $this->db->from("t_mas_grant_allocation a");
        $this->db->join("t_mas_grant b", "a.gra_grnidents = b.grn_idents", "INNER");
        $this->db->where("IFNULL(gra_is_deleted,0) <> 1");
        $this->db->where("gra_type = 2");

        $sql3 = $this->db->get_compiled_select();

        $this->db->select("grn_code, gra_idents, gra_grnidents, gra_itemdescription, gra_refkey3");
        $this->db->select("SUM(gra_budget) gra_budget, SUM(gra_budget_direct) gra_budget_direct, SUM(gra_budget_icr) gra_budget_icr");
        $this->db->from("(" . $sql1 . " UNION ALL " .$sql2. " UNION ALL " .$sql3. ") a", false);
        $this->db->group_by("grn_code, gra_idents, gra_grnidents, gra_itemdescription, gra_refkey3");

        $sql_budget = $this->db->get_compiled_select();

        $this->db->select("grn_code, gra_idents, gra_grnidents, gra_itemdescription, gra_refkey3, gra_budget, gra_budget_direct, gra_budget_icr");
        $this->db->select("SUM(sad_amountinlocalcurrency) grn_expense");
        $this->db->from("(" . $sql_budget . ") a", false);
        $this->db->join("(" . $sqlExpense . ") d", "a.grn_code = d.sad_grant AND a.gra_refkey3 = d.sad_referencekey3", "LEFT OUTER", false);
        $this->db->group_by("grn_code, gra_idents, gra_grnidents, gra_itemdescription, gra_refkey3, gra_budget, gra_budget_direct, gra_budget_icr");

        $sql_budget2 = $this->db->get_compiled_select();

        // $this->common->debug_array($sql_budget2);
        // grn_expense
        // grn_remaining
        // grn_burnrate
        // grn_remainingperiod

        $this->db->from("t_mas_grant a");

        if($anaGrant==0){
            $this->db->select("grn_idents, a.grn_code, grn_shortname, a.grn_kind, grn_datestart, grn_dateend");
            $this->db->select("SUM(gra_budget) grn_totalbudget, SUM(gra_budget_direct) grn_directbudget, SUM(gra_budget_icr) grn_icrbudget");
            $this->db->select("SUM(sad_amountinlocalcurrency) grn_expense");
            $this->db->select("SUM(gra_budget_direct) - SUM(sad_amountinlocalcurrency) grn_remaining");
            $this->db->select("SUM(sad_amountinlocalcurrency)/SUM(gra_budget_direct) * 100 grn_burnrate");
            $this->db->join("(" . $sql_budget . ") b", "a.grn_idents = b.gra_grnidents", "INNER", false);
            $this->db->join("(" . $sqlExpense . ") d", "a.grn_code = d.sad_grant AND b.gra_refkey3 = d.sad_referencekey3", "LEFT OUTER", false);
            $this->db->group_by("grn_idents, a.grn_code, grn_shortname, a.grn_kind, grn_datestart, grn_dateend");
            $order_by = "grn_idents";
        }else{
            $this->db->select("grn_idents, a.grn_code, grn_shortname, a.grn_kind, grn_datestart, grn_dateend, gra_itemdescription, gra_refkey3, gra_budget grn_totalbudget, gra_type");
            $this->db->select("SUM(sad_amountinlocalcurrency) grn_expense");
            $this->db->select("gra_budget - SUM(sad_amountinlocalcurrency) grn_remaining");
            $this->db->select("SUM(sad_amountinlocalcurrency)/gra_budget * 100 grn_burnrate");
            $this->db->join("t_mas_grant_allocation b", "a.grn_idents = b.gra_grnidents", "INNER", false);
            $this->db->join("(" . $sqlExpense . ") d", "a.grn_code = d.sad_grant AND b.gra_refkey3 = d.sad_referencekey3", "LEFT OUTER", false);
            $this->db->group_by("grn_idents, a.grn_code, grn_shortname, a.grn_kind, grn_datestart, grn_dateend, gra_itemdescription, gra_refkey3, gra_budget, gra_type");
            $order_by = "gra_type";
        }

		if($anaYear!=null){
			// $this->db->where("sad_postingdate between '". $period_1 . "' and '" . $period_2 ."'");
		}
		if($anaKind!=0){
			$this->db->where("a.grn_kind", $anaKind);
		}
		if($anaGrant!=0){
			$this->db->where("a.grn_idents", $anaGrant);
		}

        if($grid){
            $hasil = $this->crud->returnforjson(array('order_by'=>$order_by));
        }else{
            $hasil = $this->db->get();
        }
        
        return $hasil;
    }
    function getMismatch_list($anaGrant, $anaYear, $anaMonth1){
        // $this->common->debug_array($anaMonth1);
		$period_1 = $anaYear . $anaMonth1 . "01";
		$date_1=date_create($period_1);

		$period_1 = date_format($date_1,"Y-m-d");

		date_add($date_1,date_interval_create_from_date_string("1 month"));
		date_add($date_1,date_interval_create_from_date_string("-1 day"));

		$period_2 = date_format($date_1,"Y-m-d");

        $this->db->select("a.*");
		$this->db->from("t_sap_data_detail a");
		$this->db->join("t_mas_grant b","a.sad_grant = b.grn_code","INNER");
        $this->db->join("t_mas_grant_allocation c", "b.grn_idents = c.gra_grnidents AND a.sad_referencekey3 = c.gra_refkey3", "LEFT OUTER");
        $this->db->where("c.gra_idents is null");
        $this->db->where("a.sad_referencekey3 <> 'NOT-RELEVANT'");
        // $this->db->where("LENGTH(IFNULL(a.sad_referencekey3,'')) > 0");
		if($anaGrant!=0){
			$this->db->where("a.sad_grant", $anaGrant);
		}
		if($anaYear!=0 && $anaMonth1!=0){
			$this->db->where("a.sad_postingdate between '". $period_1 . "' and '" . $period_2 ."'");
		}else{
            if($anaMonth1==0 && $anaYear!=0){
                $this->db->where("YEAR(a.sad_postingdate)", $anaYear);
            }
        }
        // $this->db->limit(10);
        // $this->common->debug_sql(1,1);
        
        $hasil = $this->crud->returnforjson(array('order_by'=>"a.sad_idents"));
        return $hasil;
    }
    function getSalarygrant_list($from, $type, $grant, $source, $series=null){
        $this->db->select("grn_idents, 'Salary' ems_type, c.grn_code, grn_shortname, grn_sponsor, spn_name grn_sponsor_name, ems_refkey3salary, sum(a.emp_salary*ems_percentage/100) ems_value");
        $this->db->from("t_hrd_employee a");
		$this->db->join("t_hrd_employee_salary b", "a.emp_idents = b.ems_empidents", "INNER");
		$this->db->join("t_mas_grant c", "b.ems_grantid = c.grn_idents", "INNER");
		$this->db->join("t_mas_grant_allocation d", "c.grn_idents = d.gra_grnidents AND b.ems_refkey3salary = d.gra_refkey3", "LEFT");
        $this->db->join("t_mas_sponsor f", "c.grn_sponsor = f.spn_nomor", "INNER");
        if($grant!=0){
            if($from=="form"){
                $this->db->where("c.grn_idents", $grant);
            }else{
                $this->db->where("c.grn_code", $grant);
            }
        }
        switch($source){
            case "location":
                $this->db->select("x.COM_DESCR1 emp_workbased");
                $this->db->join("t_mas_common x", "a.emp_workbased = x.COM_TYPECD AND x.COM_HEADCD = 19", "LEFT");
                $this->db->group_by("x.COM_DESCR1");
                if($series!="0" && $series!="all"){
                    if(is_numeric($series)){
                        $this->db->where("emp_workbased", $series);
                    }else{
                        $this->db->where("x.COM_DESCR1", $series);
                    }
                }
                break;
            case "level":
                $this->db->select("x.COM_DESCR1 emp_level");
                $this->db->join("t_mas_common x", "a.emp_level = x.COM_TYPECD AND x.COM_HEADCD = 24", "LEFT");
                $this->db->group_by("x.COM_DESCR1");
                // $this->common->debug_array($series);
                if($series!="0" && $series!="all"){
                    if(is_numeric($series)){
                        $this->db->where("emp_level", $series);
                    }else{
                        $this->db->where("x.COM_DESCR1", $series);
                    }
                }
                break;
        }
        $this->db->group_by("grn_idents, c.grn_code, grn_shortname, ems_refkey3salary");

        $sql1 = $this->db->get_compiled_select();

        $this->db->select("grn_idents, 'Benefit' ems_type, c.grn_code, grn_shortname, grn_sponsor, spn_name grn_sponsor_name, ems_refkey3salary, sum(a.emp_salary_functional*ems_percentage/100) ems_value");
        $this->db->from("t_hrd_employee a");
		$this->db->join("t_hrd_employee_salary b", "a.emp_idents = b.ems_empidents", "INNER");
		$this->db->join("t_mas_grant c", "b.ems_grantid = c.grn_idents", "INNER");
		$this->db->join("t_mas_grant_allocation e", "c.grn_idents = e.gra_grnidents AND b.ems_refkey3benefit = e.gra_refkey3", "LEFT");
        $this->db->join("t_mas_sponsor f", "c.grn_sponsor = f.spn_nomor", "INNER");
        if($grant!=0){
            if($from=="form"){
                $this->db->where("c.grn_idents", $grant);
            }else{
                $this->db->where("c.grn_code", $grant);
            }
        }
        switch($source){
            case "location":
                $this->db->select("x.COM_DESCR1 emp_workbased");
                $this->db->join("t_mas_common x", "a.emp_workbased = x.COM_TYPECD AND x.COM_HEADCD = 19", "LEFT");
                $this->db->group_by("x.COM_DESCR1");
                // $this->common->debug_array($series);
                if($series!="0" && $series!="all"){
                    if(is_numeric($series)){
                        $this->db->where("emp_workbased", $series);
                    }else{
                        $this->db->where("x.COM_DESCR1", $series);
                    }
                }
                break;
            case "level":
                $this->db->select("x.COM_DESCR1 emp_level");
                $this->db->join("t_mas_common x", "a.emp_level = x.COM_TYPECD AND x.COM_HEADCD = 24", "LEFT");
                $this->db->group_by("x.COM_DESCR1");
                // $this->common->debug_array($series);
                if($series!="0" && $series!="all"){
                    if(is_numeric($series)){
                        $this->db->where("emp_level", $series);
                    }else{
                        $this->db->where("x.COM_DESCR1", $series);
                    }
                }
                break;
        }
        $this->db->group_by("grn_idents, c.grn_code, grn_shortname, ems_refkey3salary");

		$sql2 = $this->db->get_compiled_select();

		$this->db->select("grn_code, grn_shortname, grn_sponsor, grn_sponsor_name");//, spn_name, grn_sponsor_name, ems_refkey3salary, 
        $this->db->select("CONCAT(grn_code, ' ', grn_shortname) group_row");
        $this->db->select("SUM(ems_value) ems_value");
		$this->db->from("(" . $sql1 . " UNION ALL " . $sql2 . ") as ax", false);

		$this->db->group_by("grn_code, grn_shortname, grn_sponsor, grn_sponsor_name");//, spn_name, grn_sponsor_name, ems_refkey3salary");
        $this->db->order_by("grn_code");        
        switch($source){
            case "location":
                $this->db->select("emp_workbased");
                $this->db->group_by("emp_workbased");
                break;
            case "level":
                $this->db->select("emp_level");
                $this->db->group_by("emp_level");
                break;
            default:
                $this->db->select("ems_type");
                $this->db->group_by("ems_type");
                $this->db->order_by("ems_type");
                break;
        }

        // $this->common->debug_sql(1,1);
        $hasil = $this->crud->returnforjson(array('order_by'=>"grn_code"));
        return $hasil;
    }
    function getSalarygrantdetail_list($from, $type, $grant, $source, $series=null){
        $this->db->select("emp_idents, emp_wcsid, emp_name, c.grn_code, grn_shortname, grn_sponsor, spn_name grn_sponsor_name");
        $this->db->from("t_hrd_employee a");
		$this->db->join("t_hrd_employee_salary b", "a.emp_idents = b.ems_empidents", "INNER");
		$this->db->join("t_mas_grant c", "b.ems_grantid = c.grn_idents", "INNER");
        $this->db->join("t_mas_sponsor f", "c.grn_sponsor = f.spn_nomor", "INNER");
        if($grant!=0){
            if($from=="form"){
                $this->db->where("c.grn_idents", $grant);
            }else{
                $this->db->where("c.grn_code", $grant);
            }            
        }
        switch($source){
            case "location":
                $this->db->select("x.COM_DESCR1 emp_workbased");
                $this->db->join("t_mas_common x", "a.emp_workbased = x.COM_TYPECD AND x.COM_HEADCD = 19", "LEFT");
                if($series!="0" && $series!="all"){
                    if(is_numeric($series)){
                        $this->db->where("emp_workbased", $series);
                    }else{
                        $this->db->where("x.COM_DESCR1", $series);
                    }
                }
                break;
            case "level":
                $this->db->select("x.COM_DESCR1 emp_level");
                $this->db->join("t_mas_common x", "a.emp_level = x.COM_TYPECD AND x.COM_HEADCD = 24", "LEFT");
                if($series!="0" && $series!="all"){
                    if(is_numeric($series)){
                        $this->db->where("emp_level", $series);
                    }else{
                        $this->db->where("x.COM_DESCR1", $series);
                    }
                }
                break;
            default:
                break;
        }
        switch($series){
            case "Salary":
                $this->db->select("a.emp_salary*ems_percentage/100 ems_value");
                $this->db->join("t_mas_grant_allocation d", "c.grn_idents = d.gra_grnidents AND b.ems_refkey3salary = d.gra_refkey3", "LEFT");
                break;
            case "Benefit":
                $this->db->select("a.emp_salary_functional*ems_percentage/100 ems_value");
                $this->db->join("t_mas_grant_allocation e", "c.grn_idents = e.gra_grnidents AND b.ems_refkey3benefit = e.gra_refkey3", "LEFT");
                break;
            default:
                $this->db->select("a.emp_salary*ems_percentage/100 ems_salary, 0 ems_benefit, emp_salary, ems_percentage");
                $this->db->join("t_mas_grant_allocation d", "c.grn_idents = d.gra_grnidents AND b.ems_refkey3salary = d.gra_refkey3", "LEFT");
                
                $sql1 = $this->db->get_compiled_select();

                $this->db->select("emp_idents, emp_wcsid, emp_name, c.grn_code, grn_shortname, grn_sponsor, spn_name grn_sponsor_name");
                switch($source){
                    case "location":
                        $this->db->select("x.COM_DESCR1 emp_workbased");
                        $this->db->join("t_mas_common x", "a.emp_workbased = x.COM_TYPECD AND x.COM_HEADCD = 19", "LEFT");
                        if($series!="0" && $series!="all"){
                            if(is_numeric($series)){
                                $this->db->where("emp_workbased", $series);
                            }else{
                                $this->db->where("x.COM_DESCR1", $series);
                            }
                        }
                        break;
                    case "level":
                        $this->db->select("x.COM_DESCR1 emp_level");
                        $this->db->join("t_mas_common x", "a.emp_level = x.COM_TYPECD AND x.COM_HEADCD = 24", "LEFT");
                        if($series!="0" && $series!="all"){
                            if(is_numeric($series)){
                                $this->db->where("emp_level", $series);
                            }else{
                                $this->db->where("x.COM_DESCR1", $series);
                            }
                        }
                        break;
                    default:
                        break;
                }                
                $this->db->select("0 ems_salary, a.emp_salary_functional*ems_percentage/100 ems_benefit, emp_salary, ems_percentage");
                $this->db->from("t_hrd_employee a");
                $this->db->join("t_hrd_employee_salary b", "a.emp_idents = b.ems_empidents", "INNER");
                $this->db->join("t_mas_grant c", "b.ems_grantid = c.grn_idents", "INNER");
                $this->db->join("t_mas_grant_allocation d", "c.grn_idents = d.gra_grnidents AND b.ems_refkey3benefit = d.gra_refkey3", "LEFT");
                $this->db->join("t_mas_sponsor f", "c.grn_sponsor = f.spn_nomor", "INNER");
                if($grant!=0){
                    if($from=="form"){
                        $this->db->where("c.grn_idents", $grant);
                    }else{
                        $this->db->where("c.grn_code", $grant);
                    }
                }
                $sql2 = $this->db->get_compiled_select();

                $this->db->select("emp_idents, emp_wcsid, emp_name, grn_code, grn_shortname, grn_sponsor, grn_sponsor_name, emp_salary, ems_percentage");
                $this->db->select("SUM(ems_salary) ems_salary, SUM(ems_benefit) ems_benefit");
                $this->db->from("(" . $sql1 . " UNION ALL " . $sql2 . ") as ax", false);
                $this->db->group_by("emp_idents, emp_wcsid, emp_name, grn_code, grn_shortname, grn_sponsor, grn_sponsor_name, emp_salary, ems_percentage");

                switch($source){
                    case "location":
                        $this->db->select("emp_workbased");
                        $this->db->group_by("emp_workbased");
                        break;
                    case "level":
                        $this->db->select("emp_level");
                        $this->db->group_by("emp_level");
                        break;
                    default:
                        break;
                }
                break;
        }
        // $this->common->debug_sql(1,1);
        $hasil = $this->crud->returnforjson(array('order_by'=>"emp_wcsid"));
        return $hasil;
    }
    function getCorecost($grn_code, $bva_account, $fiscal_year, $active){
        $this->db->distinct();
        $this->db->select("bva_account, bva_shortname, bvd_glaccount");
        $this->db->from("t_mas_bvaccount a");
        $this->db->join("t_mas_bvaccount_detail b", "a.bva_account = b.bvd_bvaaccount", "INNER");
        $this->db->where("IFNULL(b.bvd_is_deleted,0) <> 1");
        $this->db->where("IFNULL(a.bva_is_deleted,0) <> 1");
        if($bva_account!=0){
            $this->db->where("bva_account", $bva_account);
        }
        $rsl = $this->db->get();

        $arrBVA = array();
        $sqlgrant = "a.grn_idents, a.grn_code, a.grn_shortname";
        $sql_awal = null;
        $sql_tengah = null;
        $rc = false;
        foreach($rsl->result() as $key=>$value){
            if($rc) $sql_awal .= ", ";
            $arrBVA[$value->bva_account] =$value->bva_shortname;
            $arrBVG[$value->bva_account][] =$value->bvd_glaccount;
            $sql_awal .= "SUM(IFNULL(grb_" . $value->bva_account.",0)) grb_" . $value->bva_account;
            $rc = true;
        }
        $loop = 0;

        $year_fiscal = substr($fiscal_year, -2);
        $year_fiscal = "20" . $year_fiscal;
        $date_2 = $year_fiscal . "-01-01";
        $date_2 = date_create($date_2);

        date_add($date_2,date_interval_create_from_date_string("1 year"));

        $year_fiscal2 = date_format($date_2,"Y");

        // $this->common->debug_array($arrBVA, false);

        foreach($arrBVA as $keyBVA=>$valueBVA){
            $bva_account = $keyBVA;
            $sql_detail = null;
            $sqlexp_detail = null;
            $rc = false;
            foreach($arrBVA as $keyBVA_1=>$valueBVA_1){
                if($rc) $sql_detail .= ", ";
                if($rc) $sqlexp_detail .= ", ";
                if($bva_account==$keyBVA_1){
                    $sql_detail .= "SUM(grb_budget) grb_" . $keyBVA_1;
                    $sqlexp_detail .= "SUM(sad_amountinlocalcurrency) grb_" . $keyBVA_1;
                }else{
                    $sql_detail .= "0 grb_" . $keyBVA_1;
                    $sqlexp_detail .= "0 grb_" . $keyBVA_1;
                }
                $rc = true;
            }

            $this->db->select($sqlgrant);
            $this->db->select($sql_detail);
            $this->db->from("t_mas_grant a");
            $this->db->join("t_mas_grant_budget b", "a.grn_idents = b.grb_grnidents", "INNER");
            if($grn_code!=0){
                $this->db->where("a.grn_code", $grn_code);
            }
            $this->db->where("grb_bvaaccount", $bva_account);
            $this->db->where("grb_fiscalyear", $fiscal_year);
            $this->db->where("IFNULL(grn_is_deleted,0) <> 1");
            $this->db->group_by($sqlgrant);
            ${"sql_" . $loop} = $this->db->get_compiled_select();

            $this->db->select($sqlgrant);
            $this->db->select("'" . $year_fiscal ."' year_fiscal");
            $this->db->select($sqlexp_detail);
            $this->db->from("t_mas_grant a");
            // $this->db->join("t_mas_grant_budget b", "a.grn_idents = b.grb_grnidents", "INNER");
            $this->db->join("t_sap_data_detail c", "a.grn_code = c.sad_grant", "INNER");
            if($grn_code!=0){
                $this->db->where("a.grn_code", $grn_code);
            }
            $this->db->where_in("c.sad_account", $arrBVG[$bva_account]);
            $this->db->where("YEAR(sad_postingdate)", $year_fiscal);
            $this->db->where("IFNULL(grn_is_deleted,0) <> 1");
            $this->db->group_by($sqlgrant);

            ${"sqlexp_FY1_" . $loop} = $this->db->get_compiled_select();

            $this->db->select($sqlgrant);
            $this->db->select("'" . $year_fiscal2 ."' year_fiscal");
            $this->db->select($sqlexp_detail);
            $this->db->from("t_mas_grant a");
            // $this->db->join("t_mas_grant_budget b", "a.grn_idents = b.grb_grnidents", "INNER");
            $this->db->join("t_sap_data_detail c", "a.grn_code = c.sad_grant", "INNER");
            $this->db->where_in("c.sad_account", $arrBVG[$bva_account]);
            $this->db->where("YEAR(sad_postingdate)", $year_fiscal2);
            $this->db->where("IFNULL(grn_is_deleted,0) <> 1");
            $this->db->group_by($sqlgrant);

            ${"sqlexp_FY2_" . $loop} = $this->db->get_compiled_select();
            
            $loop++;
        }
        $sqlUNION = null;
        $sqlUNION_exp_FY1 = null;
        $sqlUNION_exp_FY2 = ' UNION ALL ';
        $rc = false;
        for($i=0;$i<$loop;$i++){
            if($rc) $sqlUNION .= " UNION ALL ";
            if($rc) $sqlUNION_exp_FY1 .= " UNION ALL ";
            if($rc) $sqlUNION_exp_FY2 .= " UNION ALL ";

            $sqlUNION .= ${"sql_" . $i};
            $sqlUNION_exp_FY1 .= ${"sqlexp_FY1_" . $i};
            $sqlUNION_exp_FY2 .= ${"sqlexp_FY2_" . $i};

            $rc = true;
        }

        $sqlUNION_exp = $sqlUNION_exp_FY1;// . $sqlUNION_exp_FY2;

        $this->db->select($sqlgrant);
        $this->db->select($sql_awal);
        $this->db->from("t_mas_grant a");
        $this->db->join("(" . $sqlUNION . ") b", "a.grn_idents = b.grn_idents", "LEFT OUTER", false);
        if($grn_code!=0){
            $this->db->where("a.grn_code", $grn_code);
        }
        if($active!=0){
            if($active==1){
                $this->db->where("grn_dateend >= ", date('Y-m-d'));
            }
            if($active==2){
                $this->db->where("grn_dateend <= ", date('Y-m-d'));
            }            
        }
        $this->db->group_by($sqlgrant);

        $rslbva = $this->db->get();

        // $this->db->select($sqlgrant);
        $this->db->select("year_fiscal");
        $this->db->select($sql_awal);
        $this->db->from("t_mas_grant a");
        $this->db->join("(" . $sqlUNION_exp . ") b", "a.grn_idents = b.grn_idents", "INNER", false);
        if($grn_code!=0){
            $this->db->where("a.grn_code", $grn_code);
        }
        if($active!=0){
            if($active==1){
                $this->db->where("grn_dateend >= ", date('Y-m-d'));
            }
            if($active==2){
                $this->db->where("grn_dateend <= ", date('Y-m-d'));
            }
        }
        $this->db->group_by("year_fiscal");

        // $this->common->debug_sql(1,1);
        $rslexp = $this->db->get();

        $result["resultset"] = $rslbva;
        $result["resultexp"] = $rslexp;
        $result["arrayBVA"] = $arrBVA;

        return $result;
    }
}

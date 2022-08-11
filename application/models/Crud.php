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
 
class Crud extends CI_Model {
	var $data = array();
	var $returnArray = TRUE;
	var $table;
	var $fields;
	var $__numRows;
	var $__insertID;
	var $__affectedRows;
	var $id;
	var $primaryKey = 'id';
	var $status_save = 'query_builder';
	var $__type_save;
	var $driver;
	var $prefix;
	var $dbschm;
	var $smsdb;
	var $username;
	var $usrauthrz;
	var $usrtypusr;	
	var $datesave;
	var $app_numbr;
	var $table_user;
	var $table_menu;
	var $table_common;
	var $table_usermenu;
 	function __construct() {
		parent::__construct();
		include(APPPATH.'config/database'.EXT);
		$this->driver =  $db['default']['dbdriver'];
		$this->prefix =  $db['default']['dbprefix'];
		$this->username = $this->session->userdata('USR_LOGINS');
		
		$this->usrauthrz = $this->session->userdata('USR_AUTHRZ');	
		$this->usridents = $this->session->userdata('USR_IDENTS');	
		$this->usrtypusr = $this->session->userdata('USR_TYPUSR');
		$this->table_user = $this->config->item('tbl_user');
		$this->table_menu = $this->config->item('tbl_menu');
		$this->table_common = $this->config->item('tbl_common');
		$this->table_usermenu = $this->config->item('tbl_usermenu');
		$this->table_akseslog = $this->config->item('tbl_akseslog');
		$this->security_level = $this->config->item('security_level');
		// $this->app_numbr = $this->config->item('app_numbr');
		$this->app_numbr = $this->session->userdata('app_numbr');
		$this->datesave = date('Y-m-d H:i:s');

		$multi_lang = $this->config->item('multi_lang');
		$LANG = null;
		if($multi_lang){
			$LANG = $this->session->userdata('USR_LANGUAGE');
		}

		$this->LANG = ($LANG=="" ? 1 : $LANG);

 	}
	/**
	 * Load the associated database table.
	 *
	 */
 	function useTable($table, $protect=true){
		$this->table  = $table;
		$prefix = "";
		if($protect){
			$prefix = $this->prefix;	
		}
		$arrTable = explode(".", $table);
		if(count($arrTable)==1){
			$this->fields = $this->db->list_fields($prefix.$table);
		}else{
			$sql = "SELECT b.name 
					FROM ".$arrTable[0].".".$arrTable[1].".sysobjects a
						INNER JOIN ".$arrTable[0].".".$arrTable[1].".syscolumns b
						ON a.id=b.id
					WHERE a.name = '".$arrTable[2]."'";
			$query = $this->db->query($sql);
			foreach ($query->result() as $field){
				$this->fields[] = $field->name;
			}
		}
		$this->status_save = 'active_record';
	  
 	}
 	function unsetMe(){
		$this->crud->id = null;
		$this->crud->primaryKey = null;
		$this->crud->data = array();
		$this->crud->fields = null;
		$this->crud->table= null;
 	}
 	function get_insert_id(){
		return $this->__insertID;
 	}
	// ============================================================================================================
	// function delete dan save standar 
	function save($data, $pk=null, $protected=true, $unsetpk=true, $forceinsert=true){
		// $this->common->debug_array($data);
		if($this->table==""){
			show_error("Tentukan Table terlebih dahulu!");
		}
		if ($data){
			$this->data = $data;
		}
		if ($pk){
			$this->primaryKey = $pk;
		}else{
			$this->primaryKey = "";
		}
		// show_error($this->fields);
		if($protected){
			// cek fields ada atau tidak
			if(count($this->fields)==0){
				show_error("Tabel ". $this->table ." tidak ada!");
			}
			foreach ($this->data as $key => $value){
				if (array_search($key, $this->fields) === FALSE){
					// $this->common->debug_array($this->table, false);
					unset($this->data[$key]);
				}
			}
			if(count($this->data)==0){
				show_error("Field tidak ditemukan di tabel ". $this->table ."!");
			}
		}

		if(is_array($this->primaryKey)){
			$this->db->select("count(*) COUNT");
			// $this->common->debug_array($protected);
			foreach ($this->primaryKey as $keypk => $valuepk) {
				if(is_array($valuepk)){
					$arrWhere = explode("^", $keypk);
					if(isset($arrWhere[1])){
						switch ($arrWhere[1]) {
							case "in":
								$this->db->where_in($arrWhere[0], $valuepk);
								break;
							case "notin":
								$this->db->where_not_in($arrWhere[0], $valuepk);
								break;
						}
					}	
				}else{
					switch ($valuepk) {
						case 'isnull':
						case 'ifnull':
							$this->db->where($keypk . " IS NULL");
							break;
						case 'notnull':
							$this->db->where($keypk . " IS NOT NULL");
							break;
						default:
							$this->db->where($keypk, $valuepk);
							break;
					}
				}
			}
			$this->db->from($this->table, $protected);
			$rslTemp = $this->db->get();
			$numrows = $rslTemp->row()->COUNT;
			foreach($this->primaryKey as $keyf=> $valuef){
				$namafield[] = $keyf;	
			}
		}else{
			$numrows = 0;
		}
		if($unsetpk){
			if(isset($namafield) && $numrows<>0){
				$cntNamafield = count($namafield);
				if($cntNamafield>0){
					for($i=0;$i<$cntNamafield;$i++){
						unset($this->data[$namafield[$i]]);
					}
				}
			}			
		}
		if(!$forceinsert){
			$numrows = 1;
		}
		if($numrows>0){ // ada parameter primary key dan valuenya
			if($this->status_save=='query'){
				$xxx = false;
				$sql = "UPDATE " . $this->table ." SET ";
				foreach ($this->data as $key => $value){
					if ($xxx) $sql .= ",";
					$sql .= $key . "=" . "'".$this->data[$key]."'";
					$xxx = true;
				}
				$xxx = false;
				foreach ($this->primaryKey as $key => $value){
					if ($xxx){
						$sql .= " AND ";
					}else{
						$sql .= " WHERE ";
					}
					// $sql .= $key . "=" . "'".$this->data[$key]."'";
					$sql .= $key . "=" . "'".$value."'";
					$xxx = true;
				}
				$this->db->query($sql);
			}else{
				// foreach($this->primaryKey as $key=> $value){
				// 	$this->db->where($key, $value);
				// }
				foreach ($this->primaryKey as $keypk => $valuepk) {
					if(is_array($valuepk)){
						$arrWhere = explode("^", $keypk);
						if(isset($arrWhere[1])){
							switch ($arrWhere[1]) {
								case "in":
									// 
									$this->db->where_in($arrWhere[0], $valuepk);
									break;
								case "notin":
									$this->db->where_not_in($arrWhere[0], $valuepk);
									break;
							}
						}	
					}else{
						switch ($valuepk) {
							case 'isnull':
							case 'ifnull':
								$this->db->where($keypk . " IS NULL");
								break;
							case 'notnull':
								$this->db->where($keypk . " IS NOT NULL");
								break;
							default:
								$this->db->where($keypk, $valuepk);
								break;
						}
					}
				}
				$this->db->update($this->table, $this->data, null, null, $protected);
				$this->__affectedRows = $this->db->affected_rows();
			}
			$this->__insertID = $this->primaryKey;
			$this->__type_save="update";
			// $this->common->debug_array($this->primaryKey);

			return true;//"update";
		}else{ // insert statement
			if($this->status_save=='query'){
				$xxx = false;
				$sql = "INSERT INTO ".$this->table."
							(";
				foreach ($this->data as $key => $value){
					if ($xxx) $sql .= ",";
					$sql .= $key;
					$xxx = true;
				}
				 
				$xxx = false;
				$sql .= ")VALUES(";
				foreach ($this->data as $key => $value){
					if ($xxx) $sql .= ",";
					$sql .= "'".$this->data[$key]."'";
					$xxx = true;
				}
				$sql .= ")";
				$this->db->query($sql);
				$this->__insertID = $this->db->insert_id();
			}else{
				// $this->common->debug_array($this->data);
				$this->db->insert($this->table,$this->data, null, $protected);
				$this->__affectedRows = $this->db->affected_rows();
				$this->__insertID = $this->db->insert_id();
			}
			$this->__type_save="insert";
			return true;//"insert";
		}
	}	
	function delete($pk = null, $protected=true){
		if ($pk != null){
			$this->pk = $pk;
		}else{
			show_error("Primary Key harus ditentukan!");
		}
		$this->db->delete($this->table,$this->pk,null,TRUE,$protected);
		$this->__affectedRows = $this->db->affected_rows();
		return true;
	}
	// ============================================================================================================
	/**
	 *  Get row affected from update
	 */
	function getAffectedRows(){
			return $this->__affectedRows;
	}	
	function getRandomid_detail($types,$values){
		$this->db->select('*');
		$this->db->from('rnd_rndgen');
		$this->db->where('rnd_values', $values);
		$this->db->where('rnd_applic', $types);
		$query = $this->db->get();
		if($query->num_rows()!=0){
			return $query ;
		}else{
			return NULL;
		}
	}
	function loginfailed($username){
		$query = $this->db->get_where($this->table_user, array('USR_LOGINS'=>$username));
		$failed = 0;
		if($query->num_rows()>0){
			$queryx = $this->db->get_where('USR_FAILED', array('USR_LOGINS'=>$username));
			if($queryx->num_rows()==0){
				$failed = 0;
			}else{
				$failed = $queryx->row()->USR_FAILED;
			}
			if($failed ==2){
				$this->unsetme();
				$this->usetable($this->table_user);
				$array = array('USR_ACCESS'=>2);
				$this->save($array, $username, 'USR_LOGINS');
				$this->unsetme();
				$this->usetable($this->table_akseslog);
				$this->delete($username, 'USR_LOGINS');
			}else{
				$this->unsetme();
				$this->usetable($this->table_user);
				$input = array('USR_LOGINS'=>$username, 'USR_FAILED'=>1);
				
				$this->db->set('USR_FAILED', 'USR_FAILED+1', false);
				$this->db->where('USR_LOGINS', $username);
				$this->db->update('USR_FAILED');
				
				if($this->db->affected_rows()==0){
					$this->id=null;
					$this->primarykey = null;
					$this->save($input);
				}			
			}
		}
	}
	function cekLogins($value="",$opt=1){
		return $this->db->get_where($this->table_user, array('USR_LOGINS' => $value));
		//
	}
	function chkAsesor($usr_idents=null){
		$usr_idents = ($usr_idents==null ? $this->usridents : $usr_idents);
		$this->db->from("t_asm_asesor");
		$this->db->where("ase_asesor", $usr_idents);
		$this->db->where("IFNULL(ase_is_deleted,0) <> 1");
		$rslAsesor = $this->db->get();

		return $rslAsesor;
	}
	function getMenu_json($NEW=1,$SEC=1, $USER=null, $APPLIC=null, $CHANGE=false, $FORMENU=false){	
		if($USER==""){
			if($this->security_level=="user"){
				$USER = $this->session->userdata('USR_LOGINS');	
			}else{
				$USER = $this->session->userdata('USR_LEVELS');	
			}
		}
		$rslAsesor = $this->chkAsesor();
		$asesor = false;
		if($rslAsesor->num_rows()>0){
			$asesor = true;
			$sqlAsesor = "SELECT '19870301LO1200000000' MNU_IDENTS, 'Penilaian Asesmen' MNU_DESCRE, 2 MNU_SORTBY, 'asesor/penilaian' MNU_ROUTES, 'file-alt' MNU_ICONED,";
			$sqlAsesor .= "'19870301LO' MNU_APPLIC, '1200000000' MNU_NOMORS, '0' MNU_HVCHLD, 'PV' MNU_RIGHTS, '0' MNU_EDTBLE, '0' MNU_PARENT, '0' MNU_CHILDN,";
			$sqlAsesor .= "'0' MNU_CHANGE, null MNU_MNUADD, null MNU_MNUEDT, null MNU_MNUDEL, null MNU_MNUVIW, 'V' MNU_AUTHRZ";
		}

		$JOINADD = "";
		if($SEC==1){
			if($this->security_level=="user"){
				$this->db->where('MNU_FKUSER', $USER);
			}else{
				$this->db->where('MNU_LEVELS', $USER);
			}
			
			$JOIN = "INNER";
		}else{
			if($this->security_level=="user"){
				$JOINADD = " AND MNU_FKUSER = '". $USER . "'";
			}else{
				$JOINADD = " AND MNU_LEVELS = '". $USER . "'";
			}
			$JOIN = "LEFT OUTER";
		}
		$arrNOTINS = array("2016018000", "3087030101");
		$this->db->distinct();
		$this->db->select($this->common->concatdb(array("a.MNU_APPLIC","a.MNU_NOMORS"), "MNU_IDENTS"));
		if($this->LANG==1){
			$this->db->select("MNU_DESCRE");
		}else{
			$this->db->select("MNU_DESCRB MNU_DESCRE");
		}
		
		$this->db->select($this->common->isnulldb("a.MNU_SORTBY",99,"MNU_SORTBY"));
		$this->db->select("a.MNU_ROUTES, a.MNU_ICONED, a.MNU_APPLIC, a.MNU_NOMORS, MNU_HVCHLD, MNU_RIGHTS");
		$this->db->select($this->common->isnulldb("a.MNU_EDTBLE",1,"MNU_EDTBLE"));
		if($this->app_numbr=="9999"){
			$this->db->select("CASE MNU_PARENT WHEN '0' THEN a.MNU_APPLIC ELSE MNU_PARENT END MNU_PARENT, MNU_CHILDN+1 MNU_CHILDN");
		}else{
			$this->db->select("MNU_PARENT, MNU_CHILDN");
		}
		if($CHANGE){
			$this->db->select("1 MNU_CHANGE");
		}else{
			$this->db->select("0 MNU_CHANGE");
		}
		$this->db->select("CASE ".$this->common->isnulldb("MNU_HVCHLD","0")." WHEN 0 THEN CASE INSTR(".$this->common->isnulldb("MNU_RIGHTS",0).", 'A') WHEN 0 THEN '' ELSE '<li class=\"fas fa-check\"></li>' END END MNU_MNUADD");
		$this->db->select("CASE ".$this->common->isnulldb("MNU_HVCHLD","0")." WHEN 0 THEN CASE INSTR(".$this->common->isnulldb("MNU_RIGHTS",0).", 'E') WHEN 0 THEN '' ELSE '<li class=\"fas fa-check\"></li>' END END MNU_MNUEDT");
		$this->db->select("CASE ".$this->common->isnulldb("MNU_HVCHLD","0")." WHEN 0 THEN CASE INSTR(".$this->common->isnulldb("MNU_RIGHTS",0).", 'D') WHEN 0 THEN '' ELSE '<li class=\"fas fa-check\"></li>' END END MNU_MNUDEL");
		$this->db->select("CASE ".$this->common->isnulldb("MNU_HVCHLD","0")." WHEN 0 THEN CASE INSTR(".$this->common->isnulldb("MNU_RIGHTS",0).", 'V') WHEN 0 THEN '' ELSE '<li class=\"fas fa-check\"></li>' END END MNU_MNUVIW");


		$this->db->select("MNU_AUTHRZ");
		if($USER=='admin'){
			// $this->db->select("MNU_REFERS,MNU_CHILDN,MNU_CIGNIT,MNU_GROUPS,MNU_APPNEW,MNU_PRODEV");
		}else{
			if($SEC!=2){
				$arrNOTINS = array_merge($arrNOTINS, array("1098703010"));	
			}
		}
		$this->db->from($this->table_menu . " a");
		$this->db->join($this->table_usermenu . ' b', "a.MNU_NOMORS = b.MNU_MENUCD AND a.MNU_APPLIC = b.MNU_APPLIC" . $JOINADD, $JOIN);
		// $this->db->where("a.MNU_NOMORS like '030%'");
		// $this->db->where("len(a.MNU_NOMORS)>5");
		$this->db->where($this->common->lengthdb("a.MNU_NOMORS",5, ">"));
		if($APPLIC==""){
			if($this->app_numbr!="9999"){
				$this->db->where("a.MNU_APPLIC", $this->app_numbr);		
			}
		}else{
			$this->db->where("a.MNU_APPLIC", $APPLIC);	
		}
		// $this->db->where("ISNULL(,0) = 1");
		$this->db->where($this->common->isnulldb("MNU_ACTIVE","0") . " = 1");
		// $this->common->debug_sql(true,true);

		if($this->app_numbr=="9999"){
			$sql1 = $this->db->get_compiled_select();

			$this->db->select("DISTINCT MNU_APPLIC");
			$this->db->from($this->table_usermenu);
			if($this->security_level=="user"){
				$this->db->where('MNU_FKUSER', $USER);
			}else{
				$this->db->where('MNU_LEVELS', $USER);
			}
			if(!$FORMENU){
				$this->db->where("MNU_APPLIC", $this->app_numbr);	
			}
			$sqlUser = $this->db->get_compiled_select();

			$this->db->select("app_applic MNU_IDENTS, app_descre MNU_DESCRE, app_id MNU_SORTBY, '#' MNU_ROUTES, app_iconed MNU_ICONED,  app_applic MNU_APPLIC, CONVERT(VARCHAR(20), app_id)  MNU_NOMORS, 1 MNU_HVCHLD, null MNU_RIGHTS, 0 MNU_EDTBLE");
			$this->db->select("'0' MNU_PARENT, 0 MNU_CHILDN, 0 MNU_CHANGE");
			$this->db->select("NULL MNU_MNUADD, NULL MNU_MNUEDT, NULL MNU_MNUDEL, NULL MNU_MNUVIW, null MNU_AUTHRZ");
			$this->db->from("m_application a");
			$this->db->join("(" . $sqlUser . ") b", "a.app_applic = b.MNU_APPLIC", "INNER");
			// $this->db->where("ISNULL(MNU_ACTIVE,0) = 1");

			$sql2 = $this->db->get_compiled_select();

			$this->db->from("(" . $sql1 . " UNION ALL " . $sql2. ") a", FALSE);

		}
		
		if(isset($sqlAsesor)){
			$sqlMenu = $this->db->get_compiled_select();

			$this->db->from("(" . $sqlMenu . " UNION ALL " . $sqlAsesor . ") a", FALSE);
		}
		$this->db->order_by("a.MNU_APPLIC, MNU_PARENT, " . $this->common->isnulldb("a.MNU_SORTBY","99"), null,false);
		$result = $this->db->get();

	    $hasil= $result;//->result_array();
	    return $hasil;
	}
	function getMenutree_app($APPLIC, $USER, $MENU=null){
		$this->db->select("DISTINCT a.MNU_APPLIC + a.MNU_NOMORS  as id, MNU_DESCRE text", false);
		$this->db->select("MNU_PARENT parentid, MNU_ROUTES nilai, MNU_ICONED iconed");
		$this->db->from($this->table_menu . " a");
		if($USER!="1"){
			$this->db->join($this->table_usermenu . " b", "a.MNU_NOMORS = b.MNU_MENUCD AND a.MNU_APPLIC = b.MNU_APPLIC", "INNER");

			if($this->security_level=="user"){
				$this->db->where('MNU_FKUSER', $USER);
			}else{
				$this->db->where('MNU_LEVELS', $USER);
			}
		}
		$this->db->where("MNU_APPNEW = 1");
		$this->db->where("MNU_REFERS <> 0");
		$this->db->where("a.MNU_APPLIC", $APPLIC);
		switch ($MENU) {
			case 'dashboard':
				$this->db->where("LEFT(a.MNU_NOMORS,4) = '0101'");
				$this->db->where("MNU_PARENT != '109870301001000000'");
				$sql2 = $this->db->get_compiled_select();

				$sql1 = "
				SELECT '109870301001010001' id,'Ekstension Telp' text	,'109870301001000000' parentid,'1' nilai, '' iconed 
				UNION ALL
				SELECT '109870301001010002' id,'Email' text,'109870301001000000' parentid,'2' nilai, '' iconed";
				$this->db->from('(' . $sql1 . ' UNION ALL '. $sql2 . ') AS u' ,false);
				break;
			case 'beranda':
				$this->db->where("MNU_PARENT = '109870301001000000'");
				$this->db->where("a.MNU_NOMORS != '01010000'");
					
				break;
		}
    	$result = $this->db->get();
		$hasil['Hasil'] = $result->result();
		return $hasil;
	}
	function getMenu_tree($LOGIN=true){
		$this->db->select("DISTINCT a.MNU_APPLIC + a.MNU_NOMORS  as MNU_IDENTS, MNU_DESCRE", false); 
		$this->db->select("CASE a.MNU_PARENT WHEN 0 THEN '0' ELSE CONVERT(VARCHAR,a.MNU_APPLIC + a.MNU_PARENT) END MNU_PARENT", false);
		$this->db->select("a.MNU_SORTBY, a.MNU_ROUTES, a.MNU_ICONED");
		$this->db->from($this->table_menu . " a");
		$this->db->order_by("a.MNU_APPLIC + a.MNU_NOMORS, MNU_SORTBY", null,false);
		$result = $this->db->get();
		$hasil= $result;//->result_array();
		return $hasil;
	}	
	function getMenuall($appl, $user, $typemenu, $refers=null){
		$this->db->select('*');
		$this->db->from($this->table_menu . ' a');
		if($this->security_level=="user"){
			$FK = "MNU_FKUSER";
		}else {
			$FK = "MNU_LEVELS";
		}
		$this->db->join($this->table_usermenu . ' b', 'a.MNU_NOMORS = b.MNU_MENUCD AND ' . $FK . ' =' .$user, 'LEFT OUTER');
		$this->db->where('MNU_CHILDN', $typemenu);
		$this->db->where('MNU_APPNEW', 1);
		if($refers!=""){
			$this->db->where("MNU_REFERS = '$refers'");
		}
		$this->db->order_by('MNU_NOMORS, MNU_SORTBY');
		$query = $this->db->get();
		return $query;
	}
	function getUsermenu($user, $refers){
		$this->db->select('*');
		$this->db->from($this->table_usermenu . ' a');
		$this->db->where("MNU_MENUCD = '$refers'");
		if($this->security_level=="user"){
			$this->db->where("MNU_FKUSER = '$user'");
		}else {
			$this->db->where("MNU_LEVELS = '$user'");
		}

		$query = $this->db->get();
		if($query->num_rows()>0){
			$return = "ada";
		}else{
			$return = "none";
		}
		return $return;
	}
  	function getMaxmenu($MNU_APPLIC, $MNU_CHILDN, $MNU_PARENT){
		if($MNU_PARENT==""){
			$MNU_PARENT = "0";
		}
		$this->db->select("max(MNU_NOMORS) MNU_NOMORS");
		$this->db->from($this->table_menu);
		$this->db->where("MNU_PARENT",$MNU_PARENT);
		$query = $this->db->get();
		$NUM_MAX=$query->row()->MNU_NOMORS;
		if($NUM_MAX==""){
			$NUM_MAX=0;
			$NUM_MAX = str_replace($MNU_APPLIC, "", $MNU_PARENT);
		}
		switch ($MNU_CHILDN) {
			case '0':
				$max = substr($NUM_MAX,0,2);
				break;
			case '1':
				$max = substr($NUM_MAX,0,4);
				break;
			case '2':
				$max = substr($NUM_MAX,0,6);
				break;
			case '3':
				$max = substr($NUM_MAX,0,8);
				break;
		}
		$MAX_MENU = $max + 1;
		$MENU = substr("0" . str_pad($MAX_MENU, 8, "0"), 0, 8);
		return $MENU;
  	}
	function getOtorisasi($cignit, $show=1){
		// $appnumero = $this->config->item('app_numbr');
		if($this->security_level=="user"){
			$user  = $this->session->userdata('USR_LOGINS');
		}else{
			$user  = $this->session->userdata('USR_LEVELS');
		}
		$this->db->select('MNU_RIGHTS, MNU_DESCRE');
		$this->db->from($this->table_usermenu . ' a');
		$this->db->join($this->table_menu . ' b', 'a.MNU_MENUCD = b.MNU_NOMORS and a.MNU_APPLIC = b.MNU_APPLIC','INNER');
		$this->db->where('MNU_ROUTES', $cignit);
		if($this->security_level=="user"){
			$this->db->where("MNU_FKUSER", $user);
		}else {
			$this->db->where("MNU_LEVELS", $user);
		}
		if($this->app_numbr!="9999"){
			$this->db->where('b.MNU_APPLIC', $this->app_numbr);
		}else{
			$this->db->order_by("MNU_RIGHTS");
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0)
		{
		$row = $query->row();
		switch ($show) {
			case '2':
				$nilai = $row->MNU_DESCRE;
				break;
			default:
				$nilai = $row->MNU_RIGHTS;
				break;
		}
		$return = $nilai;
		}else{
			return false;
		}
		return $return;
	}
	function getTableInformation($tablename=null, $schema=null, $column=null, $protected=true, $type=1){
		$driver = $this->driver;
		$prefix = $this->prefix;
		$dbschm = $this->dbschm;
		if($schema!=""){
			$dbschm = $schema; 
		}
		// print_r($prefix);
		// if($protected==true){
		$sql = "";
		$tablename = $prefix . $tablename;
		// }
		switch($driver){
			case "mysql" :
			case "mysqli" :
				$sql = "show full columns from " . $tablename;
				if($column!="") $sql.=" WHERE Field ='" . $column . "'";
				break;
			case "postgre" :
				$sql_old = "
					SELECT c.column_name \"Field\",pgd.description \"Comment\", 
						CASE data_type
							WHEN 'character varying' THEN 'varchar' || '(' || character_maximum_length || ')'
							WHEN 'integer' THEN 'int'
							ELSE data_type
						END \"Type\", c.table_name
					FROM pg_catalog.pg_statio_all_tables as st
						left outer join pg_catalog.pg_description pgd on (pgd.objoid=st.relid)
						left outer join information_schema.columns c on (pgd.objsubid=c.ordinal_position
							and  c.table_schema=st.schemaname and c.table_name=st.relname)";
				$sql = "
					SELECT b.column_name \"Field\",c.description \"Comment\", 
						CASE data_type
							WHEN 'character varying' THEN 'varchar' || '(' || character_maximum_length || ')'
							WHEN 'integer' THEN 'int'
							ELSE data_type
						END \"Type\", a.relname table_name
					FROM pg_catalog.pg_statio_all_tables a
					inner join information_schema.columns b
						on a.relname = b.table_name
					left outer join pg_catalog.pg_description c
						on (c.objoid=a.relid
						and c.objsubid=b.ordinal_position 
						and b.table_schema=a.schemaname 
						and b.table_name=a.relname)				
				";
				// if($column!="") $sql.="WHERE c.column_name ='" . $column . "'";
				if($column!="") $sql.="WHERE b.column_name ='" . $column . "'";
				if($tablename!=$prefix) {
					if($column==""){
						$sql.=" WHERE";
					}else{
						$sql.=" AND";
					}
					$sql.=" table_name = '" . $tablename . "'";
				}
		//SELECT c.table_schema,c.table_name,c.column_name,pgd.description, 
		//	CASE data_type
		//		WHEN 'character varying' THEN 'varchar'
		//		WHEN 'integer' THEN 'int'
		//		ELSE data_type
		//	END,
		//	data_type
		//,C.*
		//FROM pg_catalog.pg_statio_all_tables as st
		//  inner join pg_catalog.pg_description pgd on (pgd.objoid=st.relid)
		//  inner join information_schema.columns c on (pgd.objsubid=c.ordinal_position
		//    and  c.table_schema=st.schemaname and c.table_name=st.relname)
		//WHERE table_name = 'T_COMMON'
				
				break;
		}
		$query = $this->db->query($sql);
		// echo "<pre>";
		// print_r($query);
		// die();
		if($type==2){
			if($query->num_rows()>0){
				$query = $query->row();	
			}else{
				$query = array();
			}
		}
		// echo $this->db->last_query();
		// die();
		return $query;
	}
	function savemaster($table, $arrOther=null, $pk=null, $debug=false){
		$type = $this->input->post('hidTRNSKS');
		$prefix = $this->prefix;
		$username = $this->session->userdata('USR_LOGINS');
		$datesave = date('Y-m-d H:i:s');
		$statusupload = true;
		$txt = "";
		$txtInput = "$" . "input = array(
		";
		if(!$arrOther){
			// ============================================================ //
			// kalau tidak ada definisi kolom, berarti ambil dari database
			// ============================================================ //
			$fielddetail = $this->crud->getTableInformation($table, true);
			//cari prefix field
			if ($fielddetail->num_rows() > 0){
				$onerow = $fielddetail->row();
				$prefixField = substr($onerow->Field,0,3);
			}
			$arrField = $fielddetail->result_array();
			$html2 = "";
			$loop = 1;
			$input = array();
			foreach($arrField as $key=>$value){
				$fldnam = $value["Field"];
				$dattyp = $value["Type"];
				$arrComment = $this->common->extractjson($table, $fldnam);
				$object = str_replace($prefixField . "_", "", $fldnam);//substr($fldnam,4,6);
				if($arrComment!=""){
					foreach($arrComment as $key=>$value){
						if(!is_array($value)){
							${$key}=$value;
						}else{
							if($key=="crud"){
								foreach($value as $keyd=>$valued){
									// print_r($valued);
									foreach($valued as $keye=>$valuede){
										${$keye} = $valuede;
										// echo $keye;
										if($keye=="ct"){
											$prefixElement = $valuede;	
											if($prefixElement!="viw"){
												// ====================================================
												// kalau bukan view, ambil value dari form
												// ====================================================
												$objname = $prefixElement . $object;	
												switch($dattyp){
													case "int" :
														${$objname} = $this->input->post($objname)=="" ? 0 : $this->input->post($objname);
														$txt .= "$". $objname ."=" ." $" . "this->input->post(" . "'". $objname ."')==\"\" ? 0 : $" . "this->input->post(" . "'". $objname ."');
			";
														// .")=="" ? 0 : $this->input->post($objname);";
														break;
													case "datetime" :
														if($this->input->post($objname)!=""){
															${$objname} = $this->input->post($objname);		
															$txt .= "$". $objname ."=" ." $" . "this->input->post(" . "'". $objname ."');
			";
														}
														break;
													default :
														if($prefixElement=="fil"){
															if(isset($_FILES[$objname]['name'])){
																$filename = $_FILES[$objname]['name'];
																${$objname} = $filename;
																$pathupload = '/assets/documents';
																$allowed_types = 'office';
																$max_size = 10000000;
																$overwrite = true;
															}
															$txt .= "
			if(isset($". "_FILES['" . $objname ."']['name'])){
				$" . "filename = $" . "_FILES['" . $objname ."']['name'];
				$" . $objname ." = $" . "filename;
				$" . "pathupload = '/assets/documents';
				$" . "allowed_types = 'office';
				$" . "max_size = 10000000;
				$" . "overwrite = true;
			}
			";
														}else{
															${$objname} = $this->input->post($objname);
															$txt .= "$". $objname ."=" ." $" . "this->input->post(" . "'". $objname ."');
			";

														}
														break;
												}
												$input = array_merge($input, array($fldnam=>${$objname}));
												$txtInput .= "'" . $fldnam ."'=>$". $objname .",
			";
											}
										}else{
											if($ct=="fil"){
												if($keye=="cj"){
													$typedoc = $valuede;
												}
												if($keye=="cl"){
													$pathupload = $valuede;
												}
											}
										}
									}
									if($ct=="fil"){
										$arrUpload = array(
												'path' => $pathupload,
												'typedoc' => $typedoc,
												'max_size' => $max_size,
												'field'=>$objname,
												'overwrite' => $overwrite);
										$txt .= "
			$" . "arrUpload = array(
					'path' => $" . "pathupload,
					'typedoc' => $" . "typedoc,
					'max_size' => $" . "max_size,
					'field'=> '" . $objname . "',
					'overwrite' => $" . "overwrite);
			if($" . "filename!=\"\"){
				$" . "statusupload = $" . "this->common->uploadfile($" . "arrUpload);
				if($" . "statusupload==false){
					break;
				}
			}
			";
										if($filename!=""){
											$statusupload = $this->common->uploadfile($arrUpload);
											if($statusupload==false){
												break;
											}
										}
									}									
								}
							}
						}
						
					}
				}	
				$loop++;
			}
			if(!is_array($pk)){
				$hidIDENTS = $this->input->post('hidIDENTS');
				$pk = array($prefixField. '_IDENTS'=>$hidIDENTS);
			}
		}else{
			// ====================================================
			// kalau ada definisi kolom, berarti ambil dari definisi
			// ====================================================
			$pk="";
			$hidIDENTS = 1;
			foreach ($arrOther as $key => $value){
				if($key=='COL'){
					foreach ($value as $field=>$nilai){
						$input = array_merge($input, array($field=>$nilai));
					}
					$prefixField = substr($field,0,3);
				}
				if($key=='pk'){
					$pk = $value;
				}
			}
			if(!is_array($pk)){
				$hidIDENTS = 0;
			}
		}

		if($type=="add"){
			$input = array_merge($input, array(
				$prefixField . '_USRNAM'=>$username,
				$prefixField . '_USRDAT'=> $datesave
			));
			$txtInput .= "'" . $prefixField . "_USRNAM'>$" ."username,
				'" . $prefixField . "_UPDDAT'=>$" ."datesave
				";
		}
		if($type=="edit"){
			$input = array_merge($input, array(
				$prefixField . '_UPDNAM'=>$username,
				$prefixField . '_UPDDAT'=> $datesave 
			));
			$txtInput .= "'" . $prefixField . "_USRNAM'=>$" ."username,
				'" . $prefixField . "_UPDDAT'=>$" ."datesave	
				";
		}

		$txtInput .= ");";
		if($debug==true){
			echo "<pre>";
			echo $txt;			
			echo $txtInput;
			die();

		}
		if($statusupload){
			$this->useTable($table);
			$response = $this->save($input, $pk);
		}else{
			$response = false;
		}
		return $response;
	}
  	function getDeftable($applic=null, $table=null, $protected=true){
		// include(APPPATH.'config/database'.EXT);
		$prefix =  $this->prefix;//$db['default']['dbprefix'];
		$tablename = $prefix . $table;
		if($protected==false){
			$tablename = $table;
		}
	    $this->db->select('TBL_APPLIC, TBL_DESCRE, TBL_TABLES,TBL_NOTESS, TBL_NOMORS, TBL_DEVLPR');
	    $this->db->from('AP3_MAS_TABLES a');
	    $this->db->order_by("TBL_IDENTS", "asc");
		if(isset($applic))
		{
			$this->db->where('TBL_APPLIC',$applic);
		}
		if(isset($table))
		{
			$this->db->where('TBL_TABLES', $tablename);
		}
		
		$query = $this->db->get();

		$this->__numRows = $query->num_rows();		
		return $query->result_array();//($this->returnArray) ? $query->result_array() : $query->result();
  	}
  	function getCommon_edit($HEADCD, $TYPECD){
	  	$this->db->from($this->table_common);
	  	$this->db->where("COM_HEADCD", $HEADCD);
	  	$this->db->where("COM_TYPECD", $TYPECD);
	  	$query = $this->db->get();
	  	$data = $query->row();
	  	return $data;
  	}
  	function getCommon_cmb($comhead, $field=null, $comtype=null,$com_descr2=null) {
	  	/*
	  	1 -> COM_TYPECD, COM_DESCR1
	  	2 -> COM_TYPECD, COM_DESCR2
	  	3 -> COM_IDENTS, COM_DESCR1
	  	4 -> COM_IDENTS, COM_DESCR2
	  	*/
	  	switch ($field) {
	  		case 2:
	  			$this->db->select('COM_TYPECD, COM_DESCR2');
	  			break;
	  		case 3:
	  			$this->db->select('COM_IDENTS, COM_DESCR1');
	  			break;
	  		case 4:
	  			$this->db->select('COM_IDENTS, COM_DESCR2');
	  			break;
	  		default:
	  			$this->db->select('COM_TYPECD, COM_DESCR1');
	  			break;
	  	}

	    if($comtype!=""){
			if($comtype=="0"){
				$this->db->where('COM_TYPECD = 0');
			}else{
				$this->db->where('COM_TYPECD <> 0');
				$this->db->where('COM_TYPECD',$comtype);
			}
	    }else{
			$this->db->where('COM_TYPECD <> 0');
		}
	    if($com_descr2!=""){
			$this->db->where('COM_DESCR2',$com_descr2);
	    }
		$this->db->from($this->table_common);
		if(strpos($comhead,"~")==0){
			$this->db->where('COM_HEADCD', $comhead);
		}else{
			$arrParam = explode("~", $comhead);			
			$this->db->where_in('COM_HEADCD', $arrParam);
		}

	    $query = $this->db->get();
	    $hasil['type'] = 'cmb';
	    $hasil['Hasil'] = $query->result();    
		return $hasil;
  	}  
  	function getCommonHead($type=null, $JSON=false){
		$this->db->select('COM_HEADCD, COM_DESCR1, COM_DESCR2, COM_IDENTS');
		$this->db->from($this->table_common);
		$this->db->where("COM_TYPECD",0);
		if($type!=""){
			switch ($type) {
				case 'IT':
					$this->db->where_in("COM_HEADCD", array(900, 902, 903, 904));
					break;
				case 'FEN':
					$this->db->where("COM_HEADCD", 701);
					break;
				
				default:
					# code...
					break;
			}
		}
	    $query = $this->db->get();
	    if($JSON){
	      $data['type'] = 'cmb';
	      $data['Hasil'] = $query->result();
	    }else{
		    $data[''] = '';
		    foreach($query->result() as $row){
		        $data[$row->COM_HEADCD] = $row->COM_DESCR1;
		    }    	
	    }

		return $data;
	 }
  	//========================================================================================================= 
	  function getGeneral_combo($parameter){
		// $type, $table="MAS_COMMON", $field=array("COM_IDENTS","COM_DESCR1"), $filter=null, $default=null, $protected=false, $zeroText="-"){
		$type =1;
		$table  = "t_mas_common";
		$filter = null;
		$default=null;
		$protected=false;
		$zeroText="-";
		$empty = true;
		$data = [];
		foreach ($parameter as $key => $value) {
			${$key} = $value;
		}

		if(isset($field)){
			if(is_array($field)){
				if(count($field)>1){
					foreach ($field as $keyField=>$valueField){
						if(is_numeric($keyField)){
							if(is_array($valueField)){
								foreach($valueField as $keyvaluvalue=>$valuevalue){
									$alias = $keyvaluvalue;
									if(is_array($valuevalue)){
										$concat = "CONCAT(";
										$rcconcat = false;
										$loopconcat = 0;
										if(isset($separator)){
											if(is_array($separator)){
												$sep1 = $separator[0];
												$sep2 = $separator[1];
											}else{
												$sep1 = $separator;
												$sep2 = null;
											}
										}
										if(isset($sep2)){
											$concat .= "'".$sep1."', ";
										}
										foreach($valuevalue as $valuesiege){
											if($rcconcat) $concat .= ",";
											if($loopconcat==1){
												if(isset($sep2)){
													$concat .= "'".$sep2." ', ";
												}
											}
											$concat .= $valuesiege;
											if($loopconcat==0){
												if(!isset($sep2)){
													$concat .= ",' ".$sep1." ', ";
												}
											}											
											$rcconcat = true;
											$loopconcat++;
										}
										$concat .= ")";
										$this->db->select($concat . " as " . $alias);
									}
								}
							}else{
								$this->db->select($valueField);
							}
						}else{
							$this->db->select($keyField . " as " . $valueField);
						}
					}
					// $field = explode(",", $field);
				}else{
					$this->db->select($field[0]);
				}
			}
			// foreach($field as $key){
			// 	if(strpos($key,"^")==0){
			// 		$fieldnya = $key;
			// 	}else{
			// 		$arrResult = explode("=>", $key);

			// 		$arrFieldnya = explode("^", $arrResult[0]);
			// 		$alias = $arrResult[1];
			// 		// $this->common->debug_array($arrFieldnya);
			// 		$fieldd = "";
			// 		$rcf = false;
			// 		for($e=0;$e<count($arrFieldnya);$e++){
			// 			if($rcf) $fieldd .= " + ' ' + ";
			// 			$fieldd .= $arrFieldnya[$e];
			// 			$rcf = true;
			// 		}
			// 		$fieldnya = $fieldd . " as " . $alias;
			// 		$field1 = $alias;
			// 	}
			// 	$this->db->select($fieldnya);
			// }			
		}else{
			if($table=="t_mas_common"){
				$field  = array("COM_IDENTS","COM_DESCR1");
			}
		}
		$this->db->from($table, $protected);

	  	if(is_array($filter)){
	  		foreach ($filter as $key => $value) {
	  			$fieldw = $key;
	  			preg_match("/(?P<operator>(like)|(not like)|(in)|(not in)<>|!=|=)/", $key, $matches);
	  			if(isset($matches['operator'])){
	  				$operator = $matches['operator'];
	  				if($operator!="like" && $operator!="not like"){
							// $this->common->debug_array($fieldw);
	  					if($value!=""){
								switch($operator){
									case "in" :
										$arrFieldnya = explode(" ", $fieldw);
										$fieldnya = $arrFieldnya[0];
										$this->db->where_in($fieldnya, $value);
										break;
									case "not in" :
										$this->db->where_not_in($fieldw, $value);
										break;
									default :
										$this->db->where($fieldw, $value);	
										break;										
								}
	  						
	  					}else{
	  						$this->db->where($fieldw);
	  					}
	  				}else{
	  					$fieldx = str_replace($operator, "", $key);
	  					if($operator=="like"){
	  						$this->db->like($fieldw, $value);
	  					}
	  					if($operator=="not like"){
	  						$this->db->not_like($fieldw, $value);
	  					}
	  				}
	  			}
	  		}
	  	}
		// $this->common->debug_sql(1,1);
	    $query = $this->db->get();

	  	if($default!=""){
	  		$data[''] = "Pilih " . $default;
	  	}

	    if($query->num_rows()>0){
		  	switch ($type) {
		  		case 1:
		  			if($empty){
		  				$data['0'] = $zeroText;	
		  			}
			        foreach($query->result() as $row){
						if(isset($field[1])){
							if(!is_array($field[1])){
								$arrDescre = explode("^", $field[1]);
								$jmlarray = count($arrDescre);
							}else{
								$jmlarray = 0;
							}
						}else{
							$jmlarray = 1;
						}
						
			        	if($jmlarray==1){
							if(isset($field[1])){
								$fieldnya = $field[1];
							}else{
								if(isset($field[0])){
									$fieldnya = $field[0];
								}
							}
							if(isset($fieldnya)){
								$descre = $row->{$fieldnya};
							}else{
								if(isset($descre_alt)){
									$descre = $row->{$descre_alt};
								}
							}
			        		
			        	}else{
							if($jmlarray>0){
								$field1 = $row->{$arrDescre[0]};
								$field2 = $row->{$arrDescre[1]};
								$descre = $field1 . " " . $field2;
							}else{
								// debug_array($field[1]);
								$descre = $row->$alias;
							}
						}
						// debug_array($field[0]);
						if(isset($field[0])){
							$id_value = $field[0];
						}else{
							$id_value = $id_alt;
						}
			            $data[$row->{$id_value}] = $descre;
			        }
			
		  			break;
				case "2" : //json
				    $data['type'] = 'cmb';
				    $data['Hasil'] = $query->result();
					break;
				case "3" :
					$data = $query;
					break;
		  	}
	    }
		return $data;
	}	 
  	//=========================================================================================================    	
  	function getGeneral_cmb($type, $table="common", $field=array("COM_IDENTS","COM_DESCR1"), $filter=null, $default=null, $protected=false, $all=true){
		if($table=="common"){
			$table = $this->table_common;
		}
		$field0 = $field[0];
		$field1 = $field[1];
	  	$filterdata = true;
	  	switch ($table) {
			default:
				foreach($field as $key){
					if(strpos($key,"^")==0){
						$fieldnya = $key;
					}else{
						$arrResult = explode("=>", $key);

						$arrFieldnya = explode("^", $arrResult[0]);
						$alias = $arrResult[1];
						// $this->common->debug_array($arrFieldnya);
						$fieldd = "";
						$rcf = false;
						for($e=0;$e<count($arrFieldnya);$e++){
							if($rcf) $fieldd .= " + ' ' + ";
							$fieldd .= $arrFieldnya[$e];
							$rcf = true;
						}
						$fieldnya = $fieldd . " as " . $alias;
						$field1 = $alias;
					}
					$this->db->select($fieldnya);
				}
			$this->db->from($table, $protected);
			break;
		}
	  	if(is_array($filter) && $filterdata==true){
	  		$this->filterCombo($filter);
	  	}
	    $query = $this->db->get();

	  	if($default!=""){
	  		$data[''] = "Pilih " . $default;
	  	}
	  	
	    if($query->num_rows()>0){
		  	switch ($type) {
					case '1':
						if($all){
							$data[''] = '';	
						} 
						foreach($query->result() as $row){
							$data[$row->{$field0}] = $row->{$field1};
						}
		  			break;
					case "2" : //json
							$data['type'] = 'cmb';
							$data['Hasil'] = $query->result();
							break;
		  	}
	    }else{
	    	$data['0'] = 'Tidak Ada Data';
	    }
		return $data;
  	}
	function filterCombo($filter){
		foreach ($filter as $key => $value) {
			$fieldw = $key;
			preg_match("/(?P<operator>(like)|(not like)|<>|!=|=)/", $key, $matches);
			if(isset($matches['operator'])){
				$operator = $matches['operator'];
				if($operator!="like" && $operator!="not like"){
					if($value!=""){
						$this->db->where($fieldw, $value);	
					}else{
						$this->db->where($fieldw);
					}
				}else{
					$field = str_replace($operator, "", $key);
					if($operator=="like"){
						$this->db->like($fieldw, $value);
					}
					if($operator=="not like"){
						$this->db->not_like($fieldw, $value);
					}
				}
			}
		}  	
	}    
  	function getCommon($type, $comhead, $comtype=null, $com_descr2=null, $exception=null, $alldata="-") {
		$condition ="";
    	if(isset($comtype)){
    		switch ($comtype) {
    			case "0":
    				$this->db->where('COM_TYPECD = 0');
    				break;
    			case "X":
    				break;
    			default:
					$this->db->where('COM_TYPECD <> 0');
					$this->db->where('COM_TYPECD',$comtype);
    				break;
    		}
    	}else{
			$this->db->where('COM_TYPECD <> 0');
		}
    	if($com_descr2!=""){
			$this->db->where('COM_DESCR2',$com_descr2);
    	}
	    if($exception!=""){
	    	if(!is_array($exception)){
	    		$this->db->where('COM_TYPECD <>',$exception);	
	    	}else{
	    		$this->db->where_not_in("COM_TYPECD", $exception);
	    	}
	    }

		$this->db->select('COM_HEADCD, COM_TYPECD, COM_DESCR2, COM_IDENTS');
		if($this->LANG!=1){
			$this->db->select("COM_DESCRB COM_DESCR1");
		}else {
			$this->db->select("COM_DESCR1");
		}
		$this->db->from($this->table_common);
		$this->db->where("IFNULL(COM_is_deleted,0) <> 1");
		if(isset($comhead)){
			if(strpos($comhead,"~")==0){
				$this->db->where('COM_HEADCD', $comhead);
			}else{
				$arrParam = explode("~", $comhead);
				
				$this->db->where_in('COM_HEADCD', $arrParam);
			}
		}
		$this->db->order_by('COM_TYPECD asc');
	    $query = $this->db->get();
	    $this->__numRows = $query->num_rows();
		switch($type){
			case "1" :
				$data = ($this->returnArray) ? $query->result_array() : $query->result();
				break;
			case "2" :
				$data = $query->row();
				break;
			case "3" :
		        $data['0'] = $alldata;
		        foreach($query->result() as $row){
		            $data[$row->COM_TYPECD] = $row->COM_DESCR1;
		        }
				break; 
			case "4" :
		        $data[''] = '-';
		        foreach($query->result() as $row){
		            $data[$row->COM_IDENTS] = $row->COM_DESCR1;
		        }
				break;
			case "5" : //json
			    $data['type'] = 'cmb';
			    $data['Hasil'] = $query->result();
			    break;
			case "6" :
		        foreach($query->result() as $row){
		            $data[$row->COM_TYPECD] = $row->COM_DESCR1;
		        }
				break;
			case "997":
				$data = $query;
				break;
		}
		return $data;
  	}
	function getTable_edit($table, $idents, $param){
	    $this->db->select('*');
	    $this->db->from($table);
	    $this->db->where($idents, $param);
	    $query = $this->db->get();
		$row = $query->row();
		return $row;
	}
	function getTaginput($table, $arrfield, $filter=null, $addfil=null, $protected=true, $separator=null){
		$field = "*";
		$where = "";
		$separator1 = "";

		if($separator!=""){
			if(is_array($separator)){
				$arrValue = $separator;
			}else{
				$arrValue = explode("^", $separator);
			}
			if(count($arrValue)>1){
				$loop = 1;
				for($e=0;$e<count($arrValue);$e++){
					${"separator".$loop} = $arrValue[$e];
					$loop++;
				}
			}else{
				$separator1 = $separator;
			}
		}
		foreach($arrfield as $key=>$value){
			if($key=="id"){
				$id = $value;
			};
			if($key=="field"){
				if(is_array($value)){
					$arrValue = $value;
				}else{
					$arrValue = explode("^", $value);
				}
				if(count($arrValue)>1){
					// $field = $arrValue[0] ;//. " as " . $arrValue[1];
					// debug_array($field = $arrValue[0]);
					$field = "";
					$rc = false;
					$loop = 0;
					if($this->driver=="mysqli"){
						$field ="CONCAT(";
					}					
					for($e=0;$e<count($arrValue);$e++){
						if($this->driver=="sqlsrv"){
							if($rc) $field .="+";
							$field .= ($loop==0 ? "'" . $separator1 . "' + " : "" ) . $arrValue[$e] . ($loop==0 ? (isset($separator2) ? "+ '" . $separator2 . " '" : "") : "" );
						}
						if($this->driver=="mysqli"){
							if($rc) $field .=", ' ', ";
							$field .= ($loop==0 ? "'" . $separator1 . "', " : "" ) . $arrValue[$e] . ($loop==0 ? (isset($separator2) ? ",'" . $separator2 . " '" : "") : "" );
						}
						$rc = true;
						$loop++;
					}
					if($this->driver=="mysqli"){
						$field .=")";
					}
				}else{
					$field = $value;	
				}
			}
			if($key=="where"){
				unset($arrValue);
				if(is_array($value)){
					$arrValue = $value;
				}else{
					$arrValue = explode("^", $value);
				}
				if(is_array($arrValue)){
					$where = "";
					$rc = false;
					if($this->driver=="mysqli"){
						$where ="CONCAT(";
					}
					for($e=0;$e<count($arrValue);$e++){
						if($this->driver=="sqlsrv"){
							if($rc) $where .="+";
						}
						if($this->driver=="mysqli"){
							if($rc) $where .=",";
						}
						$where .= $arrValue[$e];
						$rc = true;
					}
					if($this->driver=="mysqli"){
						$where .=")";
					}					
				}else{
					$where = $value;
				}
			};

			if($key=="extra"){
				if(is_array($value) <> 0){
					foreach ($value as $idx => $str) {
						# code...
						if($idx != 'id' && $idx != 'name'){///jangan sampe index-nya pake reserved alias
							$this->db->select($str . ' as ' . $idx, false);
						}
					}
				}
			}
		}
		$this->db->select($id . ' as id', false);
		$this->db->select($field .' as text', false);
		$this->db->from($table . ' a', $protected);
		$this->db->limit(20);
		// $this->common->debug_array($where);
		// if(isset($filter)){
			// $this->db->like('UPPER('.$where .')', strtoupper($filter));
			$this->db->where("UPPER(".$where .") like '%" . $_GET["q"] . "%'");
		// }

		// $this->common->debug_sql(1, 1);
		if(isset($filter)){
			if(is_array($filter)){
				foreach ($filter as $value) {
					# code...
					$this->db->where($value,NULL,FALSE);
				}
			}
		}
		$query = $this->db->get();
		return $query;
	}
	function revertcommon($descr, $parameter=null, $insert=false){
		$descr = str_replace(" ", "", $descr);
		if($parameter!=""){
			foreach ($parameter as $key => $value) {
				${$key} = $value;
			}
		}
		$this->db->select('COM_IDENTS, COM_TYPECD, COM_HEADCD, COM_DESCR1');
		$this->db->from($this->table_common);
		if($descr!=""){
			if(isset($format)){
				switch ($format) {
					case '1':
						// $this->db->where('replace(upper(COM_DESCR1),\' \',\'\')=',strtoupper($descr));
						$this->db->where('upper(COM_DESCR1)', strtoupper($descr));
						break;
					case '2':
						$this->db->where('upper(COM_DESCR2)', strtoupper($descr));
						break;
					default:
						$this->db->where('replace(upper(COM_DESCR1),\' \',\'\')=',strtoupper($descr));
						break;
				}			
			}else{
				$this->db->where('replace(upper(COM_DESCR1),\' \',\'\')=',strtoupper($descr));
			}			
		}
		if(isset($headcd)){
			$this->db->where('COM_HEADCD', $headcd);
		}
		if(isset($typecd)){
			$this->db->where('COM_TYPECD', $typecd);
		}		
		$revert = $this->db->get();
		if($revert->num_rows()>0){
			$rowrev = $revert->row();
			if(isset($hasil)){
				switch ($hasil) {
					case '1':
						$return = $rowrev->COM_TYPECD;
						break;
					case '2':
						$return = $rowrev->COM_HEADCD;
						break;	
					case '99':
						$return = $rowrev->COM_IDENTS;
						break;
					case '3':
						$return = $rowrev->COM_DESCR1;
						break;
					case '4':
						$return["COM_DESCR1"] = $rowrev->COM_DESCR1;
						$return["COM_HEADCD"] = $rowrev->COM_HEADCD;
						break;
					default:
						$return = $rowrev->COM_DESCR1;
						break;
				}
			}else{
				$return = $rowrev->COM_HEADCD;
			}
			
			// }
		}else{
			$return = 0;
			if($insert){
				$this->db->select_max('"COM_TYPECD"');
				$this->db->from('COMMON');
				$this->db->where('COM_HEADCD', $headcd);
				
				$comtypecd = $this->db->get()->row()->COM_TYPECD+1;
				$input = array('COM_HEADCD'=>$headcd, 'COM_TYPECD'=> $comtypecd , 'COM_DESCR1'=>$descr);
				$this->db->insert('COMMON', $input);
				$return = $comtypecd ;			
			}
		}
		return $return;
	}	
	function getMaxcommon($headcd){
		$this->db->select_max('COM_TYPECD');
		$this->db->from($this->table_common .' a');
		$this->db->where('COM_HEADCD', $headcd);
		$query = $this->db->get();
		if($query->num_rows()>0){
			$common = $query->row();
			return $common->COM_TYPECD;
		}	
	}
	function loginmenu($username, $app_numbr){
		$this->db->from($this->table_usermenu);
		$this->db->where("MNU_LEVELS", $username);
		if($app_numbr!="9999"){
			$this->db->where("MNU_APPLIC", $app_numbr);		
		}
		return $this->db->count_all_results();
	}
	function login($username, $type=1){
		$this->db->select("a.*");
		$this->db->select("USR_THEMES");
		$this->db->select("x.COM_DESCR1 USR_LEVEL_DESC, f.unt_unitkerja USR_UNITKERJA_DESC");
		// $this->db->select("PRV_NAMESS, KAB_NAMESS");
		$this->db->select($this->common->isnulldb("a.USR_LAYOUT",2,"USR_LAYOUT"));
		$this->db->from($this->table_user . ' a');
		$this->db->join('t_mas_common x','a.USR_LEVELS = x.COM_TYPECD and x.COM_HEADCD = 9 AND x.COM_TYPECD <> 0','INNER');
        $this->db->join("t_mas_unitkerja f", "a.USR_IDENTS = f.unt_idents","LEFT");
        // $this->db->join("t_mas_province f", "a.USR_UNITKERJA = f.PRV_IDENTS","LEFT");
        // $this->db->join("t_mas_kabupaten g", "a.USR_KABPTN = g.KAB_IDENTS","LEFT");
		if($this->config->item('employee')){
			$this->db->select("b.EMP_FNAMES, b.EMP_DEPTMN, b.EMP_DVSION, b.EMP_SCTION, b.EMP_POSISI, b.EMP_IDENTS, b.EMP_UNIORG, EMP_EMAILS, EMP_EXTEML");
			$this->db->select("b.EMP_NOMORS, b.EMP_PLANTS, c.STR_LEADER, c.STR_CDATAS, ");
			$this->db->join('HRD_EMPLOY b','a.USR_FKEMPL = b.EMP_IDENTS','INNER');
			$this->db->join('MAS_STRKTR c','b.EMP_POSISI = c.STR_CODESS','INNER');
		}
		if($type==1){
			$this->db->where('USR_LOGINS', $this->db->escape_str($username));
		}else{
			$this->db->where('USR_ACTDIR', $username);	
		}
		$query = $this->db->get();
		return $query;
	}
	function returnforexcel($parameter=null, $paramdb="db"){
		if(isset($parameter)){
			foreach ($parameter as $key => $value) {
				${$key}=$value;
			}			
		}		
	    $sql1 = $this->db->get_compiled_select();
	    $this->db->select('*');
	    $this->db->from('(' . $sql1 .') as u', false);
	    // $this->common->debug_array($queryrelated);
	    foreach ($queryrelated as $key => $value) {
	      if(strpos("X".$key,"FILTER")>0){
	        $arrValue = explode("<@>", $value);
	        $fieldwhere = $arrValue[0]; 
	        $valuewhere = $arrValue[1];
	        if(strpos($valuewhere,"<@h@>")!==FALSE){
	        	$arrField = explode("<@e@>", $valuewhere);
	        	for($e=0;$e<count($arrField);$e++){
	        		$arrFilter = explode("<@h@>", $arrField[$e]);
	        		$valField = $arrFilter[0];
	        		$valTypes = "";
	        		$valCondt = "";
	        		if(isset($arrFilter[1])){
	        			$valTypes = $arrFilter[1];	
	        		}
	        		if(isset($arrFilter[2])){
	        			$valCondt = $arrFilter[2];
	        		}
	        		if($valCondt!=""){
	        			$this->filterparameter($paramdb, $valCondt, $fieldwhere, $valField);	
	        		}
	        	}
	        }else{
	        	$this->db->like($fieldwhere,$valuewhere);
	        }
	      }
	      if(strpos("X".$key,"SORT")>0){
	        $arrSortby = explode("<@>", $value);
	        $fieldsort = $arrSortby[0];
	        if($arrSortby[1]=='true'){
	            $direcsort = 'ASC';
	        }else{
	            $direcsort = 'DESC';
	        }
	        // $this->db->order_by($fieldsort,$direcsort);
	      }

	      if($key=="where"){
	      	$this->db->where($value);
	      }        
	    }
	    if(isset($order_by)){
	    	if(!is_array($order_by)){
	    		$this->db->order_by($order_by);	
	    	}else{
	    		for($i=0;$i<count($order_by);$i++){
	    			$this->db->order_by($order_by[$i]);
	    		}
	    	}
	    }
	    $sqlxx = $this->db->get_compiled_select();
	    // $this->common->debug_array($sqlxx);

	    // $sqlxx = htmlspecialchars(strip_tags(stripslashes($sqlxx)));
	    //$sqlxx = "SELECT * FROM (SELECT [a].[NO_SPB], [REQUESTER], [b].[STATUS], [a].[user_input], [b].[NOTE], [QTY_SPB], ISNULL(c.QTY_PO, 0) QTY_PO, [DPR_DESCRE], dbo.[fun_ITEM](b.ITEM_CODE, b.PRODUCT_NAME) PRODUCT_NAME, [b].[UM_CODE], left(CONVERT(varchar(30), a.SPB_DATE, 120), 10) SPB_DATE, left(CONVERT(varchar(30), b.DUE_DATE, 120), 10) DUE_DATE, left(CONVERT(varchar(30), a.APPROVE_DATE, 120), 10) APPROVE_DATE, ISNULL(QTY_MASUK, 0) QTY_MASUK, ISNULL(QTY_LPB_DITERIMA, 0) QTY_LPB_DITERIMA FROM TB_SPB_MASTER a INNER JOIN TB_SPB_DETAIL b ON a.NO_SPB = b.NO_SPB LEFT OUTER JOIN TB_PO_DETAIL c ON b.NO_SPB = c.NO_SPB AND b.NO_LINE_SPB = c.NO_LINE_SPB LEFT OUTER JOIN (SELECT NO_PO, NO_LINE_PO, SUM(QTY_MASUK) QTY_MASUK, SUM(QTY_LPB_DITERIMA) QTY_LPB_DITERIMA FROM TB_LPB_DETAIL GROUP BY NO_PO, NO_LINE_PO) d ON c.NO_PO = d.NO_PO AND c.NO_LINE_PO = d.NO_LINE_PO LEFT OUTER JOIN [T_MAS_DPRTMN] [f] ON [a].[SPB_DPTMEN] = [f].[DPR_CODESS] WHERE YEAR(a.SPB_DATE) > 2014 AND [b].[STATUS] NOT IN('Completed', 'Canceled', 'Void') AND [c].[NO_PO] IS NULL AND [f].[DPR_CODESS] IS NOT NULL) as u";

			// $con = mssql_connect('kimsystem','usrkemas','H4rv357-Combat') or die('Could not connect to the server!');
			// mssql_select_db('KIM') or die('Could not select a database.');
			// $hasil = mssql_query($sql) ;//or die('A error occured: ' . mysql_error());    
	    $hasil = $this->db->query($sqlxx);
	    // die();
	    return $hasil;	
	}
	function returnforjson($parameter=null, $protect=false, $paramdb="db"){
		// $this->common->debug_post();
		// $this->common->debug_array($valuecol);
		$datatables = false;
		$pagenum = $this->input->post('pagenum');
		$pagesize = $this->input->post('pagesize');	
		$sortfield = $this->input->post('sortdatafield');
		$sortorder = $this->input->post('sortorder');
		if($pagenum==""){
			$draw = $this->input->post('draw');
			$pagenum = $this->input->post('draw');//($draw==1 ? 0 : $draw);
			$columns = $this->input->post('columns');
			$pagesize = $this->input->post('length');
			$search = $this->input->post('search');
			$search = $search["value"];
			$order = $this->input->post('order');
			if(is_array($order)){
				foreach($order as $keyorder=>$valueorder){
					$indexField = $valueorder["column"];
					$sortfield = $columns[$indexField]["data"];
					if(strtoupper($sortfield)=="CI_ROWNUM"){
						if(isset($columns[$indexField+1]["data"])){
							$sortfield = $columns[$indexField+1]["data"];
						}
					}
					$sortorder = $valueorder["dir"];
				}
			}
			if(is_array($columns)){
				foreach($columns as $keycolumns=>$valuecolumns){
					// $this->common->debug_array($valuecolumns["search"]["value"]);
					if(isset($valuecolumns["search"]["value"])){
						if($valuecolumns["search"]["value"]!=""){
							$column_search_name = $valuecolumns["data"];
							$column_search_value = $valuecolumns["search"]["value"];
	
							$column_search[$column_search_name] = $column_search_value;
						}
					}
				}
			}
			if(isset($column_search)){
				$colsearch = "(";
				$rcsearch = false;
				foreach($column_search as $keycolsearch=>$valuecolsearch){
					if($rcsearch) $colsearch .= " AND ";
					$colsearch .= $keycolsearch . " like '%" . $valuecolsearch . "%'";
					$rcsearch = true;
				}
				$colsearch .= ")";
			}
			$datatables = true;
		}
		$start = ($pagenum-1) * $pagesize;
		if($this->input->post('start')!=""){
			$start = $this->input->post('start');
		}else{
			$start = $pagenum * $pagesize;
		}
		$all = false;
		if($pagesize==-1){
			$all=true;
		}
		if($paramdb!="db"){
			$this->smsdb= $this->load->database('sms', TRUE);
		}
		if(isset($parameter)){
			foreach ($parameter as $key => $value) {
				${$key}=$value;
			}			
		}
		if(!$protect){
			$prefix = $this->db->dbprefix;
    		$this->db->set_dbprefix('');
		}
		$totalrows = 0;
		if(!isset($sql)){
			$orderby = $this->db->_compile_order_by();
			$sql = $this->{$paramdb}->get_compiled_select();
			if(!empty($orderby)){
				$sql = trim(substr($sql, 0, strrpos($sql, $orderby)));
				$order_by = trim(substr($orderby, strlen('ORDER BY') + 1, strlen($orderby)));
			}
		}
		// ============== start sort 
		if ($sortfield!=null){
			if($sortfield=="EMP_NOMORS"){
				$sortfield = "(EMP_NOMORS+0)";
			}
			if ($sortorder != ''){
				if ($sortorder == "desc"){
					$this->{$paramdb}->order_by($sortfield, 'DESC');
				}else {
					if ($sortorder == "asc"){
						$this->{$paramdb}->order_by($sortfield, 'ASC');
					}
				}	
			}			
		}else{
			if(isset($order_by)){
				if(!is_array($order_by)){
					if($order_by!=""){
						$this->{$paramdb}->order_by($order_by);	
					}
				}else{
					foreach ($order_by as $key) {
						$this->{$paramdb}->order_by($key);
					}
				}
			}else{
				// $sql2 = trim(substr($sql, 0, strpos($sql, ',')));
				$sqlcut = substr($sql, 0, 200);
				$arrSQL = explode(",", $sqlcut);
				// $this->common->debug_array($arrSQL);
				for($x=0;$x<count($arrSQL);$x++){
					if(strpos($arrSQL[$x],'*')==0){
						$string = trim(str_replace("SELECT ", "", str_replace("]", "", str_replace("[", "", $arrSQL[$x])))); 
						$order = explode(" ", $string);
						// $this->common->debug_array(count($order));
						if(count($order)>1){
							$ordernya = $order[1];
						}else{
							$ordernya = $order[0];	
						}
						$this->{$paramdb}->order_by($ordernya);
						break;
					}
				}
			}
		}
		// ============== end sort
		if(!$datatables){
			$this->filtergrid($paramdb);
		}else{
			if($search!="" || isset($colsearch)){
				$this->{$paramdb}->where($colsearch);
			}
		}
		
		$this->{$paramdb}->select('*');
		$this->{$paramdb}->from('(' .  $sql . ') as a', false);
		if(!$all){
			$this->{$paramdb}->limit($pagesize, $start);	
		}
   		$result = $this->{$paramdb}->get();
		// $this->common->debug_sql(1);
		if($paramdb!="db"){
			$this->{$paramdb}->start_cache();
			$this->{$paramdb}->select('*');
			$this->{$paramdb}->from('(' . $sql . ') as u');
			$this->{$paramdb}->stop_cache();
			$totalrows = $this->{$paramdb}->count_all_results();
		}else{
			$this->db->flush_cache();
			$this->db->select('COUNT(*) TotalRows');
			$this->db->from('(' .  $sql . ') a', false);
			if(!$datatables){
				$this->filtergrid($paramdb);
			}else{
				if($search!="" || isset($colsearch)){
					$this->{$paramdb}->where($colsearch);
				}
			}
			$qTotalrows = $this->db->get();
			if($qTotalrows->num_rows()>0){
				$qrows = $qTotalrows->row();
				$totalrows = $qrows->TotalRows;
			}
			// $this->common->debug_array($totalrows, false);
			// $this->common->debug_sql(1);
		}
		if(!$protect){
    		$this->db->set_dbprefix($prefix);
		}
		if($datatables){
			$hasil["recordsTotal"] = $totalrows;
			$hasil["recordsFiltered"] = $totalrows;
			$hasil["draw"] = $draw;//($pagenum==0 ? 1 : $pagenum);
			$hasil["type"]="datatables";
		}else{
			$hasil['TotalRows'] = $totalrows;
		}
		$hasil['Hasil'] = $result->result();
		return $hasil;
	}
	function filtergrid($paramdb){
		// $this->common->debug_post();
		// ============== start filter
		if ($this->input->post('filterscount')!==null){	
			$filterscount = $this->input->post('filterscount');
			$filtergroups = $this->input->post('filterGroups');
			// $this->common->debug_array($filtergroups);

			if ($filterscount > 0){
				for ($i=0; $i < $filterscount; $i++){
					$filtervalue = $this->input->post("filtervalue" . $i);
					$filtercondition 	= $this->input->post("filtercondition" . $i);
					$filterdatafield 	= $this->input->post("filterdatafield" . $i);
					$filteroperator 	= $this->input->post("filteroperator" . $i);

					// $this->common->debug_array($filtergroups[$i]["filters"][0]["type"], false);
					// $filtertype 	= $this->input->post("type" . $i);
					$istanggal = strtotime($filtervalue);
					if(isset($filtergroups[$i]["filters"][0]["type"])){
						$type = $filtergroups[$i]["filters"][0]["type"];
						// echo $type;
					}
					if($type=="stringfilter"){
						$istanggal = false;
					}
					if($type=="datefilter"){
						$istanggal = true;
					}
					if($paramdb=='smsdb'){
						if($filterdatafield!="SenderNumber"){
							if($filterdatafield!="ReceivingDateTime"){
								$filterdatafield = "TextDecoded";
							}
						}
					}
					//tempat filterfatafield
					switch($filtercondition){
						case "EMPTY" :
							$this->{$paramdb}->where($filterdatafield . " is null");
							break;
						case "NOT_EMPTY" :
							$this->{$paramdb}->where($filterdatafield . " is not null");
							break;
						case "CONTAINS_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " like '%" . $filtervalue . "%'");
							break;
						case "CONTAINS" :
							if($istanggal){
								// $filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield . " like UPPER('%" . $filtervalue . "%')");
							}else{
								$this->{$paramdb}->where("UPPER(".$filterdatafield.") like UPPER('%" . $filtervalue . "%')");
							}
							break;
						case "DOES_NOT_CONTAIN" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield.") not like UPPER('%" . $filtervalue . "%')");
							break;
						case "DOES_NOT_CONTAIN_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " not like '%" . $filtervalue . "%'");
							break;							
						case "EQUAL" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." = '" . $filtervalue . "'");
							}else{
								$this->{$paramdb}->where("UPPER(".$filterdatafield.") = UPPER('" . $filtervalue . "')");
							}
							break;
						case "EQUAL_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " = '" . $filtervalue . "'");
							break;							
						case "NOT_EQUAL" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield.")  <> UPPER('" . $filtervalue . "')");
							break;
						case "NOT_EQUAL_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " <> '" . $filtervalue . "'");
							break;							
						case "GREATER_THAN" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								// $this->common->debug_array($filtervalue);
								$this->{$paramdb}->where($filterdatafield ." > '" . $filtervalue . "'");
							}else{						
								$this->{$paramdb}->where($filterdatafield . " > '" . $filtervalue . "'");
							}
							break;
						case "LESS_THAN" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." < '" . $filtervalue . "'");
							}else{						
								$this->{$paramdb}->where($filterdatafield . " < '" . $filtervalue . "'");
							}
							break;
						case "GREATER_THAN_OR_EQUAL" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." = '" . $filtervalue . "'");
							}else{
								$this->{$paramdb}->where($filterdatafield . " >= '" . $filtervalue . "'");
							}
							break;
						case "LESS_THAN_OR_EQUAL" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." = '" . $filtervalue . "'");
							}else{
								$this->{$paramdb}->where($filterdatafield . " <= '" . $filtervalue . "'");
							}
							break;
						case "STARTS_WITH" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield . ") like UPPER('" . $filtervalue . "%')");
							break;
						case "STARTS_WITH_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " like '" . $filtervalue . "%'");
							break;
						case "ENDS_WITH" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield . ") like UPPER('%" . $filtervalue . "')");
							break;
						case "ENDS_WITH_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " like '%" . $filtervalue . "'");
							break;
					}
				}
			}						
		}
		// ======= end of filtering
	}
	function filterparameter($paramdb, $filtercondition, $filterdatafield, $filtervalue=null){
		switch($filtercondition){
			case "EMPTY" :
				$this->{$paramdb}->where($filterdatafield . " is null");
				break;
			case "NOT_EMPTY" :
				$this->{$paramdb}->where($filterdatafield . " is not null");
				break;
			case "CONTAINS_CASE_SENSITIVE" :
				$this->{$paramdb}->where($filterdatafield . " like '%" . $filtervalue . "%'");
				break;
			case "CONTAINS" :
				$this->{$paramdb}->where("UPPER(".$filterdatafield.") like UPPER('%" . $filtervalue . "%')");
				break;
			case "DOES_NOT_CONTAIN" :
				$this->{$paramdb}->where("UPPER(".$filterdatafield.") not like UPPER('%" . $filtervalue . "%')");
				break;
			case "DOES_NOT_CONTAIN_CASE_SENSITIVE" :
				$this->{$paramdb}->where($filterdatafield . " not like '%" . $filtervalue . "%'");
				break;							
			case "EQUAL" :
				$this->{$paramdb}->where("UPPER(".$filterdatafield.") = UPPER('" . $filtervalue . "')");
				break;
			case "EQUAL_CASE_SENSITIVE" :
				$this->{$paramdb}->where($filterdatafield . " = '" . $filtervalue . "'");
				break;							
			case "NOT_EQUAL" :
				$this->{$paramdb}->where("UPPER(".$filterdatafield.")  <> UPPER('" . $filtervalue . "')");
				break;
			case "NOT_EQUAL_CASE_SENSITIVE" :
				$this->{$paramdb}->where($filterdatafield . " <> '" . $filtervalue . "'");
				break;							
			case "GREATER_THAN" :
				$this->{$paramdb}->where($filterdatafield . " > '" . $filtervalue . "'");
				break;
			case "LESS_THAN" :
				$this->{$paramdb}->where($filterdatafield . " < '" . $filtervalue . "'");
				break;
			case "GREATER_THAN_OR_EQUAL" :
				$this->{$paramdb}->where($filterdatafield . " >= '" . $filtervalue . "'");
				break;
			case "LESS_THAN_OR_EQUAL" :
				$this->{$paramdb}->where($filterdatafield . " <= '" . $filtervalue . "'");
				break;
			case "STARTS_WITH" :
				$this->{$paramdb}->where("UPPER(".$filterdatafield . ") like UPPER('" . $filtervalue . "%')");
				break;
			case "STARTS_WITH_CASE_SENSITIVE" :
				$this->{$paramdb}->where($filterdatafield . " like '" . $filtervalue . "%'");
				break;
			case "ENDS_WITH" :
				$this->{$paramdb}->where("UPPER(".$filterdatafield . ") like UPPER('%" . $filtervalue . "')");
				break;
			case "ENDS_WITH_CASE_SENSITIVE" :
				$this->{$paramdb}->where($filterdatafield . " like '%" . $filtervalue . "'");
				break;
		}
	}
  	function getDropdownlist($jenis,$extra=null){
		if(is_array($extra)){
			foreach ($extra as $key => $value) {
				# code...
				if(is_array($value)){
					$this->db->where_in($key,$value);
				}else{
					$this->db->where($key,$value);
				}
			}
		}

	  	switch ($jenis) {
	  		case 'common':
	  			# code...
					$this->db->select('COM_IDENTS, COM_HEADCD, COM_TYPECD, COM_DESCR1');
					$this->db->from($this->table_common);
				    $query = $this->db->get();
		        $data[''] = '';
		        foreach($query->result() as $row){
		            $data[$row->COM_TYPECD] = $row->COM_DESCR1;
		        }		
	  			break;
	  		default:
	  			# code...
	  			break;
	  	}

		return $data;
  	}	
	function findAll($conditions = NULL, $fields = '*', $order = NULL, $start = 0, $limit = NULL){
		 if ($conditions != NULL) {
			 $this->db->where($conditions);
		 }
		 
		 if ($fields != NULL) {
			 $this->db->select($fields);
		 }
		 
		 if ($order != NULL) {
			 $this->db->order_by($order);
		 }
		 
		 if ($limit != NULL) {
			 $this->db->limit($limit, $start);
		 }
		 
		 $query = $this->db->get($this->table);
		 //echo $this->db->last_query();
		 $this->__numRows = $query->num_rows();
		 return ($this->returnArray) ? $query->result_array() : $query->result();
	}
	function exeHapuslink($NOMORS,$APPLIC){
		$this->crud->useTable("RND_RNDGEN");
		$this->crud->delete(array("RND_NOTRAN"=>$NOMORS,"RND_APPLIC"=>$APPLIC));
		$this->crud->unsetMe();
	}
  	function getTabledatabase($type=1, $database='mssql'){	
		$this->db->select("t.NAME AS TBL_NAMESS");
		$this->db->select("s.Name AS TBL_SCHEMA");
		$this->db->select("p.rows AS TBL_ROWCNT");
		$this->db->select("SUM(a.total_pages) * 8 AS TBL_TOTSPC");
		$this->db->select("SUM(a.used_pages) * 8 AS TBL_USDSPC");
		$this->db->from("sys.tables t", false);
		$this->db->join("sys.indexes i", "t.OBJECT_ID = i.object_id", "INNER", false);
		$this->db->join("sys.partitions p", "i.object_id = p.OBJECT_ID AND i.index_id = p.index_id", "INNER", false);
		$this->db->join("sys.allocation_units a", "p.partition_id = a.container_id", "INNER", false);
		$this->db->join("sys.schemas s", "t.schema_id = s.schema_id","LEFT OUTER", false);
		$this->db->where("t.NAME NOT LIKE 'dt%' ");
		$this->db->where("t.is_ms_shipped = 0");
		$this->db->where("i.OBJECT_ID > 255");
		$this->db->group_by("t.Name, s.Name, p.Rows");
		// echo $this->db->get_compiled_select();
		$hasil = $this->crud->returnforjson(array("order_by"=>"TBL_USDSPC"));
		return $hasil;
  	}
  	function getTableInfo($tablename, $database="mssql", $catalog=null, $schema=null){
		$this->db->select("COLUMN_NAME TBL_COLUMN");
		$this->db->select("ORDINAL_POSITION, ");
		$this->db->select("IS_NULLABLE TBL_NULLBL");
		$this->db->select("DATA_TYPE TBL_DATTYP");
		$this->db->select("CHARACTER_MAXIMUM_LENGTH TBL_LENGTH");
		$this->db->from("INFORMATION_SCHEMA.COLUMNS", false);
		$this->db->where("TABLE_CATALOG", $catalog);
		$this->db->where("TABLE_SCHEMA",$schema);
		$this->db->where("TABLE_NAME", $tablename);
		// echo $this->db->get_compiled_select();
		$hasil = $this->crud->returnforjson(array("order_by"=>"ORDINAL_POSITION"));
		return $hasil;
	}  
	function deleteparent($LOGINS){
		if($this->security_level=="user"){
			$this->db->where("MNU_FKUSER", $LOGINS);
			$fieldfk = "MNU_FKUSER";
		}else {
			$this->db->where("MNU_LEVELS", $LOGINS);
			$fieldfk = "MNU_LEVELS";
		}		
		$this->db->select("CONCAT(a.MNU_APPLIC, MNU_NOMORS) MNU_PARENT, a.MNU_APPLIC, MNU_NOMORS, " . $fieldfk . " as MNU_FKUSER");
		$this->db->from($this->table_usermenu . " a");
		$this->db->join($this->table_menu . " b", "a.MNU_APPLIC = b.MNU_APPLIC AND a.MNU_MENUCD = b.MNU_NOMORS","INNER");
		$this->db->wherE("MNU_HVCHLD = 1");

		$rslParent = $this->db->get();

		foreach ($rslParent->result() as $value){
			$count = 0;
			$PARENT = $value->MNU_PARENT;
			$APPLIC = $value->MNU_APPLIC;
			$NOMORS = $value->MNU_NOMORS;

			$this->db->from($this->table_usermenu . " a");
			$this->db->join($this->table_menu . " b", "a.MNU_APPLIC = b.MNU_APPLIC AND a.MNU_MENUCD = b.MNU_NOMORS","INNER");
			if($this->security_level=="user"){
				$fieldpk = "MNU_FKUSER";
			}else {
				$fieldpk = "MNU_LEVELS";
			}
			$this->db->where($fieldpk, $LOGINS);
			$this->db->where("MNU_PARENT", $PARENT);

			$count = $this->db->count_all_results();
			// $this->common->debug_sql(1);
			if($count==0){
				$this->db->delete($this->table_usermenu, array("MNU_APPLIC"=>$APPLIC, "MNU_MENUCD"=>$NOMORS, $fieldpk=>$LOGINS));
			}
		}
	}
	function getApplication_list($exception=null, $only=null){
		$this->db->from("m_application");
		if($exception!=null){
			if(is_array($exception)){
				foreach($exception as $key=>$value){
					${$key} = $value;
				}
			}
			if($app_applic!=""){
				$this->db->where_not_in("APP_APPLIC", $app_applic);
			}
		}
		if($only!=null){
			if(is_array($only)){
				foreach($only as $key=>$value){
					${$key} = $value;
				}
			}
			if($app_applic!=""){
				$this->db->where_in("APP_APPLIC", $app_applic);
			}			
		}
		// echo $this->db->get_compiled_select();
		// die();
		$result = $this->db->get();
		return $result;
	}
	function getApplication($env, $url){
		$search = array("http://","https://","/");
		$url = str_replace($search, "", $url);

		$this->db->from("m_application");
		switch ($env) {
			case 'development':
				$this->db->like("app_dev", $url);
				break;
			case 'production':
				$this->db->like("app_url", $url);
				break;
			case 'local':
				$this->db->like("app_devloc", $url);
				break;	
			default:
				# code...
				break;
		}
		$query = $this->db->get();
		// $this->common->debug_sql(true);
	    if($query->num_rows()!=0){
	    	$row = $query->row();
		    return $row;
	    }else{
		    return NULL;
	    }
	}
	function filterdirect($parameter, $paramdb="db"){
		// ============== start filter
		if($parameter!=null){
			foreach($parameter as $keydb=>$valuedb){
				// debug_array(substr($keydb,0,6), false);
				if(substr($keydb,0,6)=="FILTER"){
					$type = "stringfilter";
					$arrJson = json_decode($valuedb);
					$filterdatafield = null;
					$filtervalue = null;
					$filtercondition= null;
					$filtertype = null;
					if(isset($arrJson->column)){
						$filterdatafield = $arrJson->column;
					}
					if(isset($arrJson->value)){
						$filtervalue = $arrJson->value;
					}
					if(isset($arrJson->condition)){
						$filtercondition = $arrJson->condition;
					}
					// debug_array($arrJson->column);

					$istanggal = strtotime($filtervalue);
					if(isset($filtertype)){
						$type = $filtertype;
					}
					if($type=="stringfilter"){
						$istanggal = false;
					}
					if($type=="datefilter"){
						$istanggal = true;
					}

					switch($filtercondition){
						case "EMPTY" :
							$this->{$paramdb}->where($filterdatafield . " is null");
							break;
						case "NOT_EMPTY" :
							$this->{$paramdb}->where($filterdatafield . " is not null");
							break;
						case "CONTAINS_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " like '%" . $filtervalue . "%'");
							break;
						case "CONTAINS" :
							if($istanggal){
								// $filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield . " like UPPER('%" . $filtervalue . "%')");
							}else{
								$this->{$paramdb}->where("UPPER(".$filterdatafield.") like UPPER('%" . $filtervalue . "%')");
							}
							break;
						case "DOES_NOT_CONTAIN" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield.") not like UPPER('%" . $filtervalue . "%')");
							break;
						case "DOES_NOT_CONTAIN_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " not like '%" . $filtervalue . "%'");
							break;							
						case "EQUAL" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." = '" . $filtervalue . "'");
							}else{
								$this->{$paramdb}->where("UPPER(".$filterdatafield.") = UPPER('" . $filtervalue . "')");
							}
							break;
						case "EQUAL_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " = '" . $filtervalue . "'");
							break;							
						case "NOT_EQUAL" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield.")  <> UPPER('" . $filtervalue . "')");
							break;
						case "NOT_EQUAL_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " <> '" . $filtervalue . "'");
							break;							
						case "GREATER_THAN" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								// $this->common->debug_array($filtervalue);
								$this->{$paramdb}->where($filterdatafield ." > '" . $filtervalue . "'");
							}else{						
								$this->{$paramdb}->where($filterdatafield . " > '" . $filtervalue . "'");
							}
							break;
						case "LESS_THAN" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." < '" . $filtervalue . "'");
							}else{						
								$this->{$paramdb}->where($filterdatafield . " < '" . $filtervalue . "'");
							}
							break;
						case "GREATER_THAN_OR_EQUAL" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." = '" . $filtervalue . "'");
							}else{
								$this->{$paramdb}->where($filterdatafield . " >= '" . $filtervalue . "'");
							}
							break;
						case "LESS_THAN_OR_EQUAL" :
							if($istanggal){
								$filtervalue = $this->common->parseDate($filtervalue, 'Y-m-d');
								$this->{$paramdb}->where($filterdatafield ." = '" . $filtervalue . "'");
							}else{
								$this->{$paramdb}->where($filterdatafield . " <= '" . $filtervalue . "'");
							}
							break;
						case "STARTS_WITH" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield . ") like UPPER('" . $filtervalue . "%')");
							break;
						case "STARTS_WITH_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " like '" . $filtervalue . "%'");
							break;
						case "ENDS_WITH" :
							$this->{$paramdb}->where("UPPER(".$filterdatafield . ") like UPPER('%" . $filtervalue . "')");
							break;
						case "ENDS_WITH_CASE_SENSITIVE" :
							$this->{$paramdb}->where($filterdatafield . " like '%" . $filtervalue . "'");
							break;
					}					
				}
				//tempat filterfatafield
			}
		}
		// ======= end of filtering
	}
	function getSubject_email($SUBJECT){
		$this->db->from("m_subject_email");
		$this->db->where("sbj_subject", $SUBJECT);
		$result = $this->db->get();
		return $result;
	}
    function getUsers_list(){
        $this->db->select("USR_IDENTS, USR_FNAMES, USR_LOGINS, USR_ACTDIR");
        $this->db->select("b.COM_DESCR1 USR_LEVELS, c.COM_DESCR1 USR_ACTIVE");
        $this->db->select("d.COM_DESCR1 USR_ACCESS, USR_USRDAT, CASE USR_WEBSVC WHEN 1 THEN 'Y' ELSE 'T' END USR_WEBSVC");
        $this->db->from($this->table_user . " a");
        $this->db->join($this->table_common . " b","a.USR_LEVELS = b.COM_TYPECD AND b.COM_HEADCD = 9","INNER");
        $this->db->join($this->table_common . " c","a.USR_ACTIVE = c.COM_TYPECD AND c.COM_HEADCD = 99","LEFT");
        $this->db->join($this->table_common . " d","a.USR_ACCESS = d.COM_TYPECD AND d.COM_HEADCD = 99","LEFT");
        $this->db->where("USR_ACCESS", 1);
        $hasil = $this->crud->returnforjson(array('order_by'=>'USR_USRDAT desc'));
        return $hasil;
	}
	function createnumber($typenomor){
        $this->db->from("m_nomor");
        $this->db->where("nom_id", $typenomor);
        $this->db->where("nom_year", date('Y'));

        $pk = array("nom_id"=>$typenomor,"nom_year"=>date('Y'));
        $seqnc = 1;
        $rsl = $this->db->get();
        if($rsl->num_rows()>0){
            $rowNomor = $rsl->row();
            $seqncdb = $rowNomor->nom_sequence;
            $seqnc = $seqncdb + 1;
        }
        $inputnomor["nom_sequence"] = $seqnc;
        $inputnomor["nom_usrnam"] = $this->username;
        $inputnomor["nom_usrdat"] = $this->datesave;
        
        $inputnomor = array_merge($inputnomor, $pk);

        $this->crud->useTable("m_nomor");
		$this->crud->save($inputnomor, $pk);
		
		return $seqnc;
	}
	function getUserActivitylast_list($limit=5){
		$this->db->from("t_log_aktivitas");
		$this->db->where("log_usrnam", $this->username);
		$this->db->order_by("log_usrdat desc");
		$this->db->limit($limit);

		$result = $this->db->get();

		return $result;
	}
	function getLogin($id, $type=1){
		$this->db->from("t_mas_usrapp");
		if($type==1){
			$this->db->where("USR_LOGINS", $id);
		}
		if($type==2){
			$this->db->where("USR_IDENTS", $id);
		}
		$this->db->where("IFNULL(USR_ACCESS,1) <> 2");
		$this->db->limit(1);
		$result = $this->db->get();
		return $result;
	}

	function insertInbox($title,$msg,$usr_logins=null, $usr_sender=null, $id_modul=null, $delivery_status=null,$delivery_plan=NULL) {
        $data = array("inb_title"=>$title,
                "inb_message"=>$msg,
                "inb_usrlogins"=>$usr_logins,
				"inb_fkidents"=>$id_modul, 
                "inb_usrnam"=>$usr_sender,
                "inb_delivery_status"=>$delivery_status,
                "inb_delivery_plan_date"=>$delivery_plan);
        $this->db->insert("t_inbox",$data);
	}
	function getInbox_list(){
		$this->db->select("inb_idents, inb_title, inb_message, inb_usrnam, inb_usrdat");
		$this->db->select("CASE inb_is_read 	WHEN IFNULL(inb_is_read,0) = 1 THEN 'Sudah Dibaca' 
		WHEN IFNULL(inb_is_read,0) = 2 THEN 'Dibalas' 
		ELSE 'Belum Dibaca' 
	 END inb_is_read_desc");
        $this->db->from("t_inbox a");
		$this->db->where("inb_usrlogins", $this->username);
        $hasil = $this->crud->returnforjson(array('order_by'=>'inb_idents desc'));
		// $this->common->debug_sql(1);
        return $hasil;
	}
	function getInboxunread(){
        $this->db->from("t_inbox a");
		$this->db->where("IFNULL(inb_is_read,0) = 0");
		$this->db->where("inb_usrlogins", $this->username);
		$count = $this->db->count_all_results();
		return $count;		
	}
	function getInbox_edit($inb_idents){
		$this->db->from("t_inbox a");
		$this->db->join("t_asm_asesmen_jawaban b", "a.inb_fkidents = b.jwb_idents", "LEFT");
		$this->db->join("t_mas_pertanyaan c", "b.jwb_tnyidents = c.tny_idents", "LEFT");
		$this->db->where("inb_idents", $inb_idents);
		$query = $this->db->get();
		$data = $query->row();
		return $data;
	}
}
/* End of file crud.php */
/* Location: ./application/model/crud.php */

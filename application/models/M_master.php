<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_master extends CI_Model{
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
        $this->username = $this->session->userdata('USR_LOGINS');
        $this->usrlevel = $this->session->userdata('USR_LEVELS');
        $this->usrauthrz = $this->session->userdata('USR_AUTHRZ');
        $this->app_numbr = $this->session->userdata('app_numbr');
        $this->usrtypusr = $this->session->userdata('USR_TYPUSR');

        $this->usrunitkerja = $this->session->userdata('USR_UNITKERJA');

		$this->table_user = $this->config->item('tbl_user');
		$this->table_menu = $this->config->item('tbl_menu');
		$this->table_common = $this->config->item('tbl_common');
		$this->table_usermenu = $this->config->item('tbl_usermenu');
        $this->security_level = $this->config->item('security_level');

        if($this->security_level!="level"){
            $this->fieldFKUSER = "MNU_FKUSER";
        }else{
            $this->fieldFKUSER = "MNU_LEVELS";
        }
    }
    function getChangePass_edit($IDENTS){
        $this->db->select('USR_IDENTS,USR_LOGINS,USR_PASSWD,USR_FNAMES, USR_LAYOUT, USR_THEMES, USR_LANGUAGE');
        $this->db->from($this->table_user);
        $this->db->where('USR_LOGINS',$IDENTS);
        $query = $this->db->get();
        return $query->row();
    }
    function getUsers_list($status){
        $this->db->select("USR_IDENTS, USR_LOGINS, USR_ACTDIR, USR_USRNAM");
        // $this->db->select("USR_IDENTS, IFNULL(emp_name, USR_FNAMES) USR_FNAMES, USR_LOGINS, USR_ACTDIR");
        $this->db->select("b.COM_DESCR1 USR_LEVELS, c.COM_DESCR1 USR_ACTIVE, USR_UNITKERJA, unt_unitkerja USR_UNITKERJA_DESC");
        $this->db->select("d.COM_DESCR1 USR_ACCESS, USR_USRDAT, CASE USR_WEBSVC WHEN 1 THEN 'Y' ELSE 'T' END USR_WEBSVC");
        $this->db->from($this->table_user . " a");
        $this->db->join($this->table_common . " b","a.USR_LEVELS = b.COM_TYPECD AND b.COM_HEADCD = 9","LEFT");
        $this->db->join($this->table_common . " c","a.USR_ACTIVE = c.COM_TYPECD AND c.COM_HEADCD = 99","LEFT");
        $this->db->join($this->table_common . " d","a.USR_ACCESS = d.COM_TYPECD AND d.COM_HEADCD = 99","LEFT");
        if($this->config->item('employee')){
            $this->db->select("emp_name USR_FNAMES");
            $this->db->join("t_hrd_employee e","a.USR_EMPIDENTS = e.emp_idents","LEFT", false);
        }else{
            $this->db->select("USR_FNAMES");
        }
        $this->db->join("t_mas_unitkerja f", "a.USR_UNITKERJA = f.unt_idents","LEFT");
        // $this->db->join("t_mas_province f", "a.USR_UNITKERJA = f.PRV_IDENTS","LEFT");
        // $this->db->join("t_mas_kabupaten g", "a.USR_KABPTN = g.KAB_IDENTS","LEFT");
        if($status!=1){
            $this->db->where("IFNULL(USR_ACCESS,0) <> 1");
        }else {
            $this->db->where("USR_ACCESS", $status);
        }
        
        if($this->usrlevel!=1){ //Super Admin dan Administrator Aplikasi
            if($this->usrlevel!=2){
                $this->db->where("USR_LEVELS >= " . $this->usrlevel);
                $this->db->where("USR_UNITKERJA", $this->usrunitkerja);
                $this->db->where("USR_USRNAM", $this->username);
            }else{
                $this->db->where("USR_LEVELS >= " . $this->usrlevel);
            }
        }
        // $this->common->debug_sql(true, true);
        $hasil = $this->crud->returnforjson(array('order_by'=>'USR_USRDAT desc'));
        return $hasil;
        
    }
    function getUserlevel_list(){
        $this->db->select("COM_TYPECD, COM_DESCR1, COM_USRNAM, COM_USRDAT");
        $this->db->from($this->table_common . " a");
        $this->db->where("COM_HEADCD = 9");
        $this->db->where("COM_TYPECD <> 0");
        $hasil = $this->crud->returnforjson(array('order_by'=>'COM_TYPECD asc'));
        return $hasil;
        
    }
    function getUsers_edit($IDENTS){
        $this->db->select("USL_USRNAM, MAX(USL_USRDAT) TANGGAL_AKHIR");
        $this->db->from("t_USRLOG");
        $this->db->group_by("USL_USRNAM");
        $sqlLogMax = $this->db->get_compiled_select();

        $this->db->select("a.USL_USRNAM, USL_USRDAT, USL_ADDRES");
        $this->db->from("t_USRLOG a");
        $this->db->join("(" . $sqlLogMax . ") b", "a.USL_USRNAM = b.USL_USRNAM AND a.USL_USRDAT = b.TANGGAL_AKHIR", "INNER", false);
        $sqlLogMax = $this->db->get_compiled_select();

        $this->db->select("USR_IDENTS, USR_FNAMES, USR_LOGINS, USR_EMPIDENTS");
        $this->db->select("USR_PASSWD, USR_LEVELS, USR_ACCESS, USR_WEBSVC");
        $this->db->select("USR_LAYOUT, USR_THEMES, USR_ACTDIR, USR_USRNAM, USR_UPDNAM");
        $this->db->select("b.COM_DESCR1 USR_LEVELD, c.COM_DESCR1 USR_ACTIVE, USR_LANGUAGE, USR_UNITKERJA, USR_AUTUNIT");
        // $this->db->select("g.COM_DESCR1 USR_WEBSVD");
        $this->db->select("d.COM_DESCR1 USR_ACCESD, USR_USRDAT, USR_TYPUSR, USR_AUTHRZ, USL_USRDAT, USL_ADDRES");
        // $this->db->select("CASE USR_TYPUSR WHEN 1 THEN e.cb_name WHEN 2 THEN f.mb_name WHEN 3 THEN ff.fsk_nama WHEN 4 THEN gg.kst_name END USR_AUTHRD");
        // $this->db->select("USL_USRDAT, USL_ADDRES");
        $this->db->from($this->table_user . " a");
        $this->db->join($this->table_common . " b","a.USR_LEVELS = b.COM_TYPECD AND b.COM_HEADCD = 9","LEFT");
        $this->db->join($this->table_common . " c","a.USR_ACTIVE = c.COM_TYPECD AND c.COM_HEADCD = 99","LEFT");
        $this->db->join($this->table_common . " d","a.USR_ACCESS = d.COM_TYPECD AND d.COM_HEADCD = 99","LEFT");
        if($this->config->item('employee')){
            $this->db->select("emp_name");
            $this->db->join("t_hrd_employee e","a.USR_EMPIDENTS = e.emp_idents","LEFT", false);
        }
        $this->db->join("(" . $sqlLogMax. ") h", "a.USR_LOGINS = h.USL_USRNAM", "LEFT OUTER");
        if(is_numeric($IDENTS)){    
            if($IDENTS=="9999"){
                $this->db->where("USR_LOGINS", $IDENTS);
            }else{
                $this->db->where("USR_IDENTS", $IDENTS);
            }
        }else{
            $this->db->where("USR_LOGINS", $IDENTS);
        }
        
        // $this->common->debug_sql(1,1);
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }
    function getUserpeserta_edit($IDENTS){
        $this->db->select("usr_idents, usr_logins, usr_passwd, usr_typusr, isnull(b.bp_id, c.pen_bpid) bp_id, pen_id");
        $this->db->select("ISNULL(pen_penerima, ISNULL(b.bp_name, d.bp_name)) bp_name, b.bp_handphone, pen_handphone, usr_usrdat");
        $this->db->from("t_mas_userpeserta a");
        $this->db->join("m_peserta b", "a.usr_logins = b.bp_ktpa", "left outer");
        $this->db->join("m_pensiun c", "a.usr_logins = c.pen_nopens", "left outer");
        $this->db->join("m_peserta d", "c.pen_bpid = d.bp_id", "left outer");
        if(is_numeric($IDENTS)){    
            $this->db->where("USR_IDENTS", $IDENTS);
        }else{
            $this->db->where("usr_logins", $IDENTS);
        }
        
        $query = $this->db->get();
        $row = $query->row();
        return $row;
    }
    function getChangePasspeserta_edit($IDENTS){
        $this->db->select('usr_idents, usr_logins, usr_passwd');
        $this->db->from("t_mas_userpeserta a");
        $this->db->where('usr_logins',$IDENTS);
        $query = $this->db->get();
        return $query->row();
    }
    function chkUSRAPP($value="",$opt=1){
        $value = trim($value);
        $this->db->select("USR_IDENTS, USR_LOGINS, USR_PASSWD, USR_LEVELS, USR_ACCESS, ");
        $this->db->from($this->table_user . ' a');
        // $this->db->where("USR_LOGINS",$value);
        $this->db->where("UPPER(USR_LOGINS)", strtoupper($value));
        if($opt==1){
            $hasil = $this->db->count_all_results();
        }else{
            $hasil = $this->db->get();
        }
        return $hasil;
    }
    function chkKabptn($kabptn){
        $value = trim($kabptn);
        $this->db->select("USR_IDENTS, USR_LOGINS, USR_PASSWD, USR_LEVELS, USR_ACCESS");
        $this->db->from($this->table_user . ' a');
        $this->db->where("USR_KABPTN",$value);
        $hasil = $this->db->get();
        return $hasil;

    }
    function chkUSRACT($VALUES, $OPT=1){
        $VALUES = trim($VALUES);
        $this->db->select("USR_IDENTS, USR_LOGINS, USR_PASSWD, USR_LEVELS, USR_ACCESS, USR_ACCESS USR_ACTIVE");
        $this->db->from($this->table_user . ' a');
        $this->db->where("USR_ACTDIR",$VALUES);
        if($OPT==1){
            $return = $this->db->count_all_results();       
        }else{
            $return = $this->db->get();
        }
        return $return;
    }
    function getMenu_edit($APPLIC, $NOMORS, $USRNAM){
        $this->db->select("MNU_RIGHTS, MNU_DESCRE, a.MNU_PARENT, a.MNU_APPLIC, a.MNU_NOMORS");
        $this->db->from($this->table_menu . " a");
        $this->db->join($this->table_usermenu . ' b', "a.MNU_NOMORS = b.MNU_MENUCD AND a.MNU_APPLIC = b.MNU_APPLIC AND b.".$this->fieldFKUSER." = '" . $USRNAM. "'", "LEFT OUTER");
        $this->db->where("a.MNU_APPLIC", $APPLIC);
        $this->db->where("a.MNU_NOMORS", $NOMORS);
        $query = $this->db->get();
        // $row = $query->row();
        return $query;
    }
    function getDatatables(){
        $column = array("a.NOPENS", "NAMA","a.MAK");
        $this->db->select($column);
        $this->db->from($this->database . $this->table_yarall ." a");
        $this->db->join("V_AP3_PENS_MASTER b", "a.NOPENS = b.NOPENS", "INNER", false);
        // if($this->input->post('order')!="") // here order processing
        // {
        //     // debug_array($this->db->qb_select);
        //     debug_array($this->input->post('order'));
        // } 
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query;
    }
    function getBackup_list(){
        $this->db->from("t_mas_backup a");
        $hasil = $this->crud->returnforjson(array('order_by'=>'bck_usrdat desc'));
        return $hasil;
    }    
    function getTingkat_list($idk_idents=null, $idk_tahun=null){
        $this->db->distinct();
        $this->db->select("lvl_parent");
        $this->db->from('t_mas_level');
        $sqlparent = $this->db->get_compiled_select();

        $this->db->select('lvl_idents, lvl_nama, lvl_usrnam, lvl_usrdat, lvl_tahun, b.idk_nama lvl_kelompok_desc, c.idk_nama lvl_indikator_desc');
        $this->db->select('a.lvl_parent, CASE WHEN b.lvl_parent IS NULL THEN 1 ELSE 0 END lvl_child');
        $this->db->select('lvl_icon');
        $this->db->from('t_mas_level a', false);
        $this->db->join("t_mas_kategori b", "a.lvl_kelompok = b.idk_idents","LEFT OUTER");
        $this->db->join("t_mas_kategori c", "a.lvl_indikator = c.idk_idents","LEFT OUTER");
        $this->db->join("(" . $sqlparent . ") b", "a.lvl_idents = b.lvl_parent", "LEFT OUTER");
        $this->db->where("IFNULL(lvl_is_deleted,0) <> 1");
        
        if($idk_tahun!=""){
            $this->db->where("lvl_tahun", $idk_tahun);
        }        
        $result = $this->db->get();
        return $result;
    }
    function getTingkat_edit($lvl_idents){
        $this->db->from('t_mas_level a', false);
        $this->db->where("lvl_idents", $lvl_idents);
        $result = $this->db->get();
        return $result;
    }
    function chkTingkat($lvl_idents){
        $this->db->from("t_mas_level");
        $this->db->where("lvl_parent", $lvl_idents);
        $this->db->where("IFNULL(lvl_is_deleted,0) <> 1");

        $count = $this->db->count_all_results();

        if($count==0){
            // $count = $this->db->count_all_results();
            $return["audit"] = true;
            $return["found"] = $count;
        }else{
            $return["audit"] = false;
            $return["found"] = $count;
        }
        return $return;
    }
    function getKategori_list($idk_idents=null, $idk_tahun=null){
        $this->db->distinct();
        $this->db->select("idk_parent");
        $this->db->from('t_mas_kategori');
        $sqlparent = $this->db->get_compiled_select();

        $this->db->select('idk_idents, idk_nama, idk_usrnam, idk_usrdat, idk_tahun, COM_DESCR1 idk_type_desc, idk_type');
        $this->db->select('a.idk_parent, CASE WHEN b.idk_parent IS NULL THEN 1 ELSE 0 END idk_child');
        $this->db->select('idk_icon');
        $this->db->from('t_mas_kategori a', false);
        $this->db->join("t_mas_common b", "a.idk_type = b.COM_TYPECD AND b.COM_HEADCD = 8", "INNER");
        $this->db->join("(" . $sqlparent . ") b", "a.idk_idents = b.idk_parent", "LEFT OUTER");
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");
        
        if($idk_tahun!=""){
            $this->db->where("idk_tahun", $idk_tahun);
        }        
        $result = $this->db->get();
        return $result;
    }
    function getKategori_edit($idk_idents){
        $this->db->from('t_mas_kategori a', false);
        $this->db->where("idk_idents", $idk_idents);
        $result = $this->db->get();
        return $result;
    }

    function getPertanyaantemplate_list(){
        $this->db->from("t_mas_pertanyaan_template a");
        $hasil = $this->crud->returnforjson(array('order_by'=>'tmp_idents desc'));
        return $hasil;
    }
    function getPertanyaantemplate_edit($tmp_idents){
        $hasil = null;
        $this->db->from('t_mas_pertanyaan_template a', false);
        $this->db->where("tmp_idents", $tmp_idents);
        $query = $this->db->get();
        if($query->num_rows()>0){
            $hasil = $query->row();
        }
        
        return $hasil;
    }

    function chkKategori($kat_idents, $kat_adli){
        $this->db->select("'parent' jenis, COUNT(*) total_data");
        $this->db->from("t_mas_kategori");
        $this->db->where("idk_parent", $kat_idents);
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");

        $sqlkategori = $this->db->get_compiled_select();

        $this->db->select("'pertanyaan' jenis, COUNT(*) total_data");
        $this->db->from("t_mas_pertanyaan");
        $this->db->where("tny_indikator", $kat_idents);
        $this->db->where("IFNULL(tny_is_deleted,0) <> 1");

        $sqlpertanyaan = $this->db->get_compiled_select();

        $this->db->select("'kelompok' jenis, COUNT(*) total_data");
        $this->db->from("t_mas_pertanyaan");
        $this->db->where("tny_kelompok", $kat_idents);
        $this->db->where("IFNULL(tny_is_deleted,0) <> 1");

        $sqlkelompok = $this->db->get_compiled_select();

        $this->db->from("(" . $sqlkategori . " UNION ALL " . $sqlpertanyaan . " UNION ALL " . $sqlkelompok . ") a");

        $result = $this->db->get();
        $count = $result->num_rows();

        if($count==0){
            $return["audit"] = 'ok';
            $return["found"] = $count;
        }else{
            foreach($result->result() as $keycount=>$valuecount){
                $jenis = $valuecount->jenis;
                $count_data = $valuecount->total_data;
                if($count_data>0){
                    break;
                }
            }
            $return["audit"] = $jenis;
            $return["found"] = $count_data;
        }
        return $return;
    }

    function chkKategorikode($idk_nama, $idk_idents){
        $this->db->from("t_mas_kategori");
        $this->db->where("idk_nama", $idk_nama);
        $this->db->where("idk_idents <>" . $idk_idents);
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");

        $count = $this->db->count_all_results();
        $return["found"] = $count;
        return $return;
    }
    function chkTingkatkode($lvl_nama, $lvl_idents){
        $this->db->from("t_mas_level");
        $this->db->where("lvl_nama", $lvl_nama);
        $this->db->where("lvl_idents <>" . $lvl_idents);
        $this->db->where("IFNULL(lvl_is_deleted,0) <> 1");

        $count = $this->db->count_all_results();
        $return["found"] = $count;
        return $return;
    }
    function getKriteria_json($lvl_idents=null, $lvl_indikator=null, $combo=true) {
        $this->db->select("lvl_idents id, lvl_nama text");
        $this->db->from("t_mas_level");
        $this->db->where("lvl_parent", $lvl_idents);
        $this->db->where("lvl_indikator", $lvl_indikator);
        $result = $this->db->get();
        
        if($combo){
            $this->__numRows = $result->num_rows();
            $data = $result->result();
        }else{
            $data = $result;
        }
        return $data;
    }
    function getKategori_json($idk_idents=null, $combo=true) {
        $this->db->select("'' id, '-' text");
        $sql1 = $this->db->get_compiled_select();

        $this->db->select("idk_idents id, idk_nama text");
        $this->db->from("t_mas_kategori");
        $this->db->where("idk_parent", $idk_idents);
        $sql2 = $this->db->get_compiled_select();

        $this->db->from("(" . $sql1 . " UNION ALL " . $sql2 .") a", false);

        $result = $this->db->get();
        
        if($combo){
            $this->__numRows = $result->num_rows();
            $data = $result->result();
        }else{
            $data = $result;
        }
        return $data;
    }
    function getPertanyaan_list(){
        $this->db->select("a.tny_idents, CONCAT(substr(fnStripTags(tny_pertanyaan),1,200),' .....') tny_pertanyaan, b.idk_nama tny_kelompok_desc, c.idk_nama tny_indikator_desc, tny_usrnam, tny_usrdat, IFNULL(d.lvl_nama,'General') tny_level_desc, tny_level, tny_kriteria, e.lvl_nama tny_kriteria_desc");
        $this->db->select("tny_kelompok, tny_indikator, tny_urutan");
        $this->db->from("t_mas_pertanyaan a");
        $this->db->join("t_mas_kategori b", "a.tny_kelompok = b.idk_idents","INNER");
        $this->db->join("t_mas_kategori c", "a.tny_indikator = c.idk_idents","INNER");
        $this->db->join("t_mas_level d", "a.tny_level = d.lvl_idents","left outer");
        $this->db->join("t_mas_level e", "a.tny_kriteria = e.lvl_idents","left outer");
        $this->db->where("IFNULL(tny_is_deleted,0) <> 1");
        $hasil = $this->crud->returnforjson(array('order_by'=>'tny_kelompok, tny_level, tny_kriteria_desc, tny_indikator, tny_urutan, tny_usrdat desc'));
        return $hasil;
    }
    function getPertanyaantree_list(){
        $this->db->distinct();
        $this->db->select("CONCAT('K', idk_idents) tny_idents, idk_nama tny_pertanyaan, 0 tny_parent, 1 urutan, 0 rowlevel, null tny_usrnam, null tny_usrdat, null tny_pertanyaan_desc, null tny_metode_desc, null tny_kriteria_desc");
        $this->db->from('t_mas_kategori a');
        $this->db->join("t_mas_pertanyaan b", "a.idk_idents = b.tny_kelompok", "INNER");
        $this->db->where("IFNULL(tny_is_deleted,0) <> 1");
        $sqlkategori1 = $this->db->get_compiled_select();

        $this->db->distinct();
        $this->db->select("CONCAT('P', idk_idents) tny_idents, idk_nama tny_pertanyaan, CONCAT('K', idk_parent) tny_parent, idk_idents urutan, 1 rowlevel, null tny_usrnam, null tny_usrdat, null tny_pertanyaan_desc, null tny_metode_desc, null tny_kriteria_desc");
        $this->db->from('t_mas_kategori a');
        $this->db->join("t_mas_pertanyaan b", "a.idk_idents = b.tny_indikator", "INNER");
        $this->db->where("IFNULL(tny_is_deleted,0) <> 1");
        $sqlkategori2 = $this->db->get_compiled_select();

        $this->db->distinct();
        $this->db->select("CONCAT('L', a.idk_idents, b.idk_idents, d.lvl_idents) tny_idents, d.lvl_nama, CONCAT('P', b.idk_idents) tny_parent, 4 urutan, 3 rowlevel, null tny_usrnam, null tny_usrdat, null tny_pertanyaan_desc, null tny_metode_desc, null tny_kriteria_desc");
        $this->db->from('t_mas_kategori a');
        $this->db->join("t_mas_kategori b", "a.idk_idents = b.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan c", "a.idk_idents = c.tny_kelompok AND b.idk_idents = c.tny_indikator", "INNER");
        $this->db->join("t_mas_level d", "c.tny_level = d.lvl_idents", "INNER");
        // $this->db->from('t_mas_level d');
        $this->db->where("d.lvl_parent = 0");
        $this->db->where("IFNULL(a.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(b.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.tny_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.lvl_is_deleted,0) <> 1");
        $sqlkategori3 = $this->db->get_compiled_select();

        $this->db->distinct();
        $this->db->select("CONCAT('L', a.idk_idents, b.idk_idents, 0) tny_idents, 'General' lvl_nama, CONCAT('P', b.idk_idents) tny_parent, 3 urutan, 3 rowlevel, null tny_usrnam, null tny_usrdat, null tny_pertanyaan_desc, null tny_metode_desc, null tny_kriteria_desc");
        $this->db->from('t_mas_kategori a');
        $this->db->join("t_mas_kategori b", "a.idk_idents = b.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan c", "a.idk_idents = c.tny_kelompok AND b.idk_idents = c.tny_indikator", "INNER");
        $this->db->where("IFNULL(a.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(b.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.tny_is_deleted,0) <> 1");
        $sqlkategori4 = $this->db->get_compiled_select();        
        // CONCAT(substr(fnStripTags(tny_pertanyaan),1,550),' .....')
        $this->db->select("tny_idents, CONCAT(CASE WHEN LEFT(b.COM_DESCR1, 1) IS NULL THEN '' ELSE CONCAT(LEFT(b.COM_DESCR1, 1),'-') END, substr(fnStripTags(tny_pertanyaan),1,100), case when length(substr(fnStripTags(tny_pertanyaan),1,100))=100 then ' .....' else '' end) tny_pertanyaan");
        $this->db->select("CONCAT('L', tny_kelompok, tny_indikator, tny_level) tny_parent, CONVERT(CONCAT(tny_kelompok, tny_indikator, tny_level, tny_urutan), UNSIGNED INTEGER) urutan, 4 rowlevel, tny_usrnam, tny_usrdat, tny_pertanyaan tny_pertanyaan_desc, b.COM_DESCR1 tny_metode_desc, concat(substr(c.lvl_nama,1,60), case when length(substr(c.lvl_nama,1,60))=60 then ' .....' else '' end) tny_kriteria_desc");
        $this->db->from("t_mas_pertanyaan a");
        $this->db->join("t_mas_common b", "a.tny_metode = b.COM_TYPECD AND b.COM_HEADCD = 10", "LEFT OUTER");
        $this->db->join("t_mas_level c", "a.tny_kriteria = c.lvl_idents", "LEFT OUTER");
        $this->db->where("IFNULL(tny_is_deleted,0) <> 1");
        $sqlkategori5 = $this->db->get_compiled_select();

        $this->db->from("(" . $sqlkategori1 . " UNION ALL " . $sqlkategori2 ." UNION ALL " . $sqlkategori3 ." UNION ALL " . $sqlkategori4 ." UNION ALL " . $sqlkategori5 .") a", false);
        $this->db->order_by("urutan");
        // $this->db->select('lvl_idents, lvl_nama, lvl_usrnam, lvl_usrdat, lvl_tahun, b.idk_nama lvl_kelompok_desc, c.idk_nama lvl_indikator_desc');
        // $this->db->select('a.lvl_parent, CASE WHEN b.lvl_parent IS NULL THEN 1 ELSE 0 END lvl_child');
        // $this->db->select('lvl_icon');
        // $this->db->from('t_mas_level a', false);
        // $this->db->join("t_mas_kategori b", "a.lvl_kelompok = b.idk_idents","LEFT OUTER");
        // $this->db->join("t_mas_kategori c", "a.lvl_indikator = c.idk_idents","LEFT OUTER");
        // $this->db->join("(" . $sqlparent . ") b", "a.lvl_idents = b.lvl_parent", "LEFT OUTER");
        // $this->db->where("IFNULL(lvl_is_deleted,0) <> 1");
        // $this->common->debug_sql(1,1);
        $result = $this->db->get();
        return $result;
    }    
    function getPertanyaan_edit($tny_idents){
        $this->db->from('t_mas_pertanyaan a', false);
        $this->db->where("tny_idents", $tny_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }
        return $result;
       
    }
    function checkPertanyaan($tny_indikator){
        $this->db->from('t_mas_pertanyaan a', false);
        $this->db->where("tny_indikator", $tny_indikator);
        $this->db->where("IFNULL(tny_is_deleted,0) <> 1");
        $result = $this->db->get();
        return $result;
    }
    function getPertanyaan_template(){
        $this->db->from('t_mas_pertanyaan_template a', false);
        $this->db->where("IFNULL(tmp_is_deleted,0) <> 1");
        $this->db->where("IFNULL(tmp_active,0) = 1");
        $result = $this->db->get();
        return $result;
    }
    function getUsers_tag($param){
        // debug_array($param);
        foreach($param as $key=>$value){
            ${$key} = $value;
        }
        if(isset($_GET["q"])){
            $filter = $_GET["q"];
        }else{
            $filter = null;
        }
        
        $this->db->select('USR_IDENTS as id', false);
		$this->db->select('USR_LOGINS as text', false);
		$this->db->from('t_mas_usrapp a');
		$this->db->where("UPPER(USR_LOGINS) like '%" . $filter . "%'");
        $this->db->where("USR_LEVELS", $usr_level);
        if($lok_unitkerja!="from_supervisor"){
            $this->db->like("USR_UNITKERJA", $lok_unitkerja);
            if($this->usrlevel>2){
                $this->db->where("USR_USRNAM", $this->username);
            }
        }else{
            $arrlok_unitkerja = explode(";", $this->usrunitkerja);
            $this->db->where_in($arrlok_unitkerja);
        }
        
		$this->db->where("IFNULL(USR_ACTIVE,0) <> 1");
		$sqlperson = $this->db->get_compiled_select();
        
        $this->db->select("DISTINCT id, text");
		$this->db->from("(" . $sqlperson . ") as tabel", false);
		$this->db->limit(20);
		$query = $this->db->get();
		return $query;
    }    
    function getUnitkerja_list(){
        $this->db->select("USR_UNITKERJA unitkerja, COUNT(*) total");
        $this->db->from("t_mas_usrapp");
        $this->db->group_by("USR_UNITKERJA");
        $sqlUser = $this->db->get_compiled_select();

        $this->db->select("lok_unitkerja, COUNT(*) total");
        $this->db->from("t_asm_unitkerja");
        $this->db->where("IFNULL(lok_is_deleted,0) <> 1");
        $this->db->group_by("lok_unitkerja");
        $sqlAsesmen = $this->db->get_compiled_select();

        $this->db->select("unitkerja, SUM(total) total");
        $this->db->from("(". $sqlUser ." UNION ALL " . $sqlAsesmen . ") AS a");
        $this->db->group_by("unitkerja");
        $sqlCount = $this->db->get_compiled_select();

        $this->db->select("unt_idents, unt_unitkerja, unt_usrnam, unt_usrdat, total total_unitkerja");
        $this->db->from("t_mas_unitkerja a");
        $this->db->join("(" . $sqlCount . ") b", "a.unt_idents = b.unitkerja", "LEFT OUTER");
        $this->db->where("IFNULL(unt_is_deleted,0) <> 1");
        $hasil = $this->crud->returnforjson(array('order_by'=>'unt_idents desc'));
        return $hasil;
    }
    function getUnitkerja_edit($unt_idents){
        $hasil = null;
        $this->db->from("t_mas_unitkerja a");
        $this->db->where("a.unt_idents", $unt_idents);
        $query = $this->db->get();
        if($query->num_rows()>0){
            $hasil = $query->row();
        }
        
        return $hasil;
    }
    function getGlossary_list(){
        $this->db->from("t_mas_glossary a");
        $this->db->where("IFNULL(glb_is_deleted,0) <> 1");
        $hasil = $this->crud->returnforjson(array('order_by'=>'glb_usrdat desc'));
        return $hasil;
    }
    function getGlossary_edit($glb_idents){
        $this->db->from('t_mas_glossary a', false);
        $this->db->where("glb_idents", $glb_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }
        return $result;
       
    }    
}
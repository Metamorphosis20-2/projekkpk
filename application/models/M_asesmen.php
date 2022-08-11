<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_asesmen extends CI_Model{
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
        $this->usridents = $this->session->userdata('USR_IDENTS');
        $this->usrlevel = $this->session->userdata('USR_LEVELS');
        $this->usrauthrz = $this->session->userdata('USR_AUTHRZ');
        $this->app_numbr = $this->session->userdata('app_numbr');
        $this->usrtypusr = $this->session->userdata('USR_TYPUSR');        
        $this->usrunitkerja = $this->session->userdata("USR_UNITKERJA");
    }
    function getAsesmen_list(){
        $this->db->select("lok_asmidents, COUNT(*) total_data");
        $this->db->from("t_asm_unitkerja");
        $this->db->where("IFNULL(lok_is_deleted,0) <> 1");
        $this->db->group_by("lok_asmidents");
        $sql_lokasi = $this->db->get_compiled_select();

        $this->db->select("aso_asmidents, COUNT(*) total_data");
        $this->db->from("t_asm_asesmen_operator");
        $this->db->where("IFNULL(aso_is_deleted,0) <> 1");
        $this->db->group_by("aso_asmidents");
        $sql_operator = $this->db->get_compiled_select();
        
        $this->db->select("lok_asmidents, SUM(total_data) asm_totaldata");
        $this->db->from("(" . $sql_lokasi . " UNION ALL " . $sql_operator . ") a");
        $this->db->group_by("lok_asmidents");
        $sql_anak = $this->db->get_compiled_select();

        $this->db->from("t_asm_asesmen a");
        $this->db->join("(" . $sql_anak . ") b", "a.asm_idents = lok_asmidents", "LEFT OUTER");
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $hasil = $this->crud->returnforjson(array('order_by'=>'asm_usrdat desc'));
        return $hasil;
    }
    function getAsesmen_edit($asm_idents){
        $this->db->from("t_asm_asesmen a");
        $this->db->where("asm_idents", $asm_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;
    }
    function getAsesmendetail_edit($asm_idents){
        $this->db->select("a.par_process_area, b.idk_nama par_process_area_name, c.idk_nama par_kategori_name");
        $this->db->from("t_asm_asesmen_process_area a");
        $this->db->join("t_mas_kategori b","a.par_process_area = b.idk_idents", "INNER");
        $this->db->join("t_mas_kategori c","b.idk_parent = c.idk_idents", "INNER");
        $this->db->where("par_asmidents", $asm_idents);
        $result = $this->db->get();
        return $result;
    }
    function getUnitkerja_list(){
        $this->db->from("t_asm_unitkerja a");
        $this->db->join("t_asm_asesmen b", "a.lok_asmidents = b.asm_idents","INNER");
        $this->db->join("t_mas_unitkerja c", "a.lok_unitkerja = c.unt_idents","INNER");
        $this->db->where("IFNULL(lok_is_deleted,0) <> 1");
        $hasil = $this->crud->returnforjson(array('order_by'=>'lok_usrdat desc'));
        return $hasil;
    }
    function getUnitkerja_edit($lok_idents){
        $this->db->from("t_asm_unitkerja a");
        $this->db->join("t_asm_asesmen b", "a.lok_asmidents = b.asm_idents","INNER");
        // $this->db->join("t_mas_usrapp c", "a.lok_supervisor = c.USR_IDENTS","LEFT OUTER");
        $this->db->where("lok_idents", $lok_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;
    }    
    function getAsesmen_year($asm_idents){
        $this->db->distinct();
        $this->db->select("asm_tahun");
        $this->db->from("t_asm_asesmen a");
        $this->db->where("asm_idents <> " . $asm_idents);
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $result = $this->db->get();
        return $result;
    }
    function getAsesmenproses_list(){
        $this->db->select("lok_idents, asm_tahun, unt_unitkerja, lok_unitkerja");
        $this->db->select("asm_periode_start, asm_periode_end, null lok_supervisor, lok_usrdat");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_unitkerja b", "a.asm_idents = b.lok_asmidents","INNER");
        $this->db->join("t_mas_unitkerja c", "b.lok_unitkerja = c.unt_idents","INNER");
        // $this->db->join("t_mas_usrapp e", "b.lok_supervisor = e.USR_IDENTS","LEFT OUTER");
        $this->db->where_in("lok_unitkerja", $this->unitkerja);
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");

        // $this->common->debug_sql(1,1);

        $hasil = $this->crud->returnforjson(array('order_by'=>'lok_usrdat desc'));
        return $hasil;
    }
    function getAsesmenpenugasan_list($usr_idents=null, $usr_level=null){
        $usr_level=($usr_level=="" ? $this->usrlevel : $usr_level);
        $usr_idents=($usr_idents=="" ? $this->usridents : $usr_idents);
        if($usr_level==4){
            $this->db->select("b.idk_idents idk_parent, c.idk_idents idk_process_area, c.idk_nama, a.aso_operator");
            $this->db->select("MAX(tny_level) tny_level");
            $this->db->from("t_asm_asesmen_operator a");
            $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
            $this->db->join("t_mas_kategori c","a.aso_kelompok_indikator = c.idk_parent", "INNER");
            $this->db->join("t_mas_pertanyaan d","c.idk_idents = d.tny_indikator and tny_level <> 0", "INNER");
            $this->db->join("t_asm_asesmen_jawaban e","a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","INNER");
            $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
            $this->db->where("e.jwb_status = 21");
            $this->db->group_by("b.idk_idents, c.idk_idents, c.idk_nama, a.aso_operator");

            $sqlLevel = $this->db->get_compiled_select();

            $this->db->select("b.idk_idents idk_parent, c.idk_idents idk_process_area, c.idk_nama, a.aso_operator, f.tny_level");
            $this->db->select("COUNT(tny_pertanyaan) cnt_tny_pertanyaan, COUNT(e.jwb_idents) cnt_jwb_idents, sum(case when jwb_status=1 then 1 else 0 end) cnt_setuju");
            $this->db->from("t_asm_asesmen_operator a");
            $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
            $this->db->join("t_mas_kategori c","a.aso_kelompok_indikator = c.idk_parent", "INNER");
            $this->db->join("t_mas_pertanyaan d","c.idk_idents = d.tny_indikator and tny_level <> 0", "INNER");
            $this->db->join("t_asm_asesmen_jawaban e","a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","LEFT OUTER");
            $this->db->join("(" . $sqlLevel . ") f", "a.aso_kelompok_indikator = f.idk_parent AND a.aso_operator = f.aso_operator AND c.idk_idents = f.idk_process_area", "LEFT OUTER");
            // $this->db->join("t_mas_level g", "f.tny_level = g.lvl_idents","INNER");
            $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
            $this->db->group_by("b.idk_idents, c.idk_idents, c.idk_nama, a.aso_operator");
            $JOIN = "LEFT OUTER";
            $sqljawaban = $this->db->get_compiled_select();

            // $this->common->debug_array($sqljawaban);
    
        }else{
            $this->db->select("b.idk_idents idk_parent, c.idk_idents idk_process_area, c.idk_nama, a.aso_operator, d.tny_level");
            $this->db->select("COUNT(tny_pertanyaan) cnt_tny_pertanyaan, COUNT(e.jwb_idents) cnt_jwb_idents, sum(case when jwb_status=1 then 1 else 0 end) cnt_setuju");
            $this->db->from("t_asm_asesmen_operator a");
            $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
            $this->db->join("t_mas_kategori c","a.aso_kelompok_indikator = c.idk_parent", "INNER");
            $this->db->join("t_mas_pertanyaan d","c.idk_idents = d.tny_indikator and tny_level <> 0", "INNER");
            $this->db->join("t_asm_asesmen_process_area x","a.aso_asmidents = x.par_asmidents and c.idk_idents = x.par_process_area", "INNER");
            $this->db->join("t_asm_asesmen_jawaban e","a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","INNER");
            // $this->db->join("t_mas_level f", "d.tny_level = f.lvl_idents","INNER");
            $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
            $this->db->group_by("b.idk_idents, c.idk_idents, c.idk_nama, a.aso_operator, d.tny_level");
            $JOIN = "INNER";
            $sqljawaban = $this->db->get_compiled_select();
        }

        $this->db->distinct();
        $this->db->select("CONCAT('rowid', aso_idents) DT_RowId, aso_idents, asm_tahun, aso_kelompok_indikator, d.idk_nama aso_kelompok_indikator_desc");
        $this->db->select("e.idk_nama idk_process_area_desc, e.idk_idents idk_process_area, tny_level, IFNULL(lvl_nama,'-') lvl_nama");
        $this->db->select("asm_periode_start, asm_periode_end, b.aso_operator, c.USR_FNAMES aso_operator_name, aso_usrnam, aso_usrdat");
        // $this->db->select("CONCAT('<a href=''javascript:jvAnswer(this)'' class=''btn btn-primary btn-sm''>','&nbsp;&nbsp;<i class=''fas fa-edit'' style=''font-size:14px''></i>','</a>') btn_go");
        $this->db->select("CONCAT('<progress value=',(IFNULL(cnt_jwb_idents,0)/IFNULL(cnt_tny_pertanyaan,1))*100,' max=''100''></progress>') aso_progress, IFNULL(aso_status,0) aso_status, CASE WHEN ifnull(aso_status,0) = 0 THEN 'Draft' WHEN ifnull(aso_status,0) = 1 THEN 'Dikirim' END aso_status_desc");
        $this->db->select("ROUND((IFNULL(cnt_setuju,0)/IFNULL(cnt_jwb_idents,1))*100,2) aso_progress_value");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_operator b", "a.asm_idents = b.aso_asmidents","INNER");
        $this->db->join("t_mas_usrapp c", "b.aso_operator = c.USR_IDENTS","LEFT OUTER");
        $this->db->join("t_mas_kategori d", "b.aso_kelompok_indikator = d.idk_idents","INNER");
        $this->db->join("t_mas_kategori e","b.aso_kelompok_indikator = e.idk_parent", "INNER");
        $this->db->join("t_asm_asesmen_process_area f","b.aso_asmidents = f.par_asmidents and e.idk_idents = f.par_process_area", "INNER");
        $this->db->join("(" . $sqljawaban . ") g", "b.aso_kelompok_indikator = g.idk_parent AND b.aso_operator = g.aso_operator AND f.par_process_area = g.idk_process_area",$JOIN);
        $this->db->join("t_mas_level h", "g.tny_level = h.lvl_idents","LEFT OUTER");
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $this->db->where("IFNULL(aso_is_deleted,0) <> 1");
        if($usr_idents!=null){
            // die();
            if($usr_level==4){//operator
                $this->db->where("b.aso_operator", $usr_idents);
            }else{
                // if($usr_level==3){
                //     $this->db->where("b.aso_unitkerja", $this->usrunitkerja);
                // }
                if($usr_level==3){
                    $this->db->join("t_mas_usrapp x", "c.USR_USRNAM = x.USR_LOGINS","LEFT OUTER");
                    $this->db->where("x.USR_IDENTS", $usr_idents);
                }
            }
        }
        // $this->common->debug_sql(1,1);
        $hasil = $this->crud->returnforjson(array('order_by'=>'aso_usrdat desc'));
        return $hasil;
    }
    function getAsesmenbefore_list($aso_idents, $tny_level, $usr_idents=null, $usr_level=null){
        $usr_level=($usr_level=="" ? $this->usrlevel : $usr_level);
        $usr_idents=($usr_idents=="" ? $this->usridents : $usr_idents);

        $this->db->select("c.idk_idents idk_parent, d.idk_idents idk_process_area, c.idk_nama, b.aso_operator");
        $this->db->select("tny_level, lvl_idents, lvl_nama");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_operator b", "a.asm_idents = b.aso_asmidents","INNER");
        $this->db->join("t_mas_kategori c","b.aso_kelompok_indikator = c.idk_idents", "INNER");
        $this->db->join("t_mas_kategori d","b.aso_kelompok_indikator = d.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan e","d.idk_idents = e.tny_indikator and e.tny_level <> 0", "INNER");
        $this->db->join("t_asm_asesmen_jawaban f","b.aso_idents = f.jwb_asoidents AND e.tny_idents = f.jwb_tnyidents","INNER");
        $this->db->join("t_mas_level g", "e.tny_level = g.lvl_idents","INNER");
        $this->db->where("IFNULL(e.tny_is_deleted,0) <> 1");
        $this->db->where("f.jwb_status = 21");
        $this->db->where("tny_level <> " . $tny_level);
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $this->db->where("IFNULL(aso_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.idk_is_deleted,0) <> 1");
        $this->db->group_by("c.idk_idents, d.idk_idents, d.idk_nama, b.aso_operator, tny_level, lvl_idents, lvl_nama");
        $this->db->where("b.aso_operator", $usr_idents);
        $this->db->where("b.aso_idents", $aso_idents);

        $hasil = $this->db->get();
        return $hasil;
    }
    function getPenugasan_list($usr_idents=null, $usr_level=null){
        $usr_level=($usr_level=="" ? $this->usrlevel : $usr_level);
        $usr_idents=($usr_idents=="" ? $this->usridents : $usr_idents);

        $this->db->select("b.idk_idents idk_parent, a.aso_operator, COUNT(tny_pertanyaan) cnt_tny_pertanyaan, COUNT(e.jwb_idents) cnt_jwb_idents, sum(case when jwb_status=1 then 1 else 0 end) cnt_setuju");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
        $this->db->join("t_mas_kategori c","a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d","c.idk_idents = d.tny_indikator", "INNER");
        $this->db->join("t_asm_asesmen_jawaban e","a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","LEFT OUTER");
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        $this->db->group_by("b.idk_idents, a.aso_operator");

        $sqljawaban = $this->db->get_compiled_select();

        // $this->common->debug_array($sqljawaban);

        $this->db->select("CONCAT('rowid', aso_idents) DT_RowId, aso_idents, asm_tahun, aso_kelompok_indikator, d.idk_nama aso_kelompok_indikator_desc");
        $this->db->select("asm_periode_start, asm_periode_end, b.aso_operator, c.USR_FNAMES aso_operator_name, aso_usrnam, aso_usrdat");
        $this->db->select("CONCAT('<a href=''javascript:jvAnswer(this)'' class=''btn btn-primary btn-sm''>','&nbsp;&nbsp;<i class=''fas fa-edit'' style=''font-size:14px''></i>','</a>') btn_go");
        $this->db->select("CONCAT('<progress value=',(IFNULL(cnt_jwb_idents,0)/IFNULL(cnt_tny_pertanyaan,1))*100,' max=''100''></progress>') aso_progress, IFNULL(aso_status,0) aso_status, CASE WHEN ifnull(aso_status,0) = 0 THEN 'Draft' WHEN ifnull(aso_status,0) = 1 THEN 'Dikirim' END aso_status_desc");
        $this->db->select("ROUND((IFNULL(cnt_setuju,0)/IFNULL(cnt_jwb_idents,1))*100,2) aso_progress_value");
        // $this->db->select("0 aso_progress_value");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_operator b", "a.asm_idents = b.aso_asmidents","INNER");
        $this->db->join("t_mas_usrapp c", "b.aso_operator = c.USR_IDENTS","LEFT OUTER");
        $this->db->join("t_mas_kategori d", "b.aso_kelompok_indikator = d.idk_idents","LEFT OUTER");
        $this->db->join("(" . $sqljawaban . ") e", "b.aso_kelompok_indikator = e.idk_parent AND b.aso_operator = e.aso_operator","LEFT OUTER");
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $this->db->where("IFNULL(aso_is_deleted,0) <> 1");
        if($usr_idents!=null){
            // die();
            if($usr_level==4){//operator
                $this->db->where("b.aso_operator", $usr_idents);
            }else{
                // if($usr_level==3){
                //     $this->db->where("b.aso_unitkerja", $this->usrunitkerja);
                // }
                if($usr_level==3){
                    $this->db->join("t_mas_usrapp x", "c.USR_USRNAM = x.USR_LOGINS","LEFT OUTER");
                    $this->db->where("x.USR_IDENTS", $usr_idents);
                }
            }
        }
        // $this->common->debug_sql(1,1);
        $hasil = $this->crud->returnforjson(array('order_by'=>'aso_usrdat desc'));
        return $hasil;
    }
    function getPenugasan_edit($aso_idents){
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_usrapp b", "a.aso_operator = b.USR_IDENTS", "LEFT OUTER");
        $this->db->where("aso_idents", $aso_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;

    }
    function getKategori_assigned($year, $aso_idents=null){
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_asm_unitkerja b", "a.aso_asmidents = b.lok_asmidents", "INNER");
        $this->db->join("t_asm_asesmen c", "a.aso_asmidents = c.asm_idents", "INNER");
        $this->db->where("aso_unitkerja", $this->usrunitkerja);
        $this->db->where("asm_tahun", $year);
        if($aso_idents!=null){
            $this->db->where("aso_idents <> ". $aso_idents);
        }
        //
        $result = $this->db->get();
        return $result;
    }
    function getLokasi_assigned($type, $year, $lok_idents){
        $this->db->from("t_asm_unitkerja a");
        $this->db->join("t_asm_asesmen b", "a.lok_asmidents = b.asm_idents", "INNER");
        $this->db->where("asm_tahun", $year);
        if($lok_idents!=null){
            $this->db->where("lok_idents <> ". $lok_idents);
        }
        if($type==1){
            $result = $this->db->get();
        }else{
            foreach($result->result() as $keyI=>$valueI){
                $notinKabptn[] = $valueI->lok_kabptn;
            }
            $this->db->from("t_mas_kabupaten");
    
        }
        return $result;
    }
    function getKuesionerSpv_list(){
        $this->db->distinct();
        $this->db->select("aso_operator");
        $this->db->from('t_asm_asesmen_operator');
        $sqlparent = $this->db->get_compiled_select();
        $this->db->select('aso_kelompok_indikator, idk_nama, idk_usrnam, idk_usrdat, idk_tahun');
        $this->db->select('a.aso_operator, CASE WHEN b.aso_operator IS NULL THEN 1 ELSE 0 END idk_child');
        $this->db->select('idk_icon');
        $this->db->from('t_asm_asesmen_operator a', false);
        $this->db->join("(" . $sqlparent . ") b", "a.aso_operator = b.aso_operator", "LEFT OUTER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_idents","LEFT OUTER");
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");
        $result = $this->db->get();
        return $result;
    }
    function getKategoriJawaban($aso_idents, $idk_idents){

        $this->db->select("b.idk_idents, d.tny_level, IFNULL(jwb_status,0) jwb_status, COUNT(d.tny_idents) cnt_tny_idents, COUNT(e.jwb_idents) cnt_jwb_idents");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator","INNER");
        $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","LEFT OUTER");
        $this->db->where("aso_idents", $aso_idents);
        $this->db->where("c.idk_idents", $idk_idents);
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        $this->db->where("d.tny_level = 0");
        $this->db->group_by("b.idk_idents, d.tny_level, IFNULL(jwb_status,0)");
        $sql_1 = $this->db->get_compiled_select();

        $this->db->select("b.idk_idents, d.tny_level, IFNULL(jwb_status,0) jwb_status, COUNT(d.tny_idents) cnt_tny_idents, COUNT(e.jwb_idents) cnt_jwb_idents");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator","INNER");
        $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","LEFT OUTER");
        $this->db->where("aso_idents", $aso_idents);
        $this->db->where("c.idk_idents", $idk_idents);
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        $this->db->where("d.tny_level <> 0");
        $this->db->group_by("b.idk_idents, d.tny_level, IFNULL(jwb_status,0)");
        $sql_2 = $this->db->get_compiled_select();

        $this->db->from("(" . $sql_1 . " UNION ALL " . $sql_2 . ") AS a");
        // $this->db->order_by("b.idk_idents, d.tny_level");
        $result = $this->db->get();
        // $this->common->debug_sql(1);
        return $result;
    }
    function getKategoriJawabanMax($aso_idents, $idk_idents){

        $this->db->select("aso_idents, c.idk_idents id_process_area, b.idk_idents id_kelompok, MAX(d.tny_level) tny_level");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator","INNER");
        $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","INNER");
        $this->db->where("aso_idents", $aso_idents);
        $this->db->where("c.idk_idents", $idk_idents);
        $this->db->where("d.tny_level <> 0");
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        // $this->db->where_in("e.jwb_status", array(2, 21,22));
        $this->db->group_by("aso_idents, c.idk_idents, b.idk_idents");

        $sqlMax = $this->db->get_compiled_select();

        $this->db->select("b.idk_idents, MAX(d.tny_level) tny_level, IFNULL(jwb_status,0) jwb_status, COUNT(d.tny_idents) cnt_tny_idents, COUNT(e.jwb_idents) cnt_jwb_idents");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator","INNER");
        $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","INNER");
        $this->db->join("(" . $sqlMax . ") f", "a.aso_idents = f.aso_idents AND c.idk_idents = f.id_process_area AND b.idk_idents = f.id_kelompok AND d.tny_level = f.tny_level","INNER");
        $this->db->where("a.aso_idents", $aso_idents);
        $this->db->where("c.idk_idents", $idk_idents);
        $this->db->where("d.tny_level <> 0");
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        // $this->db->where_in("e.jwb_status", array(2, 21,22));
        $this->db->group_by("b.idk_idents, IFNULL(jwb_status,0)");
        $result = $this->db->get();
        // $this->common->debug_sql(1);
        return $result;
    }    
    function getKategoriGeneral_detail($aso_idents, $idk_idents, $aso_operator){
        $this->db->select("c.idk_idents, d.tny_idents, c.idk_nama, b.idk_nama idk_nama_kategori, d.tny_pertanyaan, d.tny_petunjuk");
        $this->db->select("e.jwb_idents, e.jwb_asoidents, e.jwb_tnyidents, e.jwb_jawab, e.jwb_deskripsi, e.jwb_link, e.jwb_file, e.jwb_status, jwb_usrnam");
        $this->db->select("null tny_level_desc, 'General' tny_kriteria_desc, tny_level");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents AND aso_operator = '".$aso_operator."'", "INNER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator","INNER");
        $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","LEFT OUTER");
        $this->db->where("aso_idents", $aso_idents);
        $this->db->where("c.idk_idents", $idk_idents);
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        $this->db->where("d.tny_level = 0");
        $this->db->order_by("c.idk_idents asc, d.tny_level, d.tny_kriteria, d.tny_urutan, d.tny_idents asc");
        $result = $this->db->get();
        // $this->common->debug_sql(1);
        return $result;
    }
    function getKategoriKuesioner_detail($aso_idents, $idk_idents, $aso_operator, $tny_level=999){
        $this->db->select("c.idk_idents, d.tny_idents, c.idk_nama, b.idk_nama idk_nama_kategori, d.tny_pertanyaan, d.tny_petunjuk");
        $this->db->select("e.jwb_idents, e.jwb_asoidents, e.jwb_tnyidents, e.jwb_jawab, e.jwb_deskripsi, e.jwb_link, e.jwb_file, e.jwb_status, jwb_usrnam");
        $this->db->select("g.lvl_nama tny_level_desc, f.lvl_nama tny_kriteria_desc, tny_level");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents AND aso_operator = '".$aso_operator."'", "INNER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator","INNER");
        $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","LEFT OUTER");
        $this->db->join("t_mas_level f", "d.tny_kriteria = f.lvl_idents","INNER");
        $this->db->join("t_mas_level g", "f.lvl_parent = g.lvl_idents","INNER");
        $this->db->where("aso_idents", $aso_idents);
        $this->db->where("c.idk_idents", $idk_idents);
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        if($tny_level!=999){
            $this->db->where("d.tny_level", $tny_level);
        }
        $this->db->order_by("c.idk_idents asc, d.tny_level, d.tny_kriteria, d.tny_urutan, d.tny_idents asc");
        $result = $this->db->get();
        return $result;
    }
    function getKategori_detail($aso_idents, $idk_idents, $aso_operator, $tny_level=999){
        $this->db->select("c.idk_idents, d.tny_idents, c.idk_nama, b.idk_nama idk_nama_kategori, d.tny_pertanyaan, d.tny_petunjuk");
        $this->db->select("e.jwb_idents, e.jwb_asoidents, e.jwb_tnyidents, e.jwb_jawab, e.jwb_deskripsi, e.jwb_link, e.jwb_file, e.jwb_status, jwb_usrnam");
        $this->db->select("g.lvl_nama tny_level_desc, f.lvl_nama tny_kriteria_desc, tny_level");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents AND aso_operator = '".$aso_operator."'", "INNER");
        $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator","INNER");
        $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","LEFT OUTER");
        $this->db->join("t_mas_level f", "d.tny_kriteria = f.lvl_idents","INNER");
        $this->db->join("t_mas_level g", "f.lvl_parent = g.lvl_idents","INNER");
        $this->db->where("aso_idents", $aso_idents);
        $this->db->where("b.idk_idents", $idk_idents);
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        if($tny_level!=999){
            $this->db->where("d.tny_level", $tny_level);
        }
        $this->db->order_by("c.idk_idents asc, d.tny_level, d.tny_kriteria, d.tny_urutan, d.tny_idents asc");
        $result = $this->db->get();
        // $this->common->debug_sql(1);
        return $result;
    }
    function getKelompokKategori($unitkerja, $status=null){
		$this->db->select("b.idk_idents, COUNT(tny_pertanyaan) cnt_tny_pertanyaan, COUNT(e.jwb_idents) cnt_jwb_idents");
		$this->db->from("t_asm_asesmen_operator a");
		$this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
		$this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
		$this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
		$this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "LEFT OUTER");
		$this->db->join("t_asm_asesmen f", "a.aso_asmidents = f.asm_idents", "INNER");
		$this->db->join("t_asm_unitkerja g", "a.aso_asmidents = g.lok_asmidents", "INNER");
        $this->db->where("aso_unitkerja", $unitkerja);
        if($status!=null){
            $this->db->where("IFNULL(aso_status,0) = 1");
        }
		$this->db->group_by("b.idk_idents");

        $sqljawaban = $this->db->get_compiled_select();

        $this->db->select("a.*, ROUND((IFNULL(cnt_jwb_idents,0)/IFNULL(cnt_tny_pertanyaan,1))*100,0) idk_progress"); 
        $this->db->from("t_mas_kategori a");
        $this->db->join("(" . $sqljawaban . ") b", "a.idk_idents = b.idk_idents","LEFT OUTER");
        $this->db->where("idk_parent = 0");
        $result = $this->db->get();
        return $result;
    }
    function getKelompokKategori_detail($idk_idents){
		$this->db->select("c.idk_idents, COUNT(e.jwb_jawab) cnt_jwb_ya");
		$this->db->from("t_asm_asesmen_operator a");
		$this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
		$this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
		$this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
		$this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "LEFT OUTER");
        $this->db->where("e.jwb_jawab = 1");
		$this->db->group_by("c.idk_idents");

        $sqljawaban = $this->db->get_compiled_select();

        $this->db->select("a.*, cnt_jwb_ya"); 
        $this->db->from("t_mas_kategori a");
        $this->db->join("(" . $sqljawaban . ") b", "a.idk_idents = b.idk_idents","LEFT OUTER");
        $this->db->where("idk_parent", $idk_idents);
        $result = $this->db->get();
        return $result;
    }
    function getKelompokKategori_full($asesmen, $kabptn){
        $this->db->select("ab.idk_idents, ab.idk_nama, COUNT(c.jwb_jawab) cnt_jwb_ya");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori aa","a.aso_kelompok_indikator = aa.idk_idents", "INNER");
        $this->db->join("t_mas_kategori ab","aa.idk_idents = ab.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan b", "ab.idk_idents = b.tny_indikator", "INNER");
        $this->db->join("t_asm_asesmen_jawaban c","a.aso_idents = c.jwb_asoidents AND b.tny_idents = c.jwb_tnyidents and c.jwb_jawab = 1", "LEFT OUTER");
        $this->db->where_in("a.aso_kabptn", $kabptn);
        $this->db->where("a.aso_asmidents", $asesmen);
        $this->db->group_by("ab.idk_idents, ab.idk_nama");

        $sql = $this->db->get_compiled_select();

        $this->db->select("a.idk_idents id_kelompok, a.idk_nama nama_kelompok, b.idk_idents id_Kategori, b.idk_nama nama_Kategori, IFNULL(cnt_jwb_ya,0) cnt_jwb_ya");
        $this->db->from("t_mas_kategori a");
        $this->db->join("t_mas_kategori b","a.idk_idents = b.idk_parent","INNER");
        $this->db->join("(".$sql.") c","b.idk_idents = c.idk_idents","LEFT OUTER");
        $this->db->group_by("a.idk_idents, a.idk_nama, b.idk_idents, b.idk_nama");

        $hasil = $this->crud->returnforjson(array('order_by'=>'asm_usrdat desc'));
        return $hasil;        
    }
    // function getKategori_detail($idk_idents){
    //     $this->db->from("t_mas_kategori a");
    //     $this->db->join("t_mas_pertanyaan b", "a.idk_idents = b.tny_indikator", "INNER");
    //     // $this->db->join("t_mas_asesmen_jawaban e", "b.aso_idents = e.jwb_asoidents and ","LEFT OUTER");
    //     $this->db->where("idk_parent", $idk_idents);
    //     // $this->db->where("tny_indikator = 20");
    //     $this->db->order_by("idk_idents asc, tny_idents asc");
    //     // $this->common->debug_sql(1,1);
    //     $result = $this->db->get();
    //     return $result;        
    // }
    function getTaskIncomplete(){
        switch ($this->usrlevel){
            case 5:
                $this->db->select("'1' Keterangan, b.idk_nama, COUNT(*) total_pertanyaan");
                $this->db->from("t_asm_asesmen_operator a");
                $this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
                $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
                $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
                $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "LEFT OUTER");
                $this->db->where("a.aso_operator", $this->usridents);
                $this->db->where("e.jwb_asoidents is null");
                $this->db->group_by("b.idk_nama");
        
                $sql1 = $this->db->get_compiled_select();
        
                $this->db->select("'2' Keterangan, b.idk_nama, COUNT(*) total_pertanyaan");
                $this->db->from("t_asm_asesmen_operator a");
                $this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
                $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
                $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
                $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "INNER");
                $this->db->where("a.aso_operator", $this->usridents);
                $this->db->where("e.jwb_status = 2");
                $this->db->group_by("b.idk_nama");
                
                $sql2 = $this->db->get_compiled_select();
        
                $this->db->select("'3' Keterangan, b.idk_nama, COUNT(*) total_pertanyaan");
                $this->db->from("t_asm_asesmen_operator a");
                $this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
                $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
                $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
                $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "INNER");
                $this->db->where("a.aso_operator", $this->usridents);
                $this->db->where("e.jwb_status = 1");
                $this->db->group_by("b.idk_nama");
                
                $sql3 = $this->db->get_compiled_select();
        
                $this->db->from("(". $sql1 . " UNION ALL " . $sql2 . " UNION ALL " . $sql3 . ") a", false);                
                break;
            case 4:
                $this->db->select("'1' Keterangan, b.idk_nama, f.USR_FNAMES, COUNT(*) total_pertanyaan");
                $this->db->from("t_asm_asesmen_operator a");
                $this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
                $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
                $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
                $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "LEFT OUTER");
                $this->db->join("t_mas_usrapp f", "a.aso_operator = f.USR_IDENTS","LEFT OUTER");
                $this->db->where("f.USR_USRNAM", $this->username);
                $this->db->where("e.jwb_asoidents is null");
                $this->db->group_by("b.idk_nama, f.USR_FNAMES");
        
                $sql1 = $this->db->get_compiled_select();
        
                $this->db->select("'2' Keterangan, b.idk_nama, f.USR_FNAMES, COUNT(*) total_pertanyaan");
                $this->db->from("t_asm_asesmen_operator a");
                $this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
                $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
                $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
                $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "INNER");
                $this->db->join("t_mas_usrapp f", "a.aso_operator = f.USR_IDENTS","LEFT OUTER");
                $this->db->where("f.USR_USRNAM", $this->username);
                $this->db->where("e.jwb_status = 2");
                $this->db->group_by("b.idk_nama, f.USR_FNAMES");
                
                $sql2 = $this->db->get_compiled_select();
        
                $this->db->select("'4' Keterangan, b.idk_nama, f.USR_FNAMES, COUNT(*) total_pertanyaan");
                $this->db->from("t_asm_asesmen_operator a");
                $this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
                $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
                $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
                $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "INNER");
                $this->db->join("t_mas_usrapp f", "a.aso_operator = f.USR_IDENTS","LEFT OUTER");
                $this->db->where("f.USR_USRNAM", $this->username);
                $this->db->where("e.jwb_status = 0");
                $this->db->group_by("b.idk_nama, f.USR_FNAMES");
                
                $sql3 = $this->db->get_compiled_select();
        
                $this->db->from("(". $sql1 . " UNION ALL " . $sql2 . " UNION ALL " . $sql3 . ") a", false);                
                break;
            default:
                $this->db->select("'4' Keterangan, b.idk_nama, f.USR_FNAMES, COUNT(*) total_pertanyaan");
                $this->db->from("t_asm_asesmen_operator a");
                $this->db->join("t_mas_kategori b", "a.aso_kelompok_indikator = b.idk_idents", "INNER");
                $this->db->join("t_mas_kategori c", "a.aso_kelompok_indikator = c.idk_parent", "INNER");
                $this->db->join("t_mas_pertanyaan d", "c.idk_idents = d.tny_indikator", "INNER");
                $this->db->join("t_asm_asesmen_jawaban e", "a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents", "INNER");
                $this->db->join("t_mas_usrapp f", "a.aso_operator = f.USR_IDENTS","LEFT OUTER");
                $this->db->where("f.USR_USRNAM", $this->username);
                $this->db->where("e.jwb_status = 0");
                $this->db->group_by("b.idk_nama, f.USR_FNAMES");            
                break;
        }
        $result = $this->db->get();
        return $result;
    }
    function getUnitKerjaAsesmen_json($asm_idents){
        $this->db->from("t_asm_unitkerja a");
        $this->db->join("t_asm_asesmen b", "a.lok_asmidents = b.asm_idents", "INNER");
        $this->db->where("IFNULL(lok_is_deleted,0) <> 1");
        $return = $this->db->get();
        foreach($return->result() as $keyI=>$valueI){
            $notinUnitKerja[] = $valueI->lok_unitkerja;
        }
        $this->db->select("unt_idents id, unt_unitkerja text");
        $this->db->from("t_mas_unitkerja a");
        if(isset($notinUnitKerja)){
            $this->db->where_not_in("unt_idents", $notinUnitKerja);
        }
        $this->db->where("IFNULL(unt_is_deleted,0) <> 1");
        $result = $this->db->get();
        $data = $result->result();
        return $data;
    }
    function getKategoriAsesmen_json($asm_tahun, $aso_idents){
        $this->db->from("t_asm_asesmen");
        $this->db->where("asm_idents", $asm_tahun);
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");

        $return = $this->db->get();
        $rowreturn = $return->row();

        $year = $rowreturn->asm_tahun;
        // $this->common->debug_array($year);
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_asm_unitkerja b", "a.aso_asmidents = b.lok_asmidents and a.aso_unitkerja = b.lok_unitkerja", "INNER");
        $this->db->join("t_asm_asesmen c", "a.aso_asmidents = c.asm_idents", "INNER");
        $this->db->where("aso_unitkerja", $this->usrunitkerja);
        $this->db->where("asm_tahun", $year);
        $this->db->where("IFNULL(a.aso_is_deleted,0) <> 1");
        $this->db->where("IFNULL(b.lok_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.asm_is_deleted,0) <> 1");
        if($aso_idents!=null){
            $this->db->where("aso_idents <> ". $aso_idents);
        }
        $result = $this->db->get();
        foreach($result->result() as $keyI=>$valueI){
            $notinKategori[] = $valueI->aso_kelompok_indikator;
        }

        $this->db->select("idk_parent");
        $this->db->from("t_asm_asesmen_process_area a");
        $this->db->join("t_mas_kategori b", "a.par_process_area = b.idk_idents", "INNER");
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");
        $this->db->where("par_asmidents", $asm_tahun);
        $result = $this->db->get();

        foreach($result->result() as $keyE=>$valueE){
            $asmKategori[] = $valueE->idk_parent;
        }        
        $this->db->select("idk_idents id, idk_nama text");
        $this->db->from("t_mas_kategori a");
        $this->db->where("idk_parent = 0");
        $this->db->where_in("idk_idents", $asmKategori);
        if(isset($notinKategori)){
            $this->db->where_not_in("idk_idents", $notinKategori);
        }
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");
        $result = $this->db->get();
        $data = $result->result();
        return $data;
    }
    function getHistory($jwb_idents){
        $this->db->from("t_asm_asesmen_history_approval");
        $this->db->where("his_jwbidents", $jwb_idents);

        $return = $this->db->get();
        return $return;
    }
    function getAssignment_list(){
        $this->db->select("ase_idents, ase_asesor, asm_tahun, asm_periode");
        $this->db->select("asm_periode_start, asm_periode_end, USR_FNAMES, ase_usrnam, ase_usrdat, d.idk_nama");
        $this->db->from("t_asm_asesor a");
        $this->db->join("t_mas_usrapp b", "a.ase_asesor = b.USR_IDENTS", "INNER");
        $this->db->join("t_asm_asesmen c", "a.ase_asmidents = c.asm_idents", "INNER");
        $this->db->join("t_mas_kategori d","a.ase_kategori = d.idk_idents", "INNER");
        $this->db->where("IFNULL(ase_is_deleted,0) <> 1");
        $hasil = $this->crud->returnforjson(array('order_by'=>'ase_usrdat desc'));
        return $hasil;
    }
    function getAssignment_edit($ase_idents){
        $this->db->from("t_asm_asesor a");
        $this->db->join("t_mas_usrapp b", "a.ase_asesor = b.USR_IDENTS","INNER");
        $this->db->join("t_asm_asesmen c", "a.ase_asmidents = c.asm_idents", "INNER");
        $this->db->where("ase_idents", $ase_idents);
        $result = $this->db->get();
        if($result->num_rows()>0){
            $result = $result->row();
        }else{
            $result = null;
        }
        return $result;
    }

    function getPenilaian_list($usr_idents=null, $usr_level=null){
        $usr_level=($usr_level=="" ? $this->usrlevel : $usr_level);
        $usr_idents=($usr_idents=="" ? $this->usridents : $usr_idents);

        $rslAsesor = $this->crud->chkAsesor();
        $asesor = false;
        if($rslAsesor->num_rows()>0){
            $asesor = true;
            foreach($rslAsesor->result() as $keyA=>$valueA){
                $kategori_asesor[] = $valueA->ase_kategori;
            }
        }
        $this->db->select("b.idk_idents idk_parent, c.idk_idents idk_process_area, c.idk_nama, a.aso_operator, d.tny_level");
        $this->db->from("t_asm_asesmen_operator a");
        $this->db->join("t_mas_kategori b","a.aso_kelompok_indikator = b.idk_idents", "INNER");
        $this->db->join("t_mas_kategori c","a.aso_kelompok_indikator = c.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan d","c.idk_idents = d.tny_indikator and tny_level <> 0", "INNER");
        $this->db->join("t_asm_asesmen_process_area x","a.aso_asmidents = x.par_asmidents and c.idk_idents = x.par_process_area", "INNER");
        $this->db->join("t_asm_asesmen_jawaban e","a.aso_idents = e.jwb_asoidents AND d.tny_idents = e.jwb_tnyidents","INNER");
        $this->db->where("IFNULL(d.tny_is_deleted,0) <> 1");
        $this->db->where_in("IFNULL(e.jwb_status,0)", array(1,21,22));
        $this->db->group_by("b.idk_idents, c.idk_idents, c.idk_nama, a.aso_operator, d.tny_level");
        $JOIN = "INNER";
        $sqljawaban = $this->db->get_compiled_select();

        $this->db->select("CONCAT('rowid', aso_idents) DT_RowId, aso_idents, nil_idents, tny_level, asm_tahun, aso_kelompok_indikator, d.idk_nama aso_kelompok_indikator_desc, e.idk_nama idk_process_area_desc, idk_process_area");
        $this->db->select("asm_periode_start, asm_periode_end, b.aso_operator, c.USR_FNAMES aso_operator_name, aso_usrnam, aso_usrdat, lvl_nama, unt_unitkerja");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_operator b", "a.asm_idents = b.aso_asmidents","INNER");
        $this->db->join("t_mas_usrapp c", "b.aso_operator = c.USR_IDENTS","LEFT OUTER");
        $this->db->join("t_mas_kategori d", "b.aso_kelompok_indikator = d.idk_idents","LEFT OUTER");
        $this->db->join("(" . $sqljawaban . ") e", "b.aso_kelompok_indikator = e.idk_parent AND b.aso_operator = e.aso_operator","INNER");
        $this->db->join("t_asm_asesmen_penilaian f", "b.aso_idents = f.nil_asoidents and e.idk_process_area = f.nil_process_area and e.tny_level = f.nil_tnylevel","LEFT OUTER");
        $this->db->join("t_mas_level g", "e.tny_level = g.lvl_idents","INNER");
        $this->db->join("t_mas_unitkerja h", "c.USR_UNITKERJA = h.unt_idents", "INNER");
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $this->db->where("IFNULL(aso_is_deleted,0) <> 1");
        if($usr_idents!=null){
            if($asesor){
                if(isset($kategori_asesor)){
                    $this->db->where_in("b.aso_kelompok_indikator", $kategori_asesor);
                }else{
                    $this->db->where("b.aso_kelompok_indikator = 99999999");
                }
            }else{
                if($usr_level!=1 && $usr_level!=2 ){//operator
                    $this->db->where("b.aso_kelompok_indikator = 99999999");
                }
            }
        }
        $hasil = $this->crud->returnforjson(array('order_by'=>'aso_usrdat desc'));
        return $hasil;
    }    
    function getPenilaian_edit($nil_idents){
        $this->db->from("t_asm_asesmen_penilaian a");
        $this->db->where("nil_idents", $nil_idents);
        $result = $this->db->get();
        return $result;
    }

    function getPenilaianJawaban_list($aso_idents, $idk_process_area, $tny_level){
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_operator b", "a.asm_idents = b.aso_asmidents","INNER");
        $this->db->join("t_mas_kategori c","b.aso_kelompok_indikator = c.idk_idents", "INNER");
        $this->db->join("t_mas_kategori d","b.aso_kelompok_indikator = d.idk_parent", "INNER");
        $this->db->join("t_mas_pertanyaan e","d.idk_idents = e.tny_indikator and e.tny_level <> 0", "INNER");
        $this->db->join("t_asm_asesmen_process_area f","a.asm_idents = f.par_asmidents and d.idk_idents = f.par_process_area", "INNER");
        $this->db->join("t_asm_asesmen_jawaban g","b.aso_idents = g.jwb_asoidents AND e.tny_idents = g.jwb_tnyidents","INNER");
        $this->db->where("b.aso_idents", $aso_idents);
        $this->db->where("d.idk_idents", $idk_process_area);
        $this->db->where("e.tny_level", $tny_level);

        $result = $this->db->get();
        return $result;
    }
    function getAsesmenfile_list($aso_idents, $tny_level, $aso_process_area){
        $this->db->from("t_asm_asesmen_files a");
        $this->db->where("fil_asoidents", $aso_idents);
        $this->db->where("fil_tnylevel", $tny_level);
        $this->db->where("fil_process_area", $aso_process_area);
        $result = $this->db->get();
        return $result;
        // $hasil = $this->crud->returnforjson(array('order_by'=>'fil_idents desc'));
        // return $hasil;

    }
    function getAsesmenSummary($type){
        switch ($type) {
            case 2:
                $join = "INNER";
                $this->db->where("jwb_status = 0");
                $this->db->where("tny_level <> 0");
                $select = ", operator_name, penyetuju_name";
                break;
            default:
                $join = "LEFT OUTER";
                $select = null;
                break;
        }
        $this->db->select("e.idk_idents idk_kategori, d.idk_idents idk_process_area, tny_level, h.USR_UNITKERJA, h.USR_LOGINS operator_name, i.USR_LOGINS penyetuju_name");
        // ");
        $this->db->select("COUNT(f.tny_idents) count_pertanyaan,");
        $this->db->select("COUNT(g.jwb_idents) count_jawaban,");
        $this->db->select("COUNT(CASE WHEN jwb_status = 21 then jwb_status WHEN jwb_status = 1 then jwb_status END) count_approved_atasan,");
        $this->db->select("COUNT(CASE WHEN jwb_status = 22 then jwb_status WHEN jwb_status = 2 then jwb_status END) count_not_approved_atasan,");
        $this->db->select("COUNT(CASE WHEN jwb_status = 21 then jwb_status END) count_approved_asesor,");
        $this->db->select("COUNT(CASE WHEN jwb_status = 22 then jwb_status END) count_not_approved_asesor");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_operator b", "a.asm_idents = b.aso_asmidents", "INNER");
        $this->db->join("t_asm_asesmen_process_area c", "a.asm_idents = c.par_asmidents", "INNER");
        $this->db->join("t_mas_kategori d", "c.par_process_area = d.idk_idents", "INNER");
        $this->db->join("t_mas_kategori e", "d.idk_parent= e.idk_idents", "INNER");
        $this->db->join("t_mas_pertanyaan f", "d.idk_idents = f.tny_indikator", "INNER");
        $this->db->join("t_asm_asesmen_jawaban g", "f.tny_idents = g.jwb_tnyidents AND b.aso_idents = g.jwb_asoidents", $join);
        $this->db->join("t_mas_usrapp h", "b.aso_operator = h.USR_IDENTS", "INNER");
        $this->db->join("t_mas_usrapp i", "h.USR_USRNAM = i.USR_LOGINS", "INNER");
        $this->db->where("IFNULL(a.asm_is_deleted,0) <> 1");
        $this->db->where("IFNULL(b.aso_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(e.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(f.tny_is_deleted,0) <> 1");
        $this->db->group_by("e.idk_idents, e.idk_nama, d.idk_idents, d.idk_nama, tny_level, h.USR_UNITKERJA,h.USR_LOGINS, i.USR_LOGINS ");
        //  ");
        
        $sql = $this->db->get_compiled_select();

        // $this->common->debug_array($sql);
        $this->db->distinct();
        $this->db->select("a.asm_tahun, a.asm_periode, unt_idents, c.unt_unitkerja, h.idk_idents idk_kategori, h.idk_nama idk_kategori_desc");
        $this->db->select("g.idk_idents idk_process_area, g.idk_nama idk_process_area_desc, i.tny_level, lvl_nama" . $select);
        $this->db->select("count_pertanyaan,");
        $this->db->select("count_jawaban,");
        $this->db->select("count_approved_atasan,");
        $this->db->select("count_not_approved_atasan,");
        $this->db->select("count_approved_asesor,");
        $this->db->select("count_not_approved_asesor");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_unitkerja b", "a.asm_idents = b.lok_asmidents", "INNER");
        $this->db->join("t_mas_unitkerja c", "b.lok_unitkerja = c.unt_idents", "INNER");
        $this->db->join("t_asm_asesmen_operator d", "a.asm_idents = d.aso_asmidents", "INNER");
        $this->db->join("t_mas_usrapp e", "d.aso_operator = e.USR_IDENTS", "INNER");
        $this->db->join("t_asm_asesmen_process_area f", "a.asm_idents = f.par_asmidents", "INNER");
        $this->db->join("t_mas_kategori g", "f.par_process_area = g.idk_idents", "INNER");
        $this->db->join("t_mas_kategori h", "g.idk_parent = h.idk_idents", "INNER");
        $this->db->join("(" . $sql . ") as i", "g.idk_idents = i.idk_process_area AND b.lok_unitkerja = i.USR_UNITKERJA", $join);
        $this->db->join("t_mas_level j", "i.tny_level = j.lvl_idents", "LEFT OUTER");
        $this->db->where("IFNULL(a.asm_is_deleted,0) <> 1");
        $this->db->where("IFNULL(b.lok_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.unt_is_deleted,0) <> 1");
        $this->db->where("IFNULL(j.lvl_is_deleted,0) <> 1");
        $this->db->where("IFNULL(g.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(h.idk_is_deleted,0) <> 1");
        $this->db->order_by("c.unt_idents, h.idk_idents, g.idk_idents, i.tny_level");
        $result = $this->db->get();
        // $this->common->debug_sql(1);
        return $result;
    }
    function getAsesmen_unitkerja(){
        $this->db->distinct();
        $this->db->select("unt_idents, unt_unitkerja, asm_periode_end, asm_idents");
        $this->db->from("t_mas_unitkerja a");
        $this->db->join("t_asm_unitkerja b", "a.unt_idents = b.lok_unitkerja", "INNER");
        $this->db->join("t_asm_asesmen c", "b.lok_asmidents = c.asm_idents", "INNER");
        $this->db->join("t_asm_asesmen_operator d", "c.asm_idents = d.aso_asmidents", "INNER");
        $this->db->join("t_mas_usrapp e", "d.aso_operator = e.USR_IDENTS and a.unt_idents = e.USR_UNITKERJA", "INNER");
        $this->db->where("IFNULL(unt_is_deleted,0) <> 1");
        $result = $this->db->get();
        return $result;
    }    
    function getKategoritree($asm_idents, $unit_kerja){
        // $asm_idents=12;
        $this->db->distinct();
        $this->db->select('d.idk_idents as id, d.idk_nama as text, d.idk_parent parentid');
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_process_area b", "a.asm_idents = b.par_asmidents", "INNER");
        $this->db->join("t_mas_kategori c", "b.par_process_area = c.idk_idents", "INNER");
        $this->db->join("t_mas_kategori d", "c.idk_parent = d.idk_idents", "INNER");
        $this->db->where("IFNULL(c.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.idk_is_deleted,0) <> 1");
        $this->db->where("asm_idents", $asm_idents);
        $this->db->where("aso_unitkerja", $unit_kerja);
        
        // $this->common->debug_sql(1,1);
        $sqlParent = $this->db->get_compiled_select();
        
        $this->db->select("CONCAT('PROCESS', c.idk_idents) as id, c.idk_nama as text, c.idk_parent parentid");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_process_area b", "a.asm_idents = b.par_asmidents", "INNER");
        $this->db->join("t_mas_kategori c", "b.par_process_area = c.idk_idents", "INNER");
        $this->db->join("t_mas_kategori d", "c.idk_parent = d.idk_idents", "INNER");
        $this->db->where("IFNULL(c.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.idk_is_deleted,0) <> 1");
        $this->db->where("asm_idents", $asm_idents);

        $sqlChild = $this->db->get_compiled_select();
        
        $this->db->distinct();
        $this->db->select("f.lvl_idents as id, f.lvl_nama as text, 0 parentid");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_process_area b", "a.asm_idents = b.par_asmidents", "INNER");
        $this->db->join("t_mas_kategori c", "b.par_process_area = c.idk_idents", "INNER");
        $this->db->join("t_mas_kategori d", "c.idk_parent = d.idk_idents", "INNER");
        $this->db->join("t_mas_pertanyaan e", "b.par_process_area = e.tny_indikator", "INNER");
        $this->db->join("t_mas_level f", "e.tny_level = f.lvl_idents", "INNER");
        $this->db->join("t_asm_asesmen_jawaban g", "e.tny_idents = g.jwb_tnyidents", "INNER");
        $this->db->join("t_asm_asesmen_operator x", "a.asm_idents = x.aso_asmidents", "INNER");
        $this->db->where("jwb_status = 21");
        $this->db->where("IFNULL(e.tny_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.idk_is_deleted,0) <> 1");
        $this->db->where("asm_idents", $asm_idents);
        $this->db->where("aso_unitkerja", $unit_kerja);
        
        $sqlParent = $this->db->get_compiled_select();

        $this->db->distinct();
        $this->db->select("CONCAT('Kategori-', f.lvl_idents) as id, 'Kategori' as text, f.lvl_idents parentid");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_process_area b", "a.asm_idents = b.par_asmidents", "INNER");
        $this->db->join("t_mas_kategori c", "b.par_process_area = c.idk_idents", "INNER");
        $this->db->join("t_mas_kategori d", "c.idk_parent = d.idk_idents", "INNER");
        $this->db->join("t_mas_pertanyaan e", "b.par_process_area = e.tny_indikator", "INNER");
        $this->db->join("t_mas_level f", "e.tny_level = f.lvl_idents", "INNER");
        $this->db->join("t_asm_asesmen_jawaban g", "e.tny_idents = g.jwb_tnyidents", "INNER");
        $this->db->join("t_asm_asesmen_operator x", "a.asm_idents = x.aso_asmidents", "INNER");
        $this->db->where("jwb_status = 21");
        $this->db->where("IFNULL(e.tny_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.idk_is_deleted,0) <> 1");
        $this->db->where("asm_idents", $asm_idents);
        $this->db->where("aso_unitkerja", $unit_kerja);
        

        $sqlChild = $this->db->get_compiled_select();

        $this->db->distinct();
        $this->db->select("CONCAT('ProcessArea-', f.lvl_idents) as id, 'Process Area' as text, f.lvl_idents parentid");
        $this->db->from("t_asm_asesmen a");
        $this->db->join("t_asm_asesmen_process_area b", "a.asm_idents = b.par_asmidents", "INNER");
        $this->db->join("t_mas_kategori c", "b.par_process_area = c.idk_idents", "INNER");
        $this->db->join("t_mas_kategori d", "c.idk_parent = d.idk_idents", "INNER");
        $this->db->join("t_mas_pertanyaan e", "b.par_process_area = e.tny_indikator", "INNER");
        $this->db->join("t_mas_level f", "e.tny_level = f.lvl_idents", "INNER");
        $this->db->join("t_asm_asesmen_jawaban g", "e.tny_idents = g.jwb_tnyidents", "INNER");
        $this->db->join("t_asm_asesmen_operator x", "a.asm_idents = x.aso_asmidents", "INNER");
        $this->db->where("jwb_status = 21");
        $this->db->where("IFNULL(e.tny_is_deleted,0) <> 1");
        $this->db->where("IFNULL(c.idk_is_deleted,0) <> 1");
        $this->db->where("IFNULL(d.idk_is_deleted,0) <> 1");
        $this->db->where("asm_idents", $asm_idents);
        $this->db->where("aso_unitkerja", $unit_kerja);
        

        $sqlChild2 = $this->db->get_compiled_select();

        // $this->common->debug_array($sqlChild);
        $this->db->from("(" . $sqlParent . " UNION ALL " . $sqlChild . " UNION ALL " . $sqlChild2 . ") as u");
        // $this->common->debug_sql(1,1);
        $result = $this->db->get();
        $hasil['Hasil'] = $result->result();
        return $hasil;
    }
    function getGrafik($jenis, $asm_idents, $level, $unt_idents){
        $this->db->from("t_mas_kategori a");
        $this->db->join("t_asm_asesmen_operator b", "a.idk_idents = b.aso_kelompok_indikator AND aso_asmidents = " . $asm_idents . " and aso_unitkerja = " . $unt_idents, "LEFT OUTER");
        
        if($jenis=="Kategori"){
            $this->db->select("a.idk_idents, a.idk_nama, SUM(CASE WHEN e.jwb_jawab = 1 THEN 1 ELSE 0 END) idk_jawab");
            $this->db->join("t_mas_pertanyaan c", "b.aso_kelompok_indikator = c.tny_kelompok and tny_level = " . $level, "LEFT OUTER");
            $this->db->where("idk_parent = 0");
            $this->db->where("IFNULL(a.idk_is_deleted,0) <> 1");
            $this->db->group_by("a.idk_idents, a.idk_nama");
        }else{
            $this->db->select("x.idk_idents, x.idk_nama, SUM(CASE WHEN e.jwb_jawab = 1 THEN 1 ELSE 0 END) idk_jawab");
            $this->db->join("t_mas_kategori x", "b.aso_kelompok_indikator = x.idk_parent", "LEFT OUTER");
            $this->db->join("t_mas_pertanyaan c", "x.idk_idents = c.tny_indikator and tny_level = " . $level, "LEFT OUTER");
            $this->db->where("x.idk_parent <> 0");
            $this->db->where("IFNULL(a.idk_is_deleted,0) <> 1");
            $this->db->where("IFNULL(x.idk_is_deleted,0) <> 1");
            $this->db->group_by("x.idk_idents, x.idk_nama");
        }
        $this->db->join("t_mas_level d", "c.tny_level = d.lvl_idents", "LEFT OUTER");
        $this->db->join("t_asm_asesmen_jawaban e", "c.tny_idents = e.jwb_tnyidents", "LEFT OUTER");
        $this->db->where("IFNULL(c.tny_is_deleted,0) <> 1");
        $result = $this->db->get();
        return $result;        
    }
}

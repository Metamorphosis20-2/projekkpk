<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_grafik extends CI_Model{
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
	function grfKuesioner($year, $provnc, $kabptn){
		$this->db->select("idk_idents, COUNT(*) total_pertanyaan, COUNT(c.jwb_idents) total_jawaban");
		$this->db->from("t_mas_kategori a");
		$this->db->join("t_mas_pertanyaan b", "a.idk_idents = b.tny_kelompok", "INNER");
		$this->db->join("t_asm_asesmen_jawaban c", "b.tny_idents = c.jwb_tnyidents", "LEFT OUTER");
		$this->db->where("idk_parent = 0");
		$this->db->group_by("idk_idents");

		$sql = $this->db->get_compiled_select();

		$this->db->select("a.idk_nama, ifnull(total_pertanyaan,0) Pertanyaan, ifnull(total_jawaban,0) Jawaban");
		$this->db->from("t_mas_kategori a");
		$this->db->join("(" . $sql . ") b", "a.idk_idents = b.idk_idents", "left outer");
		$this->db->where("idk_parent = 0");
		// $this->common->debug_sql(1,1);
		$hasil = $this->db->get();
		return $hasil;
	}
	function grfKuesionerYesno($year, $provnc, $kabptn){
		$this->db->select("idk_idents, COUNT(c.jwb_idents) jawaban_ya, 0 jawaban_tidak, 0 jawaban_tidakmenjawab");
		$this->db->from("t_mas_kategori a");
		$this->db->join("t_mas_pertanyaan b", "a.idk_idents = b.tny_kelompok", "INNER");
		$this->db->join("t_asm_asesmen_jawaban c", "b.tny_idents = c.jwb_tnyidents", "LEFT OUTER");
		$this->db->where("idk_parent = 0");
		$this->db->where("jwb_jawab = 1");
		$this->db->group_by("idk_idents");

		$sqlYa = $this->db->get_compiled_select();

		$this->db->select("idk_idents, 0 jawaban_ya, COUNT(c.jwb_idents) jawaban_tidak, 0 jawaban_tidakmenjawab");
		$this->db->from("t_mas_kategori a");
		$this->db->join("t_mas_pertanyaan b", "a.idk_idents = b.tny_kelompok", "INNER");
		$this->db->join("t_asm_asesmen_jawaban c", "b.tny_idents = c.jwb_tnyidents", "LEFT OUTER");
		$this->db->where("idk_parent = 0");
		$this->db->where("jwb_jawab = 2");
		$this->db->group_by("idk_idents");

		$sqlTidak = $this->db->get_compiled_select();

		$this->db->select("idk_idents, 0 jawaban_ya, 0 jawaban_tidak, COUNT(c.jwb_idents) jawaban_tidakmenjawab");
		$this->db->from("t_mas_kategori a");
		$this->db->join("t_mas_pertanyaan b", "a.idk_idents = b.tny_kelompok", "INNER");
		$this->db->join("t_asm_asesmen_jawaban c", "b.tny_idents = c.jwb_tnyidents", "LEFT OUTER");
		$this->db->where("idk_parent = 0");
		$this->db->where_not_in("jwb_jawab", array(1,2));
		$this->db->group_by("idk_idents");

		$sqlTidakJawab = $this->db->get_compiled_select();

		$this->db->select("idk_idents, SUM(jawaban_ya) jawaban_ya, SUM(jawaban_tidak) jawaban_tidak, SUM(jawaban_tidakmenjawab) jawaban_tidakmenjawab");
		$this->db->from("(" . $sqlYa . " UNION ALL " . $sqlTidak . " UNION ALL " . $sqlTidakJawab . ") a", false);
		$this->db->group_by("idk_idents");

		$sqlKueri = $this->db->get_compiled_select();

		$this->db->select("a.idk_nama, ifnull(jawaban_ya,0) jawaban_ya, ifnull(jawaban_tidak,0) jawaban_tidak, ifnull(jawaban_tidakmenjawab,0) jawaban_tidakmenjawab");
		$this->db->from("t_mas_kategori a");
		$this->db->join("(" . $sqlKueri . ") b", "a.idk_idents = b.idk_idents", "left outer");
		$this->db->where("idk_parent = 0");
		
		$hasil = $this->db->get();
		return $hasil;

	}
    function getUseraccess_list($USER=null){
	    $this->db->select($this->common->formatdatedb(1, "USL_USRDAT", "Mon") . " USL_USRDAT", false);
	    $this->db->select('COUNT(*) USL_TOTAL, MONTH(USL_USRDAT) BULAN');
	    $this->db->from('t_USRLOG');
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
}
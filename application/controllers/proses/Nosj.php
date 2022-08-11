<?
class Nosj extends MY_Controller{
  public function __construct() {
    parent::__construct();
    $this->load->library('gjson');
    $this->load->model('m_asesmen');
  }
  function getKategori_json(){
      $idk_idents = $this->input->post("idk_idents");
      $jdata = $this->m_master->getKategori_json($idk_idents);
      echo json_encode($jdata);
      // $this->gjson->returnjson($jdata);
  }
  function getKuesionerSpv_list($kat_idents=null, $kat_tahun=null, $level=0){
    $jdata = $this->m_asesmen->getKuesionerSpv_list($kat_idents, $kat_tahun, $level)->result();
    $this->gjson->returnjsontreegrid($jdata);
  }
  function getAsesmenpenugasan_list($usr_idents, $usr_level){
    $jdata = $this->m_asesmen->getAsesmenpenugasan_list($usr_idents, $usr_level);
    $this->gjson->returnjson($jdata);
  }
  function getKategorifull_list($grf_asesmen, $grf_kabptn){
    $jdata = $this->m_asesmen->getKelompokKategori_full($grf_asesmen, $grf_kabptn);
    $this->gjson->returnjson($jdata);
  }
  function getAsesmenfile_list($aso_idents, $tny_level, $aso_process_area){
    $jdata = $this->m_asesmen->getAsesmenfile_list($aso_idents, $tny_level, $aso_process_area);
    $this->gjson->returnjson($jdata);
  }
}

// 1149239488
// 7069351936
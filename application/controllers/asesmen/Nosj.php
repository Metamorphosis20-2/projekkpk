<?
class Nosj extends MY_Controller{
  public function __construct() {
    parent::__construct();
    $this->load->library('gjson');
    $this->load->model('m_asesmen');
  }
  function getUnitKerjaAsesmen_json(){
    $asm_idents = $this->input->post("lok_asmidents");
    $jdata = $this->m_asesmen->getUnitKerjaAsesmen_json($asm_idents);
    echo json_encode($jdata);
  }
  function getKategoriAsesmen_json(){
    $asm_tahun = $this->input->post("asm_tahun");
    $asm_idents = $this->input->post("asm_idents");
    $jdata = $this->m_asesmen->getKategoriAsesmen_json($asm_tahun, $asm_idents);
    echo json_encode($jdata);
      // $this->gjson->returnjson($jdata);
  }
  function getKategoritree($asm_idents=null, $unit_kerja=null){
    $jdata = $this->m_asesmen->getKategoritree($asm_idents, $unit_kerja);
    $return = $jdata;
    $this->gjson->returnjsontree($return);
  }  
}

// 1149239488
// 7069351936
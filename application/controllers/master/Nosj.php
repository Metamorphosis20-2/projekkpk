<?
class Nosj extends MY_Controller{
  public function __construct() {
    parent::__construct();
    $this->load->library('gjson');
    $this->load->model('m_master');
  }
  function getUsers_list($status){
    $jdata = $this->m_master->getUsers_list($status);
    $this->gjson->returnjson($jdata);
  }
  function getActivedirectory_ddg(){
    $this->load->library('authloginad');
    $jdata = $this->authloginad->getMember_ldap('json');
    return $jdata;
  }
  function getMenupemakai_list(){
    $jdata = $this->m_master->getMenupemakai_list();
    $this->gjson->returnjson($jdata);
  }
  function getMenu_tree($NEW=1, $SEC=1, $USER=null, $APPLIC=null, $CHANGE=false) {
    // $this->common->debug_array($APPLIC, false);
    $jdata = $this->crud->getMenu_json($NEW,$SEC, $USER, $APPLIC, $CHANGE)->result();
    $this->gjson->returnjsontreegrid($jdata);
  }
  function getGeneraljson_cmb($TABLE, $PARENT=null){
    $jdata = $this->m_master->getGeneraljson_cmb($TABLE, $PARENT);
    $this->gjson->returnjson($jdata);
  }
  function getProvinsi_list(){
    $jdata = $this->m_master->getProvinsi_list();
    $this->gjson->returnjson($jdata);
  }

  function getProvinsi_cmb($prov=null){
      $jdata = $this->m_master->getProvinsi_cmb($prov);
      $this->gjson->returnjson($jdata);
  }
  function getKabupaten_list(){
    $jdata = $this->m_master->getKabupaten_list();
    $this->gjson->returnjson($jdata);
  }
  function getKabupaten_json(){
      $prov = $this->input->post("prv_idents");
      $jdata = $this->m_master->getKabupaten_json($prov);
      echo json_encode($jdata);
      // $this->gjson->returnjson($jdata);
  }
  function getKabupatenlokasi_json(){
    $prov = $this->input->post("prv_idents");
    $asm_idents = $this->input->post("asm_idents");
    $jdata = $this->m_master->getKabupatenlokasi_json($prov, $asm_idents);
    echo json_encode($jdata);

  }
  function getKabupatenasesmen_json($asm_year, $lok_idents){
    $this->load->model("m_asesmen");
    $jdata = $this->m_asesmen->getLokasi_assigned($asm_year, $lok_idents);
    echo json_encode($jdata);
  }
  function getKategori_json(){
      $idk_idents = $this->input->post("idk_idents");
      $jdata = $this->m_master->getKategori_json($idk_idents);
      echo json_encode($jdata);
      // $this->gjson->returnjson($jdata);
  }
  function getCommon_cmb($HEADCD=null,$TYPECD=null, $DESCR2=null){
    $jdata = $this->m_master->getCommon_cmb($HEADCD, $TYPECD, $DESCR2);
    $this->gjson->returnjson($jdata);
  }
  function getReferensi_dropdown($ref=0,$plant=null,$posisi=null){
    $jdata = $this->m_master->getReferensi($ref,$plant,$posisi);
    $this->gjson->returnjson($jdata);
  }
  function getReference_List($jenis){
    $jdata = $this->m_master->getReference_list($jenis);
    $this->gjson->returnjson($jdata);
  }
  function getKategori_list($kat_idents=null, $kat_tahun=null, $level=0){
    $jdata = $this->m_master->getKategori_list($kat_idents, $kat_tahun, $level)->result();
    $this->gjson->returnjsontreegrid($jdata);
  } 
  function getTingkat_list($lvl_idents=null, $lvl_tahun=null, $level=0){
    $jdata = $this->m_master->getTingkat_list($lvl_idents, $lvl_tahun, $level)->result();
    $this->gjson->returnjsontreegrid($jdata);
  } 
  function getKriteria_json(){
    $lvl_idents = $this->input->post("lvl_idents");
    $lvl_indikator = $this->input->post("lvl_indikator");
    $jdata = $this->m_master->getKriteria_json($lvl_idents, $lvl_indikator);
    echo json_encode($jdata);
      // $this->gjson->returnjson($jdata);
  }
  function getPertanyaantree_list(){
    $jdata = $this->m_master->getPertanyaantree_list()->result();
    $this->gjson->returnjsontreegrid($jdata);
  }  
  function getAktivitas_list($from=null) {
    $this->load->model("m_common");
    $jdata = $this->m_common->getAktivitas_list($from);
    $this->gjson->returnjson($jdata);
  }
  function getRiwayatakses_list($USER=null) {
    $this->load->model("m_common");
    $jdata = $this->m_common->getRiwayatakses_list($USER);
    $this->gjson->returnjson($jdata);
  }
  function getKriteriatree_list($kat_tahun=null, $kriteria=true){
    $jdata = $this->m_master->getKriteriatree_list($kat_tahun, $kriteria)->result();
    // $this->common->debug_sql(1);
    $this->gjson->returnjsontreegrid($jdata);
  }
  function getKriteria_tree(){
    $jdata = $this->m_master->getKriteria_tree();
    $return = $jdata;
    $this->gjson->returnjsontree($return);
  }
  function getCommon_json($HEADCD=null,$TYPECD=null, $DESCR2=null){
    $jdata = $this->crud->getCommon_cmb($HEADCD, $TYPECD, $DESCR2);
    // $this->common->debug_sql(1);
    foreach($jdata["Hasil"] as $keyJdata=>$valueJdata){
      $arr[$valueJdata->COM_TYPECD] = $valueJdata->COM_DESCR1;
    }
    $jdata = json_encode($arr);
    echo $jdata;
  }
  function getBackupcron_list(){
    $path = './backups/';
    $this->load->helper(array('file','directory'));
    $map = directory_map($path);
    $loop = 0;
    foreach($map as $key){
        $file = $path . $key;
        $fileinfo = get_file_info($file);
        $size = $fileinfo["size"];
        $Rows[] = array("bck_filename"=>$key, "bck_filesize"=>$size);
        $loop++;
    }
    $hasil['type'] = 'cmb';
    $hasil['Hasil'] = $Rows;
    $this->gjson->returnjson($hasil);
  }  
  function getUnitkerja_list() {
    $jdata = $this->m_master->getUnitkerja_list();
    $this->gjson->returnjson($jdata);
  }
  function getPertanyaantemplate_list(){
    $jdata = $this->m_master->getPertanyaantemplate_list();
    $this->gjson->returnjson($jdata);
  }
}

// 1149239488
// 7069351936
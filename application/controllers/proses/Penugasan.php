<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Penugasan extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
    	$this->load->helper('ginput');
    	$this->load->model(array('m_asesmen','m_master'));
        $this->modul = $this->router->fetch_class();
        $this->notes = $this->lang->line("notes");
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");
        $this->notes = $this->lang->line("notes");
        $this->start = $this->lang->line("start");
        $this->end = $this->lang->line("end");
        $this->valid = $this->lang->line("valid");
        $this->modul = $this->router->fetch_class();
        $this->table = "t_asm_asesmen_operator";
        $this->usrunitkerja = $this->session->userdata("USR_UNITKERJA");
        $this->usrkabptn = $this->session->userdata("USR_KABPTN");
        $this->usrunitkerjadesc = $this->session->userdata("USR_UNITKERJA_DESC");
        $this->kab_namess = $this->session->userdata("KAB_NAMESS");
        $this->usr_level = $this->session->userdata("USR_LEVELS");
    }	
	public function index(){
        $prv_names = ($this->kab_namess=="" ? "" : " (" . $this->kab_namess . ")");
        $prv_names = null;
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Penugasan " . $prv_names),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listPenugasan(),'admin',$bc);  	 
	}
    function listPenugasan(){
        $gridname = "jqxPenugasan";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Penugasan/list/m_asesmen';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        // $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lko_lokidents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_name", "aw"=>120, "label"=>"Petugas","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_kelompok_indikator_desc", "aw"=>220, "label"=>"Kelompok Kategori","adtype"=>"text");        
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal('Pilih Data!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var lok_idents = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'lok_idents');
            var lok_operator_name = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'lok_operator_name');
            var lok_kabptn = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'lok_kabptn');
        ";
        
        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);
        $oADD = strpos("N".$otorisasi,"E");
        $buttonother = null;
        if($oADD>0){
            $buttonother = array(
                "Tambah Penugasan"=>array('Print1','fa-plus','jvAddOperator()','warning','80')
            );
        }
        $content = gGrid(array('url'=>$url, 
            'grid'=>'datatables',
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,      
            // 'buttonother'=> $buttonother,
            'inline_button_other'=>false,
            'jvAdd_text'=>"Tambah Penugasan",
            'add_theme'=>'warning',
            'button'=> 'standar',
            'sumber'=>'server',
            'modul'=>'proses/asesmen'
        ));
        //====== end of grid
        $content .= generateWindowjqx(array('window'=>'Kategori','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        $content .= "
        <script>

        function jvAdd(){
            title = 'Tambah';
            var unit_kerja = '".$this->usrunitkerja."';
            if(unit_kerja!=''){
                var param = {};
                param['type'] = 'add';
                param['title'] = title;
    
                $('#jqwKategori').jqxWindow('open');
    
                $.post('/proses/penugasan/edit', param,function(data){
                    var lebar = $(window).width() * 0.8;
                    $('#jqwKategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'350px',position:'middle', resizable:false,title: title + ' PIC', zIndex:'99999'});
                    $('#jqwKategori').jqxWindow('setContent', data);
                });
            }else{
                swal.fire({ 
                    title: 'Anda tidak mempunyai alokasi Unit Kerja!', 
                    text:'Tidak bisa melakukan penugasan',
                    icon: 'error'
                });
            }
        }
        function jvView(data_row){
            jvEdit(data_row, 'view');
        }
        function jvEdit(data_row, jenis='edit'){
            aso_idents = data_row['aso_idents'];
            title = 'Tambah';
            var unit_kerja = '".$this->usrunitkerja."';
            if(unit_kerja!=''){
                var param = {};
                param['type'] = jenis;
                param['title'] = title;
                param['aso_idents'] = aso_idents;
    
                $('#jqwKategori').jqxWindow('open');
    
                $.post('/proses/penugasan/edit', param,function(data){
                    var lebar = $(window).width() * 0.8;
                    $('#jqwKategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'350px',position:'middle', resizable:false,title: title + ' PIC', zIndex:'99999'});
                    $('#jqwKategori').jqxWindow('setContent', data);
                });
            }else{
                swal.fire({ 
                    title: 'Anda tidak mempunyai alokasi  Unit Kerja!', 
                    text:'Tidak bisa melakukan penugasan',
                    icon: 'error'
                });
            }
        }

        function jvDelete(data_row){
            aso_idents = data_row['aso_idents'];
            asm_tahun = data_row['asm_tahun'];
            aso_operator_name = data_row['aso_operator_name'];

            swal.fire({ 
                title:'".$this->lang->line("confirm_delete")." Penugasan ' + aso_operator_name + ' untuk Asesmen Tahun ' + asm_tahun + '?', 
                icon: 'question',
                text:'".$this->lang->line("confirm_reason")."',
                input: 'text',
                inputPlaceholder: '".$this->lang->line("confirm_reason")."',
                inputValidator: (value) => {
                    if (!value) {
                    return '<center>".$this->lang->line("confirm_reason")."</center>'
                    }
                },
                showCancelButton: true, 
                confirmButtonText: '".$this->lang->line("Ya")."', 
                cancelButtonText: '".$this->lang->line("Tidak")."', 
                confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                cancelButtonColor: '".$this->config->item("cancelButtonColor")."'  
            }).then(result => {
                if(result.value) {
                    var alasan = $('input[placeholder=\'".$this->lang->line("confirm_reason")."\']').val();
                    var prm = {};
                    prm['aso_idents'] = aso_idents;
                    prm['aso_alasan'] = alasan;
                    prm['aso_tahun'] = asm_tahun;
                    prm['aso_operator_name'] = aso_operator_name;
                    $.post('/proses/penugasan/delete',prm,function(rebound){
                        if(rebound){
                            swal.fire('Data Penugasan ' + aso_operator_name + ' ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                            // jQuery.noConflict();
                            $('#" . $gridname  . "').DataTable().ajax.reload();
                        }
                    });
                }
            });
        }        
        </script>
        ";
        return $content;
    }
    function edit($type=null,$param=null,$source=null){
        // $this->common->debug_post();
        $lok_kabptn = "from_supervisor"; //
        $title = $this->input->post("title");
        $type = $this->input->post("type");
        $aso_idents = $this->input->post("aso_idents");
        $operator = null;
        $column = null;
        $aso_kelompok_indikator = null;
        $operator_name = null;
        $tags_multiple = true;
        $readonly = false;
        if($type!="add"){
            $column = $this->m_asesmen->getPenugasan_edit($aso_idents);
            $operator = $column->aso_operator;
            $operator_name = $column->USR_FNAMES;
            $aso_kelompok_indikator = $column->aso_kelompok_indikator;
            $tags_multiple = false;
        }
        if($type=="view"){
            $readonly = true;
        }
        // debug_array($column);
        $optUsers = array(
            'type'=>'json', 
            'url'=>'proses/asesmen/taguser/4/'.$this->usrunitkerja,
            "value_desc"=>$operator_name,
            "value"=>$operator,
            "placeHolder"=>"Please Select Name",
            "minimumInputLength"=>0,
            "value_with_id"=>true,
        );

        $field = array("asm_idents", "asm_tahun");
        $field = array("asm_idents", array("asm_periode"=>array("asm_tahun", "asm_periode")));
        $this->db->join("t_asm_unitkerja", "asm_idents = lok_asmidents", "INNER");
        $this->db->where("lok_unitkerja", $this->usrunitkerja);
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
        $optAsesmen = $this->crud->getGeneral_combo($arrayOpt);

        $field = array("idk_idents", "idk_nama");
        $this->db->where("idk_parent = 0");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_kategori",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            "empty"=>false
        );

        $optKategori = $this->crud->getGeneral_combo($arrayOpt);


        $this->db->select("distinct idk_parent id_child, idk_nama nama_child");
        $this->db->from("t_mas_kategori a");
        $this->db->join("t_asm_asesmen_process_area b", "a.idk_idents = b.par_process_area", "INNER");

        $sqlChild = $this->db->get_compiled_select();
        
        $field = array("idk_idents", "idk_nama");
        $this->db->where("idk_parent = 0");
        $this->db->join("(" . $sqlChild . ") as b", "a.idk_idents = b.id_child", "INNER");
        $this->db->order_by("idk_type, idk_idents");

        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_kategori a",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );

        $optCategories = $this->crud->getGeneral_combo($arrayOpt);
        
        $arrField = array(
            "aso_idents"=>array("label"=>"ID","type"=>"hid","value"=>$aso_idents),
            "aso_asmidents"=>array("group"=>1, "label"=>"Tahun Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen, "nextto"=>'<span class="input-group-text" data-toggle="modal" data-target="#modalQuestion"><i class="fas fa-question-circle"></i></span>', "cascade"=>array("param_cascade"=>"asm_tahun", "param_cascade_other"=> array("asm_idents"=>$aso_idents), "url_cascade"=>"/asesmen/nosj/getKategoriAsesmen_json","next_cascade"=>"aso_kelompok_indikator"), "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesmen tidak boleh kosong"), "readonly"=>$readonly),
            "aso_operator"=>array("group"=>1, "label"=>"Operator", "type"=>"cmb", "option"=>$optUsers, "size"=>"500px", "validation"=>array("validation"=>"notEmpty", "message"=>"Operator tidak boleh kosong"), "readonly"=>$readonly),
            "aso_kelompok_indikator"=>array("group"=>1, "label"=>"Kelompok Kategori","type"=>"cmb", "size"=>"500px", "option"=>$optKategori,"multiple"=>$tags_multiple, "tags"=>$tags_multiple, "readonly"=>$readonly),
            "aso_unitkerja"=>array("group"=>1, "label"=>"Unit Kerja","type"=>"hid","value"=>$this->usrunitkerja),
        );
        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);         
        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$aso_idents,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/proses/penugasan/save',
                    'formcommand' => '/proses/penugasan/save',
                    'tabname'=> array(
                        '1'=>'fas fa-check-circle^Data Asesmen', 
                    )
                );

        $button = null;
        $content = generateForm($arrForm, false);
        $content .= $button;
        if($type!="view"){
            $button = array(
                array(
                    "iconact"=>"fas fa-thumbs-up", "theme"=>"primary","href"=>"javascript:jvSave()", "textact"=>"Simpan"
                )
            );
            $title = $title." PIC";
        }else{
            $title = "Lihat Operator";
        }
        $unit_kerja = $this->usrunitkerja_desc;

        $title .=  " (" . $unit_kerja . ")";
        $content = createportlet(array("content"=>$content,"title"=>$title, "icon"=>"fas fa-calendar", "listaction"=>$button));
        $content .= "
        <script>
            function jvSave(validate=false){
                validator
                .validate()
                .then(function(status){
                    if(status!='Invalid'){
                        if($('#aso_kelompok_indikator').val()!=''){
                            $('#aso_kelompok_indikator_combo').val($('#aso_kelompok_indikator').val());
                            Swal.fire({ title:'Simpan Penugasan?', text: null, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak', 
                                confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                                cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                            }).then(result => { if(result.value) {document.formgw.submit()} });
                        }else{
                            swal.fire({title:'Kelompok Kategori tidak boleh Kosong!', icon:'error'});
                        }
                    }
                })
            }
        </script>
        ";
        $content .= '
        <div class="modal fade" id="modalQuestion" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-info-circle" style="color:#ffba00"></i> Informasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        Apabila Data Asesmen tidak ditemukan, mohon konfirmasi kepada Wali Data mengenai Alokasi Unit Kerja Anda ('.$unit_kerja . ')
                    </div>
                </div>
            </div>
        </div>
        ';
        echo $content;

    }
    function save(){
        // $this->common->debug_post(false);
		$aso_idents = $this->input->post('aso_idents');
		$aso_asmidents = $this->input->post('aso_asmidents');
		$aso_operator = $this->input->post('aso_operator');
		$aso_unitkerja = $this->input->post('aso_unitkerja');
		$aso_kelompok_indikator = $this->input->post('aso_kelompok_indikator');
		$aso_kelompok_indikator_combo = $this->input->post('aso_kelompok_indikator_combo');
		$hidTRNSKS = $this->input->post('hidTRNSKS');
        
        $input["aso_asmidents"] = $aso_asmidents;
        $input["aso_operator"] = $aso_operator;
        $input["aso_unitkerja"] = $aso_unitkerja;
        if($hidTRNSKS=="add"){
            $input["aso_usrnam"] = $this->username;
        }else{
            $input["aso_updnam"] = $this->username;
            $input["aso_upddat"] = $this->datesave;
        }

        $url = "/proses/penugasan";
        
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("aso_idents"=>$aso_idents)));
        $this->crud->useTable($this->table);

        if($aso_kelompok_indikator_combo==""){
            $aso_kelompok_indikator_combo = $aso_kelompok_indikator;
        }
        $aso_kelompok_indikator = explode(",", $aso_kelompok_indikator_combo);
        $loop = 1;
        $penugasan = "Kelompok Indikator : ";
        $rc = false;
        // debug_array($aso_kelompok_indikator);
        if(is_array($aso_kelompok_indikator)){
            $count = count($aso_kelompok_indikator);
        }
        foreach($aso_kelompok_indikator as $kelompok_indikator){
            if($rc) $penugasan .= ", ";
            $rsl = $this->m_master->getKategori_edit($kelompok_indikator);
            if($rsl->num_rows()>0){
                $row = $rsl->row();
            }
            $penugasan .= $row->idk_nama;
            $rc = true;
        }
        $rowUser = $this->m_master->getUsers_edit($aso_operator);
        if($rowUser!=""){
            $user_name = $rowUser->USR_LOGINS;
        }

        $title = "Penugasan";
        $body = "Penugasan untuk input data " . $penugasan;
        // $text = $user_name . ">> " . $this->username . ">>" . $title . ">>" . $body . ">>";

        // die($text);
        foreach($aso_kelompok_indikator as $kelompok_indikator){
            unset($input["aso_kelompok_indikator"]);
            $input["aso_kelompok_indikator"] = $kelompok_indikator;
            if(!$this->crud->save($input, array("aso_idents"=>$aso_idents))){
                $this->common->message_save('save_gagal',null, $url);
            }else{
                if($loop==$count){
                    $this->common->notifyUser(1, $user_name, $this->username, $title, $body);
                    $this->common->message_save('save_sukses',null, $url);
                }
            }
            $loop++;
        }
    }
    function delete(){
        $aso_idents = $this->input->post("aso_idents");
        $aso_alasan = $this->input->post("aso_alasan");
        $aso_tahun = $this->input->post("aso_tahun");
        $aso_operator_name = $this->input->post("aso_operator_name");
        $input["aso_is_deleted"] = 1;
        $input["aso_alasan"] = $aso_alasan;
        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$aso_idents,
            "log_action"=>array("aso_idents"=>$aso_idents, "action"=>"Hapus Penugasan ")
        );
        $this->common->logmodul(false, $arrModul);        
        $this->crud->useTable($this->table);
        if($this->crud->save($input, array("aso_idents"=>$aso_idents))){
            echo "berhasil";
        }else{
            echo "gagal";
        }
    }
}
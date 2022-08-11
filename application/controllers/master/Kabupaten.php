<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kabupaten extends MY_Controller {
    function __construct(){
        parent::__construct();
        $this->load->helper('ginput');
        $this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");
        $this->table = "t_mas_kabupaten";
    }

	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Kabupaten"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listProvinsi(),'admin',$bc);  	 
	}
    public function listProvinsi(){
        $gridname = "jqxLokasi";
        $this->load->helper('jqxgrid');
        $url ='/master/nosj/getKabupaten_list';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'KAB_IDENTS','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'PRV_IDENTS','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'total_lokasi','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"KAB_NAMESS", "aw"=>"50%", "label"=>"Nama Kabupaten","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"PRV_NAMESS", "aw"=>"20%", "label"=>"Nama Provinsi","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'KAB_USRNAM','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'KAB_USRDAT','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $jvDelete = "
            function jvDelete(data_row){
                kab_idents = data_row['KAB_IDENTS'];
                KAB_NAMESS = data_row['KAB_NAMESS'];
                total_lokasi = data_row['total_lokasi'];
                if(total_lokasi==0){
                    swal.fire({ 
                        title:'".$this->lang->line("confirm_delete")." Kabupaten : ' + KAB_NAMESS + '?', 
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
                            prm['kab_idents'] = id;
                            prm['kab_alasan'] = alasan; 
                            $.post('/master/kabupaten/delete',prm,function(rebound){
                                if(rebound){
                                    swal.fire('Data Kabupaten ' + KAB_NAMESS + ' ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                    jQuery.noConflict();
                                    $('#" . $gridname  . "').DataTable().ajax.reload();
                                }
                            });
                        }
                    });
                }else{
                    swal.fire({
                        title:'Kabupaten ' + KAB_NAMESS + ' " . $this->lang->line("confirm_delete_restrict"). "', 
                        text: 'Data sudah digunakan', 
                        icon: 'error'
                    });
                }
            }
        ";
        $jvAdd ="
        function jvAdd(){
            $('#imgPROSES').show();
            $('#windowProses').jqxWindow('open');
            var param = {};
            param['type'] = 'add';
            $('#jqwPopup').jqxWindow('open');
            $.post('/master/kabupaten/edit',param,function(datax){
                $('#windowProses').jqxWindow('close');
                var lebar = $(window).width();
                var tinggi = 450;
                $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Tambah Data Kabupaten'});  
                $('#jqwPopup').jqxWindow('setContent', datax);
            });
        }
        ";
        $jvEdit = "
        function jvEdit(data_row){
            kab_idents = data_row['KAB_IDENTS'];
            total_lokasi = data_row['total_lokasi'];
            if(kab_idents=='' || kab_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['kab_idents'] = kab_idents;
                param['type'] = 'edit';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/kabupaten/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 450;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Ubah Data Provinsi'});  
                    $('#jqwPopup').jqxWindow('setContent', datax);
                });
            }
        }        
        ";
        $jvView = "
        function jvView(data_row){
            kab_idents = data_row['KAB_IDENTS'];
            if(kab_idents=='' || kab_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['kab_idents'] = kab_idents;
                param['type'] = 'view';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/kabupaten/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 450;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Ubah Data Provinsi'});  
                    $('#jqwPopup').jqxWindow('setContent', datax);
                });
            }
        }        
        ";
        $content = gGrid(array('url'=>$url, 
            'grid'=>'datatables',
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            'button'=> 'standar',
            'jvDelete'=>$jvDelete,
            'jvAdd'=>$jvAdd,
            'jvEdit'=>$jvEdit,
            'jvView'=>$jvView,
            'sumber'=>'server',
            'modul'=>'master/provinsi',
            'closeform'=>false,
        ));
        $content .= form_input(array('name' => "kab_alasan",'id'=> "prv_alasan", 'type'=>'hidden'));
        $content .= form_close();
        
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        return $content;
    }
    function edit(){
        $column = null;
        $kab_idents = $this->input->post("kab_idents");
        $type = $this->input->post("type");
        $KAB_PRVIDN = null;
        if($type!="add"){
            $column = $this->m_master->getKabupaten_edit(1, $kab_idents);
            $KAB_PRVIDN = $column->KAB_PRVIDN;
        }
        $readonly = false;
        if($type=="view"){
            $readonly = true;
        }
        $field = array("PRV_IDENTS", "PRV_NAMESS");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_province",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );

        $optProvince = $this->crud->getGeneral_combo($arrayOpt);

        $path = "kabupaten";
        $dropzone = array(
            "path"=>$path,
            "autoupload"=>false,
            "url"=>base_url("/upload/berkas/".$path),
            "maxFilesize"=>30,
            "maxFiles"=>1,
        );        

        $arrField = array(
            "KAB_IDENTS"=>array("label"=>"ID","type"=>"hid","value"=>$kab_idents),
            "KAB_PRVIDN"=>array("group"=>1, "label"=>"Provinsi","type"=>"cmb","option"=>$optProvince, "size"=>"200px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Provinsi tidak boleh kosong"), "readonly"=>$readonly),
            "KAB_NAMESS"=>array("group"=>1, "label"=>"Nama ","type"=>"txt", "size"=>"200px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Kabupaten tidak boleh kosong"), "readonly"=>$readonly),
            "KAB_file"=>array("group"=>1, "label"=>"Logo","type"=>"fil", "size"=>"100", "location"=>"/assets/kabupaten/", 'dropzone'=>$dropzone),
            "hidFILES"=>array("group"=>1,"type"=>"hid")
        );
        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);
        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$kab_idents,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/master/kabupaten/save',
                    'formcommand' => '/master/kabupaten/save'
                );

        $button = null;
        if($type!="view"){
            $button = array(array("iconact"=>"fas fa-thumbs-up", "theme"=>"success","href"=>"javascript:jvSave(1)", "textact"=>"Simpan"));
        }
        $content = generateForm($arrForm, false);
        $content .= "
        <script>
        function jvSave(){
            validator
            .validate()
            .then(function(status){
                if(status!='Invalid'){
                    Swal.fire({ 
                        title:'Simpan Data Kabupaten?', 
                        text: null, 
                        icon: 'question', 
                        showCancelButton: true, 
                        confirmButtonText: 'Ya', 
                        cancelButtonText: 'Tidak', 
                        confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                        cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                    }).then(
                        result => { 
                            if(result.value) {
                                var myDz = Dropzone.forElement('.dropzone');
                                if (myDz.getQueuedFiles().length === 0) {
                                    document.".$formname.".submit();
                                } else {
                                    myDz.processQueue();
                                    myDz.on('success', (function(file, response) {
                                        $('#hidFILES').val(response);
                                        document.".$formname.".submit();
                                    }))                            
                                }
                            }
                        }
                    );	                    
                }
            });
        }
        </script>";
        $content = createportlet(array("content"=>$content,"title"=>"Data Kabupaten", "icon"=>"fas fa-map", "listaction"=>$button));
        echo $content;
    }
    function save(){
        $KAB_IDENTS = $this->input->post("KAB_IDENTS");
        $KAB_PRVIDN = $this->input->post("KAB_PRVIDN");
        $KAB_NAMESS = $this->input->post("KAB_NAMESS");
        $hidTRNSKS = $this->input->post("hidTRNSKS");        
        $hidFILES = $this->input->post("hidFILES");

        $input["KAB_PRVIDN"] = $KAB_PRVIDN;
        $input["KAB_NAMESS"] = $KAB_NAMESS;

        if($hidFILES!=""){
            $input["KAB_file"] = $hidFILES;
        }
        if($hidTRNSKS=="add"){
            $rowKabupaten = $this->m_master->getMaxKabupaten($KAB_PRVIDN);
            $input["KAB_IDENTS"] = $rowKabupaten;
            $input["KAB_USRNAM"] = $this->username;
        }else{
            $input["KAB_UPDNAM"] = $this->username;
            $input["KAB_UPDDAT"] = $this->datesave;
        }
        $url = "/master/kabupaten";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("KAB_IDENTS"=>$KAB_IDENTS)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("KAB_IDENTS"=>$KAB_IDENTS))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $this->common->message_save('save_sukses',null, $url);
        }
    }
    function delete(){
        $kab_idents = $this->input->post('kab_idents');
		$kab_alasan = $this->input->post('kab_alasan');
        $delete["KAB_is_deleted"] = 1;
        $delete["KAB_alasan"] = $kab_alasan;

        $this->crud->useTable($this->table);
        $this->crud->save($delete, array("KAB_IDENTS"=>$kab_idents), false);
        if($this->crud->__affectedRows <>0){

            $arrAction =array(
                "action"=> "Delete Data Kabupaten",
                "reason"=>$kab_alasan,
                "PRV_IDENTS"=>$kab_idents
            );

            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"1", 
                "log_action"=>$arrAction,
                "log_fkidents"=>$kab_idents
            );
            $this->common->logmodul(false, $arrModul);

            echo $this->lang->line("confirm_success");
        }else{
            echo $this->lang->line("confirm_failed");
        }

    }
    function getjson(){
        // $this->common->debug_post();
        $prv_id = $this->input->post("prv_id");
        $result = $this->m_master->getKabupaten_json($prv_id);
        $return = array();
        if($result->num_rows()>0){
            $return = $result->result();
        }

        echo json_encode($return);
    }
}
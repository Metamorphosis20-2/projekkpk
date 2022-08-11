<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Provinsi extends MY_Controller {
    function __construct(){
        parent::__construct();
        $this->load->helper('ginput');
        $this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");
        $this->table = "t_mas_province";
    }

	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Provinsi"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listProvinsi(),'admin',$bc);  	 
	}
    public function listProvinsi(){
        $gridname = "jqxLokasi";
        $this->load->helper('jqxgrid');
        $url ='/master/nosj/getProvinsi_list';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'PRV_IDENTS','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'total_lokasi','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"PRV_NAMESS", "aw"=>"50%", "label"=>"Nama Provinsi","adtype"=>"text");
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"PRV_is_deleted", "aw"=>"50%", "label"=>"Nama Provinsi","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'PRV_USRNAM','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'PRV_USRDAT','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal('Pilih Data!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var PRV_IDENTS = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'PRV_IDENTS');
            var PRV_NAMESS = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'PRV_NAMESS');
            var total_lokasi = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'total_lokasi');
        ";
        
        $jvDelete = "
            function jvDelete(data_row){
                prv_idents = data_row['PRV_IDENTS'];
                PRV_NAMESS = data_row['PRV_NAMESS'];
                total_lokasi = data_row['total_lokasi'];
                if(total_lokasi==0){
                    swal.fire({ 
                        title:'".$this->lang->line("confirm_delete")." Provinsi : ' + PRV_NAMESS + '?', 
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
                            prm['prv_idents'] = id;
                            prm['prv_alasan'] = alasan; 
                            $.post('/master/provinsi/delete',prm,function(rebound){
                                if(rebound){
                                    swal.fire('Data Provinsi ' + PRV_NAMESS + ' ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                    jQuery.noConflict();
                                    $('#" . $gridname  . "').DataTable().ajax.reload();
                                }
                            });
                        }
                    });
                }else{
                    swal.fire({
                        title:'Provinsi ' + PRV_NAMESS + ' " . $this->lang->line("confirm_delete_restrict"). "', 
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
            $.post('/master/provinsi/edit',param,function(datax){
                $('#windowProses').jqxWindow('close');
                var lebar = $(window).width();
                var tinggi = 400;
                $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Tambah Data Provinsi'});  
                $('#jqwPopup').jqxWindow('setContent', datax);
            });
        }
        ";
        $jvEdit = "
        function jvEdit(data_row){
            prv_idents = data_row['PRV_IDENTS'];
            PRV_NAMESS = data_row['PRV_NAMESS'];
            if(prv_idents=='' || prv_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['prv_idents'] = prv_idents;
                param['type'] = 'edit';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/provinsi/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 400;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Ubah Data Provinsi'});  
                    $('#jqwPopup').jqxWindow('setContent', datax);
                });
            }
        }        
        ";
        $jvView = "
        function jvView(data_row){
            prv_idents = data_row['PRV_IDENTS'];
            PRV_NAMESS = data_row['PRV_NAMESS'];
            if(prv_idents=='' || prv_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['prv_idents'] = prv_idents;
                param['type'] = 'view';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/provinsi/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 400;
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
        $content .= form_input(array('name' => "prv_alasan",'id'=> "prv_alasan", 'type'=>'hidden'));
        $content .= form_close();
        //====== end of grid
        
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        return $content;
    }
    function edit(){
        // $this->common->debug_post();
        $column = null;
        $prv_idents = $this->input->post("prv_idents");
        $type = $this->input->post("type");
        if($type!="add"){
            $column = $this->m_master->getProvinsi_edit(1, $prv_idents);
        }
        $readonly = false;
        if($type=="view"){
            $readonly = true;
        }
        $path = "provinsi";
        $dropzone = array(
            "path"=>$path,
            "autoupload"=>false,
            "url"=>base_url("/upload/berkas/".$path),
            "maxFilesize"=>30,
            "maxFiles"=>1,
        );
        $arrField = array(
            "PRV_IDENTS"=>array("label"=>"ID","type"=>"hid","value"=>$prv_idents),
            "PRV_NAMESS"=>array("group"=>1, "label"=>"Nama Provinsi","type"=>"txt", "size"=>"200px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Provinsi tidak boleh kosong"), "readonly"=>$readonly),
            "PRV_file"=>array("group"=>1, "label"=>"Logo","type"=>"fil", "size"=>"100", "location"=>"/assets/provinsi/", 'dropzone'=>$dropzone),
            "hidFILES"=>array("group"=>1,"type"=>"hid")
        );
        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);
        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$prv_idents,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/master/provinsi/save',
                    'formcommand' => '/master/provinsi/save'
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
                        title:'Simpan Data Provinsi?', 
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
            })	
        }
        </script>";
                        
        $content = createportlet(array("content"=>$content,"title"=>"Data Provinsi", "icon"=>"fas fa-map", "listaction"=>$button));
        echo $content;
    }
    function save(){
        // $this->common->debug_post();
        $PRV_IDENTS = $this->input->post("PRV_IDENTS");
        $PRV_NAMESS = $this->input->post("PRV_NAMESS");
        $hidFILES = $this->input->post("hidFILES");
        $hidTRNSKS = $this->input->post("hidTRNSKS");
        $input["PRV_NAMESS"] = $PRV_NAMESS;
        if($hidFILES!=""){
            $input["PRV_file"] = $hidFILES;
        }
        if($hidTRNSKS=="add"){
            $rowProvince = $this->m_master->getMaxProvince();
            $input["PRV_IDENTS"] = $rowProvince;
            $input["PRV_USRNAM"] = $this->username;
        }else{
            $input["PRV_UPDNAM"] = $this->username;
            $input["PRV_UPDDAT"] = $this->datesave;
        }
        $url = "/master/provinsi";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("PRV_IDENTS"=>$PRV_IDENTS)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("PRV_IDENTS"=>$PRV_IDENTS))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $this->common->message_save('save_sukses',null, $url);
        }
    }
    function delete(){
        $prv_idents = $this->input->post('prv_idents');
		$prv_alasan = $this->input->post('prv_alasan');
        $delete["PRV_is_deleted"] = 1;
        $delete["PRV_alasan"] = $prv_alasan;

        $this->crud->useTable($this->table);
        $this->crud->save($delete, array("PRV_IDENTS"=>$prv_idents), false);
        if($this->crud->__affectedRows <>0){

            $arrAction =array(
                "action"=> "Delete Data Provinsi",
                "reason"=>$prv_alasan,
                "PRV_IDENTS"=>$prv_idents
            );

            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"1", 
                "log_action"=>$arrAction,
                "log_fkidents"=>$prv_idents
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
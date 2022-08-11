<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Unitkerja extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
        $this->load->helper(array('ginput','chartjs'));
    	$this->load->model(array('m_asesmen', 'm_master'));
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
        $this->usrkabptn = $this->session->userdata("USR_KABPTN");

        $this->unt_unitkerja = $this->session->userdata('unt_unitkerja');
        $this->kab_namess = $this->session->userdata('KAB_NAMESS');
        
        $this->usr_level = $this->session->userdata("USR_LEVELS");
        $this->usr_idents = $this->session->userdata("USR_IDENTS");
        $this->table = "t_mas_unitkerja";
    }
    public function index(){        
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Unit Kerja"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listUnitKerja(),'admin',$bc);  	    
    }
    public function listUnitKerja(){
        $gridname = "jqxUnitKerja";
        $this->load->helper('jqxgrid');
        $url ='/master/nosj/getUnitKerja_list';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'unt_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'total_unitkerja','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"unt_unitkerja", "aw"=>"50%", "label"=>"Unit Kerja","adtype"=>"text");
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"PRV_is_deleted", "aw"=>"50%", "label"=>"Nama unitkerja","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'unt_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'unt_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal('Pilih Data!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var unt_idents = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'unt_idents');
            var unt_unitkerja = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'unt_unitkerja');
            var total_unitkerja = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'total_unitkerja');
        ";
        
        $jvDelete = "
            function jvDelete(data_row){
                unt_idents = data_row['unt_idents'];
                unt_unitkerja = data_row['unt_unitkerja'];
                total_unitkerja = data_row['total_unitkerja'];
                if(total_unitkerja==0){
                    swal.fire({ 
                        title:'".$this->lang->line("confirm_delete")." unitkerja : ' + unt_unitkerja + '?', 
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
                            prm['unt_idents'] = id;
                            prm['unt_alasan'] = alasan; 
                            $.post('/master/unitkerja/delete',prm,function(rebound){
                                if(rebound){
                                    swal.fire('Data unitkerja ' + unt_unitkerja + ' ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                    // jQuery.noConflict();
                                    $('#" . $gridname  . "').DataTable().ajax.reload();
                                }
                            });
                        }
                    });
                }else{
                    swal.fire({
                        title:'Unit Kerja ' + unt_unitkerja + ' " . $this->lang->line("confirm_delete_restrict"). "', 
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
            $.post('/master/unitkerja/edit',param,function(datax){
                $('#windowProses').jqxWindow('close');
                var lebar = $(window).width();
                var tinggi = 400;
                $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Tambah Data unitkerja'});  
                $('#jqwPopup').jqxWindow('setContent', datax);
            });
        }
        ";
        $jvEdit = "
        function jvEdit(data_row){
            unt_idents = data_row['unt_idents'];
            unt_unitkerja = data_row['unt_unitkerja'];
            if(unt_idents=='' || unt_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['unt_idents'] = unt_idents;
                param['type'] = 'edit';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/unitkerja/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 400;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Ubah Data unitkerja'});  
                    $('#jqwPopup').jqxWindow('setContent', datax);
                });
            }
        }        
        ";
        $jvView = "
        function jvView(data_row){
            unt_idents = data_row['unt_idents'];
            unt_unitkerja = data_row['unt_unitkerja'];
            if(unt_idents=='' || unt_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['unt_idents'] = unt_idents;
                param['type'] = 'view';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/unitkerja/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 400;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Lihat Data unitkerja'});  
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
            'modul'=>'master/unitkerja',
            'closeform'=>false,
        ));
        $content .= form_input(array('name' => "unt_alasan",'id'=> "unt_alasan", 'type'=>'hidden'));
        $content .= form_close();
        //====== end of grid
        
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        return $content;
    }

    function edit(){
        $column = null;
        $unt_idents = $this->input->post("unt_idents");
        $type = $this->input->post("type");
        if($type!="add"){
            $column = $this->m_master->getUnitkerja_edit($unt_idents);
        }
        $readonly = false;
        if($type=="view"){
            $readonly = true;
        }
        $path = "Unitkerja";
        $dropzone = array(
            "path"=>$path,
            "autoupload"=>false,
            "url"=>base_url("/upload/berkas/".$path),
            "maxFilesize"=>30,
            "maxFiles"=>1,
        );
        $arrField = array(
            "unt_idents"=>array("label"=>"ID","type"=>"hid","value"=>$unt_idents),
            "unt_unitkerja"=>array("group"=>1, "label"=>"Unit Kerja","type"=>"txt", "size"=>"200px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Unitkerja tidak boleh kosong"), "readonly"=>$readonly),
            "hidFILES"=>array("group"=>1,"type"=>"hid")
        );
        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);
        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$unt_idents,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/master/unitkerja/save',
                    'formcommand' => '/master/unitkerja/save'
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
                        title:'Simpan Data Unit Kerja?', 
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
                                document.".$formname.".submit();
                            }
                        }
                    );
                }
            })	
        }
        </script>";
                        
        $content = createportlet(array("content"=>$content,"title"=>"Data Unit Kerja", "icon"=>"fas fa-map", "listaction"=>$button));
        echo $content;
    }

    function save(){
        // $this->common->debug_post();
        $unt_idents = $this->input->post("unt_idents");
        $unt_unitkerja = $this->input->post("unt_unitkerja");
        $hidFILES = $this->input->post("hidFILES");
        $hidTRNSKS = $this->input->post("hidTRNSKS");
        $input["unt_unitkerja"] = $unt_unitkerja;
        if($hidTRNSKS=="add"){
            $input["unt_usrnam"] = $this->username;
        }else{
            $input["unt_updnam"] = $this->username;
            $input["unt_upddat"] = $this->datesave;
        }
        $url = "/master/unitkerja";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("unt_idents"=>$unt_idents)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("unt_idents"=>$unt_idents))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $this->common->message_save('save_sukses',null, $url);
        }
    }
    function delete(){
        $unt_idents = $this->input->post('unt_idents');
		$unt_alasan = $this->input->post('unt_alasan');
        $delete["unt_is_deleted"] = 1;
        $delete["unt_alasan"] = $unt_alasan;

        $this->crud->useTable($this->table);
        $this->crud->save($delete, array("unt_idents"=>$unt_idents), false);
        if($this->crud->__affectedRows <>0){

            $arrAction =array(
                "action"=> "Delete Data Unit Kerja",
                "reason"=>$unt_alasan,
                "unt_idents"=>$unt_idents
            );

            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"1", 
                "log_action"=>$arrAction,
                "log_fkidents"=>$unt_idents
            );
            $this->common->logmodul(false, $arrModul);

            echo $this->lang->line("confirm_success");
        }else{
            echo $this->lang->line("confirm_failed");
        }

    }    
}
<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Glossary extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
        $this->load->helper(array('ginput','chartjs'));
    	$this->load->model(array('m_master'));
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
        $this->usr_level = $this->session->userdata("USR_LEVELS");
        $this->usr_idents = $this->session->userdata("USR_IDENTS");
        $this->table = "t_mas_glossary";
    }
    public function index(){        
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Istilah"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listIstilah(),'admin',$bc);  	    
    }
    public function listIstilah(){
        $gridname = "jqxIstilah";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Glossary/list/m_master';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'glb_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"glb_istilah", "aw"=>"20%", "label"=>"Istilah","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"glb_deskripsi", "aw"=>"50%", "label"=>"Istilah","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'glb_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'glb_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $jvDelete = "
            function jvDelete(data_row){
                glb_idents = data_row['glb_idents'];
                glb_istilah = data_row['glb_istilah'];
                swal.fire({ 
                    title:'".$this->lang->line("confirm_delete")." Glossary : ' + glb_istilah + '?', 
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
                        prm['glb_idents'] = id;
                        prm['glb_alasan'] = alasan; 
                        $.post('/master/Glossary/delete',prm,function(rebound){
                            if(rebound){
                                swal.fire('Data Glossary ' + glb_istilah + ' ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                // jQuery.noConflict();
                                $('#" . $gridname  . "').DataTable().ajax.reload();
                            }
                        });
                    }
                });
            }
        ";
        $jvAdd ="
        function jvAdd(){
            $('#imgPROSES').show();
            $('#windowProses').jqxWindow('open');
            var param = {};
            param['type'] = 'add';
            $('#jqwPopup').jqxWindow('open');
            $.post('/master/Glossary/edit',param,function(datax){
                $('#windowProses').jqxWindow('close');
                var lebar = $(window).width();
                var tinggi = 400;
                $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Tambah Data Glossary'});  
                $('#jqwPopup').jqxWindow('setContent', datax);
            });
        }
        ";
        $jvEdit = "
        function jvEdit(data_row){
            glb_idents = data_row['glb_idents'];
            glb_istilah = data_row['glb_istilah'];
            if(glb_idents=='' || glb_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['glb_idents'] = glb_idents;
                param['type'] = 'edit';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/Glossary/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 500;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Ubah Data Glossary'});  
                    $('#jqwPopup').jqxWindow('setContent', datax);
                });
            }
        }        
        ";
        $jvView = "
        function jvView(data_row){
            glb_idents = data_row['glb_idents'];
            if(glb_idents=='' || glb_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['glb_idents'] = glb_idents;
                param['type'] = 'view';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/Glossary/edit',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 500;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Ubah Data Glossary'});  
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
            'modul'=>'master/Glossary',
            'closeform'=>false,
        ));
        $content .= form_input(array('name' => "glb_alasan",'id'=> "glb_alasan", 'type'=>'hidden'));
        $content .= form_close();
        //====== end of grid
        
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        return $content;
    }

    function edit(){
        $column = null;
        $glb_idents = $this->input->post("glb_idents");
        $type = $this->input->post("type");
        if($type!="add"){
            $column = $this->m_master->getGlossary_edit($glb_idents);
        }
        $readonly = false;
        if($type=="view"){
            $readonly = true;
        }
        $arrField = array(
            "glb_idents"=>array("label"=>"ID","type"=>"hid","value"=>$glb_idents),
            "glb_istilah"=>array("group"=>1, "label"=>"Istilah","type"=>"txt", "size"=>"200px", "readonly"=>$readonly, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Glossary tidak boleh kosong"), "readonly"=>$readonly),
            "glb_deskripsi"=>array("group"=>1, "label"=>"Deskripsi","type"=>"txa", "size"=>"200px", "readonly"=>$readonly, 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px','width'=>'350px')),
            "hidFILES"=>array("group"=>1,"type"=>"hid")
        );
        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);
        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$glb_idents,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/master/Glossary/save',
                    'formcommand' => '/master/Glossary/save'
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
                        title:'Simpan Data Glossary?', 
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
                        
        $content = createportlet(array("content"=>$content,"title"=>"Data Glossary", "icon"=>"fas fa-book", "listaction"=>$button));
        echo $content;
    }

    function save(){
        // $this->common->debug_post();
        $glb_idents = $this->input->post("glb_idents");
        $glb_istilah = $this->input->post("glb_istilah");
        $glb_deskripsi = $this->input->post("glb_deskripsi");
        $hidTRNSKS = $this->input->post("hidTRNSKS");
        $input["glb_istilah"] = $glb_istilah;
        $input["glb_deskripsi"] = $glb_deskripsi;

        if($hidTRNSKS=="add"){
            $input["glb_usrnam"] = $this->username;
        }else{
            $input["glb_updnam"] = $this->username;
            $input["glb_upddat"] = $this->datesave;
        }
        $url = "/master/Glossary";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("glb_idents"=>$glb_idents)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("glb_idents"=>$glb_idents))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $this->common->message_save('save_sukses',null, $url);
        }
    }
    function delete(){
        $glb_idents = $this->input->post('glb_idents');
		$glb_alasan = $this->input->post('glb_alasan');
        $delete["glb_is_deleted"] = 1;
        $delete["glb_alasan"] = $glb_alasan;

        $this->crud->useTable($this->table);
        $this->crud->save($delete, array("glb_idents"=>$glb_idents), false);
        if($this->crud->__affectedRows <>0){

            $arrAction =array(
                "action"=> "Delete Data Glossary",
                "reason"=>$glb_alasan,
                "glb_idents"=>$glb_idents
            );

            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"1", 
                "log_action"=>$arrAction,
                "log_fkidents"=>$glb_idents
            );
            $this->common->logmodul(false, $arrModul);

            echo $this->lang->line("confirm_success");
        }else{
            echo $this->lang->line("confirm_failed");
        }

    }    
}
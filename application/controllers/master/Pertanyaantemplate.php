<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pertanyaantemplate extends MY_Controller {
    function __construct(){
        parent::__construct();
        $this->load->helper('ginput');
        $this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");
        $this->table = "t_mas_pertanyaan_template";
    }

	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Template Pertanyaan"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listTemplate(),'admin',$bc);  	 
	}
    public function listTemplate(){
        $gridname = "jqxLokasi";
        $this->load->helper('jqxgrid');
        $url ='/master/nosj/getPertanyaantemplate_list';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tmp_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"tmp_pertanyaan", "aw"=>"50%", "label"=>"Pertanyaan","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tmp_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tmp_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');
          
        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal('Pilih Data!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var tmp_idents = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'tmp_idents');
            var tmp_pertanyaan = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'tmp_pertanyaan');
        ";
        
        $jvDelete = "
            function jvDelete(data_row){
                tmp_idents = data_row['tmp_idents'];
                tmp_pertanyaan = data_row['tmp_pertanyaan'];
                swal.fire({ 
                    title:'".$this->lang->line("confirm_delete")." Pertanyaan : ' + tmp_pertanyaan + '?', 
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
                        prm['tmp_idents'] = id;
                        prm['tmp_alasan'] = alasan; 
                        $.post('/master/pertanyaantemplate/delete',prm,function(rebound){
                            if(rebound){
                                swal.fire('Template Pertanyaan ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                jQuery.noConflict();
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
            $.post('/master/pertanyaantemplate/edit',param,function(datax){
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
            tmp_idents = data_row['tmp_idents'];
            tmp_pertanyaan = data_row['tmp_pertanyaan'];
            if(tmp_idents=='' || tmp_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['tmp_idents'] = tmp_idents;
                param['type'] = 'edit';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/pertanyaantemplate/edit',param,function(datax){
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
            tmp_idents = data_row['tmp_idents'];
            tmp_pertanyaan = data_row['tmp_pertanyaan'];
            if(tmp_idents=='' || tmp_idents==null){
                swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
            }else{
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['tmp_idents'] = tmp_idents;
                param['type'] = 'view';
                $('#jqwPopup').jqxWindow('open');
                $.post('/master/pertanyaantemplate/edit',param,function(datax){
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
            'modul'=>'master/pertanyaantemplate',
            'closeform'=>false,
        ));
        $content .= form_input(array('name' => "tmp_alasan",'id'=> "tmp_alasan", 'type'=>'hidden'));
        $content .= form_close();
        //====== end of grid
        
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        return $content;
    }
    function edit(){
        // $this->common->debug_post();
        $column = null;
        $tmp_idents = $this->input->post("tmp_idents");
        $type = $this->input->post("type");
        if($type!="add"){
            $column = $this->m_master->getPertanyaantemplate_edit($tmp_idents);
        }
        $readonly = false;
        if($type=="view"){
            $readonly = true;
        }
        $arrField = array(
            "tmp_idents"=>array("label"=>"ID","type"=>"hid","value"=>$tmp_idents),
            "tmp_pertanyaan"=>array("group"=>1, "label"=>"Pertanyaan","type"=>"txa", "size"=>"200px", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px','width'=>'350px'), "validation"=>array("validation"=>"ckeditorempty", "message"=>"Template Pertanyaan tidak boleh kosong"), "readonly"=>$readonly),
        );
        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);
        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$tmp_idents,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/master/pertanyaantemplate/save',
                    'formcommand' => '/master/pertanyaantemplate/save'
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
                        title:'Simpan Template Pertanyaan?', 
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
                        
        $content = createportlet(array("content"=>$content,"title"=>"Template Pertanyaan", "icon"=>"fas fa-map", "listaction"=>$button));
        echo $content;
    }
    function save(){
        // $this->common->debug_post();
        $tmp_idents = $this->input->post("tmp_idents");
        $tmp_pertanyaan = $this->input->post("tmp_pertanyaan");
        $hidFILES = $this->input->post("hidFILES");
        $hidTRNSKS = $this->input->post("hidTRNSKS");
        $input["tmp_pertanyaan"] = $tmp_pertanyaan;
        if($hidFILES!=""){
            $input["PRV_file"] = $hidFILES;
        }
        if($hidTRNSKS=="add"){
            $input["tmp_usrnam"] = $this->username;
        }else{
            $input["tmp_updnam"] = $this->username;
            $input["tmp_upddat"] = $this->datesave;
        }
        $url = "/master/pertanyaantemplate";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("tmp_idents"=>$tmp_idents)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("tmp_idents"=>$tmp_idents))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $this->common->message_save('save_sukses',null, $url);
        }
    }
    function delete(){
        $tmp_idents = $this->input->post('tmp_idents');
		$tmp_alasan = $this->input->post('tmp_alasan');
        $delete["tmp_is_deleted"] = 1;
        $delete["tmp_alasan"] = $tmp_alasan;

        $this->crud->useTable($this->table);
        $this->crud->save($delete, array("tmp_idents"=>$tmp_idents), false);
        if($this->crud->__affectedRows <>0){

            $arrAction =array(
                "action"=> "Delete Data Template Pertanyaan",
                "reason"=>$tmp_alasan,
                "tmp_idents"=>$tmp_idents
            );

            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"1", 
                "log_action"=>$arrAction,
                "log_fkidents"=>$tmp_idents
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
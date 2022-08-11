<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbox extends MY_Controller {
    function __construct(){
        parent::__construct();
        $this->load->helper('ginput');
        $this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");
        $this->table = "t_inbox";
    }

	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Pesan"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listPesan(),'admin',$bc);  	 
	}
    public function listPesan(){
        $gridname = "jqxInbox";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Inbox/list';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'inb_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'inb_title','aw'=>'150','label'=>"Judul");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'inb_message','aw'=>'350','label'=>"Pesan");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'inb_is_read_desc','aw'=>'50','label'=>"Status");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'inb_usrnam','aw'=>'80','label'=>"Pengirim", 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'inb_usrdat','aw'=>'120','label'=>"Tanggal Kirim", 'group'=>$this->info_profil,'ga'=>'center');

        $buttonrow = array("view"=>array("icon"=>"reply", "function"=>"jvReply('answer', data_row)", "idents"=>"inb_idents", 'buttonclass'=>'primary'));

        $content = gGrid(array('url'=>$url, 
            'grid'=>'datatables',
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,
            'surrounded'=>true,
            'inline_buttonrow'=>$buttonrow,
            'inline_button_pos'=>'left',
            'sumber'=>'server',
            'modul'=>'inbox'
        ));
        //====== end of grid
        
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        $content .= "
        <script>

        function jvReply(type, data_row){
            inb_idents = data_row['inb_idents'];
            $('#imgPROSES').show();
            $('#windowProses').jqxWindow('open');
            var param = {};
            param['inb_idents'] = inb_idents;
            param['type'] = type;
            $('#jqwPopup').jqxWindow('open');
            $.post('/inbox/edit',param,function(datax){
                $('#windowProses').jqxWindow('close');
                var lebar = $(window).width();
                var tinggi = 600;
                $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Pesan'});  
                $('#jqwPopup').jqxWindow('setContent', datax);
            });
        }                
        </script>";
        return $content;
    }
    function edit(){
        $inb_idents = $this->input->post("inb_idents");
        $type = $this->input->post("type");        
        $this->read($inb_idents);
        if($type!="add"){
            $column = $this->crud->getInbox_edit($inb_idents);
            // debug_array($column);
            $inb_title = $column->inb_title;
            $inb_usrnam = $column->inb_usrnam;
            $inb_fkidents = $column->inb_fkidents;
        }
        // debug_array($inb_fkidents);
        $arrField = array(
            "inb_idents"=>array("label"=>"ID","type"=>"hid"),
            "inb_fkidents"=>array("label"=>"ID","type"=>"hid"),
            "inb_usrnam"=>array("group"=>1, "label"=>"inb_usrnam","type"=>"hid"),
            "inb_usrdat"=>array("group"=>1, "label"=>"inb_usrdat","type"=>"hid"),
            "inb_title"=>array("group"=>1, "label"=>"inb_title","type"=>"hid"),
            "inb_message"=>array("group"=>1, "label"=>"Pesan","type"=>"txa", "size"=>"200px", "readonly"=>true, 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px','width'=>'350px')),
            "inb_balasan"=>array("group"=>1, "label"=>"Balasan","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px','width'=>'350px')),
            // "inb_message"=>array("group"=>2, "label"=>"Pesan","type"=>"txa", "size"=>"200px", "readonly"=>true),
        );

        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);
        $formname = "formgw";
        $arrTabs = array(
            '1'=>'fas fa-envelope^Pesan'
        );
        if($inb_fkidents!=""){
            $arrField = array_merge($arrField, array(
                "tny_pertanyaan"=>array("group"=>2,"label"=>"Pertanyaan","type"=>"txt", "readonly"=>true),
                "jwb_deskripsi"=>array("group"=>2,"label"=>"Deskripsi","type"=>"txa", "readonly"=>true),
                "jwb_link"=>array("group"=>2,"label"=>"Link","type"=>"txt", "readonly"=>true),
                "jwb_file"=>array("group"=>2,"label"=>"Berkas","type"=>"viwfil", "icon"=>true, "location"=>"/assets/kuesioner/"),
            ));
            $arrTabs = array_merge($arrTabs, array('2'=>'fas fa-question-circle^Jawaban'));
        }
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$inb_idents,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/inbox/save',
                    'formcommand' => '/inbox/save',
                    'tabname'=> $arrTabs
                );

        $button = null;
        if($type!="view"){
            $button = array(array("iconact"=>"fas fa-reply", "theme"=>"primary","href"=>"javascript:jvSave(1)", "textact"=>"Balas"));
        }
        $content = generateForm($arrForm);
        $content .= "
        <script>
        function jvSave(){
            var inb_balasan = CKEDITOR.instances.inb_balasan.getData();
            // alert(nb_balasan
            if(inb_balasan!=''){
                Swal.fire({ 
                    title:'Kirim Pesan?', 
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
            }else{
                swal.fire({ title:'Pesan tidak boleh kosong!', text: null, icon: 'error', timer: 4000});
            }
        }
        </script>";
        $title = "[" . $inb_usrnam . "] <b>" . $inb_title ."</b>";
        $content = createportlet(array("content"=>$content,"title"=>$title, "icon"=>"fas fa-comments", "listaction"=>$button));
        echo $content;
    }
    function read($inb_idents){
        $inbox["inb_is_read"] = 1;
        $inbox["inb_date_read"] = $this->datesave;
        $this->crud->useTable($this->table);
        $this->crud->save($inbox, array("inb_idents"=>$inb_idents));
    }
    function save(){
        // $this->common->debug_post();
        $inb_fkidents = $this->input->post("inb_fkidents");
        $inb_usrdat= $this->input->post("inb_usrdat");
        $inb_idents = $this->input->post("inb_idents");
        $inb_title = $this->input->post("inb_title");
        $inb_balasan = $this->input->post("inb_balasan");
        $inb_message = $this->input->post("inb_message");
        $inb_usrnam = $this->input->post("inb_usrnam");
        $hidTRNSKS = $this->input->post("hidTRNSKS"); 

        $input["inb_is_read"] = 1;
        $input["inb_date_reply"] = $this->datesave;
        
        $url = "/inbox";
        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$inb_idents,
            "log_action"=>array("inb_idents"=>$inb_idents, "action"=>"Balas Pesan")
        );
        $this->common->logmodul(false, $arrModul);
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("inb_idents"=>$inb_idents))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $title = "Re : " . $inb_title;
            $body = $inb_balasan;
            $body .="<br><br>On " . $inb_usrdat .", " . $inb_usrnam . " wrote:<br>" . $inb_message . "";
            $this->common->notifyUser(1, $inb_usrnam, $this->username, $title, $body, $inb_fkidents);
            $this->common->message_save('save_sukses',null, $url);
        }
    }
}
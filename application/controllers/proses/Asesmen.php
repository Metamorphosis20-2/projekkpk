<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Asesmen extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
    	$this->load->helper('ginput');
    	$this->load->model(array('m_asesmen'));
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
        $this->table = "t_asm_unitkerja";
    }	
	public function index(){
        // $this->common->debug_array($this->session->userdata());
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Jadwal Asesmen"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listAsesmen(),'admin',$bc);  	 
	}
    public function listAsesmen(){
        $gridname = "jqxGrant";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Asesmenproses/list/m_asesmen';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"lok_unitkerja", "aw"=>120, "label"=>"Unit Kerja","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_start", "aw"=>80,  "label"=>$this->start, "group"=>"Tanggal","adtype"=>"text","adtype"=>"date", 'aw'=>80,'cf'=>'dd-MM-yyyy','adtype'=>'date');
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_end", "aw"=>80,    "label"=>$this->end, "group"=>"Tanggal","adtype"=>"text","adtype"=>"date", 'aw'=>80,'cf'=>'dd-MM-yyyy','adtype'=>'date');
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"lok_supervisor_name", "aw"=>120, "label"=>"Supervisor","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"lok_supervisor", "aw"=>120, "label"=>"Supervisor","ah"=>true);
        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal.fire('Pilih Data!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var lok_idents = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'lok_idents');
            var lok_supervisor = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'lok_supervisor');
            var lok_unitkerja = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'lok_unitkerja');
        ";
        
        $jvDelete = "
            function jvDelete(){
                " . $selrow . "
                total_grant = 0;
                if(total_grant==0){
                        swal.fire({ 
                            title:'".$this->lang->line("confirm_delete")." Asesmen tahun ' + asm_tahun + '?', 
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
                                prm['asm_idents'] = id;
                                prm['asm_alasan'] = alasan;                                                                
                                $.post('/asesmen/asesmen/delete',prm,function(rebound){
                                    if(rebound){
                                        swal.fire('Data Asesmen ' + asm_tahun + ' ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                        $('#" . $gridname . "').jqxGrid('updateBoundData');
                                    }
                                });
                            }
                        });
                }else{
                    swal.fire({
                        title:'Asesmen ' + asm_nomor + ' " . $this->lang->line("confirm_delete_restrict"). "', 
                        text: 'Data sudah digunakan', 
                        icon: 'error'
                    });
                }
            }
        ";
        
        $jvEdit = "";

        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);
        $oADD = strpos("N".$otorisasi,"E");
        $buttonother = null;
        // if($oADD>0){
        //     $buttonother = array(
        //         "Admin OPD"=>array('Print1','fa-plus','jvAddSupervisor()','warning','80')
        //     );
        // }
        $content = gGrid(array('url'=>$url, 
            'grid'=>'datatables',
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            'buttonother'=> $buttonother,
            // 'jvDelete'=>$jvDelete,
            // 'jvEdit'=>$jvEdit,
            'sumber'=>'server',
            'modul'=>'proses/asesmen'
        ));
        //====== end of grid
        $content .= generateWindowjqx(array('window'=>'Kategori','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        $content .= "
        <script>

        function jvAddSupervisor(){
            " . $selrow . "
            if(lok_idents==''){
                swal.fire({title:'Pilih Data', icon:'error'});
            }else{
                alert(lok_supervisor);
                if(lok_supervisor==''){
                    title = 'Assign';
                }else{
                    title = 'Ubah';
                }
                var param = {};
                param['type'] = 'edit';
                param['title'] = title;
                param['lok_idents'] = id;
                param['lok_unitkerja'] = lok_unitkerja;
                param['lok_supervisor'] = lok_supervisor;

                $('#jqwKategori').jqxWindow('open');

                $.post('/proses/asesmen/edit', param,function(data){
                    var lebar = $(window).width() * 0.8;
                    $('#jqwKategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'520px',position:'middle', resizable:false,title: title + ' Supervisor', zIndex:'99999'});
                    $('#jqwKategori').jqxWindow('setContent', data);
                });
            }
        }        
        </script>
        ";
        return $content;
    }
    function edit($type=null,$param=null,$source=null){
        $param = $this->input->post("lok_idents");
        $title = $this->input->post("title");
        $lok_unitkerja = $this->input->post("lok_unitkerja");
        $lok_supervisor = $this->input->post("lok_supervisor");
        $type = $this->input->post("type");

        $column = null;
        $button = null;
        $readonly = false;
        
        $column = $this->m_asesmen->getLokasi_edit($param);
        $supervisor = $column->lok_supervisor;
        $supervisor_name = $column->USR_FNAMES;
        $optUsers = array(
            'type'=>'json', 
            'url'=>'proses/asesmen/taguser/4/'.$lok_unitkerja,
            "value"=>$supervisor_name,
            "placeHolder"=>"Please Select Name",
            "minimumInputLength"=>0,
            "value_with_id"=>true,
        );

        $arrField = array(
            "lok_idents"=>array("label"=>"ID","type"=>"hid","value"=>$param),
            "asm_tahun"=>array("group"=>1, "label"=>"Tahun Penilaian","type"=>"txt", "readonly"=>true, "size"=>100),
            "lok_periode_start"=>array("group"=>1, "label"=>"Periode Mulai","type"=>"dat", "readonly"=>true),
            "lok_periode_end"=>array("group"=>1, "label"=>"Periode Berakhir","type"=>"dat", "readonly"=>true),
            "lok_supervisor"=>array("group"=>1, "label"=>"Supervisor", "type"=>"cmb", "option"=>$optUsers, "size"=>"500px"),
            // "asm_keterangan"=>array("group"=>1, "label"=>"Keterangan","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px')),
        );
        $arrTable = $this->common->generateArray($arrField, $this->table, $column, false);         
        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
					'param' =>$param,
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/proses/asesmen/save',
                    'formcommand' => '/proses/asesmen/save',
                    'tabname'=> array(
                        '1'=>'fas fa-check-circle^Data Asesmen', 
                    )
                );

        $button = null;
        $content = generateForm($arrForm, false);
        $content .= $button;
        $content .= "
        <script>
        $(document).ready(function () {
            $('#asm_tahun').on('select2:select', function (e) {
                var data = e.params.data;
                id_tahun = data.id;
                id_tahun = parseInt(id_tahun) + 1;
                $('#asm_periode_start').jqxDateTimeInput({ 
                    width: '110px',
                    height: '35px',  
                    formatString:'yyyy-MM-dd',
                    theme:'arctic',
                    min: new Date(id_tahun, 0, 1)
                });
                $('#asm_periode_start').jqxDateTimeInput({value:new Date(id_tahun, 0, 1)});

                $('#asm_periode_end').jqxDateTimeInput({ 
                    width: '110px',
                    height: '35px',  
                    formatString:'yyyy-MM-dd',
                    theme:'arctic',
                    min: new Date(id_tahun, 0, 1)
                });
                $('#asm_periode_end').jqxDateTimeInput({value:new Date(id_tahun, 0, 1)});
            });            
        });

        </script>
        ";
        if($type!="view"){
            $button = array(
                array(
                    "iconact"=>"fas fa-thumbs-up", "theme"=>"primary","href"=>"javascript:jvSave(1)", "textact"=>"Simpan"
                )
            );
            $title = $title." Supervisor";
        }else{
            $title = "Lihat Supervisor";
        }
        $content = createportlet(array("content"=>$content,"title"=>$title, "icon"=>"fas fa-calendar", "listaction"=>$button));

        echo $content;
    }
    function save(){
        // $this->common->debug_post();
        $lok_idents = $this->input->post("lok_idents");
        $lok_supervisor = $this->input->post("lok_supervisor");

        $input["lok_supervisor"] = $lok_supervisor;
        $input["lok_spvnam"] = $this->username;
        $input["lok_spvdat"] = $this->datesave;

        $url = "/proses/asesmen";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("lok_idents"=>$lok_idents)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("lok_idents"=>$lok_idents))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $this->common->message_save('save_sukses',null, $url);
        }
    }
    function delete(){
        $asm_idents = $this->input->post("asm_idents");
        $asm_alasan = $this->input->post("asm_alasan");
        $input["asm_is_deleted"] = 1;
        $input["asm_alasan"] = $asm_alasan;

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$asm_idents,
            "log_action"=>array("asm_idents"=>$asm_idents, "reason"=>$asm_alasan, "action"=>"Hapus Asesmen")
        );
        $this->common->logmodul(false, $arrModul);        
        $this->crud->useTable($this->table);
        if($this->crud->save($input, array("asm_idents"=>$asm_idents))){
            echo "berhasil";
        }else{
            echo "gagal";
        }
    }
    function taguser($usr_level, $lok_unitkerja){
        $this->db->where("IFNULL(USR_ACCESS,1) <> 2");
        $arrParameter = array('model'=>"m_master", 'function'=>"getUsers_tag", "funcparam"=> array("usr_level"=>$usr_level, "lok_unitkerja"=>$lok_unitkerja), "type"=>"combo");
        $tag = autotag($arrParameter);
        // $this->common->debug_sql(1);
        echo $tag;
    }

}
?>
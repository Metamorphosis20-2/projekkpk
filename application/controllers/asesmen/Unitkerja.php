<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Unitkerja extends MY_Controller {
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
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Unit Kerja"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listLokasi(),'admin',$bc);  	 
	}
    public function listLokasi(){
        $gridname = "jqxLokasi";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Unitkerja/list/m_asesmen';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_unitkerja','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_nomor", "aw"=>"10%", "label"=>"No. Asesmen","adtype"=>"text");
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_nama", "aw"=>"20%", "label"=>"Nama Asesmen","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_start", "aw"=>80,  "label"=>"Mulai", "group"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_end", "aw"=>80,    "label"=>"Selesai", "group"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"unt_unitkerja", "aw"=>200,  "label"=>"Unit Kerja", 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $jvDelete = "
            function jvDelete(data_row){
                lok_idents = data_row['lok_idents'];
                asm_tahun = data_row['asm_tahun'];
                unt_unitkerja = data_row['unt_unitkerja'];

                unitkerja = 'Tahun ' + asm_tahun + ' untuk Unit Kerja ' + unt_unitkerja;
                swal.fire({ 
                    title:'".$this->lang->line("confirm_delete")." Lokasi Asesmen ' + unitkerja + '?', 
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
                        prm['lok_idents'] = lok_idents;
                        prm['catatan'] = alasan;                                                                
                        $.post('/asesmen/unitkerja/delete',prm,function(rebound){
                            if(rebound){
                                swal.fire('Data Asesmen Unit Kerja ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                // jQuery.noConflict();
                                $('#" . $gridname  . "').DataTable().ajax.reload();

                            }
                        });
                    }
                });
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
            'sumber'=>'server',
            'modul'=>'asesmen/unitkerja'
        ));
        //====== end of grid
        return $content;
    }
    function show($type=null, $index = null, $source=null){
        $index = $this->input->post("grdIDENTS");
        $content = $this->edit($type, $index, $source);
        $judul = $this->btnUbah;
        if($type=="add"){
            $judul = $this->btnTambah;
        }
        if($type=="view"){
            $judul = $this->btnLihat;
        }
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/asesmen/unitkerja','text'=>"Daftar Unit Kerja"),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function edit($type=null,$param=null,$source=null){
        $column = null;
        $button = null;
        $readonly = false;
        $lok_unitkerja = null;
        $tags = true;
        $multiple = true;
        $lok_idents = null;
        if($type!="add"){
            $column = $this->m_asesmen->getUnitkerja_edit($param);
            $lok_idents = $column->lok_idents;
            $lok_unitkerja = $column->lok_unitkerja;
            $tags = false;
            $multiple = false;

            if($type=="view"){
                $readonly = true;
            }            
        }else{
            $lok_idents = null;
        }
        $this->db->where("IFNULL(unt_is_deleted,0) <> 1");
        $field = array("unt_idents", "unt_unitkerja");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_unitkerja",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );

        $optUnitkerja = $this->crud->getGeneral_combo($arrayOpt);

        // $field = array("asm_idents", "asm_tahun");
        $field = array("asm_idents", array("asm_periode"=>array("asm_tahun", "asm_periode")));
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            "empty"=>true
            // "empty"=>FALSE
        );
        
        $optAsesmen = $this->crud->getGeneral_combo($arrayOpt);

        $arr_periode_start = array("group"=>1, "label"=>"Periode Mulai","type"=>"dat", "readonly"=>$readonly);
        $arr_periode_end = array("group"=>1, "label"=>"Periode Berakhir","type"=>"dat", "readonly"=>$readonly);
        if(!$this->backdate){
            $arr_periode_start = array_merge($arr_periode_start, array("min"=>Date('Y-m-d')));
            $arr_periode_end = array_merge($arr_periode_start, array("min"=>Date('Y-m-d')));
        };

        $arrField = array(
            "lok_idents"=>array("label"=>"ID","type"=>"hid","value"=>$param),
            "lok_asmidents"=>array("group"=>1, "label"=>"Tahun Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen, "cascade"=>array("param_cascade"=>"lok_asmidents", "url_cascade"=>"/asesmen/nosj/getUnitKerjaAsesmen_json","next_cascade"=>"lok_unitkerja"), "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesmen tidak boleh kosong"), "readonly"=>$readonly),
            "lok_periode_start"=>$arr_periode_start,
            "lok_periode_end"=>$arr_periode_end,
            "lok_unitkerja"=>array("group"=>1, "label"=>"Unit Kerja","type"=>"cmb","option"=>$optUnitkerja, 'tags'=>$tags, 'multiple'=>$multiple, "size"=>"400px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Unit Kerja tidak boleh kosong"), "readonly"=>$readonly),
            "lok_keterangan"=>array("group"=>1, "label"=>"Keterangan","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'), "readonly"=>$readonly),
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
					'modul' => '/asesmen/unitkerja/save',
                    'formcommand' => '/asesmen/unitkerja/save',
                    'tabname'=> array(
                        '1'=>'fas fa-map^Data Unit Kerja', 
                    )
                );

        $button = null;
        if($type!="view"){
            $button = createButton(null, true, true, true);
        }
        $content = generateForm($arrForm);
        $content .= $button;
        
        $content .="
        <script>
            $(document).ready(function () {
                $('#lok_asmidents').on('select2:select', function (e) {
                    var data = e.params.data;
                    var keydata = data.id;
                    var param = {};
                    param['asm_idents'] = keydata;
                    $('#imgPROSES').show();
                    $('#windowProses').jqxWindow('open');
                    $.post('/asesmen/asesmen/json', param,function(jsonreturn){
                        $('#windowProses').jqxWindow('close');
                        var returnjson = JSON.parse(jsonreturn);

                        asm_periode_start = returnjson.asm_periode_start;
                        arrStart = asm_periode_start.split('-');

                        year_start = arrStart[0]; 
                        month_start = arrStart[1]-1; 
                        day_start = arrStart[2];

                        asm_periode_end = returnjson.asm_periode_end;
                        arrEnd = asm_periode_end.split('-');

                        year_end = arrEnd[0]; 
                        month_end = arrEnd[1]-1; 
                        day_end = arrEnd[2];

                        $('#lok_periode_start').jqxDateTimeInput({ 
                            width: '110px',
                            height: '35px',  
                            formatString:'yyyy-MM-dd',
                            theme:'arctic',
                            min: new Date(year_start, month_start, day_start)
                        });
                        $('#lok_periode_start').jqxDateTimeInput({value:new Date(year_start, month_start, day_start)});
        
                        $('#lok_periode_end').jqxDateTimeInput({ 
                            width: '110px',
                            height: '35px',  
                            formatString:'yyyy-MM-dd',
                            theme:'arctic',
                            min: new Date(year_start, month_start, day_start)
                        });
                        $('#lok_periode_end').jqxDateTimeInput({value:new Date(year_end, month_end, day_end)});
        
                        
                    });
                });
            });
            function jvSave(validate=false){
                validator
                .validate()
                .then(function(status){
                    if(status!='Invalid'){
                        if($('#lok_unitkerja').val()!=''){
                            $('#lok_unitkerja_combo').val($('#lok_unitkerja').val());
                            Swal.fire({ title:'Simpan Perubahan?', text: null, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak', 
                                confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                                cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                            }).then(result => { if(result.value) {document.formgw.submit()} });
                        }else{
                            swal.fire({title:'Unit Kerja tidak boleh Kosong!', icon:'error'});
                        }
                    }
                })
            }            
        </script>
        ";
        return $content;
    }
    function save(){
        $lok_idents = $this->input->post("lok_idents");
        $lok_unitkerja = $this->input->post("lok_unitkerja");
        $arrInput = array(
            "lok_asmidents",
            "lok_unitkerja",
            "lok_periode_start",
            "lok_periode_end",
            "lok_keterangan",
        );
        foreach($_POST as $keypost=>$valuepost){
            ${$keypost} = $valuepost;
            foreach($arrInput as $keyInput){
                if($keypost==$keyInput){
                    $input[$keypost] = $valuepost;
                    break;
                }
            }
        }
        if($hidTRNSKS=="add"){
            $input["lok_usrnam"] = $this->username;
        }else{
            $input["lok_updnam"] = $this->username;
            $input["lok_upddat"] = $this->datesave;
        }

        // debug_array($input);
        $url = "/asesmen/unitkerja";
        
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("lok_idents"=>$lok_idents)));
        $this->crud->useTable($this->table);
        if(!isset($lok_unitkerja_combo)){
            $lok_unitkerja_combo = $lok_unitkerja;
        }
        $lok_unitkerja = explode(",", $lok_unitkerja_combo);
        if(is_array($lok_unitkerja)){
            $count = count($lok_unitkerja);
            $loop = 1;
            foreach($lok_unitkerja as $uker){
                unset($input["lok_unitkerja"]);
                $input["lok_unitkerja"] = $uker;
                if(!$this->crud->save($input, array("lok_idents"=>$lok_idents))){
                    $this->common->message_save('save_gagal',null, $url);
                }else{
                    if($loop==$count){
                        $this->common->message_save('save_sukses',null, $url);
                    }
                }
                $loop++;
            }
        }else{
            $input["lok_unitkerja"] = $lok_unitkerja;
            // debug_array($input);
            if(!$this->crud->save($input, array("lok_idents"=>$lok_idents))){
                $this->common->message_save('save_gagal',null, $url);
            }else{
                $this->common->message_save('save_sukses',null, $url);
            }
        }

    }
    function savedata($param){
        foreach($param as $key=>$value){
            ${$key} = $value;
        }
    }
    function delete(){
        $lok_idents = $this->input->post("lok_idents");
        $lok_alasan = $this->input->post("catatan");
        $input["lok_is_deleted"] = 1;
        $input["lok_alasan"] = $lok_alasan;

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$lok_idents,
            "log_action"=>array("lok_idents"=>$lok_idents, "action"=>"Hapus Asesmen Unit Kerja")
        );
        $this->common->logmodul(false, $arrModul);        
        $this->crud->useTable($this->table);
        if($this->crud->save($input, array("lok_idents"=>$lok_idents))){
            echo "berhasil";
        }else{
            echo "gagal";
        }
    }    
}
?>
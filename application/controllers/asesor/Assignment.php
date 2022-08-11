<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Assignment extends MY_Controller {
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
        $this->table = "t_asm_asesor";
    }	
	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Asesor"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listAsesor(),'admin',$bc);  	 
	}
    public function listAsesor(){
        $gridname = "jqxLokasi";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Assignment/list/m_asesmen';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'ase_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'ase_asesor','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>60, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"idk_nama", "aw"=>150, "label"=>"Kategori","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode", "aw"=>80,  "label"=>"Periode", "group"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_start", "aw"=>80,  "label"=>"Mulai", "group"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_end", "aw"=>80,    "label"=>"Selesai", "group"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"USR_FNAMES", "aw"=>200,  "label"=>"Nama", 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'ase_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'ase_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $jvDelete = "
            function jvDelete(data_row){
                ase_idents = data_row['ase_idents'];
                asm_tahun = data_row['asm_tahun'];
                ase_asesor_desc = data_row['USR_FNAMES'];
                asm_periode = data_row['asm_periode'];

                swal.fire({ 
                    title:'".$this->lang->line("confirm_delete")." Asesor ' + ase_asesor_desc + ' untuk Asesmen Tahun ' + asm_tahun +', periode ' + asm_periode + '?', 
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
                        prm['ase_idents'] = ase_idents;
                        prm['catatan'] = alasan;                                                                
                        $.post('/asesor/assignment/delete',prm,function(rebound){
                            if(rebound){
                                swal.fire('Data Asesor ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
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
            'modul'=>'asesor/assignment'
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
            array('link'=>'/asesor/assignment','text'=>"Daftar Asesor"),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function edit($type=null,$param=null,$source=null){
        $column = null;
        $button = null;
        $readonly = false;
        $lok_asesor = null;
        $tags = true;
        $multiple = true;
        $special=true;
        $ase_idents = null;
        if($type!="add"){
            $column = $this->m_asesmen->getAssignment_edit($param);
            $ase_idents = $column->ase_idents;
            $ase_asesor = $column->ase_asesor;
            $tags = false;
            $multiple = false;
            $special=false;
            if($type=="view"){
                $readonly = true;
            }            
        }else{
            $ase_idents = null;
        }
        $this->db->where("IFNULL(USR_ACTIVE,0) <> 1");
        $field = array("USR_IDENTS", "USR_FNAMES");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_usrapp",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            "empty"=>true
        );

        $optPengguna = $this->crud->getGeneral_combo($arrayOpt);

        // $field = array("asm_idents", "asm_tahun");
        $field = array("asm_idents", array("asm_periode"=>array("asm_tahun", "asm_periode")));
        $this->db->where("IFNULL(asm_is_deleted,0) <> 1");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            // "empty"=>true
            "empty"=>true
        );
        $optAsesmen = $this->crud->getGeneral_combo($arrayOpt);

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
            "ase_idents"=>array("label"=>"ID","type"=>"hid","value"=>$param),
            "ase_asmidents"=>array("group"=>1, "label"=>"Tahun Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesmen tidak boleh kosong"), "cascade"=>array("param_cascade"=>"asm_tahun", "param_cascade_other"=> array("asm_idents"=>$ase_idents), "url_cascade"=>"/asesmen/nosj/getKategoriAsesmen_json","next_cascade"=>"ase_kategori"), "readonly"=>$readonly),
            "ase_periode_start"=>array("group"=>1, "label"=>"Periode Mulai","type"=>"dat", "readonly"=>$readonly),
            "ase_periode_end"=>array("group"=>1, "label"=>"Periode Berakhir","type"=>"dat", "readonly"=>$readonly),
            "ase_asesor"=>array("group"=>1, "label"=>"Asesor","type"=>"cmb","option"=>$optPengguna, "size"=>"400px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesor tidak boleh kosong","special"=>$special, "tags"=>$tags), "readonly"=>$readonly),
            "ase_kategori"=>array("group"=>1, "label"=>"Kategori","type"=>"cmb","option"=>$optCategories, 'tags'=>$tags, 'multiple'=>$multiple, "size"=>"400px", "validation"=>array("validation"=>"notempty","special"=>$special, "tags"=>$tags, "message"=>"Kategori tidak boleh kosong"), "readonly"=>$readonly),
            "ase_keterangan"=>array("group"=>1, "label"=>"Keterangan","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'), "readonly"=>$readonly),
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
					'modul' => '/asesor/assignment/save',
                    'formcommand' => '/asesor/assignment/save',
                    'tabname'=> array(
                        '1'=>'fas fa-user-tie^Data Asesor', 
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
                // $('#ase_asmidents').on('select2:select', function (e) {
                //     console.log(e);
                //     var data = e.params.data;
                //     var keydata = data.id;
                //     var param = {};
                //     param['asm_idents'] = keydata;
                //     $('#imgPROSES').show();
                //     $('#windowProses').jqxWindow('open');
                //     $.post('/asesmen/asesmen/json', param,function(jsonreturn){
                //         $('#windowProses').jqxWindow('close');
                //         var returnjson = JSON.parse(jsonreturn);
                //         $('#ase_periode_start').val(returnjson.asm_periode_start)
                //         $('#ase_periode_end').val(returnjson.asm_periode_end)
                //     });
                // });

                $('#ase_asmidents').on('select2:select', function (e) {
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

                        $('#ase_periode_start').jqxDateTimeInput({ 
                            width: '110px',
                            height: '35px',  
                            formatString:'yyyy-MM-dd',
                            theme:'arctic',
                            min: new Date(year_start, month_start, day_start)
                        });
                        $('#ase_periode_start').jqxDateTimeInput({value:new Date(year_start, month_start, day_start)});
        
                        $('#ase_periode_end').jqxDateTimeInput({ 
                            width: '110px',
                            height: '35px',  
                            formatString:'yyyy-MM-dd',
                            theme:'arctic',
                            min: new Date(year_start, month_start, day_start)
                        });
                        $('#ase_periode_end').jqxDateTimeInput({value:new Date(year_end, month_end, day_end)});
        
                        
                    });
                });                
            });
            function jvSave(){
                validator
                .validate()
                .then(function(status){
                    if(status!='Invalid'){
                        $('#ase_kategori_combo').val($('#ase_kategori').val());
                        Swal.fire({ 
                            title:'Simpan Perubahan?', 
                            text: null, 
                            icon: 'question', 
                            showCancelButton: true, 
                            confirmButtonText: 'Ya', 
                            cancelButtonText: 'Tidak', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'  
                        }).then(result => { if(result.value) {document.formgw.submit()} });
                    }
                })
            }            
        </script>
        ";
        return $content;
    }
    function save(){
        // $this->common->debug_post();
        $ase_idents = $this->input->post("ase_idents");
        $ase_asesor = $this->input->post("ase_asesor");
        $arrInput = array(
            "ase_asmidents",
            "ase_asesor",
            "ase_periode_start",
            "ase_periode_end",
            "ase_keterangan",
            "ase_kategori"
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
            $input["ase_usrnam"] = $this->username;
        }else{
            $input["ase_updnam"] = $this->username;
            $input["ase_upddat"] = $this->datesave;
        }

        $url = "/asesor/assignment";
        
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("ase_idents"=>$ase_idents)));
        $this->crud->useTable($this->table);
        // debug_array($ase_asesor_combo);
        if(!isset($ase_kategori_combo)){
            $ase_kategori_combo = $ase_kategori;
        }
        // ase_kategori_combo
        $ase_kategori = explode(",", $ase_kategori_combo);
        if(is_array($ase_kategori)){
            $count = count($ase_kategori);
            // debug_array($ase_asesor);
            $loop = 1;
            foreach($ase_kategori as $kategori){
                unset($input["ase_kategori"]);
                $input["ase_kategori"] = $kategori;
                if(!$this->crud->save($input, array("ase_idents"=>$ase_idents))){
                    $this->common->message_save('save_gagal',null, $url);
                }else{
                    if($loop==$count){
                        $this->common->message_save('save_sukses',null, $url);
                    }
                }
                $loop++;
            }
        }else{
            $input["ase_kategori"] = $ase_kategori;
            if(!$this->crud->save($input, array("ase_idents"=>$ase_idents))){
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
        $ase_idents = $this->input->post("ase_idents");
        $lok_alasan = $this->input->post("catatan");
        $input["ase_is_deleted"] = 1;
        $input["ase_alasan"] = $lok_alasan;

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$ase_idents,
            "log_action"=>array("ase_idents"=>$ase_idents, "action"=>"Hapus Asesmen Unit Kerja")
        );
        $this->common->logmodul(false, $arrModul);        
        $this->crud->useTable($this->table);
        if($this->crud->save($input, array("ase_idents"=>$ase_idents))){
            echo "berhasil";
        }else{
            echo "gagal";
        }
    }    
}
?>
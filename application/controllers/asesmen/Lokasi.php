<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Lokasi extends MY_Controller {
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
        $this->table = "t_asm_lokasi";
    }	
	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Lokasi"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listLokasi(),'admin',$bc);  	 
	}
    public function listLokasi(){
        $gridname = "jqxLokasi";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Lokasi/list/m_asesmen';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_nomor", "aw"=>"10%", "label"=>"No. Asesmen","adtype"=>"text");
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_nama", "aw"=>"20%", "label"=>"Nama Asesmen","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_start", "aw"=>80,  "label"=>"Mulai", "group"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_end", "aw"=>80,    "label"=>"Selesai", "group"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"PRV_NAMESS", "aw"=>200,  "label"=>"Provinsi", 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"KAB_NAMESS", "aw"=>250,  "label"=>"Kabupaten/Kota", 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lok_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $jvDelete = "
            function jvDelete(data_row){
                lok_idents = data_row['lok_idents'];
                asm_tahun = data_row['asm_tahun'];

                lokasi = 'Tahun ' + asm_tahun + ' untuk Kab/Kota ' + KAB_NAMESS + '/'+PRV_NAMESS;
                swal.fire({ 
                    title:'".$this->lang->line("confirm_delete")." Lokasi Asesmen ' + lokasi + '?', 
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
                        $.post('/asesmen/lokasi/delete',prm,function(rebound){
                            if(rebound){
                                swal.fire('Data Lokasi ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
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
            'modul'=>'asesmen/lokasi'
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
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/asesmen/lokasi','text'=>"Daftar Lokasi"),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function edit($type=null,$param=null,$source=null){
        $column = null;
        $button = null;
        $readonly = false;
        $lok_provinsi = null;
        $lok_kabptn = null;
        $tags = true;
        $multiple = true;
        $lok_idents = null;
        if($type!="add"){
            $column = $this->m_asesmen->getLokasi_edit($param);
            $lok_idents = $column->lok_idents;
            $lok_provinsi = $column->lok_provinsi;
            $lok_kabptn = $column->lok_kabptn;
            $tags = false;
            $multiple = false;

            if($type=="view"){
                $readonly = true;
            }            
        }

        $asm_year = '2021';
        $rslKabptn = $this->m_asesmen->getLokasi_assigned(1, $asm_year, $lok_idents);

        foreach($rslKabptn->result() as $keyI=>$valueI){
            $notinKabptn[] = $valueI->lok_kabptn;
        }
        if(isset($notinKabptn)){
            $this->db->where_not_in("KAB_IDENTS", $notinKabptn);
        }
        
        $field = array("asm_idents", "asm_tahun");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            "empty"=>FALSE
        );
        
        $optAsesmen = $this->crud->getGeneral_combo($arrayOpt);
        // $this->common->debug_sql(1);

        $arrField = array(
            "lok_idents"=>array("label"=>"ID","type"=>"hid","value"=>$param),
            "lok_asmidents"=>array("group"=>1, "label"=>"Tahun Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesmen tidak boleh kosong"), "readonly"=>$readonly),
            "lok_periode_start"=>array("group"=>1, "label"=>"Periode Mulai","type"=>"dat", "readonly"=>$readonly),
            "lok_periode_end"=>array("group"=>1, "label"=>"Periode Berakhir","type"=>"dat", "readonly"=>$readonly),
            "lok_provinsi"=>array("group"=>1, "label"=>"Provinsi","type"=>"cmb","option"=>$optProvince, "size"=>"200px", "cascade"=>array("param_cascade"=>"prv_idents", "param_cascade_other"=> array("asm_idents"=>"$('#lok_asmidents').val()"),"url_cascade"=>"/master/nosj/getKabupatenlokasi_json","next_cascade"=>"lok_kabptn"), "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Provinsi tidak boleh kosong"), "readonly"=>$readonly),
            "lok_kabptn"=>array("group"=>1, "label"=>"Kabupaten","type"=>"cmb","option"=>$optDistrict, "size"=>"400px", 'tags'=>$tags, 'multiple'=>$multiple, 'value'=> $lok_kabptn, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Kabupaten tidak boleh kosong","special"=>true, "tags"=>true), "readonly"=>$readonly),
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
					'modul' => '/asesmen/lokasi/save',
                    'formcommand' => '/asesmen/lokasi/save',
                    'tabname'=> array(
                        '1'=>'fas fa-map^Data Lokasi', 
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
                        $('#lok_periode_start').val(returnjson.asm_periode_start)
                        $('#lok_periode_end').val(returnjson.asm_periode_end)
                    });
                });
            });
            function jvSave(validate=false){
                validator
                .validate()
                .then(function(status){
                    if(status!='Invalid'){
                        if($('#lok_kabptn').val()!=''){
                            Swal.fire({ title:'Simpan Perubahan?', text: null, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak', confirmButtonColor: 'btn-success', cancelButtonColor: 'btn-danger'}).then(result => { if(result.value) {document.formgw.submit()} });
                        }else{
                            swal.fire({title:'Kabupaten tidak boleh Kosong!', icon:'error'});
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
        $lok_kabptn = $this->input->post("lok_kabptn");
        $arrInput = array(
            "lok_asmidents",
            "lok_provinsi",
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
        $url = "/asesmen/lokasi";

        
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("lok_idents"=>$lok_idents)));
        $this->crud->useTable($this->table);
        if(is_array($lok_kabptn)){
            $count = count($lok_kabptn);
            $loop = 1;
            foreach($lok_kabptn as $kabkota){
                unset($input["lok_kabptn"]);
                $input["lok_kabptn"] = $kabkota;
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
            $input["lok_kabptn"] = $lok_kabptn;
            // debug_array($input);
            if(!$this->crud->save($input, array("lok_idents"=>$lok_idents))){
                $this->common->message_save('save_gagal',null, $url);
            }else{
                $this->common->message_save('save_sukses',null, $url);
            }

        }
        // debug_array($count);
        // if($count>0){
        // }

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
            "log_action"=>array("lok_idents"=>$lok_idents, "action"=>"Hapus Lokasi")
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
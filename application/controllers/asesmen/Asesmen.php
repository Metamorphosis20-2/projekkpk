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
        $this->table = "t_asm_asesmen";
        $this->table_detail = "t_asm_asesmen_process_area";
    }	
	public function index(){
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
        $url ='/nosj/getNosj_list/Asesmen/list/m_asesmen';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'asm_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'asm_totaldata','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode", "aw"=>40, "label"=>"Periode","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_start", "aw"=>80,  "label"=>$this->start, "group"=>"Tanggal","adtype"=>"text","adtype"=>"date", 'aw'=>80,'cf'=>'dd-MM-yyyy','adtype'=>'date');
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_end", "aw"=>80,    "label"=>$this->end, "group"=>"Tanggal","adtype"=>"text","adtype"=>"date", 'aw'=>80,'cf'=>'dd-MM-yyyy','adtype'=>'date');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'asm_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'asm_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');
        
        $jvDelete = "
            function jvDelete(data_row){
                id = data_row['asm_idents'];
                asm_tahun = data_row['asm_tahun'];
                asm_totaldata = data_row['asm_totaldata'];
                if(asm_totaldata==0){
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
                                    // jQuery.noConflict();
                                    $('#" . $gridname  . "').DataTable().ajax.reload();
                                }
                            });
                        }
                    });
                }else{
                    swal.fire({
                        title:'Asesmen ' + asm_tahun + ' " . $this->lang->line("confirm_delete_restrict"). "', 
                        text: 'Data sudah digunakan', 
                        icon: 'error'
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
            'sumber'=>'server',
            'modul'=>'asesmen/asesmen'
        ));
        //====== end of grid
        $content .="
        <script>
            function jvEdit(data_row){
                id = data_row['asm_idents'];
                asm_totaldata = data_row['asm_totaldata'];
                asm_tahun = data_row['asm_tahun'];
                if(id=='' || id==null){
                    swal.fire({ title:'Pilih Data!', text: null, icon: 'warning', timer: 4000});
                }else{
                    action = 'edit';
                    if(asm_totaldata>0){
                        action = 'view';
                        swal.fire({ 
                            title:'Asesmen ' + asm_tahun + ' sudah direferensikan oleh data yang lain',
                            text:'Data Tidak bisa diedit'
                        })
                        $('#frmGrid').attr('action', '/'+action+'/asesmen/asesmen');
                        $('#grdIDENTS').val(id);
                        document.frmGrid.submit();                        
                    }else{
                        $('#frmGrid').attr('action', '/'+action+'/asesmen/asesmen');
                        $('#grdIDENTS').val(id);
                        document.frmGrid.submit();                        
                    }
                }
            }
        </script>
        ";
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
            array('link'=>'/asesmen/asesmen','text'=>"Jadwal Asesmen"),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function edit($type=null,$param=null,$source=null){
        $column = null;
        $button = null;
        $readonly = false;

        $start = date('Y') - 1;
        $ended = date('Y') + 2;

        // $tahun_exist = $this->m_asesmen->getAsesmen_year($param);

        for($e=$start;$e<=$ended;$e++){
            $optTAHUNS[$e] = $e;
        }
        if(isset($tahun_exist)){
            // 
            if($tahun_exist->num_rows()>0){
                foreach($tahun_exist->result() as $key=>$value){
                    $asm_tahun_exist = $value->asm_tahun;
                    unset($optTAHUNS[$asm_tahun_exist]);
                }
            }
        }
        $readonly = false;
        if($type!="add"){
            $column = $this->m_asesmen->getAsesmen_edit($param);
            $asm_tahun  = $column->asm_tahun;
            $asm_periode_start = $column->asm_periode_start;
            $asm_periode_end = $column->asm_periode_end;
            if($type=="view"){
                $readonly = true;
            }
            $colum_detail = $this->m_asesmen->getAsesmendetail_edit($param);
            if($colum_detail->num_rows()>0){
                foreach($colum_detail->result() as $keydetail=>$valuedetail){
                    $par_process_area = $valuedetail->par_process_area;
                    $par_process_area_name = $valuedetail->par_process_area_name;
                    $par_kategori_name = $valuedetail->par_kategori_name;
                    $process_name = $par_kategori_name . " / " .$par_process_area_name;
                    $source[] = array("id"=>$par_process_area, "text"=>$process_name);
                }
            }
        }else{
            $asm_tahun = $optTAHUNS[$e-1];
            $date_2 = $asm_tahun . "-01-01";
            $date_2 = date_create($date_2);
            date_add($date_2,date_interval_create_from_date_string("1 year"));
            $asm_periode_start = date_format($date_2,"Y-m-d");
            $asm_periode_end = date_format($date_2,"Y-m-d");
        }
        // debug_array($source);
        $optPeriode = array("I"=>"I","II"=>"II","III"=>"III","IV"=>"IV");

        $this->db->select("idk_idents id_parent, idk_nama nama_parent");
        $this->db->from("t_mas_kategori");
        $this->db->where("idk_parent = 0");
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");

        $sqlParent = $this->db->get_compiled_select();
        
        $field = array("idk_idents", "idk_nama");
        $field = array("idk_idents", array("idk_nama"=>array("nama_parent", "idk_nama")));
        $this->db->join("(" . $sqlParent . ") as b", "a.idk_parent = b.id_parent", "INNER");
        $this->db->where("a.idk_parent <> 0");
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");
        $this->db->order_by("idk_type, idk_parent, idk_idents");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_kategori a",
            "field"=> $field,
            "protected"=>true,
            "separator"=>"/",
            "tags"=>true,
            "multiple"=>true,
            "empty"=>false
        );
        
        $optProcess = $this->crud->getGeneral_combo($arrayOpt);
        $arr_periode_start = array("group"=>1, "label"=>"Periode Mulai","type"=>"dat","value"=>$asm_periode_start, "readonly"=>$readonly);
        if(!$this->backdate){
            $arr_periode_start = array_merge($arr_periode_start, array("min"=>Date('Y-m-d')));
        }
        // $this->common->debug_sql(1);
        $tags_multiple = true;
        $arrField = array(
            "asm_idents"=>array("label"=>"ID","type"=>"hid","value"=>$param),
            "asm_tahun"=>array("group"=>1, "label"=>"Tahun Penilaian","type"=>"cmb", "option"=>$optTAHUNS, "value"=>$asm_tahun, "readonly"=>$readonly),
            "asm_periode"=>array("group"=>1, "label"=>"Periode","type"=>"cmb","option"=>$optPeriode, "size"=>"20%", "readonly"=>$readonly),
            "asm_periode_start"=>$arr_periode_start,
            "asm_periode_end"=>array("group"=>1, "label"=>"Periode Berakhir","type"=>"dat","value"=>$asm_periode_end, "readonly"=>$readonly),
            "asm_indikator"=>array("group"=>1, "label"=>"Process Area","type"=>"cmb","option"=>$optProcess,"multiple"=>$tags_multiple, "tags"=>$tags_multiple, "value"=>$source, "size"=>"90%", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Process Area tidak boleh kosong"), "readonly"=>$readonly),
            "asm_keterangan"=>array("group"=>1, "label"=>"Keterangan","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'), "readonly"=>$readonly, "validation"=>array("validation"=>"ckeditorempty", "message"=>"Keterangan tidak boleh kosong")),
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
					'modul' => '/asesmen/asesmen/save',
                    'formcommand' => '/asesmen/asesmen/save',
                    'tabname'=> array(
                        '1'=>'fas fa-check-circle^Data Asesmen', 
                    )
                );

        $button = null;
        if($type!="view"){
            $button = createButton(null, true, true, true);
        }
        $content = generateForm($arrForm);
        $content .= $button;
        $content .= "
        <script>
        $(document).ready(function () {
            $('#asm_tahun').on('select2:select', function (e) {
                var date_now = '".Date('Y-m-d')."';
                var data = e.params.data;
                id_tahun = data.id;
                id_tahun = parseInt(id_tahun) + 1;
                var arr_now = date_now.split('-');
                now_year = arr_now[0];
                now_mon = arr_now[1];
                now_day = arr_now[2];

                month_now = 0;
                day_now = 1;
                
                if(id_tahun == now_year){
                    month_now = parseInt(now_mon)-1;
                    day_now = parseInt(now_day);
                }
                $('#asm_periode_start').jqxDateTimeInput({ 
                    width: '110px',
                    height: '35px',  
                    formatString:'yyyy-MM-dd',
                    theme:'arctic',
                    min: new Date(id_tahun, month_now, day_now)
                });
                $('#asm_periode_start').jqxDateTimeInput({value:new Date(id_tahun, month_now, day_now)});

                $('#asm_periode_end').jqxDateTimeInput({ 
                    width: '110px',
                    height: '35px',  
                    formatString:'yyyy-MM-dd',
                    theme:'arctic',
                    min: new Date(id_tahun, month_now, day_now)
                });
                $('#asm_periode_end').jqxDateTimeInput({value:new Date(id_tahun, month_now, day_now)});
            });
        });

        function jvSave(validate=false){
            // alert(validate);
            if(validate){
                validator
                .validate()
                .then(function(status){
                    if(status!='Invalid'){
                        $('#asm_indikator_combo').val($('#asm_indikator').val());
                        $('#asm_indikator_combo').val($('#ase_kategori').val());
                        Swal.fire({ title:'Simpan Perubahan?', text: null, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'                            
                        }).then(result => { if(result.value) {document.formgw.submit()} });				}
                })
            }else{
                Swal.fire({ title:'Simpan Perubahan?', text: null, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Tidak', 
                    confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                    cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                }).then(result => { if(result.value) {document.formgw.submit()} });		}
        }
        </script>
        ";
        return $content;
    }
    function save(){
        // $this->common->debug_post();
        $arrInput = array(
            "asm_idents",
            // "asm_nomor",
            // "asm_nama",
            "asm_tahun",
            "asm_periode",
            "asm_periode_start",
            "asm_periode_end",
            "asm_keterangan",
            "asm_indikator",
            "asm_indikator_combo"
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
        $pk = array("asm_idents"=>$asm_idents);
        if($hidTRNSKS=="add"){
            $input["asm_usrnam"] = $this->username;
        }else{
            $input["asm_updnam"] = $this->username;
            $input["asm_upddat"] = $this->datesave;
            $this->crud->useTable($this->table_detail);
            $this->crud->delete(array("par_asmidents"=>$asm_idents));
        }

        // debug_array($input);
        $url = "/asesmen/asesmen";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("asm_idents"=>$asm_idents)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, $pk)){            
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $asm_indikator_combo = explode(",", $asm_indikator_combo);
            $asm_idents = ($asm_idents=="" ? $this->crud->__insertID : $asm_idents);
            if(is_array($asm_indikator_combo)){
                $count = count($asm_indikator_combo);
                $loop = 1;
                foreach($asm_indikator_combo as $process_area){
                    // unset($inputdetail["par_process_area"]);
                    $inputdetail["par_asmidents"] = $asm_idents;
                    $inputdetail["par_process_area"] = $process_area;
                    $this->crud->useTable($this->table_detail);
                    if(!$this->crud->save($inputdetail)){
                        $this->common->message_save('save_gagal',null, $url);
                    }else{
                        if($loop==$count){
                            $this->common->message_save('save_sukses',null, $url);
                        }
                    }
                    $loop++;
                }
            }            
            // $this->common->message_save('save_sukses',null, $url);
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
            "log_action"=>array("asm_idents"=>$asm_idents, "action"=>"Hapus Asesmen")
        );
        $this->common->logmodul(false, $arrModul);        
        $this->crud->useTable($this->table);
        if($this->crud->save($input, array("asm_idents"=>$asm_idents))){
            echo "berhasil";
        }else{
            echo "gagal";
        }
    }
    function json(){
        $asm_idents = $this->input->post("asm_idents");
        $result = $this->m_asesmen->getAsesmen_edit($asm_idents);
        $return = array();
        if($result!=null){
            $return = $result;
        }

        echo json_encode($return);
    }
}
?>
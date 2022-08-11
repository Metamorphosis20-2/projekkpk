<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Pertanyaan extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
    	$this->load->helper('ginput');
    	$this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->table = "t_mas_grant";
        $this->notes = $this->lang->line("notes");
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");
        $this->notes = $this->lang->line("notes");
        $this->start = $this->lang->line("start");
        $this->end = $this->lang->line("end");
        $this->valid = $this->lang->line("valid");
        $this->modul = $this->router->fetch_class();
        $this->table = "t_mas_pertanyaan";
    }	
	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Pertanyaan"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listPertanyan(),'admin',$bc);  	 
	}

    public function listPertanyan(){
        $this->load->helper(array('jqxgrid'));
        $gridname = "jqxTingkat";
        $url ="/master/nosj/getPertanyaantree_list/0";
        $col[] = array('lsturut'=>1, 'namanya'=>'tny_idents','ah'=>true,'label'=>'ID','ac'=>false);
        $col[] = array('lsturut'=>2, 'namanya'=>'tny_pertanyaan','aw'=>'57%','label'=>'Pertanyaan');
        $col[] = array('lsturut'=>3, 'namanya'=>'tny_kriteria_desc','aw'=>'31%','label'=>'Kriteria');
        // $col[] = array('lsturut'=>3, 'namanya'=>'tny_metode_desc','aw'=>'8%','label'=>'Metode');
        $col[] = array('lsturut'=>6, 'namanya'=>'tny_usrnam','aw'=>'150','label'=>'Pembuat');
        $col[] = array('lsturut'=>7, 'namanya'=>'tny_usrdat','aw'=>'160','label'=>'Tanggal Buat');
        $col[] = array('lsturut'=>8, 'namanya'=>'tny_parent','aw'=>100,'label'=>'Parent','ah'=>true);
        $col[] = array('lsturut'=>8, 'namanya'=>'rowlevel','aw'=>100,'label'=>'Level','ah'=>true);
        $col[] = array('lsturut'=>8, 'namanya'=>'urutan','aw'=>100,'label'=>'Level','ah'=>true);        
        $col[] = array('lsturut'=>8, 'namanya'=>'tny_pertanyaan_desc','aw'=>100,'label'=>'Level','ah'=>true);

        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);

        $jvEdit = "
        function jvEdit(){
            var selectedrowindex = $(\"#" . $gridname ."\").jqxTreeGrid('getSelection');
            if(selectedrowindex==''){
                swal.fire({title:'Pilih Data', type:'error'});
            }else{
                var row = selectedrowindex[0];
                var rowlevel = row.rowlevel;
                if(rowlevel!=4){
                    swal.fire({title:'Hanya bisa edit data pertanyaan', type:'error'});
                }else{			
                    var id = row.tny_idents;
                    $('#frmGrid').attr('action', '/edit/master/pertanyaan');
                    $('#grdIDENTS').val(id);
                    document.frmGrid.submit();
                }
            }
        }";
        $jvView = "
        function jvView(){
            var selectedrowindex = $(\"#" . $gridname ."\").jqxTreeGrid('getSelection');
            if(selectedrowindex==''){
                swal.fire({title:'Pilih Data', type:'error'});
            }else{
                var row = selectedrowindex[0];
                var rowlevel = row.rowlevel;
                if(rowlevel!=4){
                    swal.fire({title:'Hanya bisa lihat data pertanyaan', type:'error'});
                }else{			
                    var id = row.tny_idents;
                    $('#frmGrid').attr('action', '/view/master/pertanyaan');
                    $('#grdIDENTS').val(id);
                    document.frmGrid.submit();
                }
            }
        }";

        $jvDelete = "
            function jvDelete(){
                var selectedrowindex = $(\"#" . $gridname ."\").jqxTreeGrid('getSelection');
                if(selectedrowindex==''){
                    swal.fire({title:'Pilih Data', type:'error'});
                }else{
                    var row = selectedrowindex[0];
                    var rowlevel = row.rowlevel;
                    if(rowlevel!=4){
                        swal.fire({title:'Hanya bisa hapus data pertanyaan', type:'error'});
                    }else{
                        var id = row.tny_idents;
                        var tny_pertanyaan = row.tny_pertanyaan;
                        
                        swal.fire({ 
                            title:'".$this->lang->line("confirm_delete")." Pertanyaan : ' + tny_pertanyaan.substring(0,100) + '... ?', 
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
                                $('#imgPROSES').show();
                                $(\"#windowProses\").jqxWindow('open');
                                var alasan = $('input[placeholder=\'".$this->lang->line("confirm_reason")."\']').val();
                                var prm = {};
                                prm['tny_idents'] = id;
                                prm['tny_alasan'] = alasan;                                                                
                                $.post('/master/pertanyaan/delete',prm,function(rebound){
                                    if(rebound){
                                        swal.fire('Data Pertanyaan ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                        $('#windowProses').jqxWindow('close');
                                        $('#".$gridname."').jqxTreeGrid('updateBoundData');
                                        $('#".$gridname."').jqxTreeGrid('expandAll');
                                    }
                                });
                            }
                        });
        
                    }
                }
            }
        ";        
        $jvDeleteX = "
        function jvDelete(){
            var selectedrowindex = $(\"#" . $gridname ."\").jqxTreeGrid('getSelection');
            if(selectedrowindex==''){
                swal({title:'Pilih Data', type:'error'});
            }else{
                var row = selectedrowindex[0];
                var rowlevel = row.level;
                var id = row.lvl_idents;
                var lvl_kode = row.lvl_kode;
                var lvl_nama = row.lvl_nama;
                var lvl_rowlevel = row.rowlevel;
                var parent = selectedrowindex[0].lvl_parent;

                var check = {};
                check['lvl_idents'] = id;
                check['lvl_rowlevel'] = lvl_rowlevel;
                $('#imgPROSES').show();
                $(\"#windowProses\").jqxWindow('open');

                $.post('/master/Tingkat/chkTingkat', check,function(data){
                    $('#windowProses').jqxWindow('close');
                    var checkdata = $.parseJSON(data);
                    num_rows = checkdata.found;
                    asesmen = checkdata.asesmen;
                    if(num_rows>0){
                        if(asesmen){
                            keterangan = 'sudah diinput nilai';
                        }else{
                            keterangan = 'mempunyai Kriteria';
                        }
                        swal.fire({title:'Data ' + keterangan, text:'Tingkat tidak bisa dihapus', type:'error'});
                    }else{
                        swal.fire({ 
                            title:'Hapus Tingkat?', 
                            text: lvl_nama, 
                            icon: 'question', 
                            showCancelButton: true, 
                            confirmButtonText: 'Ya', 
                            cancelButtonText: 'Tidak', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                        }).then(result => {
                            if(result.value) {
                                var param = {};
                                param['lvl_idents'] = id;
                                $('#imgPROSES').show();
                                $(\"#windowProses\").jqxWindow('open');
                                $.post('/master/Tingkat/delete', param,function(data){
                                    $('#windowProses').jqxWindow('close');
                                    kata = 'Data ' + data + ' dihapus';
                                    swal.fire({title:kata, icon:'success'});
                                    var tahun  = $('#cmbOPTION').val();
                                    var tmpS = $('#".$gridname."').jqxTreeGrid('source');
                                    console.log(tmpS._source.url);
                                    tmpS._source.url = '".$url."';
                                    $('#".$gridname."').jqxTreeGrid('updateBoundData');
                        
                                });
                            }
                        });

                    }

                });
            }
        }";
        $content = "
        <script>
        function jvChangeOption(){
            var tahun  = $('#cmbOPTION').val();
            var tmpS = $('#".$gridname."').jqxTreeGrid('source');
            console.log(tmpS._source.url);
            tmpS._source.url = '".$url."/'+tahun;
            $('#".$gridname."').jqxTreeGrid('updateBoundData');

        }          
        </script>
        ";
        $content .= gGrid(array( 'url'=>$url, 
                                'treegrid'=>true,
                                'keyfield'=>'tny_idents',
                                'keyparent'=>'tny_parent',
                                'gridname'=>$gridname,
                                'button'=>'standar',
                                'buttonotherposition'=>'first',
                                'width'=>'100%',
                                "height"=>"75vh",
                                'col'=>$col,
                                "jvEdit"=>$jvEdit,
                                "jvDelete"=>$jvDelete,
                                "jvView"=>$jvView,
                                'modul'=>'master/pertanyaan',
                                'sumber'=>'server',
                                'creategrid'=>false,
                                'expandAll'=>true,
                                'autoexpand'=>true
                            ));

        $content .="<div style='position:relative;top:0px;text-align:center'><div id=\"" . $gridname . "\"></div></div>";
        $content .= generateWindowjqx(array('window'=>'Tingkat','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        return $content;
    }    
    public function listPertanyan_datatables(){
        $gridname = "jqxPertanyan";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Pertanyaan/list/m_master';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tny_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"tny_kelompok_desc", "aw"=>"17%", "label"=>"Kategori","adtype"=>"text", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"tny_indikator_desc", "aw"=>"17%", "label"=>"Process Area","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"tny_level_desc", "aw"=>"12%", "label"=>"Level","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"tny_kriteria_desc", "aw"=>"30%", "label"=>"Kriteria","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"tny_pertanyaan", "aw"=>"35%", "label"=>"Pertanyaan","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tny_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tny_usrdat','aw'=>'150','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal('Pilih Data!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var tny_pertanyaan = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'tny_pertanyaan');
        ";
        
        $jvDelete = "
            function jvDelete(data_row){
                id = data_row['tny_idents'];
                tny_pertanyaan = data_row['tny_pertanyaan'];
                total_grant = 0;
                if(total_grant==0){
                    swal.fire({ 
                        title:'".$this->lang->line("confirm_delete")." Pertanyaan : ' + tny_pertanyaan.substring(0,100) + '... ?', 
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
                            prm['tny_idents'] = id;
                            prm['tny_alasan'] = alasan;                                                                
                            $.post('/master/pertanyaan/delete',prm,function(rebound){
                                if(rebound){
                                    swal.fire('Data Pertanyaan ' + rebound + ' ".$this->lang->line("confirm_deleted")."!')
                                    jQuery.noConflict();
                                    $('#" . $gridname  . "').DataTable().ajax.reload();
                                }
                            });
                        }
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
            'group_row'=>"tny_kelompok_desc",
            // "searchable"=>true,
            'button'=> 'standar',
            'jvDelete'=>$jvDelete,
            'sumber'=>'server',
            'modul'=>'master/pertanyaan',
            'autorowheight'=>true
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
            array('link'=>'/master/pertanyaan','text'=>"Daftar Pertanyaan"),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function edit($type=null,$param=null,$source=null){
        $column = null;
        $button = null;
        $readonly = false;
        $tny_kelompok = null;
        if($type!="add"){
            $column = $this->m_master->getPertanyaan_edit($param);
            $tny_kelompok = $column->tny_kelompok;
        }

        $field = array("idk_idents", "idk_nama");
        $this->db->where("idk_parent = 0");
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_kategori",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );

        $optCategories = $this->crud->getGeneral_combo($arrayOpt);

        $field = array("idk_idents", "idk_nama");
        $this->db->where("idk_parent <> 0");
        $this->db->where("IFNULL(idk_is_deleted,0) <> 1");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_kategori",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
        $optProcess = $this->crud->getGeneral_combo($arrayOpt);

        $field = array("lvl_idents", "lvl_nama");
        $this->db->where("lvl_parent = 0");
        $this->db->where("IFNULL(lvl_is_deleted,0) <> 1");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_level",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
        $optLevel = $this->crud->getGeneral_combo($arrayOpt);        

        $field = array("lvl_idents", "lvl_nama");
        $this->db->where("lvl_parent <> 0");
        $this->db->where("IFNULL(lvl_is_deleted,0) <> 1");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_level",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
        $optKriteria = $this->crud->getGeneral_combo($arrayOpt);
        for($x=1;$x<11;$x++){
            $optUrutan[$x] = $x;
        }

        $readonly = false;
        if($type=="view"){
            $readonly = true;
        }
        $opt_tny_metode = $this->crud->getCommon(3,10);
        $arrField = array(
            "tny_idents"=>array("label"=>"ID","type"=>"hid","value"=>$param),
            "tny_kelompok"=>array("group"=>1, "label"=>"Categories/Supporting Process","type"=>"cmb","option"=>$optCategories, "size"=>"300px", "cascade"=>array("param_cascade"=>"idk_idents","url_cascade"=>"/master/nosj/getKategori_json","next_cascade"=>"tny_indikator"),"validation"=>array("validation"=>"notzeroEmpty", "message"=>"Kelompok Kategori tidak boleh kosong"),"readonly"=>$readonly),
            "tny_indikator"=>array("group"=>1, "label"=>"Process Area","type"=>"cmb","option"=>$optProcess, "size"=>"90%", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Kategori tidak boleh kosong"),"readonly"=>$readonly),
            "tny_level"=>array("group"=>1, "label"=>"Level","type"=>"cmb","option"=>$optLevel, "size"=>"20%", "validation"=>array("validation"=>"notEmpty", "message"=>"Level tidak boleh kosong"), "cascade"=>array("param_cascade"=>"lvl_idents","url_cascade"=>"/master/nosj/getKriteria_json","next_cascade"=>"tny_kriteria", "param_cascade_other"=> array("lvl_indikator"=>"$('#tny_indikator').val()")),"readonly"=>$readonly),
            "tny_kriteria"=>array("group"=>1, "label"=>"Kriteria","type"=>"cmb","option"=>$optKriteria, "size"=>"90%", "validation"=>array("validation"=>"notEmpty", "message"=>"Kriteria tidak boleh kosong"),"readonly"=>$readonly),
            "tny_metode"=>array("group"=>1, "label"=>"Metode","type"=>"cmb","option"=>$opt_tny_metode, "size"=>"30%","readonly"=>$readonly),
            "tny_urutan"=>array("group"=>1, "label"=>"Urutan","type"=>"cmb","option"=>$optUrutan, "size"=>"10%","readonly"=>$readonly),
            "tny_pertanyaan"=>array("group"=>1, "label"=>"Pertanyaan","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'),"validation"=>array("validation"=>"ckeditorempty", "message"=>"Pertanyaan tidak boleh kosong"),"readonly"=>$readonly),
            "tny_petunjuk"=>array("group"=>1, "label"=>"Petunjuk","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'),"readonly"=>$readonly),
            "tny_parameter"=>array("group"=>1, "label"=>"Pertanyaan","type"=>"hid"),

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
                    'height'=>'100%',
					'modul' => '/master/pertanyaan/save',
                    'formcommand' => '/master/pertanyaan/save',
                    'tabname'=> array(
                        '1'=>'fas fa-check-circle^Data Pertanyaan', 
                    )
                );

        $button = null;
        if($type!="view"){
            $button = createButton(null, true, true, true);
        }
        $content = generateForm($arrForm);
        $content .= $button;
        
        return $content;
    }
    function generatepertanyaan(){
        // $this->common->get_post();
		$tny_kelompok = $this->input->post('tny_kelompok');
		$tny_indikator = $this->input->post('tny_indikator');
        $tny_parameter = $this->input->post('tny_parameter');

        $rslTemplate = $this->m_master->getPertanyaan_template();
        $jml_pertanyaan = $rslTemplate->num_rows();
        $loop = 0;
        foreach($rslTemplate->result() as $key=>$value){
            $tny_pertanyaan = $value->tmp_pertanyaan;
            $tny_petunjuk = $value->tmp_petunjuk;
            $tny_pertanyaan = str_replace("{nama categories/process area/supporting process}", $tny_parameter, $tny_pertanyaan);
            unset($input);
            $input["tny_kelompok"] = $tny_kelompok;
            $input["tny_indikator"] = $tny_indikator;
            $input["tny_petunjuk"] = $tny_petunjuk;
            $input["tny_pertanyaan"] = $tny_pertanyaan;
            $input["tny_usrnam"] = $this->username;
        
            $this->crud->useTable($this->table);
            if($this->crud->save($input)){
                $loop++;
            }
        }
        if($loop==$jml_pertanyaan){
            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"-", 
                "log_action"=>array("action"=>"Generate Pertanyaan")
            );
            $this->common->logmodul(false, $arrModul);
            $url = "/master/pertanyaan";
            $this->common->message_save('save_sukses',null, $url);
        }

    }
    function save(){
        // $this->common->debug_post();
        $arrInput = array(
            "tny_idents",
            "tny_kelompok",
            "tny_level",
            "tny_indikator",
            "tny_pertanyaan",
            "tny_petunjuk",
            "tny_urutan",
            "tny_kriteria",
            "tny_metode"
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
        if($tny_kelompok=="9999"){
            $input["tny_indikator"] = 9998;
        }
        if($hidTRNSKS=="add"){
            $input["tny_usrnam"] = $this->username;
        }else{
            $input["tny_updnam"] = $this->username;
            $input["tny_upddat"] = $this->datesave;
        }

        // debug_array($input);
        $url = "/master/pertanyaan";
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>array("tny_idents"=>$tny_idents)));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("tny_idents"=>$tny_idents))){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            $this->common->message_save('save_sukses',null, $url);
        }
    }
    function delete(){
        $tny_idents = $this->input->post("tny_idents");
        $tny_alasan = $this->input->post("tny_alasan");
        $input["tny_is_deleted"] = 1;
        $input["tny_alasan"] = $tny_alasan;
        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$tny_idents,
            "log_action"=>array("tny_idents"=>$tny_idents, "reason"=>$tny_alasan, "action"=>"Hapus Pertanyaan")
        );
        $this->common->logmodul(false, $arrModul);        
        $this->crud->useTable($this->table);
        if($this->crud->save($input, array("tny_idents"=>$tny_idents))){
            echo "berhasil";
        }else{
            echo "gagal";
        }
    }
    function checkPertanyaan(){
        $tny_indikator = $this->input->post("tny_indikator");

        $result = $this->m_master->checkPertanyaan($tny_indikator);

        echo $result->num_rows();
    }

}
?>
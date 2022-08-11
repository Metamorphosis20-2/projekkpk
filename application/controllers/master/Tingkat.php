<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tingkat extends MY_Controller {
    var $table;
    function __construct(){
        parent::__construct();
        $this->load->helper(array('ginput'));
        $this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->table = "t_mas_level";
    }   
    public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> 'Data Tingkat'),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listTingkat(),'admin',$bc);      

    }
    public function listTingkat(){
        $this->load->helper(array('jqxgrid'));
        $gridname = "jqxTingkat";
        $url ="/master/nosj/getTingkat_list/0";
        $col[] = array('lsturut'=>1, 'namanya'=>'lvl_idents','ah'=>true,'label'=>'ID','ac'=>false);
        $col[] = array('lsturut'=>3, 'namanya'=>'lvl_nama','aw'=>'55%','label'=>'Tingkat/Kriteria');
        $col[] = array('lsturut'=>4, 'namanya'=>'lvl_kelompok_desc','aw'=>'15%','label'=>'Categories');
        $col[] = array('lsturut'=>5, 'namanya'=>'lvl_indikator_desc','aw'=>'15%','label'=>'Process Area');
        $col[] = array('lsturut'=>6, 'namanya'=>'lvl_usrnam','aw'=>'150','label'=>'Pembuat');
        $col[] = array('lsturut'=>7, 'namanya'=>'lvl_usrdat','aw'=>'160','label'=>'Tanggal Buat');
        $col[] = array('lsturut'=>8, 'namanya'=>'lvl_parent','aw'=>100,'label'=>'Parent','ah'=>true);

        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);

        $oADD = strpos("N".$otorisasi,"A");
        $buttonother = null;
        if($oADD>0){
            $buttonother = array(
                "Tingkat"=>array('Print1','fa-plus','jvAddTingkat()','warning','80')
            );            
        }
        // debug_array($url);
        $jvAdd = "
        function jvAddTingkat(){
            swal.fire({ 
                title: 'Tambah Tingkat/Level?',
                text: null, 
                icon: 'question', 
                showCancelButton: true, 
                confirmButtonText: 'Ya', 
                cancelButtonText: 'Tidak', 
                confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                cancelButtonColor: '".$this->config->item("cancelButtonColor")."'  
            }).then(result => {
                if(result.value) {
                    var param = {};
                    param['type'] = 'add';
                    param['lvl_idents'] = 0;
                    param['lvl_parent'] = 0;
                    param['rowlevel'] = 0;
                    $('#jqwTingkat').jqxWindow('open');
                    $.post('/master/tingkat/edit', param,function(data){
                        var lebar = $(window).width() * 0.8;
                        $('#jqwTingkat').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'520px',position:'middle', resizable:false,title: 'Tambah Kelompok Tingkat', zIndex:'99999'});
                        $('#jqwTingkat').jqxWindow('setContent', data);
                    });  
                }
            }); 
        }
        function jvAdd(type){
            var rows = $('#" . $gridname ."').jqxTreeGrid('getRows');
            var noOfRows = 0;
            var traverseTree = function(rows)
            {
                for (var i = 0; i < rows.length; i++){
                    noOfRows += 1;
                    if (rows[i].records){
                        traverseTree(rows[i].records);
                    }
                }
                return noOfRows;
            };
            rowcount = traverseTree(rows);

            if(rowcount>0){
                var selectedrowindex = $(\"#" . $gridname ."\").jqxTreeGrid('getSelection');
                if(selectedrowindex==''){
                    var rowlevel = 0;
                    var id = null;
                    var lvl_kode = null;
                    var lvl_nama = null;
                    titlenya = 'Tambah Pertanyaan level 0?';
                    swal.fire({ 
                        title: 'Pilih Tingkat terlebih dahulu',
                        icon: 'info', 
                    });
                }else{
                    var row = selectedrowindex[0];
                    var rowlevel = row.level;
                    if(rowlevel==1){
                        swal.fire({ 
                            title:'Fitur ini tidak disupport',
                            text: null, 
                            icon: 'info', 
                        });    
                    }else{
                        var id = row.lvl_idents;
                        var lvl_nama = row.lvl_nama;
                        text_tambah = 'Kriteria';
                        titlenya = 'Tambah '+ text_tambah +' untuk ' + lvl_nama + '?';
                        swal.fire({ 
                            title:titlenya,
                            text: null, 
                            icon: 'question', 
                            showCancelButton: true, 
                            confirmButtonText: 'Ya', 
                            cancelButtonText: 'Tidak', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                        }).then(result => {
                            if(result.value) {
                                var param = {};
                                param['type'] = 'add';
                                param['lvl_idents'] = 0;
                                param['lvl_parent'] = id;
                                param['rowlevel'] = rowlevel;
                                $('#jqwTingkat').jqxWindow('open');
                                $.post('/master/tingkat/edit', param,function(data){
                                    var lebar = $(window).width() * 0.8;
                                    $('#jqwTingkat').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'420px',position:'middle', resizable:false,title: 'Tambah Tingkat', zIndex:'99999'});
                                    $('#jqwTingkat').jqxWindow('setContent', data);
                                });  
                            }
                        });
                    }
                }
            }else{
                // swal({title:'Buat Pertanyaan di Jadwal Asesmen Terlebih dahulu', type:'error'});
                var tahun  = $('#cmbOPTION').val();
                swal.fire({ 
                    title:'Buat Pertanyaan untuk ' + tahun + '?', 
                    icon: 'question', 
                    showCancelButton: true, 
                    confirmButtonText: 'Ya', 
                    cancelButtonText: 'Tidak', 
                    confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                    cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                }).then(result => {
                    if(result.value) {
                        var param = {};
                        $('#frmGrid').attr('action', '/master/Tingkat/tambah');
                        $('#grdIDENTS').val($('#cmbOPTION').val());
                        document.frmGrid.submit();
                    }
                });
            }
        }";
        $jvEdit = "
        function jvEdit(){
            var selectedrowindex = $(\"#" . $gridname ."\").jqxTreeGrid('getSelection');
            if(selectedrowindex==''){
                swal({title:'Pilih Data', type:'error'});
            }else{
                var row = selectedrowindex[0];
                var rowlevel = row.level;
                var id = row.lvl_idents;
                var lvl_kode = row.lvl_kode;
                var lvl_nama = row.lvl_nama;
                var parent = selectedrowindex[0].lvl_parent;
                if(parent==0){
                    height = '520px';
                    title = 'Tingkat';
                }else{
                    height = '420px';
                    title = 'Kriteria';
                }
                var param = {};
                param['type'] = 'edit';
                param['lvl_idents'] = id;
                param['lvl_tahun'] = $('#cmbOPTION').val();
                param['lvl_parent'] = parent;
                param['rowlevel'] = rowlevel;
                $('#jqwTingkat').jqxWindow('open');

                $.post('/master/Tingkat/edit', param,function(data){
                    var lebar = $(window).width() * 0.8;
                    $('#jqwTingkat').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:height,position:'middle', resizable:false,title: 'Ubah ' + title, zIndex:'99999'});
                    $('#jqwTingkat').jqxWindow('setContent', data);
                });
            }
        }";
        $jvView = "
        function jvView(){
            var selectedrowindex = $(\"#" . $gridname ."\").jqxTreeGrid('getSelection');
            if(selectedrowindex==''){
                swal({title:'Pilih Data', type:'error'});
            }else{
                var row = selectedrowindex[0];
                var rowlevel = row.level;
                var id = row.lvl_idents;
                var lvl_kode = row.lvl_kode;
                var lvl_nama = row.lvl_nama;
                var parent = selectedrowindex[0].lvl_parent;
                if(rowlevel==0){
                    height = '520px';
                    title = 'Tingkat';
                }else{
                    height = '420px';
                    title = 'Kriteria';
                }
                var param = {};
                param['type'] = 'view';
                param['lvl_idents'] = id;
                param['lvl_tahun'] = $('#cmbOPTION').val();
                param['lvl_parent'] = parent;
                param['rowlevel'] = rowlevel;
                $('#jqwTingkat').jqxWindow('open');

                $.post('/master/Tingkat/edit', param,function(data){
                    var lebar = $(window).width() * 0.8;
                    $('#jqwTingkat').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:height,position:'middle', resizable:false,title: 'Lihat ' + title, zIndex:'99999'});
                    $('#jqwTingkat').jqxWindow('setContent', data);
                });
            }
        }";
        $jvDelete = "
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

                        if(parent==0){
                            title = 'Tingkat';
                        }else{
                            title = 'Kriteria';
                        }                        
                        swal.fire({ 
                            title:'Hapus ' + title + '?', 
                            text: lvl_nama, 
                            input: 'text',
                            inputPlaceholder: '".$this->lang->line("confirm_reason")."',
                            inputValidator: (value) => {
                                if (!value) {
                                    return '<center>".$this->lang->line("confirm_reason")."</center>'
                                }
                            },
                            icon: 'question', 
                            showCancelButton: true, 
                            confirmButtonText: 'Ya', 
                            cancelButtonText: 'Tidak', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                        }).then(result => {
                            if(result.value) {
                                var alasan = $('input[placeholder=\'".$this->lang->line("confirm_reason")."\']').val();
                                var param = {};
                                param['lvl_idents'] = id;
                                param['lvl_alasan'] = alasan; 
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
                                'keyfield'=>'lvl_idents',
                                'keyparent'=>'lvl_parent',
                                'gridname'=>$gridname,
                                'button'=>'standar',
                                'buttonother'=>$buttonother,
                                'buttonotherposition'=>'first',
                                'width'=>'100%',
                                "height"=>"75vh",
                                'col'=>$col,
                                "jvAdd_text"=>"Kriteria",
                                "jvAdd"=>$jvAdd,
                                "jvEdit"=>$jvEdit,
                                "jvDelete"=>$jvDelete,
                                "jvView"=>$jvView,
                                'modul'=>'master/userkelompok',
                                'sumber'=>'server',
                                'creategrid'=>false,
                                'autoexpand'=>true
                            ));

        $content .="<div style='position:relative;top:0px;text-align:center'><div id=\"" . $gridname . "\"></div></div>";
        $content .= generateWindowjqx(array('window'=>'Tingkat','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        return $content;
    }
    function edit(){
        $gridname = "jqxTingkat";
        $data_perusahaan = null;
        $ro_level = false;
        $ro_score = false;
        $readonly = false;
        $hasil = false;
        foreach($_POST as $key=>$value){
            ${$key} = $value;
        }

        if($rowlevel==0){
            $hasil = true;
        }

        $rowlevel = $rowlevel+1;
        $arrCol = array(
            "lvl_idents",
            "lvl_parent",
            "lvl_nama",
            "lvl_petunjuk",
            "lvl_kelompok",
            "lvl_indikator",
            "lvl_berkas"
        );
        foreach($arrCol as $keyCol){
            if(!isset(${$keyCol})){
                ${$keyCol} = null;
            }
        }
        $title = null;
        if($type=="edit" || $type=="view" ){
            $column = $this->m_master->getTingkat_edit($lvl_idents);
            if($column->num_rows()>0){
                $column = $column->row();
                foreach($arrCol as $keyCol){
                    ${$keyCol} = $column->$keyCol;
                }
            }
        }else{
            $column = $this->m_master->getTingkat_edit($lvl_idents);
            
            if($column->num_rows()>0){
                $column = $column->row();
                foreach($arrCol as $keyCol){
                    ${$keyCol} = null;
                }
                $lvl_idents = $column->lvl_idents;
                $lvl_name_parent = $column->lvl_nama;
                $lvl_idents = 0;
            }
        }
        if($rowlevel!=0){
            $ro_level = true;
        }
        if($lvl_parent==0){
            $txt_lvl_nama = "Tingkat";
        }else{
            $txt_lvl_nama = "Kriteria";
        }
        if($type=="view"){
            $readonly = true;
        }          
        $optPARENTS = null;
        $urutan = 0;
        $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"lvl_idents", "value"=>$lvl_idents);
        $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Parent", "type"=>"hid", "namanya"=>"lvl_parent", "value"=>$lvl_parent);
        if($lvl_parent!=0){

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
        
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Categories/Supporting Process", "type"=>"cmb", "namanya"=>"lvl_kelompok", "value"=>$lvl_kelompok, "readonly"=>$readonly,"validation"=>array("validation"=>"notEmpty", "message"=>"Tingkat tidak boleh kosong"), "option"=>$optCategories, "size"=>"300px", "cascade"=>array("param_cascade"=>"idk_idents","url_cascade"=>"/master/nosj/getKategori_json","next_cascade"=>"lvl_indikator"));
            $arrTable[] = array(
                "group"=>1,
                'urutan'=>$urutan++, 
                "label"=>"Process Area", 
                "type"=>"cmb", 
                "option"=>$optProcess, "size"=>"300px",
                "namanya"=>"lvl_indikator", 
                "value"=>$lvl_indikator, 
                "readonly"=>$readonly,
                "validation"=>array("validation"=>"notEmpty", "message"=>"Process Area tidak boleh kosong"));
        }
        $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>$txt_lvl_nama, "type"=>"txa", "namanya"=>"lvl_nama", "value"=>$lvl_nama, "readonly"=>$readonly,"validation"=>array("validation"=>"notEmpty", "message"=>"Tingkat tidak boleh kosong"));
        if($lvl_parent==0){
            $optBerkas = $this->crud->getCommon(3,99);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Unggah Berkas", "type"=>"cmb", "namanya"=>"lvl_berkas", "value"=>$lvl_berkas, "readonly"=>$readonly,"validation"=>array("validation"=>"notEmpty", "message"=>"Pilihan Berkas tidak boleh kosong"), "option"=>$optBerkas, "size"=>"200px");
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Petunjuk/Bantuan", "type"=>"txa", "namanya"=>"lvl_petunjuk", "value"=>$lvl_petunjuk, 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px', 'width'=>'100%'), "readonly"=>$readonly);
        }
        // $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Tahun", "type"=>"txt", "namanya"=>"lvl_tahun", "value"=>$lvl_tahun, "readonly"=>true, "size"=>120);

        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/master/Tingkat/save',
                    'formcommand' => '/master/Tingkat/save'
                );

        $content = generateForm($arrForm, false);
        $content .= form_close();
        $button = null;
        if($type!="view"){
            $button = array(
                array(
                    "iconact"=>"fas fa-thumbs-up", "theme"=>"success","href"=>"javascript:jvSave(true)", "textact"=>"Simpan"
                )
            );
            
        }else{
            $title = "Lihat Tingkat";
        }
        $jenis = ($lvl_parent==0 ? "Kelompok " : "");
        $title .= $jenis;
        $title .= "Tingkat";

        $content = createportlet(array("content"=>$content,"title"=>$title, "icon"=>"fas fa-calendar", "listaction"=>$button));
        $content .= "
        <script>
            function jvSave(){
                validator
                .validate()
                .then(function(status){
                    if(status!='Invalid'){
                        var check = {};
                        lvl_nama = $('#lvl_nama').val();
                        lvl_parent = $('#lvl_parent').val();
                        check['lvl_nama'] = lvl_nama;
                        check['lvl_idents'] = $('#lvl_idents').val();
                        swal.fire({ 
                            title:'Simpan " . $txt_lvl_nama . "?', 
                            text: null, 
                            icon: 'question', 
                            showCancelButton: true, 
                            confirmButtonText: 'Ya', 
                            cancelButtonText: 'Tidak', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                        }).then(result => { 
                                if(result.value) {
                                    var save = {};
                                    var fieldother = [
                                        'lvl_idents',
                                        'lvl_parent',
                                        'lvl_kelompok',
                                        'lvl_indikator',
                                        'lvl_nama',
                                        'lvl_berkas',
                                        'hidTRNSKS',
                                    ];
        
                                    for(n=0; n < fieldother.length;n++){
                                        save[fieldother[n]] = $('#' + fieldother[n]).val();
                                    }
                                    if(lvl_parent==0){
                                        var desc = CKEDITOR.instances.lvl_petunjuk.getData();
                                        save['lvl_petunjuk'] = desc;
                                    }
                                    $('#imgPROSES').show();
                                    $('#windowProses').jqxWindow('open');
                                    $.post('/master/tingkat/save', save,function(data){
                                        $('#windowProses').jqxWindow('close');
                                        swal.fire(data);
                                        $('#".$gridname."').jqxGrid('clearselection');
                                        var tmpS = $('#".$gridname."').jqxTreeGrid('source');
                                        console.log(tmpS._source.url);
                                        tmpS._source.url = '/master/nosj/getTingkat_list/';
                                        $('#".$gridname."').jqxTreeGrid('updateBoundData');
                                        $('#".$gridname."').jqxTreeGrid('expandAll');
                                        $('#jqwTingkat').jqxWindow('close');
                                    });
                                } 
                        });
                    }
                });
            }
        </script>
        ";        
        echo $content;
    }
    function save(){
        // $this->common->debug_post();
        foreach($_POST as $key=>$value){
            if($key!="lvl_idents"){
                $value = $value;
                $input[$key] = $value;
            }
            ${$key} = $value;
        }
        if($hidTRNSKS=="add"){
            $input["lvl_usrnam"] = $this->username;
        }else{
            $input["lvl_updnam"] = $this->username;
            $input["lvl_upddat"] = $this->datesave;
        }
        if($lvl_parent==0){
            $jenis_title = "Tingkat";
        }else{
            $jenis_title = "Kriteria";
        }
        $this->common->logmodul(true, 
            array(
                "from"=>"Input Data Tingkat", 
                "table_name"=>$this->table, 
                "POST"=>$input, 
                "username"=>$this->username, "pk"=>array("lvl_idents"=>$lvl_idents)));        
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("lvl_idents"=>$lvl_idents))){
            echo $jenis_title . " gagal disimpan!";
        }else{
            echo $jenis_title . " berhasil disimpan!";
        }
    }
    function chkTingkat(){
        $lvl_idents = $this->input->post("lvl_idents");
        $lvl_adli = $this->input->post("lvl_adli");
        $rslCheck = $this->m_master->chkTingkat($lvl_idents, $lvl_adli);

        echo json_encode($rslCheck);
    }
    function chkTingkatkode(){
        $lvl_nama = $this->input->post("lvl_nama");
        $lvl_idents = $this->input->post("lvl_idents");
        $count = 0;
        $count = $this->m_master->chkTingkatkode($lvl_nama, $lvl_idents);
        // $this->common->debug_sql(1);
        echo json_encode($count);
    }    
    function delete(){
        $lvl_idents = $this->input->post("lvl_idents");
        $lvl_alasan = $this->input->post("lvl_alasan");
        $delete["lvl_is_deleted"] = 1;
        $this->crud->useTable($this->table);
        $this->crud->save($delete, array("lvl_idents"=>$lvl_idents), false);

        if($this->crud->__affectedRows <>0){
            $arrAction =array(
                "action"=> "Hapus Tingkat/Kriteria",
                "reason"=>$lvl_alasan,
                "unt_idents"=>$lvl_idents
            );

            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"1", 
                "log_action"=>$arrAction,
                "log_fkidents"=>$lvl_idents
            );
            $this->common->logmodul(false, $arrModul);
            echo $this->lang->line("confirm_success");
        }else{
            echo $this->lang->line("confirm_failed");
        }
    }
    function saveTingkat(){
        $tahun_impor = $this->input->post("tahun_impor");
        $tahun_Tingkat = $this->input->post("tahun_Tingkat");
        $lvl_perusahaan = $this->input->post("lvl_perusahaan");
        $rslTingkat = $this->m_master->getTingkattree_list($tahun_impor, false);
        // debug_array($rslTingkat->result());
        foreach($rslTingkat->result() as $keyKat=>$valueKat){
            $lvl_idents = $valueKat->lvl_idents;
            
            $lvl_kode = $valueKat->lvl_kode;
            $arrTingkat = explode(".", $lvl_kode);
            $lvl_jenis = $valueKat->lvl_jenis;
            if($lvl_jenis==2){
                $lvl_adli = 1;
            }else{
                $lvl_adli = 0;
            }
            $lvl_Tingkat = $arrTingkat[0];            
            $lvl_nama = $valueKat->lvl_nama;
            $lvl_parent = $valueKat->lvl_parent;
            $lvl_score = $valueKat->lvl_score;
            $lvl_penilaian = null;
            $lvl_icon = null;
            $lvl_krtidents = $valueKat->lvl_idents;

            $arr["lvl_idents"] = $lvl_idents;
            $arr["lvl_Tingkat"] = $lvl_Tingkat;
            $arr["lvl_kode"] = $lvl_kode;
            $arr["lvl_nama"] = $lvl_nama;
            $arr["lvl_parent"] = $lvl_parent;
            $arr["lvl_adli"] = $lvl_adli;
            $arr["lvl_score"] = $lvl_score;
            $arr["lvl_penilaian"] = $lvl_penilaian;
            $arr["lvl_icon"] = $lvl_icon;
            $arr["lvl_krtidents"] = $lvl_krtidents;

            $arrgw[] = $arr;
        }
        $navarray = $this->common->GenerateNavArray($arrgw);
        $isitable = $this->common->GenerateTingkat($navarray, $tahun_Tingkat, $lvl_perusahaan);
    }
}
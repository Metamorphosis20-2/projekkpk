<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends MY_Controller {
    var $table;
    function __construct(){
        parent::__construct();
        $this->load->helper(array('ginput'));
        $this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->table = "t_mas_kategori";
    }   
    public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> 'Data Kategori'),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listkategori(),'admin',$bc);      

    }

    public function listkategori(){
        $this->load->helper(array('jqxgrid'));
        $gridname = "jqxkategori";
        $url ="/master/nosj/getKategori_list/0";
        $col[] = array('lsturut'=>1, 'namanya'=>'idk_idents','ah'=>true,'label'=>'ID','ac'=>false);
        $col[] = array('lsturut'=>3, 'namanya'=>'idk_nama','aw'=>'55%','label'=>'Kategori/Process Area');
        $col[] = array('lsturut'=>3, 'namanya'=>'idk_type','aw'=>'80','label'=>'Jenis','ah'=>false);
        $col[] = array('lsturut'=>3, 'namanya'=>'idk_type_desc','aw'=>'140','label'=>'Jenis');
        $col[] = array('lsturut'=>4, 'namanya'=>'idk_usrnam','aw'=>'150','label'=>'Pembuat');
        $col[] = array('lsturut'=>5, 'namanya'=>'idk_usrdat','aw'=>'160','label'=>'Tanggal Buat');
        $col[] = array('lsturut'=>5, 'namanya'=>'idk_parent','aw'=>100,'label'=>'Parent','ah'=>true);

        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);

        $oADD = strpos("N".$otorisasi,"A");
        $buttonother = null;
        if($oADD>0){
            $buttonother = array(
                "Kategori"=>array('Print1','fa-plus','jvAddkategori()','warning','80')
            );            
        }
        // debug_array($url);
        $jvAdd = "
        function jvAddkategori(){
            swal.fire({ 
                title: 'Tambah Categories/Supporting Process?',
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
                    param['idk_idents'] = 0;
                    param['idk_parent'] = 0;
                    param['rowlevel'] = 0;
                    $('#jqwkategori').jqxWindow('open');
                    $.post('/master/kategori/edit', param,function(data){
                        var lebar = $(window).width() * 0.8;
                        $('#jqwkategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'320px',position:'middle', resizable:false,title: 'Tambah Kategori/Supporting Process', zIndex:'99999'});
                        $('#jqwkategori').jqxWindow('setContent', data);
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
                    var idk_kode = null;
                    var idk_nama = null;
                    titlenya = 'Tambah Pertanyaan level 0?';
                    swal.fire({ 
                        title: 'Untuk menambah Categories',
                        text: 'Gunakan Tombol Tambah Categories', 
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
                        var id = row.idk_idents;
                        var idk_nama = row.idk_nama;
                        var idk_type = row.idk_type;
                        if(idk_type=='1'){
                            text_tambah = 'Process Area';
                            titlenya = 'Tambah '+ text_tambah +' dibawah ' + idk_nama + '?';
                        }else{
                            text_tambah = 'Supporting Process';
                            titlenya = 'Tambah '+ text_tambah +'?';
                        }
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
                                param['idk_idents'] = 0;
                                param['idk_type_parent'] = idk_type;
                                // param['idk_tahun'] = $('#cmbOPTION').val();
                                param['idk_parent'] = id;
                                param['rowlevel'] = rowlevel;
                                $('#jqwkategori').jqxWindow('open');
                                $.post('/master/kategori/edit', param,function(data){
                                    var lebar = $(window).width() * 0.8;
                                    $('#jqwkategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'520px',position:'middle', resizable:false,title: 'Tambah '+text_tambah, zIndex:'99999'});
                                    $('#jqwkategori').jqxWindow('setContent', data);
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
                        $('#frmGrid').attr('action', '/master/kategori/tambah');
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
                var id = row.idk_idents;
                var idk_kode = row.idk_kode;
                var idk_nama = row.idk_nama;
                var parent = selectedrowindex[0].idk_parent;

                var idk_type = row.idk_type;
                if(idk_type=='1'){
                    text_tambah = 'Process Area';
                    titlenya = 'Lihat '+ text_tambah +' dibawah ' + idk_nama + '?';
                }else{
                    text_tambah = 'Supporting Process';
                    titlenya = 'Lihat '+ text_tambah +'?';
                }

                var param = {};
                param['type'] = 'edit';
                param['idk_idents'] = id;
                param['idk_tahun'] = $('#cmbOPTION').val();
                param['idk_parent'] = parent;
                param['rowlevel'] = rowlevel;
                $('#jqwkategori').jqxWindow('open');

                $.post('/master/kategori/edit', param,function(data){
                    var lebar = $(window).width() * 0.8;
                    $('#jqwkategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'520px',position:'middle', resizable:false,title: 'Ubah kategori', zIndex:'99999'});
                    $('#jqwkategori').jqxWindow('setContent', data);
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
                var id = row.idk_idents;
                var idk_kode = row.idk_kode;
                var idk_nama = row.idk_nama;
                var parent = selectedrowindex[0].idk_parent;

                var idk_type = row.idk_type;
                if(rowlevel==0){
                    text_tambah = 'Kategori';
                }else{
                    if(idk_type=='2'){
                        text_tambah = 'Process Area';
                    }else{
                        text_tambah = 'Supporting Process';
                    }
                }

                var param = {};
                param['type'] = 'view';
                param['idk_idents'] = id;
                param['idk_tahun'] = $('#cmbOPTION').val();
                param['idk_parent'] = parent;
                param['rowlevel'] = rowlevel;
                $('#jqwkategori').jqxWindow('open');

                $.post('/master/kategori/edit', param,function(data){
                    var lebar = $(window).width() * 0.8;
                    $('#jqwkategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'520px',position:'middle', resizable:false,title: 'Lihat ' + text_tambah, zIndex:'99999'});
                    $('#jqwkategori').jqxWindow('setContent', data);
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
                var id = row.idk_idents;
                var idk_kode = row.idk_kode;
                var idk_nama = row.idk_nama;
                var idk_rowlevel = row.rowlevel;
                var parent = selectedrowindex[0].idk_parent;

                var check = {};
                check['idk_idents'] = id;
                check['idk_rowlevel'] = idk_rowlevel;
                $('#imgPROSES').show();
                $(\"#windowProses\").jqxWindow('open');

                $.post('/master/kategori/chkkategori', check,function(data){
                    $('#windowProses').jqxWindow('close');
                    var checkdata = $.parseJSON(data);
                    num_rows = checkdata.found;
                    audit = checkdata.audit;
                    if(num_rows>0){
                        switch(audit){
                            case 'kelompok':
                            case 'pertanyaan':
                                keterangan = 'sudah mempunyai pertanyaan';
                                break;
                            default:
                                keterangan = 'mempunyai Process Area';
                                break;
                        }
                        if(rowlevel==0){
                            jenis = 'Kategori';
                        }else{
                            jenis = 'Process Area';
                        }
                        swal.fire({title: idk_nama + ' ' + keterangan, text: jenis + ' tidak bisa dihapus', type:'error'});
                    }else{
                        swal.fire({ 
                            title:'Hapus ' + idk_nama + '?', 
                            text:'".$this->lang->line("confirm_reason")."',
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
                                param['idk_idents'] = id;
                                param['idk_alasan'] = alasan; 
                                $('#imgPROSES').show();
                                $('#windowProses').jqxWindow('open');
                                $.post('/master/kategori/delete', param,function(data){
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
                                'keyfield'=>'idk_idents',
                                'keyparent'=>'idk_parent',
                                'gridname'=>$gridname,
                                'button'=>'standar',
                                'buttonother'=>$buttonother,
                                'buttonotherposition'=>'first',
                                'width'=>'100%',
                                "height"=>"75vh",
                                'col'=>$col,
                                "jvAdd_text"=>"Process Area",
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
        $content .= generateWindowjqx(array('window'=>'kategori','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        return $content;
    }
    function edit(){
        // $this->common->debug_post();
        $idk_type_parent = null;
        $gridname = "jqxkategori";
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
            "idk_idents",
            "idk_parent",
            "idk_nama",
            "idk_type",
            "idk_petunjuk"
        );
        foreach($arrCol as $keyCol){
            if(!isset(${$keyCol})){
                ${$keyCol} = null;
            }
        }
        $title = null;
        $opt_type_idk_type = $this->crud->getCommon(3,8);
        if($type=="edit" || $type=="view" ){
            $column = $this->m_master->getKategori_edit($idk_idents);
            if($column->num_rows()>0){
                $column = $column->row();
                foreach($arrCol as $keyCol){
                    ${$keyCol} = $column->$keyCol;
                }
                $type_idk_type = "hid";
                if($idk_parent=="0"){
                    $type_idk_type = "cmb";
                    unset($opt_type_idk_type[2]);
                    $script_jenis = "var data_type = $('#idk_type').select2('data');jenis_title = data_type[0].text;";
                }else{
                    if($idk_type==2){
                        $script_jenis = "jenis_title='Process Area'";
                    }
                    if($idk_type==3){
                        $script_jenis = "jenis_title='Supporting Process'";
                    }
                    
                }
            }
        }else{
            $column = $this->m_master->getKategori_edit($idk_idents);
            
            if($column->num_rows()>0){
                $column = $column->row();
                foreach($arrCol as $keyCol){
                    ${$keyCol} = null;
                }
                $idk_idents = $column->idk_idents;
                $idk_name_parent = $column->idk_nama;
                $opt_type_idk_type = null;
                $type_idk_type = "hid";
                if($idk_type_parent==1){
                    $idk_type = 2;
                }else{
                    $idk_type = 3;
                }
                
                $idk_parent = $idk_idents;
                $idk_idents = 0;
            }else{
                $type_idk_type = "hid";
                if($idk_type_parent==1){
                    $idk_type = 2;
                    $script_jenis = "jenis_title='Process Area'";
                }else{
                    if($idk_type_parent==3){
                        $idk_type = 3;
                        $script_jenis = "jenis_title='Supporting Process'";
                    }else{
                        $idk_type = 0;
                        $type_idk_type = "cmb";
                        unset($opt_type_idk_type[2]);
                        $script_jenis = "var data_type = $('#idk_type').select2('data');jenis_title = data_type[0].text;";
                    }
                }
            }
        }
        if($rowlevel!=0){
            $ro_level = true;
        }
        $parent_name = null;
        if($idk_parent!=0 && $idk_parent!=null){
            $rslParent = $this->m_master->getKategori_edit($idk_parent);
            $row = $rslParent->row();
            $parent_name = $row->idk_nama;
        }
        if($type=="view"){
            $readonly = true;
        }          
        $optPARENTS = null;
        $urutan = 0;
        if($idk_parent==0){
            $jenis = "Kategori ";
            $txtKategori = "Kategori";
        }else{
            $jenis = $parent_name;
            $txtKategori = "Process Area";
        }
        $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"idk_idents", "value"=>$idk_idents);
        $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Parent", "type"=>"hid", "namanya"=>"idk_parent", "value"=>$idk_parent);
        $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Jenis", "type"=>$type_idk_type, "namanya"=>"idk_type", "value"=>$idk_type, "option"=>$opt_type_idk_type);
        $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>$txtKategori, "type"=>"txa", "namanya"=>"idk_nama", "value"=>$idk_nama, "readonly"=>$readonly,"validation"=>array("validation"=>"notEmpty", "message"=>"kategori tidak boleh kosong"));
        if($idk_parent!=0){
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Petunjuk/Bantuan", "type"=>"txa", "namanya"=>"idk_petunjuk", "value"=>$idk_petunjuk, 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px', 'width'=>'100%'), "readonly"=>$readonly);
        }
        // $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Tahun", "type"=>"txt", "namanya"=>"idk_tahun", "value"=>$idk_tahun, "readonly"=>true, "size"=>120);

        $formname = "formgw";
		$arrForm =
			array(
					'type'=>$type,
					'arrTable'=>$arrTable,
					'status'=> isset($status) ? $status : "",
                    'nameForm'=>$formname,
                    'width'=>'70%',
					'modul' => '/master/kategori/save',
                    'formcommand' => '/master/kategori/save'
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
            $title = null;
        }
        
        $title .= $jenis;
        // $title .= "kategori";

        $content = createportlet(array("content"=>$content,"title"=>$title, "icon"=>"fas fa-calendar", "listaction"=>$button));
        $content .= "
        <script>
            function jvSave(){
                validator
                .validate()
                .then(function(status){
                    if(status!='Invalid'){
                        var check = {};
                        idk_nama = $('#idk_nama').val();
                        idk_parent = $('#idk_parent').val();
                        check['idk_nama'] = idk_nama;
                        check['idk_idents'] = $('#idk_idents').val();
                        " . $script_jenis . "
                        $.post('/master/kategori/chkkategorikode', check,function(data){
                            var checkdata = $.parseJSON(data);
                            num_rows = checkdata.found;
                            if(num_rows==0){
                                swal.fire({ 
                                    title:'Simpan " . $jenis . " ' + jenis_title + '?', 
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
                                                'idk_idents',
                                                'idk_parent',
                                                'idk_nama',
                                                'idk_type',
                                                'hidTRNSKS',
                                            ];
                
                                            for(n=0; n < fieldother.length;n++){
                                                save[fieldother[n]] = $('#' + fieldother[n]).val();
                                            }
                                            if(idk_parent!=0){
                                                var desc = CKEDITOR.instances.idk_petunjuk.getData();
                                                save['idk_petunjuk'] = desc;
                                            }
                                            save['jenis_title'] = jenis_title;
                                            $('#imgPROSES').show();
                                            $('#windowProses').jqxWindow('open');
                                            $.post('/master/kategori/save', save,function(data){
                                                $('#windowProses').jqxWindow('close');
                                                swal.fire(data);
                                                $('#".$gridname."').jqxGrid('clearselection');
                                                var tmpS = $('#".$gridname."').jqxTreeGrid('source');
                                                console.log(tmpS._source.url);
                                                tmpS._source.url = '/master/nosj/getkategori_list/';
                                                $('#".$gridname."').jqxTreeGrid('updateBoundData');
                                                $('#".$gridname."').jqxTreeGrid('expandAll');
                                                $('#jqwkategori').jqxWindow('close');
                                            });
                                        } 
                                });
                            }else{
                                swal.fire({title:'kategori ' + idk_nama + ' sudah ada!', text:'Silahkan Gunakan kode/nama kategori lain', icon:'error'});
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
            if($key!="idk_idents"){
                $value = $value;
                $input[$key] = $value;
            }
            ${$key} = $value;
        }
        if($hidTRNSKS=="add"){
            $input["idk_usrnam"] = $this->username;
        }else{
            $input["idk_updnam"] = $this->username;
            $input["idk_upddat"] = $this->datesave;
        }
        // debug_array($input);

        $this->common->logmodul(true, 
            array(
                "from"=>"Input Data kategori", 
                "table_name"=>$this->table, 
                "POST"=>$input, 
                "username"=>$this->username, "pk"=>array("idk_idents"=>$idk_idents)));        
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("idk_idents"=>$idk_idents))){
            echo $jenis_title . " gagal disimpan!";
        }else{
            echo $jenis_title . " berhasil disimpan!";
        }
    }
    function chkkategori(){
        $idk_idents = $this->input->post("idk_idents");
        $idk_adli = $this->input->post("idk_adli");
        $rslCheck = $this->m_master->chkkategori($idk_idents, $idk_adli);

        echo json_encode($rslCheck);
    }
    function chkkategorikode(){
        $idk_nama = $this->input->post("idk_nama");
        $idk_idents = $this->input->post("idk_idents");
        $count = 0;
        $count = $this->m_master->chkkategorikode($idk_nama, $idk_idents);
        // $this->common->debug_sql(1);
        echo json_encode($count);
    }    
    function delete(){
        $idk_idents = $this->input->post("idk_idents");
		$idk_alasan = $this->input->post('idk_alasan');
        $delete["idk_is_deleted"] = 1;   
        $this->crud->useTable($this->table);
        $this->crud->save($delete, array("idk_idents"=>$idk_idents), false);
        if($this->crud->__affectedRows <>0){
            $arrAction =array(
                "action"=> "Hapus Kategori",
                "reason"=>$idk_alasan,
                "unt_idents"=>$idk_idents
            );

            $arrModul = array(
                "from"=>$this->modul, 
                "table_name"=>$this->table, 
                "username"=>$this->username,
                "log_result"=>1, 
                "keypost"=>"1", 
                "log_action"=>$arrAction,
                "log_fkidents"=>$idk_idents
            );
            $this->common->logmodul(false, $arrModul);
            echo $this->lang->line("confirm_success");
        }else{
            echo $this->lang->line("confirm_failed");
        }
    }
    function savekategori(){
        $tahun_impor = $this->input->post("tahun_impor");
        $tahun_kategori = $this->input->post("tahun_kategori");
        $idk_perusahaan = $this->input->post("idk_perusahaan");
        $rslkategori = $this->m_master->getkategoritree_list($tahun_impor, false);
        // debug_array($rslkategori->result());
        foreach($rslkategori->result() as $keyKat=>$valueKat){
            $idk_idents = $valueKat->idk_idents;
            
            $idk_kode = $valueKat->idk_kode;
            $arrkategori = explode(".", $idk_kode);
            $idk_jenis = $valueKat->idk_jenis;
            if($idk_jenis==2){
                $idk_adli = 1;
            }else{
                $idk_adli = 0;
            }
            $idk_kategori = $arrkategori[0];            
            $idk_nama = $valueKat->idk_nama;
            $idk_parent = $valueKat->idk_parent;
            $idk_score = $valueKat->idk_score;
            $idk_penilaian = null;
            $idk_icon = null;
            $idk_krtidents = $valueKat->idk_idents;

            $arr["idk_idents"] = $idk_idents;
            $arr["idk_kategori"] = $idk_kategori;
            $arr["idk_kode"] = $idk_kode;
            $arr["idk_nama"] = $idk_nama;
            $arr["idk_parent"] = $idk_parent;
            $arr["idk_adli"] = $idk_adli;
            $arr["idk_score"] = $idk_score;
            $arr["idk_penilaian"] = $idk_penilaian;
            $arr["idk_icon"] = $idk_icon;
            $arr["idk_krtidents"] = $idk_krtidents;

            $arrgw[] = $arr;
        }
        $navarray = $this->common->GenerateNavArray($arrgw);
        $isitable = $this->common->GenerateKategori($navarray, $tahun_kategori, $idk_perusahaan);
    }
}
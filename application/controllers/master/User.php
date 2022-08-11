<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
    function __construct(){
        parent::__construct();
    	$this->load->helper(array('ginput','jqxgrid'));
        // debug_array($this->lang->line("grid_pembuat"));
    	$this->load->model(array('m_master'));

        $this->modul = $this->router->fetch_class();

        $this->daftar_pengguna = $this->lang->line("usr_daftar_pengguna");
        $this->data_pengguna = $this->lang->line("usr_data_pengguna");
        $this->login = $this->lang->line("usr_login");
        $this->nama = $this->lang->line("usr_nama");
        $this->tingkat = $this->lang->line("usr_tingkat");
        $this->status_aktif = $this->lang->line("usr_status_aktif");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->password = $this->lang->line("usr_password");
        $this->konfirmasi = $this->lang->line("usr_konfirmasi");
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->hak_akses = $this->lang->line("usr_hak_akses");
        $this->password_baru = $this->lang->line("usr_password_baru");

        $this->optLAYOUT = array_filter($this->crud->getCommon(3,14));

        $this->authrz = json_encode(
            array(
                "A"=>array("text"=>$this->btnTambah,"func"=>"ADD"), 
                "E"=>array("text"=>$this->btnUbah,"func"=>"EDT"), 
                "D"=>array("text"=>$this->btnHapus,"func"=>"DEL"), 
                "V"=>array("text"=>$this->btnLihat,"func"=>"VIW"), 
                "U"=>array("text"=>$this->btnUnggah,"func"=>"OTH"), 
                "P"=>array("text"=>$this->btnApproval,"func"=>"OTH"), 
                "T"=>array("text"=>$this->btnTolak,"func"=>"OTH"), 
                "S"=>array("text"=>"Salary","func"=>"OTH"), 
                "C"=>array("text"=>"Activation","func"=>"OTH"), 
                "K"=>array("text"=>"Semua Unit Kerja","func"=>"OTH")
        ));
		$this->table_user = $this->config->item('tbl_user');
		$this->table_menu = $this->config->item('tbl_menu');
		$this->table_common = $this->config->item('tbl_common');
        $this->table_usermenu = $this->config->item('tbl_usermenu');
        $this->activedirectory = $this->config->item('ldap');
        $this->employee = $this->config->item('employee');
        $this->multi_lang = $this->config->item('multi_lang');
        $this->security_level = $this->config->item('security_level');
        // debug_array($this->session->userdata());
        $this->usrunitkerja = $this->session->userdata("USR_UNITKERJA");
        $this->usr_level = $this->session->userdata("USR_LEVELS");        

        if($this->security_level!="level"){
            $this->fieldFKUSER = "MNU_FKUSER";
        }else{
            $this->fieldFKUSER = "MNU_LEVELS";
        }
        if($this->multi_lang){
            $this->optLANG = array("1"=>"English", "2"=>"Bahasa");
        }
    }	
	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> $this->daftar_pengguna),
        );

        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listUser(),'admin',$bc);  	 
	}
    public function listUser(){
        $gridname = "jqxUsers";
        $url ='/master/nosj/getUsers_list';
        
        $urutan = 0;
        $col = array();

        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_IDENTS','aw'=>'10%','label'=>'Identitas', 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_LOGINS','aw'=>'150','label'=>$this->login , 'adtype'=>'text');
        if($this->activedirectory){
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_ACTDIR','aw'=>'100','label'=>'Active Directory');
        }
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_FNAMES','aw'=>'150','label'=>$this->nama);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_LEVELS','aw'=>'100','label'=>$this->tingkat);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_UNITKERJA_DESC','aw'=>'180','label'=>"Unit Kerja");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_ACCESS','aw'=>'80','label'=>$this->status_aktif, 'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_USRNAM','aw'=>'150','label'=>$this->pengguna);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_USRDAT','aw'=>'150','label'=>$this->tanggal_buat);
        
        $jvDelete = "
            function jvDelete(data_row){
                id = data_row['USR_IDENTS'];
                USR_LOGINS = data_row['USR_LOGINS'];
                USR_SENDIRI = '" . $this->username. "'
                if(id){
                    if(USR_LOGINS==USR_SENDIRI){
                        swal.fire({ title : 'Tidak bisa menon-aktifkan user anda sendiri!', icon : 'error'});
                    }else{
                        if(USR_LOGINS!='9999'){

                            swal.fire({ 
                                title:'".$this->lang->line("confirm_non_aktif")." ' + USR_LOGINS + '?', 
                                text: null, 
                                icon: 'question', 
                                showCancelButton: true, 
                                confirmButtonText: '".$this->lang->line("Ya")."', 
                                cancelButtonText: '".$this->lang->line("Tidak")."', 
                                confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                                cancelButtonColor: '".$this->config->item("cancelButtonColor")."'  
                            }).then(result => { 
                                if(result.value) {
                                    var prm = {};
                                    prm['idents'] = id;
                                    $.post('/master/user/delete',prm,function(rebound){
                                        if(rebound){
                                            swal.fire(USR_LOGINS + ' ' + rebound + ' " . $this->lang->line("confirm_non_akfif_respon"). "!')
                                            // jQuery.noConflict();
                                            $('#" . $gridname  . "').DataTable().ajax.reload();
                                        }
                                    });
            
                                } 
                            });
                        }else{
                            swal.fire({ title : 'ID Pengguna 9999 tidak bisa dinon-aktifkan!', icon : 'error'});
                        }
                    }
                }
            }
        ";
        
        $optOPTION = array("1"=>"Aktif", "0"=>"Non Aktif");
        $arrCombo = array(
            'saring'=>array('width'=>'150','idents'=>'cmbOPTION','source'=>$optOPTION, 'value'=>1,'placeHolder'=>'Status Pengajuan','events'=>array('change'=>'jvChangeOption(event)')),
        );
        $content = gGrid(array('url'=>$url."/1", 
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
            'modul'=>'master/user',
            'toolbarCombo'=>$arrCombo
        ));
        $content .="
        <script>
        function jvChangeOption(){
            var option  = $('#cmbOPTION').val();
            url = '".$url."/'+option;
            $('#".$gridname."').DataTable().ajax.url(url).load();
        }
        </script>
        ";
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
            array('link'=>'/master/user','text'=>$this->daftar_pengguna),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function edit($type=null,$param=null,$source=null){
        $scriptDDG = null;
        $DDG_ACTDIR = null;
        $arrColumn = array(
            "txtLOGINS"=>"USR_LOGINS",
            "txtEMPLOY"=>"USR_FNAMES",
            "pwdPASSWD"=>"USR_PASSWD",
            "cmbLEVELS"=>"USR_LEVELS",
            "cmbLEVELD"=>"USR_LEVELD",
            "cmbACCESS"=>"USR_ACCESS",
            "cmbACCESD"=>"USR_ACCESD",
            "cmbLAYOUT"=>"USR_LAYOUT",
            "cmbTHEMES"=>"USR_THEMES",
            "txtACTDIR"=>"USR_ACTDIR",
            "txtUCREAT"=>"USR_USRNAM",
            "datDCREAT"=>"USR_UPDNAM",
            "cmbTYPUSR"=>"USR_TYPUSR",
            "txtAUTHRZ"=>"USR_AUTHRZ",
            'cmbLANGUAGE'=>'USR_LANGUAGE',
            "emp_name"=>"emp_name",
            "cmbEMPIDENTS"=>"USR_EMPIDENTS",
            "cmbUNITKERJA"=>"USR_UNITKERJA",
            "USR_AUTUNIT"=>"USR_AUTUNIT"
        );
        $combo_level_ro = "";
        if($type!="add"){
            $column = $this->m_master->getUsers_edit($param);
            $USR_LOGINS = $column->USR_LOGINS;
            if($USR_LOGINS=="9999"){
                $combo_level_ro = "readonly";
            }
        }
        // debug_array($column);
        foreach($arrColumn as $input=>$field){
            if(isset($column)){
                if(isset($column->{$field})){
                    ${$input} = $column->{$field};
                }else{
                    ${$input} = "";    
                }
            }else{
                ${$input} = null;
            }
        }
        if($cmbEMPIDENTS==""){
            $cmbEMPIDENTS = 0;
        }

        $optTHEMES = array(
        'arctic'=>'Arctic',
        'black'=>'Black',
        'darkblue'=>'Dark Blue',
        'energyblue'=>'Energy Blue',
        'glacier'=>'Glacier',
        'metro'=>'Metro',
        'office'=>'Office',
        'orange'=>'Orange',
        'shinyblack'=>'Shiny Black',
        'ui-le-frog'=>'Green');

        //TODO:TESTING 3456
        if($type=="add"){
            $cmbLAYOUT = 1;
            $cmbLEVELS = 1;
            $cmbACCESS = 1;
            $readonly = false;
            $readonlytags = false;
            $txtAUTHRD = "";
            if($this->activedirectory){
                $scriptDDG = "
                    $('#divtxtACTDIR').on('open', function () {
                        var tmpS = $('#jqxDDG_txtACTDIR').jqxGrid('source');
                        tmpS._source.url = '/master/nosj/getActivedirectory_ddg';
                        $('#jqxDDG_txtACTDIR').jqxGrid('source',tmpS);
                        $('#jqxDDG_txtACTDIR').jqxGrid('clearselection');
    
                    });
                    $('#jqxDDG_txtACTDIR').on('rowselect', function (event) {
                        var args = event.args;
                        var row = $('#jqxDDG_txtACTDIR').jqxGrid('getrowdata', args.rowindex);
                        if(row['USR_STATUS']=='Enabled'){
                            var USRNAM = row['USR_LOGINS'];
                            var LOGINS = $('#txtLOGINS').val();
                            var EMPLOY = $('#txtEMPLOY').val();
                            if($('#hidTRNSKS').val()=='add'){
                                //check user active directory
                                $.post('/master/user/chkusract',{ USRIDN:USRNAM },function(data){
                                    var arrDATA = data.split('~');
                                    if(arrDATA[0]==1){ //ada data
                                        if(arrDATA[1]==2){ //kalau sudah tidak aktif
                                            if(confirm(USRNAM + ' ".$this->lang->line("user_non_active_confirmation") ."?')){
                                                self.location.replace('/edit/master/user/'+arrDATA[2]);
                                            }
                                        }else{            
                                            num_rows = 1;
                                            usr_idents = arrDATA[2];
                
                                            if(num_rows>0){
                                                swal.fire({ 
                                                    title: 'User : ' + USRNAM + ' " . $this->lang->line("user_active_confirmation"). "?', 
                                                    text:'" . $this->lang->line("user_cannot_add_same_user"). "',
                                                    icon: 'error', 
                                                    showCancelButton: true, 
                                                    confirmButtonText: '".$this->lang->line("Ya")."', 
                                                    cancelButtonText: '".$this->lang->line("Tidak")."', 
                                                    confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                                                    cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                                                }).then(
                                                        result => { 
                                                            if(result.value) {
                                                                $('#formgw').attr('action', '/edit/master/user');
                                                                $('#grdIDENTS').val(usr_idents);
                                                                document.formgw.submit();
                                                            } 
                                                        }
                                                );
                                            }
                                        }
                                    }else{ // kalau tidak ada data
                                        var content = '<div style=\"position: relative; margin-left: 3px; margin-top: 5px;\">' + row['USR_LOGINS'] + '</div>';
                                        $('#divtxtACTDIR').jqxDropDownButton('setContent', content);
                                        $('#txtEMPLOY').val(row['USR_FULNAM']);
                                        $('#txtACTDIR').val(row['USR_LOGINS']);
                                        $('#txtLOGINS').val(USRNAM);
                                        $('#txtLOGINS').prop('readonly','readonly');
                                    }
                                });
                            }
                            $('#divtxtACTDIR').jqxDropDownButton('close');
                            if($('#txtNEWPAS').val()!=''){
                                if(!confirm('Sudah ada kata sandi dari Active Directory, tetap simpan kata sandi?')){
                                    $('#txtNEWPAS').val('');
                                    $('#txtNEWPAS_CONF').val('');
                                    $('#txtNEWPAS').prop('readonly','readonly');
                                    $('#txtNEWPAS_CONF').prop('readonly','readonly');
                                }
                            }
                        }else{
                            swal.fire('" . $this->data_pengguna . " sudah tidak Aktif!');
                        }
                    });
                ";
                $gridname = "jqxDDG_txtACTDIR";
                $url ='/nosj/getNosjnull';
                $urut=0;
                $col[] = array('lsturut'=>$urut++, 'aw'=>'23%', 'label' => 'ID Pengguna','namanya' => 'USR_LOGINS');
                $col[] = array('lsturut'=>$urut++, 'aw'=>'35%', 'label' => 'Nama','namanya' => 'USR_FULNAM');
                $col[] = array('lsturut'=>$urut++, 'aw'=>'23%','label' => 'Unit Organisasi','namanya' => 'USR_UNIORG');
                $col[] = array('lsturut'=>$urut++, 'aw'=>'19%','label' => 'Status','namanya' => 'USR_STATUS');
                $DDG_ACTDIR = gGrid(array(  'url'=>$url, 
                                            'gridname'=>$gridname,
                                            'width'=>'500px',
                                            'height'=>'450px',
                                            'col'=>$col,
                                            'fontsize'=>10,
                                            'headerfontsize'=>10,
                                            'columnsheight'=>50,
                                            'showToolbar'=>false,
                                            'creategrid'=>false,
                                            'sumber'=>'detanto',
                                            "post"=>false
                                        ));
            }
            //TODO:TESTING 56789
        }else{
            $readonly = true;
            $readonlytags = true;
            // $txtAUTHRD = $txtAUTHRZ ."~" . $txtAUTHRD;
            $scriptDDG = "";
            $DDG_ACTDIR = "";
        }
        $urutan = 0;

        $optACCESS = array_filter($this->crud->getCommon(3,99));
        $optLEVELS = array_filter($this->crud->getCommon(3,9));
        $cmbTYPUSR = $cmbTYPUSR=="" ? 1 : $cmbTYPUSR;
        
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidIDENTS','label'=>'Username','size'=>'170','value'=>$param);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'grdIDENTS','label'=>'Username','size'=>'170','value'=>$param);
        if($this->employee){
            $optEmployee = array(
                'type'=>'json', 
                'url'=>'/hr/employee/tagemployee', 
                "value_desc"=>$emp_name,
                "value"=>$cmbEMPIDENTS,
                "placeHolder"=>"Please Select Name",
                "minimumInputLength"=>0,
                "value_with_id"=>true,
            );
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'cmb', 'namanya' =>'cmbEMPIDENTS', 'label'=>$this->nama, 'size'=>'300','value'=> isset($column) ? $cmbEMPIDENTS : "", "option"=>$optEmployee);
        }else{
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'txt', 'namanya' =>'txtEMPLOY', 'label'=>$this->nama, 'size'=>'300','value'=> isset($column) ? $txtEMPLOY : "");
        }

        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'txt','namanya'=>'txtLOGINS','label'=>$this->login,'size'=>'300','readonly'=>$readonly,'value'=>$txtLOGINS);

        if($this->activedirectory){
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'ddg', 'label'=>'ID Active Directory', 'namanya' =>'txtACTDIR','size' => '150', 'text'=>$txtACTDIR, 'value'=>$txtACTDIR);
        }
        
        if($type!='view'){
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'pwd','namanya'=>'txtNEWPAS'     ,'label'=>$this->password,  'size'=>'250');        
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'pwd','namanya'=>'txtNEWPAS_CONF','label'=>$this->konfirmasi,'size'=>'250');        
        }
        $button1 = array("table", "jvCariAuthrz()"); 
        
        $readonlyunitkerja = false;
        $level_pengguna = $optLEVELS[5];
        if($this->usr_level>1){
            // if($this->usr_level==2){
                
            // }
            $i = 0;
            $level_array = $this->usr_level+1;
            if(isset($optLEVELS[$level_array])){
                $optLEVELS = array($level_array=>$optLEVELS[$level_array]);
            }else{
                $optLEVELS = array();
            }
            if($this->usr_level>2){
                $readonlyunitkerja = true;
            }
            if($this->usr_level==2){
                $optLEVELS[5] = $level_pengguna;
            }
            // $level_array = $this->usr_level+1;
            // if(isset($optLEVELS[$level_array])){
            //     $optLEVELX = array($level_array=>$optLEVELS[$level_array]);
            //     if($this->usr_level==2){
            //         $optLEVELX[$level_array+1] = $optLEVELS[$level_array+1];
            //     }
            //     $optLEVELS = $optLEVELX;
            // }else{
            //     $optLEVELS = array();
            // }            
            if($type=="add"){
                $cmbUNITKERJA = $this->usrunitkerja;
            }
        }else{
            if($this->usrlevel==1 && $type=="add"){
                $level_array = $this->usr_level+1;
                if(isset($optLEVELS[$level_array])){
                    $optLEVELS = array($level_array=>$optLEVELS[$level_array]);
                }else{
                    $optLEVELS = array();
                }
                $optLEVELS[5] = $level_pengguna;
            }else{
                $optLEVELS = array($cmbLEVELS=>$optLEVELS[$cmbLEVELS]);
            }
        }
        // debug_array($optLEVELS);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'cmb','label'=>'Role', 'namanya' =>'cmbLEVELS','size' => '200px','option' => $optLEVELS, "readonly"=>$combo_level_ro, 'value'=> ($type=='view') ? $cmbLEVELD : $cmbLEVELS);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'cmb','label'=>$this->hak_akses, 'namanya' =>'cmbACCESS','size' => '120','option' => $optACCESS,  "readonly"=>$combo_level_ro, 'value'=> ($type=='view') ? $cmbACCESD : $cmbACCESS);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'cmb','label'=>"Lihat Semua Unit Kerja", 'namanya' =>'USR_AUTUNIT','size' => '120','option' => $optACCESS,  "readonly"=>$combo_level_ro, 'value'=> ($type=='view') ? $cmbACCESD : $USR_AUTUNIT);
        
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','label'=>'Layout Menu', 'namanya' =>'cmbLAYOUT','size' => '120');
        if($this->multi_lang){
            $cmbLANGUAGE = ($cmbLANGUAGE=="" ? 1 : $cmbLANGUAGE);
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'cmb','label'=>$this->lang->line("usr_bahasa"), 'namanya' =>'cmbLANGUAGE','size' => '120','option' => $this->optLANG, 'value'=> ($type=='view') ? $this->optLANG[$cmbLANGUAGE] : $cmbLANGUAGE);
        }
        // $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'cmb','label'=>'Themes', 'namanya' =>'cmbTHEMES','size' => '120','option' => $optTHEMES,      'value'=> ($type=='view') ? $cmbTHEMES : $cmbTHEMES);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','label'=>'Themes', 'namanya' =>'cmbTHEMES','size' => '120','option' => $optTHEMES,      'value'=> 1);

        $field = array("unt_idents", "unt_unitkerja");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_unitkerja",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        if($type=="view"){
            $readonlyunitkerja = true;
        }
        $optUnitkerja = $this->crud->getGeneral_combo($arrayOpt);

        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'cmb','label'=>'Unit Kerja', 'namanya' =>'cmbUNITKERJA','size' => '320','option' => $optUnitkerja, 'divname'=>'nguik', 'value'=> ($type=='view') ? $cmbUNITKERJA : $cmbUNITKERJA, "readonly"=>$readonlyunitkerja);
        
        $formname = "formgw";

        $arrTab = array('1'=>'fas fa-user-tie^' . $this->data_pengguna);
        $fncsave = null;
        if($this->security_level!="level"){
            $menu = $this->assignmenu();
        }
        $arrForm =
          array(
                'type'=>$type,
                'arrTable'=>$arrTable,
                'status'=> isset($status) ? $status : "",
                'param' =>$param,
                'width' => 710,
                'nameForm' => $formname,
                'formcommand' => '/save/master/user',
                'ftinggi' => '100%',
                'tabname'=> $arrTab
            );
        $script = "
        <style>
            .jqx-grid-column-header{
                z-index:1 !important;
            }
        </style>
        <script>
        $(document).ready(function () {
            $('#myrowcmbUNITKERJA').css('visibility','hidden');
            $('#myrowUSR_AUTUNIT').css('visibility','hidden');
            $('#cmbLEVELS').on('select2:select', function (e) {
                var data = e.params.data;
                id_level = data.id;
                var prm = {};
                switch(id_level){
                    case '1':
                    case '2':
                        $('#myrowcmbUNITKERJA').css('visibility','hidden');
                        $('#myrowUSR_AUTUNIT').css('visibility','hidden');
                        break;
                    case '5':
                        $('#myrowUSR_AUTUNIT').css('visibility','visible');
                        break;
                    default:
                        $('#myrowcmbUNITKERJA').css('visibility','visible');
                        $('#myrowUSR_AUTUNIT').css('visibility','hidden');
                        break;
                }
            });

            if($('#cmbLEVELS').val()==1 || $('#cmbLEVELS').val()==2){
                $('#myrowcmbUNITKERJA').css('visibility','hidden');
            }else{
                $('#myrowcmbUNITKERJA').css('visibility','visible');
            }

            $('#txtLOGINS').blur(function(){
                var USRNAM = $(this).val();

                if($('#txtLOGINS').prop('readonly')==false){
                    var length = USRNAM.length;
                    var hasSpace = /\s/g.test(USRNAM);

                    var regex = new RegExp('^[0-9a-zA-Z_\. ]+$');

                    if (regex.test(USRNAM)) {
                        spec = false;
                    }else{
                        spec = true;
                    }
                    if((hasSpace==true || spec==true) && length!=0){
                        swal.fire({ 
                            title:'ID Pengguna tidak boleh mengandung spesial karakter!', 
                            icon: 'error', 
                        }).then(function(){ 
                            $('#txtLOGINS').val('');
                            $('#txtLOGINS').focus();
                        });
                    }else{
                        if($.trim($('#txtLOGINS').val())=='')return;
                        if(length>0){
                            $('#imgPROSES').show();
                            $('#windowProses').jqxWindow('open');
                            $.post('/master/user/chkusrapp/2',{ USRIDN:USRNAM },function(data){
                                $('#windowProses').jqxWindow('close');
                                var chkuser = $.parseJSON(data);
                
                                num_rows = chkuser.num_rows;
                                usr_idents = chkuser.usr_idents;
        
                                if(num_rows>0){
                                    $('#txtLOGINS').val('');
                                    $('#txtLOGINS').focus();
                                    swal.fire({ 
                                        title: 'ID Pengguna : ' + USRNAM + ' sudah digunakan!', 
                                        text:'Tidak bisa menambahkan ID Pengguna yang sama',
                                        icon: 'error'
                                    });
                                }else{
                                    $('#txtNEWPAS').focus();
                                }
                            });                              
                        }
                    }
                }
            });
            ".$scriptDDG."
        });
        function jvSave(){";
            $script .= $fncsave;
            $script .= "
                txtlogin = $('#txtLOGINS').val();
                if(txtlogin=='' || txtlogin.length<5){
                    swal.fire({ 
                        title:'ID Pengguna tidak boleh kosong atau lebih kecil dari 5 karakter!', 
                        icon: 'error', 
                    });
                }else{
                    var txtNEWPAS = $('#txtNEWPAS').val();
                    
                    if(txtNEWPAS=='' && $('#hidTRNSKS').val()=='add' && $('#cmbLEVELS').val()<3){
                        swal.fire({ 
                            title:'Kata Sandi tidak boleh kosong!', 
                            icon: 'error', 
                        });
                    }else{
                        var lanjut = false;
                        if(typeof txtNEWPAS!=='undefined'){
                            if(txtNEWPAS!=''){
                                var MD5 = function(s){function L(k,d){return(k<<d)|(k>>>(32-d))}function K(G,k){var I,d,F,H,x;F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);if(I&d){return(x^2147483648^F^H)}if(I|d){if(x&1073741824){return(x^3221225472^F^H)}else{return(x^1073741824^F^H)}}else{return(x^F^H)}}function r(d,F,k){return(d&F)|((~d)&k)}function q(d,F,k){return(d&k)|(F&(~k))}function p(d,F,k){return(d^F^k)}function n(d,F,k){return(F^(d|(~k)))}function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}function e(G){var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;while(H<F){Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]| (G.charCodeAt(H)<<d));H++}Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;return aa}function B(x){var k=\"\",F=\"\",G,d;for(d=0;d<=3;d++){G=(x>>>(d*8))&255;F=\"0\"+G.toString(16);k=k+F.substr(F.length-2,2)}return k}function J(k){k=k.replace(/rn/g,\"n\");var d=\"\";for(var F=0;F<k.length;F++){var x=k.charCodeAt(F);if(x<128){d+=String.fromCharCode(x)}else{if((x>127)&&(x<2048)){d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128)}else{d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128)}}}return d}var C=Array();var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;for(P=0;P<C.length;P+=16){h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g)}var i=B(Y)+B(X)+B(W)+B(V);return i.toLowerCase()};
    
                                var NEWPAS = ($(\"#txtNEWPAS\").val());
                                var NEWPAS_INSERT = ($(\"#txtNEWPAS_CONF\").val());
    
                                if(NEWPAS!=NEWPAS_INSERT){
                                    swal.fire({ 
                                        title:'Kata Sandi Tidak Sama!', 
                                        icon: 'error', 
                                    });
                                }else{
                                    lanjut = true;
                                }
                            }else{
                                lanjut = true;
                            }
                        }
                    }
                    if($('#cmbLEVELS').val()>2 && $('#cmbLEVELS').val()!=5){
                        if($('#cmbUNITKERJA').val()=='' || $('#cmbUNITKERJA').val()==0 ){
                            swal.fire({ 
                                title:'Unit Kerja harus diisi!', 
                                icon: 'error', 
                            });
                            lanjut = false;
                        }
                    }
                    if(lanjut){
                        if($('#cmbLEVELS').val()>=3 && txtNEWPAS=='' && $('#hidTRNSKS').val()=='add'){
                            level = parseInt($('#cmbLEVELS').val());
                            switch(level){
                                case 3: //Produsen Data
                                    password = 'KPK_ProdusenData2021';
                                    break;
                                case 4: //PIC Data
                                    password = 'KPK_Picdata1232021';
                                    break;
                                case 5: //Operator
                                    password = 'KPK_Pengguna1232021';
                                    break;
                            }
                            textnya = 'Default Password adalah ' + password;
                            $('#txtNEWPAS').val(password);
                        }else{
                            textnya = null;
                        }
                        // alert($('#cmbLEVELS').val());
                        swal.fire({ 
                            title:'Simpan data Pengguna?', 
                            icon: 'question',
                            text: textnya,
                            showCancelButton: true, 
                            confirmButtonText: '".$this->lang->line("Ya")."', 
                            cancelButtonText: '".$this->lang->line("Tidak")."', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                        }).then(result => {
                            if(result.value) {
                                $('#cmbUNITKERJA').prop('disabled',false);
                                document.formgw.submit();
                            }else{
                                $('#cmbUNITKERJA').prop('disabled',true);
                                $('#txtNEWPAS').val('');
                            }
                        });                        
                        
                    }
                    
                }
            }
        </script>
        ";
        $button = null;
        if($type!="view"){
            $button = createButton();
        }else{
            $DDG_ACTDIR = "";
        }
        
        $content = generateForm($arrForm, true);
        $content .= $DDG_ACTDIR;
        $content .= $button;
        $content .= $script;
        if($type!="view"){
            $button = array(
                array("iconact"=>"fas fa-save", "theme"=>"success","href"=>"javascript:jvSave(1)", "textact"=>"Simpan")
            );
        }
        $content .= form_close();
                
        return $content;
    }
    function assignmenu($parameter){
        $USR_THEMES = $this->session->userdata("USR_THEMES");
        $fromuser = TRUE;
        foreach($parameter as $key=>$value){
            ${$key} = $value;
        }
        $this->load->helper('jqxgrid');
        $arrTab = [];
        $exception = null;
        $only = null;
        if($this->app_numbr=="9999" || $this->app_numbr=="03013NH"){
            $exception = array("app_applic"=>"9999");
        }else{
            $only = array("app_applic"=>$this->app_numbr); //$this->app_applic;
        }
        $rslAPPLIC = $this->crud->getApplication_list($exception, $only); //);
        foreach($rslAPPLIC->result() as $keyAPPLIC=>$valueAPPLIC){
            $app_applic = $valueAPPLIC->app_applic;
            $app_descre = $valueAPPLIC->app_descre;
            $app_iconed = "fa-" . $valueAPPLIC->app_iconed;

            $arrAPPLIC[$app_applic] = array($app_iconed=>$app_descre);
        }
        $app_applic = $valueAPPLIC->app_applic;
        $app_descre = $valueAPPLIC->app_descre;
        $app_iconed = "fa-" . $valueAPPLIC->app_iconed;
        $group = 1;
        $docready = "$('";
        $checkall = "$('";
        $fncsave = "var jsonval='';
                seen = [];";
        foreach ($arrAPPLIC as $APPLIC => $APPLIC_VALUE) {
            $ICONED = array_keys($APPLIC_VALUE)[0];
            $DESCRE = array_values($APPLIC_VALUE)[0];
            unset($col);
            $urutan = 0;
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_IDENTS','label'=>'ID','ac'=>false, 'ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_DESCRE','aw'=>'50%','label'=>'Menu');
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_CHANGE','aw'=>'10%','aa'=>'center','label'=>'Chg','ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_MNUADD','aw'=>'10%','aa'=>'center','label'=>$this->btnTambah);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_MNUEDT','aw'=>'10%','aa'=>'center','label'=>$this->btnUbah);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_MNUDEL','aw'=>'10%','aa'=>'center','label'=>$this->btnHapus);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_MNUVIW','aw'=>'10%','aa'=>'center','label'=>$this->btnLihat);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_MNUOTH','aw'=>'10%','aa'=>'center','label'=>$this->other);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_PARENT','aw'=>100,'label'=>'Parent','ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_APPLIC','aw'=>100,'label'=>'Kode Aplikasi','ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_NOMORS','aw'=>100,'label'=>'Nomor','ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_HVCHLD','aw'=>100,'label'=>'Punya Anak','ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_RIGHTS','aw'=>100,'label'=>'Hak','ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_EDTBLE','aw'=>100,'label'=>'Bisa Diubah','ah'=>true);
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_AUTHRZ','aw'=>100,'label'=>'Bisa Diubah','ah'=>true);
            // $this->common->debug_array($APPLIC, false);
            $gridmenu = 'gridmenu';
            $txtLOGINS = ($txtLOGINS=="" ? "0" : $txtLOGINS);
            $url ="/master/nosj/getMenu_tree/2/2/".$txtLOGINS."/".$APPLIC;
            $fn_select = "
                var args = event.args;
                var row = args.row;
                jvSelectTree(row, '$APPLIC');";

            $event = array(
                "rowSelect" => $fn_select,
            );
            $buttonother = array(
                "Impor Menu"=>array('Impor','fa-file-import',"jvImpor('$APPLIC')",'info','120')
            );

            $grid_01 = gGrid(array( 'url'=>$url, 
                                    'bisaedit'=>false,
                                    'treegrid'=>true,
                                    'autoexpand'=>true,
                                    // 'button'=>$buttonother,
                                    'keyfield'=>'MNU_IDENTS',
                                    'keyparent'=>'MNU_PARENT',
                                    'gridname'=>$gridmenu.$APPLIC,
                                    'width'=>'100%',
                                    'height'=>'500px',
                                    'event'=>$event,
                                    'col'=>$col,
                                    'idCol'=>'MNU_IDENTS',
                                    'fontsize'=>10,
                                    'sumber'=>'detanto',
                                    'post'=>false,
                                    'virtualmode'=>true,
                                    'creategrid'=>false
                                    ));
            unset($arrTable2);

            $docready .= ($group==1 ? "" : ", ") . "#chkMNUADD_".$APPLIC;
            $docready .= ", #chkMNUEDT_".$APPLIC;
            $docready .= ", #chkMNUDEL_".$APPLIC;
            $docready .= ", #chkMNUVIW_".$APPLIC;

            $checkall .= ($group==1 ? "" : ", ") . "#chkMNUALL_".$APPLIC;;

            $fncsave .= "
                var obj = $('#".$gridmenu.$APPLIC."').jqxTreeGrid('getRows');
                json = JSON.stringify(obj, function(key, val) {
                   if (typeof val == 'object') {
                        if (seen.indexOf(val) >= 0)
                            return
                        seen.push(val)
                    }
                    return val
                });

                jsonval += json;
                $('#hidDETAIL').val(jsonval);
            ";

            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidIDNMNU_'.$APPLIC,'label'=>'Hapus',    'value'=>'');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidAPPLIC_'.$APPLIC,'label'=>'Username','size'=>'170','value'=>'');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidNOMORS_'.$APPLIC,'label'=>'Username','size'=>'170','value'=>'');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidUSRNAM_'.$APPLIC,'label'=>'Username','readonly'=>true,'size'=>'170','value'=>'');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidRIGHTS_'.$APPLIC,'label'=>'Username','readonly'=>true,'size'=>'170','value'=>'');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidAUTHRZ_'.$APPLIC,'label'=>'Authrz','readonly'=>true,'size'=>'170','value'=>'');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'txt','namanya'=>'txtDESCRE_'.$APPLIC,'label'=>'Menu','readonly'=>true,'size'=>'370','value'=>'');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'chk','namanya'=>'chkMNUALL_'.$APPLIC,'label'=>$this->lang->line("pilih_semua"),  'value'=> '');
            $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'udf','namanya'=>'div_'.$APPLIC,'label'=>null,'readonly'=>true,'size'=>'370','value'=>"<div id='divmenu_".$APPLIC."'></div>");
            // $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'chk','namanya'=>'chkMNUADD_'.$APPLIC,'label'=>'Tambah',  'value'=> '');
            // $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'chk','namanya'=>'chkMNUEDT_'.$APPLIC,'label'=>'Ubah',    'value'=> '');
            // $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'chk','namanya'=>'chkMNUDEL_'.$APPLIC,'label'=>'Hapus',   'value'=>'');
            // $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'chk','namanya'=>'chkMNUVIW_'.$APPLIC,'label'=>'Lihat',   'value'=>'');
            $arrForm =
              array(
                    'type'=>"add",
                    'arrTable'=>$arrTable2,
                    'status'=> "add",
                    'width' => 710,
                    'nameForm' => 'formmenu',
                    'formcommand' => '/save/master/user',
                    'form_create'=>false

                );        
            $html = generateForm($arrForm, false, false);
            $kanan = "<div style='overflow:hidden'>".$html."</div>";
            $menu_kiri = $grid_01;
            $menu_kiri .= "<div id='div".$gridmenu.$APPLIC."'><div id=\"".$gridmenu.$APPLIC."\"></div></div>";
            $menu_kanan = createportlet(array("content"=>$kanan,"title"=>$this->lang->line("header_otorisasi_akses_menu"),"caption_helper"=>"pilih dari daftar menu", "icon"=>"fas fa-globe", "class"=>"portletGrafik"));      
            $menu = displayGrid(array("row"=>1, "column"=>2, "grid"=>array($menu_kiri, $menu_kanan)));

            // if($type!="add"){
                $arrTable[] = array('group'=>$group, 'urutan'=>1, 'type'=> 'udf', 'value'=>$menu);    

                $arrTab = $arrTab + array($group=>'fas ' .$ICONED.'^'.$DESCRE);
                $group++;
        
            // }
        }
        $docready .= "').change(function() {
                    var arrthisid = this.id.split('_');
                    thisid = arrthisid[1];
                    jvSaveusermenu(thisid);
                });";
        $checkall .= "').change(function() {
                    var arrthisid = this.id.split('_');
                    thisid = arrthisid[1];
                    jvCheckall(thisid);
                });";
        $loop = 1;

        $script = "
        <script>
            $(document).ready(function () {
                " . $docready . "
                " . $checkall . "
            });
            function traverseTreeGrid(action) {
                var treeGrid = \"$('#" . $gridmenu ."')\";
                function traverseRows(rows) {
                var idValue;
                for(var i = 0; i < rows.length; i++) {
                    if (rows[i].records) {
                    idValue = rows[i][idColumn];
                    $('#" . $gridmenu ."').jqxTreeGrid(action+'Row',idValue);
                    traverseRows(rows[i].records);
                    };
                };
                };

                var idColumn = $('#" . $gridmenu ."').jqxTreeGrid('source')._source.id;
                traverseRows($('#" . $gridmenu ."').jqxTreeGrid('getRows'));
            };
            function jvSelectTree(row, applic){
                if(row.MNU_HVCHLD==0){
                    var pjson = '$this->authrz';
                    var pjson = $.parseJSON(pjson);
                    
                    applic = row.MNU_APPLIC;
                    authrz = row.MNU_AUTHRZ;
                    $('#chkMNUALL_'+applic).prop('checked',false);
                    $('#hidIDNMNU_'+applic).val(row.MNU_IDENTS);
                    $('#hidAPPLIC_'+applic).val(applic);
                    $('#hidNOMORS_'+applic).val(row.MNU_NOMORS);
                    $('#hidUSRNAM_'+applic).val($('#txtLOGINS').val());
                    $('#hidRIGHTS_'+applic).val(row.MNU_RIGHTS);
                    $('#hidAUTHRZ_'+applic).val(authrz);
                    $('#txtDESCRE_'+applic).val(row.MNU_DESCRE);

                    var RIGHTS = row.MNU_RIGHTS;

                    loop = 0;
                    $('#divmenu_'+applic).empty();
                    for(e=0;e<authrz.length;e++){
                        authrz_code = authrz.substr(loop,1);
                        _func = 'pjson.'+authrz_code+'.func';
                        func = eval(_func);
                        _text = 'pjson.'+authrz_code+'.text';
                        text = eval(_text);
                        
                        funct = \"javascript:jvSaveusermenu(this, '\"+authrz+\"')\";


                        var input = \"<div class='form-group row' id='myrowchkMNU\"+func.toUpperCase()+\"_\"+applic+\"'><label for='chkMNU\"+func.toUpperCase()+\"_\"+applic+\"' id='lblMNU\"+func.toUpperCase()+\"' class='col-2 col-form-label'>\"+text+\"</label><div class='col-9 col-form-label'><div class='checkbox-inline'><label class='checkbox checkbox-success'><input type='checkbox' name='chkMNU\"+func.toUpperCase()+\"_\"+applic+\"' id='chkMNU\"+func.toUpperCase()+\"_\"+applic+\"' class='control-label'  data-label='\"+authrz+\"' onclick='javascript:jvSaveusermenu(this)'/><span></span></label></div></div></div>\"; 

                        $('#divmenu_'+applic).append(input);
                        id_element = 'chkMNU'+func.toUpperCase()+'_'+applic;
                        if (RIGHTS.toLowerCase().indexOf(authrz_code.toLowerCase()) >= 0){
                            $('#'+ id_element).prop('checked',true);
                        }else{
                            $('#'+ id_element).prop('checked',false);
                        }
                        loop++;
                    }
                }else{
                    $('#divmenu_'+applic).empty();
                }
            }

            function jvCheckall(applic){
                var checked = false;
                if($('#chkMNUALL_'+applic).prop('checked')){
                    var checked = true;
                }
                var pjson = '$this->authrz';
                var pjson = $.parseJSON(pjson);
                authrz = $('#hidAUTHRZ_'+applic).val();

                loop = 0;
                var found = 0;
                for(e=0;e<authrz.length;e++){
                    authrz_code = authrz.substr(loop,1);
                    _func = 'pjson.'+authrz_code+'.func';
                    func = eval(_func);
                    id_element = 'chkMNU'+func.toUpperCase()+'_'+applic;
                    if($('#'+id_element).prop('disabled')){
                        $('#'+id_element).prop('checked', false);
                    }else{
                        $('#'+id_element).prop('checked', checked);
                        found++;
                    }                    
                    loop++;
                }
                if(found>0){
                    jvSaveusermenu(applic);    
                }
            }
            function jvSaveusermenu(applic){
                if(typeof applic==='object'){
                    authrz = applic.getAttribute('data-label');
                    id_applic = applic.id;
                    var n = id_applic.indexOf('_') + 1;
                    var l = id_applic.length;
                    applic = id_applic.substring(n, l);
                }else{
                    var authrz = $('#hidAUTHRZ_'+applic).val();    
                }
                
                var IDNMNU = $('#hidIDNMNU_'+applic).val();
                var check = '<li class=\"fas fa-check\"></li>';
                var RIGHTS = '';
                $('#".$gridmenu."'+applic).jqxTreeGrid('setCellValue', IDNMNU, 'MNU_CHANGE', 1);

                var pjson = '$this->authrz';
                var pjson = $.parseJSON(pjson);
                console.log(pjson);
                loop=0;
                for(e=0;e<authrz.length;e++){
                    authrz_code = authrz.substr(loop,1);
                    _func = 'pjson.'+authrz_code+'.func';
                    func = eval(_func);
                    id_element = 'chkMNU'+func.toUpperCase()+'_'+applic;
                    // if(id_element==id_applic){
                        if($('#'+id_element).prop('checked')){
                            RIGHTS += authrz_code;
                            $('#".$gridmenu."'+applic).jqxTreeGrid('setCellValue', IDNMNU, 'MNU_MNU'+func, check);
                        }else{
                            $('#".$gridmenu."'+applic).jqxTreeGrid('setCellValue', IDNMNU, 'MNU_MNU'+func, '');
                        }
                    // }
                    loop++;
                }
                $('#".$gridmenu."'+applic).jqxTreeGrid('setCellValue', IDNMNU, 'MNU_RIGHTS', RIGHTS);
            }
        </script>
        ";
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidDETAIL','label'=>'hidDETAIL','size'=>'170');
        if($fromuser){
            $return["menu"] = $menu;
            $return["script"] = $script;
            $return["arrtab"] = $arrTab;
            $return["fncsave"] = $fncsave;
        }else{
            $formname = "frmMenu";
            $type = "add";
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'txtLOGINS','label'=>$this->login,'size'=>'300','value'=>$txtLOGINS);
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'end','label'=>'end','size'=>'170',"value"=>true);
            $arrForm =
                array(
                    'type'=>$type,
                    'arrTable'=>$arrTable,
                    'status'=> isset($status) ? $status : "",
                    'param' =>$txtLOGINS,
                    'width' => 710,
                    'nameForm' => $formname,
                    'formcommand' => '/master/user/savemenu',
                    'ftinggi' => '100%',
                    'tabname'=> $arrTab
                );
            // $return = $menu . 
            $return = generateForm($arrForm, true);
            $return .= $script;
            $return .= "<script>
            function jvSave(){";
            $return .= $fncsave;
            $return .= "console.log(jsonval);             
                $('#cmbLEVELS').prop('disabled',false);
                $('#cmbACCESS').prop('disabled',false);
                $('#hidDETAIL').val(jsonval);
                document.frmMenu.submit();
            }
            </script>";
            if($type!="view"){
                $return .= createButton();
            } 
            // $arrTab
            // $fncsave
            // $script
        }

        return $return;
    }
    function getotorisasi(){
        $APPLIC = $this->input->post("APPLIC");
        $NOMORS = $this->input->post("NOMORS");
        $USRNAM = $this->input->post("USRNAM");
        $result = $this->m_master->getMenu_edit($APPLIC, $NOMORS, $USRNAM);
        // debug_array($result);
        $MNU_RIGHTS = $result->MNU_RIGHTS;
        $MNU_DESCRE = $result->MNU_DESCRE;
        $chkMNUADD = false;
        $chkMNUEDT = false;
        $chkMNUDEL = false;
        if(strpos("Z".$MNU_RIGHTS, "A")>0){
            $chkMNUADD = true;
        }
        if(strpos("Z".$MNU_RIGHTS, "E")>0){
            $chkMNUEDT = true;
        }
        if(strpos("Z".$MNU_RIGHTS, "D")>0){
            $chkMNUDEL = true;
        }
        $urutan = 0;
        echo $content;
    }
	function viewdetail($IDENTS=null, $source=null){
        $tabheight = '550';
        if($IDENTS==""){
            $IDENTS = $this->username;
            $tabheight = '600';
        }
        $userinfo = null;
        $result = $this->m_master->getUsers_edit($IDENTS);
        // $this->common->debug_sql(1);
        // debug_array($result);
        if($result!=null){
            $IDENTS = $result->USR_IDENTS;
            $FNAMES = $result->USR_FNAMES;
            $LOGINS = $result->USR_LOGINS;
            $ACTDIR = $result->USR_ACTDIR;
            $LEVELS = $result->USR_LEVELD;
            $ACTIVE = $result->USR_ACCESD;
            $ACCESS = $result->USR_ACCESD;
            // if(isset($result->USR_USRDAT)){
                $USRDAT = $result->USR_USRDAT;
                $USLDAT = $result->USL_USRDAT;
            // }
            $ADDRES = $result->USL_ADDRES;
            
            $urutan = 0;
    
            $this->load->library('table');
            $template = array('table_open'  => '<table border="0" style="width:100%" cellpadding="2" cellspacing="1" class="table table-striped">');
            $this->table->set_template($template);
            /*
            if($EMP_IMAGES==""){
              $EMP_IMAGES = "resources/images/noprofile.gif";
            }else{
              if(file_exists('assets/karyawan/' . $EMP_IMAGES) == FALSE){
                $EMP_IMAGES = "resources/images/na.jpg";
              }else{
                $EMP_IMAGES = "assets/karyawan/".$EMP_IMAGES;
              }
            }
    
            $srcImages = "<img src='".base_url().$EMP_IMAGES."' alt='' class='img-rounded img-responsive' style='height:200px;padding-top:5px'/>";
            */
            // $this->table->add_row(array('data'=>$srcImages, 'rowspan'=>6,'style'=>'text-align:center;width:150px'));
            if($this->session->userdata("dn")!=null){
                $FNAMES = $this->session->userdata("cn");
                $TITLE = $this->session->userdata("title");
                $arrOU = $this->session->userdata("uo");
                $ou = "";
                $rcin = false;
                foreach($arrOU as $keyou=>$valueou){
                    if($rcin) $ou .= ", ";
                    $ou .= $valueou;
                    $rcin = true;
                }
                $this->table->add_row("Nama", array('data'=>$FNAMES));
                $this->table->add_row("Jabatan", array('data'=>$TITLE));
                $this->table->add_row("Unit Organisasi", array('data'=>$ou));
            }else{
                $this->table->add_row("Nama", array('data'=>$FNAMES));
            
            }
            $this->table->add_row("ID Pengguna", array('data'=>"<kbd>".$LOGINS."</kbd>"));
            $this->table->add_row("Role Pengguna", array('data'=>$LEVELS));
            $this->table->add_row("Akses", array('data'=>$ACCESS));
            $this->table->add_row("Status Aktif", array('data'=>$ACTIVE));
            $this->table->add_row("Tanggal Dibuat", array('data'=>$USRDAT));
            $this->table->add_row("Terakhir Akses", array('data'=>$USLDAT));
            $this->table->add_row("IP Terakhir Akses", array('data'=>$ADDRES));

            $userinfo = $this->table->generate();
        }
		$content = $userinfo;
		$arrbread = array(
			array('link'=>'/home/welcome','text'=>'Beranda'),
			array('link'=>'#','text'=>'Info Pengguna'),
		);			
		$bc = generateBreadcrumb($arrbread);
        $content = generateTabjqx(array(
                      'id'=>'Dashboard',
                      'width'=>'100%',
                      'tabheight'=>$tabheight,
                      'tabwidth'=>'100%',
                      'arrTabs'=> 
                        array(
                            'fas fa-user^Pengguna'=>array('data'=>$userinfo)
                        )
                      ));
                    //   'arrTabs'=> array('fas fa-user^Pengguna'=>array('data'=>"<center>".$userinfo."</center>"))
        if($source==null){
            $this->_render('pages/home', $content,"admin",$bc);
        }else{
            echo $content;
        }
	}
    function ubahpassword($param=null,$type=null,$source=null){
        $arrColumn = array(
            'txtUSRNAM'=>'USR_LOGINS',
            'hidOLDPAS'=>'USR_PASSWD',
            'txtOLDPAS'=>'USR_PASSWD',
            'cmbLAYOUT'=>'USR_LAYOUT',
            'cmbTHEMES'=>'USR_THEMES',
            // 'cmbLANGUAGE'=>'USR_LANGUAGE'
            );

        $param = $this->username;
        $column = $this->m_master->getChangePass_edit($param);
        // debug_array($column);
        foreach($arrColumn as $input=>$field){
            if($column!=""){
                ${$input} = $column->{$field};
            }else{
                ${$input} = "";
            }
        }

        if ($type != "add"){
            $hidOLDPAS = $hidOLDPAS;
        }
        if($cmbLAYOUT=="" || $cmbLAYOUT==0){
            $cmbLAYOUT = 1;
        }
        $formname = 'formgw';   
        $onSuccess = "
            jvSave();
        ";

        $urutan=0;
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'view','namanya'=>'txtUSRNAM','label'=>'Username','value'=>$this->username, "readonly"=>true);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidUSRNAM','label'=>'Username','size'=>'170','value'=>$this->username);
        if($this->session->userdata('actdir')==''){
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidOLDPAS','label'=>'Kata Sandi Database','size'=>'170','value'=>$hidOLDPAS);
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'pwd','namanya'=>'txtOLDPAS','label'=>$this->password,'size'=>'170','value'=>'');
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'pwd','namanya'=>'txtNEWPAS','label'=>$this->password_baru,'size'=>'170','value'=>'');
            $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'pwd','namanya'=>'txtNEWPAS_CONF','label'=>$this->konfirmasi,'size'=>'170');
        }
        $optTHEMES = array(
        'arctic'=>'Arctic',
        'black'=>'Black',
        'darkblue'=>'Dark Blue',
        'energyblue'=>'Energy Blue',
        'glacier'=>'Glacier',
        'metro'=>'Metro',
        'office'=>'Office',
        'orange'=>'Orange', 
        'shinyblack'=>'Shiny Black',
        'ui-le-frog'=>'Green');

        $arrTable[] = array('group'=>1, 'urutan'=>$urutan++, 'type'=> 'hid', 'label'=>'Layout Menu', 'namanya' =>'cmbLAYOUT','size' => '120','option' => $this->optLAYOUT, 'value'=> $cmbLAYOUT);
        // $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'cmb','label'=>$this->lang->line("usr_bahasa"), 'namanya' =>'cmbLANGUAGE','size' => '120','option' => $this->optLANG, 'value'=> ($type=='view') ? $this->optLANG[$cmbLANGUAGE] : $cmbLANGUAGE);
        $arrTable[] = array('group'=>1, 'urutan'=>$urutan++, 'type'=> 'cmb', 'label'=>'Themes', 'namanya' =>'cmbTHEMES','size' => '120','option' => $optTHEMES, 'value'=> $cmbTHEMES);
        
        $btnSave = '<a href="javascript:jvSave();" alt="'.$this->lang->line("save_edit_ubah"). '"><i class="fas fa-save fa-3"></i></a>';

        $button = "
            <div style='height:20px'>&nbsp;</div>
            <div class=\"row row-centered\">
                <div class='col-sm-4'>&nbsp;</div>
                <div class='col-sm-1' style='text-align:center'>" . $btnSave. "</div>";
        if($this->session->userdata('actdir')==''){
            $button .= "                
                <div class='col-sm-5'><ul><li>Kosongkan kata sandi baru apabila tidak ingin mengubah</li><li>Kata Sandi harus diisi untuk perubahan</li></ul></div>
            </div>
            ";
        }
        $button = "<div style='float:right'>" . $btnSave . "</div>";
        $keterangan = "<ul><li>" . $this->lang->line("ubah_password_text"). "</li></ul>";
        $arrTable[] = array('group'=>1, 'urutan'=>$urutan++, 'type'=> 'title', 'namanya' =>'cmbTHEMES','size' => '120', 'value'=> $keterangan);
        // $button = displayGrid(array("row"=>1, "column"=>2, "grid"=>array($button, $keterangan)));
        // $arrTable[] = array('group'=>1, 'urutan'=>99, 'type'=> 'udf', 'value'=> $button, 'coltype'=>12);
        
        $arrForm =
            array(
                'type'=>'cetak',
                'arrTable'=>$arrTable,
                'width' => 300,
                'modul' => 'konfirm',
                'nameForm' => $formname,
                'tabname'=> array('1'=>'fas fa-user-tie^'. $this->lang->line("ubah_profil_pengguna")),   
                'formcommand' => '/master/user/savepassword'
            );
        $script = "
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.arctic.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.black.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.darkblue.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.energyblue.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.glacier.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.metro.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.office.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.orange.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.shinyblack.css'>
            <link rel='stylesheet' href='/resources/plugins/jqwidgets/styles/jqx.ui-le-frog.css'>
            <script>
            $(document).ready(function() {
                $('#cmbTHEMES').on('change', function (event) {
                    var args = event.args;
                    if (args) {
                        // index represents the item's index.
                        var index = args.index;
                        var item = args.item;
                        var label = item.label;
                        var theme = item.value;
                        var type = args.type; // keyboard, mouse or null depending on how the item was selected.

                        $('#tabDashboard').jqxTabs({ theme: theme,width:'100%', height: '100%' });
                        $('#cmbLAYOUT').jqxComboBox({theme:theme});
                        $('#cmbTHEMES').jqxComboBox({theme:theme});
                    }
                });                 
                // 
            });
                function jvSave(){
                    var txtOLDPAS = $('#txtOLDPAS').val();

                    var lanjut = true;
                    if(typeof txtOLDPAS!=='undefined'){
                        if(txtOLDPAS!=''){
                            var MD5 = function(s){function L(k,d){return(k<<d)|(k>>>(32-d))}function K(G,k){var I,d,F,H,x;F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);if(I&d){return(x^2147483648^F^H)}if(I|d){if(x&1073741824){return(x^3221225472^F^H)}else{return(x^1073741824^F^H)}}else{return(x^F^H)}}function r(d,F,k){return(d&F)|((~d)&k)}function q(d,F,k){return(d&k)|(F&(~k))}function p(d,F,k){return(d^F^k)}function n(d,F,k){return(F^(d|(~k)))}function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}function e(G){var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;while(H<F){Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]| (G.charCodeAt(H)<<d));H++}Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;return aa}function B(x){var k=\"\",F=\"\",G,d;for(d=0;d<=3;d++){G=(x>>>(d*8))&255;F=\"0\"+G.toString(16);k=k+F.substr(F.length-2,2)}return k}function J(k){k=k.replace(/rn/g,\"n\");var d=\"\";for(var F=0;F<k.length;F++){var x=k.charCodeAt(F);if(x<128){d+=String.fromCharCode(x)}else{if((x>127)&&(x<2048)){d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128)}else{d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128)}}}return d}var C=Array();var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;for(P=0;P<C.length;P+=16){h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g)}var i=B(Y)+B(X)+B(W)+B(V);return i.toLowerCase()};

                            var NEWPAS = ($(\"#txtNEWPAS\").val());
                            var NEWPAS_INSERT = ($(\"#txtNEWPAS_CONF\").val());
                            var OLDPAS = ($(\"#hidOLDPAS\").val());
                            var OLDPAS_INSERT = MD5($(\"#txtOLDPAS\").val());

                            if(OLDPAS!=OLDPAS_INSERT){
                                swal.fire('Password tidak sama dengan data yang ada di database!');
                                lanjut =false;
                            }
                            if(NEWPAS!=NEWPAS_INSERT){
                                swal.fire('Password Tidak Sama');
                                lanjut = false;
                            }
                        }                    
                    }
    
                    if(lanjut){
                        swal.fire({ 
                            title:'".$this->lang->line("save_edit_ubah")."?', 
                            text: null, 
                            icon: 'question', 
                            showCancelButton: true, 
                            confirmButtonText: 'Ya', 
                            cancelButtonText: 'Batal', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                        }).then(result => {
                            if(result.value) {
                                document.".$formname.".submit();
                            }
                        });
                    }
                }

            </script>
        ";

        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> $this->lang->line("ubah_kata_sandi")),
        );

        $bc = generateBreadcrumb($arrbread);
        $content = generateForm($arrForm);
        $content .= $script;
        $arrButton = array(
            array("text"=>"Simpan", "events"=>"jvSave()", "theme"=>"primary", "image"=>"fas fa-save"),
        );
        $content .= createButton($arrButton);
        $this->_render('pages/home',$content,'admin', $bc);
        // return $content;
    }
    function encrypt($string){
        $encrypt = "";
        $length = strlen($string);
        $length_code;
        $chrCode;
        $chr;       
    
        for ($i=0; $i<$length; $i++)
        {
            $chr = substr($string,$i,1);
            $chrCode = ord($chr);
            $length_code = strlen($chrCode);
    
            $encrypt .= $length_code . $chrCode;
        }
        return $encrypt;
    }
    public function listUsermenu(){
        $gridname = "jqxUsers";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Userlevel/list/m_master';
        $urutan = 0;
        $col = array();

        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_TYPECD','aw'=>'10%','label'=>'Identitas', 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_DESCR1','aw'=>'150','label'=>"Level" , 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_USRNAM','aw'=>'250','label'=>$this->pengguna);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_USRDAT','aw'=>'250','label'=>$this->tanggal_buat);
        
        $jvDelete = "
            function jvDelete(data_row){
                id = data_row['COM_TYPECD'];
                COM_DESCR1 = data_row['COM_DESCR1'];
                if(id){
                    swal.fire({ 
                        title:'Hapus Menu untuk ' + COM_DESCR1 + '?', 
                        text: null, 
                        icon: 'question', 
                        showCancelButton: true, 
                        confirmButtonText: '".$this->lang->line("Ya")."', 
                        cancelButtonText: '".$this->lang->line("Tidak")."', 
                        confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                        cancelButtonColor: '".$this->config->item("cancelButtonColor")."'  
                    }).then(result => { 
                        if(result.value) {
                            var prm = {};
                            prm['idents'] = id;
                            $.post('/master/user/deletemenuuser',prm,function(rebound){
                                if(rebound){
                                    swal.fire(COM_DESCR1 + ' ' + rebound + ' " . $this->lang->line("confirm_deleted"). "!')
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
            'button'=> 'standar',
            'jvDelete'=>$jvDelete,
            'sumber'=>'server',
            'modul'=>'master/menuuser'
        ));

        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Level Pengguna"),
        );

        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);  

        return $content;
    }
    function editUsermenu($type=null, $index = null, $source=null){
        $index = $this->input->post("grdIDENTS");
        $parameter = array("txtLOGINS"=>$index, "fromuser"=>FALSE);
        $content = $this->assignmenu($parameter);

        $judul = $this->btnUbah;
        if($type=="add"){
            $judul = $this->btnTambah;
        }
        if($type=="view"){
            $judul = $this->btnLihat;
        }

        $namaLevel = null;
        $rslLevel = $this->crud->getCommon_edit(9, $index);
        $namaLevel = " Otorisasi Menu [" . $rslLevel->COM_DESCR1 . "]";
        $judul = $judul . $namaLevel;
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/master/menuuser','text'=>"Daftar Otorisasi Role"),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function editx($type=null,$param=null,$source=null){
    }
    function save(){
        // $this->common->debug_post();
        $hidIDENTS = $this->input->post('hidIDENTS');
        $txtLOGINS = $this->input->post('txtLOGINS');
        $txtACTDIR = $this->input->post('txtACTDIR');
        $USR_AUTUNIT = $this->input->post('USR_AUTUNIT');
        $txtEMPLOY = $this->input->post('txtEMPLOY');        
        $EMPIDENTS = $this->input->post('cmbEMPIDENTS');
        $txtNEWPAS = $this->input->post('txtNEWPAS');
        $cmbLEVELS = $this->input->post('cmbLEVELS');
        $cmbACCESS = $this->input->post('cmbACCESS');
        $cmbLAYOUT = $this->input->post('cmbLAYOUT');
        $cmbTHEMES = $this->input->post('cmbTHEMES');
        $hidTRNSKS = $this->input->post('hidTRNSKS'); 
        $txtAUTHRZ = $this->input->post('txtAUTHRZ'); 
        $cmbTYPUSR = $this->input->post('cmbTYPUSR'); 
        $cmbWEBSVC = $this->input->post('cmbWEBSVC'); 
        $cmbLANGUAGE = $this->input->post('cmbLANGUAGE'); 
		$cmbUNITKERJA = $this->input->post('cmbUNITKERJA');

        $txtCOMPANY = 1;

        $input = array(
            "USR_LEVELS"=>$cmbLEVELS,
            "USR_ACCESS"=>$cmbACCESS,
            "USR_ACTDIR"=>$txtACTDIR,
            "USR_LAYOUT"=>$cmbLAYOUT,
            "USR_THEMES"=>$cmbTHEMES,
            "USR_AUTHRZ"=>$txtAUTHRZ,
            "USR_TYPUSR"=>$cmbTYPUSR,
            "USR_WEBSVC"=>$cmbWEBSVC,
            "USR_COMPANY"=>$txtCOMPANY,
            "USR_LANGUAGE"=>$cmbLANGUAGE,
            "USR_AUTUNIT"=>$USR_AUTUNIT
        );
        if($cmbUNITKERJA!=null){
            $input = array_merge($input, array("USR_UNITKERJA"=>$cmbUNITKERJA));
        }
        if($this->employee){
            $input = array_merge($input, array("USR_EMPIDENTS"=>$EMPIDENTS));
        }else{
            $input = array_merge($input, array("USR_FNAMES"=>$txtEMPLOY));
        }
        if($txtNEWPAS!=""){
            $input = array_merge($input, array('USR_PASSWD'=>md5($txtNEWPAS)));
        }
        if($hidTRNSKS=="add"){            
            $input = array_merge($input, array("USR_LOGINS"=>$txtLOGINS, "USR_USRNAM"=>$this->username, "USR_USRDAT"=>$this->datesave));
        }else{
            $input = array_merge($input, array("USR_UPDNAM"=>$this->username, "USR_UPDDAT"=>$this->datesave));
        }
        // debug_array($input);
        $redirect = '/master/user';
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table_user, "POST"=>$input, "username"=>$this->username, "pk"=>array('USR_IDENTS'=>$hidIDENTS)));
        $this->crud->useTable($this->table_user);
        if(!$this->crud->save($input,array('USR_IDENTS'=>$hidIDENTS))){
            $this->common->message_save('save_gagal',null,$redirect);
        }else{
            $return = true;
            $this->crud->useTable($this->table_usermenu);
            if($this->security_level!="level"){
                $this->savemenu();
            }
            
            if($return){
                $this->common->message_save('save_sukses',null, $redirect); 
            }
        }        
    }
    function savemenu(){
        $txtLOGINS = $this->input->post('txtLOGINS');
        $hidDETAIL = $this->input->post('hidDETAIL');
        $end = $this->input->post('end');
        $DETAIL = $hidDETAIL;
        $DETAIL = str_replace("}][][{", "},{", $hidDETAIL);
        $DETAIL = str_replace("][][", "", $DETAIL);
        $DETAIL = str_replace("}][{", "},{", $DETAIL);
        $DETAIL = str_replace("[]", "", $DETAIL);
        $DETAIL = json_decode($DETAIL, true);
        $hidTRNSKS = "edit";

        if($hidTRNSKS!="add"){
            // debug_array($DETAIL);
            if(isset($DETAIL)){
                foreach ($DETAIL as $key => $value_e) { //level 1
                    $MNU_CHANGE = 0;
                    if(isset($value_e['MNU_CHANGE'])){
                        $MNU_CHANGE = $value_e['MNU_CHANGE'];
                    }            
                    if($MNU_CHANGE==1){
                        $MNU_APPLIC = $value_e['MNU_APPLIC'];
                        $MNU_NOMORS = $value_e['MNU_NOMORS'];
                        $MNU_RIGHTS = $value_e['MNU_RIGHTS'];
                        $MNU_PARENT = $value_e['MNU_PARENT'];
                        if($MNU_RIGHTS!=""){
                            $inputMenu = array($this->fieldFKUSER=>$txtLOGINS,"MNU_APPLIC"=>$MNU_APPLIC,"MNU_MENUCD"=>$MNU_NOMORS,"MNU_RIGHTS"=>$MNU_RIGHTS, "MNU_USRNAM"=>$this->username,"MNU_DCREAT"=>$this->datesave);
                            $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table_usermenu, "POST"=>$inputMenu, "username"=>$this->username, "pk"=>array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS)));

                            $this->crud->useTable($this->table_usermenu);
                            if(!$this->crud->save($inputMenu,array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS))){
                                $this->common->message_save('save_gagal',null,$redirect);
                            }else{
                                if($MNU_PARENT!=0){
                                    $this->saveparent($MNU_APPLIC,$MNU_NOMORS,$txtLOGINS);
                                }
                            }
                        }else{
                            $this->deleteusermenu($txtLOGINS, $MNU_APPLIC, $MNU_NOMORS);
                        }
                    }
                    if(isset($value_e['records'])){ //level 2
                        foreach ($value_e['records'] as $key_n => $value_n) {
                            $MNU_CHANGE = 0;
                            if(isset($value_n['MNU_CHANGE'])){
                                $MNU_CHANGE = $value_n['MNU_CHANGE'];    
                            }
                            if($MNU_CHANGE==1){
                                $MNU_APPLIC = $value_n['MNU_APPLIC'];
                                $MNU_NOMORS = $value_n['MNU_NOMORS'];
                                $MNU_RIGHTS = $value_n['MNU_RIGHTS'];
                                if($MNU_RIGHTS!=""){
                                    $inputMenu = array($this->fieldFKUSER=>$txtLOGINS,"MNU_APPLIC"=>$MNU_APPLIC,"MNU_MENUCD"=>$MNU_NOMORS,"MNU_RIGHTS"=>$MNU_RIGHTS, "MNU_USRNAM"=>$this->username,"MNU_DCREAT"=>$this->datesave);

                                    $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table_usermenu, "POST"=>$inputMenu, "username"=>$this->username, "pk"=>array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS)));

                                    $this->crud->useTable($this->table_usermenu);
                                    if(!$this->crud->save($inputMenu,array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS))){
                                        $this->common->message_save('save_gagal',null,$redirect);
                                    }
                                    else{
                                        $this->saveparent($MNU_APPLIC,$MNU_NOMORS,$txtLOGINS);
                                    }
                                }else{
                                    $this->deleteusermenu($txtLOGINS, $MNU_APPLIC, $MNU_NOMORS);
                                }
                            }
                            if(isset($value_n['records'])){ //level 3
                                foreach ($value_n['records'] as $key_y => $value_y) {
                                    $MNU_CHANGE = 0;
                                    if(isset($value_y['MNU_CHANGE'])){
                                        $MNU_CHANGE = $value_y['MNU_CHANGE'];    
                                    }
                                    if($MNU_CHANGE==1){
                                        $MNU_APPLIC = $value_y['MNU_APPLIC'];
                                        $MNU_NOMORS = $value_y['MNU_NOMORS'];
                                        $MNU_RIGHTS = $value_y['MNU_RIGHTS'];
                                        if($MNU_RIGHTS!=""){
                                            $inputMenu = array($this->fieldFKUSER=>$txtLOGINS,"MNU_APPLIC"=>$MNU_APPLIC,"MNU_MENUCD"=>$MNU_NOMORS,"MNU_RIGHTS"=>$MNU_RIGHTS, "MNU_USRNAM"=>$this->username,"MNU_DCREAT"=>$this->datesave);

                                            $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table_usermenu, "POST"=>$inputMenu, "username"=>$this->username, "pk"=>array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS)));
                                            
                                            $this->crud->useTable($this->table_usermenu);
                                            if(!$this->crud->save($inputMenu,array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS))){
                                                $this->common->message_save('save_gagal',null,$redirect);
                                            }else{
                                                $this->saveparent($MNU_APPLIC,$MNU_NOMORS,$txtLOGINS);
                                            }
                                        }else{
                                            $this->deleteusermenu($txtLOGINS, $MNU_APPLIC, $MNU_NOMORS);
                                        }
                                    }
                                    if(isset($value_y['records'])){ //level 4
                                        foreach ($value_y['records'] as $key_h => $value_h) {
                                            $MNU_CHANGE = 0;
                                            if(isset($value_h['MNU_CHANGE'])){
                                                $MNU_CHANGE = $value_h['MNU_CHANGE'];    
                                            }                                    
                                            if($MNU_CHANGE==1){
                                                $MNU_APPLIC = $value_h['MNU_APPLIC'];
                                                $MNU_NOMORS = $value_h['MNU_NOMORS'];
                                                $MNU_RIGHTS = $value_h['MNU_RIGHTS'];
                                                if($MNU_RIGHTS!=""){
                                                    $inputMenu = array($this->fieldFKUSER=>$txtLOGINS,"MNU_APPLIC"=>$MNU_APPLIC,"MNU_MENUCD"=>$MNU_NOMORS,"MNU_RIGHTS"=>$MNU_RIGHTS, "MNU_USRNAM"=>$this->username,"MNU_DCREAT"=>$this->datesave);

                                                    $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table_usermenu, "POST"=>$inputMenu, "username"=>$this->username, "pk"=>array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS)));

                                                    $this->crud->useTable($this->table_usermenu);
                                                    if(!$this->crud->save($inputMenu,array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$txtLOGINS))){
                                                        $this->common->message_save('save_gagal',null,$redirect);
                                                    }else{
                                                        $this->saveparent($MNU_APPLIC,$MNU_NOMORS,$txtLOGINS);
                                                    }
                                                }else{
                                                    $this->deleteusermenu($txtLOGINS, $MNU_APPLIC, $MNU_NOMORS);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->crud->deleteparent($txtLOGINS);
        }
        if($end){
            $redirect = "/master/menuuser";
            $this->common->message_save('save_sukses',null, $redirect);
        }
    }
    function saveparent($MNU_APPLIC,$MNU_NOMORS,$MNU_LOGINS){
        // $this->common->debug_post();
        $parent = 1;
        $x = 0;
        $this->crud->useTable($this->table_usermenu);
        $condition = true;
        $keluar = false;
        while(!$keluar) {
            $save = false;
            $query = $this->m_master->getMenu_edit($MNU_APPLIC,$MNU_NOMORS,$MNU_LOGINS);
            if($query->num_rows()>0){
                $row= $query->row();    
                // debug_array($row);
                $parent = $row->MNU_PARENT;
                $MNU_APPLIC = $row->MNU_APPLIC;
                $MNU_RIGHTS = ($x==0 ? "" : $row->MNU_RIGHTS);
                $MNU_NOMORS = str_replace($MNU_APPLIC, '', $row->MNU_PARENT);

                if($MNU_RIGHTS==""){
                    $save = true;
                }            
                if($parent=="0"){
                    $MNU_NOMORS = $row->MNU_NOMORS;
                    $save = true;
                    $condition = false;
                }
                if($save){
                    $inputMenu= array($this->fieldFKUSER=>$MNU_LOGINS,"MNU_APPLIC"=>$MNU_APPLIC,"MNU_MENUCD"=>$MNU_NOMORS,"MNU_RIGHTS"=>'V', "MNU_USRNAM"=>$this->username,"MNU_DCREAT"=>$this->datesave);
                    // debug_array($inputMenu);
                    $this->crud->save(
                        $inputMenu,
                        array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS, $this->fieldFKUSER=>$MNU_LOGINS),
                        true,
                        true,
                        true
                    );

	                // function save($data, $pk=null, $protected=true, $unsetpk=true, $forceinsert=true){
                }
                if(!$condition){
                    $keluar = true;
                }
            }else{
                $keluar = true;
            }
            $x++;
        }
        // $this->common->debug_sql();
        // debug_array($inputMenu);
    }
    function deleteusermenu($LOGINS, $APPLIC, $NOMORS){
        $arrAction =array(
            "action"=> "Delete Menu",
            $this->fieldFKUSER=>$LOGINS,
            "MNU_APPLIC"=>$APPLIC,
            "MNU_MENUCD"=>$NOMORS
        );

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table_usermenu, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"1", 
            "log_action"=>$arrAction,
            "log_fkidents"=>$LOGINS
        );
        $this->common->logmodul(false, $arrModul);

        $pk = array($this->fieldFKUSER=>$LOGINS,"MNU_APPLIC"=>$APPLIC,"MNU_MENUCD"=>$NOMORS);
        $this->db->delete($this->table_usermenu,$pk,null,TRUE,true);
    }
    function chkUSRAPP($PARAM){
        $USRIDN = $this->input->post('USRIDN');
        $rsl = $this->m_master->chkUSRAPP($USRIDN,$PARAM);

        $num_rows = $rsl->num_rows();
        if($num_rows>0){
            $row = $rsl->row();
            $return["num_rows"] = 1;
            $return["usr_idents"] = $row->USR_IDENTS;
        }else{
            $return["num_rows"] = 0;
            $return["usr_idents"] = 0;           
        }
    
        echo json_encode($return);

    }
    function chkUSRACT($PARAM=2){
        $USRIDN = $this->input->post('USRIDN');
        $result=$this->m_master->chkUSRACT($USRIDN,$PARAM);

        if($result->num_rows()>0){
            $row = $result->row();
            // 1 (ada data) ~ aktif/tidak ~ user_login
            $hasil = 1 . "~" . $row->USR_ACTIVE . "~" . $row->USR_IDENTS;
        }else{
            $hasil = 0;
        }
        echo $hasil;
    }
    function savepassword(){
        // $this->common->debug_post();
        $IDENTS = $this->input->post('idents');
        $PASSWD = $this->input->post('txtNEWPAS');
        $LAYOUT = $this->input->post('cmbLAYOUT');
        $THEMES = $this->input->post('cmbTHEMES');
        $cmbLANGUAGE = $this->input->post('cmbLANGUAGE');
        $input = array(
                    'USR_LAYOUT'=>$LAYOUT,
                    'USR_THEMES'=>$THEMES,
                    'USR_LANGUAGE'=>$cmbLANGUAGE
                );
        if($PASSWD!=""){
            $input = array_merge($input, array('USR_PASSWD'=>md5($PASSWD)));
        }
        $redirect = '/login/bye';

        $arrAction =array(
            "action"=> "Change Password",
            "USR_LOGINS"=>$LOGINS
        );

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table_user, 
            "username"=>$this->input->post('hidUSRNAM'),
            "log_result"=>1, 
            "keypost"=>"1", 
            "log_action"=>$arrAction,
            "log_fkidents"=>$LOGINS
        );
        $this->common->logmodul(false, $arrModul);

        $this->crud->useTable($this->table_user);
        if(!$this->crud->save($input,array('USR_LOGINS'=>$this->input->post('hidUSRNAM')))){
            $this->common->message_save('save_gagal',null,$redirect);
        }else{
            // $this->common->debug_sql(1);
            $this->common->message_save('save_sukses',null, $redirect);
        }
    }
    function delete(){
        $USR_IDENTS = $this->input->post('idents');
        $input = array("USR_ACCESS"=>2);

        $arrAction =array(
            "action"=> "Change Password",
        );

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table_user, 
            "username"=>$USR_IDENTS,
            "log_result"=>1, 
            "keypost"=>"1", 
            "log_action"=>$arrAction,
            "log_fkidents"=>$USR_IDENTS
        );
        $this->common->logmodul(false, $arrModul);
                
        $this->crud->useTable($this->table_user);
        if(!$this->crud->save($input,array('USR_IDENTS'=>$USR_IDENTS))){
            echo $this->lang->line("confirm_failed");
        }else{
            echo $this->lang->line("confirm_success");
        }
    }
    function popUser($rowselect=false){
        $event = "click";
        $function = 'jvRowselect();';
        $event = $this->input->post('event')=="" ? $event : $this->input->post('event');
        $function = $this->input->post('function')=="" ? $function : $this->input->post('function');

        $gridname = "jqxUsers";
        $this->load->helper('jqxgrid');
        $url ='/master/nosj/getUsers_list';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_IDENTS','aw'=>'10%','label'=>'Identitas', 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_LOGINS','aw'=>'100','label'=>'Login' , 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_FNAMES','aw'=>'250','label'=>'Nama');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'USR_LEVELS','aw'=>'100','label'=>'Tingkat');

        $arrGrid = array('url'=>$url, 
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            'sumber'=>'server',
            'modul'=>'master/user'
        );
        if($rowselect){
            $arrGrid = array_merge($arrGrid, array('event'=>array($event => $function)));
        }
        $content = gGrid($arrGrid);
        echo $content;        
    }
    function tag($level=null){
        if($level!=""){
            $this->db->where("USR_LEVELS", $level);
        }
        $json = autotag(
          array(
            'table'=>$this->table_user, 
            'field'=>array(
                'id'=>'USR_LOGINS',
                'field'=>'USR_FNAMES',
                'where'=>'USR_LOGINS^USR_FNAMES',
                'disabled'=>FALSE)
          )
        );
        // $this->common->debug_sql(1);
    }
    function tagactivedirectory($base=null, $show="name"){
        $this->load->library('authloginad');
        $username = $_GET["q"];
        $arrayBase = array(
            "show"=>$show,
            "username"=>$username
        );
        // =>"OU=DIREKTORAT INVESTASI DAN KEUANGAN,OU=KANTOR PUSAT,OU=KARYAWAN"
        if($base!=null){
            $expBasedn = explode("~", $base);
            foreach($expBasedn as $e){
                $expBasedn_d = explode("--", $e);
                ${"txt".$expBasedn_d[0]} = $expBasedn_d[0];
                ${$expBasedn_d[0]}[] = strtoupper($expBasedn_d[1]);
            }
            foreach($expBasedn as $e){
                $expBasedn_d = explode("--", $e);
                $arrayBase[$expBasedn_d[0]] = ${$expBasedn_d[0]};
            }
            // debug_array($arrayBase);
            // die();
            // $arrayBase = array_merge($arrayBase, array("base_dn"=>$base_dn));
        }
        // die();
        $jdata = $this->authloginad->getOrganization($arrayBase);
        echo $jdata;
    }    
    function tagjenis($JENIS){
        if($JENIS==1){
            $arrFieldtag = array("id"=>"cb_code", "field"=>"cb_name", "where"=>"cb_name");
            $table = "m_cabang";
        }
        if($JENIS==2){
            $this->db->where("mb_is_pks_dapem", 1);
            $arrFieldtag = array("id"=>"mb_code", "field"=>"mb_name", "where"=>"mb_name");
            $table = "m_mitrabayar";
        }
        $arrField = array('table'=>$table, 'field'=>$arrFieldtag, 'protected'=>false);
        $tag = autotag($arrField);
        echo $tag;
    }    
}

// var inputAdd = \"<div class='row' id='myrowchkMNUADD_\"+applic+\"'><div class='form-group'><label for='chkMNUADD_\"+applic+\"' id='lblMNUADD' class='col-sm-12 col-md-12 col-lg-4 control-label'>\"+pjson.A.text+\"</label><div class='col-md-1'><div class='checkbox' style='padding-left:20px;top:5px'><input type='checkbox' name='chkMNUADD_03013NH' id='chkMNUADD_\"+applic+\"' class='control-label'  onclick='javascript:jvTest()'/></div></div></div></div>\"; 
// var inputEdit = \"<div class='row' id='myrowchkMNUEDT_\"+applic+\"'><div class='form-group'><label for='chkMNUEDT_\"+applic+\"' id='lblMNUEDT' class='col-sm-12 col-md-12 col-lg-4 control-label'>\"+pjson.E.text+\"</label><div class='col-md-1'><div class='checkbox' style='padding-left:20px;top:5px'><input type='checkbox' name='chkMNUEDT_\"+applic+\"' id='chkMNUEDT_\"+applic+\"' class='control-label'  /></div></div></div></div>\"; 
// var inputDelete = \"<div class='row' id='myrowchkMNUDEL_\"+applic+\"'><div class='form-group'><label for='chkMNUDEL_\"+applic+\"' id='lblMNUDEL' class='col-sm-12 col-md-12 col-lg-4 control-label'>\"+pjson.D.text+\"</label><div class='col-md-1'><div class='checkbox' style='padding-left:20px;top:5px'><input type='checkbox' name='chkMNUDEL_\"+applic+\"' id='chkMNUDEL_\"+applic+\"' class='control-label'  /></div></div></div></div>\"; 
// var inputView = \"<div class='row' id='myrowchkMNUVIW_\"+applic+\"'><div class='form-group'><label for='chkMNUVIW_\"+applic+\"' id='lblMNUVIW' class='col-sm-12 col-md-12 col-lg-4 control-label'>\"+pjson.V.text+\"</label><div class='col-md-1'><div class='checkbox' style='padding-left:20px;top:5px'><input type='checkbox' name='chkMNUVIW_\"+applic+\"' id='chkMNUVIW_\"+applic+\"' class='control-label'  /></div></div></div></div>\"; 
// var inputJaminan = \"<div class='row' id='myrowchkMNUJMN_\"+applic+\"'><div class='form-group'><label for='chkMNUVIW_\"+applic+\"' id='lblMNUVIW' class='col-sm-12 col-md-12 col-lg-4 control-label'>\"+pjson.V.text+\"</label><div class='col-md-1'><div class='checkbox' style='padding-left:20px;top:5px'><input type='checkbox' name='chkMNUVIW_\"+applic+\"' id='chkMNUVIW_\"+applic+\"' class='control-label'  /></div></div></div></div>\"; 


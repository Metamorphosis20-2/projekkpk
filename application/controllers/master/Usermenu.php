<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usermenu extends MY_Controller {
	var $modul;
	var $identcolumn;
	var $username;
	var $empdeptmn;
	var $datesave;

	function __construct(){
		parent::__construct();
		$this->load->helper(array('ginput','jqxgrid'));
		$this->load->model(array('m_master'));
		$this->modul = $this->router->fetch_class();
		$this->title = "Menu Pengguna";
		$this->table_user = $this->config->item('tbl_user');
		$this->table_menu = $this->config->item('tbl_menu');
		$this->table_common = $this->config->item('tbl_common');
		$this->table_usermenu = $this->config->item('tbl_usermenu');
	}
	public function index(){
		$arrbread = array(
			array('link'=>'/home/welcome','text'=>'Beranda'),
			array('link'=>'#','text'=> 'Menu Pengguna'),
		);

		$bc = generateBreadcrumb($arrbread);
		$this->_render('pages/home',$this->listUsermenu(),'admin',$bc);
	}
	function listUsermenu(){
		$type = 'edit';
		$param = 1;
        $urutan = 0;
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_IDENTS','label'=>'ID','ac'=>false, 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_DESCRE','aw'=>'100%','label'=>'Unit Organisasi');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_PARENT','aw'=>100,'label'=>'Parent','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_APPLIC','aw'=>100,'label'=>'Kode Aplikasi','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_NOMORS','aw'=>100,'label'=>'Nomor','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_EDTBLE','aw'=>100,'label'=>'Bisa Diubah','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_REFERS','label'=>'Refer','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_CHILDN','label'=>'Level','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_HVCHLD','label'=>'Anak','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_GROUPS','label'=>'Kelompok','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_SORTBY','label'=>'Urutan','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_ICONED','label'=>'Icon','ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'MNU_ROUTES','label'=>'Modul','ah'=>true);

        $gridmenu = 'gridmenu';
        $url ="/master/nosj/getMenu_tree/2/2/admin";

        $fn_select = "
        var args = event.args;
        var row = args.row;
        jvSelectTree(row);";

        $event = array(
            "rowSelect" => $fn_select,
        );

        $grid_01 = gGrid(array( 'url'=>$url, 
                                'bisaedit'=>true,
                                'treegrid'=>true,
                                'autoexpand'=>false,
                                'keyfield'=>'MNU_IDENTS',
                                'keyparent'=>'MNU_PARENT',
                                'gridname'=>$gridmenu,
                                'width'=>'100%',
                                'height'=>'570px',
                                'event'=>$event,
                                'col'=>$col,
                                'idCol'=>'MNU_IDENTS',
                                'sumber'=>'detanto'
                                ));
        $formname = "formgw";
        $onSuccess = "
            $('#".$formname."').submit();
        ";
        $rulAPPLIC = array(array('rule'=>'empty','message'=>'Isi Jenis Aplikasi!'));
        $rulDESCRE = array(array('rule'=>'empty','message'=>'Isi Judul Menu!'));
        $rulAPPNEW = array(array('rule'=>'empty','message'=>'Isi Modul!','onSuccess'=>$onSuccess,'formnya'=>$formname));

        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'txt','namanya'=>"MNU_APPLIC", 'label'=>'Jenis Aplikasi', 'size'=>300);
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'txt','namanya'=>"MNU_NOMORS", 'label'=>'Kode Modul', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'txt','namanya'=>"MNU_DESCRE", 'label'=>'Menu', 'value'=>'','validator'=>$rulDESCRE);
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'hid','namanya'=>"MNU_REFERS", 'label'=>'Refer', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'hid','namanya'=>"MNU_CHILDN", 'label'=>'Level', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'hid','namanya'=>"MNU_HVCHLD", 'label'=>'Punya Anak?', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'txt','size'=>200,'namanya'=>"MNU_ROUTES", 'label'=>'Modul', 'value'=>'','validator'=>$rulAPPNEW);
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'hid','size'=>100,'namanya'=>"MNU_GROUPS", 'label'=>'Kelompok', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'txt','size'=>100,'namanya'=>"MNU_SORTBY", 'label'=>'Urutan', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'chk','namanya'=>"MNU_EDTBLE", 'label'=>'Bisa Edit?', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'txt','size'=>200,'namanya'=>"MNU_ICONED", 'label'=>'Icon', 'value'=>'');
        $arrTable2[] = array('group'=>1,'urutan'=>$urutan++,'type'=>($type=='view') ? "view" : 'hid','namanya'=>"MNU_PARENT", 'label'=>'Refer Parent', 'value'=>'');
        
        $arrForm =
          array(
                'type'=>"add",
                'arrTable'=>$arrTable2,
                'status'=> "add",
                'width' => 710,
                'heightnya'=>'100%',
                'nameForm' => 'formmenu',
                'formcommand' => '/save/master/usermenu'
            );        
        $html = generateForm($arrForm, false);
        $kanan = "<div style='overflow:hidden'>".$html."</div>";
        // "bersih"=>array("Reset", "jvReset()","inverse","save"),
        $arrButton = array(
            
            "tambah"=>array("Tambah", "jvTambahMenu()","primary","save"),
            "savess"=>array("Simpan", "jvSave()","info","save"),
            "delete"=>array("Hapus", "jvDelete()","danger","delete")
        );
        $button = generateButton(array('posisi'=>'not','createToolbar'=>false,'button'=>$arrButton));

        $menu ="
            <div class='col-md-6' style='padding-left:12px'>
            " . $grid_01 . "
            </div>
            <div class='col-md-6'>
                <div class=\"panel panel-default\">
                    <div class=\"panel-heading\">".$button."</div>
                    <div class=\"panel-body\" style='height:520px'>".$kanan."</div>
                </div>
            </div>
        ";
        $arrTable[] = array('group'=>1, 'urutan'=>1, 'type'=> 'udf', 'value'=>$menu, 'style'=>'margin-left:10px');  
        $arrForm =
          array(
                'type'=>$type,
                'arrTable'=>$arrTable,
                'status'=> isset($status) ? $status : "",
                'width' => 710,
                'heightnya'=>'100%',
                'nameForm' => $formname,
                'formcommand' => '/save/master/usermenu',
                'tabheight' => '100%',
                'tabname'=> array(
                        '1'=>'fa fa-list^Daftar Menu',
                        ),
            );
        // $content = "<link rel='stylesheet' href='" . base_url(CSS."fontawesome-iconpicker.min.css") . "'>";
        // $content .= "<script src='" . base_url(JS."fontawesome-iconpicker.min.js") . "'>";
        $content = "";
        $content .= "
        <script>
            function jvReset(){
                $('#".$gridmenu."').jqxTreeGrid('clearSelection');
                $('#MNU_APPLIC').val('');
                $('#MNU_NOMORS').val('');
                $('#MNU_DESCRE').val('');
                $('#MNU_REFERS').val('');
                $('#MNU_CHILDN').val('');
                $('#MNU_HVCHLD').val('');
                $('#MNU_CIGNIT').val('');
                $('#MNU_ROUTES').val('');
                $('#MNU_GROUPS').val('');
                $('#MNU_SORTBY').val('');
                $('#MNU_ICONED').val('');
                $('#MNU_EDTBLE').prop('checked',false);
                $('#MNU_APPNEW').prop('checked',false);
                $('#MNU_PARENT').val('');
                $('#MNU_PRODEV').prop('checked',false);            
            }
            function jvSave(){
                if($('#MNU_PARENT').val()==''){
                    swal('Fitur tambah dengan level 0 belum disupport!');
                    return;
                }else{
                    $('#".$formname."').jqxValidator('validate');    
                }
            }
            function jvDelete(){
                var param = {};
                param['MNU_APPLIC'] = $('#MNU_APPLIC').val();
                param['MNU_NOMORS'] = $('#MNU_NOMORS').val();

                if(confirm('Hapus Menu : '+ $('#MNU_DESCRE'). val() +'?')){
                    $.post('/master/usermenu/delete',param,function(rebound){
                        if(rebound){
                            switch (rebound) {
                                case '1':
                                    text = 'Data berhasil dihapus';
                                    break;
                                case '2' :
                                    text = 'Data gagal dihapus';
                                case '3' :
                                    text = 'Menu menjadi referensi menu yang lain, Data gagal dihapus';
                            }
                            swal(text + '!');
                            if(rebound==1){
                                self.location.replace('/master/usermenu');    
                            }
                        }
                    });
                }
            }
            function jvTambahMenu(){
                var rowselected = $('#".$gridmenu."').jqxTreeGrid('getSelection');
                var row = rowselected[0];
                var MNU_APPLIC = row.MNU_APPLIC;
                var MNU_NOMORS = row.MNU_NOMORS;
                var MNU_CHILDN = parseInt(row.MNU_CHILDN)+1;
                var MNU_PARENT = MNU_APPLIC + MNU_NOMORS;
                var MNU_DESCRE = row.MNU_DESCRE;

                if(confirm('Tambah menu dengan parent ' + MNU_DESCRE + '?')){
                    if(row.MNU_ROUTES!='#'){
                        var conf = confirm('Parent mempunyai link!\\nHapus link?');
                    }else{
                        conf = true;
                    }
                    if(conf){
                        $('#MNU_APPLIC').val(MNU_APPLIC);
                        $('#MNU_PARENT').val(MNU_PARENT);
                        $('#MNU_NOMORS').val('');
                        $('#MNU_DESCRE').val('');
                        $('#MNU_REFERS').val(MNU_NOMORS);
                        $('#MNU_CHILDN').val(MNU_CHILDN);
                        $('#MNU_CIGNIT').val('');
                        $('#MNU_GROUPS').val('');
                        $('#MNU_SORTBY').val('');
                        $('#MNU_ICONED').val('');
                        $('#MNU_ROUTES').val('');
                        $('#hidTRNSKS').val('add');
                    }
                }
            }
            function jvSelectTree(row){
                $('#MNU_APPLIC').val(row.MNU_APPLIC);
                $('#MNU_NOMORS').val(row.MNU_NOMORS);
                $('#MNU_DESCRE').val(row.MNU_DESCRE);
                $('#MNU_REFERS').val(row.MNU_REFERS);
                $('#MNU_CHILDN').val(row.MNU_CHILDN);                
                $('#MNU_CIGNIT').val(row.MNU_CIGNIT);
                $('#MNU_GROUPS').val(row.MNU_GROUPS);
                $('#MNU_SORTBY').val(row.MNU_SORTBY);
                $('#MNU_ICONED').val(row.MNU_ICONED);
                $('#MNU_ROUTES').val(row.MNU_ROUTES);
                $('#MNU_PARENT').val(row.MNU_PARENT);

                if(row.MNU_EDTBLE==1){
                    $('#MNU_EDTBLE').prop('checked',true);
                }else{
                    $('#MNU_EDTBLE').prop('checked',false);
                }
                if(row.MNU_APPNEW=='1'){
                    $('#MNU_APPNEW').prop('checked',true);
                }else{
                    $('#MNU_APPNEW').prop('checked',false);
                }
                if(row.MNU_PRODEV==1){
                    $('#MNU_PRODEV').prop('checked',true);
                }else{
                    $('#MNU_PRODEV').prop('checked',false);
                }
            }
        </script>
        ";
        $content .= generateForm($arrForm, true, false);
        return $content;    
	}

	function show($type=null, $index = null, $source=null){
		if($type=='add'){
			$source = $index;///buat unitproduksi
			$keterangan = "Tambah";
		}else{
			$keterangan = "Ubah";
		}
		$content = $this->edit($type, $index, $source);
		$arrbread = array(
			array('link'=>'/home/welcome','text'=>'Beranda'),
			array('link'=>'/master/menupemakai','text'=> 'List Menu Pengguna'),
			array('link'=>'#','text'=> $keterangan),
		);			
		$bc = generateBreadcrumb($arrbread);
		$this->_render('pages/home', $content,'admin',$bc);			
	}
    function save(){
        $MNU_APPLIC = trim($this->input->post('MNU_APPLIC'));
        $MNU_NOMORS = trim($this->input->post('MNU_NOMORS'));
        $MNU_DESCRE = trim($this->input->post('MNU_DESCRE'));
        $MNU_REFERS = trim($this->input->post('MNU_REFERS'));
        $MNU_CHILDN = $this->input->post('MNU_CHILDN')=="" ? 1 : $this->input->post('MNU_CHILDN') ;
        $MNU_HVCHLD = $this->input->post('MNU_HVCHLD')=="" ? 0 : 1;
        $MNU_CIGNIT = trim($this->input->post('MNU_CIGNIT'));
        $MNU_GROUPS = $this->input->post('MNU_GROUPS');
        $MNU_SORTBY = $this->input->post('MNU_SORTBY')=="" ? 1 : $this->input->post('MNU_SORTBY') ;
        $MNU_EDTBLE = $this->input->post('MNU_EDTBLE')==true ? 1 : 0 ;
        $MNU_ICONED = trim($this->input->post('MNU_ICONED'));
        $MNU_APPNEW = $this->input->post('MNU_APPNEW')==true ? 1 : 0 ;
        $MNU_ROUTES = trim($this->input->post('MNU_ROUTES'));
        $MNU_PARENT = trim($this->input->post('MNU_PARENT'));
        $hidTRNSKS = $this->input->post('hidTRNSKS');

        $MNU_APPLIC = ($MNU_APPLIC=="" ? substr($MNU_PARENT,0,10) : $MNU_APPLIC);

        if($MNU_CIGNIT=="#" && $MNU_ROUTES=="#"){
            $MNU_EDTBLE = "0";
        }
        if($hidTRNSKS=="add"){
            if($MNU_NOMORS==""){
                $MNU_NOMORS = $this->crud->getMaxmenu($MNU_APPLIC, $MNU_CHILDN, $MNU_PARENT); 
            }
            $inputMenuParent = array(
                'MNU_HVCHLD'=>1,
                'MNU_CIGNIT'=>'#',
                'MNU_EDTBLE'=>0,
                'MNU_ROUTES'=>'#'
            );            
        }
        // 'MNU_GROUPS'=>$MNU_GROUPS,
        $inputMenu = array(
            'MNU_APPLIC'=>$MNU_APPLIC,
            'MNU_NOMORS'=>$MNU_NOMORS,
            'MNU_DESCRE'=>$MNU_DESCRE,
            'MNU_REFERS'=>$MNU_REFERS,
            'MNU_CHILDN'=>$MNU_CHILDN,
            'MNU_CIGNIT'=>$MNU_CIGNIT,
            'MNU_GROUPS'=>null,
            'MNU_SORTBY'=>$MNU_SORTBY,
            'MNU_EDTBLE'=>$MNU_EDTBLE,
            'MNU_ICONED'=>$MNU_ICONED,
            'MNU_APPNEW'=>$MNU_APPNEW,
            'MNU_ROUTES'=>$MNU_ROUTES,
            'MNU_PARENT'=>$MNU_PARENT
        );
        if($hidTRNSKS=="add"){
            $inputMenu = array_merge($inputMenu, 
                array(
                    "MNU_USRNAM"=>$this->username,
                    "MNU_USRDAT"=>$this->datesave
                )
            );
        }else{
            $inputMenu = array_merge($inputMenu, 
                array(
                    "MNU_UPDNAM"=>$this->username,
                    "MNU_UPDDAT"=>$this->datesave
                )
            );
        }
        // debug_array($inputMenu);
        $redirect = '/master/usermenu';
        $this->crud->useTable($this->table_menu);
        if(!$this->crud->save($inputMenu,array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_NOMORS"=>$MNU_NOMORS))){
            $this->common->message_save('save_gagal',null, $redirect); 
        }else{
            $qryParent = $this->m_master->getMenu_edit($MNU_APPLIC, $MNU_REFERS, 'detanto');
            if($qryParent->num_rows()>0){
                if($qryParent->row()->MNU_PARENT!="0"){
                    $this->crud->save($inputMenuParent,array('MNU_APPLIC'=>$MNU_APPLIC, "MNU_NOMORS"=>$MNU_REFERS));
                }
            }
            $this->common->message_save('save_sukses',null, $redirect); 
        }
    }
    function delete(){
        $MNU_APPLIC = $this->input->post('MNU_APPLIC');
        $MNU_NOMORS = $this->input->post('MNU_NOMORS');
        $affected = 0;

        $this->db->from($this->table_menu);
        $this->db->where("MNU_PARENT", $MNU_APPLIC.$MNU_NOMORS);
        $count = $this->db->count_all_results();

        if($count==0){
            $this->crud->useTable($this->table_menu);
            $this->crud->delete(array("MNU_APPLIC"=>$MNU_APPLIC, "MNU_NOMORS"=>$MNU_NOMORS));
            $affected1 = $this->crud->__affectedRows;
            $this->crud->unsetMe();

            $this->crud->useTable($this->table_usermenu);
            $this->crud->delete(array("MNU_APPLIC"=>$MNU_APPLIC, "MNU_MENUCD"=>$MNU_NOMORS));
            $affected2 = $this->crud->__affectedRows;
            $this->crud->unsetMe();

            $affected = $affected1 + $affected2;

            if($affected<>0){
                echo 1;//"Data berhasil dihapus";
            }else{
                echo 2;//"Data tidak berhasil dihapus";
            }        
        }else{
            echo 3;//"Menu menjadi referensi menu yang lain, Data gagal dihapus!";
        }
    

    }
    
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Referensi extends MY_Controller {
  function __construct(){
    parent::__construct();
    	$this->load->helper('ginput');
    	$this->load->model(array('m_master'));
        $this->modul = $this->router->fetch_class();
        $this->table_common = $this->config->item('tbl_common');
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");

    }
	public function index(){
        $uri = $this->uri->segment(3);

        $rslHEADCD = $this->crud->revertcommon($uri, array("hasil"=>4));
        if(is_array($rslHEADCD)){
            $this->HEADCD = $rslHEADCD["COM_HEADCD"];
            $this->COMDSC = $rslHEADCD["COM_DESCR1"];
        }

        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> 'Data ' . ucfirst($this->COMDSC)),
        );

        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listReferensi($this->HEADCD, $uri),'admin',$bc);  	 
	}
    public function listReferensi($HEADCD, $HEADCODE){
        // debug_array($HEADCD);
        $gridname = "jqxCommon";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Common/list/m_common/'.$HEADCD;
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_IDENTS','aw'=>'10%','label'=>'Identitas', 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_HEADCD','aw'=>'10%','label'=>'Kode', 'ah'=>true, 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_TYPECD','aw'=>'10%','label'=>'Kode', 'ah'=>true, 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_DESCR1','aw'=>'200','label'=>'Description' , 'adtype'=>'text');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_USRNAM','aw'=>'150','label'=>$this->pengguna, 'group'=>$this->info_profil);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'COM_USRDAT','aw'=>'250','label'=>$this->tanggal_buat, 'group'=>$this->info_profil);

        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal.fire('".$this->lang->line("confirm_not_selected")."!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var HEADCD = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'COM_HEADCD');
            var TYPECD = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'COM_TYPECD');
            var DESCR1 = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'COM_DESCR1');
        ";
        $jvEdit = "
            function jvEdit(){
                " . $selrow . "
                $('#frmGrid').attr('action', '/edit/referensi');
                $('#grdHEADCD').val(HEADCD);
                $('#grdIDENTS').val(TYPECD);
                document.frmGrid.submit();
            }
        ";
        $jvView = "
            function jvView(){
                " . $selrow . "
                $('#frmGrid').attr('action', '/view/referensi');
                $('#grdHEADCD').val(HEADCD);
                $('#grdIDENTS').val(TYPECD);
                document.frmGrid.submit();
            }
        ";
        $jvDelete = "
            function jvDelete(){
                " . $selrow . "
                if(id){
                    swal.fire({ 
                        title:'Hapus ' + DESCR1 + '?', 
                        text: null, 
                        icon: 'question', 
                        showCancelButton: true, 
                        confirmButtonText: '".$this->lang->line("Ya")."', 
                        cancelButtonText: '".$this->lang->line("Tidak")."'
                    }).then(result => { 
                        if(result.value) {
                            var prm = {};
                            prm['HEADCD'] = HEADCD;
                            prm['TYPECD'] = TYPECD;
                            $.post('/delete/master/referensi',prm,function(rebound){
                                if(rebound){
                                    swal.fire(DESCR1 + ' ' + rebound + ' dinonaktifkan!')
                                    $('#" . $gridname . "').jqxGrid('updateBoundData');
                                }
                            });
                        } 
                    });	
                }
            }
        ";
        $jvAdd = "
            function jvAdd(){
                // self.location.replace('/add/referensi/" . $HEADCD . "');
                $('#frmGrid').attr('action', '/add/referensi/$HEADCD');
                $('#grdHEADCD').val('".$HEADCD."');
                // $('#grdIDENTS').val(TYPECD);
                document.frmGrid.submit();
            }
        ";
        
        $content = gGrid(array('url'=>$url, 
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            'button'=> 'standar',
            'jvDelete'=>$jvDelete,
            'jvAdd'=>$jvAdd,
            'jvEdit'=>$jvEdit,
            'jvView'=>$jvView,
            'sumber'=>'server',
            'modul'=>'referensi',
            'closeform'=>false
        ));
        $content .= form_input(array('name' => "grdHEADCD",'id'=> "grdHEADCD", 'type'=>'hidden'));
        $content .= form_input(array('name' => "grdPARAMS",'id'=> "grdPARAMS", 'type'=>'hidden','value'=>$HEADCODE));
        $content .= form_close();
        //====== end of grid
        return $content;
    }
    function show($type=null, $index = null, $source=null){
        // $this->common->debug_post();
        $judul = "";
        $linknya = "";
        $grdHEADCD = $this->input->post('grdHEADCD');
        $txtHEADCD = $grdHEADCD =="" ? "0" : $grdHEADCD;
        $column = $this->crud->getCommon_edit($txtHEADCD, "0");
        
        if($column!=""){
            $judul = $column->COM_DESCR1;
            $linknya = strtolower($judul);
        }
        // debug_array($linknya);
        $content = $this->edit($type, $judul, $source);
        $judul = "Ubah";
        if($type=="add"){
            $judul = "Tambah";
        }

        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/master/referensi/'.str_replace(" ", "", $linknya),'text'=>'Daftar '. ucfirst($linknya)),
            array('link'=>'#','text'=>$judul),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    
    function edit($type=null,$param=null,$source=null){
        // $this->common->debug_post();
        $arrColumn = array(
            "txtTYPECD"=>"COM_TYPECD",
            "txtDESCR1"=>"COM_DESCR1",
            "txtDESCR2"=>"COM_DESCR2"
        );
        $txtHEADCD = $this->input->post('grdHEADCD');
        $grdPARAMS = $this->input->post('grdPARAMS');
        if($type!="add"){
            $txtTYPECD = $this->input->post('grdIDENTS');
            $column = $this->crud->getCommon_edit($txtHEADCD, $txtTYPECD);
        }
        foreach($arrColumn as $input=>$field){
            if(isset($column)){
                ${$input} = $column->{$field};
            }else{
                ${$input} = "";
            }
        }
        $urutan = 0;
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'hidHEADCD','label'=>'Headcd','size'=>'170','value'=>$txtHEADCD);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'txt','namanya'=>'hidTYPECD','label'=>'Kode','size'=>'170', "value"=>$txtTYPECD, 'readonly'=>true);
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'txt','namanya'=>'txtDESCR1','label'=>"Keterangan " . $param,'size'=>'190', "value"=>$txtDESCR1);


        // if($txtDESCR2!=""){
        //     $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'num','namanya'=>'txtDESCR2','label'=>'Keterangan','size'=>'170', "value"=>$txtDESCR2);    
        // }else{
            switch($grdPARAMS){
                case "jenislayanan":
                    $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'num','namanya'=>'txtDESCR2','label'=>'Plafon','size'=>'170', "value"=>$txtDESCR2);        
                    break;
                default:
                    if($txtDESCR2!=""){
                        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'num','namanya'=>'txtDESCR2','label'=>'Keterangan','size'=>'170', "value"=>$txtDESCR2);    
                    }
                    break;                
            }
        // }
        $arrTable[] = array('group'=>1,'urutan'=>$urutan++,'type'=>'hid','namanya'=>'grdPARAMS','label'=>"Keterangan " . $param,'size'=>'190', "value"=>$grdPARAMS);
        

        $arrForm =
          array(
                'type'=>$type,
                'arrTable'=>$arrTable,
                'status'=> "add",
                'width' => 710,
                'formcommand' => '/save/master/referensi',                
                'tabname'=> array('1'=>'fas fa-folder^Data ' . $param),
            );
        $content = ""; 
        if($type!="view"){
            $content .= createbutton();//generateButton(array('createToolbar'=>true,'check'=>'AE'));    
        }
        $content .= "<script>
            function jvSave(){
                document.formgw.submit();
            }
        </script>";
        $content .= generateForm($arrForm);
        return $content;
    }
    function save(){
        // $this->common->debug_post();
        $hidHEADCD = $this->input->post('hidHEADCD');
        $hidTYPECD = $this->input->post('hidTYPECD');
        $txtDESCR1 = $this->input->post('txtDESCR1');
        $page = $this->input->post('grdPARAMS');
        // $txtDESCR2 = $this->input->post('');
        $txtDESCR2 = preg_replace('/[^a-z\d\. ]/i', '', $this->input->post('txtDESCR2'));
        $hidTRNSKS = $this->input->post('hidTRNSKS');

        if($hidTRNSKS=="add"){
            $hidTYPECD = $this->crud->getMaxcommon($hidHEADCD) + 1;
            $input = array(
            "COM_USRNAM"=>$this->username,
                "COM_USRDAT"=>$this->datesave
            );
        }else{
            $input = array(
                "COM_UPDNAM"=>$this->username,
                "COM_UPDDAT"=>$this->datesave
            );
        }

        $input = array_merge($input, array(
            "COM_HEADCD"=>$hidHEADCD,
            "COM_TYPECD"=>$hidTYPECD,
            "COM_DESCR1"=>$txtDESCR1,
            "COM_DESCR2"=>$txtDESCR2,
        ));
        // debug_array($input);
        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table_common, "POST"=>$input, "username"=>$this->username, "pk"=>array("COM_HEADCD"=>$hidHEADCD,"COM_TYPECD"=>$hidTYPECD)));        
        $redirect = "/master/referensi/".$page;
        $this->crud->useTable($this->table_common);
        if($this->crud->save($input, array("COM_HEADCD"=>$hidHEADCD,"COM_TYPECD"=>$hidTYPECD))){
            $this->common->message_save('save_sukses',null, $redirect);
        }
    }
    function delete(){
        $HEADCD = $this->input->post('HEADCD');
        $TYPECD = $this->input->post('TYPECD');

        $this->crud->useTable($this->table_common, false);
        $delete = array("COM_is_deleted" => 1);
        $this->crud->save($delete, array("COM_HEADCD"=>$HEADCD, "COM_TYPECD"=>$TYPECD), false);
        $this->crud->unsetMe();

        if($this->crud->__affectedRows <>0){
            echo "Data berhasil dihapus";
        }else{
            echo "Data tidak berhasil dihapus";
        }
    }
    public function tag($headcd=0, $cmp_idents=0){
        if($cmp_idents!=0){
            $json = autotag(
                array(
                    'model'=>"m_kpi",
                    "function"=>"getReferensi_tag",
                    'protected'=>false
                )
            );
        }else{
            if($headcd!=0){
                $this->db->where("COM_HEADCD", $headcd);
                $this->db->where("COM_TYPECD <> 0");
            }
            $json = autotag(
                array(
                    'table'=>"t_mas_common", 
                    'field'=>array('id'=>'COM_TYPECD','field'=>'COM_DESCR1','where'=>'COM_DESCR1','disabled'=>FALSE),
                    'protected'=>false
                )
            );
        }
    }
}
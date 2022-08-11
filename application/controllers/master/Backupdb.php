<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Backupdb extends MY_Controller
{
    function __construct(){
        parent::__construct();
    	$this->load->helper('ginput');
    	$this->load->model(array('m_master'));
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->info_profil = $this->lang->line("info_profil");

    }
	public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Backup List"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listBackup(),'admin',$bc);  	 
	}
    public function listBackup(){
        $gridname = "jqxBackup";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Backup/list/m_master';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'bck_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"bck_filename", "aw"=>200, "label"=> "File Name","adtype"=>"text");
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"bck_notes", "aw"=>500, "label"=> "Notes","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'bck_usrnam','aw'=>'120','label'=>$this->pengguna, 'group'=>$this->info_profil,'ga'=>'center');
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'bck_usrdat','aw'=>'120','label'=>$this->tanggal_buat, 'group'=>$this->info_profil,'ga'=>'center');

        $content = gGrid(array('url'=>$url, 
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            'button'=> 'standar',
            'sumber'=>'server',
            'modul'=>'master/backupdb'
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
            array('link'=>'/master/grant','text'=>$this->grn_list),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }
    public function edit(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $this->load->dbutil();

        $filename = "maleo_".date("Ymd-His").'.sql';

        $prefs = array(     
            'format'      => 'zip',             
            'filename'    => $filename
        );

        $backup =& $this->dbutil->backup($prefs); 

        $db_name = "maleo_backup_".date("Ymd-His") .'.zip';
        $save = FCPATH.'assets/db/'.$db_name;
        $file_size = filesize($save);

        $inputdetail["bck_usrnam"] = $this->username;
        $inputdetail["bck_filename"] = $filename;
        // $inputdetail["bck_filesize"] = $file_size;

        $this->crud->useTable('t_mas_backup');
        $this->crud->save($inputdetail);

        $this->load->helper('file');
        write_file($save, $backup);

        $this->load->helper('download');
        force_download($db_name, $backup);
    }
    function xcron(){

        $this->load->helper('file');
        $this->load->helper('directory');
        $map = directory_map('./database/');
        $loop = 0;
        foreach($map as $key){
            $file = "./database/" . $key;
            $fileinfo = get_file_info($file);
            $size = $fileinfo["size"];

            $Rows[] = array("file_name"=>$key, "file_size"=>$size);
            $loop++;
        }

        $json["TotalRows"] = $loop;
        $json["Rows"] = $Rows;
        debug_array(json_encode($json));
    }
    function cron(){
        $gridname = "jqxBackup";
        $this->load->helper('jqxgrid');
        $url ='/master/nosj/getBackupcron_list';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'CI_Rownum','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"bck_filename", "aw"=>200, "label"=> "File Name","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"bck_filesize", "aw"=>100, "label"=> "Size", 'cf'=>'f', 'adtype'=>'number', 'ga'=>'right');

        
        $content = gGrid(array('url'=>$url, 
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            // 'button'=> 'standar',
            'sumber'=>'detanto',
            'modul'=>'master/backupdb'
        ));
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Backup List (CRON)"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);  
    }
}

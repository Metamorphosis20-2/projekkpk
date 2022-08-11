<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Beranda extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
        $this->load->helper(array('ginput','chartjs'));
    	$this->load->model(array('m_asesmen', 'm_master'));
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
        $this->usrkabptn = $this->session->userdata("USR_KABPTN");

        $this->unitkerjadesc = $this->session->userdata('USR_UNITKERJA_DESC');
        
        $this->usr_level = $this->session->userdata("USR_LEVELS");
        $this->usr_idents = $this->session->userdata("USR_IDENTS");
        $this->table = "t_asm_asesmen_jawaban";

        $field = array("asm_idents", "asm_tahun");

        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            "empty"=>false
        );
        
        $this->optAsesmen = $this->crud->getGeneral_combo($arrayOpt);        
    }
    public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
        );
        $bc = generateBreadcrumb($arrbread);
        $content = "<style>
        #divcon {


            display: flex;
            
            
            justify-content: center;
            
            
            }        
        </style>";
        if($this->usr_level>2){
            $content .= $this->unitkerja($this->usrunitkerja);
        }else{
            $content .= $this->listBerandaAll();
        }        
        $this->_render('pages/home', $content,'admin',$bc);
    }
    function listBerandaAll(){
        $rslProvince = $this->m_asesmen->getAsesmen_unitkerja();
        $table = '<table class="table" style="width:90%;margin-left:20px;padding-right:50px">';
        $table = '<div class="d-flex flex-column-fluid">';
        if($rslProvince->num_rows()>0){
            foreach($rslProvince->result() as $key=>$value){
                $asm_idents = $value->asm_idents;
                $unt_idents = $value->unt_idents;
                $unt_unitkerja = $value->unt_unitkerja;
                $asm_periode_end = $value->asm_periode_end;
                // $table .= 
                $table .= '
                <div class="col-xl-3">
                    <div class="card card-custom gutter-b card-stretch">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center py-1">
                                <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pr-3">
                                    <a href="javascript:jvView('.$unt_idents.')" class="text-dark font-weight-bolder text-hover-primary font-size-h5">'.$unt_unitkerja.'</a>
                                    <span class="text-muted font-weight-bold font-size-lg">Berakhir : '.$asm_periode_end.'</span>
                                </div>
                            </div>
                        </div>
                    </div>            
                </div>
                ';
            }
            // $table .= '</table>';
            $table .= '<script>
            function jvView(unt_idents){
                self.location.replace("/proses/beranda/unitkerja/" + unt_idents + "/0");
            }
            </script>';
        }else{
            $table .= '
                <div class="">
                    <span class="label label-danger label-inline mr-2 label-xl font-size-h5">Data Asesmen tidak ditemukan</span>
                </div>
            ';
        }
        $table .= '</div>';
        return $table;
    }
    function unitkerja($unt_idents, $return=true){
        $unt_unitkerja = null;
        $rsl = $this->m_master->getUnitkerja_edit($unt_idents);
        if($rsl!=null){
            $unt_unitkerja = $rsl->unt_unitkerja;
        }

        $content = $this->listBerandaView($unt_idents, $unt_unitkerja);
        $text = "Unit Kerja " . $unt_unitkerja;
        if($unt_unitkerja==null){
            if($this->session->userdata("USR_AUTUNIT")!=1 && $this->usr_level>=2){
                $content = '
                    <style>
                    .xcenter {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        padding: 10px;
                        }                    
                    </style>
                    <div class="xcenter">
                        <span class="label label-danger label-inline mr-2 label-xl font-size-h5">Anda tidak mempunyai Otorisasi Unit Kerja</span>
                    </div>
                ';
            }
        }
        if(!$return){
            $arrbread = array(
                array('link'=>'/home/welcome','text'=>'Beranda'),
                array('link'=>'#','text'=> $text),
            );
            $bc = generateBreadcrumb($arrbread);
            $this->_render('pages/home', $content,'admin',$bc);
        }else{
            return $content;
        }
        
    }
    function listBerandaView($unt_idents, $unt_unitkerja){
        $formname = "formBeranda";
        $tree_element = "idk_idents";
        $group = 1;
        $urutan = 0;
        $table = null;
        $script = "<script>
        $(document).ready(function () {
            $('#lok_asmidents').on('select2:select', function (e) {
                var asm_idents=$('#lok_asmidents').val();
                if(".$unt_idents."==''){
                    unt_idents = $('#grf_unitkerja').val();
                    if(unt_idents=='' || unt_idents=='0'){
                        swal.fire({ 
                            title:'Unit Kerja Harus diisi', 
                            icon: 'error', 
                        });
                        return;
                    }
                }else{
                    unt_idents ='$unt_idents'; 
                }
                $('#".$tree_element."').jqxTree('destroy'); 
                $('#div".$tree_element."').append('<div id=\"".$tree_element."\"></div>');
                jvDisplayTreeidk_idents(asm_idents, unt_idents);
            });
            $('#grf_unitkerja').on('select2:select', function (e) {
                var asm_idents=$('#lok_asmidents').val();
                if(".$unt_idents."==''){
                    unt_idents = $('#grf_unitkerja').val();
                    if(unt_idents=='' || unt_idents=='0'){
                        swal.fire({ 
                            title:'Unit Kerja Harus diisi', 
                            icon: 'error', 
                        });
                        return;
                    }
                }else{
                    unt_idents ='$unt_idents'; 
                }
                if(asm_idents!=null && asm_idents!=0){
                    $('#".$tree_element."').jqxTree('destroy'); 
                    $('#div".$tree_element."').append('<div id=\"".$tree_element."\"></div>');
                    jvDisplayTreeidk_idents(asm_idents, unt_idents);
                }
            });
        });
        function jvShowDetail(id,asm_idents){
            var param = {};
            if(isNaN(id)){
                param['jenis'] = id;
                param['asm_idents'] = asm_idents;
                param['unt_idents'] = unt_idents;
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                $.post('/proses/beranda/grafik', param,function(data){
                    $('#windowProses').jqxWindow('close');
                    $('#divgrafik').empty();
                    $('#divgrafik').html(data);
          
                });
            }

        }
        </script>";
        $field = array("asm_idents", array("asm_periode"=>array("asm_tahun", "asm_periode")));
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            "empty"=>true
            // "empty"=>FALSE
        );
        
        $optAsesmen = $this->crud->getGeneral_combo($arrayOpt);

        $arrField = array(
            "lok_asmidents"=>array("group"=>1, "urutan"=>2, "label"=>"Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen),
        );
        if($this->session->userdata("USR_AUTUNIT")==1 && $this->usr_level>=2){
            $field = array("unt_idents", "unt_unitkerja");
            $arrayOpt = array(
                "type"=> 1,
                "table"=> "t_mas_unitkerja",
                "field"=> $field,
                "protected"=>true,
                "separator"=>array("[","]")
            );
    
            $optUnitkerja = $this->crud->getGeneral_combo($arrayOpt);
            $arrField = array_merge($arrField, array("grf_unitkerja"=>array("group"=>1, "urutan"=>1, "label"=>"Unit Kerja","type"=>"cmb","option"=>$optUnitkerja, "size"=>"400px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Unit Kerja tidak boleh kosong"))));
            // $arrField[] = );
        }        
        $arrTable = $this->common->generateArray($arrField, $this->table, null, false);

        $table = generateTree(
            array(
                'as_function'=>true,
                'parameter_function'=>"idents=null, unt_kerja=null",
                'initialize'=>true,
                'name'=>$tree_element,
                'urlcustom'=>'/asesmen/nosj/getKategoritree/',
                'select'=>true,
                'width'=>250,
                'expandAll'=>true,
                'create'=>true,
                "event"=>array("itemClick"=>array("addscript"=>"var asm_idents = $('#lok_asmidents').val();", "function"=>"jvShowDetail(id, asm_idents)"))
            )
        );

        $arrTable[] = array("group"=>$group,'urutan'=>9999, "type"=>"udf", "namanya"=>"idk_idents", "value"=>"<div id='div".$tree_element."' style='margin-top:30px'>".$table ."</div>");
        $arrForm =
            array(
                    'type'=>"view",
                    'arrTable'=>$arrTable,
                    'status'=> isset($status) ? $status : "",
                    'nameForm'=>$formname,
                    'width'=>'70%',
                    'formcommand' => '/asesor/penilaian/save' ,
                    'tabname'=> array(
                        '1'=>"fas fa-folder-open^".$unt_unitkerja
                    ),
                );
        $content = generateForm($arrForm);
        $content .= $script;
        $content .= form_close();

        $content = createportlet(array("content"=>$content,"title"=>"Analisa","caption_helper"=>date('Y'), "icon"=>"fas fa-globe", "class"=>"portletGrafik"));
        $content = '
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-custom card-stretch card-stretch-half gutter-b">
                    <div class="card-body p-0">
                    '.$content.'
                    </div>
                </div>                   
            </div>
            <div class="col-xl-6">
                <div class="card card-custom p-0 card-stretch gutter-b">
                    <div id="divgrafik" style="height:600px">
                    </div>
                </div>
            </div>                
        </div>';        
        return $content;

    }
    function listBerandaViews(){
        $hidKABPTN = $this->input->post("hidKABPTN");
        $hidIDENTS = $this->input->post("hidIDENTS");
        $hidPROVNC = $this->input->post("hidPROVNC");
        $return = false;
        if($hidKABPTN==null){
            $hidKABPTN = $this->usrkabptn;
            $return = true;
        }
        $rslKabupaten = $this->m_master->getUnitkerja_edit(1, $hidKABPTN);

        if($rslKabupaten!=null){
            $KAB_NAMESS = $rslKabupaten->KAB_NAMESS;
        }
        $grafik_Kategori = '<table class="table" style="width:90%;margin-left:20px;padding-right:50px">';
        $rslKelompok = $this->m_asesmen->getKelompokKategori($hidKABPTN);
        $rslClass = array("primary","info", "warning", "success", "danger");
        $loop = 0;
        $idk_progress_total = 0;
        foreach($rslKelompok->result() as $keyKelompok=>$valueKelompok){
            $idk_idents = $valueKelompok->idk_idents;
            $idk_nama = $valueKelompok->idk_nama; 
            $idk_progress = $valueKelompok->idk_progress;
            if($loop==5){
                $loop = 0;
            }
            if($idk_progress!=0){
                $lihat = '<a href="javascript:jvView('.$idk_idents.',\''.$idk_nama.'\')"><i class="fas fa-eye" style="color:#0275d8"></i></a>';
            }else{
                $lihat = null;
            }

            $grafik_Kategori .= '
            <tr>
                <td><i class="fa fa-genderless text-'.$rslClass[$loop].' icon-xl"></i></td>
                <td><span class="font-weight-bolder text-dark-75 pl-3 font-size-lg">'.$idk_nama.'</span></td>
                <td><progress value="'.$idk_progress.'" max="100" style=""></progress></td>
                <td>'.$idk_progress.'%</td>
                <td>'.$lihat.'</td>
            </tr>
            ';
            $idk_progress_total = $idk_progress_total + $idk_progress;
            $loop++;
        }
        $grafik_Kategori .= '</table>';

        if($idk_progress_total>0){
            $flotarea = array(
                    'id'    =>  'PesertaAkses',
                    'chart' =>  'spider',
                    'width' =>  "100%",    //Setting a custom width
                    'labelling' => 'Score',
                    'warna'=>'#5cb85c',
                    'resultset'=>$rslKelompok,
                    'fields'=>array("descre"=>"idk_nama", "values"=>"idk_progress"),
            );

            // $divAccess = createportlet(array("content"=>display_highchart($flotarea),"title"=>"User Access","caption_helper"=>date('Y'), "icon"=>"fas fa-globe", "class"=>"portletGrafik"));
            $divAccess = display_chart($flotarea);
        }else{
            $divAccess = '
            <style>
            .centered {
                position: relative;
                top: 50%;
                left: 50%;
                /* bring your own prefixes */
                transform: translate(-50%, -50%);
              }    
            </style>
            <div class="centered bg-primary text-white py-2 px-4">Belum Ada Kuesioner yang diinput</div>
            ';
        }
        $grafik_Kategori = displayGrid(array("row"=>1, "column"=>2, "grid"=>array($grafik_Kategori, $divAccess)));
        $arrTabs = array(
            "id"=>"Dashboard",
            "ajax"=>false,
            "utama"=>"/home/dashboard_detail",
            "arrTabs" => array(
                "fas fa-chart-pie^Grafik Kategori"=>array("data"=>$grafik_Kategori)
              )
          );
        $content = $grafik_Kategori;
        $content .='
            <script>
                function jvView(idents, idk_nama){
                    var param = {};
                    param["idk_idents"] = idents;
                    $("#imgPROSES").show();
                    $("#windowProses").jqxWindow("open");
                    $.post("/proses/beranda/showdetail", param,function(data){
                        $("#windowProses").jqxWindow("close");
                        $("#modal-title-Kategori").html("Kelompok Kategori " + idk_nama);
                        $("#modal-body-Kategori").html(data);
                        window.$("#modalQuestion").modal("show");
                    });
                }
            </script>
            <style>
                div.dataTables_processing { 
                    z-index: 1; 
                }
                .modal {
                    padding: 0 !important; // override inline padding-right added from js
                }
                .modal .modal-dialog {
                    width: 50%;
                    max-width: none;
                    height: 95%;
                    margin-top:10;
                    margin-right: 0;
                }
                .modal .modal-content {
                    height: 100%;
                    border: 0;
                    border-radius: 0;
                }
                .modal .modal-body {
                    overflow-y: auto;
                }
            </style>
            <div class="modal" id="modalQuestion" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 id="modal-title-Kategori" class="modal-title" id="headermodal"><i class="fas fa-question-circle" style="color:red"></i> Kuesioner Kelompok Kategori <b></b></h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i aria-hidden="true" class="ki ki-close"></i>
                            </button>
                        </div>
                        <div class="modal-body" id="modal-body-Kategori">
                        </div>
                        <div class="modal-footer" id="modal-footer-Kategori">
                        Keterangan : 
                        <a class="btn btn-success btn-sm" style="cursor: default;"><i class="fas fa-thumbs-up"></i> Siap</a>
                        <a class="btn btn-warning btn-sm" style="cursor: default;"><i class="fas fa-exclamation-circle"></i>Hampir Siap</a>
                        <a class="btn btn-danger btn-sm" style="cursor: default;">&nbsp;<i class="fas fa-ban"></i>Belum Siap</a>
                        </div>
                    </div>
                </div>
            </div>';
        if($return){
            return $content;
        }else{
            $arrbread = array(
                array('link'=>'/home/welcome','text'=>'Beranda'),
                array('link'=>'/proses/beranda/provinsi/'.$hidIDENTS.'/'.$hidPROVNC,'text'=>'Daftar Kabupaten'),
                array('link'=>'#','text'=> $KAB_NAMESS),
            );
            $bc = generateBreadcrumb($arrbread);
            $this->_render('pages/home', $content,'admin',$bc);
        }
    }
    function grafik(){
        // $this->common->debug_post();
        $jenis = $this->input->post("jenis");
        $arrJenis = explode("-", $jenis);
        $jenis = $arrJenis[0];
        $asm_idents = $this->input->post("asm_idents");
        $level = $arrJenis[1];
        $unt_idents = $this->input->post("unt_idents");
        $rslKelompok = $this->m_asesmen->getGrafik($jenis, $asm_idents, $level, $unt_idents);
        // $this->common->debug_sql(1);
        $flotarea = array(
            'id'    =>  'PesertaAkses',
            'chart' =>  'spider',
            'width' =>  "100%",    //Setting a custom width
            'labelling' => 'Ya',
            // 'warna'=>'#5cb85c',
            'resultset'=>$rslKelompok,
            'fields'=>array("descre"=>"idk_nama", "values"=>"idk_jawab"),
        );

        $divAccess = display_chart($flotarea);
        $divAccess .= "<style>
        #chart{
            text-align: center;
            height:600px
          }        
          canvas{
            margin: 0 auto;
          }
        </style>";
        echo $divAccess;
    }
    function showdetail(){
        $idents = $this->input->post("idk_idents");
        $rslKategori = $this->m_asesmen->getKelompokKategori_detail($idents, $this->usrkabptn);
        $table = "";
        if($rslKategori->num_rows()>0){
            $script = '
            <script type="text/javascript" src=' . base_url(PLUGINS."DataTables/datatables.min.js") .'></script>
            <link rel="stylesheet" href=' . base_url(PLUGINS."DataTables/datatables.min.css"). ' type="text/css">
            <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/fontawesome.min.css"). ' type="text/css">
            <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/solid.css"). ' type="text/css">
            ';
    
            $table = '<table id=table_'.$idents.' class="display" style="width:100%;display:none">';
            $table .= "
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Kesiapan</th>
                </tr>
            </thead>";
            $table .= "<tbody>";
            $loop = 0;
            $loop_detail = 0;
            
            $idk_idents_temp = null;
            $idk_nama_temp = null;
            $hidden = null;
            $num_rows = $rslKategori->num_rows();
            // debug_array($rslKategori->result());
            foreach($rslKategori->result() as $key=>$value){
                $idk_idents = $value->idk_idents;
                $idk_nama = $value->idk_nama;
                $cnt_jwb_ya = $value->cnt_jwb_ya;
                $cnt_jwb_ya_desc = $cnt_jwb_ya;
                if($cnt_jwb_ya>6 && $cnt_jwb_ya<10){
                    $cnt_jwb_ya = '<a class="btn btn-success btn-sm" style="cursor:default;">&nbsp;<i class="fas fa-thumbs-up"></i></a>';
                }else{
                    if($cnt_jwb_ya>3 && $cnt_jwb_ya<7){
                        $cnt_jwb_ya = '<a class="btn btn-warning btn-sm" style="cursor:default;">&nbsp;<i class="fas fa-exclamation-circle"></i></a>';
                    }else{
                        if($cnt_jwb_ya<4){
                            $cnt_jwb_ya = '<a class="btn btn-danger btn-sm" style="cursor: default;">&nbsp;<i class="fas fa-ban"></i></a>';
                        }
                    }
                }
                $urutan = $loop_detail+1;
                $table .= "<tr>";
                $table .= "     <td>" . $idk_nama . "</td>";
                $table .= "     <td>" . $cnt_jwb_ya . "</td>";
                $table .= "</tr>";
            }
            $table .= "</tbody>";
            $table .= "</table>";
        }
        $return = $script . $table;
        $return .= "
        <script>
            var oDT_" . $idents . " = $('#table_".$idents."').DataTable(
            {  'autoWidth':false, 'paging': false,'ordering': false, 'info':false, 'searching':false, 

                columns: [
                    {   data: 'idk_nama',width: '90%'},
                    {   data: 'cnt_jwb_ya',width: '90'},                    
                ]
            });
            $('#table_".$idents."').show();
        </script>
        ";
        echo $return;
        // debug_array($rslKategori);
    }
}
/*
<div class="timeline timeline-6 mt-3">
    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">08:42</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-warning icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Text-->
        <div class="font-weight-mormal font-size-lg timeline-content text-muted pl-3">
            Outlines keep you honest. And keep structure
        </div>
        <!--end::Text-->
    </div>
    <!--end::Item-->

    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">10:00</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-success icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Content-->
        <div class="timeline-content d-flex">
            <span class="font-weight-bolder text-dark-75 pl-3 font-size-lg">AEOL meeting</span>
        </div>
        <!--end::Content-->
    </div>
    <!--end::Item-->

    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">14:37</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-danger icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Desc-->
        <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
            Make deposit
            <a href="#" class="text-primary">USD 700</a>.
            to ESL
        </div>
        <!--end::Desc-->
    </div>
    <!--end::Item-->

    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">16:50</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-primary icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Text-->
        <div class="timeline-content font-weight-mormal font-size-lg text-muted pl-3">
            Indulging in poorly driving and keep structure keep great
        </div>
        <!--end::Text-->
    </div>
    <!--end::Item-->

    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">21:03</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-danger icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Desc-->
        <div class="timeline-content font-weight-bolder text-dark-75 pl-3 font-size-lg">
            New order placed <a href="#" class="text-primary">#XF-2356</a>.
        </div>
        <!--end::Desc-->
    </div>
    <!--end::Item-->

    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">23:07</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-info icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Text-->
        <div class="timeline-content font-weight-mormal font-size-lg text-muted pl-3">
            Outlines keep and you honest. Indulging in poorly driving
        </div>
        <!--end::Text-->
    </div>
    <!--end::Item-->

    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">16:50</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-primary icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Text-->
        <div class="timeline-content font-weight-mormal font-size-lg text-muted pl-3">
            Indulging in poorly driving and keep structure keep great
        </div>
        <!--end::Text-->
    </div>
    <!--end::Item-->

    <!--begin::Item-->
    <div class="timeline-item align-items-start">
        <!--begin::Label-->
        <div class="timeline-label font-weight-bolder text-dark-75 font-size-lg">21:03</div>
        <!--end::Label-->

        <!--begin::Badge-->
        <div class="timeline-badge">
            <i class="fa fa-genderless text-danger icon-xl"></i>
        </div>
        <!--end::Badge-->

        <!--begin::Desc-->
        <div class="timeline-content font-weight-bolder font-size-lg text-dark-75 pl-3">
            New order placed <a href="#" class="text-primary">#XF-2356</a>.
        </div>
        <!--end::Desc-->
    </div>
    <!--end::Item-->
</div>
*/
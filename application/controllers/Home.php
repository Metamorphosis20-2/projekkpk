<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    var $deptmn;
    var $levels;
    var $cabang_name;
    var $mitra_name;
    function __construct(){
        parent::__construct();
        $this->load->helper(array('ginput','file','chartjs'));
        $this->load->model(array('m_grafik','m_asesmen'));
        $this->app_numbr = $this->session->userdata('app_numbr');
    } 
    public function index(){
        $this->welcome();
    }
    function welcome(){
        $arrbread = array(
            array('link'=>'#','text'=>'Beranda'),
        );
        $content = $this->dashboard();
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxHeight'=>'800px','overflow'=>'auto'));
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content, 'admin', $bc);
    }
    function dashboard(){
        $udi_maleo = $this->dashboard_detail("dashboard", "return");
        $arrTabs = array(
          "id"=>"Dashboard",
          "ajax"=>true,
          "utama"=>"/home/dashboard_detail",
          "arrTabs" => array(
            "fas fa-chart-pie^Dashboard"=>array("data"=>$udi_maleo)
            )
        );
        $content = generateTabjqx($arrTabs);
        return $content;
    }
    function dashboard_detail($parameter, $return="echo"){
        $udi = null;
        if($this->session->userdata('USR_LEVELS')=='1'){
          $usernya = "All user";
          $unornya = "Semua Unor";
        }else{
          $usernya = $this->session->userdata('USR_FNAMES');
          $unornya = "";
        }
        switch($parameter){
            case "dashboard":
                $rslPerbulan = $this->m_grafik->getUseraccess_list();
                $flotarea = array(
                        'id'    =>  'PesertaAkses',
                        'chart' =>  'line',
                        'xAxistitle'=>'Month',
                        'yAxistitle'=>'Access',
                        'width' =>  "90%",    //Setting a custom width
                        'height' => '90%',    //Setting a custom height,
                        'legend' => 'User Access',
                        'showvalue'=> 'false',
                        'warna'=>'#3466f3',
                        'resultset'=>$rslPerbulan,
                        'title'=>'User Akses',
                        'fields'=>array("descre"=>"USL_USRDAT", "values"=>"USL_TOTAL")
                );
                
                

                $divAccess = createportlet(array("content"=>display_chart($flotarea),"title"=>"User Access","caption_helper"=>date('Y'), "icon"=>"fas fa-globe", "class"=>"portletGrafik"));
                
                // $udi = displayGrid(array("row"=>1, "column"=>3, "dstyle"=>"height:90%;width:90%", "grid"=>array(display_chart($flotarea), "asdfasdfasdf", "asdf,hkbsxcv,nmb")));
                $table = null;
                if($this->usrlevel>2){
                    $rsl = $this->m_asesmen->getTaskIncomplete();
                    $arrKeterangan = array(
                        "1"=>"Pertanyaan belum dijawab",
                        "2"=>"Pertanyaan Ditolak",
                        "3"=>"Pertanyaan Disetujui",
                        "4"=>"Belum Disetujui",
                    );
                    $rslClass = array("dark","info", "warning", "success", "danger");
                    // debug_array($rsl->result(), false);
                    if($rsl->num_rows()>0){
                        $loop = 0;
                        $loopcolor =0;
                        $header_temp = null;
                        foreach($rsl->result() as $key=>$value){
                            $header = $arrKeterangan[$value->Keterangan];
                            if($loop==0){
                                $table .= '<table><tr><td><span class="label label-xl label-'.$rslClass[$loop].' label-inline mr-2">'.$header.'</span></td></tr><tr>';
                            }
                            if($loopcolor==5){
                                $loopcolor = 0;
                            }
                
                            if($header!=$header_temp){
                                if($header_temp!=null){
                                    $table .= '</tr><tr><td style="padding-top:20px;"><span class="label label-xl label-'.$rslClass[$loop].' label-inline mr-2">'.$header.'</span></td></tr>';
                                    // $loop=0;
                                }
                            }
                            $table .= '<tr><td style="padding-top:10px;" ><a href="/proses/kuesioner">' . $value->idk_nama . ' (' . $value->total_pertanyaan . ')</a></td></tr>';
                            $header_temp = $header;
                            $loop++;
                            $loopcolor++;
                        }

                        // $table = $header . "<br><br>" . $table;
                    }
                }
                $udi = '
                <div class="row">
                    <div class="col-xl-4">
                        '.$divAccess.'
                    </div>
                    <div class="col-xl-8">
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <span class="card-icon">
                                        <i class="flaticon2-chat-1 text-primary"></i>
                                    </span>
                                    <h3 class="card-label text-primary">Task</h3>
                                </div>
                            </div>
                        <div class="separator separator-solid separator-info opacity-20"></div>
                            <div class="card-body text-white">
                            ' . $table . '
                            </div>
                        </div>
                    </div>
                </div>
                ';
                $udi = '
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card card-custom card-stretch card-stretch-half gutter-b">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                            '.$divAccess.'
                            </div>
                            <!--end::Body-->
                        </div>                   
                    </div>
                    <div class="col-lg-6 col-xxl-6">
                        <!--begin::Mixed Widget 1-->
                        <div class="card card-custom bg-gray-100 card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 py-5" style="background:#013049">
                                <h1 class="card-title font-weight-bolder text-white">Asesmen Status</h1>
                            </div>

                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body p-0 position-relative overflow-hidden">
                                <!--begin::Chart-->
                                <div id="kt_mixed_widget_1_chart" class="card-rounded-bottom" style="height: 200px;background:#013049"></div>
                                <!--end::Chart-->
                                <!--begin::Stats-->
                                <div class="card-spacer mt-n25">
                                    <!--begin::Row-->
                                    <div class="row m-0">
                                        <div class="col px-6 py-8 rounded-xl mr-7 mb-7" onclick="javascript:jvShow(1)" style="cursor:pointer;background:#D62828">
                                            <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                <li class="fas fa-users-cog fa-3x" style="color:#fff"></li>
                                            </span>
                                            <a href="#" class="text-white font-weight-bold font-size-h6">Status Pertanyaan</a>
                                        </div>
                                        <div class="col px-6 py-8 rounded-xl mb-7" onclick="javascript:jvShow(2)" style="cursor:pointer;background:#FD9507">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                <li class="fas fa-user-times fa-3x" style="color:#fff"></li>
                                            </span>
                                            <a href="#" class="font-weight-bold font-size-h6 mt-2" style="color:#fff">Jawaban Belum disetujui</a>
                                        </div>
                                    </div>
                                    <!--begin::Row-->
                                    <div class="row m-0">
                                        <div class="col px-6 py-8 rounded-xl mr-7" style="background:#219EBC">
                                            <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                                <li class="fas fa-user-edit fa-3x" style="color:#fff"></li>
                                            </span>
                                            <a href="#" class="font-weight-bold font-size-h6 mt-2" style="color:#fff">Penilaian Internal</a>
                                        </div>
                                        <div class="col px-6 py-8 rounded-xl" style="background:#8ECAE6">
                                            <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
                                                <li class="fas fa-user-tie fa-3x" style="color:#FFF"></li>
                                            </span>
                                            <a href="#" class="font-weight-bold font-size-h6 mt-2" style="color:#FFF">Penilaian Eksternal</a>
                                        </div>
                                    </div>
                                    <!--end::Row-->
                                </div>
                                <!--end::Stats-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Mixed Widget 1-->
                    </div>                
                </div>

                <script>
                    function jvShow(jenis){
                        text = null;
                        switch(jenis){
                            case 1:
                                text = "Pertanyaan";
                                break;
                            case 2:
                                text = "Jawaban belum disetujui";
                                break;
                        }
                        $("#imgPROSES").show();
                        $("#windowProses").jqxWindow("open");
                        var param = {};
                        param["jenis"] = jenis;
                        param["aso_process_area_desc"] = text;
                        $("#jqwPopup").jqxWindow("open");
                        $.post("/home/show",param,function(datax){
                            $("#windowProses").jqxWindow("close");
                            $("body").css("overflow","hidden")
                            var lebar = $(window).width();
                            var tinggi = 600;
                            $("#jqwPopup").jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:"middle", resizable:true,title: "Ringkasan " + text});  
                            $("#jqwPopup").on("close", function (event) { $("body").css("overflow","auto")}); 
                            $("#jqwPopup").jqxWindow("setContent", datax);
                        });
                    }
                </script>                
                ';
                $udix = '

                <!--begin::Row-->
                <div class="row">
                    <div class="col-xl-6">
                        '.$divAccess.'
                    </div>
                    <div class="col-lg-6 col-xl-6 col-lg-12">
                        <div class="card card-custom bg-gray-100 card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header border-0 py-5" style="background:#f94144">
                                <h1 class="card-title font-weight-bolder text-white">Asesmen Status</h1>
                            </div>
                            <div class="card-body p-0 position-relative overflow-hidden">
                                <div id="kt_mixed_widget_1_chart" class="card-rounded-bottom" style="height: 200px;background:#f94144"></div>
                                <div class="card-spacer mt-n25">
                                    <!--begin::Row-->
                                    <div class="row m-0">
                                        <div class="col px-6 py-8 rounded-xl mr-7 mb-7" onclick="javascript:jvShow(1)" style="cursor:pointer;background:#023047">
                                            <span class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                <li class="fas fa-users-cog fa-3x" style="color:#fff"></li>
                                            </span>
                                            <a href="#" class="text-white font-weight-bold font-size-h6">Status Pertanyaan</a>
                                        </div>
                                        <div class="col px-6 py-8 rounded-xl mb-7" onclick="javascript:jvShow(2)" style="cursor:pointer;background:#126782">
                                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                <li class="fas fa-user-times fa-3x" style="color:#fff"></li>
                                            </span>
                                            <a href="#" class="font-weight-bold font-size-h6 mt-2" style="color:#fff">Jawaban Belum disetujui</a>
                                        </div>
                                    </div>
                                    <!--end::Row-->
                                    <!--begin::Row-->
                                    <div class="row m-0">
                                        <div class="col px-6 py-8 rounded-xl mr-7" style="background:#219EBC">
                                            <span class="svg-icon svg-icon-3x svg-icon-danger d-block my-2">
                                                <li class="fas fa-user-edit fa-3x" style="color:#fff"></li>
                                            </span>
                                            <a href="#" class="font-weight-bold font-size-h6 mt-2" style="color:#fff">Penilaian Internal</a>
                                        </div>
                                        <div class="col px-6 py-8 rounded-xl" style="background:#8ECAE6">
                                            <span class="svg-icon svg-icon-3x svg-icon-success d-block my-2">
                                                <li class="fas fa-user-tie fa-3x" style="color:#FFF"></li>
                                            </span>
                                            <a href="#" class="font-weight-bold font-size-h6 mt-2" style="color:#FFF">Penilaian Eksternal</a>
                                        </div>
                                    </div>
                                    <!--end::Row-->
                                </div>
                            </div>
                        </div>
                    </div>
                <script>
                    function jvShow(jenis){
                        text = null;
                        switch(jenis){
                            case 1:
                                text = "Pertanyaan";
                                break;
                            case 2:
                                text = "Jawaban belum disetujui";
                                break;
                        }
                        $("#imgPROSES").show();
                        $("#windowProses").jqxWindow("open");
                        var param = {};
                        param["jenis"] = jenis;
                        param["aso_process_area_desc"] = text;
                        $("#jqwPopup").jqxWindow("open");
                        $.post("/home/show",param,function(datax){
                            $("#windowProses").jqxWindow("close");
                            $("body").css("overflow","hidden")
                            var lebar = $(window).width();
                            var tinggi = 600;
                            $("#jqwPopup").jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:"middle", resizable:true,title: "Ringkasan " + text});  
                            $("#jqwPopup").on("close", function (event) { $("body").css("overflow","auto")}); 
                            $("#jqwPopup").jqxWindow("setContent", datax);
                        });
                    }
                </script>
                ';
                break;
        }

        if($return=="echo"){
            echo $udi;
        }else{
            return $udi;
        }        
    }
    function show(){
        // $this->common->debug_post();
        $jenis = $this->input->post("jenis");
        $aso_process_area_desc = $this->input->post("aso_process_area_desc");
        $script = '
        <script type="text/javascript" src=' . base_url(PLUGINS."DataTables/datatables.min.js") .'></script>
        <link rel="stylesheet" href=' . base_url(PLUGINS."DataTables/datatables.min.css"). ' type="text/css">
        <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/fontawesome.min.css"). ' type="text/css">
        <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/solid.css"). ' type="text/css">
        <style>
        
            table.dataTable td {
                font-size: 11px !important;
                padding:10px !important;
            }
            table.dataTable th {
                font-size: 10px !important;
            }
            .footer_font{
                font-size: 11px !important;
            }
        </style>                    
        ';

        switch ($jenis) {
            case '1':
            case '2':
                $rslSummary = $this->m_asesmen->getAsesmenSummary($jenis);
                // $this->common->debug_sql(1);
                $type = "view";
                $aso_idents = 10;
                $tny_level = 2;
                $aso_process_area = 2;
                // $rslBerkas = $this->m_asesmen->getAsesmenfile_list($aso_idents, $tny_level, $aso_process_area);
                $num_rows = $rslSummary->num_rows();
                if($num_rows>0){
                    if($jenis==1){
                        $tablehead = "
                        <thead>
                        <tr>
                            <th style='width:80px'>No</th>
                            <th>Kategori</th>
                            <th>Process Area</th>
                            <th>Level</th>
                            <th>Unit Kerja</th>
                            <th>Pertanyaan</th>
                            <th>Terjawab</th>
                            <th>Disetujui</th>
                            <th>Ditolak</th>
                            <th>Disetujui Asesor</th>
                            <th>Ditolak Asesor</th>
                        </tr>
                        </thead>";
                        $fieldbeda = "unt_unitkerja";
                        $visible = ", visible:false";
                    }
                    
                    if($jenis==2){
                        $tablehead = "
                        <thead>
                        <tr>
                            <th style='width:80px'>No</th>
                            <th>Kategori</th>
                            <th>Process Area</th>
                            <th>Level</th>
                            <th>Penyetuju</th>
                            <th>Pertanyaan</th>
                            <th>Terjawab</th>
                            <th>Disetujui</th>
                            <th>Ditolak</th>
                            <th>Disetujui Asesor</th>
                            <th>Ditolak Asesor</th>
                        </tr>
                        </thead>";
                        $fieldbeda = "penyetuju_name";
                        $visible = null;
                    }
                    $loop_detail = 0;
                    $loop = 0;
                    $loop = 0;
                    $loop_detail = 0;
                    $unt_unitkerja_temp = null;
                    foreach($rslSummary->result() as $key=>$value){
                        $idk_kategori_desc = $value->idk_kategori_desc;
                        $idk_process_area_desc = $value->idk_process_area_desc;
                        $lvl_nama = $value->lvl_nama;
                        $unt_unitkerja = $value->unt_unitkerja;
                        $count_pertanyaan = $value->count_pertanyaan;
                        $count_jawaban = $value->count_jawaban;
                        $count_approved_atasan = $value->count_approved_atasan;
                        $count_not_approved_atasan = $value->count_not_approved_atasan;
                        $count_approved_asesor = $value->count_approved_asesor;
                        $count_not_approved_asesor = $value->count_not_approved_asesor;
                        $idk_kategori = $value->idk_kategori;
                        $idk_process_area = $value->idk_process_area;
                        $tny_level = $value->tny_level;
                        $unt_idents = $value->unt_idents;
                        if($jenis==2){
                            $penyetuju_name = $value->penyetuju_name;
                        }
                        
                        if($unt_unitkerja_temp!=$unt_unitkerja){
                            if($loop!=0){
                                $table .= "</tbody>";
                                $table .= "</table>";
                                $arrTabs[$unt_unitkerja_temp] = array("data"=>$table);
                                // debug_array($idk_idents_temp . $idk_idents);
                            }
                            $idtable = 'tblPertanyaan_'.$unt_idents;
                            $arrIdTable[] = $idtable;
                            $table = '<table id='.$idtable.' class="display" style="width:100%;display:none">';
                            $table .= $tablehead;
                            $table .= "<tbody>";
                            $loop_detail =0;
                        }

                        $idtable = 'tblSummary_';
                        $urutan = $loop_detail+1;
                        $table .= "<tr>";
                        $table .= " <td style='width:80px'>" . $urutan . "</td>";
                        $table .= " <td>" . $idk_kategori_desc . "</td>";
                        $table .= " <td>" . $idk_process_area_desc . "</td>";
                        $table .= " <td>" . $lvl_nama . "</td>";
                        $table .= " <td>" . ${$fieldbeda} . "</td>";
                        $table .= " <td>" . $count_pertanyaan . "</td>";
                        $table .= " <td>" . $count_jawaban . "</td>";
                        $table .= " <td>" . $count_approved_atasan . "</td>";
                        $table .= " <td>" . $count_not_approved_atasan . "</td>";
                        $table .= " <td>" . $count_approved_asesor . "</td>";
                        $table .= " <td>" . $count_not_approved_asesor . "</td>";
                        $table .= " <td>" . $idk_kategori . "</td>";
                        $table .= " <td>" . $idk_process_area . "</td>";
                        $table .= " <td>" . $tny_level . "</td>";
                        $table .= "</tr>";
                        $unt_unitkerja_temp = $unt_unitkerja;
                        $loop_detail++;
                        $loop++;
                        if($loop==$num_rows){
                            $table .= "</tbody>";
                            $table .= "</table>";
                            $arrTabs[$unt_unitkerja_temp] = array("data"=>$table);
                        }            
                    }
                    $script .= "<script>    
                    jQuery(document).ready( function ($) {
                    ";
        
                    foreach ($arrIdTable as $keyID){
                        $script .= " var oDT_" . $keyID . " = $('#".$keyID."').DataTable(
                            {  'autoWidth':false, 'paging': false,'ordering': false, 'info':false, 'searching':false, 
                                rowGroup: {
                                    dataSrc: 'idk_kategori_desc'
                                },
                                columns: [
                                    {   data: 'idk_nourut',width: '50px'},
                                    {   data: 'idk_kategori_desc', visible:false},
                                    {   data: 'idk_process_area_desc'},
                                    {   data: 'lvl_nama'},
                                    {   data: '".$fieldbeda."' ".$visible."},
                                    {   data: 'count_pertanyaan',render: $.fn.dataTable.render.number(',', '.', 0, ''), className: 'dt-body-right'},
                                    {   data: 'count_jawaban',render: $.fn.dataTable.render.number(',', '.', 0, ''), className: 'dt-body-right'},
                                    {   data: 'count_approved_atasan',render: $.fn.dataTable.render.number(',', '.', 0, ''), className: 'dt-body-right'},
                                    {   data: 'count_not_approved_atasan',render: $.fn.dataTable.render.number(',', '.', 0, ''), className: 'dt-body-right'},
                                    {   data: 'count_approved_asesor',render: $.fn.dataTable.render.number(',', '.', 0, ''), className: 'dt-body-right'},
                                    {   data: 'count_not_approved_asesor',render: $.fn.dataTable.render.number(',', '.', 0, ''), className: 'dt-body-right'},
                                    {   data: 'idk_kategori', visible:false},
                                    {   data: 'idk_process_area', visible:false},
                                    {   data: 'tny_level', visible:false},
                                ]
                            });
                            $('#".$keyID."').show();
                        ";
                    }
                    $script .= "
                        });
                    </script>
                    ";            
                }
                // $content = $table;
                // $aso_process_area_desc = null;
                if(!isset($arrTabs)){
                    $arrTabs["No Data Yet"] = array("data"=>"Data Belum Ada");
                }
                $arrTabs = array(
                    "id"=>"Pertanyaan",
                    "bentuk"=>"accordion",
                    "arrTabs" => $arrTabs
                  );
                $content = generateTabjqx($arrTabs);
                $content .= $script;
                $portlet = array("content"=>$content,"title"=>$aso_process_area_desc, "icon"=>"fas fa-question-circle");
                if(isset($buttonatas)){
                    $portlet = array_merge($portlet, array("listaction"=>$buttonatas));
                }
                $content = createportlet($portlet);                
                break;
            
            default:
                # code...
                break;
        }
        echo $content;
    }
}
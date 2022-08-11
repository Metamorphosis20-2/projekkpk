<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Penilaian extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
        $this->load->helper('ginput');
        $this->load->model(array('m_asesmen','m_master'));
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
        $this->table = "t_asm_asesmen_penilaian";
        $this->table_jawaban = "t_asm_asesmen_jawaban";
        $this->usr_level = $this->session->userdata("USR_LEVELS");
        $this->usr_idents = $this->session->userdata("USR_IDENTS");
    }   
    public function index(){
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Penilaian"),
        );
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->listPenilaian(),'admin',$bc);    
    }
    public function listPenilaian(){

        $gridname = "jqxPenugasan";
        $this->load->helper('jqxgrid');
        // $url ='/proses/nosj/getPenilaian_list/'.$this->usr_idents;        
        $url ='/nosj/getNosj_list/Penilaian/list/m_asesmen';
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_idents','aw'=>'150','label'=>"ID Asesmen", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'nil_idents','aw'=>'150','label'=>"ID Penilaian", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_kelompok_indikator'    ,'aw'=>'150','label'=>"ID Kategori", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'idk_process_area','aw'=>'150','label'=>"ID Process Area", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_operator','aw'=>'150','label'=>"ID Operator", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tny_level','aw'=>'150','label'=>"Level", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>30, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"lvl_nama", "aw"=>80, "label"=>"Tingkat","adtype"=>"text");
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_name", "aw"=>120, "label"=>"Petugas","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"jwb_usrnam", "aw"=>120, "label"=>"Petugas","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"jwb_appnam", "aw"=>120, "label"=>"Produsen Data","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"jwb_asenam", "aw"=>120, "label"=>"Asesor","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"unt_unitkerja", "aw"=>150, "label"=>"Unit Kerja","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_kelompok_indikator_desc", "aw"=>150, "label"=>"Kategori","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"idk_process_area_desc", "aw"=>150, "label"=>"Process Area","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>'cnt_pertanyaan','label'=>"ID Asesmen", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>'cnt_approve_asesor','label'=>"ID Asesmen", 'ah'=>true);
        $urlsec = uri_string();
        $otorisasi = $this->common->otorisasi($urlsec);
        $oAPP = strpos("N".$otorisasi,"P");
        $oVIW = strpos("N".$otorisasi,"V");
        $callback = null;
        $buttonrow = null;
        $asesor = false;

        $rslAsesor = $this->crud->chkAsesor();
        if($rslAsesor->num_rows()>0){
            $asesor = true;
        }
         $buttonrow[] = array("view"=>array("icon"=>"eyes", "function"=>"jvAnswer('add', data_row)", "idents"=>"aso_idents", 'alt'=>'Lihat Penilaian'));
        if($asesor){
            $btn_false = '<button type="button" class="btn btn-warning btn-icon btn-xs" aria-label="Penilaian" data-microtip-position="right" role="tooltip"><i class="fas fa-thumbs-up" style="text-align:center;cursor:pointer;"></i></button>';
            $buttonrow = array("nilai"=>array("icon"=>"thumbs-up", "function"=>"jvAnswer('approve', data_row)", "idents"=>"aso_idents", 'buttonclass'=>'primary', 'alt'=>'Penilaian', "render"=>array("validation"=>"data_row.cnt_pertanyaan==data_row.cnt_approve_asesor", "return_false"=>$btn_false, "appliedall"=>true)));
        }

        // $this->db->select("CONCAT('<a href=''javascript:jvAnswer(this)'' class=''label label-xl label-success mr-2''>','<i class=''fas fa-check'' style=''font-size:14px;color:#fff''></i>','</a>') btn_go");
        $content = gGrid(array('url'=>$url, 
            'grid'=>'datatables',
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>13,
            'fontsize'=>15,
            'inline_buttonrow'=>$buttonrow,
            'inline_button_pos'=>'left',
            // 'buttonother'=> $buttonother,
            // "event"=>$event,
            'surrounded'=>true,
            'sumber'=>'server',
            'closeform'=>false,
            'modul'=>'asesor/penilaian/pertanyaan',
        ));
        //====== end of grid
        $content .= form_input(array('name' => "aso_kelompok_indikator",'id'=> "aso_kelompok_indikator", 'type'=>'hidden'));
        $content .= form_input(array('name' => "aso_operator",'id'=> "aso_operator", 'type'=>'hidden'));
        $content .= form_input(array('name' => "aso_type",'id'=> "aso_type", 'type'=>'hidden'));
        $content .= form_input(array('name' => "aso_status",'id'=> "aso_status", 'type'=>'hidden'));
        $content .= form_input(array('name' => "aso_kelompok_indikator_desc",'id'=> "aso_kelompok_indikator_desc", 'type'=>'hidden'));
        $content .= form_input(array('name' => "idk_process_area",'id'=> "idk_process_area", 'type'=>'hidden'));
        $content .= form_input(array('name' => "idk_general",'id'=> "idk_general", 'type'=>'hidden'));
        $content .= form_input(array('name' => "tny_level",'id'=> "tny_level", 'type'=>'hidden'));
        $content .= form_input(array('name' => "tny_level_form",'id'=> "tny_level_form", 'type'=>'hidden'));
        $content .= form_input(array('name' => "idk_process_area_desc",'id'=> "idk_process_area_desc", 'type'=>'hidden'));
        $content .= form_input(array('name' => "lvl_nama",'id'=> "lvl_nama", 'type'=>'hidden'));
        $content .= form_input(array('name' => "nil_idents",'id'=> "nil_idents", 'type'=>'hidden'));
        $content .= form_input(array('name' => "jwb_usrnam","id"=> "jwb_usrnam","type"=>"hidden"));
        $content .= form_input(array('name' => "jwb_appnam","id"=> "jwb_appnam","type"=>"hidden"));
        $content .= form_close();
        $content .= generateWindowjqx(array('window'=>'Kategori','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        $content .= "
        <script>
        function jvView(data_row){
            aso_idents = data_row['aso_idents'];
            aso_operator = data_row['aso_operator'];
            aso_kelompok_indikator = data_row['aso_kelompok_indikator'];
            aso_kelompok_indikator_desc = data_row['aso_kelompok_indikator_desc'];
            $('#grdIDENTS').val(aso_idents);
            $('#aso_type').val('view');
            $('#aso_kelompok_indikator').val(aso_kelompok_indikator);
            $('#aso_operator').val(aso_operator);
            $('#aso_kelompok_indikator_desc').val(aso_kelompok_indikator_desc);
            document.frmGrid.submit();
        }

        function jvAnswer(type, data_row){
            aso_idents = data_row['aso_idents'];
            idk_process_area= data_row['idk_process_area'];
            aso_operator = data_row['aso_operator'];
            aso_kelompok_indikator = data_row['aso_kelompok_indikator'];
            aso_kelompok_indikator_desc = data_row['aso_kelompok_indikator_desc'];
            idk_process_area_desc = data_row['idk_process_area_desc'];
            aso_status = data_row['aso_status'];
            nil_idents = data_row['nil_idents'];
            tny_level = data_row['tny_level'];
            lvl_nama = data_row['lvl_nama'];
            jwb_usrnam = data_row['jwb_usrnam'];
            jwb_appnam = data_row['jwb_appnam'];

            $('#grdIDENTS').val(aso_idents);
            $('#aso_type').val(type);
            $('#aso_kelompok_indikator').val(aso_kelompok_indikator);
            $('#aso_operator').val(aso_operator);
            $('#aso_status').val(aso_status);
            $('#aso_kelompok_indikator_desc').val(aso_kelompok_indikator_desc);
            $('#idk_process_area').val(idk_process_area);
            $('#idk_process_area_desc').val(idk_process_area_desc);
            $('#idk_general').val(0);
            $('#nil_idents').val(nil_idents);
            $('#tny_level').val(tny_level);
            $('#tny_level_form').val(tny_level);
            $('#lvl_nama').val(lvl_nama);
            $('#jwb_usrnam').val(jwb_usrnam);
            $('#jwb_appnam').val(jwb_appnam);

            if(type!='send'){
                document.frmGrid.submit();
            }else{
                const { value: text } = swal.fire({
                    title:'Ajukan Kuesioner?', 
                    icon: 'question',
                    showCancelButton: true, 
                    confirmButtonText: '".$this->lang->line("Ya")."', 
                    cancelButtonText: '".$this->lang->line("Tidak")."', 
                    confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                    cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                }).then(result => {
                    // console.log(result.isConfirmed);
                    if(result.isConfirmed==true) {
                        $('#frmGrid').attr('action', '/proses/kuesioner/kirim');
                        document.frmGrid.submit();
                    }
                })
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
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/asesor/penilaian','text'=>"Daftar Penilaian"),
            array('link'=>'#','text'=>$judul),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);
    }

    function pertanyaan(){
        $formname = "formgw";
        $button = null;
        $nil_idents = $this->input->post("nil_idents");
        $nil_catatan = null;
        // $this->common->debug_post();
        $aso_type = $this->input->post("aso_type");
        $tny_level = $this->input->post("tny_level");
        $tny_level_form = $this->input->post("tny_level_form");
        $lvl_nama = $this->input->post("lvl_nama");
        $idk_process_area_desc = $this->input->post("idk_process_area_desc");
        $jwb_usrnam = $this->input->post("jwb_usrnam");
        $jwb_appnam = $this->input->post("jwb_appnam");

        $aso_status = $this->input->post("aso_status");
        $idk_general = $this->input->post("idk_general");
        if($aso_status==1){
            $aso_type = "view";
        }
        $aso_idents = $this->input->post("grdIDENTS");
        $aso_operator = $this->input->post("aso_operator");
        $aso_kelompok_indikator = $this->input->post("aso_kelompok_indikator");
        $aso_kelompok_indikator_desc = $this->input->post("aso_kelompok_indikator_desc");
        $idk_process_area = $this->input->post("idk_process_area");

        if($nil_idents!=null){
            $rslPenilaian = $this->m_asesmen->getPenilaian_edit($nil_idents);
            if($rslPenilaian->num_rows()>0){
                $rowPenilaian = $rslPenilaian->row();
                $nil_catatan = $rowPenilaian->nil_catatan;
            }
        }

        $udi = null;
        $script = '
        <script type="text/javascript" src=' . base_url(PLUGINS."DataTables/datatables.min.js") .'></script>
        <link rel="stylesheet" href=' . base_url(PLUGINS."DataTables/datatables.min.css"). ' type="text/css">
        <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/fontawesome.min.css"). ' type="text/css">
        <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/solid.css"). ' type="text/css">
        ';
        $attr = array(
            "class" => "form",
            "name" => "formgw",
            "id" => "formgw",
        );

        $rslJawabanDetail = $this->m_asesmen->getKategoriJawaban($aso_idents, $idk_process_area);

        $visible_level =null;
        $showgeneral = false;
        $tny_level = $this->input->post("tny_level");
        // debug_array($tny_level);
        if($idk_general==1){
            $showgeneral = true; 
        }
        
        if($showgeneral){
            $rslKategori = $this->m_asesmen->getKategoriGeneral_detail($aso_idents, $idk_process_area, $aso_operator);
        }else{
            $rslKategori = $this->m_asesmen->getKategoriKuesioner_detail($aso_idents, $idk_process_area, $aso_operator, $tny_level);
            
        }
        
        $lvl_nama = null;
        $lvl_petunjuk = null;
        if($tny_level!=0){
            $rsl = $this->m_master->getTingkat_edit($tny_level);
            if($rsl->num_rows()>0){
                $row = $rsl->row();
                //debug_array($row);
                $lvl_nama = $row->lvl_nama;
                $lvl_petunjuk = $row->lvl_petunjuk;
            }
        }
        if($rslKategori->num_rows()>0){
            // $tablehead = "<thead><tr><th style='width:80px'>No</th><th>Pertanyaan</th><th>Action</th><th>ID Jawaban</th><th>Jawaban</th><th>Deskrisi</th><th>Link</th><th>File</th></tr></thead>";
            $tablehead = "<thead>
                    <tr>
                        <th style='width:80px'>No</th>
                       
                        <th>Pertanyaan</th>
                       <th>Aksi</th>
                        <th>Riwayat</th>
                        <th>Nilai (%)</th>
                     
                    </tr>
            </thead>";
            $loop = 0;
            $loop_detail = 0;
            $idk_idents_temp = null;
            $idk_nama_temp = null;
            $hidden = null;
            $num_rows = $rslKategori->num_rows();
            foreach($rslKategori->result() as $key=>$value){
                $idk_idents = $value->idk_idents;
                $tny_idents = $value->tny_idents;
                $tny_level_desc = $value->tny_level_desc;
                $tny_level_grid = $value->tny_level;
                $idk_nama_kategori = $value->idk_nama_kategori;
                $tny_kriteria_desc = $value->tny_kriteria_desc;
                $idk_nama = $value->idk_nama;
                $tny_pertanyaan = $value->tny_pertanyaan;
                $jwb_idents = $value->jwb_idents;
                $jwb_asoidents = $value->jwb_asoidents;
                $jwb_tnyidents = $value->jwb_tnyidents;
                $jwb_jawab = $value->jwb_jawab;
                $jwb_deskripsi = $value->jwb_deskripsi;
                $jwb_link = $value->jwb_link;
                $jwb_file = $value->jwb_file;
                $tny_petunjuk = $value->tny_petunjuk;
                $jwb_status = $value->jwb_status;
                $jwb_usrnam = $value->jwb_usrnam;
                $nli_asesor = $value->nli_asesor;
                $ctn_asesor = $value->ctn_asesor;
                
                // $arrTabs[$idk_nama][$loop] = $tny_pertanyaan;
    
                $idtable = 'tblPertanyaan_'.$idk_idents;
                if($idk_idents_temp!=$idk_idents){
                    if($loop!=0){
                        $table .= "</tbody>";
                        $table .= "</table>";
                        $arrTabs[$idk_nama_temp] = array("data"=>$table);
                        // debug_array($idk_idents_temp . $idk_idents);
                    }
                    $arrIdTable[] = $idtable;
                    $table = '<table id='.$idtable.' class="display" style="width:100%;display:none">';
                    $table .= "<thead></thead>";
                    $table .= $tablehead;
                    $table .= "<tbody>";
                    $loop_detail =0;
                }
                $urutan = $loop_detail+1;
                $table .= "<tr>";
                $table .= " <td style='width:80px'>" . $urutan . "</td>";
                $table .= " <td>" . $tny_level_grid . "</td>";                
                $table .= " <td id=id_".$tny_idents."_1>" . $tny_pertanyaan . "</td>";
                $table .= " <td>".$nli_asesor. "</td>";
                $table .= " <td></td>";
                $table .= " <td>" . $tny_kriteria_desc . "</td>";
                $table .= " <td>".$tny_idents. "</td>";
                $table .= " <td>".$idk_nama. "</td>";
                $table .= " <td>".$jwb_idents."</td>";
                $table .= " <td>".$jwb_jawab."</td>";
                $table .= " <td>".$jwb_deskripsi."</td>";
                $table .= " <td>".$jwb_link."</td>";
                $table .= " <td>".$jwb_file."</td>";
                $table .= " <td>".$tny_petunjuk."</td>";
                $table .= " <td>".$jwb_status."</td>";
                $table .= " <td>".$jwb_usrnam."</td>";
                $table .= " <td>".$nli_asesor."</td>";
                $table .= " <td>".$ctn_asesor."%</td>";
                $table .= "</tr>";
                
                
                $hidden .= form_input(array('name' => "id_".$tny_idents."_idents",'id'=> "id_".$tny_idents."_idents", 'type'=>'hidden',"value"=>$jwb_idents));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_jawab",'id'=> "id_".$tny_idents."_jawab", 'type'=>'hidden',"value"=>$jwb_jawab));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_deskripsi",'id'=> "id_".$tny_idents."_deskripsi", 'type'=>'hidden',"value"=>$jwb_deskripsi));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_link",'id'=> "id_".$tny_idents."_link", 'type'=>'hidden',"value"=>$jwb_link));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_ctn",'id'=> "id_".$tny_idents."_ctn", 'type'=>'hidden',"value"=>$ctn_asesor));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_nli",'id'=> "id_".$tny_idents."_nli", 'type'=>'hidden',"value"=>$nli_asesor));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_file",'id'=> "id_".$tny_idents."_file", 'type'=>'hidden',"value"=>$jwb_file));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_status",'id'=> "id_".$tny_idents."_status", 'type'=>'hidden',"value"=>$jwb_status));
                // $hidden .= form_input(array('name' => "id_".$tny_idents."_petunjuk",'id'=> "id_".$tny_idents."_file", 'type'=>'hidden',"value"=>$tny_petunjuk));

                $jawab[] = $tny_idents;
                $idk_idents_temp = $idk_idents;
                $idk_nama_temp = $idk_nama;
                $loop_detail++;
                $loop++;
                if($loop==$num_rows){
                    $table .= "</tbody>";
                    

                    $table .= "<thead><tr><th></th><th></th><th></th><th></th><th></th></tr></thead>";
                    $rsltotal = $this->m_asesmen->getKategoriKuesioner_detailtotal($aso_idents, $idk_process_area, $aso_operator, $tny_level);
                    foreach($rsltotal->result() as $keytotal=>$total){$average = $total->average;}
                    $table .= "<thead><tr><th></th><th></th><th></th><th><b>Total Nilai</b></th><th><b>".number_format($average,2)." %</b></th></tr></thead>";
                                        $setujui = '<button id="restor" type="button" class="btn btn-success">   Setujui  </button>
                ';
                    $restor = $this->m_asesmen->restorejawaban($aso_idents, $idk_process_area, $aso_operator, $tny_level);
                    $restor = $setujui;

                   
                    $table .="</table>   
                    <div class='setujui' style ='margin-left:90%;'>
                         $setujui 
                           <script>
                            $(document).ready(function () {
                                $('#btnsetujui').on('click',function(){
                                    jvApprove(21, '.$jwb_status.');
                                });
                                
                            });
                            </script> 
                            </div>";
                    // debug_array($arrTabs);
                    
                  

                    $tab_nama = "fas fa-question-circle^" . $idk_nama_temp . ($idk_general==1 ? "" : ($lvl_nama=="" ? "" : " - <b>" .$lvl_nama . "</b>"));
                    $arrTabs[$tab_nama] = array("data"=>$table);
                }            
            }
            
            $script .= "<script>    
            jQuery(document).ready( function ($) {
                // $.noConflict();
                $('#jqwPopup').on('close', function (event) { $('.collapse.in').addClass('collapse in'); }); 
            ";

            $render_validation = "
                switch(data.jwb_status){
                    case '0':
                        if(data.tny_level==0){
                            return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-primary btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-eye\" style=\"color:#fff\"/></a>'
                        }else{
                            return '';
                        }
                        break;
                    case '1':
                        return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-success btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-check\" style=\"color:#fff\"/></a>'
                        break;
                    case '2':
                        return ''
                        break;
                    case '21':
                        return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-approved btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-thumbs-up\" style=\"color:#fff\"/></a>'
                        break;
                    case '22':
                        return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-danger btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-thumbs-down\" style=\"color:#fff\"/></a>'
                        break;
                    default:
                        return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-success btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-check\" style=\"color:#fff\"/></a>'
                        break;
                }
            ";
            if($tny_level!=0){
                $button = '<button id="btnTolak" type="button" class="btn btn-primary">Simpan</button>
                ';
                
            }

            $render_validation2 = "
            if(data.jwb_idents!=''){
                return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-light-dark btn-sm\"><i id=\"his_'+data.tny_idents+'\" class=\"fas fa-history\"/></a>'
            }else{
                return ''
            }
            
            ";
            foreach ($arrIdTable as $keyID){
                $script .= " var oDT_" . $keyID . " = $('#".$keyID."').DataTable(
                    {  'autoWidth':false, 'paging': false,'ordering': false, 'info':false, 'searching':false, 
                        rowGroup: {
                            dataSrc: 'tny_kriteria'
                        },
                        columns: [
                            {   data: 'idk_nourut',width: '50px'},
                            {   data: 'tny_level',width: '100px', visible:false},
                            {   data: 'tny_pertanyaan',width: '90%'},
                            
                            {   data: null, className: 'dt-center editor-view', 
                                'render': function (data, type, row, meta){
                                    " . $render_validation . "
                                },
                                orderable: false, 
                                width: '20px'
                            },
                            {   data: null, className: 'dt-center history-view', 
                                'render': function (data, type, row, meta){
                                    " . $render_validation2 . "
                                },
                                orderable: false, 
                                width: '20px'
                            },
                            {   data: 'tny_kriteria',width: '0px', visible:false},
                            {   data: 'tny_idents',width: '0px', visible:false},
                            {   data: 'idk_nama',width: '0px', visible:false},
                            {   data: 'jwb_idents',width: '1px', visible:false},
                            {   data: 'jwb_jawab',width: '1px', visible:false},
                            {   data: 'jwb_deskripsi',width: '1px', visible:false},
                            {   data: 'jwb_link',width: '1px', visible:false},
                            {   data: 'jwb_file',width: '0px', visible:false},
                            {   data: 'jwb_petunjuk',width: '0px', visible:false},
                            {   data: 'jwb_status',width: '0px', visible:true},
                            {   data: 'jwb_usrnam',width: '0px', visible:false},
                            {   data: 'nli_asesor',width: '50px', visible:true},
                            {   data: 'ctn_asesor',width: '1px', visible:false},
                        ]
                    });
                    $('#".$keyID."').show();
                    recordsTotal = oDT_".$keyID.".page.info().recordsTotal;
                    
                    $('#".$keyID."').on('click', 'td.editor-view', function (e) {
                        e.preventDefault();
                        var data_row = oDT_".$keyID.".row( $(this).parents('tr') ).data(); 
                        let row = oDT_".$keyID.".row('#row-' + data_row.tny_idents);
                        // console.log(data_row);
                        jvAnswer('".$aso_type."', data_row, this)
                    } );
                    $('#".$keyID."').on('click', 'td.history-view', function (e) {
                        e.preventDefault();
                        var data_row = oDT_".$keyID.".row( $(this).parents('tr') ).data(); 
                        jvHistory(data_row, this)
                    } );
                ";
            }
            $status_edit = 'approval';
            $status_add = 'approval';
            $aso_process_area_desc = $idk_nama_temp . ($idk_general==1 ? " - General" : ($lvl_nama=="" ? "" : " " .$lvl_nama . ""));
            $tab_nama = "fas fa-question-circle^" . $aso_process_area_desc;
            
            $script .= "
            emptyfield = function(){
                $('#idk_nama').val('');
                $('#jwb_idents').val(0);
                $('#tny_idents').val(0);
                $('#tny_pertanyaan').val('');
                $('#jwb_link').val('');
                $('#nli_asesor').val('');
                $('#ctn_asesor').val('');
            }
            });
            var tanya = " . json_encode($jawab). ";

            function jvHistory(data_row){
                var tny_idents = data_row.tny_idents;
                jwb_idents = $('#id_' + tny_idents + '_idents').val();
                if(jwb_idents!=''){
                    $('#modal-footer-question').hide();
                    var param = {};
                    param['jwb_idents'] = jwb_idents;
                    $.post('/proses/kuesioner/riwayat', param,function(data){
                        if(data==0){
                            swal.fire({ 
                                title:'Tidak Ada Riwayat!', 
                                icon: 'info'
                            })
                        }else{
                            $('#windowProses').jqxWindow('close');
                            $('#modal-body-question').html(data);
                            window.$('#modalQuestion').modal('show');
                        }
                    });
                    // $('#modalQuestion').modal({backdrop: 'static', keyboard: false});
                    // window.$('#modalQuestion').modal('show');
                }
            }
            function jvPetunjuk(){
                if(".$this->usrlevel."==5){
                    $('#tblSupervisor').hide();
                    $('#tblOperator').show();
                    window.$('#modalPetunjuk').modal('show');
                }else{
                    $('#tblOperator').hide();
                    $('#tblSupervisor').show();
                    window.$('#modalPetunjuk').modal('show');
                }
            }            
            function jvGeneral(){
                $('#idk_general').val(1);
                $('#".$formname."').attr('action', '/asesor/penilaian/pertanyaan');
                document.formgw.submit();
            }
            function jvApprove(jwb_status, status_original){
                var jwb_idents = $('#jwb_idents').val();
                var tny_idents = $('#tny_idents').val();
                var jwb_usrnam = $('#jwb_usrnam').val();
                var jwb_appnam = $('#jwb_appnam').val();
                var nli_asesor = $('#nli_asesor').val();
                var ctn_asesor = $('#ctn_asesor').val();
                var lanjut = false;
                var param = {};
                param['jwb_idents'] = jwb_idents ;
                param['jwb_status'] = jwb_status ;
                param['jwb_usrnam'] = jwb_usrnam ;
                param['jwb_appnam'] = jwb_appnam ;
                param['nli_asesor'] = nli_asesor ;
                param['ctn_asesor'] = ctn_asesor ;
                param['jwb_status'] = jwb_status;
                $('#hidTRNSKS').val('');

                if(jwb_status==22){
                    const { value: text } = swal.fire({
                        title:'Apakah anda yankin ingin menyimpan jawaban Kuesioner?', 
                       
                        target: document.getElementById('modalQuestion'),
                        icon: 'question',
                        
                        
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
                        // console.log(result.isConfirmed);
                        if(result.isConfirmed==true) {
                          
                            $.post('/asesor/penilaian/approve',param,function(rebound){
                                if(rebound){
                                    message = 'Jawaban berhasil disimpan!'
                                    class_button = 'btn-danger';
                                    class_fawesome = 'fas fa-thumbs-down';
                                    closeModal(message, class_button, class_fawesome, tny_idents, status_original)
                                    lanjut = true;
                                }
                            });
                        }
                    })
                }else{
                    $.post('/asesor/penilaian/approve', param,function(data){
                        var result = $.parseJSON(data);
                        message = 'Jawaban berhasil disimpan!!';
                        class_button = 'btn-approved';
                        class_fawesome = 'fas fa-thumbs-up';
                        lanjut = true;
                        closeModal(message, class_button, class_fawesome, tny_idents, status_original)
                    });
                }
                $('#id_' + tny_idents + '_status').val(jwb_status);
            }

           function jvSetujui(jwb_status, status_original)
           {
       

              }
            
            function jvBackInput(){
                $('#idk_general').val(0);                
                // $('#".$formname."').attr('action', '/asesor/penilaian/save');
                $('#".$formname."').attr('action', '/asesor/penilaian/pertanyaan');
                document.formgw.submit();
            }
            function jvAnswer(type, data_row, this_row){
                emptyfield();
                var tny_idents = data_row.tny_idents;
                var idk_nama = data_row.idk_nama;
                var tny_pertanyaan = data_row.tny_pertanyaan;
                var tny_petunjuk = data_row.jwb_petunjuk;
                var jwb_usrnam = data_row.jwb_usrnam;
                var jwb_status = data_row.jwb_status;
                var tny_level = data_row.tny_level;

                jwb_idents = $('#id_' + tny_idents + '_idents').val();
                jwb_jawab = $('#id_' + tny_idents + '_jawab').val();
                jwb_deskripsi = $('#id_' + tny_idents + '_deskripsi').val();
                jwb_link = $('#id_' + tny_idents + '_link').val();
                nli_asesor = $('#id_' + tny_idents + '_nli').val();
                ctn_asesor = $('#id_' + tny_idents + '_ctn').val();
                jwb_file = $('#id_' + tny_idents + '_file').val();

                var param = {};

                if(jwb_idents!=''){
                    $('#hidTRNSKS').val('".$status_edit."');
                    param['jwb_jawab'] = jwb_jawab;
                }else{
                    if(type=='add' || type=='edit'){
                        $('#hidTRNSKS').val('".$status_add."');
                        param['jwb_jawab'] = 3;
                    }
                }
                
                $('#modal-footer-question').show();
                
                if($('#hidTRNSKS').val()!='approval'){
                    if(jwb_status==1){
                        swal.fire({ 
                            title:'Jawaban anda sudah disetujui!', 
                            text: 'Data tidak bisa diubah',
                            icon: 'error'
                        })
                        $('#hidTRNSKS').val('view');
                        $('#modal-footer-question').hide();
                    }
                }
                if($('#hidTRNSKS').val()!=''){
                    $('#jwb_idents').val(jwb_idents);
                    param['jwb_idents'] = jwb_idents;
                    param['jwb_deskripsi'] = jwb_deskripsi;
                    param['tny_idents'] = tny_idents;
                    param['idk_nama'] = idk_nama;
                    param['idk_petunjuk'] = tny_petunjuk;
                    param['tny_pertanyaan'] = tny_pertanyaan;
                    param['jwb_link'] = jwb_link;
                    param['jwb_file'] = jwb_file;
                     param['ctn_asesor'] = ctn_asesor;
                      param['nli_asesor'] = nli_asesor;
                    param['hidTRNSKS'] = $('#hidTRNSKS').val();
                    param['jwb_usrnam'] = jwb_usrnam;
                    param['jwb_status'] = jwb_status;
                    param['tny_level'] = tny_level;
                    $('#imgPROSES').show();
                    $('#windowProses').jqxWindow('open');
                    $.post('/asesor/penilaian/modalpertanyaan', param,function(data){
                        $('#windowProses').jqxWindow('close');
                        $('#modal-body-question').html(data);
                        $('#modalQuestion').modal({backdrop: 'static', keyboard: false});
                        window.$('#modalQuestion').modal('show');
                    });
                }
            }
            function closeModal(message, class_button, class_fawesome, tny_idents, status_original){
                checkRow();
                toastr.options = {
                    'closeButton': false,
                    'debug': true,
                    'newestOnTop': true,
                    'progressBar': true,
                    'positionClass': 'toast-top-right',
                    'preventDuplicates': true,
                    'showDuration': '300',
                    'hideDuration': '1000',
                    'timeOut': '5000',
                    'extendedTimeOut': '1000',
                    'showEasing': 'swing',
                    'hideEasing': 'linear',
                    'showMethod': 'fadeIn',
                    'hideMethod': 'fadeOut'
                };
                if(status_original==22){
                    class_remove = 'btn-danger';
                }else{
                    class_remove = 'btn-success';
                }

                Command: toastr['success'](message)
                $('#btn_' + tny_idents).removeClass(class_remove).addClass(class_button);
                $('#ico_' + tny_idents).removeClass('fas fa-thumbs-up').addClass(class_fawesome);
                window.$('#modalQuestion').modal('hide');
            }
            function jvSave(){
                if($('#idk_general').val()!=1){
                    Swal.fire({ 
                        title: 'Simpan Data Asesmen?', 
                        text: null, 
                        icon: 'question', 
                        showCancelButton: true, 
                        confirmButtonText: 'Ya', 
                        cancelButtonText: 'Tidak', 
                        confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                        cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                    }).then(
                        result => { 
                            if(result.value) {
                                $('#nil_status').val(status);
                                document.".$formname.".submit();
                            }
                        }
                    );
                }
            }
            function jvUpload(){
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
                param['type'] = 'view';
                param['aso_idents'] = $('#aso_idents').val();
                param['tny_level'] = $('#tny_level').val();
                param['aso_process_area'] = $('#idk_process_area').val();
                param['aso_process_area_desc'] = '".$aso_process_area_desc."';
                $('#jqwPopup').jqxWindow('open');
                $.post('/proses/kuesioner/uploadwindow',param,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 600;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Unggah Berkas'});  
                    $('#jqwPopup').jqxWindow('setContent', datax);
                });
            }
            function checkRow(){
                tanya_count = tanya.length;
                tanya.forEach(function(item) {
                    jawab_status = $('#id_' + item + '_status').val();
                    if(jawab_status==21){
                        tanya_count--;
                    }
                });
                if(tanya_count==0){
                    swal.fire({
                        title:'Kirim Pesan ke Produsen Data?', 
                        text:'Semua Kuesioner sudah disetujui',
                        icon: 'question',
                        showCancelButton: true, 
                        confirmButtonText: '".$this->lang->line("Ya")."', 
                        cancelButtonText: '".$this->lang->line("Tidak")."', 
                        confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                        cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                    }).then(result => {
                        if(result.isConfirmed==true) {
                            var param = {};
                            param['jwb_type'] = 2;
                            param['jwb_status'] = 1;
                            param['jwb_usrnam'] = $('#jwb_usrnam').val();
                            param['jwb_appnam'] = $('#jwb_appnam').val();
                            param['tny_level'] = $('#lvl_nama').val();
                            param['idk_process_area'] = $('#idk_process_area_desc').val();
                            param['aso_kelompok_indikator'] = $('#aso_kelompok_indikator_desc').val();

                            $.post('/asesor/penilaian/insertinbox',param,function(rebound){
                                if(rebound){
                                    message = 'Pesan berhasil dikirim!'
                                    toastr.options = {
                                        'closeButton': false,
                                        'debug': true,
                                        'newestOnTop': true,
                                        'progressBar': true,
                                        'positionClass': 'toast-top-right',
                                        'preventDuplicates': true,
                                        'showDuration': '300',
                                        'hideDuration': '1000',
                                        'timeOut': '5000',
                                        'extendedTimeOut': '1000',
                                        'showEasing': 'swing',
                                        'hideEasing': 'linear',
                                        'showMethod': 'fadeIn',
                                        'hideMethod': 'fadeOut'
                                    };
                                    Command: toastr['error'](message)
                                    lanjut = true;
                                }
                            });

                        }
                    })
                }
            }
            </script>
            ";
            $tab_nama_penilaian = "fas fa-check-circle^Penilaian";
            $readonly = false;
            $group = 2;
            if($idk_general!=1){
                $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"Catatan Asesor", "type"=>"txa", "namanya"=>"nil_catatan", "value"=>$nil_catatan, 'ckeditor'=>array('full'=>true, 'coltxa'=>'col-lg-12','toolbar'=>'sosimple','height'=>'300px', 'width'=>'100%'), "readonly"=>$readonly);
            }else{
                $tab_nama_penilaian = $tab_nama;
                $group = 1;
            }
            
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"nil_idents", "value"=>$nil_idents);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"aso_idents", "value"=>$aso_idents);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"grdIDENTS", "value"=>$aso_idents);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"aso_type", "value"=>$aso_type);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"aso_status", "value"=>$aso_status);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"idk_general", "value"=>$idk_general);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"aso_idents", "value"=>$aso_idents);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"aso_operator", "value"=>$aso_operator);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"aso_kelompok_indikator", "value"=>$aso_kelompok_indikator);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"aso_kelompok_indikator_desc", "value"=>$aso_kelompok_indikator_desc);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"jwb_idents");
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"hidFile");
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"hidTRNSKS");
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"nil_status");
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"tny_level", "value"=>$tny_level);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"tny_level_form", "value"=>$tny_level_form);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"idk_process_area_desc", "value"=>$idk_process_area_desc);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"lvl_nama", "value"=>$lvl_nama);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"idk_process_area", "value"=>$idk_process_area);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"jwb_usrnam","value"=>$jwb_usrnam);
            $arrTable[] = array("group"=>1,'urutan'=>$urutan++, "label"=>"ID", "type"=>"hid", "namanya"=>"jwb_appnam","value"=>$jwb_appnam);            
            $arrTable[] = array("group"=>$group,'urutan'=>$urutan++, "type"=>"udf", "namanya"=>"table", "value"=>$table);

            $arrForm =
                array(
                        'type'=>$aso_type,
                        'arrTable'=>$arrTable,
                        'status'=> isset($status) ? $status : "",
                        'nameForm'=>$formname,
                        'width'=>'70%',
                        'formcommand' => '/asesor/penilaian/save' ,
                        'tabname'=> array(
                            '1'=>$tab_nama_penilaian,
                            '2'=>$tab_nama
                        ),
                    );
            $content = generateForm($arrForm);
            $content .= $hidden;
            $content .= $script;
            $content .= form_close();
        }else{
            $content = "<blockquote>Data Pertanyaan tidak Ada! Mohon hubungi Administrator Aplikasi</blockquote>";
        }
        $buttonpetunjuk ='&nbsp;&nbsp;<span class="label label-primary mr-2" onclick="jvPetunjuk()" style="cursor:pointer" ><i class="fas fa-info-circle" style="color:#fff"></i></span>';
        if($lvl_petunjuk!=""){
            $buttonatas[] = array("iconact"=>"fas fa-paperclip", "theme"=>"primary","href"=>"javascript:jvUpload()", "textact"=>"Berkas");
        }
        // debug_array($tny_level);
        if($tny_level!=0){
            // debug_array($idk_general);
            if($idk_general==1){
                $buttonatas[] = array("iconact"=>"fas fa-file-alt", "theme"=>"info","href"=>"javascript:jvBackInput()", "textact"=>"Kuesioner " . $lvl_nama);
            }else{
                $buttonatas[] = array("iconact"=>"fas fa-file-alt", "theme"=>"warning","href"=>"javascript:jvGeneral()", "textact"=>"Pertanyaan General");
            }
        }
        if($idk_general!=1){
            $buttonatas[] = array("iconact"=>"fas fa-save", "theme"=>"success","href"=>"javascript:jvSave(21)", "textact"=>"Simpan");
            // $buttonatas[] = array("iconact"=>"fas fa-thumbs-down", "theme"=>"danger","href"=>"javascript:jvSave(22)", "textact"=>"Tolak");
        }else{
            $button = null;
        }
        $portlet = array("content"=>$content,"title"=>"Kategori " . $aso_kelompok_indikator_desc , "icon"=>"fas fa-map");
        if(isset($buttonatas)){
            $portlet = array_merge($portlet, array("listaction"=>$buttonatas));
        }
        $content = createportlet($portlet);
        $content .= '
        <style>
            div.dataTables_processing { z-index: 1; }
            .modal {
                padding: 0 !important; // override inline padding-right added from js
            }
            #modalQuestion .modal-dialog {
                width: 50%;
                max-width: none;
                height: 95%;
                margin-top:10;
                margin-right: 0;
            }
            #modalQuestion .modal-content {
                height: 100%;
                border: 0;
                border-radius: 0;
            }
            #modalQuestion .modal-body {
                overflow-y: auto;
            }
            .swal2-container {
                z-index: 9999999 !important;
            }
            .swal-overlay  
            {
                z-index: 100000000000; !important    
            }            
        </style>
        <div class="modal" id="modalQuestion" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="headermodal"><span class="" onclick="jvPetunjuk()" style="cursor:pointer" ><i class="fas fa-question-circle" style="color:red"></i></span>  Kuesioner Kategori <b>'.$aso_kelompok_indikator_desc.'</b></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>

                    <div class="modal-body" id="modal-body-question">
                    </div>
                    <div class="modal-footer" id="modal-footer-question">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        ' . $button . '
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="modalPetunjuk" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document" style="margin-left:50%">
                <div class="modal-content">
                    <div class="modal-body" id="modal-body-petunjuk">
                        <table id="tblOperator">
                            <img src="../../../../assets/img/ingfo.png" style="width:90%">
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
        ';
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        // $content .= generateWindowjqx(array('window'=>'Detail','title'=>'Periksa','height'=>'200', 'minWidth'=>100,'maxWidth'=>'1800px','overflow'=>'auto'));
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/asesor/penilaian','text'=>"Daftar Penilaian"),
            array('link'=>'#','text'=>"Penilaian"),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);        
    }

    function modalpertanyaan(){
        // $this->common->debug_post();
        $hidTRNSKS = $this->input->post('hidTRNSKS');
        $jwb_idents = $this->input->post('jwb_idents');
        $tny_idents = $this->input->post('tny_idents');
        $idk_nama = $this->input->post('idk_nama');
        $idk_petunjuk = $this->input->post('idk_petunjuk');
        $tny_pertanyaan = $this->input->post('tny_pertanyaan');
        $jwb_link = $this->input->post('jwb_link');
        $nli_asesor = $this->input->post('nli_asesor');
        $ctn_asesor = $this->input->post('ctn_asesor');
        $jwb_file = $this->input->post('jwb_file');
        $jwb_jawab = $this->input->post('jwb_jawab');
        $jwb_deskripsi = $this->input->post('jwb_deskripsi');
        $jwb_usrnam = $this->input->post('jwb_usrnam');
        $jwb_status = $this->input->post('jwb_status');
        $tny_level = $this->input->post('tny_level');
        $jawab_ya = null;
        $jawab_tidak = null;
        $jawab_tidak_dijawab =  null;

        if($jwb_jawab==1){
            $jawab_ya = "checked";
        }
        if($jwb_jawab==2){
            $jawab_tidak = "checked";
        }
        if($jwb_jawab==3){
            $jawab_tidak_dijawab = "checked";
        }
        $input_link = null;
        $script_link = null;
        if($jwb_link!=""){
            $jwb_link = explode(";", $jwb_link);
        }
       
        if($hidTRNSKS=="add" || $hidTRNSKS=="edit"){
            $readonly = false;
            $ro = "";
            $class_ro_radio = "";
            $scr_link = null;
            if($jwb_link!=""){
                foreach($jwb_link as $key){
                    $scr_link .= "var option0 = new Option('".$key."','".$key."', true, true);$('#jwb_link').append(option0);";
        
                }
                if($scr_link!=""){
                    $scr_link .= "$('#jwb_link').trigger('change');";
                }
            }

            $input_link = '
            <select class="form-control select2" id="jwb_link" name=jwb_link[] multiple>
            </select>
            ';
            if($hidTRNSKS=="add"){
                $input_link .= '<span class="form-text text-muted">Format Link harus menggunakan http atau https</span>';
            }
            $script_link = '
            <script>
            $(document).ready(function () {
                ' . $scr_link . '
                $("#jwb_link").select2({
                    placeholder: "Tambahkan Link",
                    tags: true
                });
            });
            </script>        
            ';
            $input_radio = '
            <label class="radio">
                <input type="radio" name="jwb_jawab" '. $jawab_ya .' ' . $ro .' id="jwb_jawab" value="1">
                <span></span>
                Ya
            </label>
            <label class="radio radio-danger">
                <input type="radio" name="jwb_jawab" '. $jawab_tidak .' ' . $ro .' id="jwb_jawab" value=2>
                <span></span>
                Tidak
            </label>
            <label class="radio radio-danger">
                <input type="radio" name="jwb_jawab" '. $jawab_tidak_dijawab .' ' . $ro .' id="jwb_jawab" value=3>
                <span></span>
                Tidak Menjawab
            </label>';
        }else{            
            $readonly = true;
            $ro = "disabled='disabled'";
            $class_ro_radio = "radio-disabled";
            if($jwb_link!=""){
                foreach($jwb_link as $key){
                    $input_link .= '<span class="label label-lg label-primary label-inline font-weight-normal mr-2"><a href="'.$key.'" target=_blank style="color:#fff">'.$key.'</a></span>';
                }
            }
            $label_class= "warning";
            $jawaban = "Tidak Menjawab";
        
            if($jwb_jawab==1){
                $label_class= "primary";
                $jawaban = "Ya";
            }
            if($jwb_jawab==2){
                $label_class= "danger";
                $jawaban = "Tidak";
            }
            if($jwb_jawab==3){
                $label_class= "warning";
                $jawaban = "Tidak Menjawab";
            }
            $input_radio = '<span class="label label-xl label-'.$label_class.' label-inline font-weight-bold mr-2" style="width:200px">'.$jawaban.'</span>';
            // $input_radio = '<button class="btn font-weight-bold btn-primary mr-2" onclick="#">Button label <span class="label label-sm label-white ml-2">5</span></button>';

        }

        $textarea_arr[] = array("group"=>1, "urutan"=>1, "namanya"=>"jwb_deskripsi", "label"=>"&nbsp;","type"=>"txa", "readonly"=>$readonly, 'value'=>$jwb_deskripsi, 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'));
        $textarea = generateinput(array('arrTable'=>$textarea_arr,'elementonly'=>true,'nojqx'=>true));

        $nli_asesor_arr[] = array("group"=>1, "urutan"=>1, "namanya"=>"nli_asesor", "label"=>"&nbsp;","type"=>"txa",  'value'=>$nli_asesor, 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'));
        $nli_asesorr = generateinput(array('arrTable'=>$nli_asesor_arr,'elementonly'=>true,'nojqx'=>true));

        $ctn_asesor_arr[] = array("group"=>1, "urutan"=>1, "namanya"=>"ctn_asesor", "label"=>"&nbsp;","type"=>"txa",  'value'=>$ctn_asesor, 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'));
        $ctn_asesorr = generateinput(array('arrTable'=>$ctn_asesor_arr,'elementonly'=>true,'nojqx'=>true));

        $dropzone = array(
            "path"=>"/assets/kuesioner/",
            "autoupload"=>false,
            "url"=>base_url("/proses/kuesioner/upload"),
            "maxFilesize"=>30,
            "maxFiles"=>1,
        );
        $path = "kuesioner";
        $dropzone = array(
            "path"=>$path,
            "autoupload"=>false,
            "url"=>base_url("/upload/berkas/".$path),
            "maxFilesize"=>30,
            "maxFiles"=>1,
        );

        $detail[] = array('group'=>1, 'urutan'=>15, 'type'=> ($readonly==true ? 'viwfil' : 'fil'), 'maxlength'=>'100', 'label'=> 'File', "icon"=>true, "location"=>"/assets/kuesioner/", "value"=>$jwb_file, 'namanya'=> 'filBerkas', 'size'=> '400', 'dropzone'=>$dropzone);
        if($readonly==true && $jwb_file==null){
            $inputfile = "-";
        }else{
            $inputfile = generateinputfile($detail[0]);
        }
        
        // debug_array($detail[0]);
        // debug_array($jwb_link);
        $content = '
            <div class="form-group">
                <label>Process Area</label>
                <input type="text" class="form-control" name=idk_nama id=idk_nama value="'.$idk_nama.'" readonly>
                <input type="hidden" class="form-control" name=jwb_usrnam id=jwb_usrnam value="'.$jwb_usrnam.'" readonly>
                <input type="hidden" class="form-control" name=tny_idents id=tny_idents value="'.$tny_idents.'" readonly>
                <input type="hidden" class="form-control" name=jwb_status id=jwb_status value="'.$jwb_status.'" readonly>
            </div>
            
            <div class="form-group">
                <label>Pertanyaan</label>
                <div class="input-group">
                    <input type="hidden" class="form-control" id=tny_pertanyaan name=tny_pertanyaan  value="'.$tny_pertanyaan.'" readonly/>
                    <div class="alert alert-custom alert-default" role="alert" style="width:100%">
                        <div class="alert-icon">
                        <li class="fas fa-question-circle"></li>
                        </div>
                        <div class="alert-text">'.$tny_pertanyaan.'</div>
                    </div>                    
                </div>
            </div>
            <div class="form-group">
                <span name=idk_petunjuk id=idk_petunjuk readonly>'.$idk_petunjuk.'</span>
            </div>
            <div class="form-group">
                <label>Jawaban  <span class="text-danger">*</span></label>
                <div class="radio-list">
                '.$input_radio.'
                </div>
            </div>
            <div class="form-group">
                <label>Deskripsi  <span class="text-danger">*</span></label>
                '.$textarea.'
            </div>
            <div class="form-group">
                <label>Link</label>
                ' . $input_link . '
            </div>
            
            <div class="form-group">
                <label>Berkas</label>
                '.$inputfile.'
            </div>
            <div class="form-group">
                <label>Nilai (%)</label>
                <input type="text" class="form-control"  name=nli_asesor  id=nli_asesor value="'. $nli_asesor . '">
        
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea type="text" class="form-control" name=ctn_asesor id=ctn_asesor >'. $ctn_asesor . '</textarea>
            </div>
            
            ' . $script_link . '
            <script>
            $(document).ready(function () {
                $("#btnTolak").on("click",function(){
                    jvApprove(22, '.$jwb_status.');
                });
              
                
            });
            </script>  
           
                        
        ';
        echo $content;
    }
    function approve(){

        $jwb_idents = $this->input->post('jwb_idents');
        $jwb_status = $this->input->post('jwb_status');
        $jwb_usrnam = $this->input->post('jwb_usrnam');
        $jwb_appnam = $this->input->post('jwb_appnam');
        $jwb_alasan = $this->input->post('jwb_alasan');
        $ctn_asesor = $this->input->post('ctn_asesor');
        $nli_asesor = $this->input->post('nli_asesor');     

        $input["jwb_status"] = $jwb_status;
        $input["jwb_asenam"] = $this->username;
        $input["jwb_asedat"] = $this->datesave;
        $input["nli_asesor"] = $nli_asesor;
        $input["ctn_asesor"] = $ctn_asesor;

        if($jwb_status=="1"){
            $text = "Persetujuan";
        }else{
            $text = "Penolakan";
        }
        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table_jawaban, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-",
            "log_fkidents"=>$jwb_idents,
            "log_action"=>array("jwb_idents"=>$jwb_idents, "action"=> $text . " Asesmen")
        );
        $this->common->logmodul(false, $arrModul);  
        $this->crud->useTable($this->table_jawaban);
        if(!$this->crud->save($input, array("jwb_idents"=>$jwb_idents))){
            $return["message"] = "Kuesioner gagal disimpan!";
        }else{
            if($jwb_status==23){
                $title = "[ASESOR] Jawaban Kuesioner tidak Lulus!";
                $body = "Mohon maaf, jawaban kuesioner anda memiliki nilai (%) < 85 , dengan catatan : " . $jwb_alasan;
                $this->common->notifyUser(1, $jwb_usrnam, $this->username, $title,$body, $jwb_idents);
                $this->common->notifyUser(1, $jwb_appnam, $this->username, $title,$body, $jwb_idents);
            }
            if($jwb_alasan!=null){
                $this->crud->useTable("t_asm_asesmen_jawaban");
                
                $history["jwb_status"] = $jwb_status;
                $history["jwb_asenam"] = $this->username;
                $history["jwb_asedat"] = $this->datesave;
                $history["nli_asesor"] = $nli_asesor;
                $history["ctn_asesor"] = $ctn_asesor;
                $this->crud->save($history);
            }

            $return["idents"] = $jwb_idents;
            $return["message"] = $text . " Kuesioner berhasil disimpan!";
        }
        echo json_encode($return);
    }
    function setujui()
    {

    $this->m_asesmen->restorejawaban($aso_idents, $idk_process_area, $aso_operator, $tny_level);
    }
    function insertinbox(){
        $jwb_type = $this->input->post("jwb_type");
        $jwb_status = $this->input->post("jwb_status");
        $jwb_usrnam = $this->input->post("jwb_usrnam");
        $jwb_appnam = $this->input->post("jwb_appnam");
        $tny_level = $this->input->post("tny_level");
        $jwb_alasan = $this->input->post("jwb_alasan");
        $idk_process_area = $this->input->post("idk_process_area");
        $aso_kelompok_indikator = $this->input->post("aso_kelompok_indikator");

        if($jwb_type=="2"){
            $title = "[ASESOR]";
        }else{
            $title = "[APPROVAL]";
        }


        $jwb_idents = null;
        if($jwb_status==2){
            $title .= " Jawaban Kuesioner Ditolak!";
            $body = "Mohon maaf, jawaban kuesioner kategori " . $aso_kelompok_indikator . ", process area " . $idk_process_area. ", " . $tny_level . " anda ditolak, dengan catatan : " . $jwb_alasan;
        }
        if($jwb_status==1){
            $title .= " Jawaban Kuesioner sudah disetujui";
            $body = "Kuesioner Kategori " . $aso_kelompok_indikator . ", Process area " . $idk_process_area. ", " . $tny_level . " sudah disetujui";
        }
        if($jwb_usrnam!=null){
            $this->common->notifyUser(1, $jwb_usrnam, $this->username, $title,$body, $jwb_idents);
        }
        if($jwb_appnam!=null){
            $this->common->notifyUser(1, $jwb_appnam, $this->username, $title,$body, $jwb_idents);
        }
        
    }
    function save(){
        $nil_idents = $this->input->post('nil_idents');
        $nil_catatan = $this->input->post('nil_catatan');
        $ctn_asesor = $this->input->post('ctn_asesor');
        $nli_asesor = $this->input->post('nli_asesor');
        $nil_status = $this->input->post('nil_status');
        $aso_idents = $this->input->post('aso_idents');
        $idk_process_area = $this->input->post('idk_process_area');
        $tny_level = $this->input->post('tny_level');
        $tny_level_form = $this->input->post('tny_level_form');
        
        $hidTRNSKS = $this->input->post("hidTRNSKS");

        $input["nil_asoidents"] = $aso_idents;
        $input["nil_process_area"] = $idk_process_area;
        $input["nil_catatan"] = $nil_catatan;
        $input["ctn_asesor"] = $ctn_asesor;
        $input["nli_asesor"] = $nli_asesor;
        $input["nil_status"] = $nil_status;
        
        $input["nil_tnylevel"] = $tny_level_form;

        if($nil_idents==null){
            $input["nil_usrnam"] = $this->username;
        }else{
            $input["nil_updnam"] = $this->username;
            $input["nil_upddat"] = $this->datesave;
        }
        $url = "/asesor/penilaian";
        $pk = array("nil_idents"=>$nil_idents);

        // $rslJawaban = $this->m_asesmen->getPenilaianJawaban_list($aso_idents, $idk_process_area, $tny_level);
        // $this->common->debug_sql(1);

        $this->common->logmodul(true, array("from"=>$this->modul, "table_name"=>$this->table, "POST"=>$input, "username"=>$this->username, "pk"=>$pk));
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, $pk)){
            $this->common->message_save('save_gagal',null, $url);
        }else{
            // $rslJawaban = $this->m_asesmen->getPenilaianJawaban_list($aso_idents, $idk_process_area, $tny_level);
            // $inputjawaban["jwb_status"] = $nil_status;

            // foreach($rslJawaban->result() as $key=>$value){
            //     $jwb_idents = $value->jwb_idents;
            //     $pkjwb = array("jwb_idents"=>$jwb_idents);
            //     $this->crud->useTable("t_asm_asesmen_jawaban");
            //     $this->crud->save($inputjawaban, $pkjwb);
            // }
            $this->common->message_save('save_sukses',null, $url);
        }
    }
}
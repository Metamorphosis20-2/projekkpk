<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner extends MY_Controller {
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
        $this->usrunitkerja = $this->session->userdata("USR_UNITKERJA");
        $this->usr_level = $this->session->userdata("USR_LEVELS");
        $this->usr_idents = $this->session->userdata("USR_IDENTS");
        $this->table = "t_asm_asesmen_jawaban";
        $this->table_file = "t_asm_asesmen_files";
    }	
	public function index(){
        // $this->common->debug_array($this->session->userdata());
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Kuesioner"),
        );
        $bc = generateBreadcrumb($arrbread);
        // if($this->usr_level==5){
        //     $content = $this->listKuesionerOperator();
        // }else{
        //     $content = $this->listKuesionerView();
        // }
        $content = $this->listKuesionerOperator();
        $this->_render('pages/home', $content,'admin',$bc);  	 
	}
    function listKuesionerOperator(){
        $gridname = "jqxPenugasan";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Asesmenpenugasan/list/m_asesmen/'.$this->usr_idents;
        $url ='/proses/nosj/getAsesmenpenugasan_list/'.$this->usr_idents.'/'.$this->usr_level;
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_idents','aw'=>'150','label'=>"ID Asesmen", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'tny_level','aw'=>'150','label'=>"Level", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_kelompok_indikator','aw'=>'150','label'=>"ID Kategori", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'idk_process_area','aw'=>'150','label'=>"ID Process Area", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_operator','aw'=>'150','label'=>"ID Operator", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_periode_end", "aw"=>80, "label"=>"Tanggal Berakhir");
        if($this->usr_level==3){
            $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lvl_nama','aw'=>'150','label'=>"Level", 'ah'=>false);
        }
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_name", "aw"=>120, "label"=>"Petugas","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_kelompok_indikator_desc", "aw"=>220, "label"=>"Kategori","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"idk_process_area_desc", "aw"=>220, "label"=>"Process Area","adtype"=>"text");        
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_progress", "aw"=>120, "label"=>"Progress Jawaban","adtype"=>"text");
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_progress_value", "aw"=>120, "label"=>"Progress Persetujuan (Persentase)","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_status", "aw"=>120, "label"=>"Status", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"lvl_nama", "aw"=>120, "label"=>"Tingkat","adtype"=>"text");
        
        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);
        $oADD = strpos("N".$otorisasi,"E");
        $callback = null;
        $buttonrow = null;
        if($this->usr_level==4){
            $buttonrow = array("view"=>array("icon"=>"edit", "function"=>"jvAnswer('add', data_row)", "idents"=>"aso_idents", 'alt'=>'Input Jawaban Kuesioner'));
        }else{

            if($this->usr_level==3){
                $btn_false = '<button type="button" class="btn btn-light-dark btn-icon btn-xs" aria-label="Lihat Jawaban Kuesioner" data-microtip-position="right" role="tooltip"><i class="fas fa-eye" style="text-align:center;cursor:pointer;"></i></button>';
                $buttonrow = array(
                    "view"=>array("icon"=>"check-circle", "function"=>"jvAnswer('approve', data_row)", "idents"=>"aso_idents", 'buttonclass'=>'light-primary', 'alt'=>'Setujui Jawaban Kuesioner'),//, "render"=>array("validation"=>"data_row.aso_status==0", "return_false"=>$btn_false)),
                    // "send"=>array("icon"=>"paper-plane", "function"=>"jvAnswer('send', data_row)", "idents"=>"aso_idents", 'buttonclass'=>'primary', 'alt'=>'Ajukan', "render"=>array("validation"=>"data_row.aso_progress_value==100  && data_row.aso_status==0", "return_false"=>"")),
                );
                $callback = "
                'rowCallback': function( row, data ) {
                    // console.log( data.aso_status.toString() );
                    if ( data.aso_status === '1' ) {
                        // $('td:eq(2)', row).css('background-color', 'Red')
                        $('td:eq(0)', row).html('".$btn_false."');
                    }
                },";
            }
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
            'callback'=>$callback,
            // 'buttonother'=> $buttonother,
            // "event"=>$event,
            'surrounded'=>true,
            'sumber'=>'server',
            'closeform'=>false,
            'modul'=>'proses/kuesioner/pertanyaan',
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
        $content .= form_input(array('name' => "asm_periode_end",'id'=> "asm_periode_end", 'type'=>'hidden'));
        $content .= form_close();
        $content .= generateWindowjqx(array('window'=>'Kategori','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        $content .= "
        <script>
        function jvView(data_row){
            aso_idents = data_row['aso_idents'];
            aso_operator = data_row['aso_operator'];
            tny_level = data_row['tny_level'];
            aso_kelompok_indikator = data_row['aso_kelompok_indikator'];
            aso_kelompok_indikator_desc = data_row['aso_kelompok_indikator_desc'];
            $('#grdIDENTS').val(aso_idents);
            $('#aso_type').val('view');
            $('#tny_level').val(tny_level);
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
            aso_status = data_row['aso_status'];
            tny_level = data_row['tny_level'];
            asm_periode_end = data_row['asm_periode_end'];
            $('#grdIDENTS').val(aso_idents);
            $('#aso_type').val(type);
            $('#tny_level').val(tny_level);
            $('#aso_kelompok_indikator').val(aso_kelompok_indikator);
            $('#aso_operator').val(aso_operator);
            $('#aso_status').val(aso_status);
            $('#aso_kelompok_indikator_desc').val(aso_kelompok_indikator_desc);
            $('#idk_process_area').val(idk_process_area);
            $('#asm_periode_end').val(asm_periode_end);
            $('#idk_general').val(0);
            
            
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
    function pertanyaan(){
        $date_now = Date('Y-m-d');
        $button = null;
        $tny_level = null;
        // $this->common->debug_post();
        $aso_type = $this->input->post("aso_type");
        $aso_status = $this->input->post("aso_status");
        $idk_general = $this->input->post("idk_general");
        $idk_force = $this->input->post("idk_force");
        $idk_level = $this->input->post("idk_level");
        if($aso_status==1){
            $aso_type = "view";
        }
        $aso_idents = $this->input->post("grdIDENTS");
        $aso_operator = $this->input->post("aso_operator");
        $aso_kelompok_indikator = $this->input->post("aso_kelompok_indikator");
        $aso_kelompok_indikator_desc = $this->input->post("aso_kelompok_indikator_desc");
        $idk_process_area = $this->input->post("idk_process_area");
        $asm_periode_end = $this->input->post("asm_periode_end");

        $date_end=date_create($asm_periode_end);
        $date_now=date_create($date_now);
        $periode_end = false;
        $text_end = null;
        if($date_end<$date_now){
            $periode_end = true;
            $text_end = "<center>Periode Asesmen sudah berakhir, data tidak bisa diedit</center>";
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
        $formcommand = "/proses/kuesioner/pertanyaan";
        $form_create = form_open_multipart($formcommand, $attr);

        $rslJawabanDetail = $this->m_asesmen->getKategoriJawaban($aso_idents, $idk_process_area);
        
        $visible_level =null;
        $showgeneral = false;
        if($rslJawabanDetail->num_rows()>0){
            foreach($rslJawabanDetail->result() as $key=>$valueJ){
                $idk_idents = $valueJ->idk_idents;
                $tny_level = $valueJ->tny_level;
                $jwb_status = $valueJ->jwb_status;
                $cnt_tny_idents = $valueJ->cnt_tny_idents;
                $cnt_jwb_idents = $valueJ->cnt_jwb_idents;
                $lanjut_level = true;
                
                if($tny_level==0){
                    if($cnt_tny_idents!=$cnt_jwb_idents){
                        $showgeneral = true;
                        $lanjut_level = false;
                        break;
                    }else{
                        // $tny_level++;
                        $showgeneral = false;
                        $lanjut_level = true;
                    }
                }
                if($lanjut_level){
                    $lanjut = 0;
                    $rslMax = $this->m_asesmen->getKategoriJawabanMax($aso_idents, $idk_process_area);
                    // $this->common->debug_sql(1);
                    $rslMax_num_rows = $rslMax->num_rows();
                    if($rslMax_num_rows>0){
                        // debug_array($rslMax->result());
                        foreach($rslMax->result() as $keyMax=>$valueMax){
                            $jwb_status_max = $valueMax->jwb_status;

                            if($jwb_status_max!=21){
                                $lanjut++;
                            }
                        }

                        $rowMax = $rslMax->row();
                        $tny_level = $rowMax->tny_level;
                        if($lanjut==0){
                            $tny_level++;
                        }
                        break;
                    }else{
                        $tny_level++;
                        break;
                    }
                }
            }
        }
        if($aso_type=="approve"){
            $tny_level = $this->input->post("tny_level");
        }
        if($idk_force==1){
            $tny_level = $this->input->post("idk_level");
        }

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
        $lvl_berkas = 0;
        if($tny_level!=0){
            $rsl = $this->m_master->getTingkat_edit($tny_level);
            if($rsl->num_rows()>0){
                $row = $rsl->row();
                // debug_array($row);
                $lvl_nama = $row->lvl_nama;
                $lvl_petunjuk = $row->lvl_petunjuk;
                $lvl_berkas = $row->lvl_berkas;
            }
            // debug_array($rsl);
        }
        if($rslKategori->num_rows()>0){
            // $tablehead = "<thead><tr><th style='width:80px'>No</th><th>Pertanyaan</th><th>Action</th><th>ID Jawaban</th><th>Jawaban</th><th>Deskrisi</th><th>Link</th><th>File</th></tr></thead>";
            $tablehead = "<thead>
                    <tr>
                        <th style='width:80px'>No</th>
                        <th>Level</th>
                        <th>Pertanyaan</th>
                        <th>Aksi</th>
                        <th>Riwayat</th>
                        <th>Kriteria</th>
                        <th>ID Pertanyaan</th>
                        <th>Kategori</th>
                        <th>ID Jawaban</th>
                        <th>Jawaban</th>
                        <th>Deskripsi</th>
                        <th>Link</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Pengguna</th>
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
                    // $table .= "<thead><tr><th style='width:80px'>No</th><th>Pertanyaan</th><th>Jawaban</th></tr></thead>";
                    $table .= $tablehead;
                    $table .= "<tbody>";
                    $loop_detail =0;
                }
                $urutan = $loop_detail+1;
                $table .= "<tr>";
                $table .= " <td style='width:80px'>" . $urutan . "</td>";
                $table .= " <td>" . $tny_level_grid . "</td>";
                
                $table .= " <td id=id_".$tny_idents."_1>" . $tny_pertanyaan . "</td>";
                // $table .= " <td><button type='button' class='btn btn-danger btn-sm'>Danger</button></td>";
                // $table .= " <td><a id='btn_".$tny_idents."' href='javascript:jvAnswer(\"".$idtable."\",".$tny_idents.", \"".$idk_nama."\",\"".$tny_pertanyaan."\")' class='btn btn-primary font-weight-bold btn-pill btn-sm'>&nbsp;&nbsp;<i id='ico_".$tny_idents."' class='fas fa-reply' style='font-size:12px'></i></a></td>";
                $table .= " <td></td>";
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
                $table .= " <td>".$periode_end."</td>";
                $table .= "</tr>";
                
                $hidden .= form_input(array('name' => "id_".$tny_idents."_idents",'id'=> "id_".$tny_idents."_idents", 'type'=>'hidden',"value"=>$jwb_idents));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_jawab",'id'=> "id_".$tny_idents."_jawab", 'type'=>'hidden',"value"=>$jwb_jawab));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_deskripsi",'id'=> "id_".$tny_idents."_deskripsi", 'type'=>'hidden',"value"=>$jwb_deskripsi));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_link",'id'=> "id_".$tny_idents."_link", 'type'=>'hidden',"value"=>$jwb_link));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_file",'id'=> "id_".$tny_idents."_file", 'type'=>'hidden',"value"=>$jwb_file));
                $hidden .= form_input(array('name' => "id_".$tny_idents."_status",'id'=> "id_".$tny_idents."_status", 'type'=>'hidden',"value"=>$jwb_file));
                // $hidden .= form_input(array('name' => "id_".$tny_idents."_petunjuk",'id'=> "id_".$tny_idents."_file", 'type'=>'hidden',"value"=>$tny_petunjuk));

                $jawab[] = $tny_idents;
                $idk_idents_temp = $idk_idents;
                $idk_nama_temp = $idk_nama;
                $loop_detail++;
                $loop++;
                if($loop==$num_rows){
                    $table .= "</tbody>";
                    $table .= "</table>";
                    // debug_array($arrTabs);
                    $aso_process_area_desc = $idk_nama_temp . ($idk_general==1 ? "" : ($lvl_nama=="" ? "" : " - <b>" .$lvl_nama . "</b>"));
                    $tab_nama = "fas fa-question-circle^" . $aso_process_area_desc;
                    $arrTabs[$tab_nama] = array("data"=>$text_end . $table);
                }            
            }
            
            $script .= "<script>            
            jQuery(document).ready( function ($) {
                // $.noConflict();
                $('#modalQuestion').on('hidden.bs.modal', function () {
                    $('#berkasgw').destroy();
                });
            ";
            /*
            kosong = info
            isi = success            
            approve = 
            */
            if($aso_type=="add" || $aso_type=="edit"){
                $render_validation = "
                if(data.jwb_idents!=''){
                    switch(data.jwb_status){
                        case '1':
                            return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-success btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-check\" style=\"color:#fff\"/></a>'
                            break;
                        case '2':
                            return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-danger btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-frown\" style=\"color:#fff\"/></a>'
                            break;
                        case '21': //disetujui asesor;
                            return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-approved btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-thumbs-up\" style=\"color:#fff\"/></a>'
                            break;
                        case '22':
                            return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-danger btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-thumbs-down\" style=\"color:#fff\"/></a>'
                            break;
                        default:
                            return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-filled btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-file-signature\" style=\"color:#fff\"/></a>'
                            break;
                    }
                }else{
                    if(data.periode_end){
                        return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-pengajuan btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-eye\" style=\"color:#fff\"/></a>'
                    }else{
                        return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-pengajuan btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-pencil-alt\" style=\"color:#fff\"/></a>'
                    }
                }                
                ";
            }else{
                if($this->usr_level<=2){
                    $render_validation = "
                    if(data.jwb_idents!=''){
                        switch(data.jwb_status){
                            case '1':
                            case '21':
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-light-success btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-check\"/></a>'
                                break;
                            case '2':
                            case '22':
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-light-danger btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-thumbs-down\"/></a>'
                                break;
                            default:
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-light-info btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-eye\"/></a>'
                                break;
                        }
                        return kembali;
                    }else{
                        return ''
                    }
                    ";
                }else{
                    $render_validation = "
                    if(data.jwb_idents!=''){

                        switch(data.jwb_status){
                            case '0':
                                if(data.tny_level==0){
                                    icon = 'eye';
                                }else{
                                    icon = 'file-signature';
                                }
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-filled btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-'+icon+'\" style=\"color:#fff\"/></a>';
                                break;
                            case '1':
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-success btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-check\"/></a>'
                                break;
                            case '2':
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-danger btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-frown\"/></a>'
                                break;
                            case '21':
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-approved btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-thumbs-up\" style=\"color:#fff\"/></a>'
                                break;
                            case '22':
                                kembali = '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-danger btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-thumbs-down\"/></a>'
                                break;
                            default:
                                kembali = ''
                                break;
                        }
                        return kembali;
                    }else{
                        return ''
                    }
                    ";
    
                }
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
                            {   data: 'jwb_status',width: '0px', visible:false},
                            {   data: 'jwb_usrnam',width: '0px', visible:false},
                            {   data: 'periode_end',width: '0px', visible:false},
                        ]
                    });
                    $('#".$keyID."').show();
                    recordsTotal = oDT_".$keyID.".page.info().recordsTotal;
                    
                    $('#".$keyID."').on('click', 'td.editor-view', function (e) {
                        e.preventDefault();
                        var data_row = oDT_".$keyID.".row( $(this).parents('tr') ).data(); 
                        let row = oDT_".$keyID.".row('#row-' + data_row.tny_idents);
                        jvAnswer('".$aso_type."', data_row, this)
                    } );
                    $('#".$keyID."').on('click', 'td.history-view', function (e) {
                        e.preventDefault();
                        var data_row = oDT_".$keyID.".row( $(this).parents('tr') ).data(); 
                        jvHistory(data_row, this)
                    } );
                ";
            }
            if($aso_type=="add" || $aso_type=="edit"){
                $button = '<button type="button" class="btn btn-primary" onclick="jvSave()">Save</button>';
                $status_edit = 'edit';
                $status_add = 'add';
            }else{
                if($aso_type!="view"){
                    if($this->usr_level>2){
                        if($tny_level!=0){
                            $button = '<button id=btnTolak type="button" class="btn btn-danger" onclick="jvApprove(2)">Tolak</button><button id=btnSetujui type="button" class="btn btn-primary" onclick="jvApprove(1)">Setujui</button>';
                        }
                    }
                }
                $status_edit = 'approval';
                $status_add = 'approval';
            }
            if($periode_end){
                $button = null;
            }
            $script .= "
            emptyfield = function(){
                $('#idk_nama').val('');
                $('#jwb_idents').val(0);
                $('#tny_idents').val(0);
                $('#tny_pertanyaan').val('');
                $('#jwb_link').val('');
            }
            });
            var tanya = " . json_encode($jawab). "
            function isUrlValid(url) {
                return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
            }
            function jvGeneral(){
                $('#idk_general').val(1);
                document.formgw.submit();
            }
            function jvGoLevel(level){
                $('#idk_force').val(1);
                $('#idk_level').val(level);
                document.formgw.submit();
            }
            function jvBackInput(){
                $('#idk_general').val(0);
                // $('#tny_level').val(0);
                document.formgw.submit();
            }
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
            function jvAnswer(type, data_row, this_row){
                emptyfield();
                var tny_idents = data_row.tny_idents;
                var idk_nama = data_row.idk_nama;
                var tny_pertanyaan = data_row.tny_pertanyaan;
                var tny_petunjuk = data_row.jwb_petunjuk;
                var jwb_usrnam = data_row.jwb_usrnam;
                var jwb_status = data_row.jwb_status;
                var tny_level = data_row.tny_level;
                var periode_end = data_row.periode_end;

                jwb_idents = $('#id_' + tny_idents + '_idents').val();
                jwb_jawab = $('#id_' + tny_idents + '_jawab').val();
                jwb_deskripsi = $('#id_' + tny_idents + '_deskripsi').val();
                jwb_link = $('#id_' + tny_idents + '_link').val();
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
                    if(jwb_status==1 && periode_end==false){
                        swal.fire({ 
                            title:'Jawaban anda sudah disetujui!', 
                            text: 'Data tidak bisa diubah',
                            icon: 'error'
                        })
                        $('#hidTRNSKS').val('view');
                        $('#modal-footer-question').hide();
                    }
                    if(jwb_status==21 && periode_end==false){
                        swal.fire({ 
                            title:'Jawaban anda sudah disetujui Asesor!', 
                            text: 'Data tidak bisa diubah',
                            icon: 'error'
                        })
                        $('#hidTRNSKS').val('view');
                        $('#modal-footer-question').hide();
                    }
                    if(jwb_status==22 && periode_end==false){
                        swal.fire({ 
                            title:'Jawaban ditolak asesor!', 
                            text: 'Menunggu Respon Dari Produsen Data',
                            icon: 'error'
                        })
                        $('#hidTRNSKS').val('view');
                        $('#modal-footer-question').hide();
                    }
                }else{
                    if(jwb_status==22){
                        $('#btnSetujui').hide();
                    }else{
                        if(jwb_status==21){
                            $('#btnSetujui').hide();
                            $('#btnTolak').hide();
                        }else{
                            $('#btnTolak').show();
                            $('#btnSetujui').show();
                        }
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
                    param['hidTRNSKS'] = $('#hidTRNSKS').val();
                    param['jwb_usrnam'] = jwb_usrnam;
                    param['jwb_status'] = jwb_status;
                    param['tny_level'] = tny_level;
                    $('#imgPROSES').show();
                    $('#windowProses').jqxWindow('open');
                    $.post('/proses/kuesioner/modalpertanyaan', param,function(data){
                        $('#windowProses').jqxWindow('close');
                        $('#modal-body-question').html(data);
                        $('#modalQuestion').modal({backdrop: 'static', keyboard: false});
                        window.$('#modalQuestion').modal('show');
                    });
                }
            }
            function jvApprove(jwb_status){
                var jwb_idents = $('#jwb_idents').val();
                var tny_idents = $('#tny_idents').val();
                var jwb_usrnam = $('#jwb_usrnam').val();
                var lanjut = false;
                var param = {};
                param['jwb_idents'] = jwb_idents ;
                param['jwb_status'] = jwb_status ;
                param['jwb_usrnam'] = jwb_usrnam ;
                param['jwb_status'] = jwb_status;
                $('#hidTRNSKS').val('');

                if(jwb_status==2){
                    const { value: text } = swal.fire({
                        title:'".$this->lang->line("btnTolak")." jawaban Kuesioner?', 
                        text:'".$this->lang->line("confirm_reason")."',
                        target: document.getElementById('modalQuestion'),
                        icon: 'question',
                        input: 'textarea',
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
                        console.log(result.isConfirmed);
                        if(result.isConfirmed==true) {
                            console.log(result.value);
                            var alasan = result.value;
                            param['jwb_alasan'] = alasan;                                                                
                            $.post('/proses/kuesioner/approve',param,function(rebound){
                                if(rebound){
                                    message = 'Jawaban berhasil ditolak!'
                                    class_button = 'btn-danger';
                                    class_fawesome = 'fas fa-frown';
                                    closeModal(message, class_button, class_fawesome, tny_idents)
                                    lanjut = true;
                                }
                            });
                        }
                    })
                }else{
                    $.post('/proses/kuesioner/approve', param,function(data){
                        var result = $.parseJSON(data);
                        message = 'Jawaban berhasil disetujui!';
                        class_button = 'btn-success';
                        class_fawesome = 'fas fa-check';
                        lanjut = true;
                        closeModal(message, class_button, class_fawesome, tny_idents)
                    });
                }
            }
            function closeModal(message, class_button, class_fawesome, tny_idents){
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
                Command: toastr['success'](message)
                var fasClass = $('#ico_' + tny_idents).attr('class');
                $('#btn_' + tny_idents).removeClass('btn-filled').addClass(class_button);
                $('#ico_' + tny_idents).removeClass(fasClass).addClass(class_fawesome);
                window.$('#modalQuestion').modal('hide');
            }
            function jvSave(){
                var aso_idents = $('#aso_idents').val();
                var hidTRNSKS = $('#hidTRNSKS').val();
                var tny_idents = $('#tny_idents').val();
                var desc = CKEDITOR.instances.jwb_deskripsi.getData();
                var rdoYa = $('input[name=\"jwb_jawab\"]:checked').val();
                var link = $('#jwb_link').val();
                var jwb_idents = $('#jwb_idents').val();
                var jwb_status = $('#jwb_status').val();
                lanjut = true;
                if(link!=''){
                    var linknya = '';
                    var rc = false;
                    for(e=0;e<link.length;e++){
                        if(rc){
                            linknya = linknya + ';'
                        }
                        linke = link[e];
                        if(!isUrlValid(linke)){
                            swal.fire({ 
                                title:'Link tidak valid!', 
                                icon: 'error'
                            })
                            $('#jwb_link').select();
                            lanjut = false;
                            break;
                        }else{
                            linknya = linknya + linke;
                            rc=true;
                        }
                    }
                }
                var param = {};
                param['jwb_asoidents'] = aso_idents ;
                param['jwb_tnyidents'] = tny_idents ;
                param['jwb_idents'] = jwb_idents ;
                param['jwb_deskripsi'] = desc;
                param['jwb_jawab'] = rdoYa;
                param['jwb_link'] = link;
                param['jwb_status'] = 0;
                param['hidTRNSKS'] = hidTRNSKS;

                var myDz = Dropzone.forElement('.dropzone');
                let message;
                if(lanjut){
                    if (myDz.getQueuedFiles().length === 0) {
                        if(rdoYa==1 && $('#hidTRNSKS').val()=='add' && link==''){
                            lanjut=false;
                            swal.fire({ 
                                title:'Berkas/Link tidak ditemukan!', 
                                text:'Untuk pilihan jawaban Ya, berkas/link harus disertakan!',
                                icon: 'error'
                            })
                        }else{
                            $.post('/proses/kuesioner/save', param,function(data){
                                var result = $.parseJSON(data);
                                message = result.message
                                $('#hidTRNSKS').val('');
                                $('#id_' + tny_idents + '_idents').val(result.idents);
                                $('#id_' + tny_idents + '_jawab').val(rdoYa);
                                $('#id_' + tny_idents + '_deskripsi').val(desc);
                                $('#id_' + tny_idents + '_link').val(linknya);
                                checkRow();
                            });
                        }
                    }else{
                        myDz.processQueue();
                        myDz.on('success', (function(file, response) {
                            console.log(file);
                            myDz.removeFile(file);
                            param['jwb_file'] = response;

                            $.post('/proses/kuesioner/save', param,function(data){
                                var result = $.parseJSON(data);
                                message = result.message
                                $('#hidTRNSKS').val('');
                                $('#id_' + tny_idents + '_idents').val(result.idents);
                                $('#id_' + tny_idents + '_jawab').val(rdoYa);
                                $('#id_' + tny_idents + '_deskripsi').val(desc);
                                $('#id_' + tny_idents + '_link').val(linknya);
                                $('#id_' + tny_idents + '_file').val(response);
                            });
                        }))
                    }
                }
                if(lanjut){
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
                    Command: toastr['success']('Jawaban Kuesioner berhasil disimpan!')
                    if(jwb_status==2){
                        btnClass = 'btn-danger';
                        fasClass = 'fas fa-thumbs-down';
                    }else{
                        btnClass = 'btn-primary';
                        fasClass = 'fas fa-reply';
                    }
                    var fasClass = $('#ico_' + tny_idents).attr('class');
                    $('#btn_' + tny_idents).removeClass(btnClass).addClass('btn-filled');
                    $('#ico_' + tny_idents).removeClass(fasClass).addClass('fas fa-file-signature');
                    window.$('#modalQuestion').modal('hide');
                }
            }

            function jvUpload(){
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                var param = {};
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
            function jvSaveUpload(){

                var myDz = Dropzone.forElement('.dropzone');
                let message;
                    if (myDz.getQueuedFiles().length === 0) {
                        swal.fire({ 
                            title:'Berkas harus diunggah!', 
                            icon: 'error'
                        })
                    }else{
                        var param = {};
                        param['aso_idents'] = $('#aso_idents').val();
                        param['tny_level'] = $('#tny_level').val();
                        param['aso_process_area'] = $('#idk_process_area').val();
                        myDz.processQueue();
                        myDz.on('successmultiple', (function(file, response) {
                            param['file'] = response;
                            
                            console.log(response);
                            $.post('/proses/kuesioner/saveupload', param,function(data){
                                $('#jqwPopup').jqxWindow('close');
                                swal.fire({ 
                                    title:'Berkas berhasil diunggah!', 
                                    icon: 'info'
                                })
        
                            });
                        }))
                    }
            }
            function checkRow(){
                tanya_count = tanya.length;
                tanya.forEach(function(item) {
                    jawab_id = $('#id_' + item + '_idents').val();
                    if(jawab_id!=''){
                        tanya_count--;
                    }
                });
                if(tanya_count==0){
                    if($('#tny_level').val()==0){
                        swal.fire({
                            title:'Lanjutkan ke Level berikutnya?', 
                            text:'Pertanyaan Umum sudah selesai diinput',
                            icon: 'question',
                            showCancelButton: true, 
                            confirmButtonText: '".$this->lang->line("Ya")."', 
                            cancelButtonText: '".$this->lang->line("Tidak")."', 
                            confirmButtonColor: '".$this->config->item("confirmButtonColor")."', 
                            cancelButtonColor: '".$this->config->item("cancelButtonColor")."'
                        }).then(result => {
                            if(result.isConfirmed==true) {
                                window.location.reload(true);
                            }
                        })
                    }
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
            function jvGlossary(){
                $('#imgPROSES').show();
                $('#windowProses').jqxWindow('open');
                $('#jqwPopup').jqxWindow('open');
                $.post('/proses/kuesioner/glossary',null,function(datax){
                    $('#windowProses').jqxWindow('close');
                    var lebar = $(window).width();
                    var tinggi = 600;
                    $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: false, autoOpen: false,width: lebar, height:tinggi,position:'middle', resizable:true,title: 'Glossary'});  
                    $('#jqwPopup').jqxWindow('setContent', datax);
                });

            }
            </script>
            ";
            $arrTabs = array(
                "id"=>"Pertanyaan",
                // "bentuk"=>"accordion",
                "arrTabs" => $arrTabs
              );
            // debug_array($arrTabs);
            $content = generateTabjqx($arrTabs);
            // $content .=form_input(array('name' => "aso_idents",'id'=> "aso_idents", 'type'=>'hidden', 'value'=>$aso_idents));
            // $content .=form_input(array('name' => "jwb_idents",'id'=> "jwb_idents", 'type'=>'hidden'));
            // $content .=form_input(array('name' => "hidFile",'id'=> "hidFile", 'type'=>'hidden'));
            // $content .=form_input(array('name' => "hidTRNSKS",'id'=> "hidTRNSKS", 'type'=>'hidden'));            
            // $content .=form_input(array('name' => "tny_level",'id'=> "tny_level", 'type'=>'hidden', 'value'=>$tny_level));
            // $content .=form_input(array('name' => "idk_process_area",'id'=> "idk_process_area", 'type'=>'hidden', 'value'=>$idk_process_area));
            $content .= $form_create;
            $content .=form_input(array('name' => "aso_idents",'id'=> "aso_idents", 'type'=>'hidden', 'value'=>$aso_idents));
            $content .=form_input(array('name' => "grdIDENTS",'id'=> "grdIDENTS", 'type'=>'hidden', 'value'=>$aso_idents));
            $content .=form_input(array('name' => "aso_type",'id'=> "aso_type", 'type'=>'hidden', 'value'=>$aso_type));
            $content .=form_input(array('name' => "aso_status",'id'=> "aso_status", 'type'=>'hidden', 'value'=>$aso_status));
            $content .=form_input(array('name' => "idk_general",'id'=> "idk_general", 'type'=>'hidden', 'value'=>$idk_general));
            $content .=form_input(array('name' => "aso_operator",'id'=> "aso_operator", 'type'=>'hidden', 'value'=>$aso_operator));
            $content .=form_input(array('name' => "aso_kelompok_indikator",'id'=> "aso_kelompok_indikator", 'type'=>'hidden', 'value'=>$aso_kelompok_indikator));
            $content .=form_input(array('name' => "aso_kelompok_indikator_desc",'id'=> "aso_kelompok_indikator_desc", 'type'=>'hidden', 'value'=>$aso_kelompok_indikator_desc));
            $content .=form_input(array('name' => "jwb_idents",'id'=> "jwb_idents", 'type'=>'hidden'));
            $content .=form_input(array('name' => "hidFile",'id'=> "hidFile", 'type'=>'hidden'));
            $content .=form_input(array('name' => "hidTRNSKS",'id'=> "hidTRNSKS", 'type'=>'hidden'));            
            $content .=form_input(array('name' => "tny_level",'id'=> "tny_level", 'type'=>'hidden', 'value'=>$tny_level));
            $content .=form_input(array('name' => "idk_process_area",'id'=> "idk_process_area", 'type'=>'hidden', 'value'=>$idk_process_area));
            $content .=form_input(array('name' => "idk_force","id"=>"idk_force", "type"=>"hidden", "value"=>"0"));
            $content .=form_input(array('name' => "idk_level","id"=>"idk_level", "type"=>"hidden", "value"=>$idk_level));
            $content .= $hidden;
            $content .= form_close();
            $content .= $script;
        }else{
            $content = "<blockquote>Data Pertanyaan tidak Ada! Mohon hubungi Administrator Aplikasi</blockquote>";
        }
        $buttonpetunjuk ='&nbsp;&nbsp;<span class="label label-primary mr-2" onclick="jvPetunjuk()" style="cursor:pointer" ><i class="fas fa-info-circle" style="color:#fff"></i></span>';
        $buttonglossary ='&nbsp;&nbsp;<span onclick="jvGlossary()" style="cursor:pointer" ><i class="fas fa-book" style="color:red"></i></span>';
        if($lvl_berkas==1){
            $buttonatas[] = array("iconact"=>"fas fa-upload", "theme"=>"primary","href"=>"javascript:jvUpload()", "textact"=>"Unggah");
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
        if($this->usr_level==4){
            $resultlain = $this->m_asesmen->getAsesmenbefore_list($aso_idents, $tny_level);
            if($resultlain->num_rows()>0){
                // debug_array($resultlain->result());
                foreach($resultlain->result() as $keyR=>$valueR){
                    $text = $valueR->lvl_nama;
                    $lvl_idents = $valueR->lvl_idents;
                    $dropdown_menu[] = array("href"=>"javascript:jvGoLevel(" . $lvl_idents . ")", "text"=>$text);
                }
                $buttonatas[] = array("iconact"=>"fas fa-history", "theme"=>"success","href"=>"#", "textact"=>"Level", "dropdown"=>true, "dropdown_menu"=>$dropdown_menu);
                if($idk_force){
                    $idk_level++;
                    $buttonatas[] = array("iconact"=>"fas fa-angle-double-right", "theme"=>"danger","href"=>"javascript:jvGoLevel(" . $idk_level . ")", "textact"=>"");
                }
            }

            // $this->common->debug_sql(1);
        }
        
        // $dropdown_menu[] = array("href"=>"#", "text"=>"Another action");
        // $dropdown_menu[] = array("href"=>"#", "text"=>"Something else");


        $portlet = array("content"=>$content,"title"=>"Kategori " . $aso_kelompok_indikator_desc . $buttonpetunjuk . $buttonglossary, "icon"=>"fas fa-map");
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
        <form action="'.base_url(). '/kuesioner/pertanyaan/save" name="frmKuesioner" id="frmKuesioner" enctype="multipart/form-data" method="post" accept-charset="utf-8">
            <div class="modal" id="modalQuestion" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="headermodal"><i class="fas fa-question-circle" style="color:red"></i> Kuesioner Kategori <b>'.$aso_kelompok_indikator_desc.'</b></h5>
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
        </form>
        <div class="modal" id="modalPetunjuk" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body" id="modal-body-petunjuk">
                        <table>
                            <tr><td style="width:80px"><a class="btn btn-pengajuan btn-sm"><i class="fas fa-pencil-alt" style="color:#fff"></i></a></td><td>Jawaban siap diisi</td></tr>
                            <tr><td style="width:80px"><a class="btn btn-filled btn-sm"><i class="fas fa-file-signature" style="color:#fff"></i></a></td><td>Jawaban sudah diisi siap disetujui</td></tr>
                            <tr><td style="width:80px"><a class="btn btn-success btn-sm"><i class="fas fa-check" style="color:#fff"></i></a></td><td>Jawaban sudah disetujui Produsen Data(Tidak dapat diedit)</td></tr>
                            <tr><td style="width:80px"><a class="btn btn-danger btn-sm"><i class="fas fa-frown" style="color:#fff"></i></a></td><td>Jawaban anda ditolak Produsen Data</td></tr>
                            <tr><td style="width:80px"><a class="btn btn-success btn-sm"><i class="fas fa-thumbs-up"></i></a></td><td>Jawaban disetujui Asesor</td></tr>
                            <tr><td style="width:80px"><a class="btn btn-danger btn-sm"><i class="fas fa-thumbs-down"></i></a></td><td>Jawaban ditolak Asesor</td></tr>
                        </table>
                        
                    </div>
                </div>
            </div>
        </div>
        <script>
        var myModalEl = document.getElementById("modalQuestion")
        myModalEl.addEventListener("hidden.bs.modal", function (event) {
            $("#berkasgw").remove();
        })        
        </script>
        ';
        // 
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200', 'widths'=>'640px', 'maxWidth'=>'800px', 'maxHeight'=>'800px','overflow'=>'auto'));
        // $content .= generateWindowjqx(array('window'=>'Detail','title'=>'Periksa','height'=>'200', 'minWidth'=>100,'maxWidth'=>'1800px','overflow'=>'auto'));
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/proses/kuesioner','text'=>"Daftar Kuesioner"),
            array('link'=>'#','text'=>"Input Jawaban"),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);        
        // return $content;        
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
            $inputfile = "<div id='berkasgw'>" . generateinputfile($detail[0]) ."</div>";
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
                <label>Deskripsi</label>
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
            ' . $script_link . '
        ';
        echo $content;
    }
    function uploadwindow(){
        $type = $this->input->post("type");
        $aso_idents = $this->input->post('aso_idents');
		$tny_level = $this->input->post('tny_level');
		$aso_process_area = $this->input->post('aso_process_area');
        $aso_process_area_desc = $this->input->post('aso_process_area_desc');
        $table = null;
        $script = null;
        $rslBerkas = $this->m_asesmen->getAsesmenfile_list($aso_idents, $tny_level, $aso_process_area);
        $type = ($type=="" ? "add" : $type);
        $num_rows = $rslBerkas->num_rows();
        if($num_rows>0){

            $keyID = "BerkasAsesmen";
            $script = '
            <script type="text/javascript" src=' . base_url(PLUGINS."DataTables/datatables.min.js") .'></script>
            <link rel="stylesheet" href=' . base_url(PLUGINS."DataTables/datatables.min.css"). ' type="text/css">
            <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/fontawesome.min.css"). ' type="text/css">
            <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/solid.css"). ' type="text/css">
            ';
            $tablehead = "<thead>
            <tr>
                <th style='width:80px'>No</th>
                <th>ID</th>
                <th>Berkas</th>
                <th>Pengguna</th>
                <th>Tanggal</th>
            </tr>
            </thead>";            
            $loop_detail = 0;
            $loop = 0;
            $table = '<table id='.$keyID.' class="display" style="width:100%;display:none">';
            $table .= $tablehead;
            $table .= "<tbody>";
            foreach($rslBerkas->result() as $key=>$value){
                $fil_idents = $value->fil_idents;
                $fil_filename = $value->fil_filename;
                $fil_usrnam = $value->fil_usrnam;
                $fil_usrdat = $value->fil_usrdat;
    
                $idtable = 'tblBerkas_'.$fil_idents;
                $urutan = $loop_detail+1;
                $table .= "<tr>";
                $table .= " <td style='width:80px'>" . $urutan . "</td>";
                $table .= " <td>" . $fil_idents . "</td>";
                $table .= " <td>" . $fil_filename . "</td>";
                $table .= " <td>" . $fil_usrnam . "</td>";
                $table .= " <td>".$fil_usrdat. "</td>";
                $table .= " <td></td>";
                $table .= "</tr>";
                
                $loop_detail++;
                $loop++;
                if($loop==$num_rows){
                    $table .= "</tbody>";
                    $table .= "</table>";
                }            
            }
            $script .= "<script>    
            jQuery(document).ready( function ($) {
            ";
            $render_validation = "
                return '<a id=\"btn_'+data.fil_idents+'\" class=\"btn btn-primary btn-sm\"><i id=\"ico_'+data.fil_idents+'\" class=\"fas fa-paperclip\" style=\"color:#fff\"/></a>'             
            ";

            $script .= " var oDT_" . $keyID . " = $('#".$keyID."').DataTable(
                {  'autoWidth':false, 'paging': false,'ordering': false, 'info':false, 'searching':false, 
                    columns: [
                        {   data: 'idk_nourut',width: '50px'},
                        {   data: 'fil_idents',width: '100px', visible:false},
                        {   data: 'fil_filename',width: '50%'},
                        {   data: 'fil_usrnam',width: '100px'},
                        {   data: 'fil_usrdat',width: '30%'},
                        {   data: null, className: 'dt-center editor-view', 
                            'render': function (data, type, row, meta){
                                " . $render_validation . "
                            },
                            orderable: false, 
                            width: '20px'
                        },
                    ]
                });
                $('#".$keyID."').show();
                $('#".$keyID."').on('click', 'td.editor-view', function (e) {
                    e.preventDefault();
                    var data_row = oDT_".$keyID.".row( $(this).parents('tr') ).data(); 
                    let row = oDT_".$keyID.".row('#row-' + data_row.fil_idents);
                    href = '".base_url()."/assets/kuesioner/'+data_row.fil_filename;
                    window.open(href, target='_blank');
                    // jvViewBerkas(data_row, this)
                } );
            });
            ";
            $script .= "
            function jvViewBerkas(data_row){
                var fil_filename = data_row.fil_filename;
            }
            </script>
            ";            
        }
        $content = $table;
        $content .= $script;
        if($type!="view"){
            $path = "kuesioner";
            $dropzone = array(
                "path"=>$path,
                "autoupload"=>false,
                "url"=>base_url("/upload/multipleberkas/".$path),
                "maxFilesize"=>30,
                "maxFiles"=>20
            );
    
            $detail[] = array('group'=>1, 'urutan'=>15, 'type'=> 'fil', 'maxlength'=>'100', 'label'=> 'File', "icon"=>true, "location"=>"/assets/kuesioner/", 'namanya'=> 'filBerkas', 'size'=> '400', 'dropzone'=>$dropzone);
            $inputfile = generateinputfile($detail[0]);
            $content .= '
                <div class="form-group">
                    <label>Berkas</label>
                    '.$inputfile.'
                </div>
            ';
    
            $buttonatas[] = array("iconact"=>"fas fa-upload", "theme"=>"primary","href"=>"javascript:jvSaveUpload()", "textact"=>"Unggah");
        }
        $portlet = array("content"=>$content,"title"=>$aso_process_area_desc, "icon"=>"fas fa-upload");
        if(isset($buttonatas)){
            $portlet = array_merge($portlet, array("listaction"=>$buttonatas));
        }
        $content = createportlet($portlet);
        echo $content;

    }
    function glossary(){
        $gridname = "jqxIstilah";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Glossary/list/m_master';
        $urutan = 0;
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'glb_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"glb_istilah", "aw"=>"20%", "label"=>"Istilah","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"glb_deskripsi", "aw"=>"50%", "label"=>"Istilah","adtype"=>"text");

        $content = gGrid(array('url'=>$url, 
            'grid'=>'datatables',
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'350px',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            'sumber'=>'server',
            'modul'=>'master/Glossary',
            'post'=>false,
        ));
        
        echo $content;

    }
    function xuploadwindow(){
        $type = $this->input->post("type");
        $aso_idents = $this->input->post('aso_idents');
		$tny_level = $this->input->post('tny_level');
		$aso_process_area = $this->input->post('aso_process_area');
        // getAsesmenfile_list($aso_idents, $tny_level, $aso_process_area)

        $readonly = false;
        $jwb_file = null;
        $path = "kuesioner";
        $this->load->helper('jqxgrid');
        $url ='/proses/nosj/getAsesmenfile_list/'.$aso_idents . "/" . $tny_level . "/" . $aso_process_area;
        $gridname = "BerkasAsesmen";
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'fil_idents','aw'=>'150','label'=>"ID Asesmen", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'fil_filename','aw'=>'150','label'=>"Berkas");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"fil_usrnam", "aw"=>80, "label"=>"Pengguna","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"fil_usrdat", "aw"=>120, "label"=>"Tanggal","adtype"=>"text");
        $buttonrow = array("view"=>array("icon"=>"paperclip", "function"=>"jvView(data_row)", "idents"=>"aso_idents", 'alt'=>'Lihat Berkas'));

        $content = gGrid(array('url'=>$url, 
            'grid'=>'datatables',
            // 'loadjs'=>false,
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'200px',
            'col'=>$col,
            'headerfontsize'=>13,
            'fontsize'=>15,
            'inline_buttonrow'=>$buttonrow,
            'inline_button_pos'=>'left',
            'surrounded'=>true,
            'sumber'=>'server',
            'post'=>false,
            'searchable'=>false
        ));
        $content .= "
        <script>
        function jvView(data_row){
            aso_idents = data_row['aso_idents'];
            aso_operator = data_row['aso_operator'];
            tny_level = data_row['tny_level'];
            aso_kelompok_indikator = data_row['aso_kelompok_indikator'];
            aso_kelompok_indikator_desc = data_row['aso_kelompok_indikator_desc'];
            $('#grdIDENTS').val(aso_idents);
            $('#aso_type').val('view');
            $('#tny_level').val(tny_level);
            $('#aso_kelompok_indikator').val(aso_kelompok_indikator);
            $('#aso_operator').val(aso_operator);
            $('#aso_kelompok_indikator_desc').val(aso_kelompok_indikator_desc);
            document.frmGrid.submit();
        }
        </script>
        ";
        
        $dropzone = array(
            "path"=>$path,
            "autoupload"=>false,
            "url"=>base_url("/upload/multipleberkas/".$path),
            "maxFilesize"=>30,
            "maxFiles"=>20
        );

        $detail[] = array('group'=>1, 'urutan'=>15, 'type'=> ($readonly==true ? 'viwfil' : 'fil'), 'maxlength'=>'100', 'label'=> 'File', "icon"=>true, "location"=>"/assets/kuesioner/", "value"=>$jwb_file, 'namanya'=> 'fil_filename', 'size'=> '400', 'dropzone'=>$dropzone);
        if($readonly==true && $jwb_file==null){
            $inputfile = "-";
        }else{
            $inputfile = generateinputfile($detail[0]);
        }
        $content .= '
            <div class="form-group">
                <label>Berkas</label>
                '.$inputfile.'
            </div>
        ';
        $buttonatas[] = array("iconact"=>"fas fa-upload", "theme"=>"primary","href"=>"javascript:jvSaveUpload()", "textact"=>"Unggah");
        $portlet = array("content"=>$content,"title"=>"Unggah Berkas", "icon"=>"fas fa-upload");
        if(isset($buttonatas)){
            $portlet = array_merge($portlet, array("listaction"=>$buttonatas));
        }
        $content = createportlet($portlet);
        echo $content;

    }
    function saveupload(){
        // $this->common->debug_post();
        $aso_idents = $this->input->post("aso_idents");
        $tny_level = $this->input->post("tny_level");
        $aso_process_area = $this->input->post("aso_process_area");
        $file = $this->input->post("file");

        $input["fil_asoidents"] = $aso_idents;
        $input["fil_tnylevel"] = $tny_level;
        $input["fil_process_area"] = $aso_process_area;
        $input["fil_usrnam"] = $this->username;

        $arrFile = json_decode($file);
        foreach($arrFile as $keyFile){
            $input["fil_filename"] = $keyFile;
            $this->crud->useTable($this->table_file);
            $this->crud->save($input);
        }
    }
    function approve(){
        // $this->common->debug_post();
        $jwb_idents = $this->input->post('jwb_idents');
        $jwb_status = $this->input->post('jwb_status');
        $jwb_usrnam = $this->input->post('jwb_usrnam');
        $jwb_alasan = $this->input->post('jwb_alasan');
        // [jwb_idents] => 76
        // [jwb_status] => 2
        // [jwb_usrnam] => pic_data_01
        // [jwb_alasan] => asdfasdf
        $input["jwb_status"] = $jwb_status;
        $input["jwb_appnam"] = $this->username;
        $input["jwb_appdat"] = $this->datesave;

        if($jwb_status=="1"){
            $text = "Persetujuan";
        }else{
            $text = "Penolakan";
        }
        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$jwb_idents,
            "log_action"=>array("jwb_idents"=>$jwb_idents, "action"=> $text . " Asesmen")
        );
        $this->common->logmodul(false, $arrModul);  
        $this->crud->useTable($this->table);
        if(!$this->crud->save($input, array("jwb_idents"=>$jwb_idents))){
            $return["message"] = "Kuesioner gagal disimpan!";
        }else{
            if($jwb_status==2){
                $title = "Jawaban Kuesioner Ditolak!";
                $body = "Mohon maaf, jawaban kuesioner anda ditolak, dengan catatan : " . $jwb_alasan;
                $this->common->notifyUser(1, $jwb_usrnam, $this->username, $title,$body, $jwb_idents);
            }
            if($jwb_alasan!=null){
                $this->crud->useTable("t_asm_asesmen_history_approval");

                $history["his_jwbidents"] = $jwb_idents;
                $history["his_jwbdeskripsi"] = $jwb_alasan;
                $history["his_jwbstatus"] = $jwb_status;
                $history["his_usrnam"] = $this->username;
                $this->crud->save($history);
            }

            $return["idents"] = $jwb_idents;
            $return["message"] = $text . " Kuesioner berhasil disimpan!";
        }
        echo json_encode($return);
    }
    function save(){
        $jwb_idents = $this->input->post('jwb_idents');
        $jwb_asoidents = $this->input->post('jwb_asoidents');
        $jwb_tnyidents = $this->input->post('jwb_tnyidents');
		$jwb_deskripsi = $this->input->post('jwb_deskripsi');
		$jwb_jawab = $this->input->post('jwb_jawab');
		$jwb_status = $this->input->post('jwb_status');
		$arr_jwb_link = $this->input->post('jwb_link');
        $jwb_file = $this->input->post('jwb_file');
        $hidTRNSKS = $this->input->post('hidTRNSKS');
        $jwb_link = null;
        $rc = false;
        if(is_array($arr_jwb_link)){
            foreach($arr_jwb_link as $key){
                if($rc) $jwb_link .= ";";
                $jwb_link .= $key;
                $rc = true;
            }
        }

        $input["jwb_asoidents"] = $jwb_asoidents;
        $input["jwb_tnyidents"] = $jwb_tnyidents;
        $input["jwb_deskripsi"] = $jwb_deskripsi;
        $input["jwb_status"] = $jwb_status;
        $input["jwb_jawab"] = $jwb_jawab;
        $input["jwb_link"] = $jwb_link;
        $input["jwb_file"] = $jwb_file;

        switch($hidTRNSKS){
            case "add":
                $input["jwb_usrnam"] = $this->username;
                break;
            case "edit":
                $input["jwb_updnam"] = $this->username;
                $input["jwb_upddat"] = $this->datesave;
                break;
            case "approve":
                $input["jwb_appnam"] = $this->username;
                $input["jwb_appdat"] = $this->datesave;
                break;
        }
        // debug_array($input);

        $this->common->logmodul(true, 
            array(
                "from"=>"Input Data Kuesioner", 
                "table_name"=>$this->table, 
                "POST"=>$input, 
                "username"=>$this->username, 
                "pk"=>array("jwb_idents"=>$jwb_idents)
            )
        );
        $this->crud->useTable($this->table);
        $pk = array("jwb_asoidents"=>$jwb_asoidents, "jwb_tnyidents"=>$jwb_tnyidents);
        if(!$this->crud->save($input, $pk)){
            $return["message"] = "Kuesioner gagal disimpan!";
        }else{
            if($jwb_idents=="" || $jwb_idents=="0"){
                $idents = $this->crud->__insertID;
            }else{
                $idents = $jwb_idents;
            }
            
            $this->crud->useTable("t_asm_asesmen_history_approval");

            $history["his_jwbidents"] = $idents;
            $history["his_jwbjawab"] = $jwb_jawab;
            $history["his_jwbdeskripsi"] = $jwb_deskripsi;
            $history["his_jwblink"] = $jwb_link;
            $history["his_jwbfile"] = $jwb_file;
            $history["his_jwbstatus"] = $jwb_status;
            $history["his_status"] = 0;
            $history["his_usrnam"] = $this->username;

            $this->crud->save($history);

            $return["idents"] = $idents;
            $return["message"] = "Kuesioner berhasil disimpan!";
        }
        echo json_encode($return);
    }
    function kirim(){
		$aso_idents = $this->input->post('grdIDENTS');
		$aso_kelompok_indikator = $this->input->post('aso_kelompok_indikator');
		$aso_operator = $this->input->post('aso_operator');
		$aso_type = $this->input->post('aso_type');
		$aso_kelompok_indikator_desc = $this->input->post('aso_kelompok_indikator_desc');

        $url = "/proses/kuesioner";
        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$this->table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_fkidents"=>$aso_idents,
            "log_action"=>array("aso_idents"=>$aso_idents, "action"=> "Kuesioner dikirim!")
        );
        $this->common->logmodul(false, $arrModul);

        $rowUser = $this->m_master->getUsers_edit($aso_operator);
        if($rowUser!=""){
            $user_name = $rowUser->USR_LOGINS;
        }
        $title = "Jawaban Anda Sudah dikirim";
        $body = "Jawaban anda untuk Kategori " . $aso_kelompok_indikator_desc . "sudah dikirim!";

        $master["aso_status"] = 1;
        $this->crud->useTable("t_asm_asesmen_operator");
        $this->crud->save($master, array("aso_idents"=>$aso_idents));

        $rslKategori = $this->m_asesmen->getKategori_detail($aso_idents, $aso_kelompok_indikator, $aso_operator);

        $total_rows = $rslKategori->num_rows();
        $loop = 1;
        foreach($rslKategori->result() as $key=>$value){
            $jwb_idents = $value->jwb_idents;
            $input["jwb_status_kirim"] = 1;
            $this->crud->useTable($this->table);
            $this->crud->save($input, array("jwb_idents"=>$jwb_idents));
            if($loop==$total_rows){
                $this->common->notifyUser(1, $user_name, $this->username, $title, $body);
                $this->common->message_save('save_sukses',null, $url);
            }
            $loop++;
        }
        // debug_array($rslKategori->result());

    }
    function riwayat(){
        $jwb_idents = $this->input->post("jwb_idents");
        $rsl = $this->m_asesmen->getHistory($jwb_idents);

        if($rsl->num_rows()>0){
            $content = '<div class="messages">';
            $loop = 0;
            $arrJawab = array("1"=>"Ya", "2"=>"Tidak", "3"=>"Tidak Menjawab");
            // debug_array($rsl->result());
            foreach($rsl->result() as $key=>$value){
                $his_jwbidents = $value->his_jwbidents;
                $his_ibxidents = $value->his_ibxidents;
                $his_jwbjawab = $value->his_jwbjawab;
                $his_jwbdeskripsi = $value->his_jwbdeskripsi;
                $his_jwblink = $value->his_jwblink;
                $his_jwbfile = $value->his_jwbfile;
                $his_jwbstatus = $value->his_jwbstatus;
                $his_jwbstatus = $value->his_jwbstatus;
                $his_usrnam = $value->his_usrnam;
                $his_usrdat = $value->his_usrdat;
                
                if($his_jwbjawab==""){
                    $content .= $this->comment(2, $his_usrnam, $his_usrdat, $his_jwbdeskripsi);
                }else{

                        $text = "Jawaban:<b>" . $arrJawab[$his_jwbjawab] ."</b>";
                        if($his_jwbdeskripsi!=""){
                            if(strlen(trim($his_jwbdeskripsi))>0){
                                $text .= "<br>". $his_jwbdeskripsi;
                            }
                        }
                        if($his_jwblink!=""){
                            $text .= "<br>Link: " . $his_jwblink;
                        }
                        if($his_jwbfile!=""){
                            $text .= "<br>Berkas: " . $his_jwbfile;
                        }
                    $content .= $this->comment(1, $his_usrnam, $his_usrdat, $text);
                }
                $loop++;
            }
            $content .='</div>';
        }else{
            $content = 0;
        }
        echo $content;
    }
    function comment($type, $user, $time, $message){
        if($type==1){
            $commentnya = '
            <div class="d-flex flex-column mb-5 align-items-start">
                <div class="d-flex align-items-center">
                    <div>
                        <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">'.$user.'</a>
                        <span class="text-muted font-size-sm">'.$time.'</span>
                    </div>
                </div>
                <div class="mt-2 rounded p-5 bg-light-success text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">'.$message.'</div>
            </div>
            ';
        }else{
            $commentnya = '
            <div class="d-flex flex-column mb-5 align-items-end">
                <div class="d-flex align-items-center">
                    <div>
                        <span class="text-muted font-size-sm">'.$time.'</span>
                        <a href="#" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">'.$user.'</a>
                    </div>
                </div>
                <div class="mt-2 rounded p-5 bg-light-primary text-dark-50 font-weight-bold font-size-lg text-right max-w-400px">'.$message.'</div>
            </div>            
            ';
        }
        return $commentnya;

    }
}
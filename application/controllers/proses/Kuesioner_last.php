<?php
//TODO:TESTING 123
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner extends MY_Controller {
    var $arrparent = [];
    function __construct(){
        parent::__construct();
    	$this->load->helper('ginput');
    	$this->load->model(array('m_asesmen'));
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
    }	
	public function index(){
        // $this->common->debug_array($this->session->userdata());
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> "Daftar Kuesioner"),
        );
        $bc = generateBreadcrumb($arrbread);
        if($this->usr_level==5){
            $content = $this->listKuesionerOperator();
        }else{
            $content = $this->listKuesionerView();
        }
        $this->_render('pages/home', $content,'admin',$bc);  	 
	}
    function listKuesionerOperator(){
        $gridname = "jqxPenugasan";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Asesmenpenugasan/list/m_asesmen/'.$this->usr_idents;
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'aso_asmidents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        // $col[] = array('lsturut'=>$urutan++, "namanya"=>"btn_go", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_name", "aw"=>120, "label"=>"Petugas","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_kelompok_Kategori_desc", "aw"=>420, "label"=>"Kelompok Kategori","adtype"=>"text");        
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_namey", "aw"=>120, "label"=>"Progress","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_namex", "aw"=>120, "label"=>"Status","adtype"=>"text");

        $selrow = "
            var selectedrowindex = $(\"#" . $gridname ."\").jqxGrid('getselectedrowindex');
            if(selectedrowindex == -1){
                swal('Pilih Data!');
                return;
            }
            var id = $(\"#" . $gridname . "\").jqxGrid('getrowid', selectedrowindex);
            var aso_idents = $(\"#" . $gridname . "\").jqxGrid('getcellvalue', selectedrowindex,'aso_idents');
        ";
        
        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);
        $oADD = strpos("N".$otorisasi,"E");

        $buttonrow = array("view"=>array("icon"=>"edit", "function"=>"jvAnswer(data_row)", "idents"=>"aso_idents", 'iconColor'=>'#0275d8'));

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
            'sumber'=>'server',
            'closeform'=>false,
            'modul'=>'proses/kuesioner/pertanyaan',
        ));
        //====== end of grid
        $content .= form_input(array('name' => "aso_kelompok_Kategori_desc",'id'=> "aso_kelompok_Kategori_desc", 'type'=>'hidden'));
        $content .= form_close();
        $content .= generateWindowjqx(array('window'=>'Kategori','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        $content .= "
        <script>
        function jvAnswer(data_row){
            aso_idents = data_row['aso_idents'];
            aso_kelompok_Kategori_desc = data_row['aso_kelompok_Kategori_desc'];
            $('#grdIDENTS').val(aso_idents);
            $('#aso_kelompok_Kategori_desc').val(aso_kelompok_Kategori_desc);
            document.frmGrid.submit();
        }
        </script>
        ";
        return $content;
    }
    function listKuesionerView(){
        $gridname = "jqxPenugasan";
        $this->load->helper('jqxgrid');
        $url ='/nosj/getNosj_list/Asesmenpenugasan/list/m_asesmen';
        
        $urutan = 0;
        $col = array();
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lko_idents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, 'namanya'=>'lko_lokidents','aw'=>'150','label'=>"Identitas", 'ah'=>true);
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"asm_tahun", "aw"=>80, "label"=>"Tahun","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_name", "aw"=>120, "label"=>"Petugas","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_kelompok_indikator_desc", "aw"=>420, "label"=>"Kelompok Kategori","adtype"=>"text");        
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_namey", "aw"=>120, "label"=>"Progress","adtype"=>"text");
        $col[] = array('lsturut'=>$urutan++, "namanya"=>"aso_operator_namex", "aw"=>120, "label"=>"Status","adtype"=>"text");

        $urlsec = uri_string();
		$otorisasi = $this->common->otorisasi($urlsec);
        $oADD = strpos("N".$otorisasi,"E");
        $buttonother = null;
        if($oADD>0){
            $buttonother = array(
                "Tambah Penugasan"=>array('Print1','fa-plus','jvAddOperator()','warning','80')
            );
        }
        $content = gGrid(array('url'=>$url, 
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'100%',
            'col'=>$col,
            'headerfontsize'=>11,
            'fontsize'=>10,            
            // 'buttonother'=> $buttonother,
            'sumber'=>'server',
            'modul'=>'proses/asesmen',
            'groupcol'=>"aso_operator_name",
            "groupable"=>true,
            "expandgroup"=>true
        ));
        //====== end of grid
        $content .= generateWindowjqx(array('window'=>'Kategori','title'=>'Progress','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));
        $content .= "
        <script>

        function jvAddOperator(){
            title = 'Tambah';
            var param = {};
            param['type'] = 'add';
            param['title'] = title;

            $('#jqwKategori').jqxWindow('open');

            $.post('/proses/penugasan/edit', param,function(data){
                var lebar = $(window).width() * 0.8;
                $('#jqwKategori').jqxWindow({isModal: true, autoOpen: false,width:lebar, height:'520px',position:'middle', resizable:false,title: title + ' Operator', zIndex:'99999'});
                $('#jqwKategori').jqxWindow('setContent', data);
            });
        }        
        </script>
        ";
        return $content;
    }
    function pertanyaan(){
        $aso_idents = $this->input->post("grdIDENTS");
        $aso_kelompok_Kategori_desc = $this->input->post("aso_kelompok_Kategori_desc");
        // debug_array($index);
        $udi = null;
        $script = '
        <script type="text/javascript" src=' . base_url(PLUGINS."DataTables/datatables.min.js") .'></script>
        <link rel="stylesheet" href=' . base_url(PLUGINS."DataTables/datatables.min.css"). ' type="text/css">
        <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/fontawesome.min.css"). ' type="text/css">
        <link rel="stylesheet" href=' . base_url(PLUGINS."font-awesome/css/solid.css"). ' type="text/css">
        ';
        $rslKategori = $this->m_asesmen->getKategori_detail($aso_idents);
        // $this->common->debug_sql(1);
        // debug_array($rslKategori->result());
        if($rslKategori->num_rows()>0){
            // $tablehead = "<thead><tr><th style='width:80px'>No</th><th>Pertanyaan</th><th>Action</th><th>ID Jawaban</th><th>Jawaban</th><th>Deskrisi</th><th>Link</th><th>File</th></tr></thead>";
            $tablehead = "<thead>
                    <tr>
                        <th style='width:80px'>No</th>
                        <th>Pertanyaan</th>
                        <th>Action</th>
                        <th>ID Pertanyaan</th>
                        <th>Kategori</th>
                        <th>ID Jawaban</th>
                        <th>Jawaban</th>
                        <th>Deskripsi</th>
                        <th>Link</th>
                        <th>File</th>
                    </tr>
            </thead>";
            $loop = 0;
            $loop_detail = 0;
            $idk_idents_temp = null;
            $idk_nama_temp = null;
            $num_rows = $rslKategori->num_rows();
            foreach($rslKategori->result() as $key=>$value){
                $idk_idents = $value->idk_idents;
                $tny_idents = $value->tny_idents;
                $idk_nama = $value->idk_nama;
                $tny_pertanyaan = $value->tny_pertanyaan;
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
                    $table = '<table id='.$idtable.' class="display" style="width:100%">';
                    // $table .= "<thead><tr><th style='width:80px'>No</th><th>Pertanyaan</th><th>Jawaban</th></tr></thead>";
                    $table .= $tablehead;
                    $table .= "<tbody>";
                    $loop_detail =0;
                }
                $urutan = $loop_detail+1;
                $table .= "<tr>";
                $table .= " <td style='width:80px'>" . $urutan . "</td>";
                $table .= " <td id=id_".$tny_idents."_1>" . $tny_pertanyaan . "</td>";
                // $table .= " <td><button type='button' class='btn btn-danger btn-sm'>Danger</button></td>";
                // $table .= " <td><a id='btn_".$tny_idents."' href='javascript:jvAnswer(\"".$idtable."\",".$tny_idents.", \"".$idk_nama."\",\"".$tny_pertanyaan."\")' class='btn btn-primary font-weight-bold btn-pill btn-sm'>&nbsp;&nbsp;<i id='ico_".$tny_idents."' class='fas fa-reply' style='font-size:12px'></i></a></td>";
                $table .= " <td></td>";
                $table .= " <td >" . $tny_idents . "</td>";
                $table .= " <td >" . $idk_nama . "</td>";
                $table .= " <td id='id_".$tny_idents."_idents'></td>";
                $table .= " <td id='id_".$tny_idents."_jawab'></td>";
                $table .= " <td id='id_".$tny_idents."_deskripsi'></td>";
                $table .= " <td id='id_".$tny_idents."_link'></td>";
                $table .= " <td id='id_".$tny_idents."_file'>5</td>";

                $table .= "</tr>";
    
                $idk_idents_temp = $idk_idents;
                $idk_nama_temp = $idk_nama;
                $loop_detail++;
                $loop++;
                if($loop==$num_rows){
                    $table .= "</tbody>";
                    $table .= "</table>";
                    // debug_array($arrTabs);
                    $arrTabs[$idk_nama] = array("data"=>$table);
                    // debug_array($arrTabs);
                }            
            }
            // debug_array($arrTabs);
            // jQuery(function ($) { $(document).ready(function () { $('#NavigationMenu').smartmenus(); }); });
            $script .= "<script>
    
            jQuery(document).ready( function ($) {
                // $.noConflict();

            ";
            
            // , { targets: 3, visible:false }, { targets: 4, visible:false }, { targets: 5, visible:false }, { targets: 6, visible:false }, { targets: 7, visible:false }
            foreach ($arrIdTable as $keyID){
                $script .= " var oDT_" . $keyID . " = $('#".$keyID."').DataTable(
                    {   'autoWidth':false, 'paging': false,'ordering': false, 'info':false, 'searching':false, 
                        columns: [
                            {   data: 'idk_nourut',width: '50px'},
                            {   data: 'tny_pertanyaan',width: '90%'},
                            {   data: null, className: 'dt-center editor-view', 
                                'render': function (data, type, row, meta){
                                    return '<a id=\"btn_'+data.tny_idents+'\" class=\"btn btn-primary btn-sm\"><i id=\"ico_'+data.tny_idents+'\" class=\"fas fa-reply\" style=\"color:#fff\"/></a>'
                                },
                                orderable: false, 
                                width: '20px'
                            },
                            {   data: 'tny_idents',width: '50px', visible:true},
                            {   data: 'idk_nama',width: '50px', visible:false},
                            {   data: 'jwb_idents',width: '50px', visible:true},
                            {   data: 'jwb_jawab',width: '50px', visible:true},
                            {   data: 'jwb_deskripsi',width: '50px', visible:true},
                            {   data: 'jwb_link',width: '50px', visible:true},
                            {   data: 'jwb_file',width: '50px', visible:true},
                        ]
                    });
                    $('#".$keyID."').on('click', 'td.editor-view', function (e) {
                        e.preventDefault();
                        var data_row = oDT_".$keyID.".row( $(this).parents('tr') ).data(); 
                        let row = oDT_".$keyID.".row('#row-' + data_row.tny_idents);
                        console.log(data_row);
                        jvAnswer(data_row, this)
                    } );                    
                ";
                // $script .= 'let table = new DataTable("#'.$keyID.'")';
    
            }
            // $('#tblPertanyaan_20 tbody').on('click', 'tr td', function () {
            //     // alert( table.cell( this ));
            //     var data_row = oDT_tblPertanyaan_20.row(this).data();
            //     // var data_row = oDT_tblPertanyaan_20.row( $(this).parents('tr') ).data();
            //     console.log(data_row.index);
            //     // console.log(oDT_tblPertanyaan_20.cell(this));
            // } );            

            $script .= "
            clear = function(){
                $('#IdMainQuestioner').val('');
                $('#indicator').val('');
                $('#question').val('');
                $('#description').val('');
                var ele = document.getElementsByName('jawaban');
                for(var i=0;i<ele.length;i++){
                    ele[2].checked = true;
                }
                $('#link').val('');
                $('#support_file').val('');
                $('.custom-file-label').html('Pilih file');
                document.getElementById('support_file').removeAttribute('href');
                document.getElementById('support_file').innerHTML = '';
            }
            });
            function jvAnswer(data_row, this_row){
                $('#tny_idents').val(data_row.tny_idents);
                $('#idk_nama').val(data_row.idk_nama);
                $('#tny_pertanyaan').val(data_row.tny_pertanyaan);
                window.$('#modalQuestion').modal('show');
            }

            </script>
            ";
            
            $arrTabs = array(
                "id"=>"Pertanyaan",
                "bentuk"=>"accordion",
                "arrTabs" => $arrTabs
              );
            $content = generateTabjqx($arrTabs);
            $content .=form_input(array('name' => "aso_idents",'id'=> "aso_idents", 'type'=>'hidden', 'value'=>$aso_idents));
            $content .= form_close();
            $content .= $script;
        }else{
            $content = "<blockquote>Data Pertanyaan tidak Ada! Mohon hubungi Administrator Aplikasi</blockquote>";
        }

        $content = createportlet(array("content"=>$content,"title"=>"Kelompok Kategori " . $aso_kelompok_Kategori_desc, "icon"=>"fas fa-map"));

        $textarea_arr[] = array("group"=>1, "urutan"=>1, "namanya"=>"jwb_deskripsi", "label"=>"&nbsp;","type"=>"txa", 'ckeditor'=>array('full'=>false, 'toolbar'=>'sosimple','height'=>'150px'));
        $textarea = generateinput(array('arrTable'=>$textarea_arr,'elementonly'=>true,'nojqx'=>true));

        $dropzone = array(
            "path"=>"/assets/incidents/",
            "autoupload"=>false,
            "url"=>base_url("/incident/upload"),
            "maxFilesize"=>30,
            "maxFiles"=>10,
        );

        $detail[] = array('group'=>1, 'urutan'=>15, 'type'=> isset($status) ? 'viwfil' : 'fil', 'maxlength'=>'100', 'label'=> 'File', 'namanya'=> 'filBerkas', 'size'=> '400', 'dropzone'=>$dropzone);
        $inputfile = generateinputfile($detail[0]);

        $content .= '
        <script>
            function jvAnswerx(data_row){
                // var thisRow = oDT_tblPertanyaan_20.row({ selected: true }).data()[2];
                console.log(data_row);
            }
            // function jvAnswer(id_table, tny_idents, idk_nama, tny_pertanyaan){
            function jvSave(){
                var aso_idents = $("#aso_idents").val();
                var tny_idents = $("#tny_idents").val();
                var desc = CKEDITOR.instances.jwb_deskripsi.getData();
                var rdoYa = $("input[name=\'jwb_jawab\']:checked").val();
                var link = $("#jwb_link").val();

                var param = {};
                param["jwb_asoidents"] = aso_idents ;
                param["jwb_tnyidents"] = tny_idents ;
                param["jwb_deskripsi"] = desc;
                param["jwb_jawab"] = rdoYa;
                param["jwb_link"] = link;

                $.post("/proses/kuesioner/save", param,function(data){
                    var result = $.parseJSON(data);
                    $("#id_" + tny_idents + "_idents").html(result.idents);
                    $("#id_" + tny_idents + "_jawab").html(rdoYa);
                    $("#id_" + tny_idents + "_deskripsi").html(desc);
                    $("#id_" + tny_idents + "_link").html(link);
                    $("#btn_" + tny_idents).removeClass("btn-primary").addClass("btn-success");
                    $("#ico_" + tny_idents).removeClass("fas fa-reply").addClass("fas fa-check");
                    window.$("#modalQuestion").modal("hide");
                });
    

            }
        </script>
        
        <style>
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
                        <h5 class="modal-title" id="headermodal"><i class="fas fa-question-circle" style="color:red"></i> Kuesioner Kelompok Kategori <b>'.$aso_kelompok_Kategori_desc.'</b></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kategori</label>
                            <input type="text" class="form-control" name=idk_nama id=idk_nama readonly>
                            <input type="hidden" class="form-control" name=tny_idents id=tny_idents readonly>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <input type="text" class="form-control" id=tny_pertanyaan name=tny_pertanyaan readonly>
                        </div>
                        <div class="form-group">
                            <label>Jawaban  <span class="text-danger">*</span></label>
                            <div class="radio-list">
                                <label class="radio">
                                    <input type="radio" name="jwb_jawab" value=1>
                                    <span></span>
                                    Ya
                                </label>
                                <label class="radio radio-danger">
                                    <input type="radio" name="jwb_jawab" value=2>
                                    <span></span>
                                    Tidak
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi  <span class="text-danger">*</span></label>
                            '.$textarea.'
                        </div>
                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" class="form-control" id=jwb_link name=jwb_link>
                        </div>
                        <div class="form-group">
                            <label>Berkas</label>
                            '.$inputfile.'
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="jvSave()">Save</button>
                    </div>                    
                </div>
            </div>
        </div>
        ';
        // $content .= generateWindowjqx(array('window'=>'Detail','title'=>'Periksa','height'=>'200', 'minWidth'=>100,'maxWidth'=>'1800px','overflow'=>'auto'));
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'/proses/kuesioner','text'=>"Daftar Kelompok Kategori"),
            array('link'=>'#','text'=>"Input Pertanyaan"),
        );          
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $content,'admin',$bc);        
        // return $content;        
    }
    function save(){
        // $this->common->get_post();
        $jwb_idents = $this->input->post('jwb_idents');
        $jwb_asoidents = $this->input->post('jwb_asoidents');
        $jwb_tnyidents = $this->input->post('jwb_tnyidents');
		$jwb_deskripsi = $this->input->post('jwb_deskripsi');
		$jwb_jawab = $this->input->post('jwb_jawab');
		$jwb_link = $this->input->post('jwb_link');
        $hidTRNSKS = $this->input->post('hidTRNSKS');

        $input["jwb_asoidents"] = $jwb_asoidents;
        $input["jwb_tnyidents"] = $jwb_tnyidents;
        $input["jwb_deskripsi"] = $jwb_deskripsi;
        $input["jwb_jawab"] = $jwb_jawab;
        $input["jwb_link"] = $jwb_link;

        if($hidTRNSKS=="add"){
        }else{
        }

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
        if(!$this->crud->save($input, array("jwb_idents"=>$jwb_idents))){
            $return["message"] = "Kuesioner gagal disimpan!";
        }else{
            $idents = $this->crud->__insertID;
            $return["idents"] = $idents;
            $return["message"] = "Kuesioner berhasil disimpan!";
        }
        echo json_encode($return);
    }
}
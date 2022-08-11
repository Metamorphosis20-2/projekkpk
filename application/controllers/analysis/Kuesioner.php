<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner extends MY_Controller {
    function __construct(){
        parent::__construct();
        $this->load->helper(array('ginput','chartjs','jqxgrid'));
        $this->load->model(array('m_master','m_grafik', 'm_asesmen'));
        $this->emp_employee_id = $this->lang->line("emp_employee_id");
        $this->emp_name = $this->lang->line("emp_name");
        $this->modul = $this->router->fetch_class();

        $this->sap_list = $this->lang->line("sap_list");
        $this->sap_title = $this->lang->line("sap_title");
        $this->sap_period = $this->lang->line("sap_period");
        $this->sap_notes = $this->lang->line("sap_notes");
        $this->sad_documenttype = $this->lang->line("sad_documenttype");
        $this->sad_documentnumber = $this->lang->line("sad_documentnumber");
        $this->sad_glshorttext = $this->lang->line("sad_glshorttext");
        $this->sad_account = $this->lang->line("sad_account");
        $this->sad_debitcreditind = $this->lang->line("sad_debitcreditind");
        $this->sad_postingkey = $this->lang->line("sad_postingkey");
        $this->sad_amountindoccurr = $this->lang->line("sad_amountindoccurr");
        $this->sad_documentcurrency = $this->lang->line("sad_documentcurrency");
        $this->sad_effexchangerate = $this->lang->line("sad_effexchangerate");
        $this->sad_amountinlocalcurrency = $this->lang->line("sad_amountinlocalcurrency");
        $this->sad_localcurrency = $this->lang->line("sad_localcurrency");
        $this->sad_costcenter = $this->lang->line("sad_costcenter");
        $this->sad_wbselement = $this->lang->line("sad_wbselement");
        $this->sad_businessarea = $this->lang->line("sad_businessarea");
        $this->sad_fund = $this->lang->line("sad_fund");
        $this->sad_grant = $this->lang->line("sad_grant");
        $this->sad_assignment = $this->lang->line("sad_assignment");
        $this->sad_text = $this->lang->line("sad_text");
        $this->sad_referencekey1 = $this->lang->line("sad_referencekey1");
        $this->sad_referencekey2 = $this->lang->line("sad_referencekey2");
        $this->sad_referencekey3 = $this->lang->line("sad_referencekey3");
        $this->sad_valuedate = $this->lang->line("sad_valuedate");
        $this->sad_documentdate = $this->lang->line("sad_documentdate");
        $this->sad_postingdate = $this->lang->line("sad_postingdate");
        $this->sad_fiscalyear = $this->lang->line("sad_fiscalyear");
        $this->sad_postingperiod = $this->lang->line("sad_postingperiod");
        $this->sad_reference = $this->lang->line("sad_reference");
        $this->sad_username = $this->lang->line("sad_username");
        $this->sad_offsettaccounttype = $this->lang->line("sad_offsettaccounttype");
        $this->sad_offsettingacctno = $this->lang->line("sad_offsettingacctno");
        $this->pengguna = $this->lang->line("usr_pengguna");
        $this->tanggal_buat = $this->lang->line("usr_tanggal_buat");
        $this->not_found = $this->lang->line("not_found");
        $this->upload_success = $this->lang->line("upload_success");
        $this->upload_failed = $this->lang->line("upload_failed");
        $this->amount = $this->lang->line("amount");

        $this->grn_description = $this->lang->line("grn_description");
        $this->grn_kind = $this->lang->line("grn_kind");
        $this->grn_code = $this->lang->line("grn_code");
        $this->grn_shortname = $this->lang->line("grn_shortname");
        $this->grn_datestart = $this->lang->line("grn_datestart");
        $this->grn_dateend = $this->lang->line("grn_dateend");
        $this->grn_sponsor = $this->lang->line("grn_sponsor");
        $this->grn_sponsor_name = $this->lang->line("grn_sponsor_name");
        
    }   
    public function index($parameter="kuesioner"){
        $txt = null;
        switch($parameter){
            case "asesmen":
                $txt = "Asesmen";
                break;
                
        }
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> $txt),
        );
        $view = $this->{$parameter}();
        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $view,'admin',$bc);
    }    
    function hasilasesmen(){
        
        $urutan = 0;
        $formname = "frmExpense";
        $optOption = array(1=>"Detail", 2=>"Summary");
        $optrefkey = array("1"=>"Yes", "2"=>"No");
        $script = null;
        $field = array("asm_idents", "asm_tahun");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
$optAsesmen = $this->crud->getGeneral_combo($arrayOpt);
        $field = array("unt_idents", "unt_unitkerja");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_unitkerja",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );

        $optUnitkerja = $this->crud->getGeneral_combo($arrayOpt);
        
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
        
        $field = array("lvl_idents", "lvl_nama");
        $this->db->where("lvl_parent = 0");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_level",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]"),
            "empty"=>true
            // "empty"=>FALSE
        );
        
        $optLevel = $this->crud->getGeneral_combo($arrayOpt);

        $optOption = array(1=>"Kategori", 2=>"Process Area");

        $arrField = array(
            "grf_asesmen"=>array("group"=>1, "label"=>"Tahun Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesmen tidak boleh kosong")),
            "grf_level"=>array("group"=>1, "label"=>"Level","type"=>"cmb", "size"=>"200px", "option"=>$optLevel, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Level tidak boleh kosong")),
            "grf_option"=>array("group"=>1, "label"=>"Jenis","type"=>"cmb", "size"=>"200px", "option"=>$optOption, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Jenis tidak boleh kosong")),
            "grf_unitkerja"=>array("group"=>1, "label"=>"Unit Kerja","type"=>"cmb","option"=>$optUnitkerja, "size"=>"400px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Unit Kerja tidak boleh kosong")),
        );
        $arrTable = $this->common->generateArray($arrField, null, null, false);

        $arrButton = array(
            array("text"=>"Graph", "events"=>"jvShow('graph')", "theme"=>"success", "image"=>"fas fa-chart-pie"),
            array("text"=>"Report", "events"=>"jvShow('report')", "theme"=>"danger", "image"=>"fas fa-table"),
        );
        $button = createButton($arrButton, false, false);
        $arrTable[] = array('group'=>1, 'urutan'=>99, "type"=>"udi", "namanya"=>"anyGrant", "label"=>"&nbsp;", "value"=>$button);

        $arrForm = array(
            'type'=>"edit",
            'arrTable'=>$arrTable,
            'status'=> isset($status) ? $status : "",
            'param' =>null,
            'width'=>'70%',
            'modul' => 'sppdb',
            'nameForm' => $formname,
            'formcommand' => '/hr/employee/save',
            'tabname'=> array(
                '1'=>'fas fa-question-circle^Asesmen'
            )
        );

        $content =  generateForm($arrForm);
        
        $content .= "
        <script>
       
        function jvShow(type){
            validator
            .validate()
            .then(function(status){
                if(status!='Invalid'){
                    var data_type = $('#grf_asesmen').select2('data');
                    grf_asesmen_desc = data_type[0].text;
        
                    var data_type = $('#grf_level').select2('data');
                    grf_level_desc = data_type[0].text;
        
                    var data_type = $('#grf_option').select2('data');
                    grf_option_desc = data_type[0].text;
        
                    var data_type = $('#grf_unitkerja').select2('data');
                    grf_unitkerja_desc = data_type[0].text;
                
                    title ='Grafik';
                    var optionnnya = grf_option_desc + '-' + $('#grf_level').val()
        
                     var param ={};
                    param['type'] = type;
                    param['grf_asesmen'] = $('#grf_asesmen').val();
                    param['grf_asesmen_desc'] = grf_asesmen_desc;
                    param['grf_option'] = $('#grf_option').val();
                    param['grf_option_desc'] = grf_option_desc;
                    param['grf_level'] = $('#grf_level').val();
                    param['grf_level_desc'] = grf_level_desc;
                    param['grf_unitkerja'] = $('#grf_unitkerja').val();
                    param['grf_unitkerja_desc'] = grf_unitkerja_desc;
                    
                    url = '/analysis/kuesioner/'+type+'Asesmen';
                    if(type=='graph'){
                        $('#jqwPopup').jqxWindow('open');
                        $.post(url,param,function(data){
                            windowWidth = $(window).width()-250; 
                            windowHeight = $(window).height()-100; 
                            
                            var winHeight = $(window).height();
                            var winWidth = $(window).width();
                
                            // windowWidth = $(window).width()-80; 
                            // windowHeight = $(window).height()-80; 
                            var posX = (winWidth/2) - (windowWidth/2) + $(window).scrollLeft() + 135;
                            var posY = (winHeight/2) - (windowHeight/2) + $(window).scrollTop();
                
                            $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: true, width:'80%', height:windowHeight, autoOpen: false,position: {x: posX, y: posY}, resizable:false,title: title });  
                            $('#jqwPopup').jqxWindow('setContent', data);
                        });
                    }else{
                        var winName='winReportAsesmen';
                        var winURL=url;
                        var winheight = $(window).height();
                        var winwidth = $(window).width();
                        var windowoption='resizable=yes,height='+winheight+',width='+winwidth+',location=0,menubar=0,scrollbars=1';
                        var form = document.createElement('form');
                        form.setAttribute('method', 'post');
                        form.setAttribute('action', winURL);
                        form.setAttribute('target', winName); 
                        for (var i in param) {
                          if (param.hasOwnProperty(i)) {
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = i;
                            input.value = param[i];
                            form.appendChild(input);
                          }
                        }
                        document.body.appendChild(form);
                        window.open('', winName,windowoption);
                        form.target = winName;
                        form.submit();
                        document.body.removeChild(form);
                    }
                            
                }
            })            
        }

        function jvCsv(){
            var data_type = $('#anaOption').select2('data');
            option_desc = data_type;

            var data_type = $('#anaOption').select2('data');
            option_desc = data_type;

            var data_type = $('#anaKind').select2('data');
            kind_desc = data_type[0].text;

            var data_type = $('#anaMonth1').select2('data');
            month1_desc = data_type[0].text;

            var data_type = $('#anaGrant').select2('data');
            grant_desc = data_type[0].text;

            var param ={};
            param['jenis'] = 'expense';
            param['anaOption'] = $('#anaOption').val();
            param['option_desc'] = option_desc;
            param['anaGrant'] = $('#anaGrant').val();
            param['anaYear'] = $('#anaYear').val();
            param['anaKind'] = $('#anaKind').val();
            param['kind_desc'] = kind_desc;
            param['anaMonth1'] = $('#anaMonth1').val();
            param['grant_desc'] = grant_desc;
            param['month1_desc'] = month1_desc;

            $('#imgPROSES').show();
            $('#windowProses').jqxWindow('open');

            var winName='csv';
            var winURL='/analysis/grant/csv';
            var form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', winURL);
            form.setAttribute('target', winName); 
            for (var i in param) {
                if (param.hasOwnProperty(i)) {
                  var input = document.createElement('input');
                  input.type = 'hidden';
                  input.name = i;
                  input.value = param[i];
                  form.appendChild(input);
                }
            }
            $('#windowProses').jqxWindow('close');
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }        
        </script>
        ";
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200px', 'widths'=>'1440px', 'maxWidth'=>'1440px', 'maxHeight'=>'1440px','overflow'=>'auto'));
        $content .= generateWindowjqx(array('window'=>'Detailgrafik','title'=>'Info Detail','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));

        return $content;
    }
    function graphKuesioner(){
        // $this->common->debug_post();
        $click = null;
        $type = $this->input->post('type');
        $grf_asesmen = $this->input->post('grf_asesmen');
        $grf_asesmen_desc = $this->input->post('grf_asesmen_desc');
        $grf_option = $this->input->post('grf_option');
        $grf_option_desc = $this->input->post('grf_option_desc');
        $grf_provinsi = $this->input->post('grf_provinsi');
        $grf_provinsi_desc = $this->input->post('grf_provinsi_desc');
        $grf_kabptn = $this->input->post('grf_kabptn');
        $grf_kabptn_desc = $this->input->post('grf_kabptn_desc');

        $title = "All Grant";
        
        switch($grf_option){
            case 1:
                if($grf_provinsi!="" && $grf_kabptn!=""){
                    $rslPerbulan = $this->m_grafik->grfKuesioner($grf_asesmen, $grf_provinsi, $grf_kabptn);
                    $grafiknya = array(
                        'id'    =>  'grfExpensepermonth',
                        'chart' =>  'column',
                        'title'=>"Kuesioner",
                        'warna'=>'#ffba00',
                        'resultset'=>$rslPerbulan,
                        'click'=>$click,
                        'rotation'=>90,
                        'limit'=>20,
                        'legend_display'=>'true',
                        'fields'=>array("descre"=>"idk_nama", "values"=>array("Pertanyaan", "Jawaban"))
                        // 'fields'=>array("descre"=>"idk_nama", "values"=>"total_pertanyaan")
                    );
                }else{
                    $rslPerbulan = $this->m_grafik->grfKuesionerYesno($grf_asesmen, $grf_provinsi, $grf_kabptn);
                    // $this->common->debug_sql(1);
                    $grafiknya = array(
                        'id'    =>  'grfExpensepermonth',
                        'chart' =>  'column',
                        'title'=>"Kuesioner",
                        'warna'=>'#ffba00',
                        'resultset'=>$rslPerbulan,
                        'click'=>$click,
                        'rotation'=>90,
                        'limit'=>20,
                        'legend_display'=>'true',
                        'fields'=>array("descre"=>"idk_nama", "values"=>array("jawaban_ya", "jawaban_tidak", "jawaban_tidakmenjawab"))
                        // 'fields'=>array("descre"=>"idk_nama", "values"=>"total_pertanyaan")
                    );
                }
                break;
        }

        $arrAction =array(
            "action"=> "View Graphic",
        );

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>'t_asm_asesmen_jawaban', 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"1", 
            "log_action"=>$arrAction,
            "log_fkidents"=>null
        );
        $this->common->logmodul(false, $arrModul);
        $grafik = display_chart($grafiknya);
        echo $grafik;
        die();
    }
    function graphPenilaian(){
        $click = null;
        $type = $this->input->post('type');
        $grf_asesmen = $this->input->post('grf_asesmen');
        $grf_asesmen_desc = $this->input->post('grf_asesmen_desc');
        $grf_level = $this->input->post('grf_level');
        $grf_level_desc = $this->input->post('grf_level_desc');
        $grf_option = $this->input->post('grf_option');
        $grf_option_desc = $this->input->post('grf_option_desc');
        $grf_unitkerja = $this->input->post('grf_unitkerja');
        $grf_unitkerja_desc = $this->input->post('grf_unitkerja_desc'); 

        $rslKelompok = $this->m_asesmen->getKelompokKategorii($grf_asesmen, $grf_unitkerja, $grf_option);
        //$this->common->debug_sql(1);
        $flotarea = array(
            'id'    =>  'grfAsesmen',
            'chart' =>  'spider',
            'width' =>  "100%",
            'labelling' => 'Score',
            'warna'=>'#5cb85c',
            'resultset'=>$rslKelompok,
            'fields'=>array("descre"=>"idk_nama", "values"=>"idk_progress"),
        );
        
        $grafik = createportlet(array("content"=>display_chart($flotarea),"title"=>"Grafik Asesmen ", "icon"=>"fas fa-chart-pie", "class"=>"portletGrafik"));
        echo $grafik;
        die();
    }
    function reportKuesioner(){
        // $this->common->debug_post();
        ini_set('memory_limit', '-1');
        $anaType = $this->input->post('type');
        $anaOption = $this->input->post('anaOption');
        $option_desc = $this->input->post('option_desc');
        $anaKind = $this->input->post("anaKind");
        $kind_desc = $this->input->post("kind_desc");
        $anaGrant = $this->input->post('anaGrant');
        $anaRefkey = $this->input->post('anaRefkey');
        $refkey_desc = $this->input->post("refkey_desc");
        $anaYear = $this->input->post('anaYear');
        $anaMonth1 = $this->input->post('anaMonth1');
        $anaMonth2 = $this->input->post('anaMonth2');
        $grant_desc = $this->input->post('grant_desc');
        $month1_desc = $this->input->post('month1_desc');
        $month2_desc = $this->input->post('month2_desc');
        $refkeydetail = $this->input->post('refkeydetail');
        $scriptdetail = null;
        $from = "graph";//($this->input->post("from")=="" ? "0" : $this->input->post("from"));
        $content = null;
        echo $content;
        die();
    }
    function csv(){
        // $this->common->debug_post();
        $jenis = $this->input->post('jenis');
        $from = $this->input->post('from');
        $type = $this->input->post('type');
        $anaOption = $this->input->post('anaOption');
        $option_desc = $this->input->post('option_desc');
        $anaGrant = $this->input->post('anaGrant');
        $anaYear = $this->input->post('anaYear');
        $anaKind = $this->input->post('anaKind');
        $kind_desc = $this->input->post('kind_desc');
        $anaMonth1 = $this->input->post('anaMonth1');
        $anaMonth2 = $this->input->post('anaMonth2');
        $anaRefkey = $this->input->post('anaRefkey');
        $grant_desc = $this->input->post('grant_desc');
        $month1_desc = $this->input->post('month1_desc');
        $refkeydetail = $this->input->post('refkeydetail');

        $namafile = null;
        switch($jenis){
            case "expense":
                $namafile = "expense";
                switch($anaOption){
                    case "1":
                        unset($col);
                        $arrField = array(
                            'sad_idents'=>"Identitas",
                            'sad_documenttype'=>"Type",
                            'sad_documentnumber'=>"Number",
                            'sad_glshorttext'=>$this->sad_glshorttext,
                            'sad_account'=>$this->sad_account,
                            'sad_debitcreditind'=>$this->sad_debitcreditind,
                            'sad_postingkey'=>$this->sad_postingkey,
                            'sad_amountindoccurr'=>"Amount",
                            'sad_documentcurrency'=>"Curr",
                            'sad_effexchangerate'=>"Exchange Rate",
                            'sad_amountinlocalcurrency'=>"Amount Local",
                            'sad_localcurrency'=>$this->sad_localcurrency,
                            'sad_costcenter'=>$this->sad_costcenter,
                            'sad_wbselement'=>$this->sad_wbselement,
                            'sad_businessarea'=>$this->sad_businessarea,
                            'sad_fund'=>$this->sad_fund,
                            'sad_grant'=>$this->sad_grant,
                            'sad_assignment'=>$this->sad_assignment,
                            'sad_text'=>$this->sad_text,
                            'sad_referencekey1'=>$this->sad_referencekey1,
                            'sad_referencekey2'=>$this->sad_referencekey2,
                            'sad_referencekey3'=>$this->sad_referencekey3,
                            'sad_valuedate'=>$this->sad_valuedate,
                            'sad_documentdate'=>$this->sad_documentdate,
                            'sad_postingdate'=>$this->sad_postingdate,
                            'sad_fiscalyear'=>$this->sad_fiscalyear,
                            'sad_postingperiod'=>$this->sad_postingperiod,
                            'sad_reference'=>$this->sad_reference,
                            'sad_username'=>$this->sad_username,
                            'sad_offsettaccounttype'=>$this->sad_offsettaccounttype,
                            'sad_offsettingacctno'=>$this->sad_offsettingacctno                            
                        );
                        // debug_array('detanto');
                        $rslCSV = $this->m_analisa->getExpensedetail_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2, $anaRefkey, $refkeydetail, false);

                        // $this->common->debug_sql(1);
                        break;
                    case "2":
                        if($anaGrant==0){
                            $arrField = array(
                                'grn_idents'=>$this->grn_code,
                                'sad_grant'=>$this->grn_code,
                                'grn_kind'=>$this->grn_kind,
                                'grn_kind_desc'=>$this->grn_kind,
                                'grn_shortname'=>$this->grn_shortname,
                                'grn_sponsor'=>$this->grn_sponsor,
                                'grn_sponsor_name'=>$this->grn_sponsor_name,
                                'grn_datestart'=>str_replace("Valid From ", "", $this->grn_datestart),
                                'grn_dateend'=>str_replace("Valid From ", "", $this->grn_dateend),
                                'sad_expense'=>$this->amount
                            );

                        }else{
                            $arrField = array(
                                'sad_referencekey3'=>$this->sad_referencekey3,
                                'gra_itemdescription'=>$this->grn_description,
                                'sad_expense'=>$this->amount
                            );
                        }
                        $rslCSV = $this->m_analisa->getExpensesummary_list($from, $anaKind, $anaGrant, $anaYear, $anaMonth1, $anaMonth2, $anaRefkey, 0, false);
                        break;
                }                
                break;
            case "bva":
                if($anaGrant==0){
                    $arrField = array(
                        'grn_idents'=>"Identitas",
                        'grn_code'=>"Grant Code",
                        'grn_shortname'=>"Grant Short Name",
                        'grn_datestart'=>"Starting Date",
                        'grn_dateend'=>"End Date",
                        'grn_totalbudget'=>"Total Budget",
                        'grn_icrbudget'=>"ICR",
                        'grn_directbudget'=>"Direct Cost",
                        'grn_expense'=>"Expense",
                        'grn_remaining'=>"Remaining",
                        'grn_burnrate'=>"Burn Rate",
                        'grn_remainingperiod'=>"Remaining (Month)",
                    );
                    $url = "/nosj/getBVAdetail_list/".$anaKind ."/".$anaGrant . "/" .$anaYear . "/" .$anaMonth1;
                }else{
                    $arrField = array(
                        "grn_idents"=>"Identitas",
                        "grn_code"=>"Item Description/Expense",
                        "gra_itemdescription"=>"Item Description/Expense",
                        "gra_refkey3"=>"Ref Key 3",
                        "grn_totalbudget"=>"Budget",
                        "grn_expense"=>"Expense",
                        "grn_remaining"=>"Remaining",
                        "grn_burnrate"=>"Burn Rate",
                    );
                }
                $namafile = "bva";
                $rslCSV = $this->m_analisa->getBVAdetail_list($anaKind, $anaGrant, $anaYear, $anaMonth1, false);
                break;
        }
        foreach($arrField as $key=>$value){
            $arrHeader[] = $value;
            $arrFields[] = $key;
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=".$namafile."");
        header("Content-Transfer-Encoding: binary ");        
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="' . $namafile . "_" . date('Y_m_d_His'). '.csv"');
        // header('Pragma: no-cache');
        // header('Expires: 0');
        // create a file pointer connected to the output stream
        // debug_array($rslCSV);
        $file = fopen('php://output', 'w');
            
        // send the column headers
        
        fputcsv($file, $arrHeader);
        foreach ($rslCSV->result() as $key => $value) {
            $row = array();
            for($e=0;$e<count($arrFields); $e++){
                if(isset($value->{$arrFields[$e]})){
                    $nilainya = $value->{$arrFields[$e]};
                }else{
                    $nilainya = null;
                }
                $row = array_merge($row, array($nilainya));
            }
            fputcsv($file, $row);           
        }
        exit();
    }
    function hasilpenilaian(){
        
        $urutan = 0;
        $formname = "frmExpense";
        $optOption = array(1=>"Detail", 2=>"Summary");
        $optrefkey = array("1"=>"Yes", "2"=>"No");
        $script = null;
        $field = array("asm_idents", "asm_tahun");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
$optAsesmen = $this->crud->getGeneral_combo($arrayOpt);
        $field = array("unt_idents", "unt_unitkerja");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_unitkerja",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );

        $optUnitkerja = $this->crud->getGeneral_combo($arrayOpt);
        
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
        
        
        $optOption = array(1=>"Kategori", 2=>"Process Area");

        $arrField = array(
            "grf_asesmen"=>array("group"=>1, "label"=>"Tahun Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesmen tidak boleh kosong")),
            //"grf_level"=>array("group"=>1, "label"=>"Level","type"=>"cmb", "size"=>"200px", "option"=>$optLevel, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Level tidak boleh kosong")),
            "grf_option"=>array("group"=>1, "label"=>"Jenis","type"=>"cmb", "size"=>"200px", "option"=>$optOption, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Jenis tidak boleh kosong")),
            "grf_unitkerja"=>array("group"=>1, "label"=>"Unit Kerja","type"=>"cmb","option"=>$optUnitkerja, "size"=>"400px", "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Unit Kerja tidak boleh kosong")),
        );
        $arrTable = $this->common->generateArray($arrField, null, null, false);

        $arrButton = array(
            array("text"=>"Graph", "events"=>"jvShow('graph')", "theme"=>"success", "image"=>"fas fa-chart-pie"),
            array("text"=>"Report", "events"=>"jvShow('report')", "theme"=>"danger", "image"=>"fas fa-table"),
        );
        $button = createButton($arrButton, false, false);
        $arrTable[] = array('group'=>1, 'urutan'=>99, "type"=>"udi", "namanya"=>"anyGrant", "label"=>"&nbsp;", "value"=>$button);

        $arrForm = array(
            'type'=>"edit",
            'arrTable'=>$arrTable,
            'status'=> isset($status) ? $status : "",
            'param' =>null,
            'width'=>'70%',
            'modul' => 'sppdb',
            'nameForm' => $formname,
            'formcommand' => '/hr/employee/save',
            'tabname'=> array(
                '1'=>'fas fa-question-circle^Penilaian'
            )
        );

        $content =  generateForm($arrForm);
        
        $content .= "
        <script>
       
        function jvShow(type){
            validator
            .validate()
            .then(function(status){
                if(status!='Invalid'){
                    var data_type = $('#grf_asesmen').select2('data');
                    grf_asesmen_desc = data_type[0].text;
        
                   //var data_type = $('#grf_level').select2('data');
                    grf_level_desc = data_type[0].text;
        
                    var data_type = $('#grf_option').select2('data');
                    grf_option_desc = data_type[0].text;
        
                    var data_type = $('#grf_unitkerja').select2('data');
                    grf_unitkerja_desc = data_type[0].text;
                
                    title ='Grafik';
                    var optionnnya = grf_option_desc + '-' + $('#grf_level').val()
        
                     var param ={};
                    param['type'] = type;
                    param['grf_asesmen'] = $('#grf_asesmen').val();
                    param['grf_asesmen_desc'] = grf_asesmen_desc;
                    param['grf_option'] = $('#grf_option').val();
                    param['grf_option_desc'] = grf_option_desc;
                    param['grf_level'] = $('#grf_level').val();
                    param['grf_level_desc'] = grf_level_desc;
                    param['grf_unitkerja'] = $('#grf_unitkerja').val();
                    param['grf_unitkerja_desc'] = grf_unitkerja_desc;
                    
                    url = '/analysis/kuesioner/'+type+'Penilaian';
                    if(type=='graph'){
                        $('#jqwPopup').jqxWindow('open');
                        $.post(url,param,function(data){
                            windowWidth = $(window).width()-250; 
                            windowHeight = $(window).height()-100; 
                            
                            var winHeight = $(window).height();
                            var winWidth = $(window).width();
                
                            // windowWidth = $(window).width()-80; 
                            // windowHeight = $(window).height()-80; 
                            var posX = (winWidth/2) - (windowWidth/2) + $(window).scrollLeft() + 135;
                            var posY = (winHeight/2) - (windowHeight/2) + $(window).scrollTop();
                
                            $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: true, width:'80%', height:windowHeight, autoOpen: false,position: {x: posX, y: posY}, resizable:false,title: title });  
                            $('#jqwPopup').jqxWindow('setContent', data);
                        });
                    }else{
                        var winName='winReportAsesmen';
                        var winURL=url;
                        var winheight = $(window).height();
                        var winwidth = $(window).width();
                        var windowoption='resizable=yes,height='+winheight+',width='+winwidth+',location=0,menubar=0,scrollbars=1';
                        var form = document.createElement('form');
                        form.setAttribute('method', 'post');
                        form.setAttribute('action', winURL);
                        form.setAttribute('target', winName); 
                        for (var i in param) {
                          if (param.hasOwnProperty(i)) {
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = i;
                            input.value = param[i];
                            form.appendChild(input);
                          }
                        }
                        document.body.appendChild(form);
                        window.open('', winName,windowoption);
                        form.target = winName;
                        form.submit();
                        document.body.removeChild(form);
                    }
                            
                }
            })            
        }

        function jvCsv(){
            var data_type = $('#anaOption').select2('data');
            option_desc = data_type;

            var data_type = $('#anaOption').select2('data');
            option_desc = data_type;

            var data_type = $('#anaKind').select2('data');
            kind_desc = data_type[0].text;

            var data_type = $('#anaMonth1').select2('data');
            month1_desc = data_type[0].text;

            var data_type = $('#anaGrant').select2('data');
            grant_desc = data_type[0].text;

            var param ={};
            param['jenis'] = 'expense';
            param['anaOption'] = $('#anaOption').val();
            param['option_desc'] = option_desc;
            param['anaGrant'] = $('#anaGrant').val();
            param['anaYear'] = $('#anaYear').val();
            param['anaKind'] = $('#anaKind').val();
            param['kind_desc'] = kind_desc;
            param['anaMonth1'] = $('#anaMonth1').val();
            param['grant_desc'] = grant_desc;
            param['month1_desc'] = month1_desc;

            $('#imgPROSES').show();
            $('#windowProses').jqxWindow('open');

            var winName='csv';
            var winURL='/analysis/grant/csv';
            var form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', winURL);
            form.setAttribute('target', winName); 
            for (var i in param) {
                if (param.hasOwnProperty(i)) {
                  var input = document.createElement('input');
                  input.type = 'hidden';
                  input.name = i;
                  input.value = param[i];
                  form.appendChild(input);
                }
            }
            $('#windowProses').jqxWindow('close');
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }        
        </script>
        ";
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200px', 'widths'=>'1440px', 'maxWidth'=>'1440px', 'maxHeight'=>'1440px','overflow'=>'auto'));
        $content .= generateWindowjqx(array('window'=>'Detailgrafik','title'=>'Info Detail','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));

        return $content;
    }
    function asesmen(){
        
        $urutan = 0;
        $formname = "frmExpense";
        $optOption = array(1=>"Detail", 2=>"Summary");
        $optrefkey = array("1"=>"Yes", "2"=>"No");
        $script = null;
        $field = array("asm_idents", "asm_tahun");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
        $optAsesmen = $this->crud->getGeneral_combo($arrayOpt);
        $field = array("unt_idents", "unt_unitkerja");
        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_mas_unitkerja",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );

        $optUnitkerja = $this->crud->getGeneral_combo($arrayOpt);

        $field = array("asm_idents", "asm_tahun");

        $arrayOpt = array(
            "type"=> 1,
            "table"=> "t_asm_asesmen",
            "field"=> $field,
            "protected"=>true,
            "separator"=>array("[","]")
        );
        
        $optAsesmen = $this->crud->getGeneral_combo($arrayOpt);

        $optOption = array(1=>"Terkirim", 2=>"Draft");

        $arrField = array(
            "grf_asesmen"=>array("group"=>1, "label"=>"Tahun Asesmen","type"=>"cmb", "size"=>"200px", "option"=>$optAsesmen, "validation"=>array("validation"=>"notzeroEmpty", "message"=>"Asesmen tidak boleh kosong")),
            "grf_option"=>array("group"=>1, "label"=>"Status","type"=>"cmb", "size"=>"200px", "option"=>$optOption),
            "grf_provinsi"=>array("group"=>1, "label"=>"Unit Kerja","type"=>"cmb","option"=>$optUnitkerja, "size"=>"200px"),
        );
        $arrTable = $this->common->generateArray($arrField, null, null, false);

        $arrButton = array(
            array("text"=>"Graph", "events"=>"jvShow('graph')", "theme"=>"success", "image"=>"fas fa-chart-pie"),
            array("text"=>"Report", "events"=>"jvShow('report')", "theme"=>"danger", "image"=>"fas fa-table"),
        );
        $button = createButton($arrButton, false, false);
        $arrTable[] = array('group'=>1, 'urutan'=>99, "type"=>"udi", "namanya"=>"anyGrant", "label"=>"&nbsp;", "value"=>$button);

        $arrForm = array(
            'type'=>"edit",
            'arrTable'=>$arrTable,
            'status'=> isset($status) ? $status : "",
            'param' =>null,
            'width'=>'70%',
            'modul' => 'sppdb',
            'nameForm' => $formname,
            'formcommand' => '/hr/employee/save',
            'tabname'=> array(
                '1'=>'fas fa-question-circle^Asesmen'
            )
        );

        $content =  generateForm($arrForm);
        
        $content .= "
        <script>
       
        function jvShow(type){
            validator
            .validate()
            .then(function(status){
                if(status!='Invalid'){
                    var data_type = $('#grf_asesmen').select2('data');
                    grf_asesmen_desc = data_type[0].text;
        
                    var data_type = $('#grf_option').select2('data');
                    grf_option_desc = data_type[0].text;
        
                    var data_type = $('#grf_provinsi').select2('data');
                    grf_provinsi_desc = data_type[0].text;
        
                    var data_type = $('#grf_kabptn').select2('data');
                    grf_kabptn_desc = null;//data_type[0].text;
        
                    title ='';
        
                    var param ={};
                    param['type'] = type;
                    param['grf_asesmen'] = $('#grf_asesmen').val();
                    param['grf_asesmen_desc'] = grf_asesmen_desc;
                    param['grf_option'] = $('#grf_option').val();
                    param['grf_option_desc'] = grf_option_desc;
                    param['grf_provinsi'] = $('#grf_provinsi').val();
                    param['grf_provinsi_desc'] = grf_provinsi_desc;
                    param['grf_kabptn'] = $('#grf_kabptn').val();
                    param['grf_kabptn_desc'] = grf_kabptn_desc;
                    $('#jqwPopup').jqxWindow('open');
                    
                    $.post('/analysis/kuesioner/'+type+'Asesmen',param,function(data){
                        windowWidth = $(window).width()-150; 
                        windowHeight = $(window).height()-20; 
                        $('#jqwPopup').jqxWindow({isModal: true, showCollapseButton: true, width:windowWidth, height:windowHeight, autoOpen: false,position:'middle', resizable:false,title: title });  
                        $('#jqwPopup').jqxWindow('setContent', data);
                    });
                            
                }
            })            
        }

        function jvCsv(){
            var data_type = $('#anaOption').select2('data');
            option_desc = data_type;

            var data_type = $('#anaOption').select2('data');
            option_desc = data_type;

            var data_type = $('#anaKind').select2('data');
            kind_desc = data_type[0].text;

            var data_type = $('#anaMonth1').select2('data');
            month1_desc = data_type[0].text;

            var data_type = $('#anaGrant').select2('data');
            grant_desc = data_type[0].text;

            var param ={};
            param['jenis'] = 'expense';
            param['anaOption'] = $('#anaOption').val();
            param['option_desc'] = option_desc;
            param['anaGrant'] = $('#anaGrant').val();
            param['anaYear'] = $('#anaYear').val();
            param['anaKind'] = $('#anaKind').val();
            param['kind_desc'] = kind_desc;
            param['anaMonth1'] = $('#anaMonth1').val();
            param['grant_desc'] = grant_desc;
            param['month1_desc'] = month1_desc;

            $('#imgPROSES').show();
            $('#windowProses').jqxWindow('open');

            var winName='csv';
            var winURL='/analysis/grant/csv';
            var form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', winURL);
            form.setAttribute('target', winName); 
            for (var i in param) {
                if (param.hasOwnProperty(i)) {
                  var input = document.createElement('input');
                  input.type = 'hidden';
                  input.name = i;
                  input.value = param[i];
                  form.appendChild(input);
                }
            }
            $('#windowProses').jqxWindow('close');
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }


                       
                    
                    
                    
                            
                       
        
         
        </script>
        ";
        $content .= generateWindowjqx(array('window'=>'Popup','title'=>'Info Detail','height'=>'200px', 'widths'=>'1440px', 'maxWidth'=>'1440px', 'maxHeight'=>'1440px','overflow'=>'auto'));
        $content .= generateWindowjqx(array('window'=>'Detailgrafik','title'=>'Info Detail','height'=>'200', 'minWidth'=>100,'widths'=>'1200px','overflow'=>'auto'));

        return $content;
    }
    function graphAsesmen(){
        $click = null;
        $type = $this->input->post('type');
        $grf_asesmen = $this->input->post('grf_asesmen');
        $grf_asesmen_desc = $this->input->post('grf_asesmen_desc');
        $grf_level = $this->input->post('grf_level');
        $grf_level_desc = $this->input->post('grf_level_desc');
        $grf_option = $this->input->post('grf_option');
        $grf_option_desc = $this->input->post('grf_option_desc');
        $grf_unitkerja = $this->input->post('grf_unitkerja');
        $grf_unitkerja_desc = $this->input->post('grf_unitkerja_desc'); 

        $rslKelompok = $this->m_asesmen->getKelompokKategorii($grf_asesmen, $grf_unitkerja, $grf_option);
        //$this->common->debug_sql(1);
        $flotarea = array(
            'id'    =>  'grfAsesmen',
            'chart' =>  'spider',
            'width' =>  "100%",
            'labelling' => 'Score',
            'warna'=>'#5cb85c',
            'resultset'=>$rslKelompok,
            'fields'=>array("descre"=>"idk_nama", "values"=>"idk_progress"),
        );
        
        $grafik = createportlet(array("content"=>display_chart($flotarea),"title"=>"Grafik Asesmen ", "icon"=>"fas fa-chart-pie", "class"=>"portletGrafik"));
        echo $grafik;
        die();
    }
    function reportAsesmen(){           //-----ini nampilin pdf di bagian laporan-----
        //$this->common->debug_post();
        $type = $this->input->post('type');
        $grf_asesmen = $this->input->post('grf_asesmen');
        $grf_asesmen_desc = $this->input->post('grf_asesmen_desc');
        $grf_level = $this->input->post('grf_level');
        $grf_level_desc = $this->input->post('grf_level_desc');
        $grf_option = $this->input->post('grf_option');
        $grf_option_desc = $this->input->post('grf_option_desc');
        $grf_unitkerja = $this->input->post('grf_unitkerja');
        $grf_unitkerja_desc = $this->input->post('grf_unitkerja_desc');        

        // $rslGrafik = $this->m_asesmen->getKelompokIndikator_per_asesmen($grf_asesmen, $grf_kabptn);
        $rslKelompok = $this->m_asesmen->getKelompokKategorii($grf_asesmen, $grf_unitkerja, $grf_level_desc, $grf_option);
       // $this->common->debug_sql(1);

        //debug_array($rslKelompok->result());

        $loop = 1;
        $idk_progress_total = 0;
        $table_indikator_parent = '
        <h3 class="fmuseo">HASIL CAPAIAN PERKELOMPOK UNIT KERJA KPK</h3>
        <table class="no-border">
            <tr>
                <td>Unit Kerja</td>
                <td style="width:10px;padding-left:10px;padding-right:10px">:</td>
                <td>'.$grf_unitkerja_desc . " (" . $grf_level_desc.')</td>
            </td>
            <tr>
                <td>Tahun Asesmen</td>
                <td style="width:10px;padding-left:10px;padding-right:10px">:</td>
                <td>'.$grf_asesmen_desc .'</td>
            </td>
        </table>
        <table class="w-border" style="margin-top:50px;width:100%;">
        <tr><th>No</th><th>Kategori</th><th>%</th></tr>
        ';
        foreach($rslKelompok->result() as $keyKelompok=>$valueKelompok){
            $idk_idents = $valueKelompok->idk_idents;
             
            $idk_nama = $valueKelompok->idk_nama; 
            $idk_progress = $valueKelompok->idk_progress;
            $table_indikator_parent .= '
            <tr>
                <td style="width:20px" >'.$loop.'.</td>
                <td><span >'.$idk_nama.'</span></td>
                <td style="width:50px" class="centerright">'. number_format($idk_progress,0) .' %</td>
            </tr>
            ';
            $idk_progress_total = $idk_progress_total + $idk_progress;
            $loop++;
        }

        $table_indikator_parent .= '</table>';

        foreach($rslKelompok->result() as $keyKelompok=>$valueKelompok)
        {
            $idk_parent = $valueKelompok->idk_idents;
            $idk_nama_parent = $valueKelompok->idk_nama;
            $rslIndikator = $this->m_asesmen->getKelompokKategori_detail($grf_asesmen, $grf_unitkerja, $idk_parent , $grf_level);
            //$this->common->debug_sql(1);
            $table_indikator_detail = '
            <h3 class="fmuseo">'.$idk_nama_parent.'</h3>
            <table class="w-border" style="width:100%;">
            <tr>
            <th style="width:5px" class="center">No</th>
            <th>Proses Area</th>
            <th style="width:50px" class="centerright">%</th>
            </tr>
            ';
            
            $loop = 1;
            foreach($rslIndikator->result() as $keyIndikator=>$valueIndikator){
            $idk_idents = $valueIndikator->idk_idents;
            $idk_nama = $valueIndikator->idk_nama; 
            $idk_progress = $valueIndikator->idk_progress;
            $table_indikator_detail .= '
            
            <tr>
            <td style="width:5px" class="center">'.$loop.'.</td>
            <td>'.$idk_nama.'</td>
            <td style="width:50px" class="centerright">'. ($idk_progress==0 ? "" : number_format($idk_progress,0) ."%") .' </td>
            </tr>';
            $loop++;
            }
            $table_indikator_detail .= '</table>';
            $table_detail[] = $table_indikator_detail;
        }
        $table_indikator_detail_show = null;
        foreach($table_detail as $keydetail=>$valuedetail){
            $table_indikator_detail_show .= $valuedetail;
        }
        // $flotarea = array(
        //     'id'    =>  'grfAsesmen',
        //     'chart' =>  'spider',
        //     'width' =>  "80%",
        //     'labelling' => 'Score',
        //     'warna'=>'#5cb85c',
        //     'resultset'=>$rslGrafik,
        //     'surround_div'=>false,
        //     'fields'=>array("descre"=>"idk_nama", "values"=>"idk_progress"),
        // );
        
        // $grafik = display_chart($flotarea);

        // $table = "<table style='width:100%' class='no-border'>";
        // // $table .= "<tr>";
        // // $table .= " <td><div style='height:80%;width:80%'>" . $grafik . "</div></td>";
        // // $table .= "</tr>";
        // $table .= "<tr>";
        // $table .= " <td>" . $table_indikator_parent . "</td>";
        // $table .= "</tr>";
        // $table .= "</table>";
        // $this->common->prnfile(array('JUDULS'=>null,'REPORT'=>$table, 'TYPESS'=>'pdf','showheader'=>false, 'perdirjen'=>true));

        $table = $table_indikator_parent;
        $table .= $table_indikator_detail_show;
        $namafile = "reportAsesmen_".str_replace(" ","_", $grf_unitkerja);
        $paramPDF = array(
            'juduls'=>$namafile, 
            'report'=>$table, 
            "filename"=>$namafile, 
            "showheader"=>false, 
            "letter_header"=>false, 
            "letter_footer"=>false,
            "paper"=>"A4",
            "margin_left"=>"1",
            "margin_right"=>"1"
        );
        $this->common->showPDF($paramPDF);        
        // echo $table;
    } 
    function reportPenilaian(){           //-----ini nampilin pdf di bagian laporan-----
        //$this->common->debug_post();
        $type = $this->input->post('type');
        $grf_asesmen = $this->input->post('grf_asesmen');
        $grf_asesmen_desc = $this->input->post('grf_asesmen_desc');
        $grf_level = $this->input->post('grf_level');
        $grf_level_desc = $this->input->post('grf_level_desc');
        $grf_option = $this->input->post('grf_option');
        $grf_option_desc = $this->input->post('grf_option_desc');
        $grf_unitkerja = $this->input->post('grf_unitkerja');
        $grf_unitkerja_desc = $this->input->post('grf_unitkerja_desc');        

        // $rslGrafik = $this->m_asesmen->getKelompokIndikator_per_asesmen($grf_asesmen, $grf_kabptn);
        $rslKelompok = $this->m_asesmen->getKelompokKategorii($grf_asesmen, $grf_unitkerja, $grf_level_desc, $grf_option);
        
       // $this->common->debug_sql(1);

        //debug_array($rslKelompok->result());

        $loop = 1;
        $idk_progress_total = 0;
        $table_indikator_parent = '<table class="no-border" style= "width: 90%; height: 5%; text-align: center; margin-left:-12%" >
 <tr> 
     <th bgcolor="">N-0%-15%</th>
    <th bgcolor="#D9D9D9">P-15%-50%</th>
    <th bgcolor="skyblue">L-50%-85%</th>
    <th bgcolor="#0070C0"  style= "color:white">F-85%-100%</th>
</tr></table><hr />
        <h3 class="fmuseo">HASIL PENILAIAN PERKELOMPOK UNIT KERJA KPK</h3>
        
        <table class="no-border">
            <tr>
                <td>Unit Kerja</td>
                <td style="width:10px;padding-left:10px;padding-right:10px">:</td>
                <td>'.$grf_unitkerja_desc . " (" . $grf_level_desc.')</td>
            </td>
            <tr>
                <td>Tahun Asesmen</td>
                <td style="width:10px;padding-left:10px;padding-right:10px">:</td>
                <td>'.$grf_asesmen_desc .'</td>
            </td>
        </table>
        <table class="w-border" style="margin-top:50px;width:100%;">
        <tr><th>No</th><th>Kategori</th><th>%</th></tr>
        ';
        foreach($rslKelompok->result() as $keyKelompok=>$valueKelompok){
            $idk_idents = $valueKelompok->idk_idents;
             
            $idk_nama = $valueKelompok->idk_nama; 
            $idk_progress = $valueKelompok->idk_progress;
            $table_indikator_parent .= '
            <tr>
                <td style="width:20px" >'.$loop.'.</td>
                <td><span >'.$idk_nama.'</span></td>
                <td style="width:50px" class="centerright">'. number_format($idk_progress,0) .' %</td>
            </tr>
            ';
            $idk_progress_total = $idk_progress_total + $idk_progress;
            $loop++;
        }

        $table_indikator_parent .= "</table>
                     ";

        foreach($rslKelompok->result() as $keyKelompok=>$valueKelompok)
        {
            $idk_parent = $valueKelompok->idk_idents;
            $idk_nama_parent = $valueKelompok->idk_nama;
            $rslIndikator = $this->m_asesmen->getKelompokKategori_detailpenilaian($grf_asesmen, $grf_unitkerja, $idk_parent , $grf_level);
            //$this->common->debug_sql(1);
            $table_indikator_detail = '
            <h3 class="fmuseo">'.$idk_nama_parent.'</h3>
            <table class="w-border" style="width:100%;">
            <tr>
            <th style="width:5px" class="center">No</th>
            <th>Proses Area</th>
            <th class="center">Level 0</th>
            <th class="center">Level 1</th>
            <th class="center">Level 2</th>
            <th class="center">Level 3</th>
            <th class="center">Level 4</th>
            <th class="center">Level 5</th>
            <th style="width:50px" class="centerright">%</th>
            </tr>
            ';
            $script .='<script>
let d = new Date();
document.body.innerHTML = "<h1>Time right now is:  " + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds()
"</h1>"
</script>';
            
            
            $loop = 1;
            foreach($rslIndikator->result() as $keyIndikator=>$valueIndikator){
            $idk_idents = $valueIndikator->idk_idents;
            $idk_nama = $valueIndikator->idk_nama; 
            $idk_progress = $valueIndikator->idk_progress;
                
            $nil_total = $valueIndikator->nil_total;
            $nil_tnylevel = $valueIndikator->nil_tnylevel; 
            $idk_idents = $valueIndikator->idk_idents;
            $table_indikator_detail .= '
            
            <tr>
                        <td style="width:5px" class="center">'.$loop.'</td>
                        <td>'.$idk_nama.'</td>
                        <td class="center">'.$nil_total.' %</td>
                        <td class="center" id="demoo"> '. 
                        .'</td>
                        <td class="center"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td >'. ($idk_progress==0 ? "" : number_format($idk_progress,0) ."%") .' </td>
            </tr>';
     
             $table .="</table>   
 
            $loop++;
            }
            $table_indikator_detail .= '</table>';
            $table_detail[] = $table_indikator_detail;
        }
        $table_indikator_detail_show = null;
        foreach($table_detail as $keydetail=>$valuedetail){
            $table_indikator_detail_show .= $valuedetail;
        }
        // $flotarea = array(
        //     'id'    =>  'grfAsesmen',
        //     'chart' =>  'spider',
        //     'width' =>  "80%",
        //     'labelling' => 'Score',
        //     'warna'=>'#5cb85c',
        //     'resultset'=>$rslGrafik,
        //     'surround_div'=>false,
        //     'fields'=>array("descre"=>"idk_nama", "values"=>"idk_progress"),
        // );
        
        // $grafik = display_chart($flotarea);

        // $table = "<table style='width:100%' class='no-border'>";
        // // $table .= "<tr>";
        // // $table .= " <td><div style='height:80%;width:80%'>" . $grafik . "</div></td>";
        // // $table .= "</tr>";
        // $table .= "<tr>";
        // $table .= " <td>" . $table_indikator_parent . "</td>";
        // $table .= "</tr>";
        // $table .= "</table>";
        // $this->common->prnfile(array('JUDULS'=>null,'REPORT'=>$table, 'TYPESS'=>'pdf','showheader'=>false, 'perdirjen'=>true));

        $table = $table_indikator_parent;
        $table .= $table_indikator_detail_show;
        $namafile = "reportAsesmen_".str_replace(" ","_", $grf_unitkerja);
        $paramPDF = array(
            'juduls'=>$namafile, 
            'report'=>$table, 
            "filename"=>$namafile, 
            "showheader"=>false, 
            "letter_header"=>false, 
            "letter_footer"=>false,
            "paper"=>"A4",
            "margin_left"=>"1",
            "margin_right"=>"1"
        );
        $this->common->showPDF($paramPDF);        
        // echo $table;
    } 
}
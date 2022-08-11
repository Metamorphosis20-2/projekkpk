<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userlog extends MY_Controller {
  function __construct(){
    parent::__construct();
    	$this->load->helper('ginput');
    	$this->load->model(array('m_master','m_common'));
        $this->modul = $this->router->fetch_class();
        $this->log_address = $this->lang->line("log_address");
        $this->usr_nama = $this->lang->line("usr_nama");
        $this->log_from = $this->lang->line("log_from");
        $this->log_table = $this->lang->line("log_table");
        $this->log_field = $this->lang->line("log_field");
        $this->log_action = $this->lang->line("log_action");
        $this->log_result = $this->lang->line("log_result");
        $this->log_usrnam = $this->lang->line("log_usrnam");
        $this->log_usrdat = $this->lang->line("date");
        $this->log_useractivity = $this->lang->line("log_useractivity");
        $this->log_accesshistory = $this->lang->line("log_accesshistory");
        $this->log_useraccess = $this->lang->line("log_useraccess");
        $this->log_successaccess = $this->lang->line("log_successaccess");
        $this->log_usertotal = $this->lang->line("log_usertotal");
    }	
	public function index($parameter="akses"){
        $txt = null;
        if($this->usrlevel!=1){
            $txt = " user : [" . $this->username . "]";
        }
        $arrbread = array(
            array('link'=>'/home/welcome','text'=>'Beranda'),
            array('link'=>'#','text'=> 'Data Log' . $txt),
        );

        $bc = generateBreadcrumb($arrbread);
        $this->_render('pages/home', $this->getUser_log($parameter),'admin',$bc);  	 
	}
    function getUser_log($parameter){
        $this->load->helper(array('highchart','jqxgrid'));
        // debug_array($parameter);
        switch ($parameter) {
            case "akses":
                $table = "t_USRLOG";
                $col[] = array('lsturut'=>0, 'namanya'=>'CI_Rownum','ah'=>true);
                $col[] = array('lsturut'=>1, 'namanya'=>'USL_IDENTS','ah'=>true);
                $col[] = array('lsturut'=>2, 'namanya'=>'USR_FNAMES','aw'=>'23%','label'=>$this->usr_nama);
                $col[] = array('lsturut'=>3, 'namanya'=>'USL_USRNAM','aw'=>'9%','label'=>$this->log_usrnam);
                $col[] = array('lsturut'=>4, 'namanya'=>'USL_USRDAT','aw'=>'14%','label'=>$this->log_usrdat, 'ac'=>true);
                $col[] = array('lsturut'=>5, 'namanya'=>'USL_ADDRES','aw'=>'10%','label'=>$this->log_address);
                $col[] = array('lsturut'=>6, 'namanya'=>'USL_BROWSR','aw'=>'36%','label'=>'Browser');
                $col[] = array('lsturut'=>7, 'namanya'=>'USL_STATUS','aw'=>'7%','label'=>'Status');
                $html = $this->gridshow(array('name'=>'Riwayatakses', 'col'=>$col));
                $content = generateTabjqx(array(
                                            'id'=>'Dashboard',
                                            'width'=>'100%',
                                            'ajax'=>true,
                                            'utama'=>'/master/userlog/tabval',
                                            'arrTabs'=> array(
                                                    'fas fa-globe^'. $this->log_accesshistory =>array('data'=>$html),
                                                    'fas fa-users^'. $this->log_useraccess =>array('data'=>""),
                                                    'fas fa-user-times^'. $this->log_successaccess =>array('data'=>''),
                                                    'fas fa-user^'. $this->log_usertotal =>array('data'=>''),
                                                )
                                            ));
                break;
            case "aktivitas" :
                $table = "t_log_aktivitas";
                $urutan = 0;
                                
                $col[] = array('lsturut'=>1, 'namanya'=>'CI_Rownum','ah'=>true);
                $col[] = array('lsturut'=>2, 'namanya'=>'USL_IDENTS','ah'=>true);
                $col[] = array('lsturut'=>3, 'namanya'=>'USL_USRNAM','aw'=>'8%','label'=>$this->log_usrnam);
                $col[] = array('lsturut'=>4, 'namanya'=>'USL_USRDAT','aw'=>'12%','label'=>$this->log_usrdat, 'ac'=>true);
                $col[] = array('lsturut'=>5, 'namanya'=>'USL_ADDRES','aw'=>'150','label'=>$this->log_address);
                $col[] = array('lsturut'=>8, 'namanya'=>'USL_MODULE','aw'=>'150','label'=>$this->log_from);
                $col[] = array('lsturut'=>9, 'namanya'=>'USL_ACTION','aw'=>'500','label'=>$this->log_action);
                $col[] = array('lsturut'=>9, 'namanya'=>'log_table','aw'=>'500','label'=>'Tindakan','ah'=>true);
                
                $where= null;
                $html = $this->gridshow(array('name'=>'Aktivitas', 'col'=>$col, "jenis"=>$where, "eventgrid"=>true));
                $content = generateTabjqx(array(
                                            'id'=>'Dashboard',
                                            'width'=>'100%',
                                            'arrTabs'=> array(
                                                    'fas fa-history^' . $this->log_useractivity =>array('data'=>$html)
                                                )
                                            ));

                break;                          
            default:
                break;
        }
        $arrAction =array(
            "action"=> "View " . ucfirst($parameter)
        );

        $arrModul = array(
            "from"=>$this->modul, 
            "table_name"=>$table, 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"1", 
            "log_action"=>$arrAction,
            "log_fkidents"=>null
        );
        $this->common->logmodul(false, $arrModul);  
        $content .= generateWindowjqx(array('window'=>'Informasi','title'=>'Informasi','height'=>'auto', 'minWidth'=>100, 'maxWidth'=>'1800px','overflowy'=>'auto'));
        return $content;
    }

    function tabval($value){
        $urutan = 0;
        switch ($value) {
            case 'riwayatakses':
            case 'accesshistory':
                $col[] = array('lsturut'=>1, 'namanya'=>'USL_IDENTS','ah'=>true);
                $col[] = array('lsturut'=>2, 'namanya'=>'USR_FNAMES','aw'=>'23%','label'=>'Nama ');
                $col[] = array('lsturut'=>3, 'namanya'=>'USL_USRNAM','aw'=>'9%','label'=>'Pengguna');
                $col[] = array('lsturut'=>4, 'namanya'=>'USL_USRDAT','aw'=>'14%','label'=>'Tanggal Akses', 'ac'=>true);
                $col[] = array('lsturut'=>5, 'namanya'=>'USL_ADDRES','aw'=>'10%','label'=>'IP');
                $col[] = array('lsturut'=>6, 'namanya'=>'USL_BROWSR','aw'=>'36%','label'=>'Browser');
                $col[] = array('lsturut'=>7, 'namanya'=>'USL_STATUS','aw'=>'7%','label'=>'Status');
                $html = $this->gridshow(array('name'=>'RiwayatAkses', 'col'=>$col));
                break;
            case 'aktivitas':
                $col[] = array('lsturut'=>1, 'namanya'=>'USL_IDENTS','ah'=>true);
                $col[] = array('lsturut'=>2, 'namanya'=>'USR_FNAMES','aw'=>'26%','label'=>'Nama ');
                $col[] = array('lsturut'=>3, 'namanya'=>'USL_USRNAM','aw'=>'9%','label'=>'Pengguna');
                $col[] = array('lsturut'=>4, 'namanya'=>'USL_USRDAT','aw'=>'10%','label'=>'Tanggal Akses', 'ac'=>true);
                $col[] = array('lsturut'=>5, 'namanya'=>'USL_ADDRES','aw'=>'8%','label'=>'IP');
                $col[] = array('lsturut'=>6, 'namanya'=>'USL_PARAMS','aw'=>'36%','label'=>'Browser');
                $col[] = array('lsturut'=>7, 'namanya'=>'USL_ERRORS','aw'=>'7%','label'=>'Status');
                $html = $this->gridshow(array('name'=>'WebService', 'col'=>$col));
                break;
            case "aksespengguna" :
            case 'useraccess':
                $result = $this->m_common->getUseraccess_list();
                $html = $this->showstat($value,array('resultset'=>$result, 'chart'=>'line', 'fields'=>'USL_USRDAT~USL_TOTAL', 'legend'=>'Akses Pengguna',"title"=>"Data Akses Pengguna " . date('Y')));
                break;
            case "aksesberhasil" :
            case "successratio":
                $result = $this->m_common->getUsersuccess_list();
                $html = $this->showstat($value,array('resultset'=>$result, 'chart'=>'pie', 'fields'=>'USL_STATUS~USL_TOTAL', 'legend'=>'Keberhasilan', "title"=>"Rasio Keberhasilan Akses Pengguna", 'arrValue'=>array('0'=>'Gagal','1'=>'Berhasil')));
                break;
            case "totalpengguna" :
            case "usertotal":
                $result = $this->m_common->getUsercompare_list();
                $html = $this->showstat($value,array('resultset'=>$result, 'chart'=>'pie', 'fields'=>'USR_ACCESS~USR_TOTAL', 'legend'=>'Pengguna', 'arrValue'=>array('5'=>'Aktif','6'=>'Tidak Aktif')));
                break;            
            default:
                # code...
                break;
        }
        $value = $html;
        echo $value;
    }

    function gridshow($parameter){
        $this->load->helper('jqxgrid');
        $eventgrid = false;
        foreach ($parameter as $key => $value) {
            ${$key} = $value;
        }
        $param = null;
        if(isset($jenis)){
            $param = "/".$jenis;
        }
        
        $gridname = "jqx" . $name;
        $url ="/master/nosj/get" . $name . "_list".$param;

        $fn_doublc = "
            var selectedrowindex = $('#" . $gridname ."').jqxGrid('getselectedrowindex');
            var idents = $('#" . $gridname. "').jqxGrid('getcellvalue', selectedrowindex,'USL_IDENTS');
            var log_table = $('#" . $gridname. "').jqxGrid('getcellvalue', selectedrowindex,'log_table');
            $('#jqwInformasi').jqxWindow('open');
            var param = {};
            param['IDENTS'] = idents;
            var tinggilayar = $(window).height();
            var lebarlayar = $(window).width()-100;
            if(log_table=='ws'){
                if(tinggilayar>1000){
                    var tinggi = 800;
                }else{
                    var tinggi = tinggilayar-100;
                }
            }else{
                tinggi = 500;
                lebarlayar = 800;
            }
            $.post('/master/userlog/viewlog',param,function(data){
              $('#jqwInformasi').jqxWindow({autoOpen: false,width:lebarlayar, height:tinggi,position:'center, left', resizable:false,title: 'Info Detail'});
              $('#jqwInformasi').jqxWindow('setContent', data);
            });
            $('#jqwInformasi').jqxWindow('focus');

            $('#" . $gridname  . "').jqxGrid('clearselection');
        ";

        $event = array(
          "rowdoubleclick" => $fn_doublc,
        );
        $arrGrid = array('url'=>$url, 
            'gridname'=>$gridname,
            'width'=>'100%',
            'height'=>'90%',
            'col'=>$col,
            'modul'=>$this->modul,
            'showToolbar'=>false,
            'fontsize'=>10,
            'pagesize'=>20,
            'gridpadding'=>'padding:0px 20px 20px 20px',
            'sumber'=>'server',
        );
        $script = null;
        if($name=="Webservice" && $jenis==1){
            $buttonother = array();
			if($this->usrtypusr!=2 || $this->usrlevel==1){
                $buttonother = array(
                    "CSV"=>array('CSV','fa-file-csv','jvCsv()','primary','80')
                );
            }
            $arrGrid = array_merge($arrGrid, array('buttonother'=>$buttonother, 'showToolbar'=>true));
            $script = "
            <script>
            function jvCsv(){
                var param = {};
                param['name'] = '$name';
                param['jenis'] = '$jenis';
                var filter = $('#".$gridname."').jqxGrid('getfilterinformation');
                var sortby = $('#".$gridname."').jqxGrid('getsortinformation');
                var status = 'outstanding';
                var jmlfilter = 0;
                // console.log(filter[1].filter.getfilters());
                for (e = 0; e < filter.length; e++) {
                //   param['FILTER'+e] = filter[e].filtercolumn +'<@>'+filter[e].filter.getfilters()[0].value+'<@>'+filter[e].filter.getfilters()[0].condition;
                    param['FILTER'+e] = '{\"column\":\"' + filter[e].filtercolumn + '\",\"value\":\"'+ filter[e].filter.getfilters()[0].value +'\",\"condition\":\"'+ filter[e].filter.getfilters()[0].condition +'\",\"type\":\"'+ filter[e].filter.getfilters()[0].type +'\"}';
                    jmlfilter++;
                }
                if(sortby.sortcolumn!=undefined){
                  param['SORT'] = sortby.sortcolumn +'<@>'+sortby.sortdirection;  
                }

                if(jmlfilter<2){
                    swal.fire({title:'Untuk export CSV harus menggunakan Filter minimal 2 filter', text:'Jumlah data terlalu banyak', icon:'error'});
                }else{
                    winURL = '/master/userlog/csv';
                    winName = 'ExcelWebservice';
                    var windowoption='resizable=yes,height=600,width=800,location=0,menubar=0,scrollbars=1';
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', winURL);
                    form.setAttribute('target', winName); 
        
                    $('#imgPROSES').show();
                    $('#windowProses').jqxWindow('open');
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
                    form.submit();                 
                    document.body.removeChild(form);
                    $('#windowProses').jqxWindow('close');
                }
            }
            </script>
            ";
        }
        if($eventgrid){
            $arrGrid = array_merge($arrGrid, array('event'=>$event));
        }
        
        $grid = gGrid($arrGrid);
        $grid .= $script;
        return $grid;

    }
    function showstat($from, $parameter){
        $this->load->helper('highchart');
        $arrValue = "";
        $click = "";
        $event = "";
        $title = "";
        foreach ($parameter as $key => $value) {
            ${$key} = $value;
        }
        $flotarea = array(
                'id'    =>  'placeholder' . $from,
                'chart' =>  $chart,
                'xAxistitle'=>'Bulan',
                'yAxistitle'=>'Jumlah Akses',
                'width' =>  "650px",    //Setting a custom width
                'height' => '360px',    //Setting a custom height,
                'legend' => $legend,
                'showvalue'=> 'false',
                'warna'=>'#3466f3',
                'resultset'=>$resultset,
                'fields'=>$fields,
                'click'=> $click,
                'arrValue' => $arrValue,
                'event'=>$event
        );
        if($chart=="pie"){
            unset($flotarea["legend"]);
        }
        // echo "<pre>";
        // print_r($flotarea);
        // die();

        $display = "
                    <div style='padding:5px 5px' id=luar>" . $title . "
                            " . display_highchart($flotarea). "
                    </div>
        ";          
        return $display;
    }    
    function viewlog(){
        $IDENTS = $this->input->post('IDENTS');

        $result = $this->m_common->getAktivitas_edit($IDENTS);

        $arrField = array(
            // "log_idents"=>"",
            "log_address"=>$this->log_address,
            "log_from"=>$this->log_from,
            "log_table"=>$this->log_table,
            "log_field"=>$this->log_field,
            "log_action"=>$this->log_action,
            // "log_result"=>"Hasil",
            "log_usrnam"=>$this->log_usrnam,
            "log_usrdat"=>$this->log_usrdat
        );

        $urutan = 0;
        $ws = false;
        foreach($arrField as $key=>$value){
            ${$key} = $result->$key;
            $label = $value;
            if(${$key}=="ws"){
                $ws = true;
            }
            if($key=="log_action" || $key=="log_result"){
                $type = "udf";
                ${$key."_json"} = json_decode(${$key});
                // debug_array(${$key."_json"});
                $txt = "<b>";
                if($ws){
                    $txt .= $this->lang->line("log_parameterresult"); 
                }else{
                    $txt .= $this->lang->line("log_modification"); 
                }
                $txt .= "</b>";
                $txt .= "<blockquote style='font-size:10pt'>";
                foreach(${$key."_json"} as $keyN=>$valueN){
                    if(is_object($valueN)){
                        // $valueN = "<blockquote style='font-size:10pt'>" . json_encode($valueN) . "</blockquote>";
                        $valuegw = json_encode($valueN);
                        $valuegw = json_decode($valuegw);
                        $valueN = "<blockquote style='font-size:10pt'>"; 
                        foreach($valuegw as $keyY=>$valueY){
                            $nilai = strip_tags($valueY, "-");
                            if($valueY==""){
                                $nilai = "-";
                            }
                            $valueN .= "[" . $keyY . "] : " . $nilai ."<br>";
                        }
                        $valueN .= "</blockquote>";

                    }
                    $txt .= "[" . $keyN . "] = " . $valueN ."<br>";
                }
                $txt .="</blockquote>";
                ${$key} = $txt;
                $ws = false;
            }else{
                $type = "txt";
            }

            $arrTable[] = array('group'=>1, 'urutan'=>$urutan++, 'type'=> $type, 'label'=>$label,'namanya'=> $key,'value'=> ${$key}, 'size'=>450, 'readonly'=>true);
        }
        $arrForm =
                array(
                    'type'=>"view",
                    'arrTable'=>$arrTable,
                    'param' =>$IDENTS,
                    'width' => 710,
                    'modul' => $this->modul,
                    'form_create'=>true,
                    'bentuk'=>'div'
                );
        $content = "<style>

        blockquote {
            display: block;
            border-width: 2px 0;
            border-style: solid;
            border-color: #eee;
            padding: 1.5em 0 1.5em;
            margin: 1.5em 0;
            position: relative;
          }
          blockquote:before {
            position: absolute;
            top: 0em;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            width: 3rem;
            height: 2rem;
            font: 6em/1.08em 'PT Sans', sans-serif;
            color: #666;
            text-align: center;
          }
        </style>";
        $content .= generateForm($arrForm);
        $content .= "<br></br>";
        echo $content;
        // debug_array($result);
    }
    function csv(){
        // $this->common->debug_post();
        $parameter = null;
        foreach($_POST as $key=>$value){
            // if(strpos("AA".$value, "<@>")>0){
            //     $arrValue = explode("<@>", $value);
            //     // $valuenya = null;
            //     // if(isset($arrValue[1])){
            //     //     $valuenya = strtoupper($arrValue[1]);
            //     // }                
            //     // if(isset($arrValue[2])){
            //     //     $valuenya .= "<@>" . $arrValue[2];
            //     // }
            //     $parameter[$arrValue[0]] = $value;//strtoupper($arrValue[1]);
            // }
            $parameter[$key] = $value;
            ${$key} = $value;
        }
        // debug_array($parameter);
        $rslCSV = $this->crud->getWebservice_list($jenis, $parameter, false);
        // $this->common->debug_sql(true);
        $arrModul = array(
            "from"=>"master/userlog/csv", 
            "table_name"=>"-", 
            "username"=>$this->username,
            "log_result"=>1, 
            "keypost"=>"-", 
            "log_action"=>array("parameter"=>$parameter, "action"=> "CSV Webservice")
          );
        $this->common->logmodul(false, $arrModul);
        $arrHeader = array(
            "ID Pengguna", 
            "Nama User", 
            "Alamat User", 
            "Parameter", 
            "Error", 
            "Nomor Pensiun", 
            "Bulan Bayar", 
            "Jenis Bayar", 
            "Akses IP Server", 
            "Waktu Akses", 
        );
        $namaFile = "WebService";
        $arrFields = array(
            "USR_FNAMES", 
            "USL_USRNAM", 
            "USL_ADDRES", 
            "USL_PARAMS", 
            "USL_ERRORS", 
            "USL_NOPENS", 
            "USL_BLNBYR", 
            "USL_JNSBYR", 
            "USL_ADDRES_SERVER", 
            "USL_USRDAT",     
        );

        ini_set('memory_limit', '2096000'); 
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=".$namaFile."");
        header("Content-Transfer-Encoding: binary ");        
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="' . $namaFile . "_" . date('Y_m_d_His'). '.csv"');
        $file = fopen('php://output', 'w');
            
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
}
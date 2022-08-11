<?php
class Common {

	var $langu;
	var $menu_id;
	var $segment2;
	var $segment3;
	var $segment4;
	var $menu_nomors;
	var $userlogins;
	var $deftable;
	var $driver;
	var $prefix;
	var $dbschm;
	var $office_file;
	var $pdf_file;
	var $image_file;
	var $video_file;
	var $all_file;
	var $kadivlayan;
	var $kadivkeu;
	var $kadivpeserta;
  	// ------------------------------------------------------------------------
	/**
	 * Session Constructor
	 */
	function __construct() {
		$this->CI =& get_instance();
		define('BASE_URL', base_url());
		$data['message']  = (empty($message)) ? '' : $message;
		$data['username'] = $this->userlogins;
		$this->hostname = '{outlook.office365.com:993/imap/ssl}INBOX';
		$this->kadivlayan = "KADIV LOLAYAN";
		$this->kadivkeu = "KADIV LOLAKU";
		$this->kadivpeserta = "KADIV PES";

		$this->email_user_dev = $this->CI->config->item('email_user_dev');
		$this->email_password_dev = $this->CI->config->item('email_password_dev');
		$this->email_user_prod = $this->CI->config->item('email_user_prod');
		$this->email_password_prod = $this->CI->config->item('email_password_prod');

		if(ENVIRONMENT=='development'){
			$this->username = $this->email_user_dev;
			$this->password = $this->email_password_dev;
		}else{
			$this->username = $this->email_user_prd;
			$this->password = $this->email_password_prd;
		}

		// $this->debug_array($this->username);
		// $arrDeftable = $this->CI->session->userdata('deftable');
		// $this->driver =  $this->CI->db->dbdriver;
		// $this->database =  $this->CI->db->database;
		include(APPPATH.'config/database'.EXT);
		$this->database =  $db['default']['database'];
		$this->driver =  $db['default']['dbdriver'];
		$this->prefix =  $db['default']['dbprefix'];
		// $this->dbschm =  $db['default']['schema'];
		$this->office_file = 'pdf|PDF|doc|DOC|docx|DOCX|xls|XLS|xlsx|XLSX|ppt|PPT|pptx|PPTX|odt|ODT|ods|ODS|odp|ODP';
		$this->image_file = 'jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF|bmp|BMP';
		$this->pdf_file = 'pdf|PDF';
		$this->all_file = $this->office_file . '|' . $this->image_file;
		$this->video_file = 'swf|SWF|flv|FLV|mp4|MP4|3gp|3GP';
		$this->logo = $this->CI->config->item('logo');
		$this->CI->load->helper('language');
		$this->usrlanguage = $this->CI->session->userdata('USR_LANGUAGE');

		if (!isset($deftable)){
			// $this->getDeftable();
		}
		$this->applic = $this->CI->router->fetch_class();
  	}
	function getDefinition($type, $value){
		if(strpos($value,"(")>0){
			$length = strpos($value,"(");
		}else{
			$length = strlen($value);
		}
		$datatype = substr($value,0, $length);
		$precision = str_replace(")","", str_replace("(", "", substr($value, $length, strlen($value))));
		if($precision==""){
			$precision = 10;
		}				
		$returnf = ${$type};
		return $returnf;
	}
	function extractjsonf($application, $jenis="table"){
		$jsondec = "";
		if($jenis=="table"){
			$info = $this->CI->crud->getDeftable($application);
			$prefix = "TBL";
			$nama = "TBL_TABLES";
		}
		if($jenis=="report"){
			$info = $this->CI->crud->getDefReport($application)->result_array();
			$prefix = "RPT";
			$nama = "RPT_APPLIC";
		}
		// echo $this->CI->db->last_query();;
		if(isset($info[0][$prefix."_DEVLPR"])){
			$jsonval = $info[0][$prefix . "_DEVLPR"];
			$jsondec = array($info[0][$nama]=>json_decode($jsonval));
		}
		// return $jsondec;
		return json_decode(json_encode($jsondec),true);
	}
	function extractjson($table, $column){
		if(substr($table, 0,2)==$this->prefix){
			$table = substr($table, 2, strlen($table)-2);
		}
		// echo substr($table, 0,3);
		// echo $table;
		// die();
		$info = $this->CI->crud->getDeftable(null, $table, false);
		$return = "";
		if(isset($info[0]["TBL_DEVLPR"])){
			// $jsonval = str_replace('`', '"', $info[0]["TBL_DEVLPR"]);
			$jsonval = $info[0]["TBL_DEVLPR"];
			$jsondec = json_decode($jsonval);
			if($jsondec!=""){
				foreach($jsondec as $prop){
					foreach($prop as $key=>$val){
						${$key} = $val;
						if($val==$column){
							$return = $prop;
							break;
						}
					}
				}
			} 
		}
		// return json_decode($return);//
		return json_decode(json_encode($return),true);
		// print_r($return);
	}
	function formatdatedb($return, $field=null, $format=null, $driver=null){
		if($format==""){
			$format = $this->CI->config->item("dateformat");
		}
		$arrMysql = array("YYYY"=>"%Y","YY"=>"%y","MM"=>"%m", "DD"=>"%d", "Mon"=>"%M", "HH24:MI:SS"=>"%T");
		$formatdt = $format;
		$final = "";
		$arrFormat = explode("-", $format);
		$rc = false;
		if($driver==""){
			$driver = $this->driver;
		}
		switch($driver){
			case "mssql":
			case "sqlsrv":
				switch ($format) {
					case 'DD Mon YY':
						$formatdt = 106;
						break;
					case 'DD-MM-YYYY':
						$formatdt = 105;
						break;
					case 'YYYYMMDD':
						$formatdt = 112;
						break;
					case 'YYYY-MM-DD':
						$formatdt = 120;
						break;
					case 'DD Mon YY, HH24:MI:SS':
						$formatdt = 120;
						break;
					default:
						$formatdt = 120;
						break;
				}
				break;	
			case "mysql":
			case "mysqli":
				$formatdt = null;
				$minus = true;
				if(count($arrFormat)==1){
					$minus = false;
					if(strpos($format, ",")>1){
						$format = str_replace(",","", $format);
					}
					$arrFormat = explode(" ", $format);
				}
				// $this->debug_array($arrFormat);
				for($i=0;$i<count($arrFormat);$i++){
					foreach($arrMysql as $key=>$value){
						if($key==$arrFormat[$i]){
							if($minus){
								if ($rc) $formatdt .="-";
							}else{
								if ($rc) $formatdt .=" ";
							}
							$formatdt .= $value;
							$rc=true;
						}
					}
				}
				break;
		}
		// $this->debug_array($formatdt);
		//echo $formatdt;
		switch($driver){
			case "mysql":
			case "mysqli":
				$function = "DATE_FORMAT";
				break;
			case "mssql":
			case "sqlsrv":
				$function = "CONVERT";
				break;
			case "oci8":
			case "postgre" :
				$function = "TO_CHAR";
				break;
		}
		switch ($return){
			case "1" : //format tanggal
				switch($driver){
					case "mysql":
					case "mysqli":
					case "postgre" :
					case "oci8" :
						$final = $function . "(" . $field .", '" . $formatdt . "')";
						break;
					case "sqlsrv" :
					case "mssql" :
						$format = strtoupper($format);
						switch ($format) {
							case 'YYYY':
								$final = "YEAR(" . $field .")";
								break;
							case 'MONTH':
								$final = "DATENAME(MONTH, " . $field .")";
								break;
							case 'DAY':
								$final = "DATENAME(weekday, " . $field .")";
								break;
							case 'MON':
								$final = "LEFT(DATENAME(MONTH, " . $field ."),3)";
								break;
							case 'DD MON YY, HH24:MI:SS':
								$final = "" . $function . "(varchar(30), " . $field ."," . $formatdt . ") ";
								break;
							case 'DD MON YY':
							case 'DD-MM-YYYY':
							case 'YYYYMMDD':
								$final = "" . $function . "(varchar(30), " . $field ."," . $formatdt . ") ";
								break;
							case 'YYYY-MM-DD HH:MM':
								$final = "left(" . $function . "(varchar(30), " . $field ."," . $formatdt . "),16)";
								break;
							default:
								$final = "left(" . $function . "(varchar(30), " . $field ."," . $formatdt . "),10) ";
								break;
						}
						break;
				}
				break;
			case "2" : //function
				$final = $function;
				break;
			case "3" : //function datediff dengan current date
				switch($driver){
					case "mysql":
					case "mysqli":
						$final = "DATEDIFF(".$field.",now())";
						break;
					case "oci8" :
						$final = "(" . $field ." - sysdate)";
						break;
				}
				break;
			case "4" :
				$final = "YEAR(" . $field . ")";
				break;
		}

		return $final;
	}
	function otorisasi($cignit, $check='true'){
		$data = array('grab','login','home','nosj','home','export');
		$ada = false;
		for($x=0;$x<count($data);$x++){
			if(strpos(strtolower($cignit), $data[$x])!==false){
				$ada = true;
				break;
			}
		}
		if($ada){
			$otorisasi = "true";
		}else{
			if($check=='true'){
				$otorisasi = $this->CI->crud->getOtorisasi($cignit);
			}else{
				$otorisasi = $check;
			}
		}
		return $otorisasi;
	}
	function hmenu(){
		ini_set('display_errors', 0);
		$username = $this->CI->session->userdata('USR_LOGINS');
		$levels   = $this->CI->session->userdata('USR_LEVELS');
		// $NEW=1,$SEC=1, $USER=null, $APPLIC=null, $CHANGE=false
		$menuItemsgw = $this->CI->crud->getMenu_json(1,1,null, null, false, true);
		// echo $this->CI->db->last_query();
		$html = '';
		if($menuItemsgw->num_rows()>0){
			$menuItemsxx = $menuItemsgw->result_array();
			$parent = 0;
			$parent_stack = array();
			$children = array();
			$loop = 0;
			foreach ( $menuItemsxx as $item ){
					$children[$item['MNU_PARENT']][] = $item;
					if($item['MNU_PARENT']=="0"){
						$loop++;
					}
			}
			$html .="<script>var totalparent=" . $loop . "</script>";
			$loopmenu = 0;
			while ( ( $option = each( $children[$parent] ) ) || ( $parent > 0 ) ){
		    if ( !empty( $option ) ){
	        // 1) Menu yg mempunyai anak
	        // store current parent in the stack, and update current parent
	        if ( !empty( $children[$option['value']['MNU_IDENTS']] ) ){
				$PARENT = $option['value']['MNU_PARENT'];
				$NOMORS = $option['value']['MNU_NOMORS'];
				$ICONED = $option['value']['MNU_ICONED'];
				$ROUTES = $option['value']['MNU_ROUTES'];
				$CHILDN = $option['value']['MNU_CHILDN'];
				$HVCHLD = $option['value']['MNU_HVCHLD'];
				$RGHT = "";
				$ICON = "";
				$LINK = "#";
				if($CHILDN==0){
					// $style= "style='padding-right:17px'";
				}
				if($PARENT==0){
					if(trim($ICONED)==""){
						$ICONED = "check-circle";
					}
				}
				if(trim($ICONED)!=""){
					$ICON = "<span class='kt-menu__link-icon'><i class=\"fas fa-" . $ICONED. "\" style='padding-right:5px'></i></span>";
				}
				$RGHT = '<span class="arrow"></span>';
				if($CHILDN==0){
					if($PARENT==0){
						$loopmenu++;
						// <li class="kt-menu__item  kt-menu__item--open kt-menu__item--here kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open kt-menu__item--here" data-ktmenu-submenu-toggle="click" aria-haspopup="true">
						$html .= '
						<li class="kt-menu__item kt-menu__item--here kt-menu__item--submenu kt-menu__item--rel" data-ktmenu-submenu-toggle="click" aria-haspopup="true">
							<a  href="javascript:;" class="kt-menu__link kt-menu__toggle">
								'.$ICON.'
								<span class="kt-menu__link-text title0">'.$option['value']['MNU_DESCRE'].'</span>
								<i class="kt-menu__hor-arrow la la-angle-down"></i>
								<i class="kt-menu__ver-arrow la la-angle-right"></i>
							</a>';
						if($HVCHLD!=0){
							$html .= '
							<div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
								<ul class="kt-menu__subnav">
							';
						}
					}else{
						$html .= '<li aria-haspopup="true" class="dropdown-submenu "><a href="javascript:;" class="nav-link nav-toggle ">'.$ICON.$option['value']['MNU_DESCRE'].'<span class="arrow"></span></a>
						';
						$html .= '<ul class="dropdown-menu">
									';	          	
					}
		      	}else{
						// $html .= '<li aria-haspopup="true" class="dropdown-submenu "><a href="javascript:;" class="nav-link nav-toggle " style="width:200px"><span style="padding-left:5px">y'.$ICON.$option['value']['MNU_DESCRE'].'</span><span class="arrow"></span></a>
						// ';
						// $html .= '<ul class="dropdown-menu" style="width:300px">
						// ';
						// <li class="kt-menu__item  kt-menu__item--open kt-menu__item--here kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open kt-menu__item--here kt-menu__item--active" data-ktmenu-submenu-toggle="click" aria-haspopup="true">
							//  onmouseover="console.log($(this).offset());"
					$html .= '
					<li class="kt-menu__item  kt-menu__item--submenu " data-ktmenu-submenu-toggle="hover" aria-haspopup="true">
							<a  href="javascript:;" class="kt-menu__link kt-menu__toggle">
								'.$ICON.'
								<span class="ktmenu_level_0 kt-menu__link-text">'.$option['value']['MNU_DESCRE'].'</span>
								<i class="kt-menu__hor-arrow la la-angle-right"></i>
								<i class="kt-menu__ver-arrow la la-angle-right"></i>
							</a>';
					if($HVCHLD!=0){
						if($loopmenu>7){
							$idmenu = "ktmenusubmenu ";//.$NOMORS;
						}else{
							$idmenu = "";
						}
						$class = "";
						if($CHILDN>=1){
							$countSubMenu = count($children[$option['value']['MNU_IDENTS']]);
							if($countSubMenu>6){
								// $class = ' style="height:200px;overflow:auto"';
							}
							// die();
						}

						$html .= '
						<div  class="ktmenu_level_' . $CHILDN . ' ' . $idmenu . 'kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--right " '.$class.'>
							<ul class="kt-menu__subnav">
						';
					}
				}
				array_push( $parent_stack, $parent );
				$parent = $option['value']['MNU_IDENTS'];
	        }else{// 2) The item yg tidak mempunyai anak
	          $ICONED = $option['value']['MNU_ICONED'];
	          $ROUTES = $option['value']['MNU_ROUTES'];
	          $ICON = "";
	          if(trim($ICONED)!=""){
							$ICON = "<span class='kt-menu__link-icon'><i class=\"fas fa-" . $ICONED. "\" style='padding-right:5px'></i></span>";
	              // $ICON = "<i class=\"fas fa-" . $ICONED. "\"></i>";
	          }
	          $html .= '<li aria-haspopup="true" class="kt-menu__item"><a href="/'.$ROUTES.'" class="kt-menu__link ">'.$ICON . '<span class="kt-menu__link-text">'.$option['value']['MNU_DESCRE'] . '</span></a></li>';
	          // $html .= '<li><a href="#">' . $option['value']['MNU_DESCRE'] . '</a></li>';
	        }
				}
	    // 3) Current parent has no more children:
	    // jump back to the previous menu level
	    	else
	    	{
	        $html .= '</li></ul></div>';
	        $parent = array_pop( $parent_stack );
	    	}
			}
		}
		ini_set('display_errors', 1);
		return $html;
	}
	public function getGenerations($userTree, $currGeneration = 0, &$result = array())
	{
		// print_r($userTree);
		$currGeneration++;
		if (!empty($userTree) && !empty($userTree['child'])) {
			foreach($userTree['child'] as $k => $v) {
				$currUser = $v;
				unset($currUser['child']);
				$result[$currGeneration][$k] = $currUser;
				$this->getGenerations($v, $currGeneration, $result);
			}
		}
		return $result;
	}
	function vmenu_original(){
		ini_set('display_errors', 0);
		$username = $this->CI->session->userdata('USR_LOGINS');
		$levels   = $this->CI->session->userdata('USR_LEVELS');
		$menuItemsgw = $this->CI->crud->getMenu_json();
		$html = '';
		if($menuItemsgw->num_rows()>0){
			$menuItemsxx = $menuItemsgw->result_array();
			$parent = 0;
			$parent_stack = array();
			$children = array();
			foreach ( $menuItemsxx as $item ){
			    $children[$item['MNU_PARENT']][] = $item;
			}
			while ( ( $option = each( $children[$parent] ) ) || ( $parent > 0 ) ){
		    if ( !empty( $option ) )
		    {
	        // 1) Menu yg mempunyai anak
	        // store current parent in the stack, and update current parent
	        if ( !empty( $children[$option['value']['MNU_IDENTS']] ) ){
	          $PARENT = $option['value']['MNU_PARENT'];
	          $ICONED = $option['value']['MNU_ICONED'];
	          $ROUTES = $option['value']['MNU_ROUTES'];
	          $RGHT = "";
	          $ICON = "";
	          $LINK = "#";
	          if(trim($ICONED)!=""){
	              $ICON = "<i class=\"fas fa-" . $ICONED. "\"></i>";
	          }
	          // if($PARENT==0){
	              // $RGHT = '<span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>';
	              $RGHT = '<span class="arrow"></span>';
	          // }

	          $html .= '<li class="nav-item  "><a href="'.$ROUTES.'"  class="nav-link nav-toggle">'.$ICON . '<span class="title">'.$option['value']['MNU_DESCRE'] . '</span>'.$RGHT.'</a>';
	          $html .= '<ul class="sub-menu">';
	          array_push( $parent_stack, $parent );
	          $parent = $option['value']['MNU_IDENTS'];
	        }else{// 2) The item yg tidak mempunyai anak
	          $ICONED = $option['value']['MNU_ICONED'];
	          $ROUTES = $option['value']['MNU_ROUTES'];
	          $ICON = "";
	          if(trim($ICONED)!=""){
	              $ICON = "<i class=\"fas fa-" . $ICONED. "\"></i>";
	          }
	          $html .= '<li class="nav-item  "><a href="/'.$ROUTES.'" class="nav-link ">'.$ICON . '<span class="title">'.$option['value']['MNU_DESCRE'] . '</span></a>';
	          // $html .= '<li><a href="#">' . $option['value']['MNU_DESCRE'] . '</a></li>';
	        }
	    	}
	    // 3) Current parent has no more children:
	    // jump back to the previous menu level
	    	else
	    	{
	        $html .= '</ul></li>';
	        $parent = array_pop( $parent_stack );
	    	}
			}
		}
		ini_set('display_errors', 1);
		return $html;
	}
	function getDeftable(){
		$this->CI->load->model('crud');
		//$carrDeftable = count($arrDeftable);
		$rslDeftable = $this->CI->crud->getDeftable();
			//$this->CI->session->set_userdata('deftable',$rslDeftable);
		//}
		foreach ($rslDeftable as $row){
			$tblAPPLIC = $row['TBL_APPLIC']; //Modul Code
			$tblTABLES = $row['TBL_TABLES']; //Modul Code
			$tblNOTESS = $row['TBL_NOTESS']; //Modul Code
			$tblDESCRE = $row['TBL_DESCRE']; //Modul Code
			$this->deftable[$tblAPPLIC]= array($tblTABLES, $tblDESCRE);
		}
	}
	function is_file_exists($url){
		if (@file_get_contents($url, 0, NULL, 0, 1)){
		return true;
		}else{
			return false;
		}
  	}
  	function deletefile($parameter){
    	$this->CI->load->library('ftp');
		// load konfigurasi ftp
		$remAddres = $this->CI->config->item('remAddres');
		$remUserid = $this->CI->config->item('remUserid');
		$remPasswd = $this->CI->config->item('remPasswd');

		$ftp_config['hostname'] = $remAddres;
		$ftp_config['username'] = $remUserid;
		$ftp_config['password'] = $remPasswd;
		$ftp_config['debug']    = TRUE;
		$response = false;
		if ($this->CI->ftp->connect($ftp_config)) {
			$remoteServer = true;
		} else {
    		$remoteServer = false;
		}

		foreach ($parameter as $indx=>$value){
			${$indx}=$value;
		}
		// echo $remoteServer;
		// die();
		if($remoteServer){
			//informasi file yang udah diunggah
			//File path di server lokal
			//path di ftp server
      		$destination = $path."/".$filename;
			//delete file
			//    echo $destination;
			// die();
			if($this->CI->ftp->delete_file($destination)){
				//tutup koneksi FTP
				$this->CI->ftp->close();
				$response = true;
			}
		}else{
			$response = true;
      		@unlink($source);
		}
		return $response;
  	}
	function mssql_escape($data) {
    	if(is_numeric($data))
      		return $data;
    	$unpacked = unpack('H*hex', $data);
    	return '0x' . $unpacked['hex'];
	}
	function uploadfile($parameter){
		// $this->debug_array($pass);
		//Load codeigniter FTP class
    	$this->CI->load->library('ftp');

		// load konfigurasi ftp
		$remAddres = $this->CI->config->item('remAddres');
		$remUserid = $this->CI->config->item('remUserid');
		$remPasswd = $this->CI->config->item('remPasswd');

		//FTP configuration
		$ftp_config['hostname'] = $remAddres;
		$ftp_config['username'] = $remUserid;
		$ftp_config['password'] = $remPasswd;
		$ftp_config['debug']    = FALSE;

		if ($this->CI->ftp->connect($ftp_config)) {
			$remoteServer = true;
		} else {
    		$remoteServer = false;
		}

    	// $remoteServer = false;

		$path = '/assets/documents';
		$allowed_types = $this->office_file;
		$max_size = 10000000;

		$overwrite = true;
		$response = true;
		$multiple = false;
		$redirect = "";
		$unlink = true;
		$typedoc = "all";
		$serverlocation = $_SERVER["DOCUMENT_ROOT"];

		foreach ($parameter as $indx=>$value){
			${$indx}=$value;
		}
		switch ($typedoc) {
			case 'pdf':
				$allowed_types = $this->pdf_file;
				break;
			case 'office':
				$allowed_types = $this->office_file;
				break;
			case 'image':
				$allowed_types = $this->image_file;
				break;
			case 'all':
				$allowed_types = $this->all_file;
				break;
			default:
				$allowed_types = $this->office_file;
				break;
		}
		$folder =  $serverlocation . "/" . $path ."/";

		$config['upload_path'] = $folder;//$serverlocation .$path;
		$config['allowed_types'] = $allowed_types;
		$config['max_size']	= $max_size;
		$config['overwrite'] = TRUE;
		$this->CI->load->library('upload');
		$this->CI->upload->initialize($config,TRUE);
		if(!$multiple){
			$fileupload = $_FILES[$field]['name'];
			$fileerror = $_FILES[$field]['error'];
		}else{
			$fileupload = $filess[$field]['name'][$loop];
			$fileerror = $filess[$field]['error'][$loop];
			if($fileerror!=4){
				$_FILES[$field]['name']= $fileupload;
				$_FILES[$field]['type']= $filess[$field]['type'][$loop];
				$_FILES[$field]['tmp_name']= $filess[$field]['tmp_name'][$loop];
				$_FILES[$field]['error']= $filess[$field]['error'][$loop];
				$_FILES[$field]['size']= $filess[$field]['size'][$loop];
			}
		}

		if($fileerror!=4){
			if (!$this->CI->upload->do_upload($field))
			{
				$error = $this->CI->upload->display_errors('\n','\n');
				$error = "Kesalahan Unggah Berkas,".$error."(" . $fileupload.")";
				$this->message_save('save_gagal', $error, $redirect);
				$response = false;
			}
			else
			{
				$data = $this->CI->upload->data();
				$timestamp = str_replace(":","", str_replace(" ", "", str_replace("-", "", date('Y-m-d H:i s'))));
				//Rename nama file yang baru saja diupload, rename dengan menghilangkan spasi
				$ext = pathinfo($fileupload, PATHINFO_EXTENSION);
				$fileupload = trim(substr($fileupload, 0, strlen($fileupload)-(strlen($ext)+1)));
				$fileupload = $timestamp. "_" . preg_replace("/[\W_]+/", "_", $fileupload) . "." . $ext;
				// $fileupload = $timestamp. "_" . str_replace(",","", str_replace(" ", "_", $fileupload));
				rename($data['full_path'], $data['file_path'].$fileupload);
				$response  = $fileupload;
				/*
				//////////////upload to ftp server
				//File path at local server
				$source = $data['file_path'].$fileupload;
				//Load codeigniter FTP class
				$this->CI->load->library('ftp');
				//FTP configuration
				$ftphostname = $this->CI->config->item('ftphostname');
				$ftpusernama = $this->CI->config->item('ftpusernama');
				$ftppassword = $this->CI->config->item('ftppassword');
				$ftp_config['hostname'] = $ftphostname;//'ftp.example.com';
				$ftp_config['username'] = $ftpusernama;
				$ftp_config['password'] = $ftppassword;
				$ftp_config['debug']    = TRUE;
				//Connect to the remote server
				if(!$this->CI->ftp->connect($ftp_config)){
							$error = "Koneksi ke FTP (".$ftphostname.") gagal!";
							$this->message_save('save_gagal', $error, $redirect);
					$response = false;
				}else{
					//File upload path of remote server
					$destination = '/testftp/' . $fileupload;
					//Upload file to the remote server
					if(!$this->CI->ftp->upload($source, ".".$destination)){
						$response = false;
					}
					//Close FTP connection
					$this->CI->ftp->close();
				}
				//Delete file from local server
				@unlink($source);
				*/
			}
		}
		if($remoteServer){
			if($response){
				//informasi file yang udah diunggah
				//File path di server lokal
				$source = $data['file_path'].$fileupload;

				//$newPath = str_replace('/assets', '', $path);
				//path di ftp server
				$destination = $path."/".$fileupload;//'/assets/'.$fileName;
				// echo $destination;
				// die();
				//upload file
				$this->CI->ftp->upload($source,$destination);

				//tutup koneksi FTP
				$this->CI->ftp->close();

				//hapus file di server lokal
				if($unlink){
					@unlink($source);
				}
			}
		}

		return $response;
	}
	public function swal2($arrParameter){
		$submit = true;
		$formname = "formgw";
		$funscript = "";
		$title = "";
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		if(isset($text)){
			$text = "'" . $text . "'";
		}else{
			$text = 'null';
		}

		$script = "Swal.fire({";
		$script .= " title:'" . $title . "',";
		$script .= " text: " . $text;

		if(isset($type)){
			$script .= ", icon: '".$type."'";
		}
		if(isset($html)){
			$script .= ", html: '".$html."'";
		}		
		if(isset($timer)){
			$script .= ", timer: ".$timer."";
		}
		if(isset($width)){
			$script .= ", width: '".$width."'";
		}
		if(isset($confirm)){
			if($confirm){
				$script .= ", showCancelButton: true";
			}
			if(isset($confirmButtonText)){
				$script .= ", confirmButtonText: '" . $confirmButtonText . "'";
			}
			if(isset($confirmButtonText)){
				$script .= ", cancelButtonText: '" . $cancelButtonText . "'";
			}
			if(!isset($confirmButtonClass)){
				$confirmButtonClass = $this->CI->config->item('confirmButtonColor');
			}
			$script .= ", confirmButtonColor: '".$confirmButtonClass."'";
			if(!isset($cancelButtonClass)){
				$cancelButtonClass = $this->CI->config->item('cancelButtonColor');
			}
			$script .= ", cancelButtonColor: '".$cancelButtonClass."'";
		}
		$script .= "})";

		if(isset($function)){
			// $script .= ".then(result => {" . $function . "});";
			$script .= ".then(result => { if(result.value) {" . $function . "} });";
			// $script .= ", function(){ " . $function . "}";
		}else{
			$submit_func = null;
			if($submit){
				$submit_func = "$('#".$formname."').submit();";
			}
			$script .= ".then(result => { if(result.value) { " . $submit_func . "} });";
			// $script .= ", function(){ }";
		}
		// $script .= ");";

		return $script;
	}
	public function message_save($val, $from=null, $redirect=null, $str=null, $time=3000, $scriptadd=null){
		if($time==""){
			$time = 3000;
		}
		$this->CI->lang->load('common');
		if($str==""){
			if($this->usrlanguage==1){
				$this->CI->lang->load("common", "english");
			}else{
				$this->CI->lang->load("common", "indonesia");
			}
	
			$str = ($this->CI->lang->line($val) == FALSE) ? $val : $this->CI->lang->line($val);
		}

		$previous = $this->CI->config->item('adminpage');
		if(isset($_SERVER['HTTP_REFERER'])) {
		  $previous = $_SERVER['HTTP_REFERER'];
		}
		$body = "<link href=" . base_url(PLUGINS."metronic/css/global/plugins.bundle.css?v=7.0.6") . " rel='stylesheet' type='text/css'/>";
		$body .= "<link href=" . base_url(PLUGINS."metronic/css/style.bundle.css?v=7.0.6") . " rel='stylesheet' type='text/css'/>";
		$body .= '<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>';
		$body .= "<script src='". base_url(PLUGINS."metronic/css/global/plugins.bundle.js?v=7.0.6") . "'></script>";
		$body .= "<script src='". base_url(PLUGINS."metronic/js/scripts.bundle.js?v=7.0.6") . "'></script>";
		$script = $this->swal2(array('title'=>$str ." " . $from,'type'=>'success', 'timer'=>14000));
		if($scriptadd!=""){
			$script .= $scriptadd;
		}
		$relocate = "";
		$timeout = "";
		if($val=="save_gagal"){
			if($redirect!=""){
				$relocate = "self.location.replace('" .$redirect . "');";
			}else{
				$relocate = "history.back();";
			}
			if($time!="0"){
				$timeout = "window.setTimeout(function(){ " . $relocate . "}, " . $time . ");";
			}
		}else{
			if($redirect!=""){
				$relocate = "self.location.replace('" .$redirect . "');";
			}
			$timeout = "window.setTimeout(function(){ " . $relocate . "}, " . $time . ");";
		}
		$body .= "<body onload=\"" . $script. ";".$timeout."\"></body>";
		echo $body;
	}
	function get_post($stop=true){
		$data = $_POST;//$this->input->post();
		$temp = "";
		echo "<pre>";
		foreach($data as $key=>$value){
		$temp .= '
		$'.$key . ' = $this->input->post(\''.$key.'\');';
		}
		echo $temp;
		echo "</pre>";
		if($stop){
			die();
		}
	}
	function debug_post($lanjut=false){
		echo "<pre>";
		print_r($_POST);
		echo "</pre>";
		if(!$lanjut){
			die();
		}
	}
	function debug_get($lanjut=true){
		echo "<pre>";
		print_r($_GET);
		echo "</pre>";
		if(!$lanjut){
			die();
		}
	}
	function debug_array($array, $stop=true){
		echo "<pre>";
		print_r($array);
		echo "</pre>";
		if($stop){
			die();
		}
	}
	function debug_sql($stop=false, $compile=false, $db='db'){
		echo "<pre>";
		if($compile){
			echo $this->CI->{$db}->get_compiled_select();
		}else{
			echo $this->CI->{$db}->last_query();
		}
		echo "</pre>";
		if($stop){
			die();
		}
		// die();
	}
	function toCombo($arrParameter){
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		$data[''] = '';
		foreach($resultset as $row){
			$data[$row->$IDENTS] = $row->$DESCRE;
		}
		return $data;
	}
	function list_bulan($lang=1, $all=false){
		if($lang==2){
			if($all){
				$optBULANS["0"]='Semua';
			}
			$optBULANS["01"]='Januari';
			$optBULANS["02"]='Februari';
			$optBULANS["03"]='Maret';
			$optBULANS["04"]='April';
			$optBULANS["05"]='Mei';
			$optBULANS["06"]='Juni';
			$optBULANS["07"]='Juli';
			$optBULANS["08"]='Agustus';
			$optBULANS["09"]='September';
			$optBULANS["10"]='Oktober';
			$optBULANS["11"]='November';
			$optBULANS["12"]='Desember';
		}else{
			if($all){
				$optBULANS["0"]='All';
			}
			$optBULANS["01"]='January';
			$optBULANS["02"]='February';
			$optBULANS["03"]='March';
			$optBULANS["04"]='April';
			$optBULANS["05"]='May';
			$optBULANS["06"]='June';
			$optBULANS["07"]='July';
			$optBULANS["08"]='August';
			$optBULANS["09"]='September';
			$optBULANS["10"]='October';
			$optBULANS["11"]='November';
			$optBULANS["12"]='December';
		}
		
		return $optBULANS;
	}
	function comboBox($tahun=NULL, $jenis=NULL){
		$arrBulan = $this->list_bulan(1);
		foreach($arrBulan as $row){
			//echo $row['id']."<br>";
			$options[$row['id']] = $row['bulan'];
		}
		if(isset($tahun)){
			$result = $this->CI->crud->grepTahun($tahun);
			$col = 'idk_tahuns';
		}else{
			$result = $this->CI->crud->getTahun($jenis);
			$col = 'ktd_yearss';
		}
		if($result['numrow'] <> 0){
			foreach($result['result'] as $row){
				$optthns[$row[$col]] = $row[$col];
			}
		}else{
			$optthns = array((date("Y")-1) => (date("Y")-1),date("Y") => date("Y"),(date("Y")+1) => (date("Y")+1));
		}
		///
		return "
		<form name='formrpt' method='post' target='_self'>
			<table class='topfilter'>
				<tr><td><b>Filter</b></td><td>:</td>
					<td>".form_dropdown("fltBulan",$options,"","id='fltBulan'")."</td>
					<td>".form_dropdown("fltTahun",$optthns,"","id='fltTahun'")."</td>
					<td><a href='javascript:void(0);' onclick='jvReload();'><img src=/images/img_info.gif></a></td>
				</tr>
			</table>
		</form>
		";
	}
	function rowLock_logout(){
		$userlock = $this->CI->session->userdata('USR_LOGINS');
		$arrTable = array(
			'KBS_QUESTN',
				'KBS_CHKLST',
				'KBS_JADWAL',
				'KBS_CHKLST_DETAIL',
				'KBS_HASILS_AUDITS',
				'KBS_BIBLIO'
			);
		for($i=0;$i<count($arrTable);$i++){
				$this->CI->db->set('LCK_USRNAM', null);
				$this->CI->db->set('LCK_USRDAT', null);
				$this->CI->db->where('LCK_USRNAM', $userlock);
				$this->CI->db->update($arrTable[$i]);
		}
	}
	function transLock($appidnya,$ishapus=0){
			$userlock = $this->CI->session->userdata('USR_LOGINS');
			$sessions = $this->CI->session->userdata('__ci_last_regenerate');
			$iplogins = $this->CI->input->ip_address();
			$modulnya = $this->CI->router->fetch_class();
			$waktunya = date("Y-m-d H:i:s");

			switch ($ishapus) {
				case 0:
					$modess = 'append';
					break;
				case 1:
					$modess = 'delete';
					break;
				default:
					$modess = 'noavail';
					break;
			}


			if($ishapus==1){
				/////////cari ident terkecil untuk mengakomodasi delete dari session yg sama
			$this->CI->db->select('MIN(LOC_IDENTS)LOC_IDENTS');
			$this->CI->db->from('APP_LOCKSS');
				$this->CI->db->where('LOC_GOURLS', $modulnya);
				$this->CI->db->where('LOC_APPIDN', $appidnya);
			$query = $this->CI->db->get();
			if($query->num_rows()!=0){////hapus kalo session sama
				$hasil = $query->row_array();
					$this->CI->db->where('LOC_GOURLS', $modulnya);
					$this->CI->db->where('LOC_APPIDN', $appidnya);
					$this->CI->db->where('LOC_SESSID', $sessions);
					$this->CI->db->where('LOC_IDENTS', $hasil['LOC_IDENTS']);
				$this->CI->db->delete('APP_LOCKSS');
			}
			//////
			}else{
			////cek dulu sedang dalam edit mode apa gak
			$this->CI->db->select('*');
			$this->CI->db->from('APP_LOCKSS');
				$this->CI->db->where('LOC_GOURLS', $modulnya);
				$this->CI->db->where('LOC_APPIDN', $appidnya);
			$query = $this->CI->db->get();
			if($query->num_rows()!=0){
				$hasil = $query->row_array();

				if($sessions == $hasil['LOC_SESSID']){
					////////insert buat gantiin yg mau dihapus
						$this->CI->db->set('LOC_GOURLS', $modulnya);
						$this->CI->db->set('LOC_APPIDN', $appidnya);
						$this->CI->db->set('LOC_IPADDR', $iplogins);
						$this->CI->db->set('LOC_LOGINS', $userlock);
						$this->CI->db->set('LOC_DATESS', $waktunya);
						$this->CI->db->set('LOC_SESSID', $sessions);
						$this->CI->db->insert("APP_LOCKSS");
						///
					$script = "alert('Data sedang di-edit oleh Anda!');";
				}else{
					$script = "alert('Data sedang di-edit oleh ".$hasil['LOC_LOGINS']."!');";
				}
					echo $script;
			}else{
					$this->CI->db->set('LOC_GOURLS', $modulnya);
					$this->CI->db->set('LOC_APPIDN', $appidnya);
					$this->CI->db->set('LOC_IPADDR', $iplogins);
					$this->CI->db->set('LOC_LOGINS', $userlock);
					$this->CI->db->set('LOC_DATESS', $waktunya);
					$this->CI->db->set('LOC_SESSID', $sessions);
					$this->CI->db->insert("APP_LOCKSS");
			}
			}

			// $this->CI->db->set('LOC_GOURLS', $modulnya);
			// $this->CI->db->set('LOC_APPIDN', $appidnya);
			// $this->CI->db->set('LOC_IPADDR', $iplogins);
			// $this->CI->db->set('LOC_LOGINS', $userlock);
			// $this->CI->db->set('LOC_DATESS', $waktunya);
			// $this->CI->db->set('LOC_MODESS', $modess);
			// $this->CI->db->insert("APP_LOCKSS");

	}
	function rowLock($arrParameter){
			$userlock = $this->CI->session->userdata('USR_LOGINS');
			$datelock = date('Y-m-d H:i:s');
			$appslock = $this->CI->config->item('app_lock');
			$return = false; // return true berarti ada yang make
			$updatedb = false;

			foreach ($arrParameter as $detail=>$valueAwal){
				${$detail} = $valueAwal;
			}
		if($USRNAM==""){
			$updatedb = true;
		}else{
			if($USRNAM!=$userlock){
				$timelock = strtotime($datelock);
				$usrdlock = strtotime($USRDAT);
				$selisih = round($timelock - $usrdlock)/60;
				if($selisih>$appslock){
					$updatedb = true;
				}else{
					$updatedb = false;
					$return = true;
				}
			}else{
				$updatedb = true;
			}
		}
		if($updatedb){
				$this->CI->db->set('LCK_USRNAM', $userlock);
				$this->CI->db->set('LCK_USRDAT', $datelock);
				$this->CI->db->where($pk);
				$this->CI->db->update($table);
				$return = false;
		}
		if($return){
			echo "<script>alert('Data sedang digunakan oleh : " . $USRNAM . " pada ". $USRDAT . "\\nAnda tidak bisa melakukan perubahan!')</script>";
		}
		return $return;
	}
	function decrypt($string){
		$StringToDecrypt;
		$StringToDecrypt=$string;
		$Decrypt="";
		while ($StringToDecrypt<>"")
		{
			$CharPos = substr($StringToDecrypt,0,1);
			$lenDec = strlen($StringToDecrypt);
			$StringToDecrypt = substr($StringToDecrypt, 1, $lenDec);
			$CharCode = substr($StringToDecrypt, 0, $CharPos);
			$lenDec2 = strlen($StringToDecrypt);
			$StringToDecrypt = substr($StringToDecrypt, strlen($CharCode) , $lenDec2);
			$Decrypt = $Decrypt . Chr($CharCode);
		}
		return  $Decrypt ;
	}
	function encrypt($string){
		$encrypt = "";
		$length = strlen($string);
		$length_code;
		$chrCode;
		$chr;

		for ($i=0; $i<$length; $i++)
		{
			$chr = substr($string,$i,1);
			$chrCode = ord($chr);
			$length_code = strlen($chrCode);

			$encrypt .= $length_code . $chrCode;
		}
		return $encrypt;
	}
	function dateformat($str,$ff="Y-m-d"){
		$time = strtotime($str);
		$newdate = date($ff,$time);
		return $newdate;
	}
	function numberformat($str){
		if(is_numeric($str)){
			$ArrVal = explode(".",$str);
			if(isset($ArrVal[1])) {
				$len = strlen($ArrVal[1]);
				$return = number_format($str,$len);
			} else {
				$return = number_format($str);
			}
		}else{
			$return = "NaN";
		}

		return $return;
	}
	function terbilang($angka=0){
		$angka = (float)$angka;
		$bilangan = array(
				'',
				'satu',
				'dua',
				'tiga',
				'empat',
				'lima',
				'enam',
				'tujuh',
				'delapan',
				'sembilan',
				'sepuluh',
				'sebelas'
		);

		if ($angka < 12) {
			return $bilangan[$angka];
		} else if ($angka < 20) {
			return $bilangan[$angka - 10] . ' belas';
		} else if ($angka < 100) {
			$hasil_bagi = (int)($angka / 10);
			$hasil_mod = $angka % 10;
			return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
		} else if ($angka < 200) {
			return sprintf('seratus %s', $this->terbilang($angka - 100));
		} else if ($angka < 1000) {
			$hasil_bagi = (int)($angka / 100);
			$hasil_mod = $angka % 100;
			return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], $this->terbilang($hasil_mod)));
		} else if ($angka < 2000) {
			return trim(sprintf('seribu %s', $this->terbilang($angka - 1000)));
		} else if ($angka < 1000000) {
			$hasil_bagi = (int)($angka / 1000); // karena hasilnya bisa ratusan jadi langsung digunakan rekursif
			$hasil_mod = $angka % 1000;
			return sprintf('%s ribu %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod));
		} else if ($angka < 1000000000) {
			// hasil bagi bisa satuan, belasan, ratusan jadi langsung kita gunakan rekursif
			$hasil_bagi = (int)($angka / 1000000);
			$hasil_mod = $angka % 1000000;
			return trim(sprintf('%s juta %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
		} else if ($angka < 1000000000000) {
			// bilangan 'milyaran'
			$hasil_bagi = (int)($angka / 1000000000);
			$hasil_mod = fmod($angka, 1000000000);
			return trim(sprintf('%s milyar %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
		} else if ($angka < 1000000000000000) {                          // bilangan 'triliun'
			$hasil_bagi = $angka / 1000000000000;
			$hasil_mod = fmod($angka, 1000000000000);
			return trim(sprintf('%s triliun %s', $this->terbilang($hasil_bagi), $this->terbilang($hasil_mod)));
		} else {
			return 'Nilai Terlalau Banyak !';
		}
	}
	function terbilangTranslate($number) {
		$string = '';
		$suffix = '';
		$max_size = pow(10,18);

		switch ($number)
		{
			// set up some rules for converting digits to words
			case $number < 0:
				$prefix = "negative";
				$suffix = $this->terbilangTranslate(-1*$number);
				$string = $prefix . " " . $suffix;
				break;
			case 1:
				$string = "one";
				break;
			case 2:
				$string = "two";
				break;
			case 3:
				$string = "three";
				break;
			case 4:
				$string = "four";
				break;
			case 5:
				$string = "five";
				break;
			case 6:
				$string = "six";
				break;
			case 7:
				$string = "seven";
				break;
			case 8:
				$string = "eight";
				break;
			case 9:
				$string = "nine";
				break;
			case 10:
				$string = "ten";
				break;
			case 11:
				$string = "eleven";
				break;
			case 12:
				$string = "twelve";
				break;
			case 13:
				$string = "thirteen";
				break;
			// fourteen handled later
			case 15:
				$string = "fifteen";
				break;
			case $number < 20:
				$string = $this->terbilangTranslate($number%10);
				// eighteen only has one "t"
				if ($number == 18)
				{
				$suffix = "een";
				} else
				{
				$suffix = "teen";
				}
				$string .= $suffix;
				break;
			case 20:
				$string = "twenty";
				break;
			case 30:
				$string = "thirty";
				break;
			case 40:
				$string = "forty";
				break;
			case 50:
				$string = "fifty";
				break;
			case 60:
				$string = "sixty";
				break;
			case 70:
				$string = "seventy";
				break;
			case 80:
				$string = "eighty";
				break;
			case 90:
				$string = "ninety";
				break;
			case $number < 100:
				$prefix = $this->terbilangTranslate($number-$number%10);
				$suffix = $this->terbilangTranslate($number%10);
				$string = $prefix . "-" . $suffix;
				break;
			// handles all number 100 to 999
			case $number < pow(10,3):
				// floor return a float not an integer
				$prefix = $this->terbilangTranslate(intval(floor($number/pow(10,2)))) . " hundred";
				if ($number%pow(10,2)) $suffix = " and " . $this->terbilangTranslate($number%pow(10,2));
				$string = $prefix . $suffix;
				break;
			case $number < pow(10,6):
				// floor return a float not an integer
				$prefix = $this->terbilangTranslate(intval(floor($number/pow(10,3)))) . " thousand";
				if ($number%pow(10,3)) $suffix = $this->terbilangTranslate($number%pow(10,3));
				$string = $prefix . " " . $suffix;
				break;
			case $number < pow(10,9):
				// floor return a float not an integer
				$prefix = $this->terbilangTranslate(intval(floor($number/pow(10,6)))) . " million";
				if ($number%pow(10,6)) $suffix = $this->terbilangTranslate($number%pow(10,6));
				$string = $prefix . " " . $suffix;
				break;
			case $number < pow(10,12):
				// floor return a float not an integer
				$prefix = $this->terbilangTranslate(intval(floor($number/pow(10,9)))) . " billion";
				if ($number%pow(10,9)) $suffix = $this->terbilangTranslate($number%pow(10,9));
				$string = $prefix . " " . $suffix;
				break;
			case $number < pow(10,15):
				// floor return a float not an integer
				$prefix = $this->terbilangTranslate(intval(floor($number/pow(10,12)))) . " trillion";
				if ($number%pow(10,12)) $suffix = $this->terbilangTranslate($number%pow(10,12));
				$string = $prefix . " " . $suffix;
				break;
			// Be careful not to pass default formatted numbers in the quadrillions+ into this function
			// Default formatting is float and causes errors
			case $number < pow(10,18):
				// floor return a float not an integer
				$prefix = $this->terbilangTranslate(intval(floor($number/pow(10,15)))) . " quadrillion";
				if ($number%pow(10,15)) $suffix = $this->terbilangTranslate($number%pow(10,15));
				$string = $prefix . " " . $suffix;
				break;
		}

		return $string;
	}
	function cekLogin($user='',$password='',$bs='EMP_NOMORS',$cnf=null){
		if($user == '' OR $password == '') {
			return false;
		}

		$query = $this->CI->crud->login($user);

		if ($query->num_rows()==1) {
			$row = $query->row_array();
			//check huruf user, kapital ato nggak
			if(md5($row['USR_LOGINS'])!=md5($user)){
				return false;
			}

			if($row['USR_ACCESS'] != 1){
				return false;
			}

			if($row['USR_LEVELS'] != 1){
				if($this->CI->config->item('site_access')!=null){
					if($row[$this->CI->config->item('site_access')] == 0){
						return false;
					}
				}else{///defaultnya punya SMS
					if($row['USR_SMSAPP'] != 1){
						return false;
					}
				}
			}

			if(isset($cnf)){
				if(is_array($cnf)){
					if(!in_array($row[$bs], $cnf)){
						return false;
					}
				}else{
					if($row[$bs] <> $cnf){
						return false;
					}
				}
			}

			$dbPassword = $this->CI->common->decrypt($row['USR_PASSWD']);

			if($password ==  $dbPassword) {
				return true;
			}else{
				return false;
			}
		}	else {
			return false;
		}

	}
	// ======= image/files upload related
	function divImage($parameter){
		$file="";
		$divfile = "filegw";
		$url="";
		$funadd = "jvAddImages";
		$fundel = "jvHapusimage";

		if($parameter!=""){
			foreach ($parameter as $detail=>$valueAwal){
				${$detail} = $valueAwal;
			}
		}
		if(!isset($addImage)){
			$addImage = "
				function ".$funadd."(elementnya)
				{
					var newrow = \"\";
					newrow += \"<div><input type=file name=\"+elementnya+\"[] size=25></div>\";
					$('#".$divfile."').append(newrow);
				}
			";
		}

		if(!isset($delImage)){
			$delImage = "
			function jvHapusimage(id,idents){
				var param = {};
				param['IDENTS'] = id;
				$.post('/".$url."/getImage_delete/',param,function(data){
				if(data){
				alert('Bukti ' + data + '!');
					$.post('/".$url."/getImage_data/'+idents+'/refresh', function(rebound){
						$('#thumbnailList1').remove();
								$('#xxx').append(rebound);
				});
				}
			});
		}
		";
		}
		$divImage = "
		<script>
		".$addImage."
		".$delImage."
		</script>
		";
		$divImage .= "<div id=".$divfile." style='float:left;padding-left:10px'>" . $input . "</div><div style='float:left'><a href=javascript:".$funadd."('".$file."')><li class='fas fa-plus-circle'></li></a></div>";
		return $divImage;
	}
	function showimage($arrParameter, $source="view"){
		$this->CI->load->library('ftp');

		//load konfigurasi ftp
		$remAddres = $this->CI->config->item('remAddres');
		$remUserid = $this->CI->config->item('remUserid');
		$remPasswd = $this->CI->config->item('remPasswd');

		//FTP configuration
		$ftp_config['hostname'] = $remAddres;
		$ftp_config['username'] = $remUserid;
		$ftp_config['password'] = $remPasswd;
		$ftp_config['debug']    = FALSE;

		//   if($this->CI->ftp->connect($ftp_config)) {
		//   	$remoteServer = ASSETS;
			// }else{
			// $remoteServer = "";
			// }
		$readonly = false;
		$single = false;
		$id = "thumbnailList1";
		$path = "/assets/vendor/";
		$arrJenis = array(".pdf"=>"ico_pdf.png", ".doc"=>"ico_word.png", ".xls"=>"ico_excel.png", ".ppt"=>"ico_powerpoint.png");
		$fldIMAGES = 'ATT_ATTCHM';
		$fldIDENT1 = 'ATT_ATTCHM';
		$fldIDENT2 = 'ATT_ATTCHM';
		$fundel = "jvHapusimage";
		$caption = "";
		if(is_array($arrParameter)){
			foreach ($arrParameter as $detail=>$valueAwal){
				${$detail} = $valueAwal;
			}
		}else{
	    $data = $_POST;
	    foreach($data as $key=>$value){
	    	${$key}=$value;
	    }
		}
		$thumbnail = "<div id='".$id."'>";
		$img = "";
		$pathoriginal = $path;
		$loop =1;

		if(!$single){
	    if(!isset($resultset)){
		    $this->CI->load->model($model);
	    	$resultset = $this->CI->$model->$function('grid', $idents);
	    }
			foreach ($resultset->result() as $key => $value) {
				$path = $pathoriginal;
				$imagess = $value->$fldIMAGES;
				$filename = $imagess;
				if(!$readonly){
					$caption = "<a href='javascript:".$fundel."(" . $value->$fldIDENT1 . ", " . $value->$fldIDENT2 . ")'>Hapus</a>";
				}
				foreach ($arrJenis as $keyimage => $valueimage) {
					if(strpos("XX" . strtoupper($imagess), strtoupper($keyimage))>1){
						if($caption!=""){
							$caption .= " | ";
						}
						$arrFILESS[$loop] = substr($imagess, strpos($imagess, "_")+1, strlen($imagess));
						$caption .= "<a href='" .ASSETS.$path."/".$imagess."' target=_blank>Lihat</a>";
						$imagess = $valueimage;
						$path = IMAGES;
						break;
					}
				}
				$imagess = ASSETS.$path.'/'.$imagess;
				$thumbnail .= "<a id='img".$loop."' class=\"imgBukti\" href='".$imagess."' data-title=\"".$caption."\">";
				$thumbnail .= "<img src='" . $imagess . "'>";
				$thumbnail .= "</a>";
				$imagess = $path.'/'.$imagess;
				$caption ="";
				$loop++;
			}
		}else{
			$imagess = $fldIMAGES;
			$filename = $imagess;

			if(!$readonly){
				$caption = "<a href='javascript:".$fundel."(" . $fldIDENT1 . ")''>Hapus</a>";
			}
			foreach ($arrJenis as $keyimage => $valueimage) {
				if(strpos("XX" . strtoupper($imagess), strtoupper($keyimage))>1){
					if($caption!=""){
						$caption .= " | ";
					}
					$arrFILESS[$loop] = substr($imagess, strpos($imagess, "_")+1, strlen($imagess));
					$caption .= "<a href='".ASSETS.$path."/".$imagess."' target=_blank>Lihat</a>";
					$imagess = $valueimage;
					$path = IMAGES;
					break;
				}
			}
			$imagess = $path .'/'. $imagess;
			$thumbnail .= "<a id='img".$loop."' class=\"imgBukti\" href='".$imagess."' data-title=\"".$caption."\">";
			$thumbnail .= "<img src='" . $imagess . "'>";
			$thumbnail .= "</a>";
			$caption ="";
		}

		if(isset($arrFILESS)){
			$thumbnail .= "<script>
			$(document).ready(function(){
			";
			foreach ($arrFILESS as $key => $value) {
				$thumbnail .= "$('#img".$key."').jqxTooltip({ content: '<b>Nama File:</b> <i>".$value."</i>', position: 'mouse', name: 'movieTooltip'});";
			}
			$thumbnail .= "});
			</script>	";
		}

		$thumbnail .= "</div>";
		$thumbnail .= scrThumbnail(array('id'=>$id));
    	$thumbnail .= scrImages(array('class'=>'imgBukti'));
		if($source=="view"){
			return $thumbnail;
		}else{
			echo $thumbnail;
		}
  	}
	function chkMEMOSS($arrUNIORG=null){
		// $this->CI->load->model('m_master');
		$UNIORG = 0;
		$DEPTMN = $this->CI->session->userdata("EMP_DEPTMN");
		$DVSION = $this->CI->session->userdata("EMP_DVSION");
		$SCTION = $this->CI->session->userdata("EMP_SCTION");
		$LEADER = $this->CI->session->userdata("STR_LEADER")==1 ? true : false;
		$MANGR = false;
		$KADIV = false;
		$UNIORG = $SCTION;
		if($SCTION==0 && $DVSION==0){
			if($LEADER){
				$MANGR = true;
			}
			$UNIORG = $DEPTMN;
		}
			if($SCTION==0 && $DVSION!=0){
			if($LEADER){
				$KADIV = true;
			}
			$UNIORG = $DVSION;
			}
		$arrDEPMMO = $this->CI->config->item('dep_memo');
		$rsltUNIORG = array();
		if(!isset($arrUNIORG)){
			$arrUNIORG = $this->chkUNIORG($UNIORG, $LEADER);
		}
		for($e=0;$e<count($arrUNIORG);$e++){
				if(array_search($arrUNIORG[$e], $arrDEPMMO)!==FALSE){
					$rsltUNIORG[] = $arrUNIORG[$e];
					break;
				}
		}
			return $rsltUNIORG;
	}
	function chkUNIORG($UNIORG=null, $LEADER=true){
		if(is_array($UNIORG)){
				foreach ($UNIORG as $keyUNIORG=>$valUNIORG){
					${$keyUNIORG} = $valUNIORG;
				}
				$LEADER = ($LEADER ==1) ? true : false;
			if($SCTION!=0){
				if($LEADER){
					$MANGR = true;
				}
				$UNIORG = $SCTION;
			}
			if($SCTION==0 && $DVSION==0){
				if($LEADER){
					$MANGR = true;
				}
				$UNIORG = $DEPTMN;
			}
				if($SCTION==0 && $DVSION!=0){
				if($LEADER){
					$KADIV = true;
				}
				$UNIORG = $DVSION;
				}
		}
		$arrUNIORG = array();
		if($LEADER){
			$rslDEPTMN = $this->CI->crud->getDepartement(1);
			$rslDPRTMN = $rslDEPTMN['records'];
			$rslDPRTMN = json_decode(json_encode($rslDPRTMN),true);
			foreach ($rslDPRTMN as $key => $value) {
				$DPR_CDATAS = $value["DPR_CDATAS"];
				$DPR_CODESS = $value['DPR_CODESS'];
				$DPR_DESCRE = $value['DPR_DESCRE'];

				if($DPR_CDATAS==$UNIORG){
					$index = array_search($key, array_keys($rslDPRTMN));
					unset($rslDPRTMN[$index]);
					$arrUNIORG[]= $DPR_CODESS;// . ">>P<<" . $DPR_CDATAS . ".1." . $DPR_DESCRE;
					foreach ($rslDPRTMN as $key1 => $value1) {
						$DPR_CDATAS1 = $value1["DPR_CDATAS"];
						$DPR_CODESS1 = $value1["DPR_CODESS"];
						$DPR_DESCRE1 = $value1['DPR_DESCRE'];
						if($DPR_CDATAS1==$DPR_CODESS){
							$arrUNIORG[]=$DPR_CODESS1;// . ">>P<<" . $DPR_CDATAS1 . ".2." . $DPR_DESCRE1;
							foreach ($rslDPRTMN as $key2 => $value2) {
								$DPR_CDATAS2 = $value2["DPR_CDATAS"];
								$DPR_CODESS2 = $value2["DPR_CODESS"];
								$DPR_DESCRE2 = $value2['DPR_DESCRE'];
								if($DPR_CDATAS2==$DPR_CODESS1){
									$arrUNIORG[]=$DPR_CODESS2;// . ">>P<<" . $DPR_CDATAS2 . ".3." . $DPR_DESCRE2;
									foreach ($rslDPRTMN as $key3 => $value3) {
										$DPR_CDATAS3 = $value3["DPR_CDATAS"];
										$DPR_CODESS3 = $value3["DPR_CODESS"];
										$DPR_DESCRE3 = $value3['DPR_DESCRE'];
										if($DPR_CDATAS3==$DPR_CODESS2){
											$arrUNIORG[]=$DPR_CODESS3;// . ">>P<<" . $DPR_CDATAS3 . ".4." . $DPR_DESCRE3;
											}
										}
									}
							}
						}
					}
				}
				if($DPR_CODESS==$UNIORG){
					$index = array_search($key, array_keys($rslDPRTMN));
					$arrUNIORG[]=$DPR_CODESS;//$DPR_DESCRE;
				}
			}
		}else{
			$arrUNIORG = array($UNIORG);
			// $DEPTMN = $arrUNIORG['DEPTMN'];
			// $DVSION = $arrUNIORG['DVSION'];
			// $SCTION = $arrUNIORG['SCTION'];
			// if($SCTION!=""){
			// 	$arrUNIORG = array($SCTION);
			// }else{
			// 	if($DVSION!=""){
			// 		$arrUNIORG = array($DVSION);
			// 	}else{
			// 		if($DEPTMN!=""){
			// 			$arrUNIORG = array($DEPTMN);
			// 		}
			// 	}
			// }
			// $arrUNIORG = $arrUNIORG;
		}
		return $arrUNIORG;
	}
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
		$sort_col[$key] = $row[$col];
		}
		array_multisort($sort_col, $dir, $arr);
	}
	function bersih($string, $spasi=null) {
		if(isset($spasi)){
			$string = str_replace(' ', $spasi, $string);
		}

		$string = preg_replace('/[^A-Za-z0-9\s\-]/', '', $string);
		if(isset($spasi)){
			$string = preg_replace('/-+/', $spasi, $string);
		}else{
			$string = $string;
		}
		return $string;// // Replaces multiple hyphens with single one.
  	}
	function searchArray($array, $key, $value){
		$results = array();
		if(is_array($array)){
			if(isset($array[$key])){
				if ( $array[$key] == $value ){
					$results[] = $array;
				}
			}else {
			foreach ($array as $subarray)
			$results = array_merge( $results, $this->searchArray($subarray, $key, $value) );
			}
		}
		return $results;
	}
	function getUNIORG($UNIORG, $level=0, $DOWN=true, $SELF=false){
		if($level==0){
			$level=99;
		}
		$rslDEPTMN = $this->CI->crud->getDepartement(1);
	  	$rslDEPTMN = $rslDEPTMN['records'];
	  	$rslDEPTMN = json_decode(json_encode($rslDEPTMN),true);
	  	$PARENT = "OKS";
		$yatim = true;
		$loop=0;
		$DEPTMN = array();

		if(!$DOWN){
			while($yatim){
				if($PARENT=="OKS"){
					$PARENT = $UNIORG;
				}
				$arrPARENT = $this->searchArray($rslDEPTMN, 'DPR_CODESS', $PARENT);
				$arrDEPTMN = array();
				if($SELF){
					$DEPTMN[] = $arrPARENT;
					$SELF = false;
				}
				if(isset($arrPARENT[0]['DPR_CDATAS'])){
					$PARENT = $arrPARENT[0]['DPR_CDATAS'];
					$arrDEPTMN = $this->searchArray($rslDEPTMN, 'DPR_CODESS', $PARENT);
				}

				if(count($arrDEPTMN)>0){
					$DEPTMN[] = $arrDEPTMN;
				}else{
					$yatim=false;
				}
				$loop++;
			}
		}else{
			while($yatim){
				if(is_array($UNIORG)){
					foreach ($UNIORG as $keyP => $valueP) {
						$arrDEPTMN = $this->searchArray($rslDEPTMN, 'DPR_CDATAS', $valueP['DPR_CODESS']);
						if(count($arrDEPTMN)>0){
							if($loop<$level){
								$nguik[] = $arrDEPTMN;
								foreach ($arrDEPTMN as $keyX => $valueX) {
									$UNIORG[] = array('DPR_CODESS'=>$valueX['DPR_CODESS'], 'DPR_DESCRE'=>$valueX['DPR_DESCRE'], 'DPR_PARENT'=>$valueX['DPR_CDATAS'], 'DPR_LEVELS'=>$valueX['DPR_LEVELS']);
								}
							}else{
								$yatim = false;
							}
						}else{
							$yatim = false;
						}
					}
				}else{
					$arrDEPTMN = $this->searchArray($rslDEPTMN, 'DPR_CDATAS', $UNIORG);
					$UNIORG = array();
					if($SELF){
						$UNIORG[] = $this->searchArray($rslDEPTMN, 'DPR_CODESS', $UNIORG);
						$SELF = false;
					}

					if(count($arrDEPTMN)>0){
						$nguik = $arrDEPTMN;
						if($loop<$level){
							foreach ($nguik as $key => $value) {
								$UNIORG[] = array('DPR_CODESS'=>$value['DPR_CODESS'], 'DPR_DESCRE'=>$value['DPR_DESCRE'], 'DPR_PARENT'=>$value['DPR_CDATAS'], 'DPR_LEVELS'=>$value['DPR_LEVELS']);
							}
						}
					}else{
						$yatim = false;
					}
				}
			}
			$DEPTMN = $UNIORG;
		}
		return $DEPTMN;
	}
	function getSTRUKTUR($PSTION, $level=0, $DOWN=true, $SELF=false){
		if($level==0){
			$level=99;
		}

	  	$rslSTRKTR = $this->CI->crud->getPosisi(1);
	  	$rslSTRKTR = $rslSTRKTR['records'];
	  	$rslSTRKTR = json_decode(json_encode($rslSTRKTR),true);
		$yatim = true;
		$i;
		$loop=0;
		$PARENT = "OKS";

		if(!$DOWN){
			while($yatim){
				if($PARENT=="OKS"){
					$PARENT = $PSTION;
				}
				$arrPARENT = $this->searchArray($rslSTRKTR, 'STR_CODESS', $PARENT);
				$arrSTRKTR = array();
				if($SELF){
					$STRKTR[] = $arrPARENT;
					$SELF = false;
				}
				if(isset($arrPARENT[0]['STR_CDATAS'])){
					$PARENT = $arrPARENT[0]['STR_CDATAS'];
					$arrSTRKTR = $this->searchArray($rslSTRKTR, 'STR_CODESS', $PARENT);
				}

				if(count($arrSTRKTR)>0){
					$STRKTR[] = $arrSTRKTR;
				}else{
					$yatim=false;
				}
				$loop++;
			}
		}else{
			while($yatim){
				if(is_array($PSTION)){
					foreach ($PSTION as $keyP => $valueP) {
						// $arrSTRKTR = $this->searchArray($rslSTRKTR, 'STR_CDATAS', $valueP);
						$arrSTRKTR = $this->searchArray($rslSTRKTR, 'STR_CDATAS', $valueP['STR_CODESS']);
						if(count($arrSTRKTR)>0){
							if($loop<$level){
								$nguik[] = $arrSTRKTR;
								foreach ($arrSTRKTR as $keyX => $valueX) {
									$PSTION[] = array('STR_CODESS'=>$valueX['STR_CODESS'], 'STR_PSTION'=>$valueX['STR_PSTION'], 'STR_PARENT'=>$valueX['STR_CDATAS']);
								}
							}else{
								$yatim = false;
							}
						}else{
							$yatim = false;
						}
					}
				}else{
					$arrSTRKTR = $this->searchArray($rslSTRKTR, 'STR_CDATAS', $PSTION);
					$PSTION = array();
					if(count($arrSTRKTR)>0){
						$nguik = $arrSTRKTR;
						if($loop<$level){
							foreach ($nguik as $key => $value) {
								$PSTION[] = array('STR_CODESS'=>$value['STR_CODESS'], 'STR_PSTION'=>$value['STR_PSTION'], 'STR_PARENT'=>$value['STR_CDATAS']);
							}
						}
					}else{
						$yatim = false;
					}
				}
				$loop++;
			}
			$STRKTR = $PSTION;
		}
		return $STRKTR;
	}
  	// =================================================
	function undermaintenance($type=1){
		$owner = $this->CI->config->item('owner');
		switch ($type) {
			case '1':
				$text = "under maintenance";
				break;
			case '2':
				$text = "on the way!";
				break;
		}
		$html = "
			<html>
				<head>
					<title>" . $owner . "</title>
				</head>
				<body>
					<h1><i class='fas fa-road'></i>&nbsp;&nbsp;This application is ".$text."!</h1>
					<kbd>It's almost there</kbd>
				</body>
			</html>
		";
		return $html;
	}
	function konv_bulan($mm,$lang=2){
		$bulan ="";
		$mm = intval($mm);
		switch ($lang) {
			case "1":
				switch ($mm)
				{
				case 1 : $bulan="January";break;
				case 2 : $bulan="February";break;
				case 3 : $bulan="March";break;
				case 4 : $bulan="April";break;
				case 5 : $bulan="May";break;
				case 6 : $bulan="June";break;
				case 7 : $bulan="July";break;
				case 8 : $bulan="August";break;
				case 9 : $bulan="September"; break;
				case 10 : $bulan="October";break;
				case 11 : $bulan="November";break;
				case 12 : $bulan="December";break;
				}
				break;
			case "2" :
				switch ($mm)
				{
				case 1 : $bulan="Januari";break;
				case 2 : $bulan="Februari";break;
				case 3 : $bulan="Maret";break;
				case 4 : $bulan="April";break;
				case 5 : $bulan="Mei";break;
				case 6 : $bulan="Juni";break;
				case 7 : $bulan="Juli";break;
				case 8 : $bulan="Agustus";break;
				case 9 : $bulan="September"; break;
				case 10 : $bulan="Oktober";break;
				case 11 : $bulan="November";break;
				case 12 : $bulan="Desember";break;
				}
				break;
			case "3" :
				switch ($mm)
				{
				case 1 : $bulan="I";break;
				case 2 : $bulan="II";break;
				case 3 : $bulan="III";break;
				case 4 : $bulan="IV";break;
				case 5 : $bulan="V";break;
				case 6 : $bulan="VI";break;
				case 7 : $bulan="VII";break;
				case 8 : $bulan="VIII";break;
				case 9 : $bulan="IX"; break;
				case 10 : $bulan="X";break;
				case 11 : $bulan="XI";break;
				case 12 : $bulan="XII";break;
				}
				break;
			default:
				# code...
				break;
		}
		return $bulan;
	}
	function konv_hari($dd, $lang=2){
	  	$hari = "";
	    $dd = intval($dd);
	    switch ($lang) {
	    	case '1':
			    switch ($dd)
			    {
			      case 1 : $hari="Monday";break;
			      case 2 : $hari="Tuesday";break;
			      case 3 : $hari="Wednesday";break;
			      case 4 : $hari="Thursday";break;
			      case 5 : $hari="Friday";break;
			      case 6 : $hari="Saturday";break;
			      case 7 : $hari="Sunday";break;
			    }
	    		break;
	    	case '2':
			    switch ($dd)
			    {
			      case 1 : $hari="Senin";break;
			      case 2 : $hari="Selasa";break;
			      case 3 : $hari="Rabu";break;
			      case 4 : $hari="Kamis";break;
			      case 5 : $hari="Jumat";break;
			      case 6 : $hari="Sabtu";break;
			      case 7 : $hari="Minggu";break;
			    }
	    		break;
	    }
    	return $hari;
  	}
	function removeElementWithValue($array, $key, $value){
		foreach($array as $subKey => $subArray){
			if($subArray[$key] == $value){
				unset($array[$subKey]);
			}
		}
		return $array;
	}
	function arraydiffm($array1, $array2){ //function utk remove multidimensional array
	    $result = [];
	    foreach($array1 as $key => $val) {
	        if(array_key_exists($key, $array2)){
	            if(is_array($val) || is_array($array2[$key])) {
	                if (false === is_array($val) || false === is_array($array2[$key])) {
	                    $result[$key] = $val;
	                } else {
	                    $result[$key] = $this->array_diff_recursive($val, $array2[$key]);
	                    if (sizeof($result[$key]) === 0) {
	                        unset($result[$key]);
	                    }
	                }
	            }
	        } else {
	            $result[$key] = $val;
	        }
	    }
	    return $result;
	}
	function get_date($type = null, $lang=1){
	    $date = date('d-m-Y');
	    $new = $this->parseDate($date, 'N-Y-m-d');
	    $dayIndo = Array('Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu');
	    $monIndo = array("Januari", "Februari", "Maret","April", "Mei", "Juni","Juli", "Agustus", "September","Oktober", "November", "Desember");

	    $hari = substr($new, 0, 1); // memisahkan format tahun menggunakan substring
	    $tahun = substr($new, 2, 4); // memisahkan format tahun menggunakan substring
	    $bulan = substr($new, 7, 2); // memisahkan format bulan menggunakan substring
	    $tgl = substr($new, 10, 2); // memisahkan format tanggal menggunakan substring
		
		switch ($type) {
	    	case 'day':
	    		return $this->konv_hari($hari);
	    		break;
	    	case 'month':
	    		return $this->konv_bulan($bulan);
	    		break;
	    	default:
	    		// return $this->konv_hari[(int) $hari] . ", " . $tgl . " " . $this->konv_bulan[(int) $bulan - 1] . " " . $tahun;
	    		break;
	    }	    
	}	
	function parseDate($date = '', $format = 'd/M/Y', $return_null = false) {
	    global $parse_date_time;
	    $parse_date_time_mulai = time() + str_replace(' ', '', microtime());// + microtime();
	    if (empty($date) || $date == " " || $date == '1901-01-01 00:00:00' || $date == '1901-01-01' || $date == '1900-01-01' || $date == '1900-01-01 00:00:00' || $date == '--') {
	        if ($return_null)
	            return '-';
	        else
	            return null;
	    }
	    $return = date($format, strtotime($date));
	    $parse_date_time_selesai = time() + str_replace(' ', '', microtime());// + microtime();
	    $parse_date_time += $parse_date_time_selesai - $parse_date_time_mulai;
	    return $return;
	}
	function is_date($str, $format="yyyymmdd") {
      return (bool)strtotime($str);
	}
	function showPDF($parameter){
		$this->CI->load->library('Dompdf');

		$logo = $this->CI->config->item('logo');
		// $this->debug_array($parameter);
		$server = false;
		$output = true;
		$paper = "A4";
		$filename = "pdfreport";
		$orientation = "portrait";
		$margin_left = "2";
		$margin_right = "2";
		$margin_bottom = "5";
		$letter_header = false;
		$letter_footer = false;
		$header= null;
		$footer= null;
		$html = "<link rel='stylesheet' href='".$_SERVER["DOCUMENT_ROOT"]."/resources/css/reportpdf.css'>";
		$stylelogo = null;
		
		if(is_array($parameter)){
			foreach ($parameter as $detail=>$value){
				${$detail} = $value;
			}
		}
		if(!isset($margin_left_header)){
			$margin_left_header = $margin_left+1;
		}
		$html .= "<style>
			.nobreak {
				page-break-inside: avoid;
		  	}		
            @page{
                margin-left:".$margin_left."cm;
				margin-right:".$margin_right."cm;
				margin_bottom:".$margin_bottom."cm;
			}
		";
		if($letter_header){
			$html .= "
            @page {
                margin: 0cm 0cm;
			}
			@page {
				margin: 2cm 0cm 0cm 0cm;
			  }			
			";
		}
		if($letter_header){
			$header = '
			<header>
				<img src="'.$_SERVER["DOCUMENT_ROOT"].'/resources/img/'.$logo.'" style="'.$stylelogo.'height:80px;">
        	</header>
			';
		}
		$margin_footer = '';
		if($letter_footer){
			$footer = '
			<footer>
				<img src="'.$_SERVER["DOCUMENT_ROOT"].'/resources/img/footer_tengah.png" style="height:80px">
				<div  style="float:right;"><img src="'.$_SERVER["DOCUMENT_ROOT"].'/resources/img/logokanan.png" style="height:80px"></div>
        	</footer>
			';
			$margin_footer = 'margin-bottom:2cm;';
			if($margin_bottom!=5){
				$margin_footer = 'margin-bottom:'.$margin_bottom.'cm;';
			}
			
		}
		$html .= "
			#header { position: fixed; left: -17cm; top: 15px; right: 0px; text-align: center; padding-bottom:50px}
			/** Define the header rules **/
            header {
                position: fixed;
                // top: 0.5cm;
                left: 0.5cm;
				right: 0cm;
				// bottom:10cm;

				top: -1.8cm;
				width: 100%;
				height: 109px;

                /** Extra personal styles **/
                color: white;
                text-align: left;
                line-height: 1.5cm;
            }

            /** Define the footer rules **/
            footer {
                position: fixed; 
                bottom: 0cm; 
                left: 0cm; 
                right: 0cm;
                height: 2cm;
                color: white;
                text-align: center;
                line-height: 1.5cm;
			}
			main{
				top:10cm;
				" . $margin_footer ."
				.page:after { top:100px; }  
			}		
		</style>";
		if(is_array($parameter)){
			foreach ($parameter as $detail=>$value){
				${$detail} = $value;
			}
		}
		// $nguik = "<body>" .$header . $footer . "<main style='margin-left:1.5cm;margin-right:1.5cm;'>" . $report . "</main>" . "</body>";
		$nguik = "<body style='width:100%'>" .$header . $footer . "<main style='width:100%;margin-left:".$margin_left."cm;margin-right:".$margin_right."cm;'>" . $report . "</main>" . "</body>";
		$html .= $nguik;
		// die($html);
		if($server){
			if(!isset($path)){
				show_error("Tentukan path di server!");
			}
		}
		$parameter = array(
			"html"=>$html,
			"filename"=>$filename,
			"output"=>$output,
			"paper"=>$paper,
			"orientation"=>$orientation,
			"server"=>$server
		);
		if(isset($path)){
			// log_message("debug", "dari common>>" . $path);
			$parameter = array_merge($parameter, array("path"=>$path));
		}
		$this->CI->dompdf->generate($parameter);
	}
	function prnFile($parameter){
		$JUDULS="Laporan";
		$REPORT="";
		$TYPESS="3";
		$FOOTER="";
		$INLINE=false;
		$perdirjen = false;
		$pdf = false;
		$showheader = true;
		if(is_array($parameter)){
			foreach ($parameter as $detail=>$value){
				${$detail} = $value;
			}
		}
		if($TYPESS==3){
			$pdf = true;
		}
		$this->CI->load->helper('file');
		// $html = "<html style='margin:0px;padding:0px;width:100%'><title>".str_replace("<br>", "", $JUDULS)."</title>";
		$html = "<html style='margin:0px;padding:0px;width:100%'>";//.$this->header_report(array('JUDULS'=>$JUDULS,'pdf'=>$pdf))."";

		if($TYPESS==3 || $TYPESS=='3F'){
			$html .= "<link rel=\"stylesheet\" href=\"".base_url(CSS."reportpdf.css") ."\">";
		}
		if($showheader){
			$html .= "<body>".$this->header_report(array('JUDULS'=>$JUDULS,'pdf'=>$pdf, "perdirjen"=>$perdirjen));	
		}
		if($TYPESS==1){
			$html .= "<link rel=\"stylesheet\" href=\"".base_url(CSS."bootstrap/bootstrap.min.css") ."\">";
			$html .= "<link rel=\"stylesheet\" href=\"".base_url(CSS."report.css") ."\">";
			$html .= "
			<div id='print-modal-controls'>
				<a href='javascript:window.print()' class='print' title='Print page'>Cetak</a>
				<a href='javascript:window.close()' class='close' title='Close print preview'>Close</a>
			</div>
			";
		}
		$tbl = null;
		if(isset($tanda_tangan)){
			// debug_array($tanda_tangan);
			$penandatangan_Nama = null;
			$posisi = null;
			if($tanda_tangan!=""){
				$tanda_tangan = json_decode($tanda_tangan);
				foreach($tanda_tangan as $keytandatangan=>$valuetandatangan){
					${$keytandatangan} = $valuetandatangan;
				}
			}
			if(isset($username)){
				if($username!=""){
					$this->CI->load->library('authloginad');

					$arrayBase = array(
						"username"=>$username,
						"return"=>"array"
					);
					$jdata = $this->CI->authloginad->getOrganization($arrayBase);
					// debug_array($jdata);
					foreach($jdata[0] as $key=>$value){
						${"penandatangan_" . $key} = $value;
					}
				}
			}
			$tbl = "<div style='float:right'><table class='no-border' style='width:8cm;margin-top:1cm;'>";
			$tbl .= "	<tr><td class='centermiddle'>D i r e k s i,</td></tr>";
			$tbl .= "	<tr><td style='height:100px'>&nbsp;</td></tr>";
			$tbl .= "	<tr><td class='centermiddle'>".$penandatangan_Nama."</td></tr>";
			$tbl .= "	<tr><td class='centermiddle'>".$posisi."</td></tr>";
			
			$FOOTER = $tbl . $FOOTER;
		}
		if(isset($paraf)){
			if($paraf==1){
				$tbl .="
				<tr>
					<td>
					<table>
						<tr><td colspan=3 style='height:10px'></td></tr>
						<tr><td>Paraf</td><td>:</td><td>__________</td></tr>
						<tr><td>Kadiv Akun</td><td>:</td><td>__________</td></tr>
						<tr><td>Kabid Lapkeu Akun Yarpens</td><td>:</td><td>__________</td></tr>
						</table>
					</td>
				</tr>
				";
			}
		}
		if(isset($tanda_tangan)){
			$tbl .= "</table></div>";
		}
		$FOOTER = "
		<div class='row'>
			<div class='col-md-8' style='float:left;height:200px;'>
				<div style='padding:180px 0px 0px 0px;width:400px;font-size:8pt;font-family:arial'>dicetak oleh : ".$this->CI->session->userdata('USR_LOGINS').", " . date('Y-m-d H:i:s') . "</div>
			</div>
			<div class='col-md-4' style='float:right;height:200px;'>" . $tbl . "</div>
		</div>
		";
		// $html .=<body style='margin:0px;padding:0px'>
		$html .= "" . $REPORT . $FOOTER ."</body></html>";
		// $INLINE = false;
		if($INLINE){
			$rpt = "rpt".rand(1,9).rand(1,9).rand(1,9).".rpcx";
			if ( ! write_file("temp/$rpt", $html, 'w')){
				$return = 'Unable to write the file';
			}else{
				$return = substr($rpt,0,-5);
			}			
			$this->showFile($return, $TYPESS);
		}else{
			echo $html;
		}
	}	
	function header_report($parameter){
		$OTHERS=null;
		$JUDULS=null;
		$ADDRES=false;
		$perdirjen = false;
		$FOOTER="";
		$pdf = true;
		$tengah = "";
		if(is_array($parameter)){
			foreach ($parameter as $detail=>$value){
				${$detail} = $value;
			}
		}
		if($perdirjen){
			$OTHERS_old = "<td style='text-align:left;font-size:10px'>
			<div style='float:right'>
			Header<br>
			</div></td>";

			$OTHERS .= "<td style='text-align:left;font-size:10px'>
			<div style='float:right'>
			Lampiran IV<br>
			</div></td>
			";
		}
		$tdwidth = "80%";
		if($OTHERS!=""){
			$tdwidth = "50%";
		}
		$openheader = "";
		$closeheader = "";
		$openfooter = "";
		$closefooter = "";
		$closepdf = "";

		if($pdf){
			$openheader = "
<!--mpdf
<htmlpageheader name='myheader'>
			";
			$closeheader = "
</htmlpageheader>			
			";
			$openfooter = "
<htmlpagefooter name='myfooter'>
<div style='border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm;'>
Page {PAGENO} of {nb}
</div>			
			";
			$closefooter = "
</htmlpagefooter>
			";
			$closepdf = "
<sethtmlpageheader name='myheader' value='on' show-this-page='1' />
<sethtmlpagefooter name='myfooter' value='on' />
mpdf-->
			";
		}
		$cssimage = 'height="60"';
		$tengah .= "<td width='60%' style='text-align:center;font-weight:bold;font-size:14px'>".$JUDULS."</td>";

		$html = $openheader . '
<table style="margin-bottom:30px" class="no-border"><tr>
<td width="20%" style="vertical-align:middle">
    <img id=logonya src="/resources/img/'.$logo.'"  style="display:block;" '.$cssimage.'>
</td>
'.$tengah.'
'.$OTHERS.'
</tr></table>

' . $closeheader . '
' . $openfooter . '
' . $closefooter. '
' . $closepdf;
		// $html= "";
		return $html;		
	}	
	function showFile($rpt, $opt, $types=null){
		$this->CI->load->helper('file');
	  	$ort = "P";
	  	$descre = $types;
	  	$string = read_file("temp/$rpt.rpcx");
	  	unlink("temp/$rpt.rpcx");

			if($opt==3){
	  		$version = explode('.', PHP_VERSION);
				if($version[0]==7){
					$opt = 4;
				}
	  	}
	  	switch ($opt) {
	  		case 1:
	  	  		echo $string;
	  			break;
	  		case 2:
				// Fungsi header dengan mengirimkan raw data excel
				header("Content-type: application/vnd-ms-excel");
				header("Content-Disposition: attachment; filename=".$descre."_".date('Ymd').".xls");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
				header("Pragma: public");
				echo $string;
	  			break;
	  		case 4:
	  		 	ini_set('memory_limit', '-1'); 
	  		 	ini_set('max_execution_time', '3000'); 
	        $filename = time()."_Laporan_" . $descre.".pdf";
	        $html = $string;
				  $this->CI->load->library('M_pdf7');
				  $mpdf = $this->CI->m_pdf7->createpdf(
				  	array('param'=>array(
					  				'mode' => 'utf-8',
					  				'format' => 'A4',
										'margin_left' => 5,
										'margin_right' => 5,
										'margin_top' => 5,
										'margin_bottom' => 5,
										'margin_header' => 5,
										'margin_footer' => 5
									), 
				  		'content'=>$html)
				  );  		
	  			break;
	  		case '3F':
	  		case 3:
	  			if($opt=="3F"){
	  				$output = "F";
	  			}else{
	  				$output = "I";
	  			}
	  			// ini_set("memory_limit","256M");
	  		 	ini_set('memory_limit', '-1'); 
	  		 	ini_set('max_execution_time', '3000'); 
	        $filename = time()."_Laporan_" . $descre.".pdf";
	        // $string = "detanto";
	        $html = $string;
	        // echo $html;
	        // die();
	        $this->CI->load->library('M_pdf');
	        $this->CI->m_pdf->createpdf(array('margin_top'=>'5','filename'=>$filename, 'content'=>$html, "output"=>$output));
	  			break;
	  		default:
	  			# code...
	  			break;
	  	}
	}
	function responsealert($parameter){
		$function = "jvSave()";
		$this->CI->lang->load('common');
		$title = ($this->CI->lang->line("save_edit_ubah") == FALSE) ? $val : $this->CI->lang->line("save_edit_ubah");
		$this->debug_array($title);
		// $title = "Simpan Perubahan?";
		$confirm = true;
		$confirmButtonText = "Ya";
		$cancelButtonText = "Tidak";
		$type = 'success';
		$confirmButtonClass='btn-success';
		$cancelButtonClass='btn-danger';

		foreach ($parameter as $indx=>$value){
			${$indx}=$value;
		}
		$save = "
			function ".$function."{
		";
		$save .= $this->swal2(array('title'=>$title,'confirm'=>$confirm,'confirmButtonText'=>$confirmButtonText,'type'=>$type,'cancelButtonText'=>$cancelButtonText,'confirmButtonClass'=>$confirmButtonClass,'cancelButtonClass'=>$cancelButtonClass));
		$save .= "}
		";

		return $save;
	}
	function fputcsv_eol($handle, $array, $delimiter = ',', $enclosure = '"', $eol = "\n") {
	    $return = fputcsv($handle, $array, $delimiter, $enclosure);
	    if($return !== FALSE && "\n" != $eol && 0 === fseek($handle, -1, SEEK_CUR)) {
	    	fwrite($handle, $eol);
	    }
	    return $return;
	}
	public function downloadFile($url, $path){
	    $newfname = $path;
	    $file = fopen ($url, 'rb');
	    if ($file) {
	        $newf = fopen ($newfname, 'wb');
	        if ($newf) {
	            while(!feof($file)) {
	                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
	            }
	        }
	    }
	    if ($file) {
	        fclose($file);
	    }
	    if ($newf) {
	        fclose($newf);
	    }
	}
	function isnulldb($field=null, $valuenull=null, $alias=null){
		$driver = $this->driver;
		$awal = "";
		$return = "";
		$akhir = "";
		switch($this->driver){
			case "sqlsrv":
				$awal = "ISNULL(";
				$akhir = ")";
				$separator = ", ";
				break;
			case "mysql":
			case "mysqli":
				$awal = "IFNULL(";
				$akhir = ")";
				$separator = ", ";
				break;
			case "oci8":
			case "postgre" :
				$separator = " || ";
				break;
		}
		$rc = false;
		$valuenull = $valuenull=="" ? "''" : $valuenull;
		$return = $field . ", " . $valuenull;
		return $awal . $return . $akhir . " " . $alias;
	}
	function lengthdb($field=null, $valuelength=null, $symbol=">",$alias=null){
		$driver = $this->driver;
		$awal = "";
		$return = "";
		$akhir = "";
		switch($this->driver){
			case "sqlsrv":
				$awal = "LEN(";
				$akhir = ")";
				$separator = ", ";
				break;
			case "mysql":
			case "mysqli":
				$awal = "LENGTH(";
				$akhir = ")";
				$separator = ", ";
				break;
			case "oci8":
			case "postgre" :
				$separator = " || ";
				break;
		}
		$rc = false;
		$valuelength = $valuelength=="0" ? "0" : $valuelength;
		$return = $field;
		return $awal . $return . $akhir . $symbol . $valuelength . " " . $alias;
	}
	function concatdb($field=null, $alias=null){
		//$this->driver
		$driver = "mysql";
		$awal = "";
		$return = "";
		$akhir = "";
		switch($this->driver){
			case "sqlsrv":
				$separator = "+";
				break;
			case "mysql":
			case "mysqli":
				$awal = "CONCAT(";
				$akhir = ")";
				$separator = ", ";
				break;
			case "oci8":
			case "postgre" :
				$separator = " || ";
				break;
		}
		$rc = false;
		for($f=0;$f<count($field);$f++){
			if ($rc) $return .= $separator;
			if(preg_match('/[\'\/~`\!@#\$%\^&\*\(\)\-\+=\{\}\[\]\|;:"\<\>,\?\\\]/', $field[$f], $match)==true) {
				$return .= "'" . $field[$f] . "'";
			}else{
				$return .= $field[$f];	
			}
			$rc = true;
		}
		return $awal . $return . $akhir . " " . $alias;
	}
	function downloadftp($parameter){
  		$this->CI->load->library('ftp');
		// load konfigurasi ftp
		$remAddres = $this->CI->config->item('remAddres');	
		$remUserid = $this->CI->config->item('remUserid');	
		$remPasswd = $this->CI->config->item('remPasswd');	
		$path = '/assets/documents';
		foreach ($parameter as $indx=>$value){
			${$indx}=$value;
		}

	    //FTP configuration
	    $ftp_config['hostname'] = $remAddres; 
	    $ftp_config['username'] = $remUserid;
	    $ftp_config['password'] = $remPasswd;
	    $ftp_config['debug']    = FALSE;
	        
	    if ($this->CI->ftp->connect($ftp_config)) {
    		$ftpServer = true;
		} else {
    		$ftpServer = false;
		}
		// $filename = "20170705133848_L17_3284.pdf";
		if($ftpServer){
			$local = $_SERVER["DOCUMENT_ROOT"];
	        $this->CI->ftp->download($path.'/'.$filename,$local.'/temp/'.$filename);
	        $this->CI->ftp->close();
		}else{
			echo "detanto";
		}		
	}
	function isValidDate($date=''){
		$lang['Januari']   = 'January';
		$lang['Februari']  = 'February';
		$lang['Maret']     = 'March';
		$lang['April']     = 'April';
		$lang['Mei']       = 'May';
		$lang['Juni']      = 'June';
		$lang['Juli']      = 'July';
		$lang['Agustus']   = 'August';
		$lang['September'] = 'September';
		$lang['Oktober']   = 'October';
		$lang['November']  = 'November';
		$lang['Desember']  = 'December';
		$lang['Jan']  = 'Jan';
		$lang['Feb']  = 'Feb';
		$lang['Mar']  = 'Mar';
		$lang['Apr']  = 'Apr';
		$lang['Mei']  = 'May';
		$lang['Jun']  = 'Jun';
		$lang['Jul']  = 'Jul';
		$lang['Ags']  = 'Aug';
		$lang['Agt']  = 'Aug';
		$lang['Sep']  = 'Sep';
		$lang['Okt']  = 'Oct';
		$lang['Nov']  = 'Nov';
		$lang['Des']  = 'Dec';

		if (strtotime($date))
		{
			return strtotime($date);
		}
		else
		{
			foreach ($lang as $k=>$v)
			{
				$date = str_replace($k,$v,$date);
			}
			return $date;
		}
		return FALSE;
	}
	function cleanString($string) {
	    $string = str_replace(array('[\', \']'), '', $string);
	    $string = preg_replace('/\[.*\]/U', '', $string);
	    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
	    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
	    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
	    $string = strtolower(trim($string, '-'));
	    return $string;
	}
	function logmodul($validate, $pass){
		$log_result = null;
		$app_number = null;
		$from = $this->CI->router->fetch_class();
		// ======================== ambil parameter
		foreach ($pass as $param=>$value){
			${$param}=$value;
		}

		if($this->CI->session->userdata('app_numbr')!=""){
			$app_number =$this->CI->session->userdata('app_numbr');
		}else{
			if(isset($app_number)){
				if($app_number!=""){
					$app_number = $app_number;
				}
			}
		}		
		if($validate){
			if(isset($table_name)){
				$this->CI->db->from($table_name);
				$this->CI->db->where($pk);
				$rslTable = $this->CI->db->get();
				foreach($pk as $keypk=>$valuepk){
					$log_fkidents = $valuepk;
				}

				$arrExclude = array("updnam","upddat");

				if($rslTable->num_rows()>0){
					$row = $rslTable->row();
					foreach($POST as $keypost=>$valuepost){
						$lanjut = true;

						$keycheck = substr($keypost, -6);

						if(isset($exclude)){
							if(is_array($exclude)){
								if(in_array($keypost, $exclude)){
									$lanjut = false;
								}
							}
						}
						if(in_array(strtolower($keycheck), $arrExclude)){
							$lanjut = false;
						}

						if($lanjut){
							if(isset($row->{$keypost})){
								$valuedb = $row->$keypost;
								if($this->is_date($valuepost)){
									$valuedb = str_replace(" 00:00:00","", $valuedb);
									$valuepost = str_replace(" 00:00:00","", $valuepost);
								}
								if($valuedb != $valuepost){
									$value_old = $valuedb;
									$value_new = $valuepost;
									if($value_new==""){
										$log_action = array("action"=>"Data dikosongkan", "field"=>$keypost, "nilai_awal"=>$value_old);
									}else{
										$log_action = array("action"=>"Ubah Data", "field"=>$keypost, "nilai_awal"=>$value_old, "nilai_ubah"=>$value_new);
									}
									$log_action = array_merge($log_action, $pk);
									if(isset($nomor_transaksi)){
										$log_action = array_merge($log_action, array("nomor"=>$nomor_transaksi));
									}
									if(isset($log_fkidents)){
										if(is_numeric($log_fkidents)){
											$input["log_fkidents"] = $log_fkidents;
										}else{
											$log_action = array_merge($log_action, array("log_fkidents"=>$log_fkidents));
										}
									}
									if(is_array($log_action)){
										$log_action = json_encode($log_action);
									}
									$input["log_action"] = $log_action;
								}
	
								if(isset($input)){
									$this->CI->crud->useTable("t_log_aktivitas");
									$input["log_address"] = $this->CI->input->ip_address();
									$input["log_from"] = $from;
									$input["log_appnumbr"] = $app_number;
									$input["log_table"] = $table_name;
									$input["log_field"] = $keypost;
									$input["log_result"] = $log_result;
									$input["log_usrnam"] = $username;
									$input["log_address_server"]=$_SERVER['SERVER_ADDR'];
									$this->CI->crud->save($input);
									unset($input);
								}
							}
						}
					}
				}else{
					$log_action = array("action"=>"Add Data");
					if(isset($nomor_transaksi)){
						$log_action = array_merge($log_action, array("nomor"=>$nomor_transaksi));
					}
					$log_action = json_encode($log_action);
					$this->CI->crud->useTable("t_log_aktivitas");
					$input["log_action"] = $log_action;
					$input["log_address"] = $this->CI->input->ip_address();
					$input["log_from"] = $from;
					$input["log_appnumbr"] = $app_number;
					$input["log_table"] = $table_name;
					$input["log_field"] = "-";
					$input["log_result"] = $log_result;
					$input["log_usrnam"] = $username;
					$input["log_address_server"]=$_SERVER['SERVER_ADDR'];
					// debug_array($input, false);
					$this->CI->crud->save($input);
					// $this->debug_sql(1);
					unset($input);
				}
			}
		}else{
			$this->CI->crud->useTable("t_log_aktivitas");
			if(is_array($log_action)){
				$log_action = json_encode($log_action);
			}
			$input["log_from"] = $from;
			$input["log_appnumbr"] = $app_number;
			$input["log_action"] = $log_action;
			$input["log_address"] = $this->CI->input->ip_address();
			$input["log_table"] = $table_name;
			$input["log_field"] = $keypost;
			$input["log_result"] = $log_result;
			$input["log_usrnam"] = $username;
			$input["log_address_server"]=$_SERVER['SERVER_ADDR'];
			// debug_array($input);
			if(isset($log_fkidents)){
				$input["log_fkidents"] = $log_fkidents;
			}
			$this->CI->crud->save($input);
			// $this->debug_sql(true);
			unset($input);
		}
		// die();
	}
	function generateArray($arrField, $table_name, $column=null, $checkTable=true, $type=null){
		$arrDb = array("Field","Type");
		$urutan = 0;
		$loop = 1;
		$paramField="Field";
		$paramType="Type";
		// $this->debug_array($column);
		// if($this->driver=="mssql" || $this->driver=="sqlsrv"){
		// 	$result = $this->CI->crud->getTableInfo($table_name, null, $this->database, "dbo");
		// 	$result = $result['Hasil'];
		// 	$paramField = "TBL_COLUMN";
		// 	$paramType = "TBL_DATTYP";
		// 	$hasilResult = $result;
		// }else{
		// 	$result = $this->CI->crud->getTableInformation($table_name);
		// 	$hasilResult = $result->result();
		// }
		if($checkTable){
			if($this->driver=="mssql" || $this->driver=="sqlsrv"){
				$result = $this->CI->crud->getTableInfo($table_name, null, $this->database, "dbo");
				$result = $result['Hasil'];
				$paramField = "TBL_COLUMN";
				$paramType = "TBL_DATTYP";
				$hasilResult = $result;
			}else{
				$result = $this->CI->crud->getTableInformation($table_name);
				$hasilResult = $result->result();
			}
		}else{
			$hasilResult = (object) $arrField;
			// if(isset($column->row())){
			// 	$column = $column->row();
			// }
			
		}
		// debug_array($arrField);	
		foreach($arrField as $keyF=>$valueF){
			// ${$keyF} = $valueF;
			if(!is_array($valueF)){
				$keyF = $valueF;
			}
			if(isset($valueF["value"])){
				$valueElement = $valueF["value"];
			}
			if(isset($valueF["urutan"])){
				$urutan = $valueF["urutan"];
			}
			if(isset($valueF["option"])){
				$option = $valueF["option"];
			}
			if(isset($valueF["tagsinput"])){
				$tagsinput = $valueF["tagsinput"];
			}
			if(isset($valueF["defaultValue"])){
				$defaultValue = $valueF["defaultValue"];
			}
			// 
			foreach($hasilResult as $key=>$valueResult){
				// debug_array($valueResult);
				if($checkTable){
					$keyHasil = $valueResult->$paramField;
					$keyType = $valueResult->$paramType;
				}else{
					$keyHasil = $key;
					$keyType = $valueF["type"];
				}

				if($keyF==$keyHasil){
					$label = null;
					$Typedb = null;
					if($checkTable){
						$Typedb = strtoupper($valueResult->$paramType);
					}
					
					$typeF = "txt";
					if(isset($valueF["type"])){
						$typeF = $valueF["type"];
					}else{
						if(strstr($Typedb, "VARCHAR")!=""){
							$Typedb = "VARCHAR";
						}
						switch ($Typedb){
							case "smallint":
							case "float" :
							case "INT(11)" :
							case "bigint" :
								$typeF = "num";
								break;
							case "DATETIME" :
								$typeF = "dat";
								break;
							case "VARCHAR" :
								$typeF = "txt";
								break;
						}
					}
					if(isset($column->$paramField)){
						if(!is_array($column->$paramField)){
							$fieldvalue = $column->$paramField;
						}
					}
					
					$arrayTable = array('group'=>(!isset($group) ? 1 : $group), 'urutan'=>$urutan++, 'type'=>$typeF, "namanya" =>$keyF);
					if(is_array($valueF)){
						foreach($valueF as $keyValueF=>$valueValueF){
							$arrayTable = array_merge($arrayTable, array($keyValueF=>$valueValueF));
						}
					}
					if(isset($tagsinput)){
						if(isset($valueF["fld_desc"])){
							$fld_dsc = $valueF["fld_desc"];
							if($column!=null){
								if(isset($column->$fld_dsc)){
									unset($tagsinput["data"]);
									$tagsinput["data"] = $column->$keyF . "~" . $column->$fld_dsc;
								}
							}
						}
						$arrayTable = array_merge($arrayTable, array("tagsinput"=>$tagsinput));
					}
					if($column!=null){
						$valueCol = null;
						if(isset($column->$keyF)){
							if(isset($valueElement)){
								$valueCol = $valueElement;
								// debug_array($valueCol, false);
							}else{
								if(!is_array($column->$keyF)){
									$valueCol = $column->$keyF;
								}
							}
						}else{
							if(isset($valueElement)){
								$valueCol = $valueElement;
							}else{
								// $this->debug_array($keyF);
								if(isset($column->$keyF)){
									$valueCol = $column[$keyF];
								}
							}
						}
						if($typeF=="chk"){
							$valueCol = ($valueCol==1 ? true : false);	
						}
						if(isset($option) && $typeF!="cmb"){
							if(isset($option[$valueCol])){
								$valueCol = $option[$valueCol];
							}
						}
						if(isset($valueF["number_format"])){
							if($valueF["number_format"]){
								$decimal = 0;
								if(isset($valueF["decimaldigit"])){
									$decimal = $valueF["decimaldigit"];
								}
								$valueCol = number_format($valueCol,$decimal,",",".");
							}
						}
						if(isset($defaultValue)){
							if($valueCol==""){
								$valueCol = $defaultValue;
							}
						}
						$arrayTable = array_merge($arrayTable, array("value"=>$valueCol));
						
		/*
						$valueCol = $column->$keyF;
						if($typeF=="chk"){
							$valueCol = ($valueCol==1 ? true : false);	
						}
						if($typeF=="txt" && isset($option)){
							$valueCol = $option[$valueCol];
							$this->debug_array($valueCol, false);
						}
						$arrayTable = array_merge($arrayTable, array("value"=>$valueCol));
						*/
					}else{
						if(isset($defaultValue)){
							$arrayTable = array_merge($arrayTable, array("value"=>$defaultValue));
						}
					}
					$arrTable[] = $arrayTable;//array('group'=>1, 'urutan'=>$urutan++, 'type'=>$typeF, "namanya" =>$keyF, "value"=>$typeF);//$fieldvalue);
					unset($valueElement);
					unset($tagsinput);
					unset($defaultValue);
				}
			}
			unset($option);
		}
		// debug_array($arrTable);
		return $arrTable;		
	}	
	function getFileType($file_name, $file_mime){
		if(substr($file_mime, 0,11)=='application'){
			$arrFilename = explode(".", $file_name);
			$cntextension = count($arrFilename)-1;
			if($cntextension>0){
				$file_type = $arrFilename[$cntextension];
			}else{
				$file_type = substr($file_mime, -3);
			}
		}
		if(substr($file_mime, 0,5)=='image'){
			$arrfile_type = explode("/", $file_mime);
			if(isset($arrfile_type[1])){
				$file_type = $arrfile_type[1];
			}
		}
		if(!isset($file_type)){
			$file_type = substr($file_mime, -3);  
		}
		return $file_type;
	}
    function fetch_email($return=false){
		$this->CI->load->library("kirimemail");
		$mitra_cabang_name = null;
		// debug_array()
		/* try to connect */
		$owner = $this->CI->config->item('owner');
        $inbox = imap_open($this->hostname,$this->username,$this->password) or die('Cannot connect to '.$owner.' Email: ' . imap_last_error());
        
        /* grab emails */
        $emails = imap_search($inbox,'UNSEEN'); // ALL-> semua; UNSEEN -> only unread email
        
        /* if emails are returned, cycle through each... */
        if($emails) {
            /* begin output var */
            $output = '';
            
            /* put the newest emails on top */
            rsort($emails);
			/* Looping for every email*/
			$loop = 0;
            foreach($emails as $email_number) {
				$respon = null;
				$subject = "";
				$kurang = "";
				$man_id = null;
				$bp_id = null;
				$bp_ktpa = null;
				$bp_no_pensiun = null;
				$nomorid = null;
				$file_tolak = null;
				$eml_id = null;
				$sbj_id = null;
				$nama_peserta = null;
				$checkAttachment = true;
				$lanjut = true;
				$found = 0;
				$jmlSyarat = 0;
                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox,$email_number,0);
                //========================================================================================================
                //======================================== Header Information ============================================
                //========================================================================================================
                $header = imap_headerinfo($inbox, $email_number);

                $email_subject = "NO SUBJECT";

				$email_from_name = $header->fromaddress;
				$email_from_mailbox = $header->from[0]->mailbox;
				$email_from_host = $header->from[0]->host;

				$email_to_mailbox = $header->to[0]->mailbox;
				$email_to_host = $header->to[0]->host;

				$email_from_email = $email_from_mailbox . "@" .  $email_from_host;
				$email_to_email = $email_to_mailbox . "@" .  $email_to_host;
				$email_date_sent = $header->MailDate;
				$email_size = $header->Size;
				// if($email_from_email!="detanto@gmail.com"){
				// 	$respon = "09";
				// 	$lanjut = false;
				// 	$checkAttachment = false;
				// }
				$chkEmail = $this->CI->crud->getEmailterdaftar($email_from_email);
				if($chkEmail->num_rows()==0){
					$respon = "09";
					$lanjut = false;
					$checkAttachment = false;
				}else{
					if(isset($chkEmail->cmb_name)){
						$mitra_cabang_name = $chkEmail->cmb_name;
					}else{
						$mitra_cabang_name = null;
					}
					
				}

                if(isset($header->subject)){
                    $email_subject = $header->subject;
                }
				$arrSubject = explode("]", $email_subject);
				if(!isset($arrSubject[1])){
					// $respon = "KTPA/Nomor Pensiun";
					$respon = "01";//Subject E-mail tidak benar";
					$lanjut = false;
					$checkAttachment = false;
				}
				
				if($lanjut){
					$SUBJECT = str_replace("[", "", $arrSubject[0]);
					$rsl = $this->CI->crud->getSubject_email($SUBJECT);
					$nama_peserta = null;
					$bp_id = null;
					$bp_no_pensiun = null;
					if($rsl->num_rows()>0){
						$row = $rsl->row();
						$sbj_id = $row->sbj_id;
						$sbj_redirect = $row->sbj_redirect;
						$arr1 = explode("-", $arrSubject[1]);

						if(isset($arr1[1])){
							$nomorid = trim($arr1[0]);
							$nomorid = trim($nomorid);
							$manfaat = trim($arr1[1]);
							$this->CI->load->model('m_master');
							$chk_nomor = $this->CI->m_master->getPeserta_info($nomorid);
							if($chk_nomor["found"]==1){
								if(isset($chk_nomor["resultset"]->bp_name)){
									$nama_peserta = $chk_nomor["resultset"]->bp_name;
									$bp_id = $chk_nomor["resultset"]->bp_id;
									$bp_ktpa = $chk_nomor["resultset"]->bp_ktpa;
									$bp_no_pensiun = $chk_nomor["resultset"]->bp_no_pensiun;
									$bp_is_life = $chk_nomor["resultset"]->bp_is_life;
									if($bp_no_pensiun!=""){
										if($bp_no_pensiun!=$nomorid){
											$respon = "03"; //Peserta sudah pensiun, silahkan melakukan pengajuan menggunakan  nomor KTPA : bp_ktpa
											$checkAttachment = false;
										}
									}
									// if($bp_is_life!=1){
									// 	$respon = "10"; //Peserta sudah pensiun, silahkan melakukan pengajuan menggunakan  nomor KTPA : bp_ktpa
									// 	$checkAttachment = false;
									// }
								}
								$lanjut = true;
							}else{
								$respon = "04"; 
								$lanjut = false;
								$checkAttachment = false;
							}
							
							if($lanjut){
								$rslManfaat = $this->CI->crud->getProduct_email($manfaat);
								if($rslManfaat->num_rows()>0){
									$row = $rslManfaat->row();
									$man_id = $row->man_id;
									$lanjut = true;
								}else{
									$respon = "05"; 
									$checkAttachment = false;
								}			
							}
						}else{
							$respon = '02';
							$checkAttachment = false;
						}
					}else{
						$respon = '01';
						$checkAttachment = false;
					}
				}

				$message = imap_fetchbody($inbox,$email_number,1);

				$time = strtotime($email_date_sent);
				$email_date_sent = date("Y-m-d H:i:s", $time);

				$structure = imap_fetchstructure($inbox, $email_number);
				//========================================================================================================
				//======================================================= debug buat detanto@gmail.com ===================
				// $lanjut = false;
				// if($email_from_email=="detanto@gmail.com"){
					$lanjut = true;
				// }

				if($lanjut){
					if($checkAttachment){
						$attachments = array();
						// Checking Attachments
						if(isset($structure->parts) && count($structure->parts)) {
							for($i = 0; $i < count($structure->parts); $i++) 
							{
								$attachments[$i] = array(
									'is_attachment' => false,
									'filename' => '',
									'name' => '',
									'attachment' => ''
								);
		
								if($structure->parts[$i]->ifdparameters) 
								{
									foreach($structure->parts[$i]->dparameters as $object) 
									{
										if(strtolower($object->attribute) == 'filename') 
										{
											$attachments[$i]['is_attachment'] = true;
											$attachments[$i]['filename'] = $object->value;
											// $attachments[$i]['filesize'] = $structure->parts[$i]->bytes;
										}
									}
								}
		
								if($structure->parts[$i]->ifparameters) 
								{
									foreach($structure->parts[$i]->parameters as $object) 
									{
										if(strtolower($object->attribute) == 'name') 
										{
											$attachments[$i]['is_attachment'] = true;
											$attachments[$i]['name'] = $object->value;
											// $attachments[$i]['filesize'] = $structure->parts[$i]->bytes;
										}
									}
								}
		
								if($attachments[$i]['is_attachment']) 
								{
									$attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
									$attachments[$i]['filesize'] = $structure->parts[$i]->bytes;
		
									/* 3 = BASE64 encoding */
									if($structure->parts[$i]->encoding == 3) 
									{ 
										$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
										// file_get_contents($attachments[$i]['attachment']);
									}
									/* 4 = QUOTED-PRINTABLE encoding */
									elseif($structure->parts[$i]->encoding == 4) 
									{ 
										$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
									}
								}
							}
						}
					}
					//========================================================================================================
					//================================================ Attachment ============================================
					//========================================================================================================
					if(isset($overview[0]->subject)){
						$subject = $overview[0]->subject;
					}else{
						$subject = "";
						$checkAttachment = false;
						$respon = "01";
					}
					$input = array(
						"eml_subject"=>$subject,
						"eml_from_name"=>$email_from_name,
						"eml_from_email"=>$email_from_email,
						"eml_to_email"=>$email_to_email,
						"eml_date"=>$email_date_sent,
						"eml_size"=>$email_size,
						"eml_message"=>$message,
						"eml_messageno"=>$email_number,
						"eml_status"=>0,
						"eml_type"=>$sbj_id,
						"eml_jenis"=>$man_id,
						"eml_bpid"=>$bp_id,
						"eml_nopens"=>$bp_no_pensiun
					);
					// debug_array($input);
					$this->CI->crud->useTable("t_email");
					$this->CI->crud->save($input);
					$eml_id = $this->CI->crud->__insertID;

					if($checkAttachment){
						$rslSyarat = $this->CI->m_master->getManfaatsyarat_list($man_id, true, null, false);
						foreach($rslSyarat->result() as $keySyarat=>$valueSyarat){
							$arrSyarat[$valueSyarat->msy_id] = $valueSyarat->msy_referensi;
						}
						$jmlSyarat = count($arrSyarat);
						if($man_id!=null && $lanjut==true){
							asort($arrSyarat);
							$arrSyarat = array_diff($arrSyarat, array(''));
							// $arrSyarat = array_values($arrSyarat);
						}
						
						$found = 0;
						foreach($attachments as $attachment){
							if($attachment['is_attachment'] == 1)
							{
								$validasi_file = true;
								$filename = $attachment['name'];
								$extension = substr($filename, -4);
								if($extension!=".pdf"){
									$lanjut = false;
									$respon = '06';//"berkas harus berupa pdf";
									$validasi_file = false;
								}else{
									$lanjut = true;
									$namafile = str_replace($extension, "", $filename);
								}
								
								if($lanjut){
									foreach($arrSyarat as $key=>$value){
										if($namafile==$value){
											$found++;
											$lanjut = true;
											unset($arrSyarat[$key]);
											$msy_id = $key;
											// $arrSyarat = array_values($arrSyarat);
										}
									}
									// debug_array($found . ">>" . $jmlSyarat);
									if($lanjut){
										if(empty($filename)) $filename = $attachment['filename'];				
										if(empty($filename)) $filename = time() . ".dat";
										$folder = "temp";
										if(!is_dir($folder))
										{
											 mkdir($folder);
										}
										$fp = fopen("./". $folder ."/". $email_number . "-" . $filename, "w+");
										$file_size = $attachment['filesize'];
										// debug_array($file_size);
										if($file_size>1000000){
											$file_tolak = $filename;
											$lanjut = false;
											$respon = "08";
										}else{
											$respon = "00";
											$file_attachment = $attachment['attachment'];
											// fwrite($fp, $file_attachment);
											// fclose($fp);
											$f = finfo_open();
											$mime_type = finfo_buffer($f, $file_attachment, FILEINFO_MIME_TYPE);
											$file_type = $this->getFileType($filename, $mime_type);
											// $attach = fread($attachment['attachment'],$attachment['filesize']);
					
											$inputAttachment = array(
												"ema_emlid"=>$eml_id,
												"ema_doc_name"=>$filename,
												"ema_doc_type"=>$file_type,
												"ema_doc_size"=>$file_size,
												"ema_doc_mime"=>$mime_type,
												"ema_msyid"=>(isset($msy_id) ? $msy_id : null)
											);
											$this->CI->crud->useTable("t_email_attachment");
											$this->CI->crud->save($inputAttachment);
											$ema_id = $this->CI->crud->__insertID;
											$file_doc = $this->CI->common->mssql_escape($file_attachment);
											$sql = "UPDATE t_email_attachment SET ema_doc = " . $file_doc . " WHERE ema_id = " . $ema_id;
											// $sql = "INSERT INTO t_email_attachment  (ema_emlid, ema_doc) VALUES  (" . $fil_id . "," . $file_doc . ");";
											$this->CI->db->query($sql);
											@unlink($_SERVER["DOCUMENT_ROOT"]."/temp/".$email_number . "-" . $filename);
										}
									}
								}
							}
						}
					}
					if($found!=$jmlSyarat && $lanjut==true){
						$respon = "07";
					}
					if($lanjut){
						// debug_array($arrSyarat);
						// 
						if(isset($arrSyarat)){
							if(count($arrSyarat)==0){
								$respon = "00";
							}else{
								if($validasi_file){
									$n=1;
									foreach($arrSyarat as $key=>$value){
										$kurang .= $n . ". " . $value ."<br>";
										$n++;
									}									
									$respon = "07";
								}
							}
						}
					}
				}
				// $email_from_email = "detanto@gmail.com";
				$arrRespon = array(
					"sender"=>$email_from_name,
					"respon"=>$respon, 
					"nama_peserta"=>$nama_peserta, 
					"bp_no_pensiun"=>$bp_no_pensiun, 
					"nomorid"=>$nomorid, 
					"kurang"=>$kurang,
					"recipient"=>$email_from_email,
					"filename"=>$file_tolak,
					"eml_id"=>$eml_id,
					"email_subject"=>$email_subject,
					"mitra_cabang_name"=>$mitra_cabang_name
				);			
				$this->responOffice($arrRespon);
				$loop++;
				echo $output;			
			}
		}
		// die();
        /* close the connection */
		imap_close($inbox);
		if($return){
			return $loop;
		}
		
	}
	function responOffice($parameter){
		$owner = $this->CI->config->item('owner');
		$mitra_cabang_name = null;
		$email_subject = null;
		$nama_peserta = null;
		$titik = true;
		foreach($parameter as $key=>$value){
			${$key}= $value;
		}		
		if($respon!="00"){
			$subject = "[Pengajuan] Proses Gagal, ";
			$bodymessage =  "Mohon maaf " . $sender . ", <br><br>Pengajuan anda melalui email dengan subject : " . $email_subject . " ";
			if(isset($nama_peserta)){
				$bodymessage .=  "untuk " . $nama_peserta;
			}
			$bodymessage .=  " tidak berhasil :: ";

			$this->CI->crud->useTable("t_email");
			$inputError = array("eml_status"=>99, "eml_processed"=>1, "eml_error_type"=>$respon);
			$this->CI->crud->save($inputError, array("eml_id"=>$eml_id));

		}else{
			$subject = "[Pengajuan] Proses Berhasil" ;
		}
		switch($respon){
			case "00": 
				$bodymessage =  "Terimakasih " . $sender . ", <br><br>Pengajuan anda melalui email dengan subject : " . $email_subject . " untuk " . $nama_peserta . " sudah kami terima, mohon menunggu verifikasi dari kami";
				break;
			case '01': 
				$subject .= "Subject E-mail tidak benar";
				$bodymessage .=  "Subject E-mail anda tidak benar, gunakan format Subject yang sudah ditentukan";
				break;
			case '02': 
				// $subject .= "KTPA/Nomor Pensiun tidak ada";
				$subject .= "Subject Email anda tidak lengkap";
				$bodymessage .=  "Subject E-mail anda tidak benar, gunakan format Subject yang sudah ditentukan";
				break;
			case "03": 
				$subject .= "Nomor Salah";  
				$bodymessage .=  "Peserta sudah pensiun, silahkan melakukan pengajuan menggunakan nomor pensiun : " . $bp_no_pensiun;
				break;
			case "04": 
				$subject .= "Nomor Salah";
				$bodymessage .="Peserta dengan KTPA/Nomor Pensiun : " . $nomorid . " tidak ditemukan";
				break;
			case "05": 
				$subject .= "Manfaat Salah";
				$bodymessage .="Kode Manfaat tidak ditemukan";
				break;
			case '06': 
				$subject .= "Berkas tidak benar";
				$bodymessage .="Berkas harus berupa pdf";						
				break;
			case "07": 
				$subject .= "Dokumen tidak lengkap";
				// $bodymessage .="Dokumen tidak lengkap";
				$bodymessage .="Dokumen yang anda kirimkan tidak lengkap.<br>Berikut Dokumen yang kurang :<br>" . $kurang;		
				$titik = false;
				break;
			case "08": 
				$subject .= "Berkas tidak benar";
				$bodymessage .="Berkas " . $filename . " melebihi batas, ukuran maksimal berkas yang diijinkan adalah 500Kb";
				break;
			case "09": 
				$subject .= "Tidak terdaftar";
				$bodymessage .="Email anda tidak terdaftar dalam sistem kami";
				break;
			case "10": 
				$subject .= "KTPA/Pensiun tidak valid";
				$bodymessage .= $nama_peserta . " sudah meninggal";
				break;
		}
		if($titik){
			$bodymessage .=  ".";
		}
		if($respon=="00"){
			$HP = $this->CI->config->item('hp_ochannel');
			if($HP!=""){
				if(substr($HP, 0,1)=="0"){
					$HP = "62" . substr($HP, 1, strlen($HP)-1);
				}
				$text = "Terdapat pengajuan " . $email_subject . " an " . $nama_peserta . " dari " . $mitra_cabang_name . ". Mohon segera ditindaklanjuti. " . $owner . ".";
				$this->CI->load->library("restclient_sms");
				$username = $this->CI->session->userdata('USR_LOGINS');
				$arr = array("sms_message"=>$text,"sms_recipient"=>$HP,"sms_usrnam"=>$username);
				$arr = json_encode($arr);
				// debug_array($arr);
				$result1 = $this->CI->restclient_sms->sendSms($arr);
			}
		}
		$paramMail = array(
			"jenis"			=>"external", 
			"recipient"		=>$recipient, 
			"subject"		=>$subject, 
			"bodymessage"	=>$bodymessage
		);
		$this->sendMail($paramMail);
	}
	function sendMail($paramMail){
		foreach($paramMail as $key=>$value){
			if($key=="bodymessage"){
				$value .= "<br><br>Regards,<br>Administrator " . $owner;
			}
			$parameter[$key] = $value;
		}
		$this->CI->kirimemail->kirim($parameter);
		// debug_array($parameter);
		// die();
	}
	function getVersion() {
		$status = shell_exec('svnversion ' . realpath(__FILE__));

		if (preg_match('/\d+/', $status, $match)) {
			$status = $match[0]/100;
			$status = number_format($status,3);
		} else {
			$status = 0;
		}		
		return $status;
	}
	function resultArray($parameter, $key=false, $die=true){
		$hasil = "";
		foreach($parameter as $key=>$value){
			if(!$key){
				$hasil = $key  . "->" . $value ."<br>";	
			}else{
				$hasil = $key ."<br>";
			}
			
		}
		return $hasil;
		if($die){
			die();
		}
	}
    function notifyUser($type, $nomor, $usr_sender, $title, $body, $id_modul=null){
		$this->CI->load->model("m_master");
        $user = $this->CI->crud->getLogin($nomor, $type);
        if($user) {
            if($user->num_rows()>0){
                $arr = array("USR_LOGINS");
                $row = $user->row();
                foreach($arr as $key){
                    ${$key} = $row->$key;
                }
                $this->CI->crud->insertInbox($title, $body, $USR_LOGINS, $usr_sender, $id_modul);
            }
        }
	}
	function nodata($JUDULS=null, $echo=true){
        $html = getCss();
		$html .=createportlet(array("content"=>"<center>Data Tidak Ditemukan!</center>","title"=>"Laporan","caption_helper"=>$JUDULS, "icon"=>"fas fa-exclamation-circle"));
		if($echo){
			echo $html;
		}else{
			return $html;
		}
        
	}
	function vmenu(){
		// ini_set('display_errors', 0);
		$username = $this->CI->session->userdata('USR_LOGINS');
		$levels   = $this->CI->session->userdata('USR_LEVELS');
		$manfaat = $this->CI->crud->getMenu_json(1,1,null, null, false, true);
		// $this->debug_array($manfaat->result());
		// $this->debug_sql(1);
		if($manfaat->num_rows()>0){
			$http_code=200;
	
			foreach($manfaat->result() as $keyKat=>$valueKat){
				$idents = strval($valueKat->MNU_IDENTS);
				$descre = $valueKat->MNU_DESCRE;
				$parent = strval($valueKat->MNU_PARENT);
				$icon = $valueKat->MNU_ICONED;
				$routes = $valueKat->MNU_ROUTES;
	
				$arr["idents"] = $idents;
				$arr["descre"] = $descre;
				$arr["parent"] = $parent;//($parent=="0" ? "" : $parent);
				$arr["icon"] = $icon;
				$arr["routes"] = $routes;

				$arrgw[] = $arr;
			}
		}
		$menu = null;
		$navarrayn = null;
		$results = array();
		if(isset($arrgw)){
			$navarrayn = $this->convertToHierarchy($arrgw, "idents", "parent");
		}
		
		// $navarrayy = $this->GenerateNavArray($arrgw);
		// $this->levelUp($navarray);
		// $this->debug_array($navarraye, false);
		// $this->debug_array($navarrayn, false);
		// $this->debug_array($navarrayy);
		if($navarrayn!=null){
			$menu = "<ul class='menu-nav'>";
			$menu .= $this->generatemenu($navarrayn);
			$menu .= "
</ul>";
		}
		return $menu;
		// $this->debug_array($menu);
	}

	function convertToHierarchy($results, $idField='id', $parentIdField='parent', $childrenField='child') {
		$hierarchy = array(); // -- Stores the final data
	
		$itemReferences = array(); // -- temporary array, storing references to all items in a single-dimention
		$level = 0;
		foreach ( $results as $item ) {
			$id       = $item[$idField];
			$parentId = $item[$parentIdField];
			// $levelId = $item[$parentIdField];
	
			if (isset($itemReferences[$parentId])) { // parent exists
				if(isset($itemReferences[$parentId]["level"])){
					$level = $itemReferences[$parentId]["level"]+1;
				}else{
					$level = 0;
				}
				$item["level"] = $level;
				$itemReferences[$parentId][$childrenField][$id] = $item; // assign item to parent
				$itemReferences[$id] =& $itemReferences[$parentId][$childrenField][$id]; // reference parent's item in single-dimentional array
				// $this->debug_array();

			} elseif (!$parentId || !isset($hierarchy[$parentId])) { // -- parent Id empty or does not exist. Add it to the root
				$level = 0;
				$hierarchy[$id] = $item;
				$hierarchy[$id]["level"] = $level;
				$itemReferences[$id] =& $hierarchy[$id];

			}
		}
		unset($results, $item, $id, $parentId);
	
		// -- Run through the root one more time. If any child got added before it's parent, fix it.
		foreach ( $hierarchy as $id => &$item ) {
			$parentId = $item[$parentIdField];
			if ( isset($itemReferences[$parentId] ) ) { // -- parent DOES exist
				$itemReferences[$parentId][$childrenField][$id] = $item; // -- assign it to the parent's list of children
				unset($hierarchy[$id]); // -- remove it from the root of the hierarchy
			}
		}
		// die();
		// $this->debug_array($hierarchy);
		unset($itemReferences, $id, $item, $parentId);
	
		return $hierarchy;
	}	
	function GenerateNavArray($arr, $parent = 0){
        $pages = Array();
        foreach($arr as $page){
			if($page["parent"]==0){
				// $page["level"] = 0;
			}
            if($page['parent'] == $parent){
                $page['child'] = isset($page['child']) ? $page['child'] : $this->GenerateNavArray($arr, $page["idents"]);
				$pages[] = $page;
				// $this->debug_array($pages, false);
			}
		}
		return $pages;
	}
	function generatemenu($nav, $level=0, $parent_before=0, $vertical=true){
		$menu =null;
		// $this->debug_array($nav);
        foreach($nav as $page){
			// $this->debug_array($page);
			$v_icon = null;
			$bullet = null;
			$icon = $page["icon"];
			// $this->debug_array($icon);
			$descre = $page["descre"];
			$parent = $page["parent"];
			$level = $page["level"];
			
			$routes = ($page["routes"]=="" ? "#" : $page["routes"]);
			if(trim($icon)!=""){
				$v_icon = "
<i class=\"menu-icon fas fa-" . $icon. "\"></i>";
			}
			$menu_item_active = "menu-item-active";
			if(isset($page['child'])){
			// if($page['child']!=null){
				$hover = "data-menu-toggle='hover'";
				$menu_arrow = "
<i class='menu-arrow'></i>";
				$menu_item_active .= " menu-item-submenu";
				$menu_toggle = "menu-toggle";
			}else{
				$hover = null;
				$menu_arrow = null;
				$menu_toggle = null;
			}
			switch($level){
				case 0:
					$bullet = null;
					break;
				case 19:
					$bullet = "
					<i class='menu-bullet menu-bullet-line'>
						<span></span>
					</i>
					";
					break;
				default:
					switch($level){
						case 1:
							$menu_bullet = "line";
							break;
						case 2:
							$menu_bullet = "dot";
							break;
						case 3:
							$menu_bullet = "dot";
							break;
						default:
							break;
					}
					$bullet = "
					<i class='menu-bullet menu-bullet-".$menu_bullet."'>
						<span></span>
					</i>
					";
					break;
			}
			if($level!=0){
				$menu .= "
				<div class='menu-submenu'>
				<i class='menu-arrow'></i>
				<ul class='menu-subnav'>
				";
			}
			$menu .= "
<li class='menu-item ".$menu_item_active."' aria-haspopup='true' " . $hover . ">";
			$menu .= "	
<a href='/".$routes."' class='menu-link " . $menu_toggle . "'>";
			$menu .= ($bullet=="" ? $v_icon : $bullet );
			$menu .= "		
<span class='menu-text'>".$descre."</span>";
			$menu .= $menu_arrow;
			$menu .= "
</a>";

			if(isset($page['child'])){
				$menu .= $this->generatemenu($page['child'], $level, $parent);
			}
			$menu .= "
</li>";
			if($level!=0){
				$menu .= "
				</ul>
				</div>
				";
			}
		}
		return $menu;
	}
	
    function GenerateNavaArray($arr, $parent = 0, $parent_before=0, $level=0){
		// $this->debug_array($arr);
        $pages = Array();
        foreach($arr as $page)
        {
			// $this->debug_array("\nMenu Descre: " . $page['descre'] . ">>Page Parent: " . $page['parent'] . ">>Parent Array: " . $parent . ">>Idents: " . $page['idents'] . "\n", false);
			if($page['parent']==0){
				$level = 0;
			}
            if($page['parent'] == $parent)
            {
				if($parent_before==0){
					if($page['parent']==0){
						$level = 0;
					}else{
						// debug_array($parent);
						$level = 1;
					}
				}else{
					if($page['parent']==$parent){
						$this->debug_array($arr);
						// $this->debug_array($page["descre"] . $page["parent"] . $parent_before);
						$level = ($level==0 ? 1 : $level) + 1;	
					}
				}
				$page['level'] = $level;
                $page['child'] = isset($page['child']) ? $page['child'] : $this->GenerateNavArray($arr, $page['idents'], $parent, $level);
                $pages[] = $page;
			}
        }
        return $pages;
	}
	function getInboxunread(){
		$rslLast = $this->CI->crud->getInboxunread();
		return $rslLast;
	}
	function getUserActivitylast_list($limit=5){
		$latestactivity = null;
		$arrColor = array("warning", "success", "danger", "info","primary");
		$rslLast = $this->CI->crud->getUserActivitylast_list($limit);
		$loop = 0;
		if($rslLast->num_rows()>0){
			// $arrField = array(
			// 	"log_address"=>$this->lang->line("sessi_berakhir"),
			// 	"log_from"=>"Modul",
			// 	"log_table"=>"Table",
			// 	"log_field"=>"Field/WS",
			// 	"log_action"=>"",
			// 	"log_usrnam"=>"Pengguna",
			// 	"log_usrdat"=>"Tanggal Perubahan"
			// );	
			foreach($rslLast->result() as $keyE=>$valueE){
				$log_idents = $valueE->log_idents;
				$log_action = $valueE->log_action;
				$log_usrdat = $valueE->log_usrdat;
				$log_fkidents = $valueE->log_fkidents;
				
				$log_action_array = json_decode($log_action);
				if(isset($log_action_array->action)){
					$idnya = null;
					if($log_fkidents!=""){
						$idnya = " (" . $log_fkidents . ")";
					}
					// $latestactivity .= $log_action_array->action . "<br>" . $log_usrdat;
					$latestactivity .='
					<div class="d-flex align-items-center bg-light-'.$arrColor[$loop].' rounded p-5 gutter-b">
						<div class="d-flex flex-column flex-grow-1 mr-2">
							<a href="#" class="font-weight-normel text-dark-75 text-hover-primary font-size-lg mb-1">'  . $log_action_array->action . $idnya . '</a>
							<span class="text-muted font-size-sm">'.$log_usrdat.'</span>
						</div>
						<span class="font-weight-bolder text-danger py-1 font-size-lg">
							<a href="javascript:jvViewLog('.$log_idents.')"><i class="fas fa-eye"></i></a>
						</span>
					</div>';
				}
				$loop++;
			}
		}
		return $latestactivity;
	}
	function levelUp(&$array, $level = 0, $count=0) {
	//               ^-- See that one? that's the  magic.
		// $count = 0;
		// if($level!=0){
			// $this->debug_array($array, false);

			if($array!=null){
				$countgw = count($array);
				debug_array($count, false);
				if($count<$countgw){
					$level = $level + 1;
					$array['level'] = $level;
				}
			}
			// $array = array_merge($array, array("level"=>$level));
		// }
	
		foreach($array as $key => &$value) {
		//                        ^-- important to add that & here too
			if(is_array($value)) {
				// if(isset($value["child"])){
				// 	$countgw = count($value["child"]);
				// }
				$this->levelUp($value, $level, $count);
				$count++;
			}
		}
	}
}
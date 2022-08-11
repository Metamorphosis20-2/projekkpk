<?php

	/*
	====================================================
	Penggunaan : 
	====================================================
	====================================================
	variabel utama :
	====================================================
	dat : tanggal
	num : numerik
	txt : text
	hid : hidden
	tim : waktu
	txa : textarea
	cmb : combobox
	view : hanya liat saja
	chk : checkbox
	viwfil : file (lihat)
	fil : file
	pwd : password
	rdb : radio button
	rdv : radio button
	ddt : data defined tree
	udi : user defined input
	udf : user defined input (full)

	====================================================
	Fungis
	====================================================
	generateTabjqx  : generate tab (jqx)
	generateBreadcrumb : buat breadcrumb
	buildInput : buat input


	*/
	function generateTabjqx($arrParameter){
		$CI = get_instance();
		$modal = false;
		$bentuk = "tab";
		$toggle = "tab";
		$tabscript = null;
		$atas = null;
		$id = "dTabs";
		$clsTabContent = null;
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		switch ($bentuk) {
			case 'tab':
				if(isset($ajax)){
					$post_tab = "";
					if($ajax){
						if(isset($utama)){
							$post_tab = "
							$('#imgPROSES').show();
							$('#windowProses').jqxWindow('open');
							$.post('" . $utama . "/'+loadurl, function( data ) {
								$(targ).html(data);
								$('#imgPROSES').hide();
								$('#windowProses').jqxWindow('close');
							});
							";
						}
						$tabscript = "
						<script>
							$(document).ready(function(){
								$('#windowProses').jqxWindow({isModal: true, autoOpen: false, height: '320px', width:'410px', animationType:'none', maxWidth: '900', zIndex:'99999'});
								$('[data-toggle=\"$toggle\"]').click(function(e) {
								  	var \$this = \$(this), loadurl = \$this.attr('href').replace('#',''), targ = \$this.attr('data-target');
									" . $post_tab . "
									\$this.tab('show');				
									return false;
								});";
						if(isset($scriptinit)){
							$tabscript .= $scriptinit;
						}
						$tabscript .="		
							});
						</script>
						";
					}
				}				
				$atas  .= "<ul class='nav nav-tabs' id='".$id."' role='tablist'>";
				$isi = "";
				$loop = 1;
				foreach($arrTabs as $key=>$nilai){
					$style = "";
					$active = "";
					if($loop==1){
						$style = "active";
						$active = "show active";
						// $isi = "<div class='tab-content' style='height:100%;padding-top:10px'>";
						$isi = "<div class='tab-content mt-5' style='height:100%'>";
					}
					$arrKey = explode("^", $key);
					if(isset($arrKey[1])){
						$tabgw = $arrKey[1];
					}else{
						$tabgw = $arrKey[0];
					}
					$namatab = strtolower(str_replace(" ", "", $tabgw));
					if(strpos($key,"^")==0){
						// $atas .= "<li " . $style . "><a href='#".$namatab."' data-toggle='tab'>" . $key . "</a></li>";	

						$atas .= "
						<li class='nav-item'>
							<a class='nav-link ".$style ."' id='li_" . $namatab . "' data-toggle='".$toggle."' href='#".$namatab."' data-target='#".$namatab."'>
								<span class='nav-text'>" . $key . "</span>
							</a>
						</li>
						";									
					}else{
						$arrKey = explode("^", $key);
						$image = $arrKey[0];
						$texts = $arrKey[1];
						$fayesno = substr($image, 0,4)=="fas " ? 'true' : 'false';
						if($fayesno=="false"){
							$gambar = "<img style='float: left;' width='25' height='25' src='/resources/img/". $image."' alt='' class='small-image'/>";	
						}else{
							$arrImage = explode(" ", $image);
							$gambar = "<i class=\"fas " . $arrImage[1] . "\"></i>&nbsp;&nbsp;";
						}
						if($gambar!=null){
							$gambar = "
							<span class='nav-icon'>
								". $gambar . "
							</span>
							";
						}
						// $atas .= "<li " . $style . "><a href='#".$namatab."' data-toggle='".$toggle."' data-target='#".$namatab."'>" . $gambar . $texts . "</a></li>";

						$atas .= "
						<li class='nav-item'>
							<a class='nav-link ".$style ."' id='li_" . $namatab . "' data-toggle='".$toggle."' href='#".$namatab."' data-target='#".$namatab."'>
								".$gambar."
								<span class='nav-text'>" . $texts . "</span>
							</a>
						</li>
						";				
					}

					foreach($nilai as $keyval => $value){
						if($keyval=="data"){
							if($value=="ajax"){
								$value ="<img src='" . base_url(IMAGES.'ajax-loader.gif') ."' />";
							}
							$isi .= "<div class='tab-pane fade ".$active. " " . $clsTabContent ."' id='".$namatab."' role='tabpanel' aria-labelledby='li_" . $namatab . "' style='height:100%'>";
							$isi .= $value;
							$isi .= "</div>";
						}
					}
					if($loop==count($arrTabs)){
						$atas .="</ul>";
						$isi .= "</div>";
					}
					$loop++;
				}
				$tabcontent = $atas;
				$tabcontent .= $isi;
				break;
			case 'div':
				$tabcontent = generateDivjqx($arrParameter);
				break;
			case 'accordion':
				$tabcontent = generateAccordion($arrParameter);
				break;
		}		

		$content = $tabscript;
		$content .= $tabcontent;
    	return $content;
	}
	function generateAccordion($arrParameter){
		$id = "accordion";
		$showall = false;
		$loop = 0;
		$content = null;
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
        $content = '<div class="accordion accordion-toggle-arrow" id="'.$id.'">';
		// debug_array($arrTabs);
		foreach($arrTabs as $key=>$value){
			$show = null;
			if($showall){
				$show = "show";
			}else{
				if($loop==0){
					$show = "show";
				}
			}
			$gambar = null;
			$arrKey = explode("^", $key);
			if(count($arrKey)>1){
				$icon = $arrKey[0];
				$texts = $arrKey[1];
				$fayesno = substr($icon, 0,4)=="fas " ? 'true' : 'false';
				if($fayesno=="false"){
					$gambar = "<img style='float: left;' width='25' height='25' src='/resources/img/". $icon."' alt='' class='small-image'/>";	
				}else{
					$arrImage = explode(" ", $icon);
					$gambar = "<i class=\"fas " . $arrImage[1] . "\"></i>&nbsp;&nbsp;";
				}
			}else{
				$texts = $key;
			}
			$content .= '<div class="card">';
			$content .= '
			<div class="card-header" id="heading'.$loop.'">
				<div class="card-title" data-toggle="collapse" data-target="#collapse'.$id.'_'.$loop.'">
					'. $gambar. ' ' . $texts .'
				</div>
			</div>
			<div id="collapse'.$id.'_'.$loop.'" class="collapse '.$show.'" data-parent="#'.$id.'">
				<div class="card-body">
				'.$value["data"].'
				</div>
			</div>			
			';
			$content .= '</div>';
			$loop++;
		}
		$content .= '</div>';
		return $content;
	}
	function old_generateTabjqx($arrParameter){
		$CI = get_instance();
		$modal = false;
		$bentuk = "tab";
		$toggle = "tab";
		$tabscript = "";
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		$atas = "";
		if(isset($button)){
			$atas = "
			<div style='height:100%;padding:0px 15px 0px 15px'>
			<div class='row' style='height:37px'>".$button."</div>
			<div class='row' style='height:92%;padding-top:10px'>
			";
		}	
		switch ($bentuk) {
			case 'tab':
				if(isset($ajax)){
					$post_tab = "";
					if($ajax){
						if(isset($utama)){
							$post_tab = "
							$('#imgPROSES').show();
							$('#windowProses').jqxWindow('open');
							$.post('" . $utama . "/'+loadurl, function( data ) {
								$(targ).html(data);
								$('#windowProses').jqxWindow('close');
							});
							";
						}
						$tabscript = "
						<script>
							$(document).ready(function(){
								$('[data-toggle=\"$toggle\"]').click(function(e) {
								  	var \$this = \$(this), loadurl = \$this.attr('href').replace('#',''), targ = \$this.attr('data-target');
									" . $post_tab . "
									\$this.tab('show');				
									return false;
								});";
						if(isset($scriptinit)){
							$tabscript .= $scriptinit;
						}
						$tabscript .="		
							});
						</script>
						";
					}
				}
				$clsTabContent = "tabinline";
				if($modal){
					$tabscript = "
					<script>
						$(document).ready(function(){
							toolbar = 0;
							$('*[id*=toolbar]:visible').each(function() {
								toolbar = 50;
							});

							modalHeight = document.getElementById('customWindowContent').offsetHeight;
							$('.tabmodal').css('height', modalHeight-toolbar-75);
							$('.tab-content').css('height', modalHeight-toolbar-65);
					";
					if(count($arrTabs)>1){
						$tabscript .= "
								$('#dTabs a').click(function (e) {
									$(this).tab('show');
									e.preventDefault();
								});
						";
					}
					$tabscript .= "
						});
					</script>	";
					$clsTabContent = "tabmodal";
				}
				$atas  .= "<ul class='nav nav-tabs' id='dTabs'>";
				$isi = "";
				$loop = 1;
				foreach($arrTabs as $key=>$nilai){
					$style = "";
					$active = "";
					if($loop==1){
						$style = "class='active'";
						$active = "active in";
						// $isi = "<div class='tab-content' style='height:100%;padding-top:10px'>";
						$isi = "<div class='tab-content' style='height:calc(100% - 52px)'>";
					}
					$arrKey = explode("^", $key);
					if(isset($arrKey[1])){
						$tabgw = $arrKey[1];
					}else{
						$tabgw = $arrKey[0];
					}
					$namatab = strtolower(str_replace(" ", "", $tabgw));
					if(strpos($key,"^")==0){
						$atas .= "<li " . $style . "><a href='#".$namatab."' data-toggle='tab'>" . $key . "</a></li>";				
					}else{
						$arrKey = explode("^", $key);
						$image = $arrKey[0];
						$texts = $arrKey[1];
						$fayesno = substr($image, 0,4)=="fas " ? 'true' : 'false';
						if($fayesno=="false"){
							$gambar = "<img style='float: left;' width='25' height='25' src='/resources/img/". $image."' alt='' class='small-image'/>";	
						}else{
							$arrImage = explode(" ", $image);
							$gambar = "<i class=\"fas " . $arrImage[1] . "\"></i>&nbsp;&nbsp;";
						}
						
						$atas .= "<li " . $style . "><a href='#".$namatab."' data-toggle='".$toggle."' data-target='#".$namatab."'>" . $gambar . $texts . "</a></li>";
						foreach($nilai as $keyval => $value){
							
							if($keyval=="data"){
								if($value=="ajax"){
									$value ="<img src='" . base_url(IMAGES.'ajax-loader.gif') ."' />";
								}
								// $isi .= "<div class='tab-pane fade ".$active. "' id='".$namatab."'  style='height:100%'>";
								// $isi .= "<div class='tab-pane fade ".$active. "' id='".$namatab."' style='height:calc(100% - 300px)'>";
								$isi .= "<div class='tab-pane fade ".$active. " " . $clsTabContent ."' id='".$namatab."'>";
								$isi .= $value;
								$isi .= "</div>";
								// $isi .= "<div id=\"content" . $id . $loop . "\" style='" . $fontstyle . " height:100%;".$jarakatas."'>" .  $value. "</div>";
							}
						}
						if($loop==count($arrTabs)){
							$atas .="</ul>";
							$isi .= "</div>";
						}				
					}
					$loop++;
				}

		    $tabcontent = $atas;
		    $tabcontent .= $isi;
				break;
			case 'div':
				$tabcontent = generateDivjqx($arrParameter);
				break;
			default:
				break;
		}
		$content = $tabscript;
		$content .= $tabcontent;
    	return $content;
	}
	function generateBreadcrumb($arrbread){
		$CI = get_instance();
		$bread = "<div class='d-flex align-items-center flex-wrap mr-2 bcrumb'>";
		$paddingbottom = null;
		$countBread = count($arrbread);
		// debug_array($arrbread);
		if($countBread>0){
			$loop = 1;
			$bc = "<ul class='breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm'>";

			for($i=0;$i<$countBread;$i++){
				$text = "";
				$link = $arrbread[$i]['link'];
				$linka = '<a href="'.$link.'" class="text-muted">';
				$linkb = '</a>';
				if($i==($countBread-1)){
					$linka = "";
					$linkb = "";
				}

				if($loop==1){
					$text = '<i class="fas fa-home" style="padding-right:10px"></i>';
				}
				if($arrbread[$i]["text"]=="Beranda"){
					$textbread = $CI->lang->line("Beranda");
				}else{
					$textbread = $arrbread[$i]["text"];
				}
				$text .= $textbread;
				if($i!=($countBread-1)){
					$bc .= '<li class="breadcrumb-item">
					'.$linka . $text .$linkb .'
					</li>
					';
				}
				$loop++;
			}
			$bc .= "</ul>";
		}
		$judulnya = '<h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5"><b>'.$text.'</b></h5>
		<div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></div>
		';
		$bread .= '
				' . $judulnya . '
			';
		$bread .= $bc;
		
		$showconn = $CI->config->item('showconn');
		// $showconn = false;
		if($showconn){
			$bread .= "<span style='width:10px'></span>";
			include(APPPATH.'config/database'.EXT);
			$hostname =  $db['default']['hostname'];
			if(ENVIRONMENT=="production"){
				$hostname = "Production";
			}
			$database =  $db['default']['database'];
			// $bread .="<li>&nbsp;&nbsp;<b><i class=\"fas fa-database\" style='color:#ff0000'></i> [ ".strtoupper($hostname)." || ".strtoupper($database)." ] </b> </li>";
			$bread .='
				<i class="fas fa-arrow-alt-circle-right" style="padding-right:20px; color:#00b831"></i>
				<span id=showconn class="kt-subheader__desc"><i class="fas fa-database" style="color:#ff0000;padding-right:5px;'.$paddingbottom.'"></i>[<span style="color:#ffba00"> '.strtoupper($hostname).' || '.strtoupper($database).' </span>]</span>
			';
		}
		$bread .= "</div>";
    	return $bread;
		/*
		<!--begin::Info-->

		<div class="d-flex align-items-center flex-wrap mr-2 bcrumb">
			<!--begin::Page Title-->
			<h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">Dashboard</h5>
			<!--end::Page Title-->
			<!--begin::Breadcrumb-->
			<div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-4 bg-gray-200"></div>
			<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
				<li class="breadcrumb-item">
					<a href="" class="text-muted">Crud</a>
				</li>
				<li class="breadcrumb-item">
					<a href="" class="text-muted">Forms &amp; Controls</a>
				</li>
				<li class="breadcrumb-item">
					<a href="" class="text-muted">Form Widgets</a>
				</li>
				<li class="breadcrumb-item">
					<a href="" class="text-muted">Bootstrap Select</a>
				</li>
			</ul>
			<!--end::Breadcrumb-->
		</div>
		<!--end::Info-->
		*/
	}
	function buildInput($parameter){

		$CI = get_instance();
		$column = "";
		$loop = 1;
		$modul = '';
		$arrField = array();
		$debug = false;

		foreach ($parameter as $indx=>$value){
			${$indx}=$value;
		}
		if($type=="view"){
			$status = "view";
		}
		if($source=="db"){
			// $fielddetail = $CI->crud->getTableInformation($table);
			// print_r($fielddetail);
			//cari prefix field
			if ($fielddetail->num_rows() > 0){
				$onerow = $fielddetail->row();
				$prefixField = substr($onerow->Field,0,3);
				$adatable = true;
			}else{
				$adatable = false;
			}
			$arrField = $fielddetail->result_array();
		}else{
			$arrComment = $CI->common->extractjsonf($modul);

			foreach ($arrComment as $key => $value) {
				$table = $key;
				foreach ($value as $key_one => $value_one) {
					$loopsrc = 1;
					foreach($value_one as $keyd=>$valued){
						if($keyd=="fn"){
							$ottable = false;
							$fieldname = $valued;
							$datatype = $CI->crud->getTableInformation($table, null, $fieldname, null, 2);
							// echo $CI->db->last_query();
							if(count($datatype)!=0){
								$datatype = $datatype->Type;
								$ottable = true;
								$arrField = array_merge($arrField, array(array('Field'=>$fieldname, 'Type'=>$datatype)));
								if($loopsrc==1){
									$prefixField = substr($valued,0,3);
								}
							}
						}
						if($ottable=true){
							if($keyd=="ft"){
								$datatype = $valued; 
								$arrField = array_merge($arrField, array(array('Field'=>$fieldname, 'Type'=>$datatype)));
								// $prefixField = substr($valued,0,3);
							}
						}
						$loopsrc++;
					}
				}
			}
			// print_r($arrField);		
		}
		//query ke database
		$desc = "";
		// print_r($column);
		if($type!="add"){
			if($type=="view"){
				$desc = "_DESC";
			}
			if(!$column){
				if(isset($function)=="" && isset($model)==""){
					$column = $CI->crud->getTable_edit($table, $prefixField.'_IDENTS', $param);
				}else{
					$CI->load->model($model);
					$column = $CI->{$model}->{$function}($param);
				}				
			}else{
				$column = $column;	
			}
		}
		$txt = "";
		foreach($arrField as $key=>$value){
			$fldnam = $value["Field"];
			$dattyp = $value["Type"];
			$presis = getDefinition('precision', $dattyp);
			$dattyp = getDefinition('datatype', $dattyp);
			$option ="";
			$arrComment = $CI->common->extractjson($table, $fldnam);
			// echo $CI->db->last_query();
			// ============================================== start of build input
			if($arrComment!=""){
				$gs="";
				$lao ="1";
				$validator="";
				// ======== ambil value dari json ========
				foreach($arrComment as $key=>$value){
					if(!is_array($value)){
						${$key}=$value;
					}else{
						//print_r($value);
						if($key=="crud"){
							foreach($value as $keyd=>$valued){
								foreach($valued as $keyd=>$valuede){
									${$keyd} = $valuede;
								}
							}
						}
					}
				}
				$object = substr($fldnam,4,6);
				$prefixElement = $ct;
				$objname = $prefixElement . $object;
				if(!isset($cg)){
					$cg=1;
					unset($cg);
				}					
				if(isset($cr)){
					$validator = array('harusisi'=>true);
					unset($cr);
				}
				if(isset($cw)){
					$size = $cw;
					unset($cw);
				}else{
					$size = 4*$presis;				
				}
				if($column){
					${$objname} = $column->{$fldnam};
				}else{
					if($dattyp == "varchar" || $dattyp == "datetime" || strpos("X" . $dattyp, "timestamp")>0){
						${$objname} = "";
						$maxlength = $presis;
					}else{
						${$objname} = "";
					}
				}
				if($dattyp == "datetime"){
					$size = 100;
				}

				$label = $fd;
				$input = $ct;
				// kalau bukan hidden
				// if($ct!='hid'){
				$nourut = isset($cu) ? $cu : 999;
				// kalau combobox
				if($ct=="cmb"){
					if($column){					
						${$objname} = $column->{$fldnam.$desc};
					}
					$option = "";
					$vField = $fldnam;
					if(isset($cs)){
						if(isset($cm)){
							$model = $cm;
							unset($cm);
						}else{
							$model = "crud";
						}
						$parameter = array();
						if(isset($cp)){
							$parameter = explode('^',$cp);
							unset($cp);
						}
						$option = call_user_func_array(array($CI->$model,$cf), $parameter);
						unset($cs);
						unset($cf);							
					}else{
						if(isset($optionC)){
							if(is_array($optionC)){
								$dapet = false;
								foreach ($optionC as $key => $value){
									if($object==$key){
										${'opt'. $object} = $value;
										$dapet = true;
									}
								}
								if($dapet==true){
									$option = ${'opt'. $object};
									$vField = "";
								}
							}
						}							
					}
					// 
					if($size=="50"){
						$size =100;
					}
					$arrTable[] =array('group'=>$cg, 'urutan'=>$nourut, 'type'=> isset($status) ? 'view' : 'cmb', 'label'=>$label, 'namanya' => $objname,'size' => $size, 'option' => $option, 'value'=> isset($column) ? (isset($status) ? ${$objname} : ${$objname}) : '147');
					$txt .=  '$arrTable[] = array(\'group\'=>' . $cg .', \'urutan\'=>'. $nourut .', \'type\'=> isset($status) ? \'view\' : \'cmb\', \'label\'=>\''. $label. '\', \'namanya\' =>\''. $objname. '\',\''. $size. '\' => \'50\',\'option\' => $option, \'value\'=> isset($column) ?(isset($status) ? $'. $objname .' : $' . $objname .') : \'0\');
';
				}else{
					if($ct=="txa"){
						$arraynya=array('group'=>$cg, 'urutan'=>$nourut, 'type'=> isset($status) ? 'view' : 'txa', 'maxlength'=>$presis, 'label'=> $label, 'namanya'=> $objname, 'size'=> $size, 'value'=> isset($column) ? ${$objname} : '');	
						if(isset($cy)){
							$arraynya = array_merge($arraynya, array('ckeditor'=> isset($cy) ? $cy : ''));
						}
						$arrTable[] = 
						$txt .= '$arrTable[] = array(\'group\'=>' . $cg .', \'urutan\'=>' . $nourut . ', \'type\'=> isset($status) ? \'view\' : \'' . $input .'\', \'maxlength\'=>\'' . $presis .'\', \'label\'=> \''. $label.'\', \'namanya\'=> \'' .$objname .'\', \'ckeditor\'=> isset($cy) ? $cy : \'\', \'size\'=> \'' .$size . '\', \'value\'=> isset($column) ? $' . $objname . ' : \'\');
';						
					}else{
						// echo $input ."<br>";
						switch ($input) {
							case 'hid':
								$status_e = "edt";
								break;
							default:
								$status_e = isset($status) ? $status : "edit";
								break;
						}
						if(isset($status)){
							$typeInput = $status_e=="view" ? "view" : $input;		
						}else{
							// if($input=="hid"){
							// 	$input = "txt";
							// }
							$typeInput = $input;
						}
						$arrInputTables = array('group'=>$cg, 'urutan'=>$nourut, 'type'=> $typeInput, 'maxlength'=>$presis, 'label'=> $label, 'namanya'=> $objname, 'size'=> $size, 'value'=> isset($column) ? ${$objname} : '');	
						if(isset($ci)){
							$arrInputTables = array_merge($arrInputTables, array('tagsinput'=>isset($ci) ? $ci : ''));
						}
						$arrTable[] = $arrInputTables;
						$txt .= '$arrTable[] = array(\'group\'=>' . $cg .', \'urutan\'=>' . $nourut . ', \'type\'=> isset($status) ? \'view\' : \'' . $input .'\', \'maxlength\'=>\'' . $presis .'\', \'label\'=> \''. $label.'\', \'namanya\'=> \'' .$objname .'\', \'size\'=> \'' .$size . '\', \'value\'=> isset($column) ? $' . $objname . ' : \'\');
';
					}
				}
				unset($cu);
				unset($ci);
			}
			$loop++;
		}
		// ============================================== end of build input
		if($debug=="true"){
			debug_array($txt);
		}else{
			return $arrTable;	
		}
		
	}
	function getArrInput($detail){
		$inp_groups = $detail['group'];
		$inp_urutan = 999;
		if(isset($detail['urutan'])){
			$inp_urutan = $detail['urutan'];
		}
		$inp_typinp = $detail['type'];
		$inp_labels = "";
		$inp_namess = "";
		$inp_values = "";
		$inp_sizess = "";
		if(isset($detail['label'])){
			$inp_labels = $detail['label'];
		}
		if(isset($detail['namanya'])){
			$inp_namess = $detail['namanya'];	
		}		
		if(isset($detail['size'])){
			$inp_sizess = $detail['size'];	
		}
		if(isset($detail['value'])){
			$inp_values = $detail['value'];	
		}
		$arrInput = array('group'=>$inp_groups, 'urutan'=>$inp_urutan, 'type'=> $inp_typinp, 'label'=> $inp_labels, 
											'namanya'=> $inp_namess, 'size'=> $inp_sizess, 'value'=> $inp_values);

		if(isset($detail['events'])){
			$arrInput = array_merge($arrInput, array('events'=> $detail['events']));
		}
		if(isset($detail['placeholder'])){
			$arrInput = array_merge($arrInput, array('placeholder'=> $detail['placeholder']));
		}
		if(isset($detail['style'])){
			$arrInput = array_merge($arrInput, array('style'=> $detail['style']));
		}
		if(isset($detail['colInput'])){
			$arrInput = array_merge($arrInput, array('colInput'=> $detail['colInput']));
		}
		if(isset($detail['colinputform'])){
			$arrInput = array_merge($arrInput, array('colinputform'=> $detail['colinputform']));
		}		
		if(isset($detail['link'])){
			$arrInput = array_merge($arrInput, array('link'=> $detail['link']));
		}
		if(isset($detail['nextto'])){
			$arrInput = array_merge($arrInput, array('nextto'=> $detail['nextto']));
		}
		if(isset($detail['divname'])){
			$arrInput = array_merge($arrInput, array('divname'=> $detail['divname']));
		}
		if(isset($detail['validation'])){
			$arrInput = array_merge($arrInput, array('validation'=> $detail['validation']));
		}
		if($inp_typinp=="cmb"){
			if(isset($detail['option'])){
				$inp_option = $detail['option'];	
			}else{
				$inp_option = array("0"=>"Array Kosong");
			}
			
			$arrInput = array_merge($arrInput, array('option' => $inp_option));
			// $arrInput = array('group'=>$inp_groups, 'urutan'=>$inp_urutan, 'type'=> $inp_typinp, 'label'=>$inp_labels, 
			// 									'namanya'=> $inp_namess,'size' => $inp_sizess, 'option' => $inp_option, 'value'=> $inp_values,
			// 									'events'=> $inp_events);
			if(isset($detail['readonly'])){
				$readonly = $detail['readonly'];	
				if($readonly){
					$arrInput = array_merge($arrInput, array("readonly"=>true));	
				}
			}
			if(isset($detail['otoheight'])){
				$otoheight = $detail['otoheight'];	
				if($otoheight){
					$arrInput = array_merge($arrInput, array("otoheight"=>true));	
				}
			}
			if(isset($detail['checkbox'])){
				$checkbox = $detail['checkbox'];	
				if($checkbox){
					$arrInput = array_merge($arrInput, array("checkbox"=>true));	
				}
			}
			if(isset($detail['cascade'])){
				$cascade = $detail['cascade'];	
				$arrInput = array_merge($arrInput, array("cascade"=>$cascade));
			}
			if(isset($detail['tags'])){
				if($detail['tags']){
					$arrInput = array_merge($arrInput, array("tags"=>true));
				}
			}
			if(isset($detail['multiple'])){
				if($detail['multiple']){
					$arrInput = array_merge($arrInput, array("multiple"=>true));
				}
			}
			if(isset($detail['validator'])){
				$inp_validt = $detail['validator'];	
				$arrInput = array_merge($arrInput, array("validator"=>$inp_validt));
			}			
		}else{
			$inp_typinp = ($inp_typinp=="viw" ? "view" : $inp_typinp);
			// $inp_maxlen = isset($detail['maxlength']) ? $detail['maxlength'] : '';
			// $inp_taginp = isset($detail['tagsinput']) ? $detail['tagsinput'] : '';
			// $inp_txtinp = isset($detail['text']) ? $detail['text'] : '';

			// $arrInput = array_merge($arrInput, array('maxlength'=>$inp_maxlen, 'text'=> $inp_txtinp));

			// $arrInput = array('group'=>$inp_groups, 'urutan'=>$inp_urutan, 'type'=> $inp_typinp, 'maxlength'=>$inp_maxlen, 
			// 									'label'=> $inp_labels, 'namanya'=> $inp_namess, 'size'=> $inp_sizess, 'value'=> $inp_values, 
			// 									'text'=> $inp_txtinp,'events'=> $inp_events);

			if(isset($detail['maxlength'])){
				$arrInput = array_merge($arrInput, array("maxlength"=>$detail['maxlength']));
			}
			if(isset($detail['text'])){
				$arrInput = array_merge($arrInput, array("text"=>$detail['text']));
			}
			if(isset($detail['dropzone'])){
				$arrInput = array_merge($arrInput, array("dropzone"=>$detail['dropzone']));
			}
			if(isset($detail['ckeditor'])){
				$inp_ckeditor = $detail['ckeditor'];	
				$arrInput = array_merge($arrInput, array("ckeditor"=>$inp_ckeditor));
			}
			if(isset($detail['rows'])){
				$arrInput = array_merge($arrInput, array("rows"=>$detail['rows']));
			}
			if(isset($detail['coltxa'])){
				$arrInput = array_merge($arrInput, array("coltxa"=>$detail['coltxa']));
			}
			if(isset($detail['jqxeditor'])){
				$inp_jqxeditor = $detail['jqxeditor'];	
				$arrInput = array_merge($arrInput, array("jqxeditor"=>$inp_jqxeditor));
			}
			if(isset($detail['tagsinput'])){
				$inp_taginp = $detail['tagsinput'];	
				$arrInput = array_merge($arrInput, array("tagsinput"=>$inp_taginp));
			}
			if(isset($detail['validator'])){
				$inp_validt = $detail['validator'];	
				$arrInput = array_merge($arrInput, array("validator"=>$inp_validt));
			}
			if(isset($detail['button'])){
				$button = $detail['button'];	
				$arrInput = array_merge($arrInput, array("button"=>$button));
			}
			if(isset($detail['readonly'])){
				$readonly = $detail['readonly'];	
				if($readonly){
					$arrInput = array_merge($arrInput, array("readonly"=>$readonly));	
				}
			}
			if(isset($detail['pilihan'])){
				$pilihan = $detail['pilihan'];	
				$arrInput = array_merge($arrInput, array("pilihan"=>$pilihan));	
			}
			if(isset($detail['asal'])){
				$asal = $detail['asal'];	
				$arrInput = array_merge($arrInput, array("asal"=>$asal));	
			}	
			if(isset($detail['digits'])){
				$digits = $detail['digits'];	
				$arrInput = array_merge($arrInput, array("digits"=>$digits));	
			}	
			if(isset($detail['max'])){
				$max = $detail['max'];	
				$arrInput = array_merge($arrInput, array("max"=>$max));	
			}
			if(isset($detail['min'])){
				$arrInput = array_merge($arrInput, array("min"=>$detail['min']));	
			}	
			if(isset($detail['otherelement'])){
				$arrInput = array_merge($arrInput, array("otherelement"=>$detail['otherelement']));	
			}			
			if(isset($detail['location'])){
				$inp_lokasi = $detail['location'];	
				if($inp_lokasi!=""){
					$arrInput = array_merge($arrInput, array("location"=>$inp_lokasi));	
				}
			}
			if(isset($detail['icon'])){
				$icon = $detail['icon'];	
				if($icon!=""){
					$arrInput = array_merge($arrInput, array("icon"=>$icon));	
				}
			}
			if(isset($detail['masked'])){
				if($detail['masked']!=""){
					$arrInput = array_merge($arrInput, array("masked"=>$detail['masked']));	
				}
			}	
			if(isset($detail['optional'])){
				if(is_array($detail['optional'])){
					$arrInput = array_merge($arrInput, array("optional"=>$detail['optional']));	
				}
			}
			if(isset($detail['decimaldigit'])){
				$arrInput = array_merge($arrInput, array("decimaldigit"=>$detail['decimaldigit']));	
			}				
		}		
		return $arrInput;
	}
	function generateForm($parameter, $createtab=true, $hidTRNSKS=true){
		$CI = get_instance();
		//================================
		// deklarasi default awal
		//================================
		$modal = false;
		$multicolumn = false;
		$column = "";
		$modul ="";
		$debug = false;
		$save = true;
		//form related
		//================================
		$nameForm = "formgw";
    	$classForm = "form";
    	//================================
		$width = 710;
		$tabname  = "";
		$source ="";
		$table = "";
		$form_create = true;
		$heightnya = "96%";
    	//================================
		foreach ($parameter as $indx=>$value){
			${$indx}=$value;
		}
		if(!isset($param)){
			$param=0;
		}
		if(!isset($arrTable)){
			$arrPass = array('source'=>$source, 'type'=>$type, 'param'=>$param, 'modul'=>$modul,'table'=>$table, 'debug'=>$debug, 'column'=>$column);
			if(isset($model)){
				$arrPass = array_merge($arrPass, array('model'=>$model));
			}
			if(isset($function)){
				$arrPass = array_merge($arrPass, array('function'=>$function));
			}				
			$arrTable = buildInput($arrPass);	
		}

		foreach ($arrTable as $key => $row) {
			$group_urutan[$key]  = $row['group'];
			$elemen_urutan[$key] = $row['urutan'];
		}
		array_multisort($group_urutan, SORT_ASC, $elemen_urutan, SORT_ASC, $arrTable);
		foreach($arrTable as $detail){
			if(isset($detail["validation"])){
				if($detail["type"]=="cmb" && isset($detail["tags"])){
					$detail["validation"] = array_merge($detail["validation"], array("tags"=>$detail["tags"]));
				}
				
				$inputValidation[$detail["namanya"]] = $detail["validation"];
			}
		}
		if($createtab){
			$grouptab = 0;
			$grouptab_temp = 1;
			$looptab =1;
			$ketemu = 1;
			$namatab = "";
			$arrTabs =array();
			$arrCount = count($arrTable);
			
			// looping form input yang didapat dari controller
			$resetX = false;
			foreach($arrTable as $detail){
				$inp_groups = $detail['group'];//initial group
				// if($looptab>1 && count($arrTable)>1){
				if($arrCount>1){
					$namatab = "Tab " . $grouptab_temp;
					if(is_array($tabname)){
						$namatab = $tabname[$grouptab_temp];
					}
					// kalau group != group sebelumnya atau jumlah loop = jumlah arrTable
					
					if($inp_groups!=$grouptab_temp){
						// ${'html'.$ketemu} = generateinput(array('arrTable'=>$arrTableX));
						$arrInput = array('multicolumn'=>$multicolumn, 'arrTable'=>$arrTableX);
						if(isset($colLabel)){
							$arrInput = array_merge($arrInput, array('colLabel'=>$colLabel));
						}
						if(isset($colInput)){
							$arrInput = array_merge($arrInput, array('colInput'=>$colInput));
						}
						$isinya = generateinput($arrInput);
						if($multicolumn){
							if(isset($arrInput["arrTable"])){
								$number_of_green_fruit = 0;
								for ($row = 0; $row < count($arrInput["arrTable"]); $row++) {
									if($arrInput["arrTable"][$row]["type"]=="hid") {
										 $number_of_green_fruit++;
									}
								}
								$element_jumlah = count($arrInput["arrTable"]) - $number_of_green_fruit;
	
								if($element_jumlah%2!=0){
									$isinya .= "</div>";
								}
							}
						}
						${'html'.$ketemu} = $isinya;
						$arrTabs = array_merge($arrTabs, array($namatab=>array('data'=>${'html'.$ketemu})));
						unset($arrTableX);
						$arrTableX[] = getArrInput($detail);
						if($looptab==$arrCount){
							$namatab = "Tab " . $inp_groups;
							if(is_array($tabname)){
								$namatab = $tabname[$inp_groups];
							}

							// ${'html'.$ketemu} = generateinput(array('arrTable'=>$arrTableX));

							$arrInput = array('multicolumn'=>$multicolumn, 'arrTable'=>$arrTableX);
							if(isset($colLabel)){
								$arrInput = array_merge($arrInput, array('colLabel'=>$colLabel));
							}
							if(isset($colInput)){
								$arrInput = array_merge($arrInput, array('colInput'=>$colInput));
							}
							if(isset($colinputform)){
								$arrInput = array_merge($arrInput, array('colinputform'=>$colinputform));
							}							
							$isinya = generateinput($arrInput);

							${'html'.$ketemu} = $isinya; 						

							$arrTabs = array_merge($arrTabs, array($namatab=>array('data'=>${'html'.$ketemu})));
						}
					}else{
						$arrTableX[] = getArrInput($detail);
						// ${'html'.$ketemu} = generateinput(array('arrTable'=>$arrTableX));
						$arrInput = array('multicolumn'=>$multicolumn, 'arrTable'=>$arrTableX);
						if(isset($colLabel)){
							$arrInput = array_merge($arrInput, array('colLabel'=>$colLabel));
						}
						if(isset($colInput)){
							$arrInput = array_merge($arrInput, array('colInput'=>$colInput));
						}
						if(isset($colinputform)){
							$arrInput = array_merge($arrInput, array('colinputform'=>$colinputform));
						}
						$isinya = generateinput($arrInput);

						if($multicolumn){
							if(isset($arrInput["arrTable"])){
								$number_of_green_fruit = 0;
								for ($row = 0; $row < count($arrInput["arrTable"]); $row++) {
									if($arrInput["arrTable"][$row]["type"]=="hid") {
										 $number_of_green_fruit++;
									}
								}
								$element_jumlah = count($arrInput["arrTable"]) - $number_of_green_fruit;
	
								if($element_jumlah%2!=0){
									$isinya .= "</div>";
								}
							}
						}
						${'html'.$ketemu} = $isinya;

						$arrTabs = array_merge($arrTabs, array($namatab=>array('data'=>${'html'.$ketemu})));
					}
				}else{
					//kalau cuman satu elemen
					$namatab = "Tab " . $inp_groups;
					if(is_array($tabname)){
						$namatab = $tabname[$inp_groups];
					}					
					if(!isset($arrTableX)){
						$arrTableX = array();
						$arrTableX[] = getArrInput($detail);
					}

					${'html'.$ketemu} = generateinput(array('arrTable'=>$arrTableX));
					$arrTabs = array_merge($arrTabs, array($namatab=>array('data'=>${'html'.$ketemu})));					
				}
				$looptab++;
				$grouptab_temp = $inp_groups;//group lama
			}
			$arrTabs = array('id'=>'Dashboard',"modal"=>$modal, 'arrTabs'=> $arrTabs);

			if(isset($font)){
				$arrTabs = array_merge($arrTabs,array('font'=>$font));
			}

			if(isset($tabwidth)){
				$arrTabs = array_merge($arrTabs, array('tabwidth'=>$tabwidth));
			}
			if(isset($tabheight)){
				$arrTabs = array_merge($arrTabs, array('tabheight'=>$tabheight));
			}
			if(isset($bentuk)){
				$arrTabs = array_merge($arrTabs, array('bentuk'=>$bentuk));
			}
			if(isset($scriptinit)){
				$arrTabs = array_merge($arrTabs, array('scriptinit'=>$scriptinit));
			}
			if(isset($funcinit)){
				$arrTabs = array_merge($arrTabs, array('funcinit'=>$funcinit));
			}
			if(isset($button)){
				$arrTabs = array_merge($arrTabs, array('button'=>$button));
				// $content = ;
			}
			$isinya = generateTabjqx($arrTabs);

		}else{
			$arrInput = array('multicolumn'=>$multicolumn, 'arrTable'=>$arrTable);
			if(isset($colLabel)){
				$arrInput = array_merge($arrInput, array('colLabel'=>$colLabel));
			}
			if(isset($colInput)){
				$arrInput = array_merge($arrInput, array('colInput'=>$colInput));
			}
			if(isset($colinputform)){
				$arrInput = array_merge($arrInput, array('colinputform'=>$colinputform));
			}
			$isinya = generateinput($arrInput);
		}
		if($form_create){
			$attr = array('class' => $classForm, 'name' => $nameForm, 'id' => $nameForm);
			if(isset($heightnya)){
				$attr = array_merge($attr, array('style'=>'top:50px;height:'.$heightnya));
			}
			$command = "";
			if($save==true){
				if(isset($formcommand)){
					$formcommand =$formcommand;
				}else{
					$command = "save";	
					$formcommand = '/'.$command.'/' . $modul;
				}
			}
			$form_create = "
			<script>
			$(document).ready(function(){
				$('#".$nameForm."').on('submit', function (e) {
					e.preventDefault();
				});
			});
			</script>
			";
			$form_create .= form_open_multipart($formcommand, $attr);
		}else{
			$form_create = "";
		}
		// debug_array($form_create, false);
		$content = $form_create;
		$content .= $isinya;
		if(!isset($type)){
			$type = "view";
		}
		if($form_create!=""){
			if($hidTRNSKS){
				$content .= form_input(array('name' => "hidTRNSKS",'id'=> "hidTRNSKS", 'type'=>'hidden', 'value'=> (isset($type) ? $type : "view")));		
			}
		}
		if(isset($inputValidation)){
			if($type != "view"){
				$content .= generateValidator($inputValidation, $nameForm);
			}
		}
		return $content;
	}
	function createButton($arraynya=null, $initjs=true, $buttontop=true, $validate=false){
		$CI = get_instance();
		$button = "";
		$buttonnya = "";
		$function = null;
		if(is_array($arraynya)){
			foreach ($arraynya as $keyE=>$valueE){
				${$keyE} = $valueE;
			}
		}else{
			$txtSave = $CI->lang->line("save");
			$arraynya = array(array("text"=>$txtSave, "events"=>"jvSave(".$validate.")", "theme"=>"primary", "image"=>"fas fa-save", "id"=>"SaveForm"));
		}
		// if(!is_array($button)){
			// $arraynya = array(array("text"=>"Simpan", "function"=>"jvSave()", "theme"=>"primary", "icon"=>"fas fa-save"));
		// }
		$script = null;
		if($initjs){
			$script = "
			<script>
					$(document).ready(function(){
			";
		}
		$rc = false;
		$loopbutton = 0;
		foreach($arraynya as $keyN=>$valueN){
			if($rc) $buttonnya .= "&nbsp;";
			$light = false;
			$theme = "primary";
			foreach($valueN as $keyY=>$valueY){
				${$keyY} = $valueY;
			}
			if(strpos("A" . $events, "jv")!=0){
				$function = "javascript:";
			}
			$function .= $events;
			if(strpos("A" . $image, "fas")!=0){
				$image = "<i class=\"".$image."\"></i>";
			}else{
				$image = "<img src=\"/resources/icon/".$image."\">";
			}
			if($light){
				$light = "-light";
			}
			if(!isset($id)){
				$id = $loopbutton;
			}
			$buttonnya .= '<button type="submit" onclick="'.$function .'" class="btn btn'.$light.'-'.$theme.' font-weight-bolder btn-sm" id="button'.$id.'">'.$image . $text.'</button>';
			$loopbutton++;
			unset($id);
			$rc = true;
		}
		// debug_array($buttonnya);
		// $('#button_place').html('<a href="#" class="btn btn-light-primary font-weight-bolder btn-sm">Button</a>&nbsp;<a href="#" class="btn btn-light-primary font-weight-bolder btn-sm">Actions</a>')
		if($buttontop){
			$script .= "
			$('#button_place').html('".$buttonnya."');
			";
		}else{
			$script = $buttonnya;
		}

		if($initjs){				
			$script .= "});</script>";
		}
		return $script;
	}
	function createToolbarCombo($detail){
		// debug_array($detail);
		$arrCombobox = array();
		$script = null;
		$scr = null;
		$urutan = 0;
		$rc = false;
		$eventjs = null;
		$arrEvent = array(
			"change"=>"change",
			"select"=>"select2:selecting",
		);
		foreach($detail as $keydetail=>$valuedetail){
			if($rc) $scr .= "&nbsp;";
			$arrcmb = array("group"=>1, "type"=>"cmb", "urutan"=>$urutan);
			foreach($valuedetail as $keyArr=>$valueArr){
				${$keyArr} = $valueArr;
				if(isset($width)){
					$arrcmb = array_merge($arrcmb, array("size"=>$width));
				}
				if(isset($idents)){
					$arrcmb = array_merge($arrcmb, array("namanya"=>$idents));
				}
				if(isset($source)){
					$arrcmb = array_merge($arrcmb, array("option"=>$source));
				}
				if(isset($value)){
					$arrcmb = array_merge($arrcmb, array("value"=>$value));
				}
				if(isset($events)){
					foreach($events as $keyscr=>$valuescr){
						if(isset($arrEvent[$keyscr])){
							$onevent = $arrEvent[$keyscr];
						}
					}
					$eventjs .= "$('#".$idents."').on('".$onevent."', function(e) { " . $valuescr . "});";
					unset($events);
				}
			}
			
			$arrInputCombo = inputCombo($arrcmb);

			$scr .= $arrInputCombo["return"];
			
			if(count($arrInputCombo['arrCombobox'])>0){
				$arrCombobox = array_merge($arrCombobox, $arrInputCombo['arrCombobox']);
			}
			$rc = true;
			$urutan++;
		}
		$scrCombobox = null;
		$rslCombobox = generateCombobox($arrCombobox);
		
		foreach($rslCombobox as $keyCombobox=>$valueCombobox){
			${$keyCombobox} = $valueCombobox;
		}
		
		$script .= str_replace("'", "\"", $scrCombobox);
		$clean = str_replace(array("\n\r", "\n", "\r"), "", $scr);
		$clean = str_replace('"', '\"', $clean);

		$scriptCmb = "
		$('#combo_place').html('" . $clean . "');
		";
		$scriptCmb .= $eventjs;
		$scriptCmb .= $script;
		return $scriptCmb;
	}
	function createToolbar($arraynya=null, $initjs=true, $buttontop=true){
		$CI = get_instance();
		$button = "";
		$buttonnya = "";
		$function = null;
		if(is_array($arraynya)){
			foreach ($arraynya as $keyE=>$valueE){
				${$keyE} = $valueE;
			}
		}else{
			$arraynya = array(array("text"=>"Save", "events"=>"jvSave()", "theme"=>"primary", "image"=>"fas fa-save"));
		}
		// if(!is_array($button)){
			// $arraynya = array(array("text"=>"Simpan", "function"=>"jvSave()", "theme"=>"primary", "icon"=>"fas fa-save"));
		// }
		$script = null;
		if($initjs){
			$script = "
			<script>
					$(document).ready(function(){
			";
		}
		$rc = false;
		$loopbutton = 0;
		if(isset($arrButton)){
			$script .= createButton($arrButton, false);
		}
		if(isset($toolbarCombo)){
			$script .= createToolbarCombo($toolbarCombo);
		}
		// debug_array($buttonnya);
		// $('#button_place').html('<a href="#" class="btn btn-light-primary font-weight-bolder btn-sm">Button</a>&nbsp;<a href="#" class="btn btn-light-primary font-weight-bolder btn-sm">Actions</a>')

		if($initjs){				
			$script .= "});</script>";
		}
		return $script;
	}	
	function generateButton($arraynya=null, $lebartoolbar=null){
		// debug_array($arraynya);
		$CI = get_instance();
		$url = substr(uri_string(),strpos(uri_string(),"/")+1,strlen(uri_string()));
		$height = 30;
		$posisi = "float";
		$style = "";
		$iddiv = "";
		$button = "";
		$buttonnya = "";
		$check = true;
		$createToolbar = false;
		$toolbarname = "toolbarForm";
		$RTL = true;
		$jqx = true;
		if(is_array($arraynya)){
			foreach ($arraynya as $detail=>$value){
				${$detail} = $value;
			}
		}
		if($jqx){
			if(isset($lebar)){
				$width = $lebar;
			}else{
				$width = 100;	
			}
			if(!is_array($button)){
				$button = array("save"=>array("Simpan", "jvSave()","info","save",null, "fas fa-save"));	
			}
			
			if(!$createToolbar){
				// debug_array($button);
				$script = "
				<script>
						$(document).ready(function(){
				";
				foreach($button as $key=>$value){
					$theme = "danger";
					// $arrThemes = explode("^", $key);
					$texts = $key;
					if(count($value)>2){
						$theme = $value[2];
					}
					$script .= "$('#jqx" . $texts . "').jqxButton({ template: '". $theme. "', width:".$width.", height :".$height."});";	
				}
				
				$script .= "
						});
				</script>
				";
				$nbsp = "&nbsp;";
				if($posisi == "float"){
					$iddiv = "id='tombolfloat'";
					$nbsp = "";
				}else{
					if($posisi!=""){
						$iddiv = "id='".$posisi."'";
						$nbsp = "";
					}
				}
				$buttonnya = $script . $style . "<div ".$iddiv.">";
	
				foreach($button as $key=>$value){
					$image = "";
					if(isset($value[3])){
						$icon = $value[3];
						$image = "<i class=\"fas fa-".$icon."\"></i>";
					}				
					$buttonnya .= "<input type='button' onclick='" . $value[1] . "' value='".$image . "&nbsp;".$value[0]."' id='jqx".$key."'/>".$nbsp;
				}
				$buttonnya .= "</div>";			
			}else{
				$arrButton = array();
				$index = 0;
				krsort($button);
				foreach($button as $key=>$value){
					$texts = $key;				
					foreach($value as $keyButton=>$valueButton){
						${$keyButton} = $valueButton;
					}
					if(!isset($text)){
						$text = $value[0];
					}
					if(!isset($theme)){
						if(count($value)>2){
							$theme = $value[2];
						}else{
							$theme = "primary";
						}
					}
					if(!isset($function)){
						$function = $value[1];
					}
					// $script .= "$('#jqx" . $texts . "').jqxButton({ template: '', width:"..", height :".$height."});";
	
					if(!isset($icon)){
						if(isset($value[5])){
							$icon = "fa-" . $value[5]	;
						}else{
							$icon = "fa-save";
						}
					}else{
						$icon = "fa-" . $icon;
					}
					$image = "<i style='position: relative; top: -2px' class='fas ".$icon."'></i>"	;
					$butt = array(
						$index=>array(
							'text' => "&nbsp;".$text,
							'image'=> $image,
							'events'=> $function,
							'theme'=> $theme,
							'width'=> $width
						)
					);
					$arrButton = array_merge($arrButton, $butt); 
					unset($text);
					unset($image);
					unset($function);
					unset($theme);
					// unset($width);
				}
				$arrButt = array('createToolbar'=>true,'toolbarname'=>$toolbarname, 'arrButton'=>$arrButton, 'RTL'=>$RTL);
				if($lebartoolbar!=""){
					$arrButt = array_merge($arrButt, array('lebartoolbar'=>$lebartoolbar));
				}
				$buttonnya = generateToolbar($arrButt);
			}
			$otorisasi = $CI->common->otorisasi($url, $check);
			$oADD = strpos("N".$otorisasi,"A");
			$oEDT = strpos("N".$otorisasi,"E");
		}else{
			 
		}
		
    	return $buttonnya;
	}
	function generateinputfile($detail, $status="edit"){
		$CI =& get_instance();
		$CI->load->helper('file');
		$height = "400";
		$dropzone = false;
		$parallelUploads = 1;
		$maxFilesize = 1;
		$maxFiles = 1;
		$previewTemplate = 'n';
		$showfile = true;
		$filexist = true;
		$location = "/assets/documents/";
		$autoupload = true;
		$uploadMultiple = 'false';
		$icon = false;
		if(isset($detail['icon'])){
			if($detail['icon']){
				$icon = true;
			}
		}
		if(isset($detail['location'])){
			if(isset($detail['location'])!=""){
				$location = $detail['location'];	
			}
		}
		if(isset($detail['link'])){
			if($detail['link']!=""){
				$link = $detail['link'];	
			}
		}
		if(isset($detail['size'])){
			$height = $detail['size'];
		}
		if(isset($detail["dropzone"])){
			$dropzone = true;
			if(is_array($detail["dropzone"])){
				foreach($detail["dropzone"] as $keyDZ=>$valueDZ){
					${$keyDZ} = $valueDZ;
				}
			}else{
				show_error("Error Dropzone Parameter");
			}
		}
		$arrACRPDF = array('pdf','PDF');
		$arrDOCMNT = array('doc','DOC','docx','DOCX','odt','ODT');
		$arrEXCELS = array('xls','XLS','xlsx','XLSX','ods','ODS');
		$arrPPOINT = array('ppt','PPT','pptx','PPTX','odp','ODP');
		$arrIMAGES = array('jpg','JPG','jpeg','JPEG','png','PNG','gif','GIF','bmp','BMP');
		$arrVIDEOS = array('swf','SWF','flv','FLV','mp4','MP4','3gp','3GP');
		$arrLOOPNG = array('ACRPDF','DOCMNT','EXCELS','PPOINT','IMAGES','VIDEOS');

		if(isset($detail['value'])){
			$value = $detail['value'];
		}else{
			$value = null;
		}

		if($status=="view"){
			$forminputnya = "";
		}else{
			if(!$dropzone){
				if(isset($detail['size'])){
					$size = $detail['size'];
				}else{
					$size = 30;
				}
				$arrupload = array('name'=> $detail['namanya'], 'id'=> $detail['namanya'], 'size'=>$size, 'value'=> $value );
				if(isset($detail["function"])){
					if(!is_array($detail["function"])){
						if($detail["function"]=="validate"){
							$arrupload = array_merge($arrupload, array("onchange"=>"validate(this)"));
						}
					}else{
	
					}
				}
				$forminputnya = form_upload($arrupload);
			}else{
				$eventDZ = null;
				if(isset($event)){
					$eventDZ .= ", ";
					$rcDZ = false;
					foreach($event as $keyEDZ=>$valueEDZ){
						if($rcDZ) $eventDZ .=',';
						$eventDZ .= '
						dzo'.$detail['namanya'].'.on("'.$keyEDZ.'", (function(o, response) {
							'.$valueEDZ.';					
						}))
						';
						$rcDZ = true;
					}
				}
				if(!$autoupload){
					$autoProcessQueue = 'autoProcessQueue: false,';
				}else{
					$autoProcessQueue = null;
				}
				// parallelUploads: '.$parallelUploads.',
				$script1 = null;
				if($maxFiles==1){
					$script1 = '
					dzo'.$detail['namanya'].'.on("addedfile", function (file) {
						if (this.files.length > 1) {
							this.removeAllFiles()
							this.addFile(file);
						}
					}),					
					';
				}else{
					$uploadMultiple = 'true';
					$parallelUploads = 'true';
				}
				if($uploadMultiple=='true'){
					$sending = 'sendingmultiple';
				}else{
					$sending = 'sending';
				}
				$txtAcceptedFiles = null;
				// $acceptedFiles = "xls";
				if(isset($acceptedFiles)){
					$txtAcceptedFiles = 'acceptedFiles: "';
					// $acceptedFiles = array("xls", "XLS", "xlsx", "pdf");
					// debug_array(array_unique($acceptedFiles));
					
					$arrCheck = array_merge($arrACRPDF, $arrDOCMNT, $arrEXCELS, $arrPPOINT, $arrIMAGES, $arrVIDEOS);

					if(is_array($acceptedFiles)){
						$acceptedFiles = array_intersect_key($acceptedFiles, array_unique(array_map('strtolower', $acceptedFiles)));
						$rc = false;
						foreach($acceptedFiles as $keyA=>$valueA){
							if($rc) $txtAcceptedFiles .= ", ";
							$keyFound = array_search($valueA,$arrCheck);
							if(false !== $keyFound){
								$txtAcceptedFiles .= get_mime_by_extension("some.".$valueA);
								$rc=true;
							}
						}
					}else{
						$keyFound = array_search($acceptedFiles, $arrCheck);
						if(false !== $keyFound){
							$txtAcceptedFiles .= get_mime_by_extension("some.".$acceptedFiles);
						}
					}
					$txtAcceptedFiles .= '"';
					// debug_array($txtAcceptedFiles);

				}
				if(!isset($url)){
					show_error("URL Dropzone not defined");
				}
				$forminputnya  = '
				<script type=module>
				var dzo'.$detail['namanya'].';
				$(document).ready(function(){
					var e = "#dz_'.$detail['namanya'].'",
					o = $(e + " .dropzone-item");
					o.id = "";
					var n = o.parent(".dropzone-items").html();
					o.remove();
					var dzo'.$detail['namanya'].' = new Dropzone(e, {
						url: "'.$url.'",
						'.$autoProcessQueue.'
						// parallelUploads: '.$parallelUploads.',
						parallelUploads: 10,
						uploadMultiple: '.$uploadMultiple.',
						maxFiles: ' . $maxFiles . ',
						maxFilesize: '.$maxFilesize.',
						previewTemplate: '.$previewTemplate .',
						previewsContainer: e + " .dropzone-items",
						clickable: e + " .dropzone-select",
						'.$txtAcceptedFiles.'
					});
					dzo'.$detail['namanya'].'.on("addedfile", (function(o) {
						$(document).find(e + " .dropzone-item").css("display", "")
					})), dzo'.$detail['namanya'].'.on("totaluploadprogress", (function(o) {
						$(e + " .progress-bar").css("width", o + "%")
					})), dzo'.$detail['namanya'].'.on("'.$sending.'", (function(o) {
						$(e + " .progress-bar").css("opacity", "1")
					})), dzo'.$detail['namanya'].'.on("complete", (function(o) {
						var n = e + " .dz-complete";
						setTimeout((function() {
							$(n + " .progress-bar, " + n + " .progress").css("opacity", "0")
						}), 300)
					})), '.$script1.' dzo'.$detail['namanya'].'.on("success", (function(o, response) {
						$("#'.$detail['namanya'].'").val(response);
						this.removeAllFiles();
					}))' . $eventDZ . '

					// $("#nguik").click(function() {
					// 	var myDropzone = Dropzone.forElement(".dropzone");
					// 	myDropzone.processQueue();
					// });
				});
				// console.log(dzo);
				</script>
				';
				$forminputnya .= '
				<div class="dropzone dropzone-multi" id="dz_'.$detail['namanya'].'">
					<div class="dropzone-panel mb-lg-0 mb-2">
						<a class="dropzone-select btn btn-light-primary font-weight-bold btn-sm" id="nguik">Attach files</a>
					</div>
					<div class="dropzone-items">
						<div class="dropzone-item" style="display:none">
							<div class="dropzone-file">
								<div class="dropzone-filename" title="some_image_file_name.jpg">
									<span data-dz-name="">some_image_file_name.jpg</span>
									<strong>(
									<span data-dz-size="">340kb</span>)</strong>
								</div>
								<div class="dropzone-error" data-dz-errormessage=""></div>
							</div>
							<div class="dropzone-progress">
								<div class="progress">
									<div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
								</div>
							</div>
							<div class="dropzone-toolbar">
								<span class="dropzone-delete" data-dz-remove="">
									<i class="flaticon2-cross"></i>
								</span>
							</div>
						</div>
					</div>
				</div>
				';
				$forminputnya .= form_input(array('name' => $detail['namanya'],'id'=> $detail['namanya'], 'type'=>'hidden'));
				/*
            var e = "#kt_dropzone_5",
                o = $(e + " .dropzone-item");
            o.id = "";
            var n = o.parent(".dropzone-items").html();
            o.remove();
            var t = new Dropzone(e, {
                url: "http://maleo.detanto.net/transaction/sapupload/upload",
                parallelUploads: 20,
                maxFilesize: 30,
                previewTemplate: n,
                previewsContainer: e + " .dropzone-items",
                clickable: e + " .dropzone-select",
                init: function() {
                    this.on("success", function(file) { alert("Added file."); });
                }                    
            });
            t.on("addedfile", (function(o) {
                $(document).find(e + " .dropzone-item").css("display", "")
            })), t.on("totaluploadprogress", (function(o) {
                $(e + " .progress-bar").css("width", o + "%")
            })), t.on("sending", (function(o) {
                $(e + " .progress-bar").css("opacity", "1")
            })), t.on("complete", (function(o) {
                var n = e + " .dz-complete";
                setTimeout((function() {
                    $(n + " .progress-bar, " + n + " .progress").css("opacity", "0")
                }), 300)
            }))	
			*/			
			}
		}
		if($value != ""){
			$ext = explode('.',$value);
			if(isset($ext[1])){
				$extension = $ext[1];
			}else{
				$extension = $ext[0];
			}
			if(isset($extension)){
				$iconed = "<i class='fas fa-file fa-lg' style='color:#FFC0CB'></i>";
				for($x=0;$x<count($arrLOOPNG);$x++){
					$TYPEFIL = $arrLOOPNG[$x];
					if(in_array($extension, ${'arr'.$TYPEFIL})){
						switch ($TYPEFIL) {
							case 'ACRPDF':
								$iconed = "<i class='fas fa-file-pdf fa-lg' style='color:#ff0000'></i>";
								break;
							case 'DOCMNT':
								$iconed = "<i class='fas fa-file-word fa-lg' style='color:#3232FF'></i>";
								break;
							case 'EXCELS':
								$iconed = "<i class='fas fa-file-excel fa-lg' style='color:#009900'></i>";
								break;
							case 'PPOINT':
								$iconed = "<i class='fas fa-file-powerpoint fa-lg' style='color:#ffa500'></i>";
								break;
							case 'IMAGES':
								if($location!="db"){
									if($icon){
										$labelgw = "<a href='".$location .$detail['value']."' class='btn btn-success' target=_blank><i class='fas fa-paperclip fa-lg'></i></a>";
									}else{
										$labelgw = "<a href='".$location .$detail['value']."' target=_blank><img src='". $location .$detail['value']."' height='".$height."'></a>";	
									}
								}else{
									$iconed = "<i class='fas fa-file-image fa-lg' style='color:#009900'></i>";
								}								
								break;
							case 'VIDEOS':
								$iconed = "<i class='fas fa-file-video fa-lg' style='color:#9975b9'></i>";
								break;
							default :
								$iconed = "<i class='fas fa-paperclip fa-lg' style='color:#FFC0CB'></i>";
								break;
						}
					}
				}
				if(isset($link)){
					$linknya = $link;
				}else{
					$linknya = $location .$detail['value'];
				}
				$targetblank = "target=_blank";
				if(strpos("A" . $linknya,"javascript:")>0){
					$targetblank = "";
				}
				if(!isset($labelgw)){
					$labelgw = $iconed . "&nbsp;&nbsp;<a href='".$linknya."' " . $targetblank . ">". $detail['value']."</a>";
				}
				if($detail['type']=="viwfil"){
					$forminputnya = null;
				}
				$return = form_label($labelgw,'', array('class' => 'btn btn-default', 'style'=>'padding:8px; background-color:#fff;border-color:#fff')). $forminputnya . "&nbsp;&nbsp;";	
			}else{
				$return = form_label( "<a href='".$location .$detail['value']."' target=_blank>".$detail['value']."</a>",'', array('class' => 'btn btn-default','target'=>'_blank')) . $forminputnya . "&nbsp;&nbsp;";	
			}
		}else{
			if(isset($divname)){
				$forminputnya = "<div class='".$divname."'>". $forminputnya . "</div>";
			}
			$return = $forminputnya . "&nbsp;&nbsp;";	
		}
		return $return;	
	}
	function inputNumber($detail){
		$btntexts = "";
		$button = "";
		$lefts = "";
		$right = "";

		$arrInputNum = array();
		$namanya = "txtNUMINP";
		if(isset($detail['namanya'])){
			$namanya = $detail['namanya'];
		}else{
			if(isset($detail['name'])){
				$namanya = $detail['name'];
			}
		}
		if(isset($detail['value'])){
			$valuenya = $detail['value'];
		}else{
			$valuenya = 0;
		}
		$arrNumnya = array(
			'id'=>$namanya,
			'val'=>$valuenya,
			);
		if(isset($detail['readonly'])){
			$arrNumnya = array_merge($arrNumnya, array('disabled'=>$detail['readonly']));
		}
		
		if(isset($detail['decimaldigit'])){
			$arrNumnya = array_merge($arrNumnya, array('decimaldigit'=>$detail['decimaldigit']));
		}					
		if(isset($detail['size'])){
			$arrNumnya = array_merge($arrNumnya, array('width'=>$detail['size']));
		}					
		if(isset($detail['inputmode'])){
			$arrNumnya = array_merge($arrNumnya, array('inputmode'=>$detail['inputmode']));
		}
		if(isset($detail['digits'])){
			$arrNumnya = array_merge($arrNumnya, array('digits'=>$detail['digits']));
		}
		if(isset($detail['symbol'])){
			$arrNumnya = array_merge($arrNumnya, array('symbol'=>$detail['symbol']));
		}
		if(isset($detail['max'])){
			$arrNumnya = array_merge($arrNumnya, array('max'=>$detail['max']));
		}
		if(isset($detail['spinmode'])){
			$arrNumnya = array_merge($arrNumnya, array('spinmode'=>$detail['spinmode']));
		}
		$arrInputNum = array_merge($arrInputNum, array($arrNumnya));

		if(isset($detail['readonly'])){
			if($detail['readonly']){
				$urutan = "-1";	
			}else{
				$urutan = $detail['urutan'];	
			}
		}else{
			$urutan = $detail['urutan'];
		}
		if(isset($detail['button'])){
			$btnscript = "";
			if(isset($detail['button'][1])){
				$btnscript =  " onclick=\"" . $detail['button'][1] . "\"" ;
			}
			if(isset($detail['button'][0])){
				$button = $detail['button'][0];
				$arrButton = explode("^", $button);
				switch ($arrButton[0]) {
					case 'txt':
						$button = $arrButton[1];
						$btntexts = "".$button."";
						break;
					default:
						$btntexts = "<li class='fas fa-".$button."' id='btn".$detail['namanya']."'></li>";
						break;
				}
				
			}
			$lefts = "<div style='float:left'>";
			$right = "</div>";
			if($arrButton[0]!="txt"){
				$buttona = "<div class=\"input-group col-xs-5 col-md-3\" style=\"padding:0px\">";
				$buttonb = "<div class=\"input-group-addon\" style=\"border-radius:0px;padding: 2px 8px;\" " . $btnscript . ">".$btntexts."</div>";	
				$buttonc = "</div>";
				$button = $buttona.$buttonb.$buttonc;
			}else{
				$button = "&nbsp;&nbsp;".$btntexts;
			}

			unset($detail['button']);
		}
		$stylewidth = "";
		$readonly = "";
		if(isset($detail['size'])){
			$stylewidth = "width:".$detail['size']."px";
		}
		if(isset($detail['readonly'])){
			if($detail['readonly']){
				$readonly = "readonly";
			}
			if(isset($detail['size'])){
				$stylewidth = "width:".$detail['size']."px";	
			}
		}
		$return = $lefts . "<div style='margin-top: 0px;' name='" . $detail['namanya'] . "' tabindex='".$urutan."' id='" . $detail['namanya'] . "'></div>".$right . $button;

		// return $arrInputNum;
		return array('return'=>$return, 'arrNumerik'=>$arrInputNum);
	}
	function inputEditor($detail){
		$arrEditor = array();
		$namanya = "txtNUMINP";
		if(isset($detail['namanya'])){
			$namanya = $detail['namanya'];
		}else{
			if(isset($detail['name'])){
				$namanya = $detail['name'];
			}
		}

		$arrEditor = array(
			'id'=>$namanya,
			'val'=>$detail['value'],
			);

		$editoropt = $detail['jqxeditor'];
		if(isset($editoropt['create'])){
			$arrEditor = array_merge($arrEditor, array('create'=>$editoropt['create']));
		}
		if(isset($editoropt['readonly'])){
			$arrEditor = array_merge($arrEditor, array('disabled'=>$editoropt['readonly']));
		}
		if(isset($editoropt['full'])){
			$arrEditor = array_merge($arrEditor, array('full'=>$editoropt['full']));
		}
		if(isset($editoropt['toolbar'])){
			$arrEditor = array_merge($arrEditor, array('toolbar'=>$editoropt['toolbar']));
		}
		if(isset($editoropt['toolbarPosition'])){
			$arrEditor = array_merge($arrEditor, array('toolbarPosition'=>$editoropt['toolbarPosition']));	
		}
		if(isset($editoropt['width'])){
			$arrEditor = array_merge($arrEditor, array('width'=>$editoropt['width']));
		}					
		if(isset($editoropt['height'])){
			$arrEditor = array_merge($arrEditor, array('height'=>$editoropt['height']));
		}		
		return $arrEditor;
	}	
	function inputCombo($detail){
		$tags = false;
		$multiple = false;
		$nojqx = false;
		$arrCombobox = array();
		$arrEvents = array();
		$arrJs = array();
		$width = "";
		$checkbox = false;

		if(isset($detail['size'])){
			$width = $detail['size'];
		}
		if(isset($detail['readonly'])){
			$cbro = $detail['readonly'];
		}else{ 
			$cbro = false; 
		}
		if(isset($detail['multiselect'])){
			$cbmt = $detail['multiselect'];
		}else{ 
			$cbmt = false; 
		}
		if(isset($detail['otoheight'])){
			$ohei = $detail['otoheight'];
		}else{ 
			$ohei = false; 
		}
		if(isset($detail['checkbox'])){
			$checkbox = $detail['checkbox'];
		}
		if(isset($detail['multiple'])){
			$multiple = $detail['multiple'];
		}
		if(isset($detail['fontsize'])){
			$fontsize = $detail['fontsize'];
		}
		if(isset($detail['value'])){
			if(is_array($detail['value'])){
				$value_element = $detail['value'];
			}
		}
		// if(is_array($detail['option'])){
		if(!isset($detail['option'])){
			$lanjut = false;
			$return = null;
		}else{
			$lanjut = true;
		}
		if($lanjut){
			$is_json = false;
			if(array_search("json", $detail['option'])){
				foreach($detail["option"] as $keyjson=>$valuejson){
					${$keyjson} = $valuejson;
				}
				$cmbValue = "";
				if(isset($detail['value'])){
					$cmbValue = $detail['value'];
				}
				$arrCombojson = array('width'=>$width, 'json'=> array_merge($detail['option'],array('value'=>$cmbValue)));
				$is_json = true;
				$opsyen = array();
				// $arrCombobox = array_merge($arrCombobox, array($detail['namanya']=>$arrCombojson));
			}
			if(isset($detail['events'])){
				if(is_array($detail['events'])){
					foreach ($detail['events'] as $keyevents => $valueevents) {
						$arrJs = array_merge($arrJs, array($keyevents=> 'javascript:' . $valueevents));
						if(!$nojqx){
							$arrEvents = array_merge($arrEvents, array('events'=> $detail['events']));
						}
					}
				}
			}
			if(!is_array($detail['option'])){
				$opsyen = array("0"=>"Array Kosong");
			}else{
				if(!$is_json){
					$opsyen = $detail['option'];
				}
			}
			if(!$nojqx){
				$arrNojqx = array('disabled'=>$cbro,'jmlrow' =>count($detail['option']),'width'=>$width);
				if($ohei){
					$arrNojqx = array_merge($arrNojqx, array('otoheight'=>'true'));
				}
				if($cbmt){
					$arrNojqx = array_merge($arrNojqx, array('multiselect'=>'true'));
				}
				if($checkbox){
					$arrNojqx = array_merge($arrNojqx, array('checkbox'=>'true'));
				}
				if($multiple){
					$arrNojqx = array_merge($arrNojqx, array('multiple'=>'true'));
				}
				if($tags){
					$arrNojqx = array_merge($arrNojqx, array('tags'=>'true'));
				}
				if(isset($detail["tags"])){
					if($detail["tags"]){
						$arrNojqx = array_merge($arrNojqx, array('tags'=>true));
					}
				}
				if(count($arrEvents)>0){
					$arrNojqx = array_merge($arrNojqx, $arrEvents);	
				}
				if(isset($arrCombojson)){
					$arrNojqx = array_merge($arrCombojson, $arrNojqx);
				}
			}

			if(isset($detail["cascade"])){
				$arrNojqx = array_merge($arrNojqx, array("cascade"=>$detail["cascade"]));
			}
			if(isset($value_element)){
				$arrNojqx = array_merge($arrNojqx, array("value"=>$value_element));
			}
			$arrCombobox = array_merge($arrCombobox, array($detail['namanya']=> $arrNojqx));
			$arrBeforeJS = array('id'=>$detail['namanya'],'tabindex'=>$detail['urutan'], 'class'=>'form-control');
			if($multiple){
				$arrBeforeJS = array_merge($arrBeforeJS, array("multiple"=>true));
			}
			$arrJs = array_merge($arrJs, $arrBeforeJS);
			if(isset($width)){
				// die($width);
				if(strpos($width,"px")>0){
					$width = substr($width, 0, strpos($width,"px"));
				}
				$arrJs = array_merge($arrJs, array("style"=>"width:".$width."px"));
			}
			$value = "";
			if(isset($detail['value'])){
				$value = $detail['value'];
			}
			
			$divopen = "";
			$divopnx = "";
			$divclos = "";			
			$nextto = "";
			if(isset($detail['nextto'])){
				$nextto = $detail['nextto'];
				$divopen = "<div style='float:left;'>";
				$divopnx = "<div style='float:left;padding-left:10px'>";
				$divclos = "</div>";
			}
			$namacombo = $detail['namanya'];

			if($tags==true || $multiple==true){
				// if($namacombo=="cmbKABPTN"){
				// 	debug_array($namacombo);
				// }
				// riset
				// $namacombo = $namacombo . "[]";
			}
			if(is_array($value)){
				$value = null;
			}
			$return = $divopen . form_dropdown($namacombo, $opsyen, $value, $arrJs) . $divclos . $divopnx . $nextto . $divclos;
		}
		return array('return'=>$return, 'arrCombobox'=>$arrCombobox);
	}
	function inputDate($detail){
		$separator = "<span class='input-group-addon' style='height:5px;padding: 2px 5px;'> &nbsp - &nbsp</span>";
		if(isset($detail['readonly'])){
			$dtro = $detail['readonly'];
		}else{ 
			$dtro = false; 
		}
		$arrDate = array();
		if(is_array($detail['namanya'])){
			$return = "<div class='input-daterange input-group' id='datepicker' style='width:230px'>";
			$loop = 1;
			foreach ($detail['namanya'] as $keydate => $valuedate) {
				$elmDATESS = $keydate;
				$nilaidat = "";
				if(isset($valuedate['value'])){
					$nilaidat = $valuedate['value'];
				}
				$valDATESS = $nilaidat;
				$arrDATESS = array('name'=>$elmDATESS,'value'=>$valDATESS);
				if(isset($valuedate['readonly'])){
					if($valuedate['readonly']){
						$arrDATESS = array_merge($arrDATESS, array('disabled'=>true));	
					}
				}
				$arrDate = array_merge($arrDate, array($arrDATESS));
				                // $("#jqxWidget .jqx-input-content").attr('tabindex', '1');
				$return .= "<div name='" . $elmDATESS . "' id='" . $elmDATESS . "' tabindex='".$detail['urutan']."'></div>";
				if($loop==1){
					$return .= $separator;
				}
				$loop++;
			}
			$return .= "</div>";
		}else{
			if(strpos($detail['namanya'],"~")>0){
				$arrDateName = explode("~", $detail['namanya']);
				$arrDateValue = explode("~", $detail['value']);
				$datename1 = $arrDateName[0];
				$datename2 = $arrDateName[0]."2";
				$datevalue1 = $arrDateValue[0];
				$datevalue2 = "";

				if(count($arrDateName)>1){
					$datename2 = $arrDateName[1];
				}
				if(count($arrDateValue)>1){
					$datevalue2 = $arrDateValue[1];
				}
				$arrDate1 = array('name'=>$datename1,'value'=>$datevalue1, 'disabled'=>$dtro);
				$arrDate2 = array('name'=>$datename2,'value'=>$datevalue2, 'disabled'=>$dtro);
				$arrDate = 	array_merge(
											array($arrDate1), 
									 		array($arrDate2)
									 		);

				$return = "
							<div class='input-daterange input-group' id='datepicker' style='width:230px'>
							  <div name='" . $datename1 . "' id='" . $datename1 . "'></div>
							  <span class='input-group-addon' style='height:5px;padding: 2px 5px;'> &nbsp - &nbsp</span>
							  <div name='" . $datename2 . "' id='" . $datename2 . "'></div>
						  </div>";						
			}else{
				$valuenya = "";
				if(isset($detail['value'])){
					$valuenya = $detail['value'];
				}
				$arrDatevalue = array('name'=>$detail['namanya'],'value'=>$valuenya, 'disabled'=>$dtro);
				if(isset($detail['optional'])){
					$arrDatevalue = array_merge($arrDatevalue, $detail['optional']);
				}
				if(isset($detail['min'])){
					$arrDatevalue = array_merge($arrDatevalue, array("min"=>$detail['min']));
				}
				if(isset($detail['max'])){
					$arrDatevalue = array_merge($arrDatevalue, array("max"=>$detail['max']));
				}				
				$arrDate = array($arrDatevalue);
				$return = "<div name='" . $detail['namanya'] . "' id='" . $detail['namanya'] . "' tabindex='".$detail['urutan']."'></div>";
				if($dtro){
					$return .= form_input(array('name' => $detail['namanya'],'id'=> $detail['namanya'], 'type'=>'hidden', 'value'=> $valuenya));
				}
			}			
		}
		return array('return'=>$return, 'arrDate'=>$arrDate);
	}
	function generateinput($parameter,$addTabs=null){
		$CI =& get_instance();
		$multicolumn=false;
		$ckeditor=false;
		$elementonly = false;
		$layout = "row";
		$colLabel = null;
		$colInput = null;
		$classinput =null;
		$script = null;
		$loop = 1;
		$rr = 1;
		$group_temp = null;
		$tinggi_div = null;
		$lebar_div = null;
		$placeholder = null;
		$html = null;
		$responsive = true;
		$divID = null;
		foreach ($parameter as $param=>$value){
			${$param}=$value;
		}
		$urutan = array();
		foreach ($arrTable as $arrUrut) {
			$urutan[] = $arrUrut['urutan'];
		}
		array_multisort($urutan, SORT_ASC, $arrTable);
		$arrDate = array();
		$arrTimeinput = array();
		$arrNumInput = array();
		$arrEditor = array();
		$arrValid = array();
		$arrDDT = array();
		$arrDDL = array();
		$arrDDG = array();
		$arrCombobox = array();
		$arrTags = array();
		$arrMasked = array();
		$arrNumber = array();
		$arrPersen = array();
		$urutan_elemen = 0;
		foreach($arrTable as  $detail){
			if($multicolumn){
				$colLabel = "col-lg-2 col-form-label text-lg-right";
				$colInput = "";
			}
			$typeInput = $detail['type'];
			if(isset($detail['group'])){
				$group = $detail['group'];
			}
			if(isset($detail['urutan'])){
				$urutan_elemen = $detail['urutan'];
			}
			if(isset($detail['label'])){
				$adalabel = false;
				if(isset($detail['namanya'])){
					$detailnama = $detail['namanya'];
					if(is_array($detailnama)){
						$detailnama = removespecial($detail['label']);
					}
				}
				if(is_array($detail['label'])){
					$arrLabel = $detail['label'];
					if(isset($arrLabel['value'])){
						$textlabel = $arrLabel['value'];
						$adalabel = true;
					}
					if(isset($arrLabel['style'])){
						$labelstyle = $arrLabel['style'];	
					}
				}else{
					if($detail['label']!=""){
						$textlabel = $detail['label'];
						$adalabel = true;
					}
				}
				$arrLabel = array(
					'id'=>'lbl'.substr($detailnama,3,6)
				);
				if($layout=="row"){
					if(!isset($colLabel)){
						$colLabel = "col-sm-12 col-lg-2";
					}
					// $classlable = $colLabel . ' col-form-label' . ($typeInput=="view" ? "-view" : "");
					$classlable = null;
					if(!$multicolumn){
						$classlable = $colLabel . ' col-form-label';
					}else{
						$classlable = 'col-lg-2 col-form-label text-lg-right';
					}
					$arrLabel = array_merge($arrLabel, array('class'=>$classlable));
				}else{
					if(!isset($colLabel)){
						$colLabel = "col-lg-3";
					}
				}
				if($adalabel){
					$label = form_label( $textlabel, $detailnama, $arrLabel);
				}
			}
			if($responsive){
				if(isset($detail['size'])){
					$ukuran = $detail['size'];	
				}
			}

			if(isset($detail['elementonly'])){
				if($detail['elementonly']){
					$elementonly = true;
				}
			}
			$valuenya = null;
			switch($typeInput){
				case "dat": // tanggal
					$arrInputDate = inputDate($detail);
					$return = $arrInputDate['return'];
					$arrDate = array_merge($arrDate, $arrInputDate['arrDate']);
					break;
				case "tim": // waktu
					if(isset($detail['readonly'])){
						$dtro = $detail['readonly'];
					}else{ $dtro = false; }
					
					if(strpos($detail['namanya'],"~")>0){	
						$arrTimeName = explode("~", $detail['namanya']);
						$arrTimeValue = explode("~", $detail['value']);
						$timename1 = $arrTimeName[0];
						$timename2 = "";
						$timevalue1 = $arrTimeValue[0];
						$timevalue2 = "";

						if(count($arrTimeName)>1){
							$timename2 = $arrTimeName[1];
						}
						if(count($arrTimeValue)>1){
							$timevalue2 = $arrTimeValue[1];
						}

						$arrTimeinput = array_merge($arrTimeinput, 
															array(array('name'=>$timename1,'value'=>$timevalue1, 'disabled'=>$dtro)), 
															array(array('name'=>$timename2,'value'=>$timevalue2, 'disabled'=>$dtro))
														);
						$return = "
									<div class='input-daterange input-group' id='datepicker' style='width:230px'>
									  <div name='" . $timename1 . "' id='" . $timename1 . "' tabindex='".$detail['urutan']."'></div>
									  <span class='input-group-addon' style='height:5px;padding: 2px 5px;'> &nbsp - &nbsp</span>
									  <div name='" . $timename2 . "' id='" . $timename2 . "' tabindex='".$detail['urutan']."'></div>
								  </div>";
					}else{
						if(isset($detail['value'])){
							$valuetim = $detail['value'];
						}else{
							$valuetim = "00:00";
						}

						$arrTimeinput = array_merge($arrTimeinput, array(array('name'=>$detail['namanya'],'disabled'=>$dtro,'value'=>$valuetim)));	
						$return = "<div name='" . $detail['namanya'] . "' id='" . $detail['namanya'] . "' tabindex='".$detail['urutan']."'></div>";
					}
					
					break;					
				case "title":
					unset($label);
					$return = "<h3 class='font-size-lg text-dark font-weight-bold mb-6'>" . $detail['value'] . "</h3>";
					break;
				case "hid" :
					if(isset($detail['value'])){
						$valuenya = $detail['value'];
					}
					$return = form_input(array('name' => $detail['namanya'],'id'=> $detail['namanya'], 'type'=>'hidden', 'value'=> $valuenya));
					break;
				case "pwd":
					$tabindex = $detail['urutan'];
					$stylepwd = null;
					if(isset($detail['size'])){
						$stylepwd = "width:".$detail['size'] ."px";
					}
					$arrPasswd = array('name'=> $detail['namanya'],'id'=> $detail['namanya'], 'class'=>"form-control", "tabindex"=>$tabindex, "style"=>$stylepwd);
					if(isset($detail['value'])){
						if($detail['value']!=""){
							$arrPasswd = array_merge($arrPasswd, array('value'=>$detail['value']));
						}
					}
					$inputan = form_password($arrPasswd);
					$forminput = $inputan;
					$return = $forminput ;
					break;
				case "view" :	
				case "txh":
				case "num" :
				case "pct" :
				case "txn" :
				case "txt" :
					if($typeInput=="view"){
						$detail["readonly"] = true;
					}
					$valueinput = isset($detail['value']) ? $detail['value'] : '';
					$namesinput = $detail['namanya'];
					$element = array();
					/* digunakan kalau ingin dalam satu col isinya 2 txt :
					Contoh cara penggunaan : 
					$arrNamanya = array(
						'txtITEMSS'=>array(
							'size'=> '160', 
							'readonly'=>true
							),
						'txtNMAITM'=>array(
							'size'=> '200', 
							'readonly'=>true)
					);
					$arrTable[] = array('group'=>2, 'urutan'=>$urutan++, 'type'=> 'txt', 'label'=>'Produk', 'namanya' => $arrNamanya,'size' => '350','value'=>'','readonly'=>true);
					*/

					if(is_array($namesinput)){ //=== kalau nameinput array(lebih dari satu)
						$loop = 0;
						$urut = $detail['urutan'];
						foreach ($namesinput as $idelement => $valueelement) {
							$element[$loop]['namanya']=$idelement;	
							$element[$loop]['name']=$idelement;	
							$element[$loop]['id']=$idelement;
							$element[$loop]['urutan']=$urut;
							if(is_array($valueelement)){
								foreach ($valueelement as $keyelement => $valuekeyelement) {
									switch ($keyelement) {
										case 'dat' :
										case 'view' :
										case 'cmb' :
										case 'num' :
											$element[$loop]['type']=$valuekeyelement;		
											break;
										case 'size':
											$element[$loop]['style']='width:' . $valuekeyelement  .'px';
											if(strpos("N".$valuekeyelement,"%")>0){
												$valuekeyelement = $valuekeyelement;
											}else{
												$valuekeyelement = $valuekeyelement."px";
											}
			
											$element[$loop]['size']=$valuekeyelement;	
											break;
										case 'button':
										case 'readonly':
										case 'masked':
										case 'value':
										case 'delimiter':
											$element[$loop][$keyelement]=$valuekeyelement;
											break;
										default:
											$element[$loop][$keyelement]=$valuekeyelement;
											break;
									}
								}
							}
							$loop++;
							$urut++;
						}
					}else{
						$element[0]['namanya']=$namesinput;
						$element[0]['name']=$namesinput;	
						$element[0]['id']=$namesinput;
						if(!isset($detail['tagsinput'])){
							if(isset($ukuran)){
								if(strpos("N".$ukuran,"%")>0){
									$ukuran = $ukuran;
								}else{
									$ukuran = $ukuran."px";
								}
								$style_text = 'width:' . $ukuran;
								if(isset($detail['readonly'])){
									if($detail['readonly']){
										$style_text .= ";background-color:#F3F6F9";
									}
								}								
								$element[0]['style']=$style_text;
							}
							if(isset($detail['maxlength'])){
								$element[0]['maxlength'] = $detail['maxlength'];
							}
						}
						$element[0]['value']=$valueinput;
						if(isset($detail['tagsinput'])){
							$element[0]['tagsinput'] = $detail['tagsinput'];
						}
						if(isset($detail['readonly'])){
							if($detail['readonly']){
								// $element[0]['disabled'] = "disabled";
								// $element[0]['disabled'] = "readonly";
								$element[0]['readonly'] = true;
								// $element[0]['style'] = "background-color:#ccc";
							}
						}
						if(isset($detail['button'])){
							$element[0]['button'] = $detail['button'];
						}
						if(isset($detail['masked'])){
							if($detail['masked']!=""){
								$element[0]['masked'] = $detail['masked'];
							}
						}						
						if(isset($valueinput)){
							$classinput = 'form-control input-small';
							// if(is_numeric(str_replace(",","", $valueinput))){
							// 	$classinput = 'form-control input-number';
							// }
							$element[0]['class'] = $classinput;
						}else{
							$element[0]['class'] = 'form-control input-small';
						}
					}
					
					$forminput = "";
					$countElement = count($element);
					$divstr = "";
					$divend = "";					
					if($countElement>1){
						$divstr = "<div style='float:left;padding-right:2px'>";
						$divend = "</div>";
					}
					foreach ($element as $kunci => $nilai) {
						unset($inputan);
						$numerik = false;
						$classinput = 'form-control';
						if(isset($nilai['value'])){
							if($typeInput=="txn"){
								$classinput = 'form-control input-number';
							}
						}
						$tag=false;
						
						if(isset($nilai['tagsinput'])){
							if($ukuran==""){
								$panjangtoken = ($ukuran > $panjangtoken) ? $ukuran : $panjangtoken;///ambil ukuran terpanjang
							}else{
								$panjangtoken = $ukuran;
							}
							
							if($nilai['tagsinput']!=""){
								if(isset($nilai['namanya'])){
									$nilainama = $nilai['namanya'];
								}else{
									$nilainama = $nilai['namanya'];
								}
								$arrTagspass = array_merge($nilai['tagsinput'], array('size'=>$ukuran));
								$arrTags = array_merge($arrTags, array($nilainama=>$arrTagspass));
							}
							$tag = true;
							unset($nilai['tagsinput']);
						}
						if($numerik){
							$tabindex = $nilai['urutan'];
							if(isset($nilai['readonly'])){
								if($nilai['readonly']){
									$tabindex = "-1";	
								}
							}
							$inputan = "<div style='margin-top: 3px;' name='" . $nilai['namanya'] . "' id='" . $nilai['namanya'] . "' tabindex=".$tabindex."></div>";
						}else{
							$nilai['urutan'] = $detail['urutan'];
							if(is_array($namesinput)){
								$geninp = true;
								if(isset($nilai['type'])){
									if($nilai['type']=="button"){
										$geninp = false;
									}
								}
								if(!isset($inputan) && $geninp==true){
									$nilai['tabindex'] = $detail['urutan'];
									$inputan = form_input($nilai);
								}
							}
							$geninp = true;
							if(isset($nilai['type'])){
								if($nilai['type']=="button"){
									$geninp = false;
								}
							}
							$nilai['tabindex'] = $detail['urutan'];
							if($typeInput=="num"){
								$stylenum = 'text-align:right;width:200px';
								if(isset($detail["percentage"])){
									if($detail["percentage"]){
										$stylenum = 'text-align:right;width:100px';
									}
								}
								$nilai["class"] = 'form-control input-number';
								$nilai["style"] =$stylenum; 
								$nilai["size"] = '100px';
								if($typeInput=="num"){
									$arr_num = $detail;
									if(isset($detail["value"])){
										if($detail["value"]==""){
											$nilai["value"]=0;
										}
									}else{
										$nilai["value"]=0;
									}
									$arrNumber[] = $arr_num;
								}
							}
		
							if(!isset($inputan) && $geninp==true){
								$inputan = form_input($nilai);
							}
							$nextto = null;
							$divopen = null;
							$divopnx = null;
							$divclos = null;
							if(isset($detail['nextto'])){
								$nextto = $detail['nextto'];
								$divopen = "<div style='float:left;'>";
								$divopnx = "<div style='float:left;padding-left:10px'>";
								$divclos = "</div>";
							}							
						}
						if(isset($inputan)){
							$forminput = $divopen . $inputan . $divclos . $divopnx . $nextto . $divclos;
							unset($inputan);
						}
					}

					$return = $forminput ;
					break;
				case "cmb":
					$arrInputCombo = inputCombo($detail);
					$return = $arrInputCombo['return'];
					if(isset($detail["multiple"])){
						$nama_element = $detail['namanya'] . "_combo";
						$value_element = null;
						if(isset($detail["value"])){
							if(!is_array($detail["value"])){
								$value_element = $detail["value"];	
							}
						}
						$return .= form_input(array('name' => $nama_element,'id'=> $nama_element, 'type'=>'hidden', 'value'=> $value_element));
					}
					if(count($arrInputCombo['arrCombobox'])>0){
						$arrCombobox = array_merge($arrCombobox, $arrInputCombo['arrCombobox']);
					}
					break;
				case "ddg" :///jqxgrid dropdownlist
					$strDiv1 = "";
					$endDiv1 = "";
					$strDiv2 = "";
					$endDiv2 = "";
					if(isset($detail['otherelement'])){
						$strDiv1 = "<div style='float:left;padding-right:2px'>";
						$endDiv1 = "</div>";
						$strDiv2 = "<div style='float:left'>".$detail['otherelement']."</div>";
					}
					$valuetext = "";
					if(isset($detail['text'])){
						$valuetext = $detail['text'];
					}
					$arrDDG = array_merge($arrDDG, array($detail['namanya']=>array('width'=>$detail['size'],'value'=>$valuetext)));
					$ddgvalue = "";
					if(isset($detail['value'])){
						$ddgvalue = $detail['value'];
					}
					$return = $strDiv1 . "
						<div name='div" . $detail['namanya'] . "' id='div" . $detail['namanya'] . "'>
							<div id='jqxDDG_" . $detail['namanya'] . "' style='border-color: transparent;'></div>
						</div>
						<input type=hidden id='" . $detail['namanya'] . "' name='" . $detail['namanya'] . "' value='".$ddgvalue."'>
					".  $endDiv1 . $strDiv2;
					break;					
				case "viwfil":
					$return = generateinputfile($detail, 'view');
					break;
				case "fil":	
					$return = generateinputfile($detail);
					if(isset($detail["dropzone"])){
						$dropzone = true;
					}
					break;					
				case "udf":
					$return = "<div ". (isset($style) ? $style : "") .">" . $detail['value'] ."</div>";
					break;
				case "div":
					$return = "<div id='".$detail['namanya']."'>" . (isset($detail['value']) ? $detail['value'] : null ) ."</div>";
					break;
				case "udi":
					$return = "<div ". (isset($style) ? $style : "") .">" . $detail['value'] ."</div>";
					break;
				case "chk":
					$colInputValue = "col-md-1";
					$chkvalue = 0;
					if(isset($detail['value'])){
						$chkvalue = $detail['value'];
					}
					$chek = ($chkvalue == 1) ? TRUE : FALSE;
					$attr = array(
						'name' => $detail['namanya'],
						'id' => $detail['namanya'],
						'value'  => 'true',
						'checked' => $chek,
						'class' => 'control-label'
					);

					if(isset($detail['readonly'])){
						if($detail['readonly']){
							$attr = array_merge($attr, array("onclick"=>"this.checked=!this.checked;"));
						}
					}
					$return = "<div class='col-9 col-form-label'><div class='checkbox-inline'><label class='checkbox checkbox-success'>" . form_checkbox($attr) . "<span></span></label></div></div>";
					$returnx = '
					<div class="col-9 col-form-label">
					<div class="checkbox-inline" style="top:20px">
					<label class="checkbox checkbox-success">
					<input type="checkbox" name="Checkboxes5" />
					<span></span>Default</label>
					<label class="checkbox checkbox-success">
					<input type="checkbox" name="Checkboxes5" checked="checked" />
					<span></span>Checked</label>
					<label class="checkbox checkbox-success checkbox-disabled">
					<input type="checkbox" name="Checkboxes5" disabled="disabled" />
					<span></span>Disabled</label>
					</div></div>';
					break;
				case "txa":
					$editor = "";
					$valuetxa = ""; 
					$rows = 10;
					if(isset($detail['value'])){
						$valuetxa = $detail['value'];
					}
					$arrtxt = array('name'=> $detail['namanya'], 'id'=> $detail['namanya'], 'tabindex'=>$detail['urutan'], 'value'=> $valuetxa ,'class'=>'form-control col-xs-12', 'rows'=>$rows, 'cols'=>'10');
					if(isset($detail['style'])){
						$style = $detail['style'];
						// $arrtxt = array_merge($arrtxt, array('style'=>$detail['style']));
					}
					if(isset($detail['rows']) || $rows>0){
						if(isset($style)){
							$style .= $style .";";
						}
						if(isset($detail['rows'])){
							$rows = $detail['rows'];
						}
						$heighttxa = $rows*10;
						$styletxa = "height:" . $heighttxa. "px";
					}
					if(isset($styletxa)){
						$arrtxt = array_merge($arrtxt, array('style'=>$styletxa));
					}
					if(isset($detail['readonly'])){
						if($detail['readonly']){
							$arrtxt = array_merge($arrtxt, array('readonly'=>$detail['readonly']));
						}
					}					
					if(isset($detail['placeholder'])){
						$arrtxt = array_merge($arrtxt, array('placeholder'=>$detail['placeholder']));
					}					
					if(isset($detail['jqxeditor'])){
						// $arrtxt = array('name'=> $detail['namanya'], 'id'=> $detail['namanya'],'value'=> $detail['value']);
						$arrtxt = array('name'=> $detail['namanya'], 'id'=> $detail['namanya'],'value'=> htmlspecialchars($detail['value']));
						$arrEditor = array_merge($arrEditor, inputEditor($detail));
					}
				 	$stat_ckeditor = true;
					if(isset($detail['ckeditor'])){
						// debug_array($detail);
						$ro_ckeditor = false;
						if(isset($detail["readonly"])){
							if($detail["readonly"]){
								$ro_ckeditor = true;
							}
						}						
						if($ro_ckeditor){
							$editor = null;
							if(is_array($detail['ckeditor'])){
								foreach ($detail['ckeditor'] as $key => $value) {
									${$key}=$value;
								}
							}							
							$arrtxt = "<div style='background-color:#EEE;width:80%;height:".$height.";border:1px solid #ccc;border-radius: 5px;padding:10px;overflow:auto'>" . $detail['value'] . "</div>";
							$stat_ckeditor = false;
						}else{
							if(isset($detail['colInput'])){
								$coltxa = $detail['colInput'];
							}
							// 'ckeditor'=>array('full'=>false, 'toolbar'=>'verysimple', 'coltxa'=>'col-md-6','width'=>'70%','height'=>'80px')
							$width = "100%";
							$height = "200px";
							$toolbar = "Full";
							$full = true;
							if(isset($detail['size'])){
								$width = $detail['size'];
							}
							$CI->load->helper('ckeditor');
							if(is_array($detail['ckeditor'])){
								foreach ($detail['ckeditor'] as $key => $value) {
									${$key}=$value;
								}
							}
		
							$ckeditor= array(
								//ID of the textarea that will be replaced
								'id' 	=> 	$detail['namanya'],
								'path'	=>	'resources/plugins/ckeditor',
								'toolbar' 	=> $toolbar,
								//Optional values
								'config' => array( 	//Using the Full toolbar
									'width' 	=> 	$width,	//Setting a custom width
									'height' 	=> 	$height,	//Setting a custom height,
									'filebrowserImageUploadUrl' => '/form/upload'
								)
							);
							if(isset($detail['readonly'])){
								if($detail['readonly']){
									$ckeditor = array_merge($ckeditor, array("readonly"=>true));
								}
							}
							$editor = display_ckeditor($ckeditor);
						}
					}else{
						if(isset($detail['colInput'])){
							$coltxa = $detail['colInput'];//"col-xs-13 col-md-6";
						}else{
							if($multicolumn==false){
								$coltxa = "col-md-5";
							}
						}
					}
					if(isset($others)){
						if(is_array($others)){
							$arrtxt = array_merge($arrtxt, $others);	
						}
					}
					if($stat_ckeditor){
						$return = form_textarea($arrtxt) . $editor;
					}else{
						$return = $arrtxt;
					}
					break;
			}
			if(isset($detailnama)){
				$divID = "id='myrow".$detailnama."'";
			}
			if($multicolumn==false){
				if($typeInput!="hid" && $elementonly==false){
					if(!$ckeditor && $typeInput!="udx"){
						if($layout!="row"){
							$classinput = "col-md-12";
						}else{
							if($typeInput!="udf"){
								if(!isset($colinputform)){
									$classinput = "col-md-10";
								}else{
									$classinput = $colinputform;
								}
							}else{
								$classinput = "col-md-12";
							}
						}
						$input = "<div ".$divID." class='form-group ".($layout=="row" ? "row" : "col-md-12")."'>";
						$input .= isset($label) ? $label : "";
						$input .= "<div class='".$classinput."'>";
						$input .= isset($return) ? $return : null;
						$input .= "</div>";
						$input .= "</div>";
					}else{
						if($full){
							if(!isset($coltxa)){
								$coltxa = 'col-md-9';
							}
		
							// $style = "style='padding: 100px 100px 100px 100px'";
							if(isset($style)){
								$style = "style='padding-right:50px;padding-left:50px;". $style. "'";
							}else{
								$style = "";
							}
							$input = null;
							if(isset($label)){
								$input .= "<div class='row'>".$label."</div>";
							}

							$input .= "
								<div class='row' ".$divID.">
									<div class='form-group' style='width:100%'>
										<div class=".$coltxa." ".$style.">" . $return . "</div>
									</div>
								</div>";
							$ckeditor = false;
						}else{
							if(isset($coltxa)){
								$classinput = $coltxa;
								unset($coltxa);
							}
							if(!isset($classinput)){
								$classinput = "class='col-xs-12 col-md-6'";
							}
							$input = "<div ".$divID." class='form-group ".($layout=="row" ? "row" : "col-md-12")."'>";
							$input .= isset($label) ? $label : "";
							$input .= "<div class='".$classinput."'>";
							$input .= isset($return) ? $return : null;
							$input .= "</div>";
							$input .= "</div>";
							$ckeditor = false;							
						}	

					}
				}else{
					$input = $return;
				}
			}else{
				$classinput = "";
				if(isset($colInput)){
					$classinput = "class='" . $colInput . "'";
				}
				if(isset($coltxa)){
					if($typeInput=="txa"){
						$classinput = "class='" . $coltxa . "'";	
					}
					unset($coltxa);
				}

				if($typeInput!="hid"){
					$bikin = "";
					if(!isset($label)){
						$label = null;
					}
					if($rr==1){
						$bikin .= "	<div class='form-group row' ".$divID.">";
						$classinput = "";
						if(isset($colInput)){
							$classinput = "class='" . $colInput . "'";
						}
						if($return!=""){
							if($typeInput!="udf"){
								$divmulti_start = "<div class='col-lg-3'>";
								$divmulti_end = "</div>";
							}else{
								$divmulti_start = "<div class='col-lg-12'>";
								$divmulti_end = "</div>";
							}
							 
							$bikin .= $label . $divmulti_start . $return .$divmulti_end;
						}
					}else{
						$divmulti_start =  null;
						$divmulti_end = null;
						if($typeInput!="udf"){
							$divmulti_start = "<div class='col-lg-3'>";
							$divmulti_end = "</div>";
						}else{
							$divmulti_start = "<div class='col-lg-12'>";
							$divmulti_end = "</div>";
						}

						$bikin .= $label . $divmulti_start . $return .$divmulti_end;
						if($rr == $multicolumn){
							$bikin .= "</div>";
							$rr=0;
						}
					}
					if($typeInput=="text"){
						$inputnya = "<div class='row col-md-12'>".$detail['value']."</div>";
						$bikin = $inputnya.$bikin;
					}
					$input = $bikin;
					$rr++;
					$group_temp = $group;
				}else{
					$input = $return;
				}
			}
			$html .= $input;
			unset($label);
			$loop++;
		}
		// if(count($arrDate)>0 || count($arrEditor)>0 || count($arrMasked)>0 || count($arrValid)>0 || count($arrCombobox)>0 || count($arrTimeinput)>0 || count($arrNumInput)>0 || count($arrTags)>0 || $filexist==true || count($arrDDL)>0|| count($arrDDT)>0|| count($arrDDG)>0){

		// }
		if(isset($dropzone)){
			$script .= '<script src=' . base_url(PLUGINS."dropzonejs/dist/dropzone.js") .'></script>';
		}
		$script .= "
		<script>
			$(document).ready(function(){";	
		if(count($arrNumber)>0){
			$txtAutonumeric = "AutoNumeric";
			$strnumber = null;
			$rc = false;
			$arrAutoNumeric = array(
				"decimaldigit"=>"decimalPlaces",
				"max"=>"maximumValue",
				"minvalue"=>"minimumValue"
			);
			foreach($arrNumber as $keynumber=>$valuenumber){
				$numberoption = null;
				$arr_option = [];
				if ($rc) $strnumber .= ";";
				// $strnumber .= $numberarr;
				
				foreach($valuenumber as $keynumber_detail=>$valuenumber_detail){
					${"number_".$keynumber_detail} = $valuenumber_detail;
					$arr_option[$keynumber_detail] = $valuenumber_detail;
				}

				if(count($arr_option)>0){
					$rcoption = false;
					$numberoption .= ", {";
					foreach($arr_option as $keyoption=>$valueoption){
						if(isset($arrAutoNumeric[$keyoption])){
							if ($rcoption) $numberoption .= ",";
							$numberoption .= $arrAutoNumeric[$keyoption] . ":" . $valueoption;
							$rcoption = true;
						}
					}
					$numberoption .= "}";
				}
				if(isset($number_percentage)){
					if($number_percentage){
						$numberoption = ",'percentageUS2dec'";
					}
					unset($number_percentage);
				}

				$strnumber .=  "new AutoNumeric(".$number_namanya."".$numberoption.");";
				$rc = true;
			}
			// if(count($arrNumber["persen"])==1){
			// 	$strnumber = "'#" . $strnumber . "'";
			// }else{
			// 	$strnumber = "[" . $strnumber . "]";
			// }
			// $script .=  "new ".$txtAutonumeric."(".$strnumber.");";
			$script .=  $strnumber;
			// debug_array($script);
			// if(count($arrNumber["number"])>0){
			// 	$txtAutonumeric = "AutoNumeric.multiple";
			// 	$strnumber = null;
			// 	$rc = false;
			// 	foreach($arrNumber["number"] as $numberarr){
			// 		if ($rc) $strnumber .= ",";
			// 		$strnumber .= $numberarr;
			// 		$rc = true;
			// 	}
			// 	if(count($arrNumber["persen"])==1){
			// 		$txtAutonumeric = "AutoNumeric";
			// 		$strnumber = "'#" . $strnumber . "'";
			// 	}else{
			// 		$strnumber = "[" . $strnumber . "]";
			// 	}
			// 	$script .=  "new ".$txtAutonumeric."(".$strnumber.");";
			// }
			// if(count($arrNumber["persen"])>0){
			// 	$txtAutonumeric = "AutoNumeric.multiple";
			// 	$strnumber = "";
			// 	$rc = false;
			// 	foreach($arrNumber["persen"] as $persenarr){
			// 		if ($rc) $strnumber .= ",";
			// 		$strnumber .= $persenarr;
			// 		$rc = true;
			// 	}
			// 	if(count($arrNumber["persen"])==1){
			// 		$txtAutonumeric = "AutoNumeric";
			// 		$strnumber = "'#" . $strnumber . "'";
			// 	}else{
			// 		$strnumber = "[" . $strnumber . "]";
			// 	}
			// 	$script .=  "new ".$txtAutonumeric."(".$strnumber.", 'percentageUS2dec');";
			// }			
		}
		$script_add = null;
		if(count($arrCombobox)>0){
			$scrCombobox = null;
			$rslCombobox = generateCombobox($arrCombobox);
			foreach($rslCombobox as $keyCombobox=>$valueCombobox){
				${$keyCombobox} = $valueCombobox;
			}
			$script .= $scrCombobox;
			if($is_json){
				$script_add .= "
				function processData(data) {
					var mapdata = $.map(data, function (obj) {      
					  obj.id = obj.Id;
					  obj.text = obj.name;
					  return obj;
					});
					return { results: mapdata }; 
				}				
				";
			}
		}
		if(count($arrDate)>0){
			$script .= generateDate($arrDate);
		}
		if(count($arrTags)>0){
			$script .= generateTags($arrTags);
		}
		if(count($arrDDG)>0){
			$script .= generateDDG($arrDDG);
		}
		if(count($arrTimeinput)>0){
			$script .= generateDate($arrTimeinput,'time');
		}
		$script .= "
			});";
		$script .= $script_add;
		$script .= "</script>
		<style>
        .select2-container--open {
            z-index: 9999999 !important;
        }
        </style>
		";
		$return = isset($style) ? $style : null;
		$return .= $html;
		$return .= $script;
		return $return;		
	}
	function generateEditor($arrEditor){
		$strEditor = "";
		$create = true;
		$height = 500;
		foreach ($arrEditor as $key => $value) {
			${$key}=$value;
		}
		if($create){
			$strEditor = "$('#".$id."').jqxEditor({ lineBreak :'', ";
			$rc = false;
			if(isset($toolbarPosition)){
				$strEditor .= "toolbarPosition:\"".$toolbarPosition."\"";
				$rc = true;
			}
			if(isset($width)){
				if ($rc) $strEditor .= ",";
				$strEditor .= 'width:"'.$width.'"';
				$rc = true;
			}

			if(isset($height)){
				if ($rc) $strEditor .= ",";
				$strEditor .= 'height:"'.$height.'"';
				$rc = true;
			}
			if(isset($toolbar)){
				switch ($toolbar) {
					case 'sedang':
						$toolbar = "bold italic underline | format font size";
						break;
					default:
						$toolbar = "bold italic underline | left center right";
						break;
				}
				if ($rc) $strEditor .= ",";
				$strEditor .= "tools:\"" .$toolbar ."\"";
				$rc = true;
			}
			$strEditor .= "});";		
		}	
		return $strEditor;		
	}
	function generateMasked($arrParam){
		/*
		# - digit, dari 0 ke 9
		9 - digit, dari 0 ke 9
		0 - digit, dari 0 ke 9
		A - alphanumerik, dari 0 ke 9 dan a ke z dan A ke Z
		L - alpha, a ke z dan A ke Z
		[abcd] - 	karakter set, hanya bisa diisi karakter yg dikurung
							contoh :[abcd] = [a-d],
											[0-5] => hanya menerima karakter 0-5
											[ab] => hanya menerima karakter a dan b

		*/
		$strMasked = "";
		foreach ($arrParam as $key => $value) {
			$strMasked .= "$('#".$key."').jqxMaskedInput({ mask: '".$value."' });";
		}
		return $strMasked;
	}
	function generateValidator($arrValid, $formname){
		// debug_array($arrValid);
		$scriptawal = "<script>
		validator = null;
		$(document).ready(function () {
			// document.addEventListener('DOMContentLoaded', function (e) {
				const ".$formname."Form = document.getElementById('".$formname."');
				";
		$script = "
			const notzeroEmpty = function (){
                return {
                    validate: function (input){
                        const value = input.value;
                        if(value == 0){
                            return {
                                valid:false,
                            }
                        }
                        if(value == ''){
                            return {
                                valid:false,
                            }
                        }
                        return {
                            valid:true,
                        }
                    }
                }
            }
			const ckeditorempty = function(){
				return {
					validate: function (input){
						var editorid = input.field;
						console.log(editorid);
						var messageLength = CKEDITOR.instances[editorid].getData();//.replace(/<[^>]*>/gi, '').length;
						console.log(messageLength);
						
						if(messageLength.length === 0){
                            return {
                                valid:false,
                            }
						}
                        return {
                            valid:true,
                        }
					}
				}
			}
            FormValidation.validators.notzeroEmpty = notzeroEmpty;
            FormValidation.validators.ckeditorempty = ckeditorempty;
            validator = FormValidation.formValidation(
                ".$formname."Form,{
					fields: {
		";
		$rc = false;
		foreach($arrValid as $keyValid=>$valueValid){
			if($rc) $script .= ",";
			$rtn_callback = null;
			unset($conditional);
			$special = false;
			foreach($valueValid as $key_detail_valid=>$value_detail_valid){
				${$key_detail_valid} = $value_detail_valid;
			}
			if($special){
				if(isset($tags)){
					if($tags){
						$scriptawal .= "const ".$keyValid."Field = jQuery(".$formname."Form.querySelector('[name=\"".$keyValid."\"]'));";
						
						$message = "
							callback: {
								message: '".$message ."',
								callback: function (input) {
									const options = ".$keyValid."Field.select2('data');
									return options !== null && options.length >= 1;
								},
							}
						";
					}					
				}
				if(isset($cke)){
					if($tags){
						$scriptawal .= "const ".$keyValid."Field = jQuery(".$formname."Form.querySelector('[name=\"".$keyValid."\"]'));";
						$message = "
							callback: {
								message: '".$message ."',
								callback: function(value, validator, \$field) {
									if (value === '') {
										return true;
									}
									var div  = $('<div/>').html(value).get(0),
										text = div.textContent || div.innerText;
	
									return text.length <= 200;
								}								
							}
						";
					}
				}
			}else{

				if(isset($conditional)){
					$messageerror = $message;
					
					$message = "callback: {";
					$message .= " message:'" . $messageerror . "',";
					$message .= "callback: function(value, validator, \$field) {";
					$message .= " console.log($('#tny_kelompok').val());";
					$message .= "if(" . $conditional . "){";
					$message .= " console.log($('#tny_kelompok').val());";
					$message .= " return true;";
					$message .= "}";
					$message .= "return false;}" ;
					$message .= "}";
				}else{
					$message = $validation . " : {message:'" . $message . "'}";
				}
			}
			$script .= "
			". $keyValid. " : { validators: { " . $message . " } }";
			$rc = true;
			$rtn_callback = null;
		}
		$script .= "},
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    bootstrap: new FormValidation.plugins.Bootstrap()
                }
            });
		";
		
		// $script = null;
		$script = $scriptawal . $script;

		$script .="});</script>";
		// die($script);
		return $script;
	}
	function generateValidator_bootstrap($arrValid){
		$scriptValidator ="
			$('#formgw').bootstrapValidator({
				container:'#messages',
				fields: {
		";
		$koma = false;
		for($x=0;$x<count($arrValid);$x++){
			if ($koma) $scriptValidator .= ",";
			$scriptValidator .= $arrValid[$x];
			if($arrValid[$x]!=""){
				$koma = true;
			}else{
				$koma = false;
			}
		}
		$koma = false;
		$scriptValidator .="}});";
		return $scriptValidator;
	}
	function generatejqxValidator($arrValid){
		$strule = "";
		$onSuccess = "swal('Tidak ada fungsi!')";
		$formname = "formgw";
		$position = "right";
		$hintType = "arrow:false,";
		foreach ($arrValid as $key => $value) {
			for ($i=0; $i < count($value); $i++) {
				# code...
				switch ($value[$i]['rule']) {
					case 'empty':
						# code...
						$str = "{input: '#".$key."', message: '".$value[$i]['message']."', action: 'keyup, blur', rule: function(input){var nilai = input.val();var result = (!(nilai == 0 && $.trim(nilai) == ''));return result;}}";
						break;
					case 'zero':
						# code...
						$str = "{input: '#".$key."', message: '".$value[$i]['message']."', action: 'keyup, blur', rule: function(input){var nilai = input.val();var result = (!(nilai == 0));return result;}}";
						break;
					case 'required':
						# code...
						$str = "{ input: '#".$key."', message: '".$value[$i]['message']."', action: 'keyup, blur', rule: 'required' }";
						break;
					case 'custom':
						# code...
						if(isset($value[$i]['function'])){
							$str = "{ input: '#".$key."', message: '".$value[$i]['message']."', action: 'keyup, blur', rule: function(input){var nilai = input.val();" . $value[$i]['function'] . "return result;}}";
						}else{
							$str = "{ input: '#".$key."', message: '".$value[$i]['message']."', action: 'keyup, blur', rule: 'required' }";
						}
						break;
					case 'email':
						$str = "{ input: '#".$key."', message: 'Format email tidak benar!', action: 'keyup, blur', rule: 'email' }";
						break;
					default:
						# code...
						$str = "{ input: '#".$key."', message: '".$value[$i]['message']."', action: 'keyup, blur', rule: 'required' }";
						break;
				}

				$strule .= ($strule=="") ? $str : "," . $str;

				if(isset($value[$i]['onSuccess'])){
					$onSuccess = $value[$i]['onSuccess'];
				}
				if(isset($value[$i]['position'])){
					$position = "position: '".$position."',"; //$value[$i]['position'];
				}
				if(isset($value[$i]['label'])){
					if($value[$i]['label']){
						$hintType = "hintType: 'label', arrow:true, ";	
					}
				}
				if(isset($value[$i]['formnya'])){
					$formname = $value[$i]['formnya'];
				}

			}
		}

		$strValidator = "
			$('#".$formname."').jqxValidator({
				".$hintType."
        animationDuration: 0,
        // ".$position."
        focus: true,
				rules: [
				" . $strule . "
				],
				onSuccess: function(event){
					" . $onSuccess . "
				}
			});
		";

		return $strValidator;
	}
	function generateDate_datepicker($arrDate){
		$CI =& get_instance();
		$scriptDate = "
				$('";
		$rc = false;
		for($x=0;$x<count($arrDate);$x++){
			if ($rc) $scriptDate .= ",";
			$scriptDate .= "#" . $arrDate[$x];
			$rc = true;
		}
		$scriptDate .= "').datepicker({
			format: \"" . strtolower($CI->config->item('dateformat')) . "\",
			todayBtn: \"linked\",
			language: \"id\",
			autoclose: true
		});
		";
		return $scriptDate;		
	}
	function generateDate($arrDate, $jenis=null){
		$CI = get_instance();
		$theme = $CI->config->item('app_theme');
		$USR_THEMES = $CI->session->userdata("USR_THEMES");
		if($USR_THEMES!=""){
			$theme = $USR_THEMES;
		}		
		$rc = false;
		$scriptDate = "";
		$timebutton = "";
		$disabled = "";
		$formatString = "yyyy-MM-dd";
		if($jenis=="time"){
			$formatString = "HH:mm";
			$timebutton = ", showTimeButton: true, showCalendarButton: false";
		}
		foreach ($arrDate as $keyarr => $valuearr) {
			if(is_array($valuearr)){
				// $('#" . $valuearr['name'] . "').jqxDateTimeInput({ readonly: true});
				if(isset($valuearr['disabled'])){
					if($valuearr['disabled'] != ""){
						$disabled = "disabled:true,";
					}else{
						$disabled = "";
					}
				}
				$scriptDate .= "
					var " . $valuearr['name'] . "offset = $(\"#" . $valuearr['name'] . "\").offset();
					var " . $valuearr['name'] . "letaknya = " . $valuearr['name'] . "offset.top;
					if(" . $valuearr['name'] . "letaknya <= (screen.height / 2)){
						var " . $valuearr['name'] . "dropDownVerticalAlignment = 'bottom';
					}else{
						var " . $valuearr['name'] . "dropDownVerticalAlignment = 'top';
					}
					$('#" . $valuearr['name'] . "').jqxDateTimeInput({ 
						width: '110px',
						dropDownVerticalAlignment: " . $valuearr['name'] . "dropDownVerticalAlignment, 
						height: '35px', " . $disabled . " 
						formatString:'" . $formatString . "',
						theme:'".$theme."'
						" . $timebutton;
				
				$rc = true;
				if(isset($valuearr['disabled'])){
					if($valuearr['disabled']){
						if($rc) $scriptDate .= ",";
						$scriptDate .= "showCalendarButton: false
						";
						$rc = true;
					}
				}
				// $('#datDUEDAT').jqxDateTimeInput({min: new Date(2016,10-1,04}));
				if(isset($valuearr['min'])){
					if($valuearr['min']){
						$arr = explode("-", $valuearr['min']);
						if($rc) $scriptDate .= ",";
						$scriptDate .= "min: new Date($arr[0],$arr[1]-1," . substr($arr[2],0,2) .") ";
						$rc = true;
					}
				}
				if(isset($valuearr['max'])){
					if($valuearr['max']){
						$arr = explode("-", $valuearr['max']);
						if($rc) $scriptDate .= ",";
						$scriptDate .= "max: new Date($arr[0],$arr[1]-1," . substr($arr[2],0,2) .") ";
						$rc = true;
					}
				}
				// if($rc) $scriptDate .= ",";
				$scriptDate .= "
					});
				";
				if($valuearr['value']!=""){
					if($jenis!="time"){
						$arr = explode("-", $valuearr['value']);
						if(count($arr)>2){
							$arrMonth = $arr[1]-1;
							$scriptDate .= "
								$('#" . $valuearr['name'] . "').jqxDateTimeInput('setDate', new Date($arr[0],$arrMonth," . substr($arr[2],0,2) ."));
							";
							// $('#" . $valuearr['name'] . "').jqxDateTimeInput('setDate', new Date(1960,11,28));
							// 
						}else{
							$scriptDate .= "
								$('#" . $valuearr['name'] . "').jqxDateTimeInput('val', '".$valuearr['value']."');
							";
						}
					}else{
						$arr = explode(":", $valuearr['value']);
						if(count($arr)>1){
							$scriptDate .= "
							$('#" . $valuearr['name'] . "').jqxDateTimeInput('setDate', new Date(1975,1,20,$arr[0]," . substr($arr[1],0,2) ."));
						";
						}
					}
				}else{
					$scriptDate .= "
						$('#" . $valuearr['name'] . "').jqxDateTimeInput({value:null});
					";					
				}
			}else{
				$scriptDate .= "$('#" . $valuearr . "').jqxDateTimeInput({ width: '110px', height: '30px', enableBrowserBoundsDetection: true, formatString:'" . $formatString . "', " . $timebutton. "theme:'".$theme."'});";
			}
			if(isset($valuearr['fontsize'])){
				$scriptDate .= "$('#" . $valuearr['name'] . "').find('input').css('font-size', '".$valuearr['fontsize']."px');";
			}
		}		
		// $scriptDate .= "$('#input" . $valuearr['name'] . "').css('font-size', '6px');";
		return $scriptDate;
	}
	function generateNumberInput($arrNumInput){
		$scriptNumberInput = "";
		$decimaldigit = "0";
		$inputmode = "";
		$spinButtons = 'false';
		$spinMode = 'none';
		$promptChar = '';
		$disabled = "";
		$digits = "";
		$symbol = "";
		$minvalue = "min:0, ";
		$height = "25px";
		$rc = false;
		$jmldigit = 10;
		foreach ($arrNumInput as $value) {
			$IDELM = $value['id'];
			$ni_size = '150';
			$digits = "";
			$jmldigit = 9;
			$symbolinput = "";
			$disabled = "";
			if(isset($value['width'])){
				$ni_size = (is_numeric($value['width'])) ? $value['width'] : $ni_size;
			}
			if(isset($value['height'])){
				$height = $value['height'];
			}
			if(isset($value['minvalue'])){
				$minvalue = (is_numeric($value['minvalue'])) ? ("min:" . $value['minvalue'] .",") : "min:0, ";
			}
			if(isset($value['symbol'])){
				if($value['symbol']=="%"){
					$symbolinput .= "symbolPosition: 'right',";	
				}				
				$symbolinput .= "symbol : '" .$value['symbol'] . "',";
			}			
			if(isset($value['decimaldigit'])){
				$decimaldigit = $value['decimaldigit'];
			}
			if(isset($value['digits'])){
				$jmldigit = (is_numeric($value['digits'])) ? $value['digits'] : "";
				$digits = "digits :" .$jmldigit . ",";
			}
			if(isset($value['promptChar'])){
				$promptChar = "promptChar : '" . $value['promptChar'] ."',";
			}			
			if(isset($value['inputmode'])){
				if($value['inputmode']!=""){
					$inputmode = "inputMode : '" .$value['inputmode']."', ";	
				}
			}
			if(isset($value['spinmode'])){
				if($value['spinmode']!=""){
					$spinMode = $value['spinmode'];	
				}
			}
			if(isset($value['max'])){
				if($value['max']!="" && is_numeric($value['max'])){
						$maxvalue = $value['max'];
				}
			}
			if(!isset($maxvalue)){
				$maxvalue = str_pad("",$jmldigit,"9");//$jmldigit*"0";
				$inputmode = "max : '" .$maxvalue."', ";
			}else{
				$inputmode = "max : '" .$maxvalue."', ";
			}

			$theme = "";
			if(isset($value['disabled'])){
				if($value['disabled']!=""){
					$disabled = $value['disabled']==true ? "true" : "false";
					$disabled = "readOnly: " . $disabled . ",";
					$theme = "theme : 'fresh', ";
				}
			}
			$scriptNumberInput .= "$('#" . $IDELM . "').jqxNumberInput({ " . $theme . " width: '".$ni_size."px', height: '".$height."', spinMode: '".$spinMode."',  spinButtons: ".$spinButtons.", ". $promptChar ." " . $disabled . " " . $inputmode ."" . $digits . $symbolinput . $minvalue ." decimalDigits: ".$decimaldigit." });";
			if($value['val']!=""){
				$scriptNumberInput .= "$('#" . $IDELM . "').jqxNumberInput('val'," . $value['val'] . ");";	
			}
			if(isset($value['fontsize'])){
				$scriptNumberInput .= "$('#" . $IDELM . "').find('input').css('font-size', '".$value['fontsize']."px');";
			}

			$inputmode = "";
		}
		return $scriptNumberInput;		
	}
	function generateDDT($arrDDT){
		$scriptDDT = "";
		$rc = false;
		$width = 150;
		// ({ width: 150, height: 25});

		foreach ($arrDDT as $key => $value) {
			// print_r($value);
			if(isset($value['width'])){
				$width = $value['width'];
			}
			if(isset($value['value'])){
				$nilai = $value['value'];
			}			
			$scriptDDT .= "
				$('#div" . $key . "').jqxDropDownButton({ width: " . $width . ", height: 25});
				var content = '<div style=\"position: relative; margin-left: 3px; margin-top: 5px;\">".$nilai."</div>';
				$('#div" . $key. "').jqxDropDownButton('setContent', content);
			";
			if(isset($value['disable'])){
				if($value['disable']){
					$scriptDDT .= "$('#div" . $key. "').jqxDropDownButton({disabled: true });";
				}
			}
		}
      //       var args = event.args;
      //       var item = $('#" . $elementid. "').jqxTree('getItem', args.element);                
      //       var id = args.element.id;
						// var ip;
      //       var recursion = function (object) {
      //         for (var i = 0; i < object.length; i++) {
      //           if (id == object[i].id) {
      //             ip = object[i].nilai;
      //             break;
      //           } else if (object[i].items) {
      //             recursion(object[i].items);
      //           };
      //         };
      //       };
      //       recursion(records);
      //       alert(id);
      //       var dropDownContent = '<div style=\"position: relative; margin-left: 3px; margin-top: 5px;\">' + item.label + '</div>';
      //       $('#" . str_replace("tree", "", $elementid). "').jqxDropDownButton('setContent', dropDownContent);
      //       $('#" . str_replace("tree", "", $elementid). "').jqxDropDownButton('close');          


		return $scriptDDT;
	}
	function generateDDG($arrDDG){
		$scriptDDG = "";

		$rc = false;
		$width = 150;
		$valued = "";
		$focusout = "";

		// ({ width: 150, height: 25});
		foreach ($arrDDG as $key => $value) {
			if(isset($value['width'])){
				$width = $value['width'];
			}
			if(isset($value['value'])){
				$valued = $value['value'];
			}
			if(isset($value['focusout'])){
				if($value['focusout']==true){
					$focusout = "
						$('#div" . $key . "').on('focusout', function () {
							$('#div" . $key . "').jqxDropDownButton('close');
						});
					";
				}
			}
			// //edited by detanto
			// $focusout = "
			// 	$('#jqxDDG_" . $key . "').on('focusout', function () {
			// 		// alert('detanto');
			// 		$('#div" . $key . "').jqxDropDownButton('close');
			// 	});
			// ";

			$scriptDDG .= "
				$('#div" . $key . "').jqxDropDownButton({ width: " . $width . ", height: 25});
				var content = '<div style=\"position: relative; margin-left: 3px; margin-top: 5px;\">".$valued."</div>';
				$('#div" . $key . "').jqxDropDownButton('setContent', content);
			" . $focusout;
		}
		
		return $scriptDDG;

	}
	function generateTags($arrTags){
		// debug_array($arrTags);
	}
	function generateTags_token($arrTags){
		$scriptTags = "";
		$extranilai = "";
		$rc = false;
		foreach ($arrTags as $key => $value) {
			$parameter = "";
			$hinttext = "Pencarian data";
			$noduplicate = 'true';
			$tokenvalue = 'name';
			$tiro = "";
			$cb = "";
			foreach ($value as $keyvalue => $valuekey) {
				${$keyvalue}=$valuekey;
				if($keyvalue == 'url'){
					$url = "'" . $valuekey . "'";	
				}
				if($keyvalue == 'function'){
					$url = $valuekey;
				}
				if($keyvalue == 'noduplicate'){
					if(!$valuekey){
						$noduplicate = "false";
					}
				}
				if($keyvalue == 'tokenvalue'){
					if($valuekey=="id"){
						$tokenvalue = "id";
					}
				}
				if($keyvalue == 'tambah'){
					if($valuekey){
						$parameter .= ", allowFreeTagging: true";	
					}
				}
				if($keyvalue == 'limit'){
					$parameter .= ", tokenLimit: " . $valuekey;	
				}
				if($keyvalue == 'minchar'){
					$parameter .= ", minChars: " . $valuekey;	
				}
				if($keyvalue == 'zindex'){
					$parameter .= ", zindex: " . $valuekey;	
				}

				if($keyvalue == 'searchDelay'){
					$parameter .= ", searchDelay: " . $valuekey;	
				}
				if($keyvalue == 'readonly'){
					if($valuekey==true){
						$tiro = ",'readonly' : true";
					}
				}
				if($keyvalue=="height"){
					$height = $valuekey;
				}
				if($keyvalue == 'callback'){
					if(is_array($valuekey)){
						$zz = "";
            foreach ($valuekey as $fn => $str) {
            	# code...
            	$zz .= "
            		, ".$fn.":function(item){
            			".$str."
            		}
            	";
            }
            $cb .= $zz;
					}
				}	
				if($keyvalue =='data'){
					$arrData = explode(";", $valuekey);					
				}
				if($keyvalue =='extradata'){/////index saat prepopulate selain id dan name-->sesuaikan dengan token formatter
					foreach ($valuekey as $ked => $ved) {
						$extranilai .= $ked . ":'" . $ved . "',";
					}
				}	
			}
			if(isset($arrData)){
				if(count($arrData)>0){
					$nilai = "["; 
					$rc=false;

					for($x=0;$x<count($arrData);$x++){
						// if($rc==true)
						$nilai .= ($rc==true ? "," : "");
						if($tokenvalue=="id"){
							$arrDataToken = explode("~", $arrData[$x]);
							$token0 = $arrDataToken[0];
							if(isset($arrDataToken[1])){
								$token1 = $arrDataToken[1];
								$nilai .= "{id:'" . $token0 . "',".$extranilai." name:'" . trim($token1). "'" . $tiro . "}";
							}else{
								$token1 = $arrDataToken[0];
								$nilai .= "{id:'" . $token0 . "',".$extranilai." name:'" . trim($token1). "'" . $tiro . "}";
							}
						}else{
							$token1 = trim($arrData[$x]);
							$nilai .= "{name:'" . trim($arrData[$x]). "'" . $tiro . "}";	
						}
						$rc=true;
					}
					$nilai .= "]";						
				}
				
				// if($nilai!="[]" && $nilai!="[{id:'', name:''}]" && $nilai!="[{id:'0', name:''}]"){
				if($token1!=""){
					$parameter .= ", prePopulate: " . $nilai;	
				}	
			}

			// $scriptTags .= "$('#" . $key . "').tagsInput({ width: '".$arrTagsd[0]."', tambah: '".$arrTagsd[1]."', 'autocomplete_url':'".$arrTagsd[2]."'});";
			$scriptTags .= "$('#" . $key . "').tokenInput(". $url. ",
				{ preventDuplicates: ".$noduplicate.", 
					tokenDelimiter:';', 
					hintText:'" . $hinttext. "', 
					noResultsText:'Data Tidak ditemukan', 
					searchingText:'..mencari..',
					tokenValue:'" . $tokenvalue . "'" . $parameter . $cb . "});";
			if($size!=""){
				// $scriptTags .= "$('#token" .$key . "').css('width', '". $size . "px');";
				$scriptTags .= "$( '#".$key."' ).siblings().css('width', '". $size . "px');";
			}
			if(isset($height)){
				if($limit==1){
					$scriptTags .= "$('#token".$key."').css('cssText', 'height:".$height." !important');";
				}
			}
		}
		
		return $scriptTags;		
	}
	function generateCombobox($arrCombobox){
		$CI = get_instance();
		$scriptCombobox = "";
		$rc = false;
		$multiple = false;
		$tags = false;
		$is_json = false;
		// $CI->load->helper('language');
		$CI->lang->load('common', "english");
		$placeholder = $CI->lang->line("choose_data");
		foreach ($arrCombobox as $key => $valuecombobox) {
			$multiple = false;
			$tags = false;
			$disabled = false;
			$setwidth = false;
			$select2option = null;
			$rc = false;
			foreach($valuecombobox as $keycombo=>$valuecombo){
				${$keycombo} = $valuecombo;
			}
			if(!isset($width)){
				$setwidth=true;
			}else{
				if($width==""){
					$select2option = "dropdownAutoWidth : true,width: 'auto'";
					$rc = true;
					unset($width);
				}
			}
			if($tags){
				$select2option .= ($rc==true ? "," : "") .  "tags : true";
				$rc = true;
			}
			if($placeholder!=null){
				$select2option .= ($rc==true ? "," : "") .  "placeholder : '" . $placeholder ."'";
				$rc = true;
			}
			if($multiple){
				$select2option .= ($rc==true ? "," : "") .  "multiple : true";
			}
			if($select2option!=null){
				$select2option = "{" . $select2option . "}";
			}
			if($disabled){
				$scriptCombobox .= "$('#" . $key . "').prop('disabled',true);";
			}
			if(isset($json)){
				$is_json = true;
				$json_placeHolder = "Select Value";
				$json_minimumInputLength = 3;
				$tags_json = null;
				$multiple_json = null;
				$json_value_desc = null;
				$processdata = null;
				// debug_array($json);
				foreach($json as $keyjson=>$valuejson){
					${"json_" . $keyjson} = $valuejson;
				}
				if(isset($json_tags)){
					if($json_tags){
						$tags_json = "tags : true, ";
					}
				}
				
				$script_multiple = null;
				if(isset($json_multiple)){
					if($json_multiple){
						$multiple_json = "multiple : true, ";
						$processdata = null;
						$loop = 0; 
						if(is_array($json_value_desc)){
							foreach($json_value_desc as $key_id=>$key_desc){
								$script_multiple .= "
									var option".$loop." = new Option('".$key_desc."','".$key_id."', true, true);
									$('#".$key."').append(option".$loop.");	
								";
							}
						}
						$script_multiple .= "$('#".$key."').trigger('change');";
						// debug_array($json_value_desc);
						/*
						
						var option2 = new Option('tanto1','tanto1', true, true);
	
						console.log(option1);
	
						
						$('#".$key."').append(option2);
	
						param_".$key.".trigger({
							type: 'select2:select',
							params: {
							  data: data
							}
						  });
						*/

					}
				}else{
					$processdata = "data: processData([{ \"Id\": \"".$json_value."\", \"name\":\"".$json_value_desc."\" }]).results,";
				}
				
				$scriptCombobox .= "
					var param_".$key." = $('#".$key."').select2({
						" . $tags_json . "
						" . $multiple_json . "
						" . $processdata . "
						placeholder: '".$json_placeHolder . "',
						minimumInputLength : ".$json_minimumInputLength.",
						ajax: {
							url: '".base_url(). $json_url . "',
							dataType: 'json',
							results : function(data, page) {
								return {
									results :
										data.map(function(item) {
											return {
												id : item.id,
												text : item.name
											};
										}
								)};
							}
						}
					});
				";
				$scriptCombobox .= $script_multiple;
				unset($json);
				/*
					// x.val([\"Trade Fair\", \"CA\", \"Party\"]).trigger(\"change\");
				*/
			}else{
				$scriptCombobox .= "$('#" . $key . "').select2(".$select2option.");";
			}
			if(isset($cascade)){
				foreach($cascade as $keycascade=>$valuecascade){
					${$keycascade} = $valuecascade;
				}
				$scriptCombobox .= "$('#".$key."').on('select2:select', function (e) {";
				if(!isset($script_cascade)){
					if(!isset($param_cascade)){
						show_error("Parameter param_cascade missing!");
					}
					if(!isset($url_cascade)){
						show_error("Parameter url_cascade missing!");
					}
					if(!isset($next_cascade)){
						show_error("Parameter next_cascade missing!");
					}
					$param_cascade_oth = null;
					if(isset($param_cascade_other)){
						if(is_array($param_cascade_other)){
							$param_cascade_oth = null;
							foreach($param_cascade_other as $keyCascade_2=>$valueCascade_2){
								$value_cascade = $valueCascade_2=="" ? "''" : $valueCascade_2;
								$param_cascade_oth .= "param['" . $keyCascade_2 . "'] = " . $value_cascade;
							}
						}
					}
					$scriptCombobox .= "
						$('#" . $next_cascade . "').empty().trigger('change');
						var data = e.params.data;
						var keydata = data.id;
						var param = {};
						param['".$param_cascade ."'] = keydata;
						" . $param_cascade_oth . "
						$('#imgPROSES').show();
						$('#windowProses').jqxWindow('open');
						$.post('" . $url_cascade . "', param,function(jsonreturn){
							$('#windowProses').jqxWindow('close');
							var returnjson = JSON.parse(jsonreturn);
							$('#". $next_cascade . "').select2({
								allowClear: true,
								placeholder:'Silahkan Pilih Data',
								disabled:false,
								data: returnjson,
							});
						});
					";
				}else{
					$scriptCombobox .= $script_cascade;	
				}
				$scriptCombobox .= "
					});
				";
				unset($cascade);
			}
			if(isset($value)){
				if(is_array($value)){
					$json_e = json_encode($value);
					// debug_array($json_e);
					// debug_array($value);
					$scriptCombobox .= "var data = " . $json_e . ";console.log(data);";
					$scriptCombobox .= "var product_select = $('#".$key."');";
					$scriptCombobox .= "data.forEach(function(item){";
					$scriptCombobox .= "	console.log(item.text);";
					$scriptCombobox .= "	var option = new Option(item.text, item.id, true, true);";
					$scriptCombobox .= "	product_select.append(option).trigger('change');";
					$scriptCombobox .= "});";
		
					// $scriptCombobox .= "var newOption = new Option(data.text, data.id, true, true);";
					// $scriptCombobox .= "console.log(newOption);";
					// $scriptCombobox .= "$('#" . $key . "').append(newOption).trigger('change');";
					// $scriptCombobox .= "$('#" . $key . "').select2('data', data);";

					// $scriptCombobox .= "Json.stringify(".$value.");";
					foreach($value as $keyCombo=>$valueCombo){
						// $scriptCombobox .= "selectedValues[".$keyCombo."]= '" . $valueCombo . "';";
					}
				}
				unset($value);
				// $scriptCombobox .= "$('#" . $key . "').append(selectedValues).trigger('change');
				// ";
			}
		}
		$combobox = array("scrCombobox"=>$scriptCombobox, "is_json"=>$is_json);
		return $combobox;
	}
	function jqxgenerateCombobox($arrCombobox){
		$CI = get_instance();
		$theme = $CI->config->item('app_theme');
		$USR_THEMES = $CI->session->userdata("USR_THEMES");
		if($USR_THEMES!=""){
			$theme = $USR_THEMES;
		}		
		$scriptCombobox = "";
		$rc = false;
		$cmbheight= 30;
		$css = "";
		foreach ($arrCombobox as $key => $value) {
			$extrascript = "";
			$script = "";
			$extracombo = "";
			$autoheight = "false";
			$checkbox = "";
			$lebar = "";
			$multiselect = "";
			$tinggi = "height:".$cmbheight.",";
			if(is_array($value)){
				foreach ($value as $keyval => $value_value) {
					if($keyval=="otoheight"){
						$autoheight = $value_value;
					}
					if($keyval=="jmlrow"){
						if($value_value<7){
							$autoheight = "true";
						}					
					}
					if($keyval=="width"){
						if($value_value!=""){
							$lebar = "width:'" . $value_value . "',";
						}
					}
					if($keyval=="height"){
						if($value_value!=""){
							$tinggi = "height:" . $value_value . ",";
						}
					}
					if($keyval=="fontsize"){
						if($value_value!=""){
							$fontsize = $value_value;
						}
					}
					if($keyval=="disabled"){
						if($value_value){
							$extrascript .= "$('#" . $key . "').jqxComboBox({ disabled: true });";
						}					
					}
					if($keyval=="multiselect"){
						if($value_value){
							$multiselect = "multiSelect:true,";
						}
					}
					if($keyval=="checkbox"){
						if($value_value){
							$checkbox = "checkboxes: true,";
							$extrascript .= "$('#" . $key . "').jqxComboBox('checkAll'); ";
						}
					}
					if($keyval=="events"){
						$comboid = $key;
						$script = "";
						foreach ($value_value as $keyevents => $valueevents) {
							$script .= "$(\"#".$key ."\").on('".$keyevents."', function(event".$comboid."){";
							if(substr($valueevents, 0,2)=="jv"){
								$script .= $valueevents;
							}else{
								$script .= "
						    var args = event".$comboid.".args;
						    if (args) {
							    var index = args.index;
							    var item = args.item;
							    var label = item.label;
							    var value = item.value;
							    var type = args.type; // keyboard, mouse or null depending on how the item was selected.
							    ".$valueevents."
								}
								";								
							}
							$script .= "});";
						}
					}
					// disini
					if($keyval=="json"){
						// $autoheight = "false";
		        		$extracombo .= ", source : dataAdapter".$key .",";

						foreach ($value_value as $keyjson => $valuejson) {
							if($keyjson=="url"){
		            			$scriptCombobox .= "
		            			var url = \"" . $valuejson . "\"";
		          			}
							if($keyjson=="fields"){
								$extracombo .= "displayMember: '".$valuejson[1]."', valueMember: '".$valuejson[0]."'";
								$scriptCombobox .= "
									var source".$key ." =
									{
										datatype: \"json\",
										datafields: [
											{ name: '".$valuejson[0]."' },
											{ name: '".$valuejson[1]."' }
										],
										url: url,
										async: false
									};
									var dataAdapter".$key ." = new $.jqx.dataAdapter(source".$key .");
								";
							}
							if($keyjson=="value"){
								$extrascript .= "$('#".$key ."').jqxComboBox('selectItem','".$valuejson."');";
							}
		          			// echo($extrascript);
							if($keyjson=="script"){
								if(!is_array($valuejson)){
									$script = $valuejson;
								}else{
									$cascade = 'true';
									$cmbwidth = 300;
									$cmbscript = "";
									$autodropdownheight = true;
									foreach ($valuejson as $arrparam=>$arrvalue){
										${$arrparam}=$arrvalue;
									}
									if(!$cascade){
										$script = $cmbscript;
									}else{
										$script = "
											$(\"#".$key ."\").bind('select', function(event".$cmbid."){
												if (event".$cmbid.".args) {
													args".$cmbid." = event".$cmbid.".args;

													$(\"#".$cmbid."\").jqxComboBox({
														disabled: false,
														selectedIndex: -1
													});
													if(args".$cmbid.".item){
														var value".$cmbid." = event".$cmbid.".args.item.value;
														". $cmbscript ."
								            			var url = \"".$cmburl."\"+value".$cmbid.";
								          			}else{
								          				var url = \"".$cmburl."0\";
								          			}
						                			var source".$cmbid." =
						                			{
														datatype: \"json\",
														datafields: [
															{ name: '".$cmbvalue."' },
															{ name: '".$cmbdisplay."' }
														],
														url: url,
														async: false
						                			};
													var dataAdapter".$cmbid." = new $.jqx.dataAdapter(source".$cmbid.");
													$('#".$cmbid."').jqxComboBox({ width:".$cmbwidth.", autoDropDownHeight: ".$autodropdownheight.", height: ".$cmbheight.", theme: '', selectionMode: 'dropDownList', 
														source : dataAdapter".$cmbid.",displayMember: '".$cmbdisplay."', valueMember: '".$cmbvalue."'
													});
					              				}
											});									
										";									
									}
		          				}
		          			}
						}
					}				
				}
			}

				$scriptCombobox .= "
					$('#" . $key . "').jqxComboBox({" . $lebar . $tinggi . "
animationType: 'none',
autoDropDownHeight: ". $autoheight.",  
theme: '" . $theme . "',
".$multiselect."
".$checkbox."
selectionMode: 'dropDownList',
enableBrowserBoundsDetection:true
".$extracombo."
																				});
					" . $extrascript ."
					" . $script;
			if(isset($fontsize)){
				$scriptCombobox .= "$('#" . $key . "').find('input').css('font-size', '".$fontsize."px');";
				unset($fontsize);
			}

			// if($checkbox!=""){
			// 	$scriptCombobox .="
	  		//               $('#" . $key . "').on('checkChange', function (event) {
			//                  if (event.args) {
			//                    var item = event.args.item;
			//                    if (item) {
			//                        var items = $('#" . $key . "').jqxComboBox('getCheckedItems');
			//                        var checkedItems = '';
			//                        $.each(items, function (index) {
			//                            checkedItems += this.value + ', ';                          
			//                        });
			//                        $('#" . $key . "').val(checkedItems);
			//                        alert($('#" . $key . "').val());
			//                    }
			//                  }
			//               });
			// 	";			
			// }			
		}
		return $scriptCombobox;
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
	function generateFunctionCmbKabupaten(){
		$script = "
		<script>
		$(document).ready(function(){
			var srcKabupaten =
			{
				datatype: 'json',
				datafields: [
					{ name: 'KAB_IDENTS'},
					{ name: 'KAB_DESCRE'},
					{ name: 'KAB_PROVNC'}
				],
				url: '/master/kabupaten/getKabupatenJQW',
				cache: false,
	      async: false
			};

			var kabAdapter = new $.jqx.dataAdapter(srcKabupaten);		

			$('#cmbKABPTN').jqxComboBox({
				theme:'shinyblack',
				width: 200,
				height: 25,
				// disabled: true,
				promptText: 'Pilih Kabupaten...',
				displayMember: 'KAB_DESCRE',
				valueMember: 'KAB_IDENTS',
				autoDropDownHeight: true
			});

			$('#cmbPROVNC').bind('select', function(event){
				if (event.args){
					$('#cmbKABPTN').jqxComboBox({ disabled: false, selectedIndex: -1});		
					var value = event.args.item.value;
					srcKabupaten.data = {KAB_PROVNC: value};
					kabAdapter = new $.jqx.dataAdapter(srcKabupaten);
					$('#cmbKABPTN').jqxComboBox({source: kabAdapter});
				}
			});

			$('#cmbKABPTN').bind('select', function(event){
				// alert($('#cmbKABPTN').val());
				$('#hidKABPTN').val($('#cmbKABPTN').val());
			});

		});
		</script>
		";
		return $script;
	}
	function generatePaging($paging, $tabs=null){
		$CI =& get_instance();
		$page = $CI->input->post('page');
		$CI->load->library(array('pagination'));
		if(is_array($paging)){
			foreach($paging as $key=>$row){
				${$key}=$row;
			}
			//=========== paging
			$config['base_url'] = $url;
			$config['per_page'] = $per_page;
			$config["uri_segment"] = $uri_segment;
			$config["anchor_class"] = "class='page gradient' ";
			$config["prev_link"] = "<";
			$config["next_link"] = ">";
			$config["first_link"] = "<<";
			$config["last_link"] = ">>";
			$config['total_rows'] = $total_rows;
			$form = 'formgw';
			$pagingtable = "
			<link rel=\"stylesheet\" type=\"text/css\" href=\"/resources/css/paging.css\" />
			<script>
				$(document).ready(function(){
					$('#pagination a').click(function () {
						var link = $(this).get(0).href; // 
						var form = $('#form1');
						var segments = link.split('/');
						$('[name=\"page\"]').val('".$page."'); // set a hidden field with the page number
						// alert('$page');
						// $('[name=\"page\"]').val(segments[$uri_segment]); // set a hidden field with the page number
						$('form#$form').attr('action', link); // set the action attribute of the form
						$('form#$form').submit();
						return false; // avoid the default behaviour of the link
					});
				});
			</script>			
			";
			
			$CI->pagination->initialize($config);		
			$paging = $CI->pagination->create_links($page);
			$pagingtable .= "
							<div id=container style=width:600px>
								<div id=pagination>" . $paging . "</div>
							</div>";
			$attr = array('class' => 'form-horizontal', 'name' => $form,'id' => $form);
			$pagingtable .= form_open(null, $attr);
			$pagingtable .= form_hidden('page',$page);
			//echo $tabs;
			if($tabs){
				$pagingtable .= form_hidden('txtTabs',$tabs);
			}
			$pagingtable .= form_close();
			//$CI->table->add_row(array('data'=>$pagingtable, 'colspan'=>5, 'style'=>'text-align:center'));    
		}
		return $pagingtable;
	}
	function autotag($arrParameter){
		$CI =& get_instance();
		
		$table=null;
		$field=null;
		$filter=null;
		$addfil=null;
		$db='db';
		$model='crud';
		$function='getTaginput';
		$protected=true;
		$separator = null;
		$funcparam = null;
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		if($model!="crud"){
			$CI->load->model($model);
		}
		if($model=="crud" && substr($function,0,6)=='getTag'){
			$resultset = $CI->{$model}->{$function}($table, $field, $filter, $addfil, $protected, $separator);
		}else{
			$resultset = $CI->{$model}->{$function}($funcparam);
		}
		$rc=false;
    	$loop = 1;
		$arr = array();
		foreach ($resultset->result() as $key => $value) {
			$arr[] = $value;
		}

		$jeson = json_encode($arr);
		if($type=="combo"){
			$jeson = '{"results":' . $jeson . '}';
		}
		if(isset($_GET["callback"])) {
		    $jeson = $_GET["callback"] . "(" . $jeson . ")";
		}		
		echo $jeson;
	}
	function hanyajson($resultset){
		$arr = array();
		foreach ($resultset->result() as $key => $value) {
			$arr[] = $value;
		}

		$jeson = json_encode($arr);
		echo $jeson;
	}
	function makePage($parameter)
	{
		$CI =& get_instance();
		$CI->load->helper('html');
		$menuny = "";
		$divnya = "";
		$functs = "";

		$widths = "85%";

		$loadonce = false;

		$Mnames = "jqxMenu";
		$idDivs = "tab";
		$arrDiv = "";
		$sMenus = array();
		foreach ($parameter as $key => $value) {
			${$key} = $value;
		}

		if(is_array($arrDiv)){
			$loop = 1;
			$ddiv = "";
			foreach ($arrDiv as $k => $v) {
				# code...
			////bikin menu + icon
				if(isset($v['icon'])){
					$icon = $v['icon'];
					if(substr($icon, 0,2)!="fa"){
						$imgprop = array(
							'src' => base_url(IMAGES .$v['icon']),
							'width' => '16',
							'height' => '16',
							'title' => $k,
							'style' => 'float:left; margin-right:5px;'
						);
						$images = img($imgprop);
					}else{
						$images = "<i class=\"fas " . $icon . "\"></li>";
					}
				}

				$sMenus[] = $images . "<span>" . $k . "</span>";	


			////bikin Div
				if($loadonce == true){///debug
					///cek semua div diberi data atau tidak
					if(trim($v['data']) == ""){
						echo "<script>swal('Warning 01 : Data awal untuk ". $k ." tidak ada!')</script>";
						return;
					}
				}
				if($loop==1){
					$ddiv .= "<div id=".$idDivs.$loop." style='height:90%'>" . $v['data'] . "</div>";
				}
			//////////////////
				$fungsinya = "
				";

				$loop++;
			}

		}

		$menuny .= "<div id='" . $Mnames . "'>" . ul($sMenus) . "</div>	";

		$divnya .= "
		<div class=row style='height:100%;'>		
			<div class='col-md-2 col-xs-5' style='height:100%;'>" . $menuny . "</div>
			<div class='col-md-10 col-xs-10' style='height:100%'>
				<div id='jqxTabs' style='height:100%'>" . $ddiv . "</div>
			</div>
		</div>
		";

		////script toggle data berdasarkan loadonce
		if($loadonce == true){
			$scload = "
				$('#jqxTabs').find('div[id^=\'".$idDivs."\']').hide();
				$('#".$idDivs."' + (prm + 1)).show();
			";

		}else{
			if(!isset($utama)){///debug
				echo "<script>swal('Warning 02 : Set url untuk parameter \'utama\'!')</script>";
				return;
			}
			$scload = "
					$.post('" . $utama . "/'+echo, function( data ) {
						$('#".$idDivs."1').html(data);
						$('#".$idDivs."1').find('div[id=\'tabval\']').css('top',0);
					});
			";
		}

		$script = "
			<script>
			$(document).ready(function(){
				// $('#divfloatGrid').css('height',(screen.width));
				$('#divfloatGrid').css('height','90%');
				
				$(\"#".$Mnames."\").jqxMenu({height:'90%', width:'100%',mode: 'vertical',theme:'orange'});
        $(\"#".$Mnames."\").css('visibility', 'visible');
				$('#".$Mnames."').on('itemclick', function (event)
				{
				    // get the clicked LI element.
				    var element = event.args;
				    // alert($(element).find('img').attr('title'));
				    klik".$Mnames."($(element));
				    // tampilinmsg(element);
				});

			});

	    function tampilinmsg(element) {
				// console.log(element.offsetLeft,element.clientLeft,element.offsetTop,element.clientTop,$(element).width());
	    	var txt = $(element).find('img').attr('title');

				$( '#message' )
				.css('left',(element.offsetLeft + $(element).width()) + 'px')
				.css('top',element.offsetTop + 'px')
				.html(txt)
				.show(1000)
				.delay(800)
				.fadeOut(1000);
	    }

			function klik".$Mnames."(elm){
				var prm = elm.index();
				echo = elm.text().toLowerCase().replace(/\s+/g, '');
				// alert(echo);

				".$scload."

			}
			</script>
		";
		$cssnya = "
		<style>
		body { overflow:hidden; }

		#divfloatGrid{
			float: right;
			margin-right: 10px;
			width:100%;
		}

		#".$Mnames."{
			width : 155px;
			float:left;
			visibility: hidden;
			margin-left:10px;
		}

		@media screen and (max-width:480px){

			#divfloatGrid{
				margin-right: 5px;
				width:90%;
			}

			#".$Mnames."{
				width : 24px;
				margin-left:-1px;
			}
			#".$Mnames." span{
				visibility: hidden;
			}

			#".$Mnames." li{
				padding: 4px 0px;
			}

		}

		</style>";

		$html = $cssnya . $divnya . $script;

		return $html;
	}

	function generateTree($parameter){
		$as_function = false;
		$create = false;
		$height = "100%";
		$width = "500px";
		$fontsize = 11;
		$scripttambahan = "";
		$scripturlcustom = null;
		$data = "";
		$element = null;
		foreach ($parameter as $indx=>$value){
			${$indx}=$value;
		}
		if(isset($url)){
			$url = "url: '" . $url . "'";
		}else{
			if(isset($urlcustom)){
				$parameter = "";
				if(isset($parameter_function)){
					$arrFunction = explode(",", $parameter_function);
					$rc = false;
					foreach($arrFunction as $keyfunct){
						if($rc) $parameter .= " + '/' + ";
						$arrkeyfunc = explode("=", $keyfunct);
						$parameter .= trim($arrkeyfunc[0]);
						$rc = true;
					}
				}else{
					$parameter = "idents";
				}
				$scripturlcustom = "urlgw = '".$urlcustom."' + " . $parameter;
				$url = "url: urlgw";
			}else{
				$url = "localdata: data";
			}
		}
		$script = null;
		if($create){
			$element = "<div id='".$name."' style='height:100%;margin-top:10px'></div>";
		}
		$script .="
				" . $scripturlcustom . "
				" . $data ."
				var source =
				{
					datatype: 'json',
					datafields: [
							{ name: 'id' },
							{ name: 'icon' },
							{ name: 'parentid' },
							{ name: 'text' },
							{ name: 'nilai' }
					],
					id: 'id',
					async: false,
					". $url ."
				};
	      var dataAdapter = new $.jqx.dataAdapter(source);
	      dataAdapter.dataBind();
	      var records = dataAdapter.getRecordsHierarchy('id', 'parentid', 'items', [{ name: 'text', map: 'label'}]);
	      $('#" . $name. "').jqxTree({ source: records, height: '". $height. "', width: '".$width."'});
	      ";
		if(isset($expandAll)){
			if($expandAll){
				$script .= "$('#" . $name. "').jqxTree('expandAll');";
			}
		}
		if(isset($collapseAll)){
			if($collapseAll){
				$script .= "$('#" . $name. "').jqxTree('collapseAll');";
			}
		}
		if(isset($event)){
			foreach($event as $keyEvent=>$valueEvent){
				$addscript = null;
				$function = null;
				if(isset($valueEvent["addscript"])){
					$addscript = $valueEvent["addscript"];
				}
				if(isset($valueEvent["function"])){
					$function = $valueEvent["function"];
				}
				$script .= "$('#" . $name. "').on('".$keyEvent."', function (event) {
					var args = event.args;
					var item = $('#" . $name. "').jqxTree('getItem', args.element);
					label = item.label;
					id = item.id;
					parentid = item.parentId;
					".$addscript."
					".$function."
				});
				";
			}

		}
		if(isset($select)){
			if($select){
				$script .="
					$('#" . $name. "').on('select', function (event) {
						var args = event.args;
						var item = $('#" . $name. "').jqxTree('getItem', args.element);                
						var id = args.element.id;
						var ip;
						var recursion = function (object) {
							for (var i = 0; i < object.length; i++) {
								if (id == object[i].id) {
									ip = object[i].nilai;
									break;
								} else if (object[i].items) {
									recursion(object[i].items);
								};
							};
						};
						recursion(records);
				";
				if($scripttambahan==""){
					$scripttambahan = "
						var dropDownContent = '<div id=".str_replace("tree", "dpr", $name). " style=\"position: relative; margin-left: 3px; margin-top: 5px;\">' + item.label + '</div>';
						$('#" . str_replace("tree", "div", $name). "').jqxDropDownButton('setContent', dropDownContent);
						$('#" . str_replace("tree", "div", $name). "').jqxDropDownButton('close');
						$('#" . str_replace("tree", "", $name). "').val(id);
					";
				}
				$script .= $scripttambahan;
				$script .="});";
			}
		}
		$scriptawal = "<script type=\"text/javascript\">";

		if($as_function){
			$funcname = "jvDisplayTree".$name;
			if($initialize){
				$scriptawal .= "$(document).ready(function () {" . $funcname . "()});";
			}			
			$script = $scriptawal . "function ".$funcname."(".$parameter_function."){" . $script;
			$script .="}";

		}else{
			$script = $scriptawal . "$(document).ready(function () {" . $script;
			$script .="});";
		}
		$script .= "</script>";

		$style = "
		<style>
		#" . $name. " li{
		    // font-size:".$fontsize."px;
			font-family:Poppins, Helvetica, 'sans-serif';
		}
		#" . $name. " {
			border:none !important;
		}
		</style>
		";
		$script .= $style;
		$script .= $element;
		return $script;
	}
	function cetak($pass){
		$script = "";
		foreach ($pass as $param=>$value){
		${$param}=$value;
		}		
		$scriptCetak = "
		<script>
		function jvCetak(tipe){
			var param = {};
			". $parameter ."
			". $script . "
			$.post('/".$controllerdata."',param,function(data){
			if(data=='0'){
				swal('Tidak ada data!');
				return;
			}else{
				userWidth = window.screen.width;userHeight = window.screen.height;
				openCenterWin('/".$controllerreport.", userWidth, userHeight, '', 'status=1,fullscreen=1,toolbar=0,scrollbars=1,menubar=1')
			}
			});
		}
		function openCenterWin(url, height, width, name, parms){
			$('#printanimasi').css('display','none');
			var left = Math.floor( (screen.width - width) / 2);
			var top = Math.floor( (screen.height - height) / 3);
			var winParms = 'top=' + top + ',left=' + left + ',height=' + height + ',width=' + width;
			if (parms) { winParms += ',' + parms; }
			var win = window.open(url, name, winParms);
			if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
			return ;
		}      
		</script>  		
		";
		return $scriptCetak;
	}
	function generateWindowjqx($arrParameter){
		$CI = get_instance();
		$theme = $CI->config->item('app_grids'); // grid theme, the value came from config.php
		$USR_THEMES = $CI->session->userdata("USR_THEMES");
		if($USR_THEMES!=""){
			$theme = $USR_THEMES;
		}		
		$CI->load->helper(array('form','html'));
		// $overflow = "hidden";
		$loop = 1;
		$widths = "90%";
		$height = "90%";
		$isModal = "true";
		$autoOpen = "false";
		$autoClose = true;
		$animationType = "none";
		$showCloseButton = true;
		$content = "
		<style>
		.container-fluid{
		height:100%;
		display:table;
		width: 100%;
		padding: 0;
		}
		.row-fluid {height: 100%; display:table-cell; vertical-align: middle;}
		.centering {
		float:none;
		margin:0 auto;
		}
		</style>
		<div class=\"container-fluid\">
			<div class=\"row-fluid\">
				<div class=\"centering text-center\">
				<img src=\"" . base_url(IMAGES.'spinloader.gif') . "\">
				</div>
			</div>
		</div>
		";
		$content = "";
		$loader = "<center><img src=\"" . base_url(IMAGES.'spinloader.gif') . "\"></center>";
		$heightwidth = "";
		$contentwidth = 1000;
		// $maxWidth = '100%';
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		$maxLebar = "";
		$maxTinggi = "";
		$minLebar = "";
		if(isset($maxWidth)){
			// $maxWidth = $widths;
			$maxLebar = "maxWidth: '".$maxWidth."',";
		}
		if(isset($maxHeight)){
			// $maxWidth = $widths;
			$maxTinggi = "maxHeight: '".$maxHeight."',";
		}
		if(isset($minWidth)){
			// $minWidth = $maxWidth;
			$minLebar = "minWidth: '".$minWidth."',";
		}
	
		if($height!="auto" && $widths!="auto"){
			$heightwidth = "
	        	height: '" . $height . "', 
	        	width:'".$widths."',
			";
			if(is_numeric($widths)){
				$contentwidth = $widths-20;	
			}
		}else{
			$heightwidth = "
	        	height: 'auto', 
	        	width:'auto',
			";			
		}
		
		// if($height!="auto" && $widths!="auto"){
		// 	$heightwidth = "
		//        	height: '" . $height . "', 
		//        	width:'".$widths."',
		//        	maxHeight: '" . $height . "', 
		//        	maxWidth: '" . $widths . "', 
		// 	";
		// 	$contentwidth = $widths-20;
		// }else{
		// 	$heightwidth = "
		//        	height: 'auto', 
		//        	width:'auto',
		// 	";			
		// }
		if($isModal){
			$isModal = "isModal : true,";
		}
		$showclose = "";
		if(!$showCloseButton){
			$showclose = "showCloseButton : false,";
		}
		$windowscript = "
			<script>
	      $(document).ready(function(){
	        $('#jqw". $window . "').jqxWindow({
	        	theme : '".$theme."',
	        	position: 'center, center',
	        	" .$isModal. "
	        	autoOpen: ".$autoOpen.",
	        	".$heightwidth."
				".$maxLebar."
				".$maxTinggi."
	        	".$minLebar."
	        	".$showclose."
	        	animationType:'".$animationType."' 
	        	});";
		if($autoClose){
			$windowscript .= "$('#jqw". $window . "').on('close', function (event) { $('#jqw". $window . "').jqxWindow('setContent', '".$content."'); }); ";
		}
		if(isset($event)){
			foreach ($event as $keyE => $valueE) {
				$windowscript .= "$('#jqw". $window . "').on('".$keyE."', function (event) { ".$valueE." }); ";
			}
		}

	  $windowscript .= "    });				
			</script>";
		$vOverflow = "";
		if(isset($overflow)){
			$vOverflow .= "overflow:".$overflow; 
		}else{
			if(isset($overflowx)){
				$vOverflow .= "overflow-x:".$overflowx .";";
			}
			if(isset($overflowy)){
				$vOverflow .= "overflow-y:".$overflowy .";";
			}			
		}
		if($vOverflow==""){
			$vOverflow = "overflow:hidden"; 
		}
    	// $windowresult = ($content!="" ? $windowscript : "" ) .  "<div id=\"jqw" . $window. "\">			
    	$windowresult = $windowscript .  "<div id=\"jqw" . $window. "\">
          <div id=\"customWindowHeader\">
              <span id=\"captureContainer\" style=\"float: left\">".$title."</span>
          </div>
          <div id=\"customWindowContent\" style=\"".$vOverflow."\">
              <div id=\"winContent\" style=\"margin: 10px;width:95%\">". $content . "</div>
          </div>
      	</div>";
      	return $windowresult;
	}
	function generatePopup($arrParameter){
		$CI = get_instance();
		$CI->load->helper('jqxgrid');
		$width = 380;
		$index = "CHK_IDENTS";
		$field = "txtUNIORG";
		$url ="";
		$gridname = 'jqxgrid';
		$autorowheight = false;
		$setdatanya = "";
		$scripoverride = "";
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		if($scripoverride!=""){
			$setdatanya = $scripoverride;
		}else{
			if(is_array($field)){
				foreach ($field as $key => $value) {
					$setdatanya .= "var pop".$key."=rowData.".$value.";$('#".$key ."').val(pop".$key.");";
				}
			}else{
				$setdatanya = "
			    var popINDEX = rowData.". $index ."
			    $('#".$field ."').val(popINDEX);
				";
			}			
		}
	
		$arrGrid = array('col'=>$col, 'url'=>$url, 'gridname'=> $gridname, 'pagermode'=>'simple','width'=>$width,'showToolbar'=>false, 'autorowheight'=>$autorowheight);
		if(isset($height)){
			$arrGrid = array_merge($arrGrid, array('height'=>$height));
		}
		
		if(isset($columnsheight)){
			$arrGrid = array_merge($arrGrid, array('columnsheight'=>$columnsheight));
		}
		$content = gGrid($arrGrid);
		$content .= "<center><div id=\"". $gridname. "\"></div></center>";
		$content .= "
		<style>
        #footer
        {
          position: absolute;
          bottom: 0px;
          height: 15px;
          width: 100%;
          border-top-width: 1px;
          border-top-style: solid;
          border-bottom: none;
        }		
		</style>
		<script>
		$('#". $gridname. "').on('rowselect', function (event) 
		{
		    var args = event.args;
		    var rowData = args.row;
		    ". $setdatanya."
        $('#". $windowname. "').jqxWindow('close');
		});
		</script>";

		return $content;
	}
	function scrThumbnail($arrParameter=null){
		$id = "thumbnailList1";
		$backgroundColor="#ccc";
		$imageDivClass = "dpic";
	  $thumbWidths = 120;
	  $thumbHeight = 100;

		if($arrParameter!=""){
			foreach ($arrParameter as $detail=>$valueAwal){
				${$detail} = $valueAwal;
			}			
		}
		$script = "
		<script>
			$(document).ready(function() {		
				$(\"#". $id. " img\").MyThumbnail({
				  thumbWidth:".$thumbWidths.",
				  thumbHeight:".$thumbHeight.",
				  backgroundColor:\"".$backgroundColor."\",
				  imageDivClass:\"".$imageDivClass."\"
				});	
			});
		</script>		
		";
		return $script;
	}
	function scrImages($arrParameter=null){
		$type = "single";
		if($arrParameter!=""){
			foreach ($arrParameter as $detail=>$valueAwal){
				${$detail} = $valueAwal;
			}			
		}
		$script = "
		<script>
			$(document).ready(function() {
				$('.".$class."').magnificPopup({";
		if($type=="single"){
			$script .= "
					type: 'image',
					closeOnContentClick: true,
					fixedContentPos: true,
					mainClass: 'mfp-no-margins mfp-with-zoom',
					image: {
						verticalFit: true
					}
			";			
		}else{
			$script .= " 
				delegate: 'a',
				type: 'image',
				tLoading: 'Loading image #%curr%...',
				mainClass: 'mfp-img-mobile',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
					preload: [0,1] // Will preload 0 - before current, and 1 after the current image
				},
				image: {
					tError: '<a href=\"%url%\">The image #%curr%</a> could not be loaded.',
					titleSrc: function(item) {
						return item.el.attr('title');
					}
				}			
			";
		}

		$script .="
			});
		});
		</script>		
		";
		return $script;
	}
	function scheduler($arrParameter=null){
		$CI = get_instance();
		$theme = $CI->config->item('app_theme'); // grid theme, the value came from config.php
		$USR_THEMES = $CI->session->userdata("USR_THEMES");
		if($USR_THEMES!=""){
			$theme = $USR_THEMES;
		}		
		// $theme = "metro";
		$url = "";
		$todate = null;
		$year = date('Y');        
		$month = date('m');
		$day = '01';
		$width = '100%';
		$height = '100%';
		$schedulename="scheduler";
		$editDialog = "editDialog: false,";
		$appointmentTooltips = "appointmentTooltips: false,";
		$enablehover = "enableHover: false,";
		$defaultview = "monthView";
		$optionview = "'monthView'";
		$scriptevents = "";
		if($arrParameter!=""){
			foreach ($arrParameter as $detail=>$valueAwal){
				${$detail} = $valueAwal;
			}			
		}
		if(is_array($optionview)){
			$option = "";
			$ro =false;
			for($x=0;$x<count($optionview);$x++){
				if($ro) $option .= ",";
				if(is_array($optionview[$x])){
					$option .= "{";
					$rcin = false;
					for ($i=0; $i < count($optionview[$x]); $i++) { 
						if($rcin) $option .= ",";
						if(strpos($optionview[$x][$i],":")==0){
							$option .= "type:'" . $optionview[$x][$i] ."'";		
						}else{
							$option .= $optionview[$x][$i];
						}
						$rcin = true;	
					}
					$option .= "}";
				}else{
					$option .= "'" . $optionview[$x] . "'";	
				}
				
				$ro =true;
			}
			$optionview = $option;
		}
		$dataFields = "";
		$rc = false;
		$ri = false;
		$include = "";
		foreach ($col as $key => $value) {
			if($rc) $dataFields .= ", ";
	  	$dataFields .= "{ ";
	  	$dataFields .= "name : '".$value['namanya']."'";
	  	$dataFields .= ", type : 'string'";
	  	if(isset($value['type'])){
	  			$dataFields .= ", type : '".$value['type']."'";
	  	}
	  	if(isset($value['format'])){
	  		$dataFields .= ", format : '".$value['format']."'";
	  	}
	  	if(isset($value['fromdate'])){
	  		$fromdate = $value['namanya'];
	  	}
	  	if(isset($value['todate'])){
	  		$todate = $value['namanya'];
	  	}
	  	if(isset($value['subject'])){
	  		$subject = $value['namanya'];
	  	}
	  	if(isset($value['include'])){
	  		if($value['include']){
	  			if($ri) $include .= ", ";
	  			if($value['namanya']=="calendar"){
	  				$include .= "resourceId : '".$value['namanya']."'";
	  			}else{
	  				$include .= $value['namanya'] . " : '".$value['namanya']."'";	
	  			}
	  			$ri=true;
	  		}
	  	}
	  	$dataFields .= "}";
	  	$rc = true;
	  }
	  $scriptsource = "
          var source =
          {
              dataType: 'json',
              dataFields: [".$dataFields."],
              id: '".$id."',
              url: '". $url . "'
          };
          var adapter = new $.jqx.dataAdapter(source);	  
	  ";
	  if(isset($events)){
	  	$scriptevents = "";
	  	foreach ($events as $keyevents => $valueevents) {
	  		$scriptevents .= "$('#".$schedulename."').on('".$keyevents."', function (event) {";
	  		$scriptevents .= $valueevents;
	  		$scriptevents .= "});";
	  	}
	  	$scriptevents .= "";
	  }
	  // 
    $script = "

      <script type='text/javascript'>
        $(document).ready(function () {
        	".$scriptsource."
          $('#".$schedulename."').jqxScheduler({
						theme : '" . $theme . "',
            date: new $.jqx.date(".$year.", ".$month.", ".$day."),
            width:  '".$width."',
            height: '".$height."',
            source: adapter,
       			localization: {
							firstDay: 1,
							days: {
								names: [\"Minggu\",\"Senin\",\"Selasa\",\"Rabu\",\"Kamis\",\"Jumat\",\"Sabtu\"],
								namesAbbr: [\"Mgu\",\"Sen\",\"Sel\",\"Rab\",\"Kam\",\"Jum\",\"Sab\"],
								namesShort: [\"Mg\",\"Sn\",\"Sl\",\"Rb\",\"Km\",\"Jm\",\"Sa\"]
							},
							months: {
								names: [\"Januari\",\"Februari\",\"Maret\",\"April\",\"Mei\",\"Juni\",\"Juli\",\"Agustus\",\"September\",\"Oktober\",\"November\",\"Desember\",\"\"],
								namesAbbr: [\"Jan\",\"Feb\",\"Mar\",\"Apr\",\"Mei\",\"Jun\",\"Jul\",\"Ags\",\"Sep\",\"Okt\",\"Nov\",\"Des\",\"\"]
							},
            	monthViewString: \"Bulan\",
            	timelineMonthViewString: \"Timeline Bulan\"
       			},
            showLegend: true,
            " . $editDialog ."
            " . $appointmentTooltips . "
            " . $enablehover . "
            ready: function () {
           
            },

		        resources:
		        {
		            colorScheme: \"scheme05\",
		            dataField: \"calendar\",
		            source:  new $.jqx.dataAdapter(source)
		        },            
            appointmentDataFields:
            {
              from: '".$fromdate."',
              to: '".$todate."',
              id: '".$id."',
              subject: '".$subject."',
              ". $include . "
            },
            appointmentsMinHeight: 20,
            view: '".$defaultview."',
            views:
            [
                ".$optionview."
            ]
          });
					" . $scriptevents . "
        });
    </script>
    <div id='".$schedulename."' style='width:99%'></div>
    ";

    return $script;
	}
	function generateToolbar($arrParameter){
		$CI = get_instance();
		$theme = $CI->config->item('app_theme'); // grid theme, the value came from config.php
		$USR_THEMES = $CI->session->userdata("USR_THEMES");
		if($USR_THEMES!=""){
			$theme = $USR_THEMES;
		}		
		$arrCombo = array();
		$toolbarname = "toolbar";
		$name = "";
		$createToolbar = false;
		$autoDropDownHeight = 'true';
		// $lebartoolbar = "100%";
		$lebartoolbar = "width:'100%',";
		$tinggitoolbar = "height: 40,";
		$width = "";
		$RTL = false;
		$kanan = "";
		if($arrParameter!=""){
			foreach ($arrParameter as $detail=>$valueAwal){
				${$detail} = $valueAwal;
			}			
		}
		if($toolbarname!="toolbar"){
			$name = str_replace("toolbar", "", $toolbarname);
		}
		
		if($RTL){
			$kanan = "rtl: true,";
		}

		$toggle = "'";
		$indexToolbar = 0;
		$endofCombo = 0;
		$srcOptions = "";
		$toolbarAdapter = "";
		$variable = "";
		$comboscript = "";
		if(isset($toolbarCombo)){
			if(is_array($toolbarCombo)){
				// if($indexToolbar>0){
				// 	$toggle .= " | "; 	
				// }
				
				$rt = false;
				foreach ($toolbarCombo as $key => $value) {
					if($rt) $toggle .=" ";
					$toggle .= "combobox";
					$toolbarValue = "";
					$toolbarEvents = "";
					$toolbarJson = "";
					$toolbarSource = "";
					$toolbarHeight = "";
					$toolbarIdents = $toolbarCombo[$key]['idents'];
					$toolbarWidths = $toolbarCombo[$key]['width'];
					$toolbarPlchdr = $toolbarCombo[$key]['placeHolder'];

					if(isset($toolbarCombo[$key]['height'])){
						$toolbarHeight = $toolbarCombo[$key]['height'];	
					}

					if(isset($toolbarCombo[$key]['variable'])){
						if($toolbarCombo[$key]['variable']){
							$variable .= "var " . $toolbarIdents . ", " . $toolbarIdents."Min;";	
						}
					}
					if(isset($toolbarCombo[$key]['events'])){
						$toolbarEvents = $toolbarCombo[$key]['events'];
					}
					if(isset($toolbarCombo[$key]['json'])){
						$toolbarJson = $toolbarCombo[$key]['json'];
					}
					if(isset($toolbarCombo[$key]['comboscript'])){
						$comboscript .= $toolbarCombo[$key]['comboscript'];
					}

					if(isset($toolbarCombo[$key]['value'])){
						$toolbarValue = $toolbarCombo[$key]['value'];
					}
					if(isset($toolbarCombo[$key]['source'])){
						$toolbarSource = $toolbarCombo[$key]['source'];
					}
					if(isset($toolbarCombo[$key]['autoDropDownHeight'])){
						$toolbarautoDropDownHeight = $toolbarCombo[$key]['autoDropDownHeight'];
					}else{
						$toolbarautoDropDownHeight = 'true';
					}
					if(isset($toolbarCombo[$key]['adapter'])){
						$toolbarAdapter .= $toolbarCombo[$key]['adapter'];
					}
					$arrCombo = array_merge($arrCombo, 
						array(
							array(
								'idents'=>$toolbarIdents,
								'widthcombo'=>$toolbarWidths,
								'index'=>$indexToolbar,
								'source'=> "source".$key,
								'events'=> $toolbarEvents,
								'placeHolder'=>$toolbarPlchdr,
								'value'=>$toolbarValue,
								'json'=>$toolbarJson,
								'heightCombo' => $toolbarHeight,
								'autoDropDownHeight'=>$toolbarautoDropDownHeight
								)
							)
						);
					
					$srcOptions .= "
var source".$key." = [
";
					$rcmb = false;
					if(is_array($toolbarSource)){
						foreach ( $toolbarSource as $keysrc => $valuesrc) {
							if($rcmb) $srcOptions .= ",";
								$srcOptions .= "{ value : '".$keysrc."', text : '".$valuesrc."'}";
							$rcmb = true;
						}
					}
$srcOptions .= "
];";			
					$rt = true;
					$indexToolbar++;
					$endofCombo =$indexToolbar;
				}
			}
		}
		$rt=false;
		if(count($arrButton)>0){
			if($indexToolbar>0){
				$toggle .= " | "; 	
			}
		}

		for($e=0;$e<count($arrButton);$e++){
			if($rt) $toggle .=" ";
			$toggle .= "custom"; 
			$indexToolbar++;
			$rt = true;
		}		
			
		$styleToolbar = "
    <style type=\"text/css\">
        .buttonIcon
        {
            margin: -5px 0 0 -3px;
            width: 16px;
            height: 17px;
        }
    </style>
		";
    $toggle .= "'";
		$buttonToolbar = $srcOptions . $toolbarAdapter . $variable . "
		$(\"#" . $toolbarname ."\").jqxToolBar({
			theme : '" . $theme . "',
			".$lebartoolbar." 
			".$tinggitoolbar."
			tools: ". $toggle .",
			" . $kanan . "
			initTools: function (type, index, tool, menuToolIninitialization) {
        switch (index) {
    ";
    $buttontop = "margin-top:5px";
    $loop = 1;
    foreach ($arrButton as $keybutton => $valuebutton) {
    	$idx = $keybutton+$endofCombo;
    	$idx = substr("0000" . $idx, -4);//$keybutton+$endofCombo;
    	$buttidx = "butt".$name . $idx;
    	// $text = $valuebutton['text']!="" ? "tool.text(\"".$valuebutton['text']."\")" : "";
    	$text = $valuebutton['text']!="" ? $valuebutton['text'] : "";
    	$image = $valuebutton['image'];
    	$events = $valuebutton['events']!="" ? "tool.on('click', function (event) { " . $valuebutton['events'] ."});" : "";
    	$theme = $valuebutton['theme']!="" ? "template: '" . $valuebutton['theme'] ."'" : "template: 'info'";
    	$width = $valuebutton['width']!="" ? ", width: " . $valuebutton['width'] : "";
			// $buttemp .= "

			if($text!=""){
				$button = "var " . $buttidx ."= $(\"<div id='".$buttidx."' style='float: left;'>" . $image . "<span style='margin-left: 4px; position: relative; top: -2px;'>" . $text ."</span></div>\");";	
			}else{
				$button = "var " . $buttidx ."= $(\"<div style='float: left;'><span style='margin-left: 4px; '>" . $image ."</span></div>\");";
			}

$button .= "tool.append(".$buttidx.");
".$buttidx.".jqxButton({".$theme.$width."});
";			
    	$buttonToolbar .= "
case " . $idx .":
" . $button ."
" . $events;	
$buttonToolbar .= "
break;";
			$loop++;		
    }

		$eventsCombo = "";
		if(isset($arrCombo)){
			if(count($arrCombo)>0){
				foreach ($arrCombo as $keyCombo => $valueCombo) {
					$kindss = substr($valueCombo['idents'],0,3);
					$jqxcombox = "";
					$eventsCombo = "";
					$widthCombo = isset($valueCombo['widthcombo']) ? $valueCombo['widthcombo']  : "150";
					$heightCombo = isset($valueCombo['heightcombo']) ? $valueCombo['heightcombo']  : "28px";
					$placeHolder = $valueCombo['placeHolder']!="" ? ", placeHolder: '" .$valueCombo['placeHolder'] ."'" : "";
					$autoHeight = ($valueCombo['autoDropDownHeight']!="" ? $valueCombo['autoDropDownHeight']  : "true");
					
					if(isset($valueCombo['events'])){
						if($valueCombo['events']!=""){
							// $keysevents = array_keys(array_keys($valueCombo['events']));
							if(is_array($valueCombo['events'])){
								$eventsCombo = "";
								foreach ($valueCombo['events'] as $kEvents => $vEvents) {
									if($kEvents!="none"){
										$eventsCombo .= "tool.on('".$kEvents."', function (event) { " . $vEvents . "; });";
									}else{
										$eventsCombo .= $vEvents;
									}									
								}

								// $keysevents = (array_keys($valueCombo['events']));
								// $eventsCombo = $valueCombo['events']!="" ? ("tool.on('".$keysevents[0]."', function (event) { " . $valueCombo['events'][$keysevents[0]]. "; });") : "";	
							}else{
								$eventsCombo = $valueCombo['events'];
							}
						}
					}
					switch ($kindss) {
						case "txt":
							$jqxcombo = "jqxComboBox({ width: ".$widthCombo.", valueMember: 'value' " . $placeHolder ."});";		
							break;
						case "cmb":
							if(is_array($valueCombo['json'])){
								$jsonvalue = $valueCombo['json'];	
								foreach ($valueCombo['json'] as $param=>$meter){
									${$param} = $meter;
								}								
								$jqxcombo = "jqxComboBox({ width: '".$widthCombo."',autoDropDownHeight:".$autoHeight.", height:'".$heightCombo."', source: ".$source.", animationType: 'none', displayMember: '".$displayMember."', valueMember: '".$valueMember."' " . $placeHolder ."});";		
							}else{
								$jqxcombo = "jqxComboBox({ width: '".$widthCombo."',autoDropDownHeight:".$autoHeight.", height:'".$heightCombo."', source: ".$valueCombo['source'].",  animationType: 'none', displayMember: 'text', valueMember: 'value' " . $placeHolder ."});";			
							}
							// 
							
							if(isset($valueCombo['value'])){
								$jqxcombox = "tool.jqxComboBox('selectItem','".$valueCombo['value']."');";	
							}
							break;
					}

			    $buttonToolbar .= "
			case " . $valueCombo['index'] .":
			tool." . $jqxcombo .";
			".$jqxcombox."
			tool.attr('id','".$valueCombo['idents']."');
			" . $eventsCombo . "
			break;
			";
				}	
			}	
		}
		$buttonToolbar .= "
        }
			}
		});" . $comboscript ."";
		if($toggle==="''"){
			$createToolbar = false;	
			$buttonToolbar = "";
		}

		if($createToolbar){
			$buttonToolbar = "<script type=\"text/javascript\">
      $(document).ready(function () {". $buttonToolbar . "});</script>";	
			$buttonToolbar .= "<center><div id=\"" . $toolbarname . "\" style='width:".$width.";margin-bottom:5px'></div></center>";
		}
		return $buttonToolbar;		
	}

	function code128A($char){
		if(ord($char) == 32){
			$code128A = "00";
		}else{
			$tempcode = (ord($char)) - 32;
			$code128A = $tempcode;
		}
		return $code128A;
	}
	function debug_array($array, $stop=true, $text=null){
		echo "<pre>";
		if($text!=""){
			echo "================================
";
			echo $text . "
================================
";
		}
		print_r($array);
		echo "</pre>";
		if($stop){
			die();	
		}
	}	

	function generateNavjqx($arrParameter){
		$CI = get_instance();
		$CI->load->helper(array('form','html'));
		$theme = $CI->config->item('app_grids'); // grid theme, the value came from config.php
		$USR_THEMES = $CI->session->userdata("USR_THEMES");
		if($USR_THEMES!=""){
			$theme = $USR_THEMES;
		}		
		$atas = "";
		$isi = "";
		$save = "";
		$loop = 1;
		$tabwidth = "100%";
		$tabheight = "100%";
		$width = "800";
		$margintop = "125";
		$style = "";
		$fontstyle ="";
		$expandmode = "singleFitHeight";
		$animation = "none";
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		$content ="
			<script type=\"text/javascript\" src=\"".base_url(PLUGINS."jqwidgets/jqxnavigationbar.js"). "\"></script>
			<script>
					$(document).ready(function(){
						$('#nav" . $id . "').on('created', function () { 
							$('#loading').hide();
						}); 

						$('#nav" . $id . "').jqxNavigationBar({ theme:'" . $theme . "', width:'" . $tabwidth . "', height: '" . $tabheight . "', expandMode: '".$expandmode."', animationType: '".$animation."' });

		";
		$content .= "
					});
			</script>";
		// $content .= "<center><div id='nav". $id ."' style='width:100%'>";/
		$content .= "<div id='nav". $id ."' style='width:100%'>";
		foreach($arrTabs as $key=>$nilai){
			if(strpos($key,"^")==0){
				$content .= "<div " . $style . ">" . $key . "</div>";
			}else{
				$gambar = "";
				$fawesom = "";
				$arrKey = explode("^", $key);

				$image = $arrKey[0];
				$fayesno = substr($image, 0,4)=="fas " ? 'true' : 'false';
				$texts = $arrKey[1];

				if($fayesno=="false"){
					$gambar = "<img style='float: left;' width='25' height='25' src='/resources/img/". $image."' alt='' class='small-image'/>";	
				}else{
					$arrImage = explode(" ", $image);
					$gambar = "<i class=\"fas " . $arrImage[1] . "\" style='margin-left:10px'></i>&nbsp;&nbsp;";
				}

				$content .= "<div " . $style . ">".$gambar . $texts."</div>";
				// $atas .= $gambar . "<div style=\"float: left; margin-left: 6px; text-align: center; vertical-align:middle; font-size: 13px;\">" . $fawesom . "".$texts."</div>";
			}
			foreach($nilai as $keyval => $value){
				if($keyval=="data"){
					$content .= "<div id=\"content" . $id . $loop . "\" style='" . $fontstyle . " height:100%'><div style='margin-top:".$margintop."px'>" .  $value. "</div></div>";	
				}
			}

			$loop++;
		}
		// $content .= "</div></center>";
		$content .= "</div>";
		return $content;
	}
	function getAuthuser(){
		$CI = get_instance();
		$url1 = $CI->uri->segment(2);
		$url2 = $CI->uri->segment(3);
		$url = $url1 . "/" . $url2;
		$otorisasi = $CI->common->otorisasi($url);
		return $otorisasi;
	}
	function generateDivjqx($arrParameter){
		$atas = "";
		$isi = "";
		$save = "";
		$loop = 1;
		$tabwidth = "100%";
		$tabheight = "100%";
		$width = "800";
		$margintop = "25px";
		$style = "";
		$fontstyle ="";
		$animation = "none";
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}

		$content ="";
		$dimensi = "";
		$rc=false;
		if($tabwidth!=0){
			$dimensi .= "width:".$tabwidth.";";
		}
		if($tabwidth!=0){
			$dimensi .= "height:".$tabheight.";";
		}
		foreach($arrTabs as $key=>$nilai){
			if(strpos($key,"^")==0){
				$content .= "<div " . $style . " id='" . $key ."'>";
			}else{
				$arrKey = explode("^", $key);

				$idents = $arrKey[0];
				if(isset($arrKey[1])){
					$style = $dimensi . $arrKey[1];	
				}

				$content .= "<div id='".$idents."' style='".$style."'>";
			}
			foreach($nilai as $keyval => $value){
				if($keyval=="data"){
					// $content .= "<div id=\"content" . $id . $loop . "\" style='" . $fontstyle . " height:100%'><div class=row style='width:100%;height:".$margintop."'></div>" .  $value. "</div>";	
					$content .= $value;	
				}
			}
			$content .="</div>";
			$loop++;
		}
		$content .= "</div>
		";
		return $content;
	}
	function windowSize($type=null){
		$script = "<script></script>";
		echo $script;
	}
 	function showReport($types,$opt,$rpt){
		$ort = "P";
		$descre = $types;
		$string = read_file("temp/$rpt.rpcx");
		unlink("temp/$rpt.rpcx");
		switch ($opt) {
			case 1:
			echo $string;
				break;
			case 2:
			// Fungsi header dengan mengirimkan raw data excel
			header("Content-type: application/vnd-ms-excel");    
			// Mendefinisikan nama file ekspor "Laporan-HRD.xls"
			header("Content-Disposition: attachment; filename=Laporan-$descre.xls");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			header("Pragma: public");
			echo $string;
				break;
			default:
				pdf_create($string,$ort,"Laporan $descre.pdf");
				break;
		}
	}
	function format_number($value, $typess=1, $decimal=null, $dec_point=null, $thousands_sep=null){
		$return = "0";
		$value = trim($value)=="" ? "0" : trim($value);
		if($value=="0"){
			if($typess==1){
				$return = "-";
			}else{
				$return = number_format($value, $decimal);
			}
			
		}else{
			if($decimal=="" && $dec_point=="" && $thousands_sep==""){
				if($typess==1){
					$return = number_format($value);
				}else{
					$return = $value;
				}
			}
			if($decimal!="" && $dec_point=="" && $thousands_sep==""){
				$return = number_format($value, $decimal);
			}
			if($decimal!="" && $dec_point!="" && $thousands_sep==""){
				$return = number_format($value, $decimal, $dec_point);
			}
			if($decimal!="" && $dec_point!="" && $thousands_sep!=""){
				$return = number_format($value, $decimal, $dec_point, $thousands_sep);
			}
		}
		return $return;
	}
	function getJs($full=true){

		$content ="<script src=". base_url(JS."jquery.min.js"). "></script>";
		$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxcore.js")."></script>";
		$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxwindow.js")."></script>";
		$content .="<link rel='stylesheet' href=". base_url(PLUGINS."jqwidgets/styles/jqx.base.css"). " type='text/css'>";
		$content .="<link rel='stylesheet' href=". base_url(PLUGINS."jqwidgets/styles/jqx.custom.css"). " type='text/css'>";		

		if($full){		
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxnumberinput.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."bootstrap/bootstrap.min.js") . "></script>";	
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdropdownbutton.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdate.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdatetimeinput.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxnumberinput.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxcalendar.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxcombobox.js")."></script>";

			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdata.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdata.export.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdatatable.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.export.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.filter.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.columnsresize.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.grouping.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.selection.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.sort.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.pager.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxscrollbar.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxbuttons.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdropdownbutton.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxmenu.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxdropdownlist.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxlistbox.js")."></script>";
			$content .="<script src=" . base_url(PLUGINS."jqwidgets/jqxtoolbar.js")."></script>";
		}		
		$content .= "
		<div id='windowProses'>
			<div id='headerProses'>
				<span id='captureContainer' style='float: left'>Processing..</span>
			</div>
			<div id='content' style='overflow: hidden'>
				<img id=imgPROSES src='" . base_url(IMAGES."process.gif") . "' style='display:none;width:400px;height: 300px'>
			</div>
		</div>
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#windowProses').jqxWindow({isModal: true, autoOpen: false, height: '320px', width:'410px', animationType:'none', maxWidth: '900', zIndex:'9999999'});
				$('#windowProses').jqxWindow('bringToFront');

			});
		</script>
		";
		return $content;
	}
	function getCss(){
		$CI = get_instance();
		$apps_theme = $CI->config->item('app_theme');
		$grid_theme = $CI->config->item('app_grids');

		$content ="<link rel='stylesheet' href='" .  base_url(CSS."fonts/opensans.css") ."'>";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."font-awesome/css/fontawesome.min.css")."'>";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."font-awesome/css/solid.css")."'>";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."bootstrap/css/bootstrap.css")."'>";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."metronic/global/css/components-rounded.min.css")."' id='style_components' type='text/css' >";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."metronic/global/css/plugins.min.css")."' type='text/css' />";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."metronic/global/plugins/simple-line-icons/simple-line-icons.min.css")."'>";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."sweetalert/css/sweetalert2.min.css")."'>";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."jqwidgets/styles/jqx." . $grid_theme . ".css") ."'>";
		$content .="<link rel='stylesheet' href='" . base_url(PLUGINS."jqwidgets/styles/jqx." . $apps_theme . ".css") ."'>";
        
		return $content;
	}	
	function prnFile($table){
		$CI = get_instance();
		$CI->load->helper(array('file'));
		$html = "<link rel=\"stylesheet\" href=\"".base_url(PLUGINS."bootstrap/css/bootstrap.min.css") ."\">";
		$html = "<link rel=\"stylesheet\" href=\"".base_url(CSS."report.css") ."\">";
		$html .= "<html>";
		$html .= "
		<style>
		</style>
		<div id='print-modal-controls'>
			<a href='javascript:window.print()' class='print' title='Print page'>Cetak</a>
			<a href='javascript:window.close()' class='close' title='Close print preview'>Close</a>
		</div>
		";
		$html .= $table . prnFooter() ."</html>";
		$rpt = "rpt".rand(1,9).rand(1,9).rand(1,9).".rpcx";
		if ( ! write_file("temp/$rpt", $html, 'w')){
			echo 'Unable to write the file';
		}else{
			echo substr($rpt,0,-5);
		}		
	}

	function prnFooter(){
		$CI = get_instance();
		$footer = "
		<div style='width:100%'>
			<div class='col-md-6' style='float:left;height:200px;'>
				<div style='padding:180px 0px 0px 0px;width:400px;font-size:8pt;font-family:arial'>dicetak oleh : ".$CI->session->userdata('USR_LOGINS').", " . date('Y-m-d H:i:s') . "</div>
			</div>
		</div>
		";
		return $footer;
	}
	function removespecial($string){
		$string = str_replace(array('[\', \']'), '', $string);
		$string = preg_replace('/\[.*\]/U', '', $string);
		$string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '', $string);
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');
		$string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
		$string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '', $string);
		return strtolower(trim($string, '-'));
	}
	function createportlet($arrParameter){
		$backgroundColor = "";
		$content = "";
		$card_header = null;
		// $title = "";
		// $icon = "icon-speech";
		$portletbutton = true;
		$iconcolor = "primary";
		$titlecolor = "font-green-sharp";
		$txtAction = "";
		$class = "";
		$caption_helper = "";
		$border = false;
		$bordered = null;
		$bottom = false;
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		if($backgroundColor!=""){
			$backgroundColor = "style='background-color:".$backgroundColor."'";
		}
		if(isset($listaction)){
			$count_button = count($listaction);
			$txtAction = null;
			if($count_button>1){
				// $txtAction ="<div class='btn-group'>";
			}
			if($portletbutton){
				$txtAction .= "<div class='actions'>";
				$indexbutton = "-1";
				if(is_array($listaction)){
					$rc = false;
					$next = "";
					for($e=0;$e<count($listaction);$e++){
						// debug_array($listaction);
						$href = "javascript:;";
						$iconact = "fas fa-pencil";
						$textact = "detanto";
						$theme = "default";
						$style = "";
						$dropdown = false;
						$dropdown_toggle = null;
						$dropdown_option = null;

						foreach ($listaction[$e] as $key => $value) {
							${$key} = $value;
						}
						// if($rc){
							$style .= ";margin-right:2px";
						// }
						if($dropdown){
							// $txtAction .="<div class='dropdown'>";
							$txtAction .= "<div class='btn-group'>";
							$dropdown_toggle = " dropdown-toggle";
							$dropdown_option = "data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'";
							$href = "#";
						}
						// <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

						$txtAction .="<a href='".$href."' class='btn btn-circle btn-".$theme." btn-sm" .$dropdown_toggle."' ".$dropdown_option." style='".$style."' tabindex=".$indexbutton."><i class='".$iconact."'></i>".$textact."<span class='sr-only'>Toggle Dropdown</span></a>";
						if($dropdown){
							if(is_array($dropdown_menu)){
								$txtAction .='<div class="dropdown-menu">';
								foreach($dropdown_menu as $keyddmenu=>$valueddmenu){
									$href_dropdown = $valueddmenu["href"];
									$text_dropdown = $valueddmenu["text"];
									$txtAction .='<a class="dropdown-item" href="'.$href_dropdown.'">'.$text_dropdown.'</a>';
								}
								$txtAction .='</div>';
							}
							$txtAction .="</div>";
							$txtAction .="</div>";
						}
						$rc = true;
					}
				}
				$txtAction .= "</div>";
			}else{
				for($e=0;$e<count($listaction);$e++){
					$href = "javascript:;";
					$iconact = "fas fa-pencil";
					$textact = "detanto";
					$theme = "default";
					$style = "";
					foreach ($listaction[$e] as $key => $value) {
						${$key} = $value;
					}
					$arrButton[]=array("text"=>$textact, "events"=>$href, "theme"=>$theme, "image"=>$iconact);
					$rc = true;
				}
				$txtAction = createButton($arrButton);
			}
			if($count_button>1){
				// $txtAction .="</div>";
			}			
		}
		
		if($border){
			$bordered = "card-border";
		}
		if($bottom){
			$actionatas = null;	
			$actionbawah = "<div class='card-toolbar' style='border-bottom:0px'>" . $txtAction . "</div>";
		}else{
			// $actionatas = $txtAction;
			$actionatas = "<div class='card-toolbar' style='border-bottom:0px'>" . $txtAction . "</div>";
			$actionbawah = null;
		}
		$header = false;
		if(isset($title)){
			$header = true;
		}
		if(isset($icon)){
			$header = true;
		}
		if($header){
			$card_header = "<div class='card-header'>";
			if(isset($title)){
				$card_header .= "<div class='card-title'>";
			}
			if(isset($icon)){				
				$card_header .= "<span class='card-icon'>
					<i class='".$icon." text-" . $iconcolor . "'></i>
				</span>";
			}
			if(isset($title)){
				$card_header .= "<h3 class='card-label'>" . $title . "";
				if($caption_helper!=""){
					$caption_helper = "<small class='caption-helper'>".$caption_helper."</small>";
				}		
				$card_header .= "</h3></div>";
			}
			$card_header .= $actionatas;
			$card_header .= "</div>";
		}
		$html = "<div class='card card-custom ".$bordered."'>";
		$html .= $card_header;
		$html .= "<div class='card-body'>";
		$html .= $content;
		$html .= "</div>";
		$html .= "</div>";
		return $html;
/*
		<div class="card card-custom card-border">
		<div class="card-header">
			<div class="card-title">
				<span class="card-icon">
					<i class="flaticon2-mail text-primary"></i>
				</span>
				<h3 class="card-label">Bordered Style 
				<small>sub title</small></h3>
			</div>
			<div class="card-toolbar">
				<a href="#" class="btn btn-xs btn-icon btn-danger mr-2">
					<i class="flaticon2-drop"></i>
				</a>
				<a href="#" class="btn btn-xs btn-icon btn-success mr-2">
					<i class="flaticon2-gear"></i>
				</a>
				<a href="#" class="btn btn-xs btn-icon btn-primary">
					<i class="flaticon2-bell-2"></i>
				</a>
			</div>
		</div>
		<div class="card-body">
		'.$kanan.'
		</div>
		</div>	
		*/	
	}
	function createTree($parameter){
		$id = "jqtree";
		$style = "padding-bottom:20px";

		foreach ($parametere as $keyn=>$valuey){
			${$keyn} = $valuey;
		}

		$tree = "<div id='".$id."'>";
		$tree .= "<ul>";

		$tree .= "</ul>";
	
		$tree .= "</div>";
	}
	function nodata($JUDULS=null){
		$html = getCss();
		$html .=createportlet(array("content"=>"<center>Data Tidak Ditemukan!</center>","title"=>"Laporan","caption_helper"=>$JUDULS, "icon"=>"fas fa-exclamation-circle"));
		return $html;
	}
	function cmbChecked($element="cmbJNSBYR"){
		$jqChecked = "
			var checkedItems = '';
			var item = $('#".$element."').jqxComboBox('getItems'); 
			var itemCount = item.length;
			var itemChecked = $('#".$element."').jqxComboBox('getCheckedItems');
			var rc = false;
			var loop=0;
			$.each(itemChecked, function (index) {
				if(rc){
					checkedItems += ','	
				}
				checkedItems += this.value;
				loop++;
				rc=true;
			});
			if(loop==itemCount){
				checkedItems='';
			}
			if(loop==0){
				checkedItems = '999';
			}
		";
		return $jqChecked;
	}
	function displayGrid($arrParameter){
		// debug_array($arrParameter);
		$class = "";
		$style = "";
		$wsstyle = "";
		$divstyle = "";
		foreach ($arrParameter as $detail=>$valueAwal){
			${$detail} = $valueAwal;
		}
		if(isset($column)){
			$class .= " col".$column;
		}
		if(isset($row)){
			$class .= " row".$row;
		}
		if(isset($dstyle)){
			$divstyle = "style='" . $dstyle . "'";
		}else{
			$divstyle = "style='width:99%'";
		}
		$content = "<div class='wrapperGrid $class' style='height:90%'>";
		foreach($grid as $key){
			$content .= "<div $divstyle>" . $key . "</div>";
		}
		$content .= "</div>";
		return $content;
	}
	function generatejqTree(){
		$script = '	
		<script type="text/javascript" src="' . base_url(PLUGINS."jstree/dist/jstree.js") .'"></script>
		<link rel="stylesheet" href="' . base_url(PLUGINS."jstree/dist/themes/default/style.min.css") . '" />
		';

		$script .= "<script>";
		$scriptx = "

		jQuery(function($) {
			$('#jstree_demo_div').jstree();
	   	});
		";
		$script .= "
		jQuery(function($) {
			$('#kt_tree_6').jstree({
				'core' : {
					'data' : {
					  'url' : function (node) {
						return '/proses/beranda/json';
					  },
					  'data' : function (node) {
						return { 'id' : node.id };
					  }
					}
				}
			});
		});
		";
		$script .= "</script>";
		$content = $script; 
		$content .= '  <div id="kt_tree_6" class="tree-demo"></div>';
		return $content;
	}
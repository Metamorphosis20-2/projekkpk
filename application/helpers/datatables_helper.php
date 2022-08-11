<?php
function gDatatable($pass){
	$CI = get_instance();
	$post = true;
	$inline = true;
	$closeform = true;
	$searching = 'true';
	$formname = "frmGrid";
	$footer = false;
	$table = null;
	$tfoot = null;
	$scrTable = null;
	$scrCol = null;
	$sumber = "server";
	$loadjs = true;
	$script = null;
	$additional_script = null;
	$class = "display cell-border responsive nowrap";
	$class = "cell-border table table-separate table-head-custom table-checkable";
	$pengurang = 150;
	$tdfontsize = null;
	$tdheaderfontsize = null;
	$caption = null;
	$group_script = null;
	$inline_button_pos = "left";
	$script_subtotal = null;
	$autoWidth = 'false';	
	//Button Related
	$inline_button_other = true;
	$scrbuttonrow = null;
	$check = true;
	$buttontop = true;
	$buttonToolbar = true;
	$DESCRE_COLUMN = "DESCRE";
	$IDENTS_COLUMN = "IDENTS";
	$grouping = null;
	$jvAdd = "";
	$jvAdd_text = $CI->lang->line("btnTambah");
	$add_icon = "fas fa-plus-circle";
	$add_event = "jvAdd()";
	$add_theme = "primary";

	$jvUnggah = "";
	$jvUnggah_text = $CI->lang->line("btnUnggah");

	$jvEdit = "";
	$jvEdit_text = $CI->lang->line("btnUbah");
	$edit_icon = "edit";
	$edit_event = "jvEdit(data_row)";
	$edit_theme = "success";

	$jvApprove = "";
	$jvApprove_text = $CI->lang->line("btnApprove");
	$approve_icon = "thumbs-up";
	$approve_event = "jvApprove(data_row)";
	$approve_theme = "warning";
	
	$jvView = "";
	$jvView_text = $CI->lang->line("btnLihat");
	$jvDelete = "";
	$jvDelete_text = $CI->lang->line("btnHapus");
	$jvDelete_confirm = $CI->lang->line("btnHapus");
	$jvApprove = "";
	$jvApprove_text = $CI->lang->line("btnPersetujuan");
	$searchable = true;
	$callback = null;
	$font_body = "10px";
	$font_head = "11px";

	foreach ($pass as $param=>$value){
		${$param}=$value;
	}
	if(isset($group)){
		if($group!=""){
			foreach($col as $keycol=>$valuecol){
				if($valuecol["namanya"]==$group){
					$group_index = $keycol;
					break;
				}
			}
		}
	}

	if($caption!=""){
		$caption ="<center><h3>".$caption."</h3></center>";
	}
	// debug_array($pass);
	if(isset($fontsize)){
		$tdfontsize = "td { font-size: ".$fontsize."px; }";
	}
	if(isset($headerfontsize)){
		$tdheaderfontsize = "th { font-size: ".$headerfontsize."px; }";
	}
	if($sumber=="server"){
		$server = 'true';
	}else{
		$server = 'false';
	}
	$serverSide = '"serverSide": '.$server.',';
	
	// "scrollY": height,
	if(isset($button)){
		if(!isset($pengurang)){
			$pengurang = 200;
		}else{
			$pengurang = 140;
		}
	}
	// debug_array($pass);
	if($caption!=""){
		$pengurang = $pengurang;
	}
	$headersearch = null;
	if($searchable){
		$headersearch = '
		$("#'.$gridname.' thead tr:eq(1) th").each( function (i) {
			var title = $(this).text();
			if(title!=""){
				$(this).html( \'<input type="text" style="width:100%;height:32px" class="form-control"/>\' );
			}
			$("input", this ).on("keyup change", function (e) {
				if (e.keyCode == 13) {
					if ( oDT_'.$gridname.'.column(i).search() !== this.value ) {
						oDT_'.$gridname.'
							.column(i)
							.search( this.value )
							.draw();
					}
				}
			});
		} );		
		';
	}
	$scrTable = null;
	if($loadjs){
		$scrTable .= '	
		<script type="text/javascript" src=' . base_url(PLUGINS."DataTables/datatables.min.js") .'></script>
		<script type="text/javascript" src=' . base_url(PLUGINS."DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js") .'></script>
		<script type="text/javascript" src=' . base_url(PLUGINS."DataTables/Select-1.3.3/js/dataTables.select.min.js") .'></script>
		<link rel="stylesheet" href=' . base_url(PLUGINS."DataTables/datatables.min.css"). ' type="text/css">';
	
	}
	$scrTable .= '
	<style>
	.dataTables_wrapper .dt-buttons{
		width:20px !important; 
		// border-left: none !important;
		// border-right: none !important;
	}
	.dataTables_wrapper .dataTables_info {
		padding:5px;
	}
	.dataTables_length{
		padding:5px;
	}
	table {
	}
	table td, table th {
		clear:both
	}
	table th.head {
		clear:both
	}
	#search_tr th{
		color:#fff !important
	}
	table tr.alt td {
		clear:both
	}
	#cardbody{
		padding-top:0px !important;
		padding-bottom:5px !important;
	}
	tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
	.dataTables_wrapper .dataTables_filter{
		padding-top:5px;
	}

    .dataTables_processing {
        top: 50% !important;
        z-index: 11000 !important;
    }	
	table td.button-dt {
		margin-left:0px !important;
		margin-right:0px !important;
	}
	</style>

    <script>
	jQuery(document).ready( function ($) {
		' . $headersearch . '
		';
	if(isset($dataset)){
		$scrTable .= $dataset . ";
";
		$serverSide = '';
	}
	if(isset($pagesize)){
		// $pageLength = '"pageLength":'.$pagesize.',';
		$pageLength = 'pageLength = '.$pagesize.';';
	}else{
		// $pageLength = '"pageLength":pageLength,';
		$pageLength = 'pageLength = pageLength;';
	}
	if(isset($lengthMenu)){
		$lengthMenu = '"lengthMenu": [[10, 20, 30, 40, 60, -1], [10, 20, 30, 40, 60, "All"]],';
	}else{
		$lengthMenu = '"lengthMenu": [[10, 20, 30, 40, 60, -1], [10, 20, 30, 40, 60, "All"]],';
	}
	if(isset($group_row)){
		$grouping = "
			rowGroup: {
				dataSrc: '".$group_row."'
			},
		";
	}
	// $group_script = null;
	// alert(height);
	// if(height>400){
	// 	height = height-'.$pengurang.';
	// }else{
	// 	height = height-100;
	// }
	// height = $("#div'.$gridname.'").parent().outerHeight();
	$countheight = true;
	if(isset($height)){
		if($height!="100%"){
			$countheight = false;
			$scrTable .= 'height = "' . $height . '";
			pageLength = 10;
			' . $pageLength;
		}
	}
	if($countheight){
		$scrTable .= '
			height = $(document).height();
			width = $(document).width();
			if(width>1024){
				height = height - 120 - 120 - 120;
			}else{
				height = height - 120 - 100 - 110;
			}
			
			pageLength = 10;
			' . $pageLength . '
			if(height>600){
				pageLength = 20;
			}

			if(height>1000){
				pageLength = 40;
			}		
			if(height<=0){
				height = "65%";
			}		
		';
	}
	$scrTable .= '
		var oDT_'.$gridname.' = $("#'.$gridname.'").DataTable({
			'.$grouping.'
			orderCellsTop: true,
			// fixedHeader: true,
			"dom": \'rt<"bottom"ilp><"clear">\',
			"autoHeigth":true,
			"rowHeight": "auto",
			"scrollY" : height,
			"scrollX": "100%",
			"width":"100%",
			"lengthChange": false,
			"processing": true,
			"pageLength": pageLength,
			'.$serverSide.'
			'.$lengthMenu.'
			"language": {
				"decimal": ".",
				"thousands": "",
				processing: "<i class=\"fa fa-cog fa-spin fa-6x fa-fw\" style=\"color:#34cfeb\"></i><span class=sr-only>Loading..n.</span> "
			}
	'; 
	// "autoWidth":'.$autoWidth.',
	// "searching":'.$searching.',
	// '.$pageLength.'
	if(!isset($dataset)){
		$scrTable .= '
		, "ajax": {
			"url": "'.$url.'",
			"type": "POST"
		},		
		';
	}else{
		$scrTable .= ', data : dataSet,';
	}
	// render: $.fn.dataTable.render.number(',', '.', 2, '')
	// columnDefs: [
	// 	{
	// 		data: "JML_NETTO",
	// 		render: function ( data, type, row ) {
	// 			return $.fn.dataTable.render.number( ",", ".", 2, "$" )
	// 		}
	// 	}],	
		
	// className: 'btn btn-primary
	$table ='
	<style>
		#'.$gridname.'{
			table-layout: fixed !important;
			word-wrap:break-word;
		}	
	</style>
	' . $caption. '
	<table id="'.$gridname.'" class="'.$class.'" style="width:100%">';
	$thead = null;
	if($footer){
		$tfoot .= "<tr>";
	}
	$urutan = array();		 
	foreach ($col as $arrUrut) {
		$urutan[] = $arrUrut['lsturut'];
	}
	$rcCol = false;
	if(isset($dataset)){
		$txtCol = "title";
	}else{
		$txtCol = "data";
	}

	$loopcols = 1;
	foreach($col as $key=>$val){
		if($rcCol) $scrCol .=",";
		foreach($val as $keyDT=>$valDT){
			${$keyDT}=$valDT;
		}
		if(strtoupper($namanya)!="CI_ROWNUM"){
			if(isset($dataset)){
				$txtCol = "title";
				$fieldnya = $label;
			}else{
				$txtCol = "data";
				$fieldnya = $namanya;
			}
			if($loopcols==1){
				if($IDENTS_COLUMN=="IDENTS"){
					$IDENTS_COLUMN = $fieldnya;	
				}
			}
			if(!isset($label)){
				$label = null;
			}
			$thead .= "	<th>" . $label . "</th>";
			$scrCol .= '{ "'.$txtCol.'": "'. $fieldnya . '"';
			if(isset($aw)){
				if(strpos($aw, "%")>0){
					$widthnya = $aw;
				}else{
					$widthnya = $aw. "px";
				}
				$scrCol .= ",width: '" . $widthnya . "'";
				unset($aw);		
			}
			if(isset($ds)){
				$sort = 'false';
				if($ds){
					$orderable_index = $key;
				}
				unset($ds);
			}
			if(isset($adtype)){
				if($adtype=="number"){
					$scrCol .= ",render: $.fn.dataTable.render.number(',', '.', 2, ''), className: 'dt-body-right'";
				}
				if($adtype=="date"){
					$scrCol .= ",width: '80px'";
				}
				unset($adtype);
			}
			if(isset($ah)){
				if($ah){
					$scrCol .= ",visible: false";
				}
				unset($ah);
			}
			if(isset($ga)){
				$scrCol .= ",className: 'dt-body-" . $ga. "'";
				unset($ga);
			}
			$scrCol .= '}';
			if($footer){
				$foot .= "	<th>" . $label . "</th>";
			}
			$rcCol = true;
		}
		$loopcols++;
	}
	//button related
	$lebartoolbar = 0;//$lebar;
	$tinggitoolbar = "height: 40,";
	if(isset($idCol)){
		$IDENTS_COLUMN = $idCol;
	}	
	$selectrow = 'id = data_row[\''.$IDENTS_COLUMN.'\'];';
	$dscrow = null;
	if(isset($DESCRE_COLUMN)){
		$dscrow = 'descre = data_row[\''.$DESCRE_COLUMN.'\'];';
	}

	$colbutton = null;
	$thbutton = null;
	$columndefs = null;
	if(isset($button)){
		$standar = false;
		$arrButton = array();
		$indexToolbar = 0;
		if(!is_array($button)){
			if(substr($button, 0, 7) == 'standar') {
				$standar = true;
			}
		}
		if($standar){
			$url = uri_string();
			$auth = explode("-", $button);
			$otorisasi = $CI->common->otorisasi($url, $check);
			$oADD = strpos("N".$otorisasi,"A"); // Add
			$oEDT = strpos("N".$otorisasi,"E"); // Edit
			$oDEL = strpos("N".$otorisasi,"D"); // Delete
			$oVIW = strpos("N".$otorisasi,"V"); // View
			$oAPP = strpos("N".$otorisasi,"P"); // Approval
			$oUPL = strpos("N".$otorisasi,"U"); // Upload

			if(isset($auth[1])){
				$auth = "X" . strtoupper($auth[1]);
				if(strpos($auth, 'A')>0) {
					$oADD = 0;
				}else{
					
				}
				if(strpos($auth, 'E')>0) {
					$oEDT = 0;
				}
				if(strpos($auth, 'D')>0) {
					$oDEL = 0;
				}
				if(strpos($auth, 'V')>0) {
					$oVIW = 0;
				}
				if(strpos($auth, 'P')>0) {
					$oAPP = 0;
				}
				if(strpos($auth, 'U')>0) {
					$oUPL = 0;
				}
			}
			// https://www.asabri.co.id/asset/images/logo/logo_header.png
			if($oADD>0){
				$arrButton = array_merge($arrButton, 
					array(
						$indexToolbar=>array(
							'text'=>$jvAdd_text, 
							'image'=>$add_icon,
							'events'=>$add_event,
							'theme'=>$add_theme,
							'width'=>75
							)
						)
				);
				if($jvAdd==""){
					if(isset($bisaedit)){
						if($bisaedit){
							$jvAdd = "
							function jvAdd(){
								var commit = $(\"#" . $gridname . "\").jqxGrid('addrow', null, {})	;
							}
							";
						}
					}
					if($jvAdd==""){
					  $jvAdd = "
								function jvAdd(){
									self.location.replace('/add/" . $modul . "');
								}
						";
					}
				}				
				$indexToolbar++;
				
				if(isset($arrButton) || isset($toolbarCombo)){
					$CI->load->helper('ginput');
					$arrToolbar = array('toolbarname'=>"toolbar" . $gridname, 'lebartoolbar'=>$lebartoolbar, 'tinggitoolbar'=>$tinggitoolbar);
					if(isset($arrButton)){
						if(is_array($arrButton)){
							$arrToolbar = array_merge($arrToolbar, array('arrButton'=>$arrButton));
						}				
					}
					if(isset($toolbarCombo)){
						if(is_array($toolbarCombo)){
							$arrToolbar = array_merge($arrToolbar, array('toolbarCombo'=>$toolbarCombo));
						}
					}
					if(count($arrButton)>0){
						if($buttontop){
							$buttonToolbar = createToolbar($arrToolbar, false);
						}else{
							$buttonToolbar = generateToolbar($arrToolbar);
						}
					}
				}else{
					$showToolbar = false;	
				}
			}
			if($oEDT>0){
				$inline_buttonrow_standar["edit"] = array("icon"=>$edit_icon, "function"=>$edit_event, 'buttonclass'=>$edit_theme,"alt"=>$jvEdit_text);
				if($jvEdit==""){
					if($post){
						$fncEdit = "
							$('#".$formname."').attr('action', '/edit/".$modul."');
							$('#grdIDENTS').val(id);
							document.".$formname.".submit();
						";
					}else{
						$fncEdit = "self.location.replace('/edit/".$modul."/'+id);";
					}
					
					$jvEdit = "
						function jvEdit(data_row){
							" . $selectrow . "
							if(id=='' || id==null){
								swal.fire({ title:'".$CI->lang->line("confirm_pilih_data")."!', text: null, icon: 'warning', timer: 4000});
							}else{
								" . $fncEdit . "
							}
						}";
				}				
			}
			if($oAPP>0){
				$inline_buttonrow_standar["approve"] = array("icon"=>$approve_icon, "function"=>$approve_event, 'buttonclass'=>$approve_theme,"alt"=>$jvApprove_text);
				if($jvApprove==""){
					if($post){
						$fncApprove = "
							$('#".$formname."').attr('action', '/approve/".$modul."');
							$('#grdIDENTS').val(id);
							document.".$formname.".submit();
						";
					}else{
						$fncApprove = "self.location.replace('/approve/".$modul."/'+id);";
					}
					
					$jvApprove = "
						function jvApprove(data_row){
							" . $selectrow . "
							if(id=='' || id==null){
								swal.fire({ title:'".$CI->lang->line("confirm_pilih_data")."!', text: null, icon: 'warning', timer: 4000});
							}else{
								" . $fncApprove . "
							}
						}";
				}				
			}
			if($oDEL>0){
				$inline_buttonrow_standar["delete"] = array("icon"=>"times-circle", "function"=>"jvDelete(data_row)", 'buttonclass'=>'danger',"alt"=>$jvDelete_text);

				if($jvDelete==""){
					$jvDelete = "
					function jvDelete(data_row){
						descre = '';
						" . $selectrow . "
						if(id==null){
							swal.fire({ title:'".$CI->lang->line("confirm_not_selected")."', text: null, type: 'warning', timer: 4000});
						}else{
							" . $dscrow . "
							swal.fire({ 
								title:'".$CI->lang->line("confirm_delete")."', 
								text:'".$jvDelete_confirm." ' + descre + '?', 
								icon: 'question', 
								showCancelButton: true, 
								confirmButtonText: '".$CI->lang->line("Ya")."', 
								cancelButtonText: '".$CI->lang->line("Tidak")."', 
								confirmButtonColor: '".$CI->config->item("confirmButtonColor")."', 
								cancelButtonColor: '".$CI->config->item("cancelButtonColor")."'
							}).then(
								result => { 
									if(result.value) {
										var prm = {};
										prm['idents'] = id;
										$.post('/delete/". $modul . "',prm,function(rebound){
											if(rebound){
												var clearFilters = false;
												swal.fire({title:descre + ' ' + rebound + ' dihapus!', icon:'success'});
												// jQuery.noConflict();
												$('#" . $gridname  . "').DataTable().ajax.reload();
											}
										});
									} 
								}
							);
						}
					}";
				}				
			}
			if($oVIW>0){
				$inline_buttonrow_standar["view"] = array("icon"=>"eye", "function"=>"jvView(data_row)", 'buttonclass'=>'dark',"alt"=>$jvView_text);

				if($jvView==""){
					if($post){
						$fncView = "
							$('#".$formname."').attr('action', '/view/".$modul."');
							$('#grdIDENTS').val(id);
							document.".$formname.".submit();
						";
					}else{
						$fncView = "self.location.replace('/view/".$modul."/'+id);";
					}
					
					$jvView = "
						function jvView(data_row){
							" . $selectrow . "
							if(id=='' || id==null){
								swal.fire({ title:'".$CI->lang->line("confirm_not_selected")."', text: null, icon: 'warning', timer: 4000});	
							}else{
								" . $fncView . "
							}
						}";
				}				
			}
			if($inline){
				if(isset($inline_buttonrow_standar)){
					$columndefs = ",";
					if($inline_button_pos=="right"){	
						$colbutton .= ',';
					}
					$rcrow = false;
					$surrounded = true;
					$buttonclass = "btn-primary";
					$prev = null;
					$end = null;
					$iconColor = null;
					$loopbutton =0;
					foreach($inline_buttonrow_standar as $keyrow_standar=>$valuerow_standar){
						// debug_array($valuerow_standar);
						foreach($valuerow_standar as $keydetail=>$valuedetail){
							${$keydetail} = $valuedetail;
						}
						if($rcrow) $colbutton .= ', ';
						if($rcrow) $columndefs .= ', ';
						$func_id = null;
						if(isset($IDENTS_COLUMN)){
							$keyid = $IDENTS_COLUMN;
							$func_id = 'id = data_row[\''.$IDENTS_COLUMN.'\'];';
						}
						
						if($surrounded){
							$tooltip = null;
							if(isset($alt)){
								$tooltip = 'aria-label="'.$alt.'" data-microtip-position="right" role="tooltip"';
							}
							$prev = '<button type="button" class="btn btn-'.$buttonclass.' btn-icon btn-xs" ' . $tooltip . '>';
							$end = '</button>';
						}
						if(isset($iconColor)){
							$iconColor = 'style="color:'.$iconColor.'"';
						}
						
						$icon = $prev . '<i class="fas fa-'.$icon.'" '.$iconColor.' style="text-align:center;cursor:pointer;" />' . $end;
						$colbutton .= "{data: null, className: 'dt-buttons dt-center editor-".$keyrow_standar."', defaultContent: '".$icon."', orderable: false}";
						$thbutton .= "<th style='width:40px!important'></th>";
						$scrbuttonrow .= '$("#'.$gridname.'").on("click", "td.editor-'.$keyrow_standar.'", function (e) {
							e.preventDefault();
							var data_row = oDT_'.$gridname.'.row( $(this).parents(\'tr\') ).data(); 
							' . $func_id . '
							' . $function. '
						} );
						';
						$columndefs .= "{targets : ".$loopbutton.", createdCell: function (td, cellData, rowData, row, col) {\$(td).css('padding', '0px')}}";
						$loopbutton++;
						$rcrow = true;
					}
					if($inline_button_pos=="left"){	
						$colbutton .= ',';
					}	
				}
			}
			if($inline_button_pos=="right"){
				$thead .= $thbutton;
				$scrCol .= $colbutton;
			}
			if($inline_button_pos=="left"){
				$thead = $thbutton . $thead;
				$scrCol = $colbutton . $scrCol;
			}			
		}
	}
	if($inline_button_other){
		if(isset($inline_buttonrow)){
			$buttonother = $inline_buttonrow;
		}
	}
	if(isset($buttonother)){
		if(count($buttonother)==0){
			unset($buttonother);
		}
	}
	if(isset($buttonother)){
		$icon_row = "save";
		$function = null;
		$columndefs = ",";
		if($inline_button_pos=="right"){	
			$colbutton .= ',';
		}
		$render = false;
		$rcrow = false;
		$surrounded = true;
		$arrButtonClass = array("primary", "dark", "info", "danger", "warning");
		$arrIcon = array("edit", "eye", "check", "thumbs-up", "save");
		// $buttonclass = "primary";
		$prev = null;
		$end = null;
		$iconColor = null;
		$loopbutton =0;
		foreach($buttonother as $keyrow_buttonrow=>$valuerow_buttonrow){
			foreach($valuerow_buttonrow as $key_buttonrow=>$value_buttonrow){
				if(is_numeric($key_buttonrow)){
					if(isset($valuerow_buttonrow[0])){
						$alt = $valuerow_buttonrow[0];
					}
					if(isset($valuerow_buttonrow[1])){
						$icon_row = str_replace("fa-", "", $valuerow_buttonrow[1]);
					}
					if(isset($valuerow_buttonrow[2])){
						// $function = $valuerow_buttonrow[2];
						$function = str_replace('\\', "", $valuerow_buttonrow[2]);
					}
					if(isset($valuerow_buttonrow[3])){
						$buttonclass = $valuerow_buttonrow[3];
					}
				}else{
					if($key_buttonrow=="icon"){
						${$key_buttonrow ."_row"} = $value_buttonrow;
					}else{
						${$key_buttonrow} = $value_buttonrow;
					}
					
				}
	
				
			}
			
			if($rcrow) $colbutton .= ', ';
			if($rcrow) $columndefs .= ', ';
			$func_id = null;
			
			if($surrounded){
				$tooltip = null;
				if(isset($alt)){
					$tooltip = 'aria-label="'.$alt.'" data-microtip-position="right" role="tooltip"';
				}
				if(isset($buttonclass)){
					$buttonclass = $buttonclass;
				}else{
					$buttonclass = $arrButtonClass[$loopbutton];
				}
				$prev = '<button type="button" class="btn btn-'.$buttonclass.' btn-icon btn-xs" '. $tooltip .'>';
				$end = '</button>';
			}
			if(isset($iconColor)){
				$iconColor = 'style="color:'.$iconColor.'"';
			}
			if(isset($icon_row)){
				$icon_row = $icon_row;
			}else{
				$icon_row = $arrIcon[$loopbutton];
			}
			$icon = $prev . '<i class="fas fa-'.$icon_row.'" '.$iconColor.' style="text-align:center;cursor:pointer;"/>' . $end;
			$condition_start = null;
			$condition_ended = null;
			$keyrow_buttonrow_validated = str_replace(" ","_", $keyrow_buttonrow);
			if(is_array($render)){
				foreach($render as $keyrender=>$valuerender){
					${$keyrender} = $valuerender;
				}
				$condition_start = "if(" . $validation . "){";
				$condition_ended = "}";

				// debug_array($icon);
				$colbutton .= '{   data: null, className: "dt-center editor-'.$keyrow_buttonrow_validated.'", 
					"render": function (data_row, type, row, meta){
						temp_data_row = data_row;
						if('.$validation.'){
							return \''.$icon.'\'
						}else{
							return \''.$return_false.'\'
						}
					},
					orderable: false, 
					width: "20px"
				}';
			}else{
				$colbutton .= '{data: null, className: "dt-center editor-'.$keyrow_buttonrow_validated.'", defaultContent: \''.$icon.'\', orderable: false, width: "10px"}';
			}

			$thbutton .= "<th style='width:10px'></th>";
			$scrbuttonrow .= '$("#'.$gridname.'").on("click", "td.editor-'.$keyrow_buttonrow_validated.'", function (e) {
				e.preventDefault();
				var data_row = oDT_'.$gridname.'.row( $(this).parents(\'tr\') ).data(); 
				' . $func_id . '
				' . $condition_start . '
				' . $function. '
				' . $condition_ended . '
			} );
			';
			$columndefs .= "{targets : ".$loopbutton.", createdCell: function (td, cellData, rowData, row, col) {\$(td).css('padding', '0px')}}";
			unset($buttonclass);
			unset($icon_row);
			$loopbutton++;
			$rcrow = true;
		}
		if($inline_button_pos=="right"){
			$thead .= $thbutton;
			$scrCol .= $colbutton;
		}
		if($inline_button_pos=="left"){
			$thead = $thbutton . $thead;
			$scrCol = $colbutton . "," . $scrCol;
		}
	}
	if($footer){
		$tfoot .= "</tr>";
		$tfoot .= '</thead>';
	}
	$scrCol = '"columns": [' . $scrCol . ']';

	if(isset($arrButton) || isset($toolbarCombo)){
		$CI->load->helper('ginput');
		$arrToolbar = array('toolbarname'=>"toolbar" . $gridname, 'lebartoolbar'=>$lebartoolbar, 'tinggitoolbar'=>$tinggitoolbar);
		$count = 0;
		if(isset($arrButton)){
			$count = $count + count($arrButton);
			if(is_array($arrButton)){
				$arrToolbar = array_merge($arrToolbar, array('arrButton'=>$arrButton));
			}				
		}
		if(isset($toolbarCombo)){
			$count = $count + count($toolbarCombo);
			if(is_array($toolbarCombo)){
				$arrToolbar = array_merge($arrToolbar, array('toolbarCombo'=>$toolbarCombo));
			}
		}

		if($count>0){
			if($buttontop){
				// $buttonToolbar = createButton($arrButton, false);
				$buttonToolbar = createToolbar($arrToolbar, false);
			}else{
				$buttonToolbar = generateToolbar($arrToolbar);
			}
		}
	}else{
		$showToolbar = false;	
	}

	if(isset($button_top)){
		$scrButton = "dom: 'Birft<\"bottom\"l>p',buttons: [";
		$rcButton = false;
		foreach($button_top as $keyButton=>$valueButton){
			if($rcButton) $scrButton .= ",";
			if(isset($valueButton[1])){
				$icon = "<i class=\"fa " . $valueButton[1] . "\"></i>&nbsp;&nbsp;";
			}
			if(isset($valueButton[2])){
				$arrFunc = explode("@", $valueButton[2]);
				if($arrFunc[0]=="extend"){
					if(count($arrFunc)==2){
						$jFunc = json_decode($arrFunc[1]);
						foreach($jFunc as $keyFunc=>$valueEnc){
							${$keyFunc} = $valueEnc;
							$extend[$keyFunc] = $valueEnc;
						}
						$funct = extendButton($extend);
						// debug_array($jFunc->type, false);
						// debug_array($eType);
					}
				}else{
					$funct = "action: function ( e, dt, node, config ) {
							" . $valueButton[2] . "
						}
					}";
				}
			}
			if(isset($valueButton[3])){
				$theme = $valueButton[3];
			}
			$scrButton .= "{ text: '". $icon . $keyButton ."',";
			$scrButton .= $funct;
			$rcButton = true;
		}
		$scrButton .= "], ";
	}
	if(isset($scrButton)){
		$scrTable .= $scrButton;
	}
	// if(isset($orderable_index)){
	// 	$group_script .= ", order: [[".$orderable_index.", 'asc']]";
	// }
	$scrTable .= $scrCol . "
		".$group_script.",
		columnDefs: [
			{ orderable: false, targets: 0 }" . $columndefs . "
		], " . $callback . "
	})";

	$arrStandar = array("Add", "Edit", "Delete", "View", "Approve", "Unggah");
	foreach($arrStandar as $keyStandar){
		if(isset(${"jv".$keyStandar})){
			$script .= ${"jv" . $keyStandar};
		}
	}
	$scrTable .= '	
		$.fn.dataTable.ext.errMode = "none";
		// $("#'.$gridname . '").on("processing.dt", function (e, settings, processing) {
		// 	$("#imgPROSES").css("display", "none");
		// 	if (processing) {
		// 		$("#imgPROSES").show();
		// 		$("#windowProses").jqxWindow("open");
		// 		$("#windowProses").jqxWindow("bringToFront");
		// 	} else {
		// 		$("#windowProses").jqxWindow("close");
		// 	}
		// })
		// $("#'.$gridname.'_filter input").unbind();
		// $("#'.$gridname.'_filter input").bind("keyup", function(e) {
		// 	if(e.keyCode == 13) {
		// 		oDT_'.$gridname.'.search(this.value).draw();
		// 	}
		// });
		// oDT_'.$gridname.'.columns().every( function () {
		// 	var that = this;
	 
		// 	$("input", this.header() ).on("keyup change", function () {
		// 		if ( that.search() !== this.value ) {
		// 			that
		// 				.search( this.value )
		// 				.draw();
		// 		}
		// 	} );
		// } );		
		' . $additional_script . '
		' . $scrbuttonrow . '
		' . $buttonToolbar . '
	});
	' . $script . '

	</script>
	';
	// <style>
	// th { font-size: 11px !important; }
	// td { font-size: 10px !important; }	
	// </style>	
	if($searchable){
		$thead = '<thead><tr>' . $thead . '</tr><tr id="search_tr">' . $thead . '</tr></thead>';
	}else{
		$thead = '<thead><tr>' . $thead . '</tr></thead>';
	}
	
	
	
	$table .= $thead . $tfoot;
	// debug_array($table);
	$table .= '</table>';

	$html = "<div id='div".$gridname."'>";
	$html .= $table;
	$html .= "</div>";
	$html .= $scrTable;
	$form = null;
	if($post){
		$form = form_open_multipart($modul, array("name"=> $formname,"id"=> $formname));
		$form .= form_input(array('name' => "grdIDENTS",'id'=> "grdIDENTS", 'type'=>'hidden'));
		if($closeform){
			$form .= form_close();	
		}
	}
	$styleresp = "
	<style>
		.dataTables_wrapper .dataTable th, .dataTables_wrapper .dataTable td {
			font-weight: 400;
			font-size: 10px !important;
			padding: 1rem 1rem; 
		}
		.positive td {
			background-color:#ffba00;
		}
	</style>
	";
	return $html . $form;
}
function extendButton($parameter){
	// debug_array($parameter);
	$filename = "filecsv_";
	$eClass = 'compact';
	$eFontsize = "10";
	$prepend = "";
	$title = null;
	$scrOrientation = "";
	$eOrientation = "portrait";
	$prependcmd = '\'<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />\'';
	$prependcmd = "'detanto'";
	$messageTop = 'This print was produced using the Print button for DataTables';
	// messageTop: '<table><tr><td>This print was produced using the Print button for DataTables</td></tr><tr><td>aklsdflkajshdf</td></tr>',
	foreach($parameter as $key=>$value){
		${$key} = $value;
	}
	if(isset($prependcmd)){
		$prepend = "
		.prepend(
			" . $prependcmd . "
		);		
		";		
	}
	if(isset($eTitle)){
		$title = "title: '".$eTitle."',";
	}
	if($eOrientation=="landscape"){
		$scrOrientation = "
			var css = '@page { size: landscape; }',
				head = win.document.head || win.document.getElementsByTagName('head')[0],
				style = win.document.createElement('style');

			style.type = 'text/css';
			style.media = 'print';

			if (style.styleSheet){
				style.styleSheet.cssText = css;
			}else{
				style.appendChild(win.document.createTextNode(css));
			}

			head.appendChild(style);		
		";
	}
	switch($eType){
		case "print":
			$button = "
				extend: 'print',
				" . $title . "
				customize: function ( win ) {
					$(win.document.body)
						.css( 'font-size', '".$eFontsize."pt' )
					$(win.document.body).find( 'table' )
						.addClass( '".$eClass."' )
						.css( 'font-size', 'inherit' );
					var last = null;
					var current = null;
					var bod = [];
					".$scrOrientation."
				}
			}";
			unset($eTitle);
			break;
		case "csv":
			$text = "CSV";
			// if(isset($eTitle)){
			// 	$text = $eTitle;
			// }
			$button = "
			extend: 'csv',
			title: '".$text."',
			filename: function(){
                var d = new Date();
                var n = d.getTime();
                return '".$filename."' + n;
            },			
            exportOptions: {
                modifier: {
                    search: 'none'
                }
			}
		}";

			break;			
	}
	return $button;
}


/*
		.prepend(
			'<img src=\"http://datatables.net/media/images/logo-fade.png\" style=\"position:absolute; top:0; left:0;\" />'
		);

		new $.fn.dataTable.Buttons( oDT_'.$gridname.', {
			buttons: [
				"copy",
				"pdf"
			]
		} );
		oDT_'.$gridname.'.buttons().container().appendTo( $(".col-sm-6:eq(0)", oDT_'.$gridname.'.table().container() ) );
	<script>
	
    $(document).ready(function() {
      $("#example").DataTable( {
          "processing": true,
          "serverSide": true,
          "ajax": {
              "url": "nosj/datatables/",
              "type": "POST"
          },
          "columns": [
              { "data": "first_name" },
              { "data": "last_name" },
              { "data": "position" },
              { "data": "office" },
              { "data": "start_date" },
              { "data": "salary" }
          ]
      } );
  } );    
    </script>


    <table id="example" class="display" style="width:100%">
      <thead>
          <tr>
              <th>First name</th>
              <th>Last name</th>
              <th>Position</th>
              <th>Office</th>
              <th>Start date</th>
              <th>Salary</th>
          </tr>
      </thead>
      <tfoot>
          <tr>
              <th>First name</th>
              <th>Last name</th>
              <th>Position</th>
              <th>Office</th>
              <th>Start date</th>
              <th>Salary</th>
          </tr>
      </tfoot>
    </table>
*/
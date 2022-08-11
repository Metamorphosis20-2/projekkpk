<?php
function gGrid($pass){
	$CI = get_instance();
	$post = true;
	$gridcss = true;
	// $CI->common->debug_array($pass);
	$table = null;
	$shownumrow = false;
	$inlinebutton = false;
	$buttonotherposition = "end";
	// ======= Function for CRUD
	$jvAdd = "";
	$jvAdd_text = $CI->lang->line("btnTambah");
	$jvUnggah = "";
	$jvUnggah_text = $CI->lang->line("btnUnggah");
	$jvEdit = "";
	$jvEdit_text = $CI->lang->line("btnUbah");
	$jvView = "";
	$jvView_text = $CI->lang->line("btnLihat");
	$jvDelete = "";
	$jvDelete_text = $CI->lang->line("btnHapus");
	$jvDelete_confirm = $CI->lang->line("btnHapus");
	$jvApprove = "";
	$jvApprove_text = $CI->lang->line("btnPersetujuan");
	$scriptadd = "";
	$pinned = "";
	$width = "95%";
	$script = "";
	$formname = "frmGrid";
	$readyfunction = "";
	$gridscript = false;
	$showaggregates = "";
	$ketAgggregate = false;
	$closeform = true;
	$keyboardnavigation = false;
	$buttontop = true;
	// ===================================
	// =================================== for script

	$jvscript = "";
	$grid = "grid";

  	// =================================== grid display related
	$tooltip = true;
	$gridcenter = true;
	$bisaedit = false;
	$awalCenter = "";
	$akhrCenter = "";
	$buttonToolbar = "";
	$creategrid = true;
	$filterable = true;
	$groupable = "";
	$groupnya = "";
	$expandgroup = true;
	$expandgroupnya = "";
	$toolbarcontainer = "";
	$toolbarheight = "";
	$fontsize = "";
	$headerfontsize = "";
	$autorowheight=""; // if set then row height will depends on content
	$autoheight = false;
	$showToolbar = true; // if false toolbar will not show
	$showGridbar = false; // if false toolbar will not show
	$autoshowfiltericon = false; //if false then filter icon will not show, user have to do mouse over on grid header
	$filtermode = "default"; // you can set this to excel so filter dialog will be more like excel type
	$rowsheight = ""; // if set rowheight will follow this variable value
	$pageable = true; // will set grid's paging, if false then grid will use virtual scrolling
	$editable = ""; // whether grid editable or not
	$pagesize = ""; // pagesize
	$pagesizeoptions = ""; // page size option only work for grid, will not work with treegrid
	$sumber = 'server'; // source : data from server or from raw json 
	$scrollmode = "scrollmode : 'logical',";
	$scrollmode = "";
	// $theme = $CI->config->item('app_grids'); 

	$theme = ($CI->config->item('app_grids')=="" ? "arctic" : $CI->config->item('app_grids'));// grid theme, the value came from config.php
	$USR_THEMES = $CI->session->userdata("USR_THEMES");
	if($USR_THEMES!=""){
		$theme = $USR_THEMES;
	}
	// $theme = "bootrsa";
	$lebar = ""; // grid width
	$height = "full"; // gridheight
	$colgroup = array(); // for colspan in grid header
	$columnsheight = ""; // grid header height
	$pagermode = 'default';
	$virtualmode = false;
	$rowdetails = false;
	$initrowdetails = "";
	// =================================== treegrid
	$hirarki = ""; // hirarchy for treegrid
	$autoexpand = true;
	$expand = "";
	$modul = ""; // module name, function will need this variable
	$folder = $CI->router->fetch_directory();
	$divluar = "kt_content";
	$gridpadding = "padding:2px 2px 0px 0px";
	$startscript = true;
  	// =================================== database related, this is to get value
	$DESCRE_COLUMN = "DESCRE";
	$IDENTS_COLUMN = "IDENTS";

	// =================================== authorization check
	$check = true;
  	$expand = "";
  	$columngroupdesc = "";  
	$fromdb = false;

	$jstinggi = "";
	$buttong = "";
	$cellclassfunc = "";
	$cellclassfunc_content = "";
	$cellcss = "";
	$rc = false;
	
	
	$scripteditor = "";
	// ======================== ambil parameter
	foreach ($pass as $param=>$value){
		${$param}=$value;
	}
	// debug_array($pass);
	if(!isset($url)){
		$url = "/" . $folder . "nosj/get".ucfirst($modul)."_list";
	}else{
		if($url==""){
			$url = "/" . $folder . "nosj/get".ucfirst($modul)."_list";	
		}
	}
  // if($pagesize==""){
  // 	$pagesize = "pagesize : 10, ";	
  // }else{
  // 	$pagesize = "pagesize : " . $pagesize . ", ";
  // }
	if(isset($width)){
		$lebar = "width:'".$width . "',";
	}
	if(isset($height) && $height!=""){
		if($height!="full"){
			$height = "height:'".$height."',";	
		}else{
			$gridheight = "full";
			if(isset($treegrid)){
				if($filterable){
					$height = "height:gridheight,";	
				}else{
					$height = "height:treeheight,";
				}
			}else{
				$height = "height:'99%',";	
			}
		}
	}

	if($rowsheight!=""){
		$rowsheight = "rowsheight: '" . $rowsheight . "',";
	}
	if($readyfunction!=""){
		$create_ready = true;
	}else{
		$create_ready = false;
	}

	if($bisaedit){
		$script = "<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.edit.js") ."></script>";
	}	
	if($grid=="html"){
	    $CI->load->helper('table');
	    return generateTable($pass);
	}else{
		if($grid=="datatables"){
    		$CI->load->helper('datatables');
			return gDatatable($pass);
		}else{
			if($startscript){
				$script .="
						<script type=\"text/javascript\">
							var height = $('#".$divluar."').height()*0.93;
							var treeheight = height;
							var gridheight = height-35;
				      $(document).ready(function () {
				    ";		
			}
			if(isset($treegrid)){
				if($treegrid){
					$script .="
					$('#" . $gridname ."').on('bindingComplete', function() {
						$('#" . $gridname ."').jqxTreeGrid('expandAll');
					});					
					";
				}
			}			
			if($pagesize==""){
				// $pagesize = "pagesize : 10, ";
				if($startscript){
					$script .= "
						if(height>700){
							var pagesizenya = 20;
						}else{
							var pagesizenya = 10;
						}
					";
				}else{
					$script .= "var pagesizenya = 20;";
				}
			}else{
				$script .= "var pagesizenya = " . $pagesize;
			}
		  	$pagesize = "pagesize : pagesizenya, ";
			$script .="
			 		var url = \"" . $url . "\";
			      	var theme = '" . $theme . "';
			";
		
			$adapter = "
					var source =
						{
							type:\"POST\",
							datatype: \"json\",
							datafields: [
								";
			// ===================================================================================
			// == kalau tidak ada definisi kolom, maka col akan dibentuk dari json T_MAS_TABLES ==
			// ===================================================================================
			if(!isset($col)){
				// $fielddetail = $CI->crud->getTableInformation($table);
				$loop =1;
				$col="";
				
				foreach($fielddetail->result() as $key=>$value){
					$fieldname = $value->Field;
					$arrComment = $CI->common->extractjson($table, $fieldname);
					if($arrComment!=""){
						$gs="";
						$lao ="1";
						// echo $arrComment;
						foreach($arrComment as $key=>$value){
							if(!is_array($value)){
								${$key}=$value;
							}else{
								//print_r($value);
								if($key=="grid"){
									foreach($value as $keyd=>$valued){
										foreach($valued as $keyd=>$valuede){
											${$keyd} = $valuede;
										}
									}
								}
							}
						}
						/*
						gs : grid show
						gh : grid hidden
						gt : grid sort
						gc : grid search (cari)
						gw : grid width
						gu : grid urutan
						*/
						if($gs){
							$col = array('lsturut'=>$loop, 'namanya'=>$fieldname, 'label'=>$fd);
							
							if($gh==true){
								$col = array_merge($col, array('ah'=>$gh));
							}
							if($gt==false){
								$col = array_merge($col, array('at'=>$gt));
							}
							
							if($gc!=""){
								$col = array_merge($col, array('ac'=>$gc));
							}
							if($gw!=""){
								$col = array_merge($col, array('aw'=>$gw));
							}
							if($gu!=""){
								$col = array_merge($col, array('au'=>$gu));
							}
							$column[] = $col;
						}
					}
					$loop++;
				}
				// die();
				$fromdb = true;
				$col = $column;
			}
			// ======================================================================================
			// == looping kolom ($col) (didapat dari parameter atau database di proses sebelumnya) =
			// ======================================================================================
			$colDetail = "";
			$loop=1;
			$urutan = array();		 
			foreach ($col as $arrUrut) {
				$urutan[] = $arrUrut['lsturut'];
			}
			array_multisort($urutan, SORT_ASC, $col);

			if($cellclassfunc_content!=""){
				$cellclassfunc = "
					var cellclass = function (row, columnfield, value, rowData) {
						". $cellclassfunc_content . "
			    }
				";
				if($cellcss==""){
					$cellcss = "
					<style>
			        .red {
			            color: black\9;
			            background-color: #e83636\9;
			        }	
			        .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
			            color: black;
			            background-color: #e83636;
			        }        	
					</style>
					";		
				}
			}
			$genscriptcreate = true; 
			$genscriptedit = true;
			foreach($col as $key=>$val){
				//deklarasi awal
				$ah="";
				$aw="";
				$am="";
				$at="";
				$ac="";
				$aa="";
				$align = "";
				$hidden ="";
				$type="";
				
				if($rc) {
					$adapter .= ",";
					$colDetail .= ",";
				}
				foreach($val as $val1=>$val2){
					${$val1}=$val2;

					if($loop==1){
						if($val1=="namanya"){
							$IDENTS_COLUMN = $val2;	
						}
					}
					if($loop==2){
						if($val1=="namanya"){
							$DESCRE_COLUMN = $val2;
						}
					}		
				}	
				
				if($fromdb){
					$fielddetail = $CI->crud->getTableInformation($table,null,$namanya,null,2);
					if($fielddetail){
						$arrComment = $CI->common->extractjson($table, $fielddetail->Field);
						if($arrComment!="x"){
							foreach($arrComment as $key=>$value){
								if(!is_array($value)){
									${$key}=$value;
								}else{
									foreach($value as $valued){
										foreach($valued as $keyd=>$valuede){
											${$keyd} = $valuede;
										}
									}
								}
							}
						}
					}
				}
				$adaptertype="";
				$filtertype="";
				if(isset($fltype)){
					$filtertype = ", filtertype : '".$fltype."'";
					unset($fltype);
				}
				if(isset($adtype)){
					if($adtype != ""){
						$adaptertype = ", type : '".$adtype."'";
						if($adtype=="date"){
							$filtertype = ", filtertype : 'date'";	
						}
					}
					unset($adtype);
				}
				//script pembentuk jqx adapter (sumber data)
				$adapter .= "{ name: '" . $namanya . "',datafield:'" . $namanya . "'".$adaptertype."}";
				if(isset($label)){
					$fd = $label;
				}
				$prop = "";
				//hidden
				if(isset($gh) && $ah==""){
					if($gh){
						$prop .= ", hidden: true" ;	
					}
					unset($gh);
				}else{
					if($ah){
						$prop .= ", hidden: true";
					}
					unset($ah);
				}
				//lebar kolom
				if(isset($gw) && $aw==""){
					$prop .= ", width: '" . $gw . "'";
				}else{
					if($aw!=""){
						$useragent=$_SERVER['HTTP_USER_AGENT'];
						if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
						  $aw = str_replace("%", "0", $aw);
						}
						$prop .= ", width: '" . $aw . "'";	
					}
				}
				if($am!=""){
					$prop .= ", minwidth: '" . $am . "'";	
				}
				//lebar kolom
				if(isset($ga) && $aa==""){
					$prop .= ", cellsalign: '" . $ga . "'";
					unset($ga);
				}else{
					if($aa!=""){
						$prop .= ", cellsalign: '" . $aa . "'";	
					}
				}		
				//sortable
				if(isset($gt) && $at==""){
					if(!$gt){
						$prop .= ", sortable: false" ;
					}
					unset($gt);
				}else{
					if($at=="n"){
						$prop .= ", sortable: false";
					}else{
						$prop .= ", sortable: true";
					}
				}
				if(isset($aggregate)){
					if(is_array($aggregate)){
						foreach ($aggregate as $key => $value) {
							${$key}=$value;
						}
						$arrOperator = explode(",", $operator);

						if(is_array($arrOperator)){
							$rc = false;
							$operator = "";
							for($e=0;$e<count($arrOperator);$e++){
								if($rc) $operator .= ",";
								$operator .= "'" . $arrOperator[$e] . "'";
								$rc = true;
							}
						}
					}else{
						$operator = "'" . $aggregate . "'";
					}
					// debug_array($operator);
					// $jsaggregate = "<script src=" . base_url(JS."jqxwidgets/jqxgrid..js") . "></script>";
					$jsaggregate = "<script src=" . base_url(PLUGINS."jqwidgets/jqxgrid.aggregates.js") ."></script>";
					$showaggregates ="showstatusbar: true, showaggregates: true,";

					if($ketAgggregate){
						$ketAgggregate = "
								var name = '';
								switch (key) {
									case 'sum' :
										name = 'Total';
										break;
									default :
										name = key.charAt(0).toUpperCase() + key.slice(1);
										break;
								}
              renderstring += '<div id=agg".$namanya." style=\"position: relative;vertical-align:middle;margin: 6px; text-align: right; overflow: hidden;\">' + name + ' : <b>' + value + '</b>&nbsp;</div>';
						";
					}else{
						$ketAgggregate = "renderstring += '<div id=agg".$namanya." style=\"position: relative;vertical-align:middle;margin: 6px; text-align: right; overflow: hidden;font-weight:bold\">' + value + '</div>';";
					}
					$prop .= ", aggregates: [".$operator."], 
                  	aggregatesrenderer: function (aggregates, column, element, summaryData) {
                      var renderstring = \"<div style='float: left; width: 100%; height: 100%;'>\";
                      $.each(aggregates, function (key, value) {
			                  " . $ketAgggregate . "
                      });
                      renderstring += \"</div>\";
                      return renderstring;
                  }
					";
					$ketAgggregate = false;
					$aggregate="";
				}
				// $prop .= ", sortable: true";
				//filterable
				if($ac=="" || $ac==false){
					$ac=="s";
				}
				if(isset($gc) && $ac==""){
					if(!$gc){
						$prop .= ", filterable: false" ;
					}
					unset($gc);
				}else{
					// echo $ac;/
					if($ac=='false'){
						$prop .= ", filterable: false";
					}
				}
				if(isset($cf)){
					$prop .= ", cellsformat: '".$cf."'" ;
					unset($cf);
				}
				if($bisaedit){
					if(isset($ct)){///tipe cell edit
						unset($createeditor);
						if(isset($ce)){
							if($ce){
								$prop .= ", editable:true";	
							}else{
								$prop .= ", editable:false";
							}
							unset($ce);
						}
						if(!isset($createeditor)){
							$prop .= ", columntype: '".$ct."'" ;
							if(isset($initeditor)){
								$prop .= ", initEditor : " . $initeditor ;
							}
							if(isset($cellvalidation)){
								$prop .= ", validation : " . $cellvalidation;
								unset($cellvalidation);
							}
						}
						unset($ct);
					}elseif(isset($ce)){///bisa edit pa gak
						$prop .= ", editable: '" . $ce . "'";
						unset($ce);
					}else{
						$prop .= ", editable:false" ;
					}
				}

				if(isset($cv)){///tipe cell edit
					$prop .= ", validation: function(cell,value){
						".$cv."
					}
					" ;
					unset($cv);
				}		

				// kalau image tidak bisa sort/filter
				if($type=="image"){
					$prop .= ", sortable: false, filterable: false";
				}
				if(!isset($fd)){
					$fd = "Judul";
				}
				$columngroup = "";
				//script pembentuk jqx grid
				if(isset($group)){
					$columngroup = ",columnGroup:'" . $group . "'";
					$colgroup = array_merge($colgroup, array($group));
					unset($group);
				}
				$cellclass="";
				if(isset($cellclassname)){
					if($cellclassname==true){
						$cellclass = ", cellclassname:cellclass";	
					}
					$cellclassname = false;
				}
				// $colDetail .= "{ text: '". $fd . "' " . $columngroup . ", dataField: '". $namanya . "'" . $prop . ",cellsrenderer: cellsrenderer " . $cellclass ."}";
				$propcreate = "";
				if(isset($createeditor)){
					$propcreate = ", columnType: 'custom', createeditor:".$createeditor;
					if($genscriptcreate){
						$scripteditor .= "
							var " . $createeditor . " = function(row, cellValue, editor, cellText, width, height)
							{
						" . $scriptcreateeditor . "
							}
						";
						$genscriptcreate = false;
					}
					unset($createeditor);
				}
				if(isset($initeditor)){
					$propcreate .= ", initEditor:".$initeditor;
					unset($initeditor);
				}
				if(isset($cellbeginedit)){
					$propcreate .= ", cellbeginedit:".$cellbeginedit;
					unset($cellbeginedit);
				}

				if(isset($geteditorvalue)){
					if(isset($geteditorvalue)){
						$propcreate .= ", getEditorValue:".$geteditorvalue;
						if($genscriptedit){
							$scripteditor .= "
								var " . $geteditorvalue . " = function (rowID, cellValue, editor)
								{
							" . $scriptediteditor . "
								}
							";
							$genscriptcreate = false;
						}				
					}
					unset($geteditorvalue);
				}
				if(isset($cellcontent)){
					$cellcontent = ', cellsrenderer:' .$cellcontent;
				}else{
					$cellcontent ="";
				}

				if(isset($cellpinned)){
					if($cellpinned){
						$pinned = ', pinned:true';	
					}
					unset($cellpinned);
				}else{
					$pinned ="";
				}		
				$colDetail .= "{ text: '". $fd . "' " . $columngroup . ", renderer : columnsrenderer, dataField: '". $namanya . "'" . $prop . $cellclass ."" . $propcreate . "" . $cellcontent . "" . $pinned.$filtertype ."}";
				$pinned ="";
				unset($cellcontent);
				
				$rc=true;
				$loop++;	
			}
			// =======================================================
			// == untuk colspan atas menggunakan parameter colgroup ==
			// =======================================================
			if(count($colgroup)>0){
				$colgroup = array_unique($colgroup);
				$columngroupdesc = ",columnGroups: [";
				$rc = false;
				foreach ($colgroup as $key => $valuegroup) {
					if($rc) $columngroupdesc .=",";
					$columngroupdesc .= "{ text: '". $valuegroup . "', name: '". $valuegroup . "', 'align':'center'}";
					$rc=true;
				}
				$columngroupdesc .= "]";
			}
			// ==================================
			// == penentuan grid atau treegrid ==
			// ==================================
			if(isset($idCol)){
				$IDENTS_COLUMN = $idCol;
			}

			$tipegrid = "jqxGrid";
			$functionexpand = "";

			if($pageable == true){
				$pageable = "pageable: true,";
			}else{
				if($pagesize==''){
					$pagesize = "pagesize : 50, ";	
				}
			}
			if($toolbarheight!=""){
				$toolbarheight = "toolbarheight : '" . $toolbarheight . "px',";
			}
			if($filterable){
				$filterable = "filterable: ".$filterable.",";	
			}

			$selectrow = "
				var selectedrowindex = $(\"#" . $gridname ."\").".$tipegrid."('getselectedrowindex');
				var id = $(\"#" . $gridname . "\").".$tipegrid."('getrowid', selectedrowindex);
				// var id = $(\"#" . $gridname . "\").".$tipegrid."('getcellvalue', row, '".$IDENTS_COLUMN."');
			";
			$idrow = "";
			if(!isset($dscrow)){
				$dscrow = "var descre = $(\"#" . $gridname . "\").".$tipegrid."('getcellvalue', selectedrowindex,'".$DESCRE_COLUMN."');";	
			}

			if(isset($treegrid)){
				if($treegrid){
					$autoshowfiltericon = "";	
					$jvscript .= "<script src=" . base_url(PLUGINS."jqwidgets/jqxtreegrid.js") ."></script>";
					$scrollmode = "";
					$pagesize = "";
					$pageable = "";
					if($height==""){
						$height = "height:treeheight,";	
					}
					
					$tipegrid = "jqxTreeGrid";
					if($virtualmode){
						$virtualmode = "                
						virtualModeCreateRecords: function (expandedRecord, done) {
				      var dataAdapter = new $.jqx.dataAdapter(source,
			          {
		              loadComplete: function()
		              {
		              	done(dataAdapter.records);
		              },
		              loadError: function (xhr, status, error) {
			              done(false);
			              throw new Error(\"http://aplikasi.kemas.co.id : \" + error.toString());
		              }
			          }
				      );   
					    dataAdapter.dataBind();
					  },
					  virtualModeRecordCreating: function (record) {
		 					if (record.level == 2) {
		            // by setting the record's leaf member to true, you will define the record as a leaf node.
		            record.leaf = true;
		        	}
					  },
						";
					}
					$virtualmode = "";
					$hirarki = "
						hierarchy:{
							keyDataField: { name: '".$keyfield."' },
							parentDataField: { name:'".$keyparent."' }
						}	,
					";
					$functionexpand = "
						function traverseTreeGrid(action) {
		         var treeGrid = \"$('#" . $gridname ."')\";
					    function traverseRows(rows) {
					      var idValue;
					      for(var i = 0; i < rows.length; i++) {
					        if (rows[i].records) {
					          idValue = rows[i][idColumn];
					          $('#" . $gridname ."').jqxTreeGrid(action+'Row',idValue);
					          traverseRows(rows[i].records);
					        };
					      };
					    };

					    var idColumn = $('#" . $gridname ."').jqxTreeGrid('source')._source.id;
					    traverseRows($('#" . $gridname ."').jqxTreeGrid('getRows'));
						};
					";
					if($autoexpand){
						$expand = "
							ready: function()
							{
								var rows = $(\"#" . $gridname . "\").jqxTreeGrid('getRows');
								var traverseTree = function(rows)
								{
									for(var i = 0; i < rows.length; i++){
										if (rows[i].records){
											idValue = rows[i][idColumn];
											$(\"#" . $gridname . "\").jqxTreeGrid('expandRow',idValue);
											traverseTree(rows[i].records);
										}
									}
								};
								var idColumn = $(\"#" . $gridname . "\").jqxTreeGrid('source')._source.id;
								traverseTree(rows);
							},
						";

						$expand = "
							ready: function()
							{
								$(\"#" . $gridname . "\").jqxTreeGrid('expandAll');
							},
						";					
					}


					$selectrow = "
						var id = $(\"#" . $gridname ."\").".$tipegrid."('getSelection');
						";
					$idrow = "var id = id[0].uid;";
					$dscrow = "var descre = $(\"#" . $gridname . "\").".$tipegrid."('getCellValue', id,'".$DESCRE_COLUMN."');";
				}
			}else{
				$pagesizeoptions = "pagesizeoptions: ['10', '20', '30'],";
			}

			// pembuatan toolbar
			$indexToolbar = 0;
			$lebartoolbar = $lebar;
			$tinggitoolbar = "height: 40,";
			if($buttonotherposition=="first"){
				if(isset($buttonother)){
					$indexToolbar = count($buttonother);
				}
			}
			if(isset($button) || $showToolbar==true){
				// $showToolbar = false;
				$arrButton = array();
				$indexToolbar = $indexToolbar;
				if(isset($button)){
					$standar = false;
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
						//======================== button tambah ========================
						if($oUPL>0){
							if($oAPP==0){
								$urutanToolbar = $indexToolbar;
							}else{
								$urutanToolbar = $indexToolbar+1;
							}
							$arrButton = array_merge($arrButton, 
								array(
									$urutanToolbar=>array(
										'text'=>$jvUnggah_text, 
										'image'=>"fas fa-upload",
										'events'=>"jvUnggah()",
										'theme'=>'primary',
										'width'=>75
										)
									)
								);

							if($jvUnggah==""){
								$jvUnggah = "
										function jvUnggah(){
											self.location.replace('/add/" . $modul . "');
										}
								";
							}
							$indexToolbar++;
						}
						//======================== button tambah ========================
						if($oADD>0){
							$arrButton = array_merge($arrButton, 
								array(
									$indexToolbar=>array(
										'text'=>$jvAdd_text, 
										'image'=>"fas fa-plus-circle",
										'events'=>"jvAdd()",
										'theme'=>'primary',
										'width'=>75,
										'urutan'=>$indexToolbar
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
						}
						//======================== button edit ========================
						if($oEDT>0){
							if(!$inlinebutton){
								$arrButton = array_merge($arrButton, 
								array(
									$indexToolbar=>array(
										'text'=>$jvEdit_text, 
										'image'=>"fas fa-edit",
										'events'=>"jvEdit()",
										'theme'=>'success',
										'width'=>70,
										'urutan'=>$indexToolbar
										)
									)
								);
								$indexToolbar++;
							}else{
								$buttonRow[] = "
								{
									text: '".$jvEdit_text."', datafield: 'buttonedit', width: 40, pinned:true,
									createwidget: function (row, column, value, htmlElement) {
										var datarecord = value;
										var img = '<i class=\"fas fa-edit\"></i>';
										var button = $(\"<div style='border:none;'>\" + img + \"</div>\");
										$(htmlElement).append(button);
										button.jqxButton({ template: 'success', height: '100%', width: '80%' });
										button.click(function (event) {
											rowindex = row.boundindex;
											$('#".$gridname."').jqxGrid('selectrow', rowindex);
											jvEdit();
										});
									},
									initwidget: function (row, column, value, htmlElement) {
										
									}				
								},					
								";
							}

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
									function jvEdit(){
										" . $selectrow . "
										if(id=='' || id==null){
											swal.fire({ title:'".$CI->lang->line("confirm_pilih_data")."!', text: null, icon: 'warning', timer: 4000});
										}else{
											" . $idrow . "
											" . $fncEdit . "
										}
									}";
							}
						}
						//======================== button hapus ========================
						if($oDEL>0){
							if(!$inlinebutton){
								$arrButton = array_merge($arrButton, 
								array(
									$indexToolbar=>array(
										'text'=>$jvDelete_text, 
										'image'=>"fas fa-times-circle",
										'events'=>"jvDelete()",
										'theme'=>'danger',
										'width'=>70,
										'urutan'=>$indexToolbar
										)
									)
								);
								$indexToolbar++;
							}else{
								$buttonRow[] = "
								{
									text: '". $jvDelete_text . "', datafield: 'buttondelete', width: 43, pinned:true,
									createwidget: function (row, column, value, htmlElement) {
										var datarecord = value;
										var img = '<i class=\"fas fa-times-circle\"></i>';
										var button = $(\"<div style='border:none;'>\" + img + \"</div>\");
										$(htmlElement).append(button);
										button.jqxButton({ template: 'danger', height: '100%', width: '80%' });
										button.click(function (event) {
											rowindex = row.boundindex;
											$('#".$gridname."').jqxGrid('selectrow', rowindex);
											jvDelete();
										});
									},
									initwidget: function (row, column, value, htmlElement) {
										
									}				
								},					
								";
							}
							if($jvDelete==""){
								$expandit = "";
								if($tipegrid=="jqxTreeGrid"){
									$expandit = "traverseTreeGrid(\"expand\");";
								}
							  	$jvDelete = "
								function jvDelete(){
									" . $selectrow . "
									if(id==null){
										swal.fire({ title:'".$CI->lang->line("confirm_not_selected")."', text: null, icon: 'warning', timer: 4000});
									}else{
										" . $idrow . "
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
																		$('#" . $gridname  . "').".$tipegrid."('clearfilters');
																		$('#" . $gridname  . "').".$tipegrid."('updateBoundData');
																		" . $expandit . "
																	}
																});
														} 
												}
										);
									}
								}
									";
															// " . $functionexpand . " -> untuk expandall, yg lama tidak support
							}				
						}
						//======================== button view ========================
						if($oVIW>0){
							if(!$inlinebutton){
								$arrButton = array_merge($arrButton, 
								array(
									$indexToolbar=>array(
										'text'=>$jvView_text, 
										'image'=>"fas fa-eye",
										'events'=>"jvView()",
										'theme'=>'dark',
										'width'=>70,
										'urutan'=>$indexToolbar
										)
									)
								);
								$indexToolbar++;
							}else{

								$buttonRow[] = "
								{
									text: '". $jvView_text . "', datafield: 'CI_rownum', width: 40, pinned:true,
									createwidget: function (row, column, value, htmlElement) {
										var datarecord = value;

										var img = '<i class=\"fas fa-eye\"></i>';
										var button = $(\"<div style='border:none;'>\" + img + \"</div>\");
										$(htmlElement).append(button);
										button.jqxButton({ template: 'inverse', height: '100%', width: '80%' });
										button.click(function (event) {
											rowindex = row.boundindex;
											$('#".$gridname."').jqxGrid('selectrow', rowindex);
											var args = row.args;
											jvView();
										});
									},
									initwidget: function (row, column, value, htmlElement) {
										// console.log(row);
									}				
								},					
								";
							}
							if($jvView==""){
								if($post){
									$fncEdit = "
										$('#".$formname."').attr('action', '/view/".$modul."');
										$('#grdIDENTS').val(id);
										document.".$formname.".submit();
									";
								}else{
									$fncEdit = "self.location.replace('/view/".$modul."/'+id);";
								}
								
								$jvView = "
									function jvView(){
										" . $selectrow . "
										if(id=='' || id==null){
											swal.fire({ title:'".$CI->lang->line("confirm_not_selected")."', text: null, icon: 'warning', timer: 4000});	
										}else{
											" . $idrow . "
											" . $fncEdit . "
										}
									}";
							}
						}
						//======================== button persetujuan ========================
						if($oAPP>0){
							$arrButton = array_merge($arrButton, 
								array(
									$indexToolbar=>array(
										'text'=>$jvApprove_text, 
										'image'=>"fas fa-thumbs-up",
										'events'=>"jvApprove()",
										'theme'=>'success',
										'width'=>100,
										'urutan'=>$indexToolbar
										)
									)
								);

							if(!isset($jvApprove)){
								if($post){
									$fncApp = "
										$('#".$formname."').attr('action', '/approval/".$modul."');
										$('#grdIDENTS').val(id);
										document.".$formname.".submit();
									";
								}else{
									$fncApp = "self.location.replace('/approval/".$modul."/'+id);";
								}
								
								$jvApprove = "
									function jvApprove(){
										" . $selectrow . "
										if(id=='' || id==null){
											swal.fire({ title:'".$CI->lang->line("confirm_not_selected")."', text: null, icon: 'warning', timer: 4000});
										}else{
											" . $idrow . "
											" . $fncApp . "
										}
									}";
							}				
							$indexToolbar++;
						}						
					}else{
						$buttemp = "";
						$container = "";
						$jqxbutton ="";
						$jqxscript = "";
						if(is_array($button)){
							foreach($button as $keyy=>$butval){
								$ident = $butval[0];				
								$image = "";
								$themebutton = "";
								$widthbutton = "";
								$events = "";
								if(isset($butval[1])){
									$image = $butval[1];
									//==================== check font-awesome ====================
									if(strpos("A".$image,"fa-")>0){
										// $imgbutton = "<i style='position: relative; top: -2px;left:4px' class='fas " . $image . "'></i>&nbsp;";
										$imgbutton = "<i style='position: relative; top: 0px;left:4px;color:#fff!important' class='fas " . $image . "'></i>&nbsp;";
									}else{
										$imgbutton = "<img style='position: relative; margin-top: -4px;width:15px;' src='/resources/img/". $image ."'/>";
									}
								}
								//============================================================
								$events = $butval[2];
								//==================== check theme ====================
								if(isset($butval[3])){
									if($butval[3]!=""){
										$themebutton = $butval[3];	
									}
								}
								//==================== check width ====================
								if(isset($butval[4])){
									$widthbutton = $butval[4];
								}
								$arrButton = array_merge($arrButton, 
									array(
										$indexToolbar=>array(
											'text'=>$keyy, 
											'image'=>$imgbutton,
											'events'=>$events,
											'theme'=>$themebutton,
											'width'=>$widthbutton,
											'urutan'=>$indexToolbar
											)
										)
									)
									;
								$indexToolbar++;
							}
						}
					}
				}
				if(isset($buttonother)){
					if($buttonotherposition=="first"){
						$indexToolbar = 0;
					}
					if(is_array($buttonother)){
						foreach($buttonother as $keyother=>$valother){
							$ident = $valother[0];				
							$image = "";
							$themebutton = "";
							$widthbutton = "";
							$events = "";
							if(isset($valother[1])){
								$image = $valother[1];
								//==================== check font-awesome ====================
								if(strpos("A".$image,"fa-")>0){
									// $imgbutton = "<i style='position: relative; top: -2px' class='fas " . $image . "'></i>&nbsp;";
									$imgbutton = "fas " . $image . "";
								}else{
									$imgbutton = "<img style='position: relative; margin-top: -4px;width:15px;' src='/resources/img/". $image ."'/>";
								}
							}
							//============================================================
							$events = $valother[2];
							//==================== check theme ====================
							if(isset($valother[3])){
								if($valother[3]!=""){
									$themebutton = $valother[3];	
								}
							}
							//==================== check width ====================
							if(isset($valother[4])){
								$widthbutton = $valother[4];
							}
							if(strpos("N".$keyother,"notext")>0){
								$keyother ="";
							}
							$arrButton = array_merge($arrButton, 
								array(
									$indexToolbar=>array(
										'text'=>$keyother, 
										'image'=>$imgbutton,
										'events'=>$events,
										'theme'=>$themebutton,
										'width'=>$widthbutton,
										'urutan'=>$indexToolbar
										)
									)
								)
								;
							$indexToolbar++;
						}
					}			
				}
				if(isset($help)){
					if($help){
						$arrButton = array_merge($arrButton, 
							array(
								$indexToolbar=>array(
									'text'=>"", 
									'image'=>"<li class='fas fa-question'></li>&nbsp;",
									'events'=>'jvHelp()',
									'theme'=>"default",
									'width'=>"20"
									)
								)
							)
							;
						$indexToolbar++;
					}
				}
				if($buttonotherposition=="first"){
					$keys = array_column($arrButton, 'urutan');
					array_multisort($keys, SORT_ASC, $arrButton);
				}
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
							// $buttonToolbar = createButton($arrButton, false);
							$buttonToolbar = createToolbar($arrToolbar, false);
						}else{
							$buttonToolbar = generateToolbar($arrToolbar);
						}
					}
				}else{
					$showToolbar = false;	
				}
			}else{
				$showToolbar = false;
			}

			if(isset($showGridbar)){
				if($showGridbar){
					$txtToolbar = "showToolbar: true,";
				}else{
					$txtToolbar = "";
				}
			}else{
				$txtToolbar = "showToolbar: true,";	
			}
			if($autorowheight!=""){
				$autorowheight = "autorowheight: true, ";
			}
			if($columnsheight!=""){
				$columnsheight = "columnsheight : '" . $columnsheight . "',";
			}
			// debug_array($columnsheight);
			$detailAdd = "";
			$adapterAdd = "";
			if($autoshowfiltericon==true){
				$autoshowfiltericon = "autoshowfiltericon: false,";	
			}
			if($filtermode=='excel'){
				$filtermode = "filterMode: 'excel',";	
			}else{
				if($filtermode=="filterrow"){
					$filtermode = "showfilterrow: true,";
				}else{
					$filtermode = "filterMode: '".$filtermode."',";		
				}
			}
			if($tipegrid=='jqxTreeGrid'){
				$filtermode = "filterMode: 'simple',";		
			}
			if($pagermode!="default"){
				$pagermode = "pagermode: '".$pagermode."',";
			}else{
				$pagermode = "";
			}
			if(isset($bisaedit)){
				if($bisaedit){
					if(isset($editmode)){
						$editmode = $editmode;
					}else{
						$editmode = 'selectedrow';
					}			

					if($tipegrid != "jqxTreeGrid"){
						$editable = "editable: true,
								        selectionmode: 'singlerow',
								        editmode: '".$editmode."',
								        ";
						//editmode: 'click',
						// editmode: 'selectedrow',
						// showeverpresentrow: true,
						// everpresentrowposition: 'top',
					}else{
						$editable = "editable: true,
								        ";
					}
				}
				// unset($bisaedit);
			}	
				// $detailAdd = "virtualmode: true,";
			if(isset($sumber) && $tipegrid!="jqxTreeGrid"){
				if($sumber=="server"){
					$detailAdd = "virtualmode: true,";
					if(isset($bisaedit)){
						if($bisaedit){
							$detailAdd .= "";
						}
					}
					$detailAdd .= "
						rendergridrows: function(obj)
						{
							return obj.data;    
						},
					";
					$adapterAdd = "
						filter: function()
						{
							// update the grid and send a request to the server.
							$(\"#" . $gridname . "\").jqxGrid('updatebounddata', 'filter');
						},
						sort: function()
						{
							// update the grid and send a request to the server.
							$(\"#" . $gridname . "\").jqxGrid('updatebounddata', 'sort');
						},
						root: 'Rows',
						beforeprocessing: function(data)
						{	
							if (data != null)
							{
								source.totalrecords = data[0].TotalRows;
							}
						}
					";
				}		
			}
			$detail ="";
			// $detail ="
			// 		var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties) {
			//        return '<span style=\"margin: 6px; margin-right: 10px; font-size: 10px; float: ' + columnproperties.cellsalign + ';\">' + value + '</span>';
			//      }";
			$renderercell = "";
			if(isset($cellsrenderer)){
				$renderercell = "";
				foreach ($cellsrenderer as $keyrender => $valuerender) {
					$renderercell .= $keyrender . " = " . $valuerender ."";
				}
				$renderercell .= "";
			}

			$detail .= $renderercell;
				
			if($scriptadd!=""){
				$detail .= $scriptadd;
			}
				
			$colrender ="
			var columnsrenderer = function (value) {
				return '<div style=\"display: table;  width:100%;height: 100%;\"><div style=\"display: table-cell;text-align:center;vertical-align: middle;\">' + value + '</div></div>';
			}";
			if($groupable!=""){
				if($groupable){
					$groupable = "groupable: true,
		                
					";
					// 
					$autoshowfiltericon = "";
					if(isset($groupcol)){
						$groupnya = ", groups: ['".$groupcol."']";
						if($expandgroup){
							$expandgroupnya = ", ready: function()
								{
									$('#".$gridname."').jqxGrid('expandallgroups');
								}";
							// $('#".$gridname."').jqxGrid({ pagesizeoptions: ['10','30']}); 
							// $('#".$gridname."').jqxGrid('expandallgroups');
						}
					}	
				}
			}
			if(isset($gridparam)){
				$arrGridparam = "";
				if(is_array($gridparam)){
					$arrGridparam = "data : {";
					$rg = false;
					for($e=0;$e<count($gridparam);$e++){
						if($rg) $arrGridparam .= ",";
						$arrGridparam .= $gridparam[$e].":''";
						$rg = true;
					}
					$arrGridparam .= "},";
				}
				$gridparam = $arrGridparam;
			}else{
				$gridparam = "";
			}
				
			$enabletooltips = "";

			if($tipegrid != "jqxTreeGrid"){
				if($autoheight){
					$autorowheight .= "autoheight:true,";
				}else{
					if($pageable!=""){
						// debug_array($pageable);
						$autorowheight .= "autoheight:false,";	
					}else{
						if(!$autoheight){
							$autorowheight .= "autoheight:false,";
						}else{
							$autorowheight .= "autoheight:true,";
						}
					}
					// $autorowheight .= "autoheight:false,";	
					// if($autorowheight!=""){
					// 	$autorowheight .= "autoheight:true,";
					// }else{
					// 	if($pageable){
					// 		$autorowheight .= "autoheight:false,";	
					// 	}else{
					// 		$autorowheight .= "autoheight:true,";
					// 	}
					// }
				}

				if(isset($selectionmode)){
					$selectionmode = "selectionmode: '".$selectionmode."',";
				}else{
					$selectionmode = "selectionmode: 'singlerow',";
				}
				if($tooltip){
					$enabletooltips = "enabletooltips : true, ";
				}
			}else{
				$selectionmode = "";
			}
			// localizationobj.filterstringcomparisonoperators = ['empty', 'not empty', 'berisi', 'berisi(case sensitive)','tidak berisi', 'tidak berisi(case sensitive)', 'dimulai karakter', 'dimulai karakter(case sensitive)','diakhiri karakter', 'diakhiri karakter(case sensitive)', 'sama dengan', 'sama dengan(case sensitive)', 'null', 'not null'];
			// localizationobj.filternumericcomparisonoperators =  ['sama dengan', 'tidak sama dengan', 'lebih kecil', 'lebih kecil sama dengan', 'lebih besar dari', 'lebih besar sama dengan', 'kosong', 'tidak kosong'];
			// localizationobj.filterdatecomparisonoperators = ['sama dengan', 'tidak sama dengan', 'lebih kecil', 'lebih kecil sama dengan', 'lebih besar dari', 'lebih besar sama dengan', 'kosong', 'tidak kosong'];
			// localizationobj.filterorconditionstring= 'Atau';
			// localizationobj.filterandconditionstring= 'Dan';
			// localizationobj.filtershowrowstring = 'Tampilkan data dengan kondisi :';

			$enablebrowserselection = ($tipegrid=="jqxTreeGrid") ? "enableBrowserSelection: true," : "enablebrowserselection: true,";

			if($rowdetails){
				if($readyfunction!=""){
					$create_ready = false;
				}
				$rowdetails ="
					rowdetails:true,
					rowdetailstemplate: { rowdetails: \"<div id='grid' style='margin: 10px;'></div>\", rowdetailsheight: 120, rowdetailshidden: true },
					ready: function () {
						$(\"#" . $gridname . "\").jqxGrid('showrowdetails', 1);
						" . $readyfunction . "
					},
        		";
    		}
	        if($initrowdetails!=""){
	        	$detail .= $initrowdetails;
	        	$initrowdetails = "initrowdetails: initrowdetails,";
	        }
			if($create_ready){
				$readyfunction = "
					    ready: function () {
			        	" . $readyfunction . "	
					    },
				";
			}
			// tambahan pengaturan menu cari
			if(isset($treegrid)){
				$menucolumn = '';
			}else{
				$menucolumn = 'columnmenuopening: function (menu, datafield, height) {menu.height(290);},';
			}

			$columnrow = "";

			if($inlinebutton){
				if(isset($buttonRow)){
					$rc = false;
					foreach($buttonRow as $key){
						if($rc) $columnrow .=",";
						$columnrow .= $key;
						$rc = true;
					}
				}
			}

			// { text: 'Edit', datafield: 'Edit', columntype: 'button', width: 80, cellsrenderer: function () {
			// 	return '<i class=\"fas fa-edit\"></i>';
			//  }, buttonclick: function (row) {
			// 	 jvEdit();
			// }},		
			if(!$keyboardnavigation){
				$keyboardnavigation = "keyboardnavigation: false,";
			}
			if(isset($treegrid)){
				if($treegrid){
					$keyboardnavigation = null;
				}
			}

			if($shownumrow){
				$columnrow .= "{text: 'No', sortable: false, filterable: false, editable: false,groupable: false, draggable: false, resizable: false,datafield: '', columntype: 'number', width: 40, cellsrenderer: function (row, column, value) {return \"<div style='margin:4px;'>\" + (value + 1) + \".</div>\";}},";
			}
			$columnrow .= $colDetail;
			
			$detail .= $colrender . "
		      " . $cellclassfunc . "
					var getLocalization = function () {
						var localizationobj ={};
				    localizationobj.groupsheaderstring = 'Pilih Kolom, tarik dan letakkan disini untuk pengelompokan data berdasarkan kolom';		
						return localizationobj
					};

					$(\"#" . $gridname . "\")." . $tipegrid . "(
					{
						" . $virtualmode . "
						" . $selectionmode . "
						" . $rowsheight . "
						" . $lebar . "
						" . $height . "
						" . $toolbarheight . "
						theme : theme,
						source: dataAdapter,
						" . $autorowheight . "
						" . $scrollmode . "
						" . $txtToolbar. "
						" . $groupable . "
						" . $pagermode . "
						" . $showaggregates . "
						columnsResize: true,
						" . $menucolumn . "
						localization: getLocalization(),
						sortable:true,
						" . $keyboardnavigation . "
						" . $enablebrowserselection . "
						" . $filtermode ."
						" . $autoshowfiltericon ."
						" . $columnsheight . "
						" . $detailAdd . "
						" . $pageable . "
						" . $editable . "
						" . $pagesize . "
						" . $filterable	. "
						" . $expand . "
						" . $buttong . "
						" . $enabletooltips . "
						" . $pagesizeoptions . "
						" . $rowdetails . "
						" . $initrowdetails . "
						" . $readyfunction . "						
						columns: [" . $columnrow . "] " . $columngroupdesc . "
						" . $groupnya . $expandgroupnya . "
		      });";
			//buat rowdetail jqxtreegrid
			/*
			  rowDetails: true,
			  rowDetailsRenderer: function (rowKey, row) {
				  if(row.rowlevel==4){
					  var indent = (1+row.level) * 20;
					  var details = \"<table style='margin: 10px; margin-left: \" + indent + \"px;'><tr><td></td><td>\" + row.tny_pertanyaan_desc + \"</td></tr></table>\";
					  return details;
				  }
			  },
			*/
				
			$urlparam = "";
			if(isset($paramurl)){
				if(is_array($paramurl)){
					$urlparam = "data: {";
					$rcin = false;
					foreach ($paramurl as $key => $value) {
						if($rcin) $urlparam .= ",";
						$urlparam .= $key . ":" . $value;
						$rcin = true;
					}
					$urlparam .="},";			
				}
			}
			$fnc_updaterow = "";
			if(isset($updaterow)){
				$adapterAdd .= $adapterAdd!="" ? "," : "";
				$adapterAdd .= "updaterow: function (rowid, rowdata, commit) {";
				$adapterAdd .= $updaterow;
				$adapterAdd .= "}";
			}
			$adapter .="	], " . $hirarki . "
							id: '". $IDENTS_COLUMN . "',
							url: url,
							" . $gridparam . "
							" . $urlparam . "
							" . $adapterAdd . "
						};
						var dataAdapter = new $.jqx.dataAdapter(source);";

			$script .= $adapter . $scripteditor . $detail;

			if(isset($event)){
				if(is_array($event)){
					foreach ($event as $key => $value) {
						# code...
						$script .= "$('#".$gridname."').on('" . $key . "',function(event){ ". $value ." });";
					}
				}
			}

			$script .= $buttonToolbar;
			if($startscript){
				$script .= "});";
			}
			// localizationobj.pagergotopagestring = "Gehe zu:";
			$arrStandar = array("Add", "Edit", "Delete", "View", "Approve", "Unggah");
			foreach($arrStandar as $keyStandar){
				if(isset(${"jv".$keyStandar})){
					$script .= ${"jv" . $keyStandar};
				}
			}

			// debug_array($script);
			// $jvAdd . $jvEdit . $jvDelete . $jvView . ;
			if($startscript){
				$script .= "</script>";
			}
			$script .= $cellcss;
			$gridstyle = "";
			if(isset($jsaggregate)){
				$gridstyle = $jsaggregate;
			}
			if($fontsize!="" || $headerfontsize!=""){
					$gridstyle .= "
					<style>";	
				if($fontsize!=""){
					$gridstyle .= "
						.jqx-grid-cell{
							font-size: ".$fontsize."px;
						}
					";
				}
				if($headerfontsize!=""){
					$gridstyle .= "
						.jqx-grid-column-header, .jqx-grid-columngroup-header {
								font-size: ".$headerfontsize."px;
						 }		
					";
				}
					$gridstyle .= "
					</style>";
			}
			$gridstyle .= "<style>
				// .jqx-listbox-container{
				// 	z-index:999999!important;
				// }
				// .jqx-calendar {
				// 	z-index:99999999!important;
				// }    				
			</style>
			";
			if($theme!="arctic"){
				$gridstyle .= "<link rel='stylesheet' href='" . base_url(PLUGINS."jqwidgets/styles/jqx.".$theme.".css") . "'>";
			}
			
			if($gridcenter){
				$awalCenter = "<div style='height:99%;'>";//"<center>";
				$akhrCenter = "</div>";//"</center>";
			}
			$gridview ="";

			if(isset($breadcrumb)){
				$gridview .= "<div style='height:40px;top:100px'>".$breadcrumb ."</div>";
			}
			$heightDiv = "99%";
			if($showToolbar){
				if(count($arrButton)>0 || isset($toolbarCombo)){
					// $gridview .= "<div style='height:37px'><div id=\"toolbar" . $gridname . "\" style='width:".$width.";margin-bottom:5px'></div></div>";
					//untuk metronic agar formnyaa turun dikit..tidak nempel ke toolbar button atas.
					$gridview .= "<div id=\"toolbar" . $gridname . "\" style='width:".$width.";margin-bottom:2px'></div>";	
					$heightDiv = "calc(100% - 50px)";
					// $heightDiv = "78vh";
				}
			}else{
				// $heightDiv = "83vh";
			}
			if(!isset($heightDiv)){
				$heightDiv = "99%";
			}
			if($creategrid){
				$gridview .= "<div style='".$gridpadding.";height:$heightDiv'><div id=\"" . $gridname . "\"></div></div>";
			}
			// if($creategrid && $gridcss==true){
			// 	// die();
			// 	$gridview .= "<div id=\"" . $gridname . "\" style='height:$heightDiv'></div>";
			// }			
			$form = "";
			if($post){
				$form = form_open_multipart($modul, array("name"=> $formname,"id"=> $formname));
				$form .= form_input(array('name' => "grdIDENTS",'id'=> "grdIDENTS", 'type'=>'hidden'));
				if($closeform){
					$form .= form_close();	
				}
			}
			// debug_array($gridstyle);
			return $gridstyle . $script . $gridview .	$form ;	
		}
	}
}
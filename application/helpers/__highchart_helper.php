<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * CKEditor helper for highchart
 * 
 * @author detanto
 * @package CodeIgniter
 * 
 */

 /** initialize highchart
  * based on type call highchart type
  * */
function highchart_initialize($data = array()) {
	$return = '';
	//print_r(get_defined_constants());
	// if(!defined('HIGHCHART_HELPER_LOADED')) {
		// define('HIGHCHART_HELPER_LOADED', TRUE);
		$return =  "<script>
		if (window.Highcharts === undefined) {
			var hc = $(\"<script type='text/javascript' src='". base_url(PLUGINS."highcharts/highcharts.js") ."'>\");
			$(\"head\").append(hc);
			var exp = $(\"<script type='text/javascript' src='". base_url(PLUGINS."highcharts/modules/exporting.js") ."'>\");
			$(\"head\").append(exp);
			};
		</script>
		<script src='". base_url(PLUGINS."highcharts/themes/grid-light.js") ."'></script>
		";
		//<script type=\"text/javascript\" src=\"".base_url(JS."highcharts.js"). "\"></script>";
		//$return .=  "<script type=\"text/javascript\" src=\"".base_url(JS."exporting.js"). "\"></script>

	// }
	return $return;
}

function highchart_create_instance($data = array()){
	$CI =& get_instance();
	$dataset = "";
	$click="";
	$script = "";
	$showvalue = true;
	$percentage = true;
	$colorByPoint = false;
	$showVal = "";
	$legend = "";
	$legendlayout = "vertical";
	$xAxistitle ="";
	$yAxistitle = "";
	$processeddata = false;

	foreach($data as $keyHC=>$valueHC){
		${$keyHC} = $valueHC;
	}

	if(isset($legend)){
		if($legend=='right'){
			if(isset($legendlayout)){
				$legendlayout = "horizontal";
			}
			$legend = ",
				legend: {
					responsive: true,
		            enabled: true,
		            layout: '".$legendlayout."',
		            align: 'right',
		            width: 220,
		            verticalAlign: 'top',
		            borderWidth: 0, x:0,
								y:40,
		            useHTML: true,
		            labelFormatter: function() {
						var total = 0;
      					for(var i=this.yData.length; i--;) { total += this.yData[i]; };
      	                return '<div style=\"top:100px;width:200px\"><span style=\"float:left\">' + this.name + '</span>&nbsp;&nbsp;<span style=\"color:#10c100\">[&nbsp;' + addCommas(total) + '&nbsp;]</span></div>';
		            }
				}
			";			
		}
	}
	if(!isset($yAxistitle)){
		if(isset($legend)){
			$yAxistitle = $legend;
		}
	}	
	$total = 0;
	$grouping = false;
	if(isset($seriesgrouping)){
		// $return .= "options.plotOptions.series.grouping=".$data['seriesgrouping'].";";
		$grouping = $data['seriesgrouping'];
	}	
	$rc = false;
	$showtotal = false;
	$charttype = $chart;
	$chartid = $id;
	switch ($charttype){
		//============================================ grafik pie
		case "pie" :
			$field = explode("~", $fields);
			foreach($data['resultset']->result() as $value){
				if ($rc) $dataset .= ",";
				$rc = true;
				$PIE_DESCRE = $value->{$field[0]};
				$PIE_VALUES = $value->{$field[1]};
				$total = $total + $PIE_VALUES;
				if(isset($data['arrValue'])){
					if(is_array($data['arrValue'])){
						foreach($data['arrValue'] as $akey=>$avalue){
							if(isset($field[2])){
								$PIE_ADDTIN = $value->{$field[2]};
								if($akey==$PIE_ADDTIN){	
									$PIE_DESCRE = $avalue;
								}								
							}else{
								if($akey==$PIE_DESCRE){	
									$PIE_DESCRE = $avalue;
								}							
							}
						}
					}
				}
				// $dataset .= "['" . $PIE_DESCRE . "', " . $PIE_VALUES . "]";
				if($PIE_DESCRE==""){
					$PIE_DESCRE = "Tidak Ada";
				}
				if(isset($field[2])){
					$PIE_ADDTIN = $value->{$field[2]};	

					if($PIE_ADDTIN=="")	{
						$PIE_ADDTIN = "0";
					}
					$dataset .= "{name:'" . $PIE_DESCRE . "', y:" . $PIE_VALUES . ",additional:'".$PIE_ADDTIN."'}";		
				}else{
					$dataset .= "['" . $PIE_DESCRE . "', " . $PIE_VALUES . "]";	
				}				
			}
			if($showvalue){
				$showVal = ": {point.y:,.0f}";
			}
			if($percentage){
				$valPercent = "{point.percentage:.1f}%";
			}else{
				$valPercent = "{point.y:,.0f}";
			}
			if(isset($data["legend"])){
				$legend = $data["legend"];
			}else{
				$legend = null;
			}
			$options = ",
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: false
						},
						showInLegend: false
					},
					series: {
						dataLabels: {
							enabled: true,
							padding: 1,
							format: '{point.name}".$showVal."'
						}
					}
				},//" . $legend . ",
				series: [{
					type: 'pie',
					name: '" .$legend ."',
					data: [" . $dataset . "],
					point: {
						events: {
							click: function(e) {
								". $click. "
							}
						}
					}
				}],
				tooltip: {
					pointFormat: '{series.name}: <b>".$valPercent."</b>'
				},
			";
			break;
		//============================================ grafik line
		case "line" :
			$dataset = "";
			$xaxis = "";
			$categories = "";
			$descr = "[";
			$rc = false;
			$loop = 1;
			$field = explode("~", $data['fields']);
			foreach($data['resultset']->result() as $value){
				if ($rc) {
					$dataset .= ",";
					$categories .= ",";
					$descr .= ",";
				}
				$rc = true;
				$fieldnya = $value->{$field[0]};
				// $dataset .= "" . $value->{$field[1]} . "";
				if(isset($field[2])){
					$ADDTION = $value->{$field[2]};		
					$dataset .= "{y:" . $value->{$field[1]} . ",a:'".$ADDTION ."'}";
				}else{
					$dataset .= "" . $value->{$field[1]} . "";
				}
				$total = $total + $value->{$field[1]};
				$categories .= "'" . $fieldnya . "'";
				$loop++;
			}

			$descr .= "]";
			$data['dataset'] = $dataset;
			$data['xaxis'] = $xaxis;
			$data['descr'] = $descr;
			$rotation = "0";
			$fontsize = 10;
			$xAxistitle ="";
			$yAxistitle = "";
			$desclegend = "";
			$font = "Verdana, sans-serif";
			if(isset($data["xAxistitle"])){
				$xAxistitle = $data["xAxistitle"];
			}
			if(isset($data["yAxistitle"])){
				$yAxistitle = $data["yAxistitle"];
			}else{
				if(isset($data["legend"])){
					$yAxistitle = $data["legend"];
				}
			}
			if(isset($data["fontsize"])){
				$fontsize=$data["fontsize"];
			}
			if(isset($data["rotation"])){
				$rotation=$data["rotation"];
			}
			if(isset($data["font"])){
				$font=$data["font"];
			}

			if(isset($data['total'])==true){
				$desclegend = "
				legend: {
					 labelFormatter: function() {
							var total = 0;
							for(var i=this.yData.length; i--;) { total += this.yData[i]; };
							return this.name + ' - Total: ' + total;
					 }
				},";
			}else{
				$desclegend = "";
			}
			$options = ",".$desclegend."
				xAxis: {
					title: {
							text: '".$xAxistitle."'
					},
					categories: [" . $categories . "],
					labels: {
						rotation: " . $rotation . ",
						style: {
							fontSize: '" . $fontsize . "px',
							fontFamily: '" . $font . "'
						}
					}
				},
				yAxis: {
					title: {
						text: '".$yAxistitle."'
					}
				},
				plotOptions: {
					line: {
						dataLabels: {
							enabled: true
						},
						enableMouseTracking: false
					}
				},
				series: [{";
				if(isset($data["warna"])){
					$options .= "color: '" . $data["warna"]."',";	
				}
				
				$options .= "
						name: '" . $yAxistitle. "',
						data: [" . $dataset . "]
				}],
				plotOptions: {
					series: {
						cursor: 'pointer',
						point: {
							events: {
							click: function(e) {
									" . $click . "
								}
							}
						},
						marker: {
							lineWidth: 1
						}
					}
				}
			";
			break;
		//============================================ grafik bar/column
		case "bar" :
		case "column" :
			$charttype = "column";
			$dataset = "";
			$xaxis = "";
			$categories = "";
			$descr = "[";
			$rc = false;
			$loop = 0;
			$options = null;
			$field = explode("~", $data['fields']);
			if(!$processeddata){
				if(isset($data['resultset'])){
					foreach($data['resultset']->result() as $value){
						$fieldnya = $value->{$field[0]};
						$nilai = $value->{$field[1]};
						if ($rc) {
							$dataset .= ",";
							$categories .= ",";
						}
						$dataset .= "{";
						$rc = true;					
						$dataset .= "\"name\": \"". trim($fieldnya)."\",";
						$dataset .= "\"data\": [[".$loop.",".$nilai."]]";
						if(isset($field[2])){
							$idents = $value->{$field[2]};
							$dataset .= ", \"a\" : \"".$idents."\"";
						}
						$dataset .= "}";
						$categories .= "'" . $fieldnya . "'";
						$total = $total + $nilai;
						$loop++;
					}
				}
			}else{
				$dataset = $resultdata;
				$categories = $categoriesdata;
			}
			$descr .= "]";
			$rotation = "0";
			$fontsize = 8;
			$font = "Verdana, sans-serif";
			if(isset($data["fontsize"])){
				$fontsize=$data["fontsize"];
			}
			if(isset($data["rotation"])){
				$rotation=$data["rotation"];
			}
			if(isset($data["font"])){
				$font=$data["font"];
			}
			// $options = $legend;
			//===== option xaxis
			$options .= " 
				, xAxis: {
					title: {
							text: '".$data["xAxistitle"]."'
					},";
			if(isset($url)){
				$options .= "categories: categoriesnya,";
			}else{
				$options .="categories: [" . $categories . "],";				
			}
			$options .="
					labels: {
						rotation: " . $rotation . ",
						style: {
							fontSize: '" . $fontsize . "px',
							fontFamily: '" . $font . "'
						}
					},
        			crosshair: true
				},
				yAxis: {
					title: {
						text: '".$data["yAxistitle"]."'
					}
				},
				plotOptions: {
					column: {
						dataLabels: {
								enabled: true
						},
						enableMouseTracking: false
					}
				},
				series: seriesnya";

			if($click!=""){
				$click = "
					point: {
						events: {
							click: function(e) {
							". $click. "
							}
						}
					},
				";
			}
			if($colorByPoint){
				$colorByPoint = "
					column: {
						colorByPoint: true
					}
				";
			}else{
				$colorByPoint = "";
			}
			$options .=",
				plotOptions: {
					series: {
						cursor: 'pointer',
						grouping : " . ($grouping==true ? "true" : "false") . ",
						" . $click . "
						marker: {lineWidth: 1}
					},
					" . $colorByPoint. "
				},
			";
			break;		
		case "area" :
			$charttype = "area";
			$options = ",
				xAxis: {
					categories: categoriesnya,
					tickmarkPlacement: 'on',
					title: {
						text: '".$xAxistitle."',
						enabled: false
					}
			},
			yAxis: {
					title: {
							text: '".$yAxistitle."'
					}
			},			
			series: seriesnya,
			plotOptions: {
				area: {
						stacking: 'percent',
						lineColor: '#ffffff',
						lineWidth: 1,
						marker: {
								lineWidth: 1,
								lineColor: '#ffffff'
						}
				}
			},

			";
			break;
	}

	$return = "
	<script type=\"text/javascript\">
		$(document).ready(function(){
			$('#imgPROSES').show();
			$('#windowProses').jqxWindow('open');
			Highcharts.setOptions({
			    lang: {
			      decimalPoint: '.',
			      thousandsSep: ',',
			      numericSymbols: null
			    }
	    	});
	";
	
	$descTotal = number_format($total);
	if(isset($data['showtotal'])){
		$showtotal = $data['showtotal'];
		if($showtotal){
			$total = " [ " . $descTotal . " ]";
		}else{
			$total = "";
		}
	}else{
		$total = "";
	}
	if(isset($data['title'])){
		$title = $data['title'];
	}
	$titlechart = "'" . (isset($title) ? $title : '') . (isset($url) ? "" : $total) . "'" ;
	$chartopt = "
			var options".$chartid." = {
			    chart: {
					renderTo: '".$chartid."',
					type: '".$charttype."',
					events: {
						load: function(event) {
							$('#loader".$chartid."').css('display', 'none');
						}
					},
					// height : '50%',
					// marginRight: 130,
					// marginBottom: 55,
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
			    },
				title: {
	            	useHTML:true,
					text: $titlechart
				}";
	$chartopt .= $options;
	$chartopt .= "
			};";
	if(isset($data['pointWidth'])){
		$chartopt .= "options".$chartid.".plotOptions.series.pointWidth=".$data['pointWidth'].";";
	}
	if($grouping){
		$chartopt .= "options".$chartid.".plotOptions.series.grouping=true;";
	}
	$chartopt .= "
			chart".$chartid." = new Highcharts.Chart(options".$chartid.");
			hghPlaceholder = $('#" . $data["id"] . "').parent().height()-20;
			// swal(hghPlaceholder.toString());
			if(hghPlaceholder>0){
				$('#" . $data["id"] . "').height(hghPlaceholder);
			}
			// chart".$chartid.".showLoading();

	";
	if(isset($url)){
		$field = explode("~", $data['fields']);
		$statuslengkap = "";
		if(isset($field[2])){
			$lengkap = "
				lengkap.push([obj.".$field[0].", obj.".$field[1].", obj.".$field[2]."]);
			";
			$details = "
				if(detail.indexOf(obj.".$field[1].") === -1){
					if(obj.".$field[1]."==0){
						// detail.splice(obj.".$field[1].");
						detail.splice(id,0, '0');
					}else{
						detail.push(obj.".$field[1].");
					}
				}
			";
			$grouping = "true";
			$statuslengkap = "komplit = true;";
		}else{
			$lengkap = "
				lengkap.push([obj.".$field[0].", obj.".$field[1]."]);
			";
			$details = "
				if(obj.".$field[1]."==0){
					detail.splice(id,0, '0');
				}else{
					detail.push(obj.".$field[1].");
				}
			";
		}
		if($showtotal){
			$statuslengkap .= "var itungtotal = true;";
		}
				// if(detail.indexOf(obj.".$field[1].") === -1){
				// 	if(obj.".$field[1]."==0){
				// 		// detail.splice(obj.".$field[1].");
				// 		detail.splice(id,0, '0');
				// 	}else{
				// 		detail.push(obj.".$field[1].");
				// 	}
				// }		
		$return .= "	
			var jqxhr = $.getJSON('/". $url. "', function(datax) {
				$('#imgPROSES').show();
				$('#windowProses').jqxWindow('open');
				var arr = {'1':'Jan','2':'Feb','3':'Mar','4':'Apr','5':'May','6':'Jun','7':'Jul','8':'Aug','9':'Sep','10':'Oct','11':'Nov','12':'Dec'};
				var $field[1] = {};
				var id = 0;
				var categories = [];
				var detail = [];
				var value = [];
				var lengkap = [];
				var komplit = false;
				var itungtotal = false;
				var gtotal = 0;
				" . $statuslengkap . "
				datax.forEach(function(obj){
				if(categories.indexOf(obj.".$field[0].") === -1){
					categories.push(obj.".$field[0].");
				}
				" . $details . "
				" . $lengkap . "
				id++;
			});
			jqxhr.done(function() {
				$('#windowProses').jqxWindow('close');
			})


			var grid = {};
			categories = categories.filter(function(e){return e});
			// categories.sort();
			// console.log(categories);
			for(var e=0; e <categories.length; e++){
				var category = categories[e];
				if(category in grid == false){
					if(komplit){
						grid[category] = {}; // must initialize the sub-object, otherwise will get 'undefined' errors
					}else{
						if(detail[e]!==undefined){
							grid[category] = detail[e];
							if(itungtotal){
								if(detail[e]=='0'){
									gtotal += 0;
								}else{
									gtotal += detail[e];
								}
							}
						}else{
							grid[category] = 0;
						}
					}
				}
				
				if(komplit){
					for(var n=0; n<detail.length; n++){
						var detailnya = detail[n];
						grid[category][detailnya] = 0;  
						for(var y=0; y<lengkap.length;y++){
							if(category==lengkap[y][0]){
								if(detailnya==lengkap[y][1]){
									value = lengkap[y][2];
									grid[category][detailnya] = value;
								}
							}
						}
					}
				}
			}
			categoriesnya = '[';
			rc = false;
			if(komplit){
				categories = detail;	
			}
          	for(var h=0; h<categories.length; h++){
				if(rc) {
					categoriesnya += ',';
				}
				categoriesnya += '\"' + categories[h] + '\"';
            	rc = true;
          	}
			categoriesnya += ']';
			categoriesnya = JSON.parse(categoriesnya);
					
			if(komplit){
            	var total = {};
	            seriesnya = '[';
	            rc = false;
	            for (var key in grid) {
	            	gtotal = 0;
					if(rc) {
						seriesnya += ',';
					}
					seriesnya += '{\"name\" :\"'+ key+ '\", \"data\" : ['
					rcin = false;
					for(var h=0; h<detail.length; h++){
						if(rcin) {
							seriesnya += ',';
						}
						arr = detail[h];
						gtotal += grid[key][arr];
						seriesnya += grid[key][arr];

						rcin = true;
					}
	            	total[key] = gtotal;
					seriesnya += ']}'
					rc = true;
	          	}
	            seriesnya += ']';            	
            }else{
				seriesnya = '[';
				rc = false;
				var e = 0;
	            for (var key in grid) {
					if(rc) {
						seriesnya += ',';
					}
					seriesnya += '{ \"name\" : \"' + key + '\", \"data\" : [[' + e + ',' + grid[key] + ']]}';
					rc = true;
					e++;
	          	}
  				seriesnya += ']';
          	}
			if(komplit){
				totalvalue = '<table>';
				gtotal = 0;
				for(var crut in total){
					gtotal += total[crut];
					totalvalue += '<tr><td>' + crut + '</td><td style=\'width:20px\'>:</td><td style=\'text-align:right\'>' +  addCommas(total[crut]) + '</td></tr>';
				}
				totalvalue += '</table>';
        	}else{
        		totalvalue = addCommas(gtotal);	
        	}
        	totalvalue = addCommas(gtotal);
			seriesnya = JSON.parse(seriesnya);
			" . $chartopt . "
			if(itungtotal){
				titlenya = $titlechart + ' [ ' + totalvalue + ' ]';
				chart".$chartid.".setTitle({text: titlenya});
			}
		});
		";
	}else{
		if($charttype=="column"){
			if($processeddata){
				$return .= "
					seriesnya = JSON.parse('[".$dataset."]');
				";
			}else{
				$return .= "
					seriesnya = ".$dataset.";
				";
			}
		}
		$return .= $chartopt;
	}
	$return .= "
			$('#imgPROSES').hide();
			$('#windowProses').jqxWindow('close');
		})		
	</script>";	
	$style = "";
	if(isset($data["width"])){
		$style .= "min-width: " . $data["width"] .";";
	}
	if(isset($data["height"])){
		$style .= "height: " . $data["height"] ."";
	}
	if($style==""){
		$style = "width:100%;";
	}
	$return .="
	<style>
	.loader {
	  border: 16px solid #f3f3f3;
	  border-radius: 50%;
	  border-top: 16px solid #3498db;
	  width: 120px;
	  height: 120px;
	  -webkit-animation: spin 2s linear infinite; /* Safari */
	  animation: spin 2s linear infinite;
	}
	
	/* Safari */
	@-webkit-keyframes spin {
	  0% { -webkit-transform: rotate(0deg); }
	  100% { -webkit-transform: rotate(360deg); }
	}
	
	@keyframes spin {
	  0% { transform: rotate(0deg); }
	  100% { transform: rotate(360deg); }
	}
	</style>
	<center><div class='loader' id='loader".$chartid."'></div></center>
	";
	$return .= "<div id='" . $chartid . "' style='margin: 0 auto;" . (isset($style) ? $style : "") . "'></div>";
	return	$return;
}
function display_highchart($data = array(), $defined=false){
	// Initialization
	//
	$return = highchart_initialize($data);
	$return .= highchart_create_instance($data);
	return $return;
}

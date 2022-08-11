
	function generateinputfile($detail, $status="edit"){
		// debug_array($detail);
		$dropzone = false;
		$parallelUploads = 'false';
		$maxFilesize = 1;
		$maxFiles = 1;
		$previewTemplate = 'n';
		$showfile = true;
		$filexist = true;
		$location = "/assets/documents/";
		$autoupload = true;
		$uploadMultiple = 'false';
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
		if(isset($detail['divname'])){
			if($detail['divname']!=""){
				$divname = $detail['divname'];	
			}
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
						t.on("'.$keyEDZ.'", (function(o, response) {
							'.$valueEDZ.';					
						}))
						';
						$rcDZ = true;
					}
				}
				if(!$autoupload){
					$autoupload = 'autoProcessQueue: false,';
				}else{
					$autoupload = 'autoProcessQueue: true,';
				}
				// parallelUploads: '.$parallelUploads.',
				$script1 = null;
				if($maxFiles==1){
					$script1 = '
					t.on("addedfile", function (file) {
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
				
				$forminputnya  = '
				<script type=module>
				var t;
				$(document).ready(function(){
					var e = "#dz_'.$detail['namanya'].'",
					o = $(e + " .dropzone-item");
					o.id = "";
					var n = o.parent(".dropzone-items").html();
					o.remove();
					var t = new Dropzone(e, {
						url: "'.$url.'",
						'.$autoupload.'
						parallelUploads: '.$parallelUploads.',
						maxFiles: ' . $maxFiles . ',
						uploadMultiple: '.$uploadMultiple.',
						maxFilesize: '.$maxFilesize.',
						previewTemplate: '.$previewTemplate .',
						previewsContainer: e + " .dropzone-items",
						clickable: e + " .dropzone-select",
						// init: function () {
						// 	var myDropzone = this;
						// 	$("#nguik").click(function (e) {
						// 		e.preventDefault();
						// 		myDropzone.processQueue();
						// 	});
						// }						
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
					})), '.$script1.' t.on("success", (function(o, response) {
						$("#'.$detail['namanya'].'").val(response);
					}))' . $eventDZ . '
				});
				console.log(t);
				</script>
				';
				if(!isset($url)){
					show_error("URL Dropzone not defined");
				}
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
									$labelgw = "<a href='".$location .$detail['value']."' target=_blank><img src='". $location .$detail['value']."' height='400' width='400'></a>";	
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
				if(!isset($labelgw)){
					$labelgw = $iconed . "&nbsp;&nbsp;<a href='".$linknya."' target=_blank>". $detail['value']."</a>";
				}
				$return = form_label($labelgw,'', array('class' => 'btn btn-default', 'style'=>'padding:8px')). $forminputnya . "&nbsp;&nbsp;";	
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
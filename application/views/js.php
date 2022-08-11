<?
$sessi_berhasil = $this->lang->line("sessi_berhasil");
?>
<!--======= start :: jqxwidget loading -->
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxcore.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxtabs.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdropdownbutton.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdata.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdata.export.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxwindow.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxtree.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxlayout.js");?>"></script>	
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxribbon.js");?>"></script>
<!--===================== start :: grid related -->
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdatatable.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxtreegrid.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.export.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.filter.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.columnsresize.js")?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.grouping.js")?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.selection.js")?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.sort.js")?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxgrid.pager.js")?>"></script>
<!--===================== end :: grid related -->
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxinput.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxscrollbar.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxbuttons.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxtoolbar.js");?>"></script>	
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdate.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdatetimeinput.js");?>"></script>
<!-- <script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxnumberinput.js");?>"></script> -->
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxcalendar.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxmenu.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxtooltip.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdropdownlist.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxlistbox.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxcombobox.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxvalidator.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxsplitter.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxcheckbox.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxscheduler.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxscheduler.api.js");?>"></script>
<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/globalization/globalize.js");?>"></script>
<script type="text/javascript" type="module" src="<?=base_url(PLUGINS."autoNumeric/autoNumeric.js");?>"></script>

<!-- <script src="../" type="text/javascript"></script>s -->

<script>
	// $(this).html(event.strftime(totalHours + ' hr %M min %S sec'));
	$(document).ready(function(){
		// $('#wLogin').modal({backdrop: 'static', keyboard: false});
		jvCountdown();
		$('form').attr('autocomplete', 'off');
		$('form input').attr('autofill', 'off');
		// $(".animsition").animsition({
		// 	inClass: 'fade-in-down',
		// 	outClass: 'fade-out-down',
		// 	inDuration: 1500,
		// 	outDuration: 800,
		// 	linkElement: '.animsition-link',
		// 	// e.g. linkElement: 'a:not([target="_blank"]):not([href^="#"])'
		// 	loading: true,
		// 	loadingParentElement: 'body', //animsition wrapper element
		// 	loadingClass: 'animsition-loading',
		// 	loadingInner: '', // e.g '<img src="loading.svg" />'
		// 	timeout: false,
		// 	timeoutCountdown: 5000,
		// 	onLoadEvent: true,
		// 	browser: [ 'animation-duration', '-webkit-animation-duration'],
		// 	// "browser" option allows you to disable the "animsition" in case the css property in the array is not supported by your browser.
		// 	// The default setting is to disable the "animsition" in a browser that does not support "animation-duration".
		// 	overlay : false,
		// 	overlayClass : 'animsition-overlay-slide',
		// 	overlayParentElement : 'body',
		// 	transition: function(url){ window.location.href = url; }
		// });
	});
	function jvCountdown(){
		var endtime = "<?=$this->config->item('sess_expiration');?>";
		var sess_exp = new Date().getTime() + (endtime * 1000)
		$('#clock').countdown(sess_exp, function(event) {
			var totalHours = event.offset.totalDays * 24 + event.offset.hours;
			$('#clock').html(event.strftime(totalHours + ' hr %M min'));
		}).on('finish.countdown', function(event) {
			$('#wLogin').modal({backdrop: 'static', keyboard: false});
			window.$('#wLogin').modal('show');
			// $('#wLogin').modal({backdrop: 'static', keyboard: false});
		});
	}
	function jvLogin(){
		var param = {};
		param['username'] = $('#xusername').val();
		param['password'] = $('#xpassword').val();
		param['sumber'] = 'relogin';
		$("#windowProses").jqxWindow('open');
		$.post('/login/validate_credentials',param,function(rebound){
			if(rebound!=1){
				$("#windowProses").jqxWindow('close');
				Swal.fire({title:rebound,icon:'error', timer: 4000});
			}else{
				$("#windowProses").jqxWindow('close');
				<?=$this->common->swal2(array('title'=> $sessi_berhasil . '!','type'=>'success','timer'=>3000, 'submit'=>false))?>
				$('#xpassword').val('');
				// $("#wLogin .close").click();
				$("#wLogin").modal("hide"); 
				jvCountdown();
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
  function jvNumericonly(event){
    var theEvent = event || window.event;
    var key = theEvent.keyCode || theEvent.which;
    if(key!=8){
	    key = String.fromCharCode(key);
	    var regex = /[0-9]/;
	    if( !regex.test(key) ) {
	      theEvent.returnValue = false;
	      if(theEvent.preventDefault) theEvent.preventDefault();
	    }
    }
  }

  	function addCommas(Str){
		nStr = roundme(Str,2);
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
	function roundme(value, decimals) {
		return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
	}
</script>
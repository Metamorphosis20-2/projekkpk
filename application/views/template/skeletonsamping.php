<?php

// $this->common->debug_array($this->session->userdata);

$usr_aplikasi = $this->session->userdata('USR_AD');
$usr_fnames = $this->session->userdata('USR_FNAMES');
$usr_logins = $this->session->userdata('USR_LOGINS');
$usr_levels = $this->session->userdata('USR_LEVELS');
$usrauthrz = $this->session->userdata('USR_AUTHRZ');	
$usrtypusr = $this->session->userdata('USR_TYPUSR');
$usr_layout = $this->session->userdata('USR_LAYOUT');
$desc = $this->session->userdata('USR_LEVEL_DESC');

$unit_kerja = $this->session->userdata('USR_UNITKERJA');
$unit_kerja_desc = $this->session->userdata('USR_UNITKERJA_DESC');
if($usr_levels>2){
	$desc .= ($unit_kerja_desc=="" ? "" : (" - " . $unit_kerja_desc));
}

$show_envrm = $this->config->item('showenv');
$metaconten = $this->config->item('metacontent');
$app_names_short = $this->config->item('app_names_short');
$aside_minimize  = "aside-minimize";
// $aside_minimize  = "";

$app_names = $this->config->item('app_names');
$ownerapp  = $this->config->item('ownerapp');
$apps_theme = ($this->config->item('app_theme')=="" ? "arctic" : $this->config->item('app_theme'));
$grid_theme = ($this->config->item('app_grids')=="" ? "arctic" : $this->config->item('app_grids'));

$logo = $this->config->item('logo');
$logo_light = $this->config->item('logo_light');
$layout = "";
$svnversion = $this->common->getVersion();

$latestactivity = $this->common->getUserActivitylast_list();

// $this->common->debug_array($rslLast->result());
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title><?=$ownerapp?> | <?=$app_names?></title>
		<meta name="description" content="Updates and statistics" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link rel="stylesheet" href="https://unpkg.com/microtip/microtip.css"/>		

        <link href="<?=base_url(PLUGINS."metronic/css/global/plugins.bundle.css?v=7.0.6");?>" rel="stylesheet" type="text/css"/>
        <link href="<?=base_url(PLUGINS."metronic/css/style.bundle.css?v=7.0.6");?>" rel="stylesheet" type="text/css"/>

        <link href="<?=base_url(PLUGINS."metronic/css/css/themes/layout/header/base/light.css?v=7.0.7");?>" rel="stylesheet" type="text/css"/>
        <link href="<?=base_url(PLUGINS."metronic/css/css/themes/layout/header/menu/light.css?v=7.0.7");?>" rel="stylesheet" type="text/css"/>

        <link href="<?=base_url(PLUGINS."metronic/css/css/themes/layout/brand/dark.css?v=7.0.7");?>" rel="stylesheet" type="text/css"/>
        <link href="<?=base_url(PLUGINS."metronic/css/css/themes/layout/aside/dark.css?v=7.0.7");?>" rel="stylesheet" type="text/css"/>
		<link rel="shortcut icon" href="<?=base_url(IMAGES."favicon/favicon.ico");?>" />
        <link rel="stylesheet" href="<?=base_url(PLUGINS."jqwidgets/styles/jqx.base.css");?>">
        <link rel="stylesheet" href="<?=base_url(PLUGINS."jqwidgets/styles/jqx." . $grid_theme . ".css");?>">
        <link rel="stylesheet" href="<?=base_url(PLUGINS."jqwidgets/styles/jqx.orange.css");?>">
        <link rel="stylesheet" href="<?=base_url(PLUGINS."jqwidgets/styles/jqx.bootstrap.css");?>">

		<script type="text/javascript" src="<?=base_url(JS."jquery.min.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxcore.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdata.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxwindow.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."jqwidgets/jqxdata.export.js");?>"></script>
		<script src="<?=base_url(PLUGINS."metronic/css/global/plugins.bundle.js?v=7.0.6");?>"></script>
		<script src="<?=base_url(PLUGINS."metronic/js/scripts.bundle.js?v=7.0.6");?>"></script>

		<?=$css?>
		<?=$scriptjs?>
		<style>
			.row{
				margin-right:0px;
				margin-left:0px;
			}
			@media (min-width: 992px){
				.container, .container-fluid, .container-sm, .container-md, .container-lg, .container-xl, .container-xxl {
					padding: 0px 8px;
					max-width: 100%;
				}
				.content{
					padding: 5px 0px;
				}
				.bcrumb{
					padding: 10px;
				}
			}
			.form-group {
				margin-bottom: 0.75rem;
			}
		</style>
	</head>

	<body id="kt_body" class="header-fixed header-mobile-fixed aside-enabled aside-fixed <?=$aside_minimize?> aside-minimize-hoverable footer-fixed page-loading">
		<!-- <body id="kt_body" class="page-loading-enabled page-loading header-fixed header-mobile-fixed aside-enabled aside-fixed <?=$aside_minimize?> aside-minimize-hoverable footer-fixed page-loading"> -->
		<!-- <body id="kt_body" class="header-fixed header-mobile-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading"> -->
		<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed"  style="background-color:#1AC8ED">
			<!--begin::Logo-->
			<a href="/home/welcome" class="brand-logo" style="text-align:center">
				<img src="<?=base_url(IMAGES.$logo_light);?>" alt="Logo" style="height:40px"/>
			</a>
			<div class="d-flex align-items-center">
				<button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
					<span></span>
				</button>
				<button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
					<span></span>
				</button>
				<button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
					<span class="svg-icon svg-icon-xl">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon points="0 0 24 0 24 24 0 24" />
								<path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
								<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
							</g>
						</svg>
					</span>
				</button>
			</div>
			<!--end::Toolbar-->
		</div>

		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">
				<!--begin::Aside-->
				<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside"  style="z-index:9999999 !important;">
					<!--begin::Brand-->
					<div class="brand flex-column-auto" id="kt_brand" style="background-color:#1AC8ED">
						<!--begin::Logo-->
						<a href="/home/welcome" class="brand-logo" style="text-align:center">
							<img src="<?=base_url(IMAGES.$logo_light);?>" alt="Logo" style="height:40px"/>
						</a>

						<!--end::Logo-->
						<!--begin::Toggle-->
						<button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
							<span class="svg-icon svg-icon svg-icon-xl">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Angle-double-left.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
										<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
									</g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</button>
						<!--end::Toolbar-->
					</div>
					<!--end::Brand-->
					<!--begin::Aside Menu-->
					<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
						<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="2229500" style="z-index:9999999!important">
							<?=$this->common->vmenu();?>
						</div>						
					</div>
				</div>
				<!--end::Aside-->
				<!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
					<!--begin::Header-->
					<div id="kt_header" class="header header-fixed">
						<!--begin::Container-->
						<div class="container-fluid d-flex align-items-stretch justify-content-between">
							<!--begin::Header Menu Wrapper-->
							<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
								<!--begin::Header Menu-->
								<div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
									<?=$breadcrumb?>
								</div>
								<!--end::Header Menu-->
							</div>
							<!--end::Header Menu Wrapper-->
							<!--begin::Topbar-->
							<div class="topbar">
								<!--begin::User-->
								<div class="topbar-item">
									<div class="d-flex align-items-center">
										<div class="d-flex align-items-right" id=combo_place style="padding-right:10px"></div>
										<div class="d-flex align-items-center" id=button_place style="padding-right:10px"></div>
									</div>
								</div>
								<div class="topbar-item">
									<div class="btn btn-icon btn-clean btn-lg mr-1">
										<span class="symbol symbol-lg-35 symbol-25 symbol-light-primary" aria-label="Baca Pesan" data-microtip-position="bottom-left" role="tooltip">
											<a href="/inbox"><i class="fas fa-envelope" style="color:#3699FF"></i></a>
											<div><i id="icoInbox" class="symbol-badge bg-danger"></i></div>
										</span>
									</div>
									<div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
										<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
											<i class="fas fa-user" style="color:#20c997"></i>
										</span>
									</div>
								</div>
								<!--end::User-->
							</div>
							<!--end::Topbar-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Header-->
					<!--begin::Content-->
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
						<!--begin::Entry-->
						<div class="d-flex flex-column-fluid">
							<!--begin::Container-->
							<div class="container">
								<!--begin::Dashboard-->
								<!--begin::Row-->
								<div class="row" style="height:100%;width:100%">
									<div class="card card-custom gutter-b" style="height:100%;width:100%">
										<div class="card-body" style="padding-top:5px;padding-right:10px;padding-left:10px;padding-bottom:0px">
											<?=$content?>
										</div>
									</div>
								</div>
								<!--end::Row-->
							</div>
							<!--end::Container-->
						</div>
						<!--end::Entry-->
					</div>
					<!--end::Content-->
					<!--begin::Footer-->

					<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer" style="height:50px">
						<!--begin::Container-->
						<div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
							<!--begin::Copyright-->
							<div class="text-dark order-2 order-md-1 footer_font">
								<?=$usr_fnames . " [ " . $desc . " ]" ?>
							</div>
							<!--end::Copyright-->
							<!--begin::Nav-->
							<div class="nav nav-dark footer_font">
								<span id="sesiabis" style="padding-right: 10px;color:#20c997">
									<?=$this->lang->line("sessi_berakhir")?>
								</span>
								<span class="username" style="color:#ffba00;padding-right: 10px" id="clock"></span>
								<?=$show_envrm==true ? "[ " . ENVIRONMENT ." ]" : ""?>
								2021&nbsp;&copy;&nbsp;
								<a target="_blank" href="<?=base_url()?>" class="kt-link"><?=$app_names_short?></a>&nbsp;&nbsp;
								Version 1
							</div>
							<!--end::Nav-->
						</div>
						<!--end::Container-->
					</div>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>

		<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
			<!--begin::Header-->
			<div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
				<h3 class="font-weight-bold m-0"><?=$usr_fnames?></h3>
				<a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
					<i class="ki ki-close icon-xs text-muted"></i>
				</a>
			</div>
			<!--end::Header-->
			<!--begin::Content-->
			<div class="offcanvas-content pr-5 mr-n5">
				<!--begin::Header-->
				<!--end::Header-->
				<!--begin::Separator-->
				<div class="separator separator-dashed mt-8 mb-5"></div>
				<!--end::Separator-->
				<!--begin::Nav-->
				<div class="navi navi-spacer-x-0 p-0">
					<!--begin::Item-->
					<a href="/master/user/viewdetail" class="navi-item">
						<div class="navi-link">
							<div class="symbol symbol-40 bg-light mr-3">
								<div class="symbol-label">
									<span class="svg-icon svg-icon-md svg-icon-success">
										<i class="fas fa-user" style="color:#00bbff"></i>
									</span>
								</div>
							</div>
							<div class="navi-text">
								<div class="font-weight-bold"><?=$this->lang->line("profil")?></div>
								<div class="text-muted"><?=$this->lang->line("info_profil")?></div>
							</div>
						</div>
					</a>
					<!--end:Item-->
					<!--begin::Item-->
					<a href="/master/user/ubahpassword" class="navi-item">
						<div class="navi-link">
							<div class="symbol symbol-40 bg-light mr-3">
								<div class="symbol-label">
									<span class="svg-icon svg-icon-md svg-icon-warning">
									<i class="fas fa-key" style="color:#20c997"></i>
									</span>
								</div>
							</div>
							<div class="navi-text">
								<div class="font-weight-bold"><?=$this->lang->line("ubah_profil")?></div>
								<div class="text-muted"><?=$this->lang->line("info_ubah_profile")?></div>
							</div>
						</div>
					</a>
					<!--end:Item-->
					<!--begin::Item-->
					<a href="/master/log/aktivitas" class="navi-item">
						<div class="navi-link">
							<div class="symbol symbol-40 bg-light mr-3">
								<div class="symbol-label">
									<span class="svg-icon svg-icon-md svg-icon-danger">
										<i class="fas fa-file-alt" style="color:#ff0019"></i>
									</span>
								</div>
							</div>
							<div class="navi-text">
								<div class="font-weight-bold"><?=$this->lang->line("aktivitas")?></div>
								<div class="text-muted"><?=$this->lang->line("info_aktivitas")?></div>
							</div>
						</div>
					</a>
					<!--end:Item-->
				</div>
				<div class="d-flex align-items-center mt-5">
					<div class="d-flex flex-column">
						<div class="navi mt-2">
							<a href="<?php echo base_url(); ?>login/bye" class="btn btn-sm btn-light-primary font-weight-bolder py-2 px-5">Sign Out</a>
						</div>
					</div>
				</div>

				<!--end::Nav-->
				<!--begin::Separator-->
				<div class="separator separator-dashed my-7"></div>
				<!--end::Separator-->
				<!--begin::Notifications-->
				<div>
					<!--begin:Heading-->
					<h5 class="mb-5"><?=$this->lang->line("aktivitas_terakhir")?></h5>
					<!--end:Heading-->
					<!--begin::Item-->
					<div id="latestactivity">
					<?=$latestactivity?>
					</div>
				</div>
				<!--end::Notifications-->
			</div>
			<!--end::Content-->
		</div>

		<div id="kt_scrolltop" class="scrolltop">
			<span class="svg-icon">
				<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Up-2.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1" />
						<path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
					</g>
				</svg>
				<!--end::Svg Icon-->
			</span>
		</div>		
		<div class="modal fade" id="wLogin" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><b><?=$this->config->item('app_names');?> :: <?=$this->lang->line("sessi_habis")?>!</b></h4>
					</div>
					<div class="modal-body">
						<div class='form-group row'>
							<label for="txtLOGINS" id="lblLOGINS" class="col-sm-12 col-lg-2 col-form-label"><?=$this->lang->line("Pengguna")?></label>
							<div class='col-md-10'>
								<input class="form-control form-control-solid placeholder-no-fix form-group" style='width:200px' type="text" autocomplete="off" placeholder="Username" name="xusername" id="xusername" required value="<?=$usr_logins?>" readonly/>
							</div>
						</div>
						<div class='form-group row'>
							<label for="txtLOGINS" id="lblLOGINS" class="col-sm-12 col-lg-2 col-form-label"><?=$this->lang->line("Password")?></label>
							<div class='col-md-10'>
								<input class="form-control form-control-solid placeholder-no-fix form-group" autocomplete="off" style='width:200px' type="password" autocomplete="off" placeholder="Password" name="xpassword" id="xpassword" value=""  onfocus="if (this.hasAttribute('readonly')) { this.removeAttribute('readonly');this.blur();    this.focus();  }" required/> 
							</div>
						</div>
						<?=$this->lang->line("sessi_end_descre")?>
					</div>
					<div class="modal-footer">					
						<a class="btn btn-danger font-weight-bold" href="<?php echo base_url(); ?>login/bye"><i class="fas fa-sign-out-alt"></i>Sign Out</a>
						<button type="button" class="btn btn-pengajuan font-weight-bold" onclick="jvLogin()"><i class="fas fa-key"></i>Sign In</button>
					</div>
				</div>
			</div>
		</div>
		<div id='windowProses'>
			<div id="headerProses">
				<span id="captureContainer" style="float: left">Processing..</span>
			</div>
			<div id="content" style='overflow: hidden'>
				<img id=imgPROSES src="<?=base_url(IMAGES."process.gif")?>" style='display:none;width:400px;height: 300px'>
				<span id="process_text" style="display:table;margin:0 auto;font-weight:bold"></span>
			</div>
		</div>
		<div id='windowProsesLog'>
			<div id="headerProses">
				<span id="captureContainer" style="float: left">Processing..</span>
			</div>
			<div id="content" style='overflow: auto'>
				<img id=imgPROSES src="<?=base_url(IMAGES."process.gif")?>" style='display:none;width:400px;height: 300px'>
			</div>
		</div>
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."misc/jquery.countdown.min.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."bootstrap/js/bootstrap.min.js");?>"></script>
		<?=$basejs?>

		<script>
			$(document).ready(function(){
                $('#icoInbox').hide();
				checkinbox();
				$('#windowProses').jqxWindow({isModal: true, autoOpen: false, height: '320px', width:'410px', animationType:'none', maxWidth: '900', zIndex:'99999'});
				$('#windowProsesLog').jqxWindow({isModal: true, autoOpen: false, height: '500px', width:'410px', animationType:'none', zIndex:'99999'});
				// $('#combo_place').html('<select class="form-control selectpicker"><option>Mustard</option><option>Ketchup</option><option><b>Relish</b></option></select>')
				// $('#button_place').html('<a href="#" class="btn btn-light-primary font-weight-bolder btn-sm">Button</a>&nbsp;<a href="#" class="btn btn-light-primary font-weight-bolder btn-sm">Actions</a>')
				var timeout = setInterval(reloadChat, 200000);    

			});
			function reloadChat () {
				$.post('/welcome/getUserActivitylast_list',null,function(data){
					$('#latestactivity').empty();
					$('#latestactivity').html(data);
				});
				checkinbox();
			}
			function checkinbox(){
				$.post('/welcome/getInboxunread',null,function(data){
					if(parseInt(data)!=0){
						$('#icoInbox').show();
					}
				});
			}
			function jvViewLog(idents){
				$('#windowProsesLog').jqxWindow('open');
				var param = {};
				param['IDENTS'] = idents;
				var tinggilayar = $(window).height();
				var lebarlayar = $(window).width()-100;
				tinggi = 600;
				lebarlayar = 800;
				$.post('/master/userlog/viewlog',param,function(data){
					$('#windowProsesLog').jqxWindow({autoOpen: false,width:lebarlayar, height:tinggi,position:'center, left', resizable:false,title: 'Info Detail'});
					$('#windowProsesLog').jqxWindow('setContent', data);
				});
				$('#windowProsesLog').jqxWindow('focus');
			}
		</script>
		<style>
			.select2-selection__rendered {
				line-height: 31px !important;
			}
			.select2-container .select2-selection--single {
				height: 35px !important;
			}
			.select2-selection__arrow {
				height: 34px !important;
			}			
			table.dataTable th {
				text-align:center;
			}
			@media only screen and (min-width: 768px) and (max-width: 1024px) {
				table.dataTable td {
					font-size: 11px !important;
					padding:5px !important;
				}
				table.dataTable th {
					font-size: 10px !important;
				}
				.footer_font{
					font-size: 11px !important;
				}
			}
			body.modal-open {
				overflow: hidden !important;
				/* position: fixed; */
			}

			.btn-pengajuan {
				color: #ffffff;
				background-color: #00c6ff;
				border-color: #00c6ff;
				-webkit-box-shadow: none;
				box-shadow: none; 
			}
			.btn-pengajuan:hover {
					color: #ffffff;
					background-color: #1086ff;
					border-color: #037fff; 
			}
			.btn-pengajuan:focus, .btn-pengajuan.focus {
				color: #ffffff;
				background-color: #1086ff;
				border-color: #037fff;
				-webkit-box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5);
				box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5); 
			}
			.btn-pengajuan.disabled, .btn-pengajuan:disabled {
				color: #ffffff;
				background-color: #3699FF;
				border-color: #3699FF; 
			}
			.btn-pengajuan:not(:disabled):not(.disabled):active, .btn-pengajuan:not(:disabled):not(.disabled).active,
			.show > .btn-pengajuan.dropdown-toggle {
				color: #ffffff;
				background-color: #037fff;
				border-color: #0079f5; 
			}
			.btn-pengajuan:not(:disabled):not(.disabled):active:focus, .btn-pengajuan:not(:disabled):not(.disabled).active:focus,
				.show > .btn-pengajuan.dropdown-toggle:focus {
				-webkit-box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5);
				box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5); 
			}

			.btn-filled {
				color: #ffffff;
				background-color: #0f8ce6;
				border-color: #0f8ce6;
				-webkit-box-shadow: none;
				box-shadow: none; 
			}
			.btn-filled:hover {
					color: #ffffff;
					background-color: #1086ff;
					border-color: #037fff; 
			}
			.btn-filled:focus, .btn-filled.focus {
				color: #ffffff;
				background-color: #1086ff;
				border-color: #037fff;
				-webkit-box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5);
				box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5); 
			}
			.btn-filled.disabled, .btn-filled:disabled {
				color: #ffffff;
				background-color: #3699FF;
				border-color: #3699FF; 
			}
			.btn-filled:not(:disabled):not(.disabled):active, .btn-filled:not(:disabled):not(.disabled).active,
			.show > .btn-filled.dropdown-toggle {
				color: #ffffff;
				background-color: #037fff;
				border-color: #0079f5; 
			}
			.btn-filled:not(:disabled):not(.disabled):active:focus, .btn-filled:not(:disabled):not(.disabled).active:focus,
				.show > .btn-filled.dropdown-toggle:focus {
				-webkit-box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5);
				box-shadow: 0 0 0 0.2rem rgba(84, 168, 255, 0.5); 
			}

			.btn-approved {
				color: #ffffff;
				background-color: #117a65;
				border-color: #117a65;
				-webkit-box-shadow: none;
				box-shadow: none; 
			}
			.btn-approved:hover {
				color: #ffffff;
				background-color: #16a39d;
				border-color: #159892; 
			}
			.btn-approved:focus, .btn-approved.focus {
				color: #ffffff;
				background-color: #16a39d;
				border-color: #159892;
				-webkit-box-shadow: 0 0 0 0.2rem rgba(61, 206, 199, 0.5);
				box-shadow: 0 0 0 0.2rem rgba(61, 206, 199, 0.5); 
			}
			.btn-approved.disabled, .btn-approved:disabled {
				color: #ffffff;
				background-color: #1BC5BD;
				border-color: #1BC5BD; 
			}
			.btn-approved:not(:disabled):not(.disabled):active, .btn-approved:not(:disabled):not(.disabled).active,
			.show > .btn-approved.dropdown-toggle {
				color: #ffffff;
				background-color: #159892;
				border-color: #138d87; }
				.btn-approved:not(:disabled):not(.disabled):active:focus, .btn-approved:not(:disabled):not(.disabled).active:focus,
				.show > .btn-approved.dropdown-toggle:focus {
				-webkit-box-shadow: 0 0 0 0.2rem rgba(61, 206, 199, 0.5);
				box-shadow: 0 0 0 0.2rem rgba(61, 206, 199, 0.5); 
			}			
		</style>
	</body>	
	<!--end::Body-->
</html>
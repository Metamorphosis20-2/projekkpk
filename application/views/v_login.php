<?php
$owner = $this->config->item('ownerapp');
$logo = $this->config->item('logo');
$app_names = $this->config->item('app_names');
$metacontent = $this->config->item('metacontent');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>

		<!-- Meta data -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta content="<?=$app_names?>" name="description">
		<meta content="<?=$owner?>" name="author">
		<meta name="keywords" content="<?=$metacontent?>">

		<!-- Title -->
		<title><?=$owner?> | Login Page</title>

        <!--Favicon -->
		<link rel="shortcut icon" href="<?=base_url(IMAGES."favicon/favicon.ico");?>" />
<!--Bootstrap css -->
<!-- Style css -->
<link href="<?=base_url(PLUGINS."bootstrap/css/bootstrap.min.css");?>" rel="stylesheet" type="text/css"/>
<link href="<?=base_url(PLUGINS."login/login02/style.css");?>" rel="stylesheet" type="text/css"/>
<link href="<?=base_url(PLUGINS."login/login02/dark.css");?>" rel="stylesheet" type="text/css"/>
<link href="<?=base_url(PLUGINS."login/login02/skin-modes.css");?>" rel="stylesheet" type="text/css"/>
<!-- Animate css -->
<link href="<?=base_url(PLUGINS."login/login02/animated.css");?>" rel="stylesheet" type="text/css"/>
<!---Icons css-->
<link rel='stylesheet' href='<?=base_url(PLUGINS."font-awesome/css/fontawesome.min.css");?>' >
<link rel='stylesheet' href='<?=base_url(PLUGINS."font-awesome/css/solid.css");?>' >
    </head>

	<body class="error-bg h-100vh">

		
            <div class="register-2">

        
			<!-- Loader -->
			<div id="global-loader">
				<img src="<?=base_url(IMAGES."/loader/loader.svg");?>" class="loader-img" alt="Loader">
			</div>
			<!-- /Loader -->

			<form class="signin-form" method="POST" action="<?=base_url();?>login/validate_credentials">
				<div class="page">
					<div class="page-content">
						<div class="container">
							<div class="row">
								<div class="col mx-auto">
									<div class="row justify-content-center">
										<div class="col-md-4">
											<div class="card">
												<div class="card-body">
												<div class="text-center mb-5">
												<img src="<?=base_url(IMAGES.$logo);?>" class="header-brand-img desktop-lgo" alt="Azea logo">
											</div>
													<form class="mt-5">
														<div class="input-group mb-4">
																<div class="input-group-text">
																	<i class="fas fa-user"></i>
																</div>
																<input type="text" class="form-control form-control-user" tabindex=1 placeholder="Username" name="username" value="" required>
														</div>
														<div class="input-group mb-4">
															<div class="input-group" id="Password-toggle1">
																<a href="" class="input-group-text">
																<i class="fa fa-eye" aria-hidden="true"></i>
																</a>
																<input type="password" class="form-control form-control-user" tabindex=2 id="user-password" placeholder="Password" name="password" required>
															</div>
														</div>
														<div class="form-group text-center mb-3">
	            											<button type="submit" class="btn btn-primary btn-lg w-100 br-7">Sign In</button>
														</div>
														<div class="form-group">
															<label class="custom-control custom-checkbox">
															<?=isset($messg) ? "<font style='font-weight:bold;color:#ff0000!important'>".$messg."</font>" : "" ?>
															</label>
														</div>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
					
		</div>
		<script type="text/javascript" src="<?=base_url(JS."jquery.min.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."bootstrap/js/popper.min.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."bootstrap/js/bootstrap.min.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(PLUGINS."bootstrap/js/bootstrap-show-password.min.js");?>"></script>
		<script type="text/javascript" src="<?=base_url(JS."custom_azea.js");?>"></script>
    </body>
</html>



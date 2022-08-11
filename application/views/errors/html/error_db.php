<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$ci = get_instance(); // CI_Loader instance
$owner = $ci->config->item('ownerapp');
$logo = $ci->config->item('logo');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title><?=$owner?> Database Error</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Maven+Pro:400,900" rel="stylesheet">

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="/resources/css/style404.css" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>

	<div id="notfound">
		<div class="notfound">
			<img src="<?=base_url(IMAGES.$logo);?>" alt="logo" class="logo-default" style="height: 120px;"></a>
			<div class="notfound-404">
				<h1>Error</h1>
			</div>

			<h2><?php echo $heading; ?></h2>
			<?php echo $message; ?>
			<hr>
			<p>Mohon Maaf, Ada Kesalahan Database!</p>
		</div>
	</div>

</body>

</html>

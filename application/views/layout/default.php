<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title><?php echo $title; ?></title>
	<meta name="description" content="{{ page.description }}">
	<meta name="author" content="{{ page.author }}">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">

	<link rel="stylesheet" href="/media/css/style.css">
	<?php echo $_styles; ?>

	<script>!window.jQuery && document.write(unescape('%3Cscript src="/media/js/jquery.min.js"%3E%3C/script%3E'))</script>
	<?php echo $_scripts; ?>

</head>
<body>
<div id="wrapper" class="container-fluid">

	<ul id="userbar">
		<li><?php echo anchor('app/logout', 'logout'); ?></li>
	</ul>

	<div id="menuback"></div>

	<div id="menuwrap" class="sidebar">
		<div id="header" role="header">
			<?php echo $header; ?>
		</div>
		<div class="navigator"><?php echo $navigator; ?></div>

		<div class="copy-right">
			<?php echo $footer; ?>
		</div>
	</div>

	<div class="content">
		<div id="main" role="main">
            
			<?php echo $content; ?>
		</div>
	</div>

</div>

</body>
</html>
<?php 	
	include(dirname(__FILE__).'/LandiniPlugin.php');

	$info = json_decode(file_get_contents('plugin-info.json'),true);

	if (empty($info)) die("EROOR!: Info plugin not found");
	
	$MyPlugin = new LandiniPlugin(array(
		'title' => $info['title'],
		'name' => $info['name'],
		'logo' => $info['icon']
	));

	if(!defined('PREPEND_PATH')) define('PREPEND_PATH', '../../');
	#########################################################

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

	<head>
		<meta charset="<?php echo datalist_db_encoding; ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title><?php echo $MyPlugin->name; ?></title>
		
		<link id="browser_favicon" rel="shortcut icon" href="L.png">

		<?php echo $MyPlugin->get_theme_css_links(); ?>
		
		<link rel="stylesheet" href="../../dynamic.css.php">

		<!-- jquery ui -->
		<link rel="stylesheet" href="../plugins-resources/jquery-ui/jquery-ui.min.css">
		<link rel="stylesheet" href="docs.css">

		<!--[if lt IE 9]>
			<script src="../../resources/initializr/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<![endif]-->
		<script src="../../resources/jquery/js/jquery-1.12.4.min.js"></script>

		<!-- jquery ui -->
		<script src="../plugins-resources/jquery-ui/jquery-ui.min.js"></script>

		<script>var $j = jQuery.noConflict();</script>
		<script src="../../resources/initializr/js/vendor/bootstrap.min.js"></script>	
		<script src="../plugins-resources/plugins-common.js"></script>

		<script>
			$j(function(){
				// disable rtl.css, if it exists ...
				$j('link[href$="rtl.css"]').remove();
			})
		</script>
	</head>
	<body>
		<div class="container">
		
			<!-- process notifications -->
			<div style="height: 60px; margin: -15px 0 -45px;">
				<?php if(function_exists('showNotifications')) echo showNotifications(); ?>
			</div>

<?php

	/* grant access to the groups 'Admins' only */
	if (!$MyPlugin->is_admin() ){
		echo "<br>".$MyPlugin->error_message('Access denied.<br>Please, <a href=\'../../index.php?signIn=1\' >Log in</a> as administrator to access this page.' , false);
		exit;
	}
				
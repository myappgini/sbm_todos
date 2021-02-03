<?php include(dirname(__FILE__) . '/header.php'); ?>

<div class="bs-docs-section row">
	<h1 class="page-header">
        <img src="<?php echo $MyPlugin->logo ?>" style="height: 1em;"> 
        <?php echo $MyPlugin->title; ?>
    </h1>
    <p class="lead">
		<a href="index.php">Home</a> &gt; 
		Output folder
	</p>
</div>

<?php
	echo $MyPlugin->show_select_output_folder(array(
		'next_page' => '03_generate.php?axp=' . urlencode($_REQUEST['folderName']),
		'extra_options' => array(
			'dont_write_to_hooks' => 'Only show me the hooks code without actually writing it to existing hook files.'
		)
	));
?>

<?php include(dirname(__FILE__) . "/footer.php"); ?>
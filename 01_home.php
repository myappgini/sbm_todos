<?php
$folderProject = dirname(__FILE__, 2) . '/hooks'; ?>
<style>
	.item{
		cursor:pointer;
	}
</style>


<div class="bs-docs-section">
    <h1 class="page-header">
        <img src="<?php echo $MyPlugin->logo ?>" style="height: 1em;"> 
        <?php echo $MyPlugin->title; ?>
    </h1>
    <p class="lead">
		<a href="./index.php">Home</a> >
        <a href="02_output.php?folderName=<?php echo $folderProject; ?>" class="pull-right btn btn-success btn-lg col-md-3 col-xs-12">
            <span class="glyphicon glyphicon-play"></span>  
            Enable <?php echo $MyPlugin->name; ?>
        </a>
	</p>
</div>

<div class="row">
	<?php  ?>
    <div id="coment" class="col-md-9 col-xs-12">
        <div class="bs-callout bs-callout-info"> 
            <h4>Welcome to <?php echo $MyPlugin->title; ?></h4> 
            
            <p>Thank you for choosing this plugin for your project, please click on the <strong><?php echo $MyPlugin->name; ?></strong> enable button to continue.
               <br>
               The assistant will guide you easily to install the plugin.
            </p> 
        </div>
        <div class="bs-callout bs-callout-danger"> 
            <h4>Considerations for its proper functioning</h4> 
            <p>The plugin works with version <span class="badge"><?php echo $info['product_use']; ?></span> of <strong>AppGini</strong>.<br>
                You also need to have acquired some other official <strong>AppGini</strong> plugin.<br>
                Verify that the <code>projects</code> folder is inside the <code>plugin</code> folder. 
            </p> 
        </div>
    </div>
</div>
<div class="row">
    <?php  
        echo file_get_contents(dirname(__FILE__) . "/video-link.html")
    ?>
</div>



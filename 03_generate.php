<?php include(dirname(__FILE__) . '/header.php');

try {
    if (!isset($_POST['path'])) {
        throw new RuntimeException('This page has expired');
    }

    $path = rtrim(trim($_POST['path']), '\\/');
    if (!is_dir($path)) {
        throw new RuntimeException('Invalid path');
    }

    if (
        !(
            file_exists("$path/lib.php") &&
            file_exists("$path/db.php") &&
            file_exists("$path/index.php")
        )
    ) {
        throw new RuntimeException(
            'The given path is not a valid AppGini project path'
        );
    }

    if (!is_writable($path . '/hooks')) {
        throw new RuntimeException(
            'The hooks folder of the given path is not writable'
        );
    }

    if (!is_writable($path . '/resources')) {
        throw new RuntimeException(
            'The resources folder of the given path is not writable'
        );
    }
} catch (RuntimeException $e) {
    echo '<br>' . $MyPlugin->error_message($e->getMessage());
    exit();
}
//-------------------------------------------------------------------------------------

$write_to_hooks = $_REQUEST['dont_write_to_hooks'] == 1 ? false : true;
?>

<div class="bs-docs-section row">
    <h1 class="page-header">
        <img src="<?php echo $MyPlugin->logo ?>" style="height: 1em;"> 
        <?php echo $MyPlugin->title; ?>
    </h1>
	<p class="lead">
		<a href="./index.php">Home</a> > 
		<a href="./02_output.php">  Select output folder</a> > Coping Files MPI
	</p>
</div>

<h4>Progress log</h4>

<?php
$MyPlugin->progress_log->add("Output folder: $path", 'text-info');

//coping resources folders

$MyPlugin->progress_log->ok();
$MyPlugin->progress_log->line();

//copy rouserse folder ------------------------------------------------------
$source = dirname(__FILE__) . '/app-resources';
$dest = $path . '/hooks';
$MyPlugin->recurse_copy($source, $dest, true);

//add code to hedear-extras.php ------------------------------------------------------

$sql = file_get_contents(
    dirname(__FILE__) . '/app-resources/todos/sql.sql'
);
if ($sql) {
    $eo = ['silentErrors' => true];
    $res = sql($sql, $eo);
    if ($eo['error'] != '') {
        $MyPlugin->progress_log->add(
            'ERROR: Audit table not created',
            'text-danger spacer'
        );
    } else {
        $MyPlugin->progress_log->add('Audit table created');
    }
}

$MyPlugin->progress_log->line();
$code ="<?php include('hooks/todos/scripts.php');?>";
$file_path = $path . '/hooks/footer-extras.php';
$res = $MyPlugin->add_to_file($file_path, false, $code);

inspect_result($res, $file_path, $MyPlugin);

$MyPlugin->progress_log->line();
$code ="<?php include('hooks/box/scripts.php');?>";
$file_path = $path . '/hooks/footer-extras.php';
$res = $MyPlugin->add_to_file($file_path, false, $code);

inspect_result($res, $file_path, $MyPlugin);

echo $MyPlugin->progress_log->show();

?>

<center>
	<a style="margin:20px;" href="index.php" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-home" ></span>   Start page</a>
</center>

<script>	
	$j( function(){

		$j("#progress").height( $j(window).height() - $j("#progress").offset().top - $j(".btn-success").height() - 100 );

		//add resize event
		$j( window ).resize(function() {
		   $j("#progress").height( $j(window).height() - $j("#progress").offset().top - $j(".btn-success").height() - 100 );
		});

	});
</script>

<?php
include dirname(__FILE__) . '/footer.php';

function inspect_result($res, $file_path, &$MyPlugin)
{
    if ($res) {
        $MyPlugin->progress_log->add(
            "Installed code into '{$file_path}'.",
            'text-success spacer'
        );
    } else {
        $error = $MyPlugin->last_error();

        if ($error == 'Code already exists') {
            $MyPlugin->progress_log->add(
                "Skipped installing to '{$file_path}', code is already installed.",
                'text-warning spacer'
            );
        } else {
            $MyPlugin->progress_log->add(
                "Failed to install code '{$file_path}': {$error}",
                'text-danger spacer'
            );
        }
    }
}


?>

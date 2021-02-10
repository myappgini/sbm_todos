<?php
if (!function_exists('getMemberInfo')) {
    $curr_dir = dirname(__FILE__);
    include("{$curr_dir}/../../lib.php");
}

$mi = getMemberInfo();
$admin_config = config('adminConfig');
$guest = $admin_config['anonymousMember'];

if($guest == $mi['username']) die();
?>

<link rel="stylesheet" href="hooks/todos/css/TodoList.css">
<script src="hooks/todos/js/jquery-ui.min.js"></script>
<script src="hooks/todos/js/TodoList.js"></script>
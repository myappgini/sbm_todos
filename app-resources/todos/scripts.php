<?php
if (!function_exists('getMemberInfo')) {
    include(dirname(__FILE__)."/../../lib.php");
}

$mi = getMemberInfo();
$admin_config = config('adminConfig');
$guest = $admin_config['anonymousMember'];

if ($guest == $mi['username']) {
    die();
}
?>

<link rel="stylesheet" href="hooks/todos/css/TodoList.css">
<script src="hooks/todos/js/jquery-ui.min.js"></script>
<script src="hooks/todos/js/TodoList.js"></script>
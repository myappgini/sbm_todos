<?php
define('PREPEND_PATH', '../../');
$dir = dirname(__FILE__);
include_once "{$dir}/../../lib.php";
include_once "{$dir}/../../header.php";


include('templates/todo.hbs');


?>
<link rel="stylesheet" href="../box/css/box.css">
<link rel="stylesheet" href="css/TodoList.css">
<script src="js/jquery-ui.min.js"></script>
<script src="../box/js/add_card.js"></script>
<script src="../box/js/box.js"></script>
<script src="js/TodoList.js"></script>
<?php
include_once "{$dir}/../../footer.php";

<?php
if (!function_exists('getMemberInfo')) {
    include '../../lib.php';
}
/*
    REQUEST parameters:
    ===============
    p: page number (default = 1)
    s: search term
    return json */
    header('Content-type: application/json');

    $start_ts = microtime(true);

    // how many results to return per call, in case of json output
    $results_per_page = 50;

    $search_term = false;
    if (isset($_REQUEST['s'])) {
        $search_term = from_utf8($_REQUEST['s']);
    }

    $page = intval($_REQUEST['p']);
    if ($page < 1) {
        $page = 1;
    }
    $skip = $results_per_page * ($page - 1);

    $prepared_data = [];
    $sql = "SELECT u.memberID, g.name FROM membership_users u LEFT JOIN membership_groups g ON u.groupID = g.groupID WHERE g.name != 'anonymous' LIMIT {$skip}, {$results_per_page}";
    $res = sql($sql, $eo);
    while ($row = db_fetch_row($res)) {
        $prepared_data[] = array('id' => to_utf8($row[0]), 'text' => to_utf8("<b>{$row[1]}</b>/{$row[0]}"));
    }

    echo json_encode(array(
        'results' => $prepared_data,
        'more' => (@db_num_rows($res) >= $results_per_page),
        'elapsed' => round(microtime(true) - $start_ts, 3)
    ));

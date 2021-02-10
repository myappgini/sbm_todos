<?php
if (!function_exists('getMemberInfo')) {
    include '../../lib.php';
}
include 'landini_commons/landini_functions.php';
include 'handlebars.php';

$cmd = Request::val('cmd', false);
if (!$cmd) {
    die('bad command');
}

$data_selector = [
    'tn' => 'landini_todo',
    'fn' => 'todos',
    'ix' => Request::val('ix', false),
    'mi' => getMemberInfo(Request::val('mi', false)),
    'id' => getLoggedMemberID(),
    'tk' => Request::val('task', false),
    'nt' => Request::val('newtext', false),
    'ok' => Request::val('complete', false),
];

header('Content-Type: application/json; charset=utf-8');

if ($cmd) {
    $tasks = get_data($data_selector);
    switch ($cmd) {
        case 'option-todo':
            $html = $handlebars->render('dropdown_menu', []);
            echo $html;
            break;
        case 'get-todo':
            $tasks['list_delete'] = false;
            $html = $handlebars->render('todo', $tasks);
            echo $html;
            break;
        case 'get-deleted':
            $tasks['list_delete'] = true;
            $html = $handlebars->render('todo', $tasks);
            echo $html;
            break;
        case 'removed-deleted':
            unset($tasks['deleted_tasks']);
            $res = update_data($data_selector, $tasks);
            $tasks['list_delete'] = false;
            echo $handlebars->render('todo', $tasks);
            break;
        case 'remove-task':
            unset($tasks['deleted_tasks'][$data_selector['ix']]);
            $res = update_data($data_selector, $tasks);
            echo 'removed: '. $res;
            break;
        case 'delete-task':
            $uid = uniqid();
            $tasks['tasks'][$data_selector['ix']]['deleted']=true;
            $tasks['tasks'][$data_selector['ix']]['date_deleted']=date('d.m.y h:m:s');
            $tasks['deleted_tasks'][$uid]=$tasks['tasks'][$data_selector['ix']];
            $tasks['deleted_tasks'][$uid]['uid']=$uid;
            unset($tasks['tasks'][$data_selector['ix']]);
            $res = update_data($data_selector, $tasks);
            echo 'deleted: '. $res;
            break;
        case 'recover-task':
            $uid = uniqid();
            $tasks['deleted_tasks'][$data_selector['ix']]['deleted']=false;
            $tasks['deleted_tasks'][$data_selector['ix']]['recovered_deleted']=date('d.m.y h:m:s');

            $tasks['tasks'][$uid]=$tasks['deleted_tasks'][$data_selector['ix']];
            $tasks['tasks'][$uid]['uid']=$uid;
            unset($tasks['deleted_tasks'][$data_selector['ix']]);
            $res = update_data($data_selector, $tasks);
            echo 'recovered: '. $res;
            break;
        case 'edit-task':
            if (!$data_selector['nt']) {
                echo "{error:'something wrong in edit task'}";
                break;
            }
            $tasks['tasks'][$data_selector['ix']]['task']=$data_selector['nt'];
            $tasks['tasks'][$data_selector['ix']]['edited'][]=$data_selector['nt'];
            $res = update_data($data_selector, $tasks);
            echo 'edited: '. $res;
            break;
        case 'check-task':
            $ok = $data_selector['ok'] === "true" ? true : false;
            $tasks['tasks'][$data_selector['ix']]['complete']=$ok;
            $res = update_data($data_selector, $tasks);
            echo 'edited: '. $res;
            break;
        case 'get-values':
            $res['length']= is_null($tasks['length'])?0:$tasks['length'];
            $res['deleted']=is_null($tasks['deleted'])?0:$tasks['deleted'];
            $res['listed']=is_null($tasks['listed'])?0:$tasks['listed'];
            $res['completed']=is_null($tasks['completed'])?0:$tasks['completed'];
            echo json_encode($res);
            break;
        case 'add-task':
            if (!$data_selector['tk']) {
                echo "{error:'something wrong'}";
                break;
            }
            $task = add_data($data_selector);
            $html = $handlebars->render('task', $task);
            echo $html;
            break;
        default:
            echo "{error:'something wrong!!!'}";
            break;
    }
    return;
}

function get_data(&$data)
{
    $res = getDataTable($data, true);
    return json_decode($res['todos'], true);
}
function add_data(&$data)
{
    $tasks = get_data($data);
    $uid = uniqid();
    $task = [
        'task' => $data['tk'],
        'complete' => false,
        'added' => date('d.m.y h:m:s'),
        'due' => false,
        'edited' => [$data['tk']],
        'deleted' => false,
        'date_deleted' => false,
        'uid' => $uid,
    ];
    $tasks['tasks'][$uid] = $task;

    $res = update_data($data, $tasks);

    return $task;
}

function update_data(&$data, $set)
{
    $where = whereConstruct($data);
    $eo = ['silentErrors' => true];
    //check if member exist
    $count = sqlValue("SELECT COUNT( * ) FROM `{$data['tn']}` WHERE {$where};");
    if ($count < 1) {
        //add member if not exist
        $res = sql(
            "INSERT INTO `{$data['tn']}`(`memberID`) VALUES ('{$data['id']}')",
            $eo
        );
        $errors[] = $eo;
    }
    $del = count($set['deleted_tasks']);
    $completed = array_value_recursive_count('complete', true, $set['tasks']);
    $elements=count($set['tasks']);
    $set['length']=$elements + $del;
    $set['deleted']=$del;
    $set['listed']=$elements;
    $set['completed']=$completed;
    $set = "`{$data['fn']}`='" . json_encode($set) . "'";
    $sql = "UPDATE `{$data['tn']}` SET {$set} WHERE {$where}";
    $res = sql($sql, $eo);
    $errors[] = $eo;

    $data['errors'] = $errors;

    return $res;
}
function array_value_recursive_count($key, $value, array $arr)
{
    $val = array();
    array_walk_recursive($arr, function ($v, $k) use ($key, &$val, $value) {
        if ($k === $key && $v === $value) {
            array_push($val, $v);
        }
    });
    return count($val) >= 1 ? count($val) : 0;
}

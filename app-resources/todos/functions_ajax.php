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

$data = [
    'tn' => 'landini_todo',
    'fn' => 'todos',
    'ix' => Request::val('ix', false),
    'mi' => getMemberInfo(Request::val('mi', false)),
    'id' => getLoggedMemberID(),
    'tk' => Request::val('task', false),
    'nt' => Request::val('newtext', false),
    'ok' => Request::val('complete', false),
    'us' => Request::val('user', false), //user to send task
    'pr' => Request::val('preserve', false)=== "true" ? true : false, //preserve task in my list
    'du' => Request::val('due', false), //due task
];

header('Content-Type: application/json; charset=utf-8');

if ($cmd) {
    $tasks = get_data($data);
    switch ($cmd) {
        case 'option-todo':
            $html = $handlebars->render('dropdown_menu', []);
            echo $html;
            break;
        case 'removed-deleted':
            unset($tasks['deleted_tasks']);
            $res = update_data($data, $tasks);
            // no break
        case 'get-todo':
            $tasks['list_delete'] = false;
            $html = $handlebars->render('todos', $tasks);
            echo $html;
            break;
        case 'get-deleted':
            $tasks['list_delete'] = true;
            $html = $handlebars->render('todos', $tasks);
            echo $html;
            break;
        case 'remove-task':
            unset($tasks['deleted_tasks'][$data['ix']]);
            $res = update_data($data, $tasks);
            echo 'removed: '. $res;
            break;
        case 'delete-task':
            echo 'deleted: '. delete_task($data, $tasks);
            break;
        case 'recover-task':
            $uid = uniqid();
            $tasks['deleted_tasks'][$data['ix']]['deleted']=false;
            $tasks['deleted_tasks'][$data['ix']]['details'][] = add_message("Recovered task");

            $tasks['tasks'][$uid]=$tasks['deleted_tasks'][$data['ix']];
            $tasks['tasks'][$uid]['uid']=$uid;
            unset($tasks['deleted_tasks'][$data['ix']]);
            $res = update_data($data, $tasks);
            echo 'recovered: '. $res;
            break;
        case 'edit-task':
            if (!$data['nt']) {
                echo "{error:'something wrong in edit task'}";
                break;
            }
            if ($tasks['tasks'][$data['ix']]['task']===$data['nt']) {
                echo "{error:'no task changed'}";
                break;
            }
            $tasks['tasks'][$data['ix']]['task']=$data['nt'];
            $tasks['tasks'][$data['ix']]['details'][]=add_msg("Task change to:{$data['nt']}");
            $res = update_data($data, $tasks);
            echo 'edited: '. $res;
            break;
        case 'edit-description':
            if (!$data['nt']) {
                echo "{error:'something wrong in edit description'}";
                break;
            }
            if ($tasks['tasks'][$data['ix']]['description']===$data['nt']) {
                echo "{error:'no descrition changed'}";
                break;
            }
            $tasks['tasks'][$data['ix']]['description']=$data['nt'];
            $tasks['tasks'][$data['ix']]['details'][]= add_msg("Description change to:{$data['nt']}");
            $res = update_data($data, $tasks);
            echo 'edited: '. $res;
            break;
        case 'check-task':
            $ok = $data['ok'] === "true" ? true : false;
            $tasks['tasks'][$data['ix']]['complete']=$ok;
            $tasks['tasks'][$data['ix']]['details'][]=add_msg($ok ? "Task marked as completed" : "Task marked as uncompleted");
            $res = update_data($data, $tasks);
            echo 'edited: '. $res;
            break;
        case 'set-due':
            $tasks['tasks'][$data['ix']]['due']= mysql_datetime($data['du']);
            $tasks['tasks'][$data['ix']]['details'][]=add_msg("Set due to task: ".$data['du']);
            $res = update_data($data, $tasks);
            echo 'edited: '. $res;
            break;
        case 'get-values':
            $res['length'] = is_null($tasks['length']) ? 0 : $tasks['length'];
            $res['deleted'] = is_null($tasks['deleted']) ? 0 : $tasks['deleted'];
            $res['listed'] = is_null($tasks['listed']) ? 0 : $tasks['listed'];
            $res['completed'] = is_null($tasks['completed']) ? 0 : $tasks['completed'];
            echo json_encode($res);
            break;
        case 'add-task':
            if (!$data['tk']) {
                echo "{error:'something wrong'}";
                break;
            }
            $task = add_data($data);
            $html = $handlebars->render('task', $task);
            echo $html;
            break;
        case 'task-detail':
            $task = $tasks['tasks'][$data['ix']];
            $details = array_reverse($task['details']);
            $task['details']=array_reverse($task['details']);

            $task += detail_options();
            $html = $handlebars->render('detail', $task);
            echo $html;
            break;
        case 'send-task-user':
            if (!$data['us'] || $data['us'] === $data['id']) {
                echo "{error:'select a correct user'}";
                break;
            }
            $uid = uniqid();
            $task = $tasks['tasks'][$data['ix']];
            $tasks['tasks'][$data['ix']]['details'][]=add_msg("Send task to: " . $data['us']);
            $tasks['tasks'][$data['ix']]['send_to']= $data['us'];
            $res = ' edited: '. update_data($data, $tasks);

            if (!$data['pr']) {// if not preserve task
                $res .= ' deleted: '. delete_task($data, $tasks);
            }
            
            $newdata = $data;
            $newdata['id']=$data['us'];
            $task['uid']=$uid;

            $user_tasks = get_data($newdata);
            $user_tasks['tasks'][$uid]=$task;
            $user_tasks['tasks'][$uid]['from']=$data['id'];
            $user_tasks['tasks'][$uid]['from_date']=date('Y-m-d H:i:s');
            $user_tasks['tasks'][$uid]['details'][]=add_msg("task from {$data['us']}");

            $res .= ' sending: '. update_data($newdata, $user_tasks);
            echo $res;
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
        'added' => date('Y-m-d H:i:s'),
        'due' => false,
        'details' => [add_msg("New task: {$data['tk']}")],
        'deleted' => false,
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
    if ($count < 1) {//add member if not exist
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

function delete_task($data, $tasks)
{
    $uid = uniqid();
    $tasks['tasks'][$data['ix']]['deleted']=true;
    $tasks['tasks'][$data['ix']]['details'][]= add_message("Delete this task");
    $tasks['deleted_tasks'][$uid]=$tasks['tasks'][$data['ix']];
    $tasks['deleted_tasks'][$uid]['uid']=$uid;
    unset($tasks['tasks'][$data['ix']]);
    $res = update_data($data, $tasks);
    return $res;
}

function add_msg($message = false)
{
    return $message ? ["message"=>"$message","date"=>date('Y-m-d H:i:s')] : [];
}

function detail_options()//detail modal windows options
{
    return [
        'modal_header'=>[
            "headline"=>"To-Do Task Detail",
            "id"=>"modal-todo",
            "size"=>"",
            "dismiss"=>true,
            "header_class"=>"bg-gray",
            "body_class"=>" bg-gray todo-details",
        ],
        'modal_footer'=>[
            "footer_class"=>"bg-gray",
            "close_btn"=>[
                "enable"=>true,
                "text"=>"Close",
                "color"=>"default",
                "size"=>"xs",
                "class"=>"",
                "attr"=>"data-dismiss='modal'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-remove",
                ]
            ]
        ],
        //send task box options
        'send_box_options'=>[
            "headline"=>"Send Task to user",
            "color"=>"success",
            "solid"=>false,
            "with-border"=>false,
            "class"=>"",
            "attr"=>"",
            "box-tool"=>[
                "enable"=>false,
                "collapsable"=>true,
                "removable"=>true,
            ],
        ],
        //send taks button options
        'send_options'=>[
            "send_btn"=>[
                "enable"=>true,
                "text"=>"Send",
                "color"=>"primary",
                "size"=>"xs",
                "class"=>"send-taks-user pull-right",
                "attr"=>"data-cmd='send-task-user'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-send",
                ],
            ],
        ],
        //details task box options
        'due_box_options'=>[
            "headline"=>"Due Task",
            "color"=>"warning",
            "solid"=>false,
            "with-border"=>false,
            "class"=>"",
            "attr"=>"",
            "box-tool"=>[
                "enable"=>false,
                "collapsable"=>true,
                "removable"=>false,
            ],
        ],
        //set due taks button options
        'due_options'=>[
            "set_due_btn"=>[
                "enable"=>true,
                "text"=>"Set due",
                "color"=>"primary",
                "size"=>"xs",
                "class"=>"set-due pull-right",
                "attr"=>"data-cmd='set-due'",
                "icon"=>[
                    "enable"=>true,
                    "icon"=>"glyphicon glyphicon-time",
                ],
            ],
        ],
    ];
}

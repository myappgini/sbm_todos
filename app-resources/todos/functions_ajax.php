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
    'sr' => Request::val('sort_array', false), // array to new sort
    'pg' => Request::val('progress',false)//progress current task
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
            $tasks += detail_options();
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
            $tasks['deleted_tasks'][$data['ix']]['details'][] = add_msg("Recovered task");

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
            $ok = $data['ok'] === "true" ;
            if ($ok){
                if ($tasks['tasks'][$data['ix']]['progress']<100) {
                    $tasks['tasks'][$data['ix']]['old_progress']=$tasks['tasks'][$data['ix']]['progress'];
                }
                $tasks['tasks'][$data['ix']]['progress']=100;
            }else{
                $tasks['tasks'][$data['ix']]['progress']=$tasks['tasks'][$data['ix']]['old_progress'];
            }
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
        case 'set-progress':
            $tasks['tasks'][$data['ix']]['progress']= $data['pg'];
            $tasks['tasks'][$data['ix']]['details'][]=add_msg("Set task progress to: ".$data['pg']);
            $res = update_data($data, $tasks);
            echo 'edited: '. $res;
            break;
        case 'get-values':
            $res['length'] = is_null($tasks['length']) ? 0 : $tasks['length'];
            $res['deleted'] = is_null($tasks['deleted']) ? 0 : $tasks['deleted'];
            $res['listed'] = is_null($tasks['listed']) ? 0 : $tasks['listed'];
            $res['completed'] = is_null($tasks['completed']) ? 0 : $tasks['completed'];
            $res['progress'] = is_null($tasks['progress']) ? 0 : $tasks['progress'];
            echo json_encode($res);
            break;
        case 'get-progress':
            echo $tasks['tasks'][$data['ix']]['progress'];
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
            $task['progress_options']['progress_bar']['width']=$task['progress'];
            $html = $handlebars->render('detail', $task);
            echo $html;
            break;
        case 'config-todo':
            $task=[];
            $task += detail_options();
            $html = $handlebars->render('settings', $task);
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
        case "sort-list":
            $sorted=[];
            foreach ($data['sr'] as $value) {
                $sorted[$value]=$tasks['tasks'][$value];
            }
            $tasks['tasks']=$sorted;
            $res = update_data($data, $tasks);
            echo "sorted: ".$res;
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
    $progress_values = array_column($set['tasks'],'progress');
    $progress_sum = array_sum(array_map('porcentual',$progress_values));

    $elements=count($set['tasks']);
    $set['length']=$elements + $del;
    $set['deleted']=$del;
    $set['listed']=$elements;
    $set['completed']=$completed;
    $set['progress']=$progress_sum;
    // $set["arabic"] = (object)array(
    //     "html" => "<foo bar=\"baz\"/> &amp;",
    //     "arabic" => "العربية al-ʿarabiyyah, IPA: [æl ʕɑrɑˈbijjɐ], or عربي ʿarabī",
    //     "hebrew" => "    ",
    //     "chinese" => "汉语/漢語 Hanyu; 华语/華語 Huáyǔ; 中文 Zhōngwén",
    //     "korean" => "한국어/조선말",
    //     "japanese" => "日本語 Nihongo",
    //     "umlauts" => "äüöãáàß",
    //     "escaped" => "\u65e5\u672c\u8a9e",
    //     "emoji" => json_decode('"\u263a \ue415\ue056\ue057\ue414\ue405\ue106\ue418 \ud83d\ude04\ud83d\ude0a\ud83d\ude03\ud83d\ude09\ud83d\ude0d\ud83d\ude18"'),
    // );//https://gist.github.com/muhqu/863757

    $set = "`{$data['fn']}`='" . json_encode($set, JSON_UNESCAPED_UNICODE) . "'";

    $sql = "UPDATE `{$data['tn']}` SET {$set} WHERE {$where}";
    $res = sql($sql, $eo);
    $errors[] = $eo;

    $data['errors'] = $errors;

    return $res;
}
function porcentual($v){
    return ($v/100);
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
    $tasks['tasks'][$data['ix']]['details'][]= add_msg("Delete this task");
    $tasks['deleted_tasks'][$uid]=$tasks['tasks'][$data['ix']];
    $tasks['deleted_tasks'][$uid]['uid']=$uid;
    unset($tasks['tasks'][$data['ix']]);
    $res = update_data($data, $tasks);
    return $res;
}

function add_msg($message = false)
{
    return $message ? ["message"=>"$message","date"=>date('Y-m-d H:i:s'),"user"=>getLoggedMemberID()] : [];
}

function detail_options()//detail modal windows options
{
    include ("templates/options/options.php");
    return $settings;
}

<?php

include('database_connection.php');
session_start();

$query = "
    SELECT * FROM user
    WHERE user_id != '".$_SESSION['user_id']."'
";

$statement = $connect->prepare($query);
$statement->execute();

$res = $statement->fetchAll();

$output = '
   <table class="table table-bordered table-striped">
        <tr>
            <td>用户名</td>
            <td>状态</td>
            <td>进行聊天</td>
        </tr>
';

foreach ($res as $row){
    $status = '';
    $current_timestamp = strtotime(date('Y-m-d H-i-s').'-10 second');
    $current_timestamp = date('Y-m-d H-i-s',$current_timestamp);

    $user_last_activity = fetch_user_last_activity($row['user_id'],$connect);

    if($user_last_activity>$current_timestamp){
    $status = '<span class="label label-success">在线</span>';
    }
    else{
        $status = '<span class="label label-danger">离线</span>';
    }

    $output .= '
 <tr>
  <td>'.$row['username'].' '.count_unseen_message($row['user_id'], $_SESSION['user_id'], $connect).' '.fetch_is_type_status($row['user_id'], $connect).'</td>
  <td>'.$status.'</td>
  <td><button type="button" class="btn btn-info btn-xs start_chat" data-touserid="'.$row['user_id'].'" data-tousername="'.$row['username'].'">发信息</button></td>
 </tr>
 ';
}
$output.='</table>';

echo $output;
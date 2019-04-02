<?php
include("database_connection.php");
session_start();

if(!isset($_POST['submit'])){
    exit("错误执行");
}//判断是否有submit操作

$name=$_POST['name'];//post获取表单里的name
$password=$_POST['password'];//post获取表单里的password
$comfirmPWD = $_POST['confirmPWD'];


if($comfirmPWD != $password){
        echo '<script>alert("密码与确认密码不一致");history.go(-1);</script>';
        exit(0);
}

if ($comfirmPWD == $password){
    $query="insert into user(user_id,username,password) values (null,'$name','$password')";
    $statement = $connect->prepare($query);
    $res = $statement->execute();

}

if (!$res){
    die('Error: ' . mysqli_error());//如果sql执行失败输出错误
}else{
    echo "注册成功";//成功输出注册成功
}

$connect=null;//断开数据库

?>
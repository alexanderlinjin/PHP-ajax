<?PHP



include('database_connection.php');//链接数据库
session_start();

if(isset($_SESSION['user_id'])){
    header('location:index.php');
}
$message = '';
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $query = "
    SELECT * FROM user
    WHERE
    username = '$username'
    ";
    $statement = $connect->prepare($query);
    $statement->execute();

    $count = $statement->rowCount();
    if($count>0){
        $res = $statement->fetchAll();
        foreach ($res as $row){
            if($_POST['password'] == $row['password']){
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $sub_query = "
                INSERT INTO login_detail (user_id)
                VALUES ('".$row['user_id']."')
                ";
                $statement = $connect->prepare($sub_query);
                $statement->execute();
                $_SESSION['login_details_id'] = $connect->lastInsertId();
                header('location:index.php');
            }
            else{
                $message = "<p>密码错误</p>";
            }
        }
    }
    else{
        echo '用户名错误';
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登陆</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<div class="panel panel-default">
    <div class="panel-heading">登录</div>
    <div class="panel-body col-lg-4 col-lg-offset-4">
        <p class="text-danger"><?php echo $message; ?></p>
        <form name="login"  method="post" class="form-group">
            <div class="form-group">
                <label >用户名</label>
                <input class="form-control" type="text" name="username" required>
            </div>
            <div class="form-group">
                <label >密码</label>
                <input class="form-control" type="password" name="password" required>
            </div>
            <div class="form-group">
                <input class="form-control" type="submit" name="login" value="登录">
            </div>
        </form>
    </div>



</div>

<script src="../js/jquery-3.2.1.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
</body>
</html>



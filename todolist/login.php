<?php
session_start();
require('tdlibrary.php');

$error = [];
$password ='';
$userid ='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $userid = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($userid === ''|| $password === ''){
        $error['login'] = 'blank';
    } else {
        //ログインチェック
        $db = dbconnect();
        $stmt = $db->prepare('select id, name, password from members where user_id=? limit 1');
        if (!$stmt) {
            die($db->error);
        }

        $stmt->bind_param('s', $userid);
        $success =$stmt->execute();
        if (!$success) {
            die($db->error);
        }
        
        $stmt->bind_result( $id, $name, $hash);
        $stmt->fetch();

        if (password_verify($password, $hash)){
            //ログイン成功
            session_regenerate_id();
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            header('Location: index.php');
        } else {
            $error['login'] = 'failed';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
                integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="/todolist/style.css">
    <title>ToDoList ログイン</title>
</head>
<body class="body_color">

<div class="wrapper">
        <header class="parent">
                <div class="child1"><h1><span class="to">To</span><span class="do">Do</span><span class="list">List</span></h1></div>
                <div style="text-align:center; margin: auto 8px; color: white;" >
                    <span style="font-weight:bold;" >ようこそ！</span>
                </div>
                <div id="init-btn-header" class="child2" >
                    <span style="font-weight:bold; color:white; margin:0 54px;" >ゲスト</span>
                <div>
            
        </header>

    <div class="container">

        <hr>
        <div class="form-group">
            <div class="form-check form-check-inline">
                <p>認証をお願いします</p>
            </div>
            <div class="form-check form-check-inline">
                <a href="/todolist/join/index.php">登録がまだの方はこちら</a>    
            </div>
            <div class="add_list">
                <form action="" method="post">
                    <div class="form-group">
                        <label class="d-block">ユーザーID</label>
                            <input type="text" name="user_id" class="form-control" value="<?php echo h($userid); ?>"/>
                            <?php if (isset($error['login']) && $error['login'] === 'blank'): ?>
                            <p class="error">※ユーザーIDとパスワードをご記入ください</p>
                            <?php endif; ?>
                            <?php if(isset($error['login']) && $error['login'] === 'failed'): ?>
                            <p class="error">※ログインに失敗しました。正しくご記入ください。</p>
                            <?php endif; ?>
                    </div>
                    <div class="form-gloup">
                        <label class="d-block">パスワード</label>
                        
                        
                        <p><input type="password" name="password" class="form-control" value="<?php echo h($password); ?>"></p>
                    </div>
                    <div style="text-align:center;">
                        <button type="submit" class="add-btn" >認証する</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <footer class="parent_f">
        <p>製作：山口幸介</p>
    </footer>
</div>
</body>
</html>
<?php
session_start();
require('/MAMP/ToDoList/todolist/tdlibrary.php');

if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    $form = [
        'name' =>'',
        'user_id' =>'',
        'password' =>'',
    ];
}

$error = [];

/* フォームの内容をチェック */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form['name'] = filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
    if ($form['name'] === '') {
        $error['name'] = 'blank';
    }

    $form['user_id'] = filter_input(INPUT_POST,'user_id',FILTER_SANITIZE_STRING);
    if ($form['user_id'] === '') {
        $error['user_id'] = 'blank';
    } else {
        $db = dbconnect();
        $stmt = $db->prepare('select count(*) from members where user_id=?');
        if(!$stmt){
            die($db->error);
        }
        $stmt->bind_param('s', $form['user_id']);
        $success = $stmt->execute();
        if(!$success){
            die($db->error);
        }

        $stmt->bind_result($cnt);
        $stmt->fetch();
        
        if($cnt > 0) {
            $error['user_id'] = 'duplicate';
        }
    }

    $form['password'] = filter_input(INPUT_POST,'password',FILTER_SANITIZE_STRING);
    if ($form['password'] === '') {
        $error['password'] = 'blank';
    }else if(strlen($form['password']) < 4) {
        $error['password'] = 'length';
    }

    if (empty($error)){
        $_SESSION['form'] =$form;

        header('Location: check.php');
        exit();
        
    }

}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
                integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="/todolist/style.css">
    <title>ToDoList 新規登録</title>
</head>

<body class="body_color">

<div class="wrapper">
        <header class="parent">
                <div class="child1"><h1><span class="to">To</span><span class="do">Do</span><span class="list">List</span></h1></div>
                <div style="text-align:center; margin: auto 8px; color: white;" >
                    <span style="font-weight:bold;" >新規登録</span>
                </div>
                <div id="init-btn-header" class="child2" >
                    <span style="font-weight:bold; color:white; margin:0 54px;" >ゲスト</span>
                <div>
            
        </header>


    <div class="container">

        <hr>
        <div class="form-group">
            <div class="form-check form-check-inline">
                <p>ユーザー登録をお願いします</p>
            </div>
            <div class="form-check form-check-inline">
                <a href="/todolist/login.php">すでに登録されている方はこちら</a>    
            </div>
            <div class="add_list">
                <form action="" method="post">
                    <div class="form-group">
                        <label class="d-block">ユーザーネーム <span style="color: #f33;">必須</span></label>
                        <?php if (isset($error['name']) && $error['name'] === 'blank'): ?>
                            <p class="error">* ニックネームを入力してください</p>
                        <?php endif; ?>
                        <input type="text" name="name" class="form-control" value="<?php echo h($form['name']); ?>"/>
                    </div>   
                    <div class="form-group">
                        <label class="d-block">ユーザーID <span style="color: #f33;">必須</span></label>
                        <?php if (isset($error['user_id']) && $error['user_id'] === 'blank'): ?>
                            <p class="error">※ユーザーIDを入力してください</p>
                        <?php endif; ?>
                        <?php if (isset($error['user_id']) && $error['user_id'] === 'duplicate'): ?>
                        <p class="error">※そのユーザーIDはすでに登録されています</p>
                        <?php endif; ?>
                        <input type="text" name="user_id" class="form-control" maxlength="255" value="<?php echo h($form['user_id']); ?>"/>
                    </div>   
                    <div class="form-gloup">
                        <label class="d-block">パスワード <span style="color: #f33;">必須</span></label>
                            <?php if (isset($error['password']) && $error['password'] === 'blank'): ?>
                            <p class="error">※パスワードを入力してください</p>
                            <?php endif; ?>
                            <?php if (isset($error['password']) && $error['password'] === 'length'): ?>
                        <p class="error">※パスワードは4文字以上で入力してください</p>
                        <?php endif; ?>
                        <p><input type="password" name="password" class="form-control" value="<?php echo h($form['password']); ?>"></p>
                    </div>
                    <div style="text-align:center;">
                        <button type="submit" class="add-btn" >確認画面へ</button>
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
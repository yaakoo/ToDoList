<?php
session_start();
require('/MAMP/ToDoList/todolist/tdlibrary.php');

if (isset($_SESSION['form'])) {
	$form = $_SESSION['form'];
} else {
	header('Location: index.php');
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$db = dbconnect();
	$stmt = $db->prepare('insert into members (name, user_id, password) VALUES (?,?,?)');
	if (!$stmt){
		die($db->error);
	}
	//passwordは暗号化が必要
	$password = password_hash($form['password'], PASSWORD_DEFAULT);
	$stmt->bind_param('sss',$form['name'], $form['user_id'], $password);
	$success = $stmt->execute();
	if (!$success) {
		die($db->error);
	}
	//重複登録対策のため$_SESSIONの内容を消す。
	unset($_SESSION['form']);
	header('Location: thanks.php');
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
	<title>ToDoList 登録確認</title>

</head>

<body class="body_color">

<div class="wrapper">
        <header class="parent">
                <div class="child1"><h1><span class="to">To</span><span class="do">Do</span><span class="list">List</span></h1></div>
                <div style="text-align:center; margin: auto 8px; color: white;" >
                    <span style="font-weight:bold;" >登録確認</span>
                </div>
                <div id="init-btn-header" class="child2" >
                    <span style="font-weight:bold; color:white; margin:0 54px;" >ゲスト</span>
                </div>
            
        </header>

    <div class="container">

        <hr>
        <div class="form-group">
            <div class="form-check form-check-inline">
                <p>以下の内容で登録を確定します</p>
            </div>
            <div class="form-check form-check-inline">
                <a href="index.php?action=rewrite">&laquo;&nbsp;書き直す場合はこちら</a>    
            </div>
            <div class="add_list" style="text-align:center;">
                <p>よろしければ、「確定する」ボタンをクリックしてください</p>
                <form action="" method="post"  >
                    <div class="form-group">
                        <hr>
                        <dl >
                            <dt>ユーザーネーム</dt>
                            <dd><?php echo h($form['name']); ?></dd>

                            <dt>ユーザーID</dt>
                            <dd><?php echo h($form['user_id']); ?></dd>
                        
                            <dt>パスワード</dt>
                            <dd>
                                【表示されません】
                            </dd>
                        
                        </dl>
                        <hr>
                        <div style="text-align:center;">
                            <button type="submit" class="add-btn" >確定する</button>
                        </div>
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
<?php
session_start();
require('tdlibrary.php');

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
} else {
    header('Location: login.php');
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id){
    header('Location: index.php');
    exit();
}

$db = dbconnect();

$error = [];

/* フォームの内容をチェック */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['priority'] = filter_input(INPUT_POST,'priority',FILTER_SANITIZE_NUMBER_INT);
    if ($form['priority'] === null) {
        $error['priority'] = 'blank';
    }

    $form['message'] = filter_input(INPUT_POST,'message',FILTER_SANITIZE_STRING);
    if ($form['message'] === '') {
        $error['message'] = 'blank';
    }


    if (empty($error)){
        $_SESSION['form'] =$form;

        
         //メッセージの投稿
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $priority = filter_input(INPUT_POST, 'priority', FILTER_SANITIZE_NUMBER_INT);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

            $stmt = $db->prepare('UPDATE lists SET message=?, priority=? WHERE id=?');
            if (!$stmt) {
                die($db->error);
            }
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
            $priority = filter_input(INPUT_POST, 'priority', FILTER_SANITIZE_NUMBER_INT);
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $stmt->bind_param('sii', $message, $priority, $id);
            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }

            header('Location: index.php');


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
    <link rel="stylesheet" href="/css/style.css">

    <title>ToDoList <?php echo h($name); ?>さんのリスト</title>
</head>
<body class="body_color">

<div class="wrapper">
        <header class="parent">
                <div class="child1"><h1><span class="to">To</span><span class="do">Do</span><span class="list">List</span></h1></div>
                <div style="text-align:center; margin: auto 8px; color: white;" >
                    ユーザー名：<span style="font-weight:bold;" ><?php echo h($name); ?></span>
                </div>
                <div id="init-btn-header" class="child2" >
                    <button onclick="location.href='./logout.php'" class="lo-btn" >ログアウト</button>
                </div>
            
        </header>

    <div class="container">

            <hr>

        <div id="content">
            <p><a href="index.php">一覧にもどる</a></p>
            <?php 
            $stmt = $db->prepare('select l.id, l.member_id, l.priority,
            l.message, l.created from lists l,members 
            m where l.id=? and m.id=l.member_id order by id desc');
            if (!$stmt){
                die($db->error);
            }
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $stmt->bind_param('i', $id);
            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }

            $stmt->bind_result($id, $member_id, $priority, $message, $created);
            if ($stmt->fetch()):

            $_POST['priority'] = h($priority);

            ?>


                <div class="add_list">
                <form action="" method="post"  >
                    <div class="form-group">
                        <label class="d-block">優先度</label>
                        
                            <?php if (isset($error['priority']) && $error['priority'] === 'blank'): ?>
                                <p class="error" style="color: #F33;">※優先度を選んでください</p>
                            <?php endif; ?>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="priority" id="q1_1" class="form-check-input" value="1"
                                    <?php if (isset($_POST['priority']) && $_POST['priority'] == "1") echo 'checked'; ?>>
                                <label for="q1_1" class="form-check-label">最優先</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="priority" id="q1_2" class="form-check-input" value="2" 
                                    <?php if (isset($_POST['priority']) && $_POST['priority'] == "2") echo 'checked'; ?>>
                                <label for="q1_2" class="form-check-label">優先</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="priority" id="q1_3" class="form-check-input" value="3" 
                                    <?php if (isset($_POST['priority']) && $_POST['priority'] == "3") echo 'checked'; ?>>
                                <label for="q1_3" class="form-check-label">普通</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="priority" id="q1_4" class="form-check-input" value="4" 
                                    <?php if (isset($_POST['priority']) && $_POST['priority'] == "4") echo 'checked'; ?>>
                                <label for="q1_4" class="form-check-label">後回し</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="priority" id="q1_5" class="form-check-input" value="5" 
                                    <?php if (isset($_POST['priority']) && $_POST['priority'] == "5") echo 'checked'; ?>>
                                <label for="q1_5" class="form-check-label">可能なら</label>
                            </div>
                        
                    </div>
                    <div class="form-gloup">
                        <label for="message">内容</label>

                        <?php if (isset($error['message']) && $error['message'] === 'blank'): ?>
                            <p class="error" style="color: #F33;">※文字を入力してください</p>
                        <?php endif; ?>
                        
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <textarea name="message" id="message" rows="5" class="form-control" 
                                maxlength="200" placeholder="※200文字以内で入力してください"><?php echo h($message); ?></textarea>
                    </div>
                    <div style="text-align:center;">
                        <button type="submit" class="add-btn" >編集する</button>
                    </div>

                </form>
                </div>

            




            <?php else: ?>
            <p>その投稿は削除されたか、URLが間違えています</p>
            <?php endif; ?>
        </div>
    </div>
    <footer class="parent_f">
        <p>製作：山口幸介</p>
    </footer>
</div>

</body>
</html>

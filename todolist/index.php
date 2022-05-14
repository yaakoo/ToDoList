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

            $stmt = $db->prepare('insert into lists (message, priority, member_id) values(?,?,?)');
            if (!$stmt)  {
                die($db->error);
            }

            $stmt->bind_param('sii',$message, $priority, $id);
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
    <link rel="stylesheet" href="/todolist/style.css">
    <title>ToDoList <?php echo h($name); ?>さんのリスト</title>
</head>
<body class="body_color">

<div class="wrapper">
        <header class="parent">
                <div class="child1"><h1><span class="to">To</span><span class="do">Do</span><span class="list">List</span></h1></div>
                <div style="text-align:center; margin: auto 8px; color: white;">ユーザー名：<span style="font-weight:bold;" ><?php echo h($name); ?></span>
                </div>
                <div id="init-btn-header" class="child2" >
                    <button onclick="location.href='./logout.php'" class="lo-btn" >ログアウト</button>
                <div>
            
        </header>

    <div class="container">
        
        <hr>

        <details open>
        <summary>リストを追加</summary>
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
                    <textarea name="message" id="message" rows="5" class="form-control"
                        maxlength="200" placeholder="※200文字以内で入力してください"><?php if(isset($_POST['message'])) echo h($_POST['message']); ?></textarea>
                </div>
                <div style="text-align:center;">
                    <button type="submit" class="add-btn" >追加</button>
                </div>
                

            </form>
        
        </div>
        </details>

        <hr>

        <?php 
        
        $stmt = $db->prepare('select l.id, l.member_id, l.priority, l.message, l.modified from lists l, members 
        m where m.id=l.member_id in (?) order by priority');
        if (!$stmt){
            die($db->error);
        }

        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }

        $stmt->bind_result($id, $member_id, $priority, $message, $modified);

        while ($stmt->fetch()):
        ?>
        
        <div class="list-<?php echo h($priority); ?>">
            
            <p><?php echo nl2br(h($message)); ?></p>
            <td class="day"><?php echo h($modified); ?></td>
            <td>[<a href="update.php?id=<?php echo h($id); ?>" style="color: #33F;">編集</a>]</td>
            <td>[<a href="delete.php?id=<?php echo h($id); ?>" style="color: #F33;">削除</a>]</td>
            
        </div>

        <?php endwhile; ?>
        <?php
        $stmt2 = $db->prepare('select 
        count(priority) as ap, 
        COUNT(priority=1 or null) as p1,
        COUNT(priority=2 or null) as p2, 
        COUNT(priority=3 or null) as p3,
        COUNT(priority=4 or null) as p4,
        COUNT(priority=5 or null) as p5
        FROM lists where member_id in (?);');
        if (!$stmt2){
        die($db->error);
        }

        $stmt2->bind_param('i', $id);
        $success = $stmt2->execute();
        if (!$success) {
            die($db->error);
        }

        $stmt2->bind_result($ap, $p1, $p2, $p3, $p4, $p5);

        $stmt2->fetch();

        echo  var_dump($ap, $p1, $p2, $p3, $p4, $p5);
        ?>
    </div>

    <footer class="parent_f">
        <p>製作：山口幸介</p>
    </footer>
</div>

</body>
</html>
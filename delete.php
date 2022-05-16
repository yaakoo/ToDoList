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

$list_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$list_id) {
    header('Location: index.php');
    exit();
}

$db = dbconnect();
$stmt = $db->prepare('delete from lists where id=? and member_id=? limit 1');
if (!$stmt) {
    die($db->error);
}
$stmt->bind_param('ii', $list_id, $id);
$success = $stmt->execute();
if (!$success){
    die($db->error);
}

header('Location: index.php'); exit();
?>

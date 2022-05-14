<?php

//DBへの接続
function dbconnect() {
    $db = new mysqli('localhost:8889','root','root','tdlist_db');
    if (!$db) {
        die($db->error);
    }
    return $db;
}

/* htmlspecialcharsを短くする */
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
}

/* mime_contents_type()の代わり */
function mime_content_type_image($filename)
{
	list($w, $h, $type) = getimagesize($filename);
	if (!$type) {
		return '';
	} else {
		return image_type_to_mime_type($type);
	}
}
?>

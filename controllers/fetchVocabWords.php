<?php
require_once(DB_FILE);

$db = new DB;

$query = $db->query("SELECT * FROM `vocab_words` WHERE phrase = ? AND `visable` = ? AND `hide` = ? LIMIT 50", ['false', 'true', 'false']);

$word_array = [];

while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {

    $des = strip_tags(ltrim(rtrim($rows['description'])));
    $des = str_replace('&nbsp;', ' ', $des);

    $word_array[] = [
        'word' => $rows['name'],
        'description' => $des, 
        'type' => $rows['type']
    ];
}

echoJson($word_array);
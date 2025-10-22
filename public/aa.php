<?php

$file = file_get_contents('a.txt');

$arr = json_decode($file, true);

echo count($arr['items']);



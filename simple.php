<?php
require 'module/SiXhEaD.Template.php';

$tp = new Template('_tp_simple.html');

$title = 'วิธีใช้ class SiXhEaD.Template';

$tp->block('data');
for ($no = 1; $no < 6; $no++) {
    $tp->apply();
}

$tp->ignore('jQuery');
$tp->ignore('1000');
$tp->ignore('library');

$tp->display();
exit;
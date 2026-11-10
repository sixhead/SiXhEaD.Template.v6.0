<?php
require 'module/SiXhEaD.Template.php';

$item = (int) $_GET['item'];
$fix = (int) $_GET['fix'];
if (!$item) {
    $item = 5;
}
if ($item > 100) {
    $item = 100;
}

$tp = new Template('_tp_table.html');

$title = 'วิธีการเติม table ให้เต็ม';

$tp->block('data');
$tp->sub(4);
for ($no = 1; $no <= $item; $no++) {
    $name = $no;
    $tp->apply();
}

if ($fix) {
    if ($tp->getCurrentSub() != 1) {
        for ($no = $tp->getCurrentSub(); $no <= 4; $no++) {
            $name = '';
            $tp->apply();
        }
    }
}

$tp->display();
exit;
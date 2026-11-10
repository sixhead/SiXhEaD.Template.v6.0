<?php
require 'module/SiXhEaD.Template.php';

$tp = new Template('_tp_other.html');

$title = 'วิธีใช้งาน จาก class, function';
_setData();

$tp->display();
exit;

function _setData() {
    global $tp;

    $tp->setGlobal(false);
    $tp->block('data');
    for ($no = 1; $no < 6; $no++) {
        $tp->assign('no', $no);
        $tp->apply();
    }
    $tp->setGlobal(true);
}
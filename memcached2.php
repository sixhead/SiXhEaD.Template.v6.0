<?php
require 'module/SiXhEaD.Template.php';

$mem = new Memcached();
$mem->addServer('memcached', 11211);

$tp = new Template('_tp_memcached.html');

$title = 'วิธีใช้งานกับ Memcached2';
$now = date('Y-m-d H:i:s');

$cache_name = "test2";
$data = $mem->get($cache_name);
if (!$data) {
  for ($no = 1; $no < 6; $no++) {
    $data .= $tp->applyBlock('data');
  }
  $mem->set($cache_name, $data, 5);
}

$tp->display();
exit;
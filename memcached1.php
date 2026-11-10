<?php
require 'module/SiXhEaD.Template.php';

$mem = new Memcached();
$mem->addServer('memcached', 11211);

$cache_name = "test1";
$cache = $mem->get($cache_name);
if (!$cache) {
  $tp = new Template('_tp_memcached.html');

  $title = 'วิธีใช้งานกับ Memcached1';
  $now = date('Y-m-d H:i:s');

  $tp->block('data');
  for ($no = 1; $no < 6; $no++) {
    $tp->apply();
  }

  $cache = $tp->generate();
  $mem->set($cache_name, $cache, 5);
}

echo $cache;
exit;
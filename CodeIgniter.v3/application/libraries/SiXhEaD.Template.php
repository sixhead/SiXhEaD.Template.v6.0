<?php
/*
SiXhEaD Template - PHP Template Engine
Copyright (c) 2005-2016, Chairat Sunthornwiphat (sixhead.com)
Licensed under the MIT License (LICENSE)
Version 6.0.4
 */

class Template {
    public $tp_file = '';
    public $tp_key = '';
    protected $tp_block = [];
    protected $tp_vars;
    protected $tp_ignore_vars;

    public function __construct($tp_file, $tp_key = "SiXhEaD") {
        $this->tp_file = $tp_file;
        $this->tp_key = $tp_key;
        $this->start();
    }

    protected function start() {
        $fp = fopen($this->tp_file, "r");
        if (!$fp) {
            $this->setDebug("Template file \"$this->tp_file\" not found");
        }
        $html = fread($fp, filesize($this->tp_file));
        fclose($fp);
        $html = $this->encodeSpecial($html);

        $this->setGlobal(true);
        $this->all_html = $html;
        $this->setKey($this->tp_key);
        $this->autoHide();
    }

    public function ignore($key) {
        $this->tp_ignore_vars["$key"] = 1;
    }

    public function assign($key, $value) {
        $this->tp_vars["$key"] = $value;
    }

    public function setGlobal($is_global) {
        $this->is_global = $is_global;
    }

    protected function setKey($tp_key) {
        $this->tp_key = $tp_key;
    }

    protected function autoHide() {
        if (preg_match_all("/<!--\/$this->tp_key:(.*?)-->/", $this->all_html, $matches)) {
            foreach ($matches[1] as $block_name) {
                $this->block($block_name);
            }
        }
    }

    public function block($block_name) {
        $this->tp_block[$block_name]['hide'] = 0;
        $this->tp_block[$block_name]['apply'] = '';
        $this->block_name = $block_name;
        if (preg_match("/<!--$this->tp_key:$block_name-->(.*?)<!--\/$this->tp_key:$block_name-->/", $this->all_html, $matches)) {
            $this->tp_block[$block_name]['html'] = $matches[1];
            $this->hide();
            $this->sub(0);
        } else {
            $this->setDebug("&lt;!--$this->tp_key:$block_name--&gt;............&lt;!--/$this->tp_key:$block_name--&gt; not found");
        }
    }

    public function clear($block_name) {
        $this->tp_block[$block_name]['hide'] = 0;
        $this->tp_block[$block_name]['apply'] = '';
    }

    public function hide() {
        $this->tp_block[$this->block_name]['hide'] = 1;
    }

    public function apply() {
        $this->tp_block[$this->block_name]['hide'] = 0;
        $this->tp_block[$this->block_name]['apply'] .= $this->encodeDollar($this->applyBlock($this->block_name));
    }

    public function applyBlock($block_name) {
        return $this->setValue($this->blockHtml($block_name));
    }

    public function blockHtml($block_name) {
        if ($this->all_sub_in_block > 0) {
            $this->subCount();
            return $this->decodeSpecial($this->tp_block[$block_name]['sub_html'][$this->current_sub]);
        } else {
            return $this->decodeSpecial($this->tp_block[$block_name]['html']);
        }
    }

    public function sub($all_sub_in_block) {
        $this->all_sub_in_block = $all_sub_in_block;
        $this->subClear();
        if ($this->all_sub_in_block > 0) {
            for ($current_sub = 1; $current_sub <= $all_sub_in_block; $current_sub++) {
                if (preg_match("/<!--SUB:$current_sub-->(.*?)<!--\/SUB:$current_sub-->/i", $this->tp_block[$this->block_name]["html"], $matches)) {
                    $this->tp_block[$this->block_name]["sub_html"][$current_sub] = $matches[1];
                } else {
                    $this->setDebug("&lt;!--SUB:$current_sub--&gt;............&lt;!--/SUB:$current_sub--&gt; not found in block : " . $this->block_name);
                }
            }
        }
    }

    protected function subClear() {
        $this->current_sub = 0;
    }

    protected function subCount() {
        if ($this->current_sub == $this->all_sub_in_block) {
            $this->subClear();
        }
        $this->current_sub += 1;
    }

    public function applySub($block_name, $sub_no) {
        return $this->setValue($this->subHtml($block_name, $sub_no));
    }

    protected function subHtml($block_name, $sub_no) {
        return $this->decodeSpecial($this->tp_block[$block_name]['sub_html'][$sub_no]);
    }

    public function getCurrentSub() {
        $current_sub = $this->current_sub + 1;
        if ($current_sub > $this->all_sub_in_block) {
            $current_sub = 1;
        }
        return $current_sub;
    }

    public function getCurrentSubTotal() {
        return $this->all_sub_in_block;
    }

    public function generate() {
        $gen_block_name = [];
        $gen_block_apply = [];
        $html = $this->all_html;
        if (is_array($this->tp_block)) {
            foreach (array_keys($this->tp_block) as $block_name) {
                $gen_block_name[$block_name] = "/<!--$this->tp_key:$block_name-->(.*?)<!--\/$this->tp_key:$block_name-->/";
                if ($this->tp_block[$block_name]['hide'] == 1) {
                    $gen_block_apply[$block_name] = '';
                } else {
                    $gen_block_apply[$block_name] = $this->tp_block[$block_name]['apply'];
                }
            }
            $html = preg_replace($gen_block_name, $gen_block_apply, $html);
        }

        $html = $this->setValue($html);
        $html = $this->decodeSpecial($html);
        $html = $this->decodeDollar($html);
        $html = preg_replace("@<!--(\/|)$this->tp_key(.*?)-->@", '', $html);
        return $html;
    }

    public function display() {
        echo $this->generate();
    }

    protected function setValue($html) {
        $html = $this->encodeIgnore($html);
        if ($this->is_global == true) {
            $html = preg_replace_callback('/\\$(\w+)/', function ($m) {
                return $GLOBALS[$m[1]];
            }, $html);
        } else {
            $html = preg_replace_callback('/\\$(\w+)/', function ($m) {
                return $this->tp_vars[$m[1]];
            }, $html);
        }
        $html = $this->decodeIgnore($html);
        return $html;
    }

    protected function encodeSpecial($string) {
        $string = str_replace("\r", "%0D", $string);
        $string = str_replace("\n", "%0A", $string);
        $string = str_replace("\t", "%09", $string);
        return $string;
    }

    protected function decodeSpecial($string) {
        $string = str_replace("%0D", "\r", $string);
        $string = str_replace("%0A", "\n", $string);
        $string = str_replace("%09", "\t", $string);
        return $string;
    }

    protected function encodeIgnore($string) {
        if (is_array($this->tp_ignore_vars)) {
            foreach (array_keys($this->tp_ignore_vars) as $ignore_var) {
                $string = preg_replace("/\\\$$ignore_var(\W)/", '{my_ignore}$0', $string);
                $string = preg_replace("/{my_ignore}\\\$/", '{my_ignore}', $string);
            }
        }
        return $string;
    }

    protected function decodeIgnore($string) {
        return str_replace("{my_ignore}", "$", $string);
    }

    protected function encodeDollar($string) {
        return str_replace("\$", "{%24}", $string);
    }

    protected function decodeDollar($string) {
        return str_replace("{%24}", "$", $string);
    }

    protected function setDebug($string) {
        echo "
<br><strong>$this->tp_key Template debug message</strong><br><br>
$string
";
        exit;
    }
}

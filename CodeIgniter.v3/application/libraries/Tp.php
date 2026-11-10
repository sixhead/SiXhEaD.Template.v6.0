<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/SiXhEaD.Template.php';

class TP extends Template {
    public function __construct($params) {
        $template_file = $params['template_file'];
        parent::__construct($template_file);
        $this->setGlobal(FALSE);
    }
}
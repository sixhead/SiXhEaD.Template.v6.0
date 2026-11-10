<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sixhead extends CI_Controller {

    public function index() {

        $params = array('template_file' => APPPATH . 'views/_tp_item.html');
        $this->load->library('tp', $params, 'tp_item');

        $this->tp_item->block("welcome");
        $this->tp_item->assign("username", 'admin');
        $this->tp_item->apply();
        $welcome = $this->tp_item->generate();

        $params = array('template_file' => APPPATH . 'views/_tp_index.html');
        $this->load->library('tp', $params, 'tp');

        $this->tp->assign("welcome", $welcome);
        $this->tp->assign("text1", 'text 1');
        $this->tp->assign("text2", 'text 2');

        $this->tp->block("data");
        for ($i = 1; $i <= 5; $i++) {
            $this->tp->assign("no", $i);
            $this->tp->assign("title", "title: $i");
            $this->tp->apply();
        }

        $this->tp->display();

    }

}

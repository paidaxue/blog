<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
2015年2月14日
*/


class Index extends CI_Controller{

    //private $_data;
    //public $referrer;

    public function __construct(){
        parent::__construct();

        //$this->load->library('form_validation');

        //加载用户处理model,起别名users
        //$this->load->model('users_mdl','users');

    }

    public function index(){
        echo "ddd";
    }
}

/*
 End of file
 Location:inedx.php
 */
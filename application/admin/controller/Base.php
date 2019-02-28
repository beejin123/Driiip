<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Session;

class Base extends Controller {
	private $pass = '6526b828aac667bb9a838aea3136b849';
	public function __construct() {
		//必须先调用父类的构造函数
		parent::__construct();
		//判断登录
		if (session('?login')) {
			$password = Session::get('login');
			if ($this->pass != $password) {
				$this->redirect('admin/login/login');
			}
		} else {
			$this->redirect('admin/login/login');
		}
	}
}

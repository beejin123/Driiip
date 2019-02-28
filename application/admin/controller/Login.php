<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Session;

class Login extends Controller {
	private $pass = '6526b828aac667bb9a838aea3136b849';
	public function login() {
		if (session('?login')) {
			$password = Session::get('login');
			if ($this->pass == $password) {
				$this->redirect('admin/index/index');
			}
		}
		return $this->fetch("login/login");
	}

	public function verify() {
		$username = request()->param('username');
		$password = request()->param('password');

		if ($username == 'admin') {
			if ($this->pass == md5($password)) {
				session('login', md5($password));
				$this->success('登录成功', 'admin/index/index', '', 1);
			} else {
				$this->error('密码错误');
			}
		} else {
			$this->error('账号错误');
		}
	}
	public function quit() {
		session('login', null);
		if (session('?login')) {
			$this->error('退出失败，稍后重试');
		} else {
			$this->success('退出成功', 'admin/index/index', '', 1);
		}
	}
}

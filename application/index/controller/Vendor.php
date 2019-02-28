<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Vendor extends Controller {
	public function index() {
		return $this->fetch();
	}
	public function get() {
		$res = Db::table('zh_vendor')->select();
		return json_encode($res);
	}
}
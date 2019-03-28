<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Order extends Controller {
	public function index() {
		$vendor = Db::table('zh_vendor')->where('online', 1)->select();
		// dump($vendor);
		$this->assign('vendor', $vendor);
		return $this->fetch();
	}
	public function get() {
		$dev = request()->get('dev', '');
		if ($dev != '') {
			$garden = Db::table('zh_stock')->where('device_sn', $dev)->select();
			return $garden;
		}
		return;
	}
	public function add() {
		$category_type = request()->get('category_type', '');
		$goods_key = request()->get('goods_key', '');
		$category = Db::table('zh_category')->select();
		// dump($category);
		$goods = [];
		if ($category_type != '' || $goods_key != '') {
			if ($category_type != '') {
				$where[] = ['category_type', '=', $category_type];
			}
			if ($goods_key != '') {
				$where[] = ['name', 'like', '%' . $goods_key . '%'];
			}
			$goods = Db::table('zh_goods')->where($where)->select();
		}
		$this->assign('goods', $goods);
		$this->assign('category', $category);
		return $this->fetch();
	}
}
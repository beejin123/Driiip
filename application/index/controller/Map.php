<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Map extends Controller {
	public function index() {
		$today_order_amount = Db::table('zh_order_master')->where('create_time', '>', date('Y-m-d', time()))->sum('order_amount');
		$order_amount = Db::table('zh_order_master')->sum('order_amount');
		$new_customer = Db::table('zh_customer')->where('create_time', '>', date('Y-m-d', time()))->count();
		$local_customer = Db::table('zh_order_master')->field('customer_account,count(customer_account)')
			->group('customer_account')
			->having('count(customer_account)>=3')
			->select();
		$this->assign('data', array(
			'today_order_amount' => $today_order_amount,
			'order_amount' => $order_amount,
			'new_customer' => $new_customer,
			'local_customer' => count($local_customer),
		));
		return $this->fetch();
	}
}
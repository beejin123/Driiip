<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use think\Db;

class User extends Base {
	public function index() {
		$wx_nick_name = request()->get('wx_nick_name', '');
		$price_min = request()->get('price_min', '');
		$price_max = request()->get('price_max', '');
		$date_start = request()->get('date_start', '');
		$date_end = request()->get('date_end', '');
		$type = request()->get('type', '');
		$sort = request()->get('sort', '');
		$param = array(
			'wx_nick_name' => $wx_nick_name,
			'price_min' => $price_min,
			'price_max' => $price_max,
			'date_start' => $date_start,
			'date_end' => $date_end,
			'type' => $type,
			'sort' => $sort,
		);
		$where = array();
		if ($type == 1) {
			if ($sort == 1) {
				$order = ['cl.consumption_amount' => 'asc'];
			} else {
				$order = ['cl.consumption_amount' => 'desc'];
			}
		} else {
			if ($sort == 1) {
				$order = ['c.customer_id' => 'asc'];
			} else {
				$order = ['c.customer_id' => 'desc'];
			}
		}
		if ($wx_nick_name != '') {
			$where[] = ['c.wx_nick_name', 'like', '%' . $wx_nick_name . '%'];
		}
		if ($price_min != '') {
			$where[] = ['cl.consumption_amount', '>=', $price_min];
		}
		if ($price_max != '') {
			$where[] = ['cl.consumption_amount', '<=', $price_max];
		}
		if ($date_start != '') {
			$where[] = ['c.create_time', '>=', $date_start];
		}
		if ($date_end != '') {
			$where[] = ['c.create_time', '<=', $date_end];
		}
		$res = Db::table('zh_customer_ledger')
			->alias('cl')
			->leftJoin('zh_customer c', 'c.customer_id = cl.customer_id')
			->where($where)
			->order($order)
			->field('c.account, c.wx_avatar_url, c.wx_nick_name, c.create_time AS time, cl.consumption_amount, cl.giving_balance, cl.giving_consumption_amount')
			->paginate(10, false, ['query' => $param]);
		$this->assign('res', $res);
		$this->assign('param', $param);
		return $this->fetch();

	}
}
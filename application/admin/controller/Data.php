<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Data extends Controller {
	public function index() {
		$dev_sn = request()->get('localtion', '');
		$date = request()->get('date', '');
		$where1 = [];
		$where2 = [];
		if ($dev_sn != '') {
			$where1[] = ['s.dev_sn', '=', $dev_sn];
			$where2[] = ['o.dev_sn', '=', $dev_sn];
		}
		if ($date != '') {
			$date_arr = explode(' - ', $date);
			$where1[] = ['s.create_time', ['>=', $date_arr['0']], ['<', $date_arr['1']], 'and'];
			$where2[] = ['o.create_time', ['>=', $date_arr['0']], ['<', $date_arr['1']], 'and'];
		}
		$shelve = Db::table('zh_shelve')
			->alias('s')
			->leftJoin('zh_shelve_detail d', 's.shelve_id = d.shelve_id')
			->leftJoin('zh_goods g', 'd.goods_id = g.goods_id')
			->field('s.shelve_id, s.inc_goods_num, s.red_goods_num, d.goods_num, d.shelve_type, d.goods_id, g.name')
			->where($where1)
			->select();
		$data = [
			'up' => [],
			'down' => [],
		];
		foreach ($shelve as $k => $v) {
			if ($v['shelve_type'] == 1) {
				if (array_key_exists($v['goods_id'], $data['up'])) {
					$data['up'][$v['goods_id']]['up_goods_num'] += $v['goods_num'];
				} else {
					$data['up'][$v['goods_id']] = [
						'goods_name' => $v['name'],
						'up_goods_num' => $v['goods_num'],
						'goods_id' => $v['goods_id'],
					];
				}
			} else {
				if (array_key_exists($v['goods_id'], $data['down'])) {
					$data['down'][$v['goods_id']]['down_goods_num'] += $v['goods_num'];
				} else {
					$data['down'][$v['goods_id']] = [
						'goods_name' => $v['name'],
						'down_goods_num' => $v['goods_num'],
					];
				}
			}
		}
		$order = Db::table('zh_order_master')
			->alias('o')
			->leftJoin('zh_order_detail d', 'o.order_id = d.order_id')
			->field('d.goods_id, d.goods_name, d.goods_price')
		// ->where('d.status', 1)
			->where($where2)
			->select();
		$data_order = [];
		foreach ($order as $k => $v) {
			if (array_key_exists($v['goods_id'], $data_order)) {
				$data_order[$v['goods_id']]['goods_num'] += 1;
				$data_order[$v['goods_id']]['goods_price'] += $v['goods_price'];
			} else {
				$data_order[$v['goods_id']] = [
					'goods_name' => $v['goods_name'],
					'goods_num' => 1,
					'goods_price' => $v['goods_price'],
				];
			}
		}

		$arr = $data['up'];
		foreach ($data['down'] as $k => $v) {
			$arr[$k]['down_goods_num'] = $v['down_goods_num'];
		}
		foreach ($data_order as $k => $v) {
			$arr[$k]['goods_num'] = $v['goods_num'];
			$arr[$k]['goods_price'] = $v['goods_price'];
		}
		foreach ($arr as $key => $value) {
			if (empty($value['goods_id'])) {
				unset($arr[$key]);
			}
		}
		$location = Db::table('zh_vendor')->select();
		$this->assign('date', $date);
		$this->assign('dev_sn', $dev_sn);
		$this->assign('arr', $arr);
		$this->assign('location', $location);
		return $this->fetch();
	}
	public function excel() {
		$dev_sn = request()->get('localtion', '');
		$date = request()->get('date', '');
		$where1 = [];
		$where2 = [];
		if ($dev_sn != '') {
			$where1[] = ['s.dev_sn', '=', $dev_sn];
			$where2[] = ['o.dev_sn', '=', $dev_sn];
		}
		if ($date != '') {
			$date_arr = explode(' - ', $date);
			$where1[] = ['s.create_time', ['>=', $date_arr['0']], ['<', $date_arr['1']], 'and'];
			$where2[] = ['o.create_time', ['>=', $date_arr['0']], ['<', $date_arr['1']], 'and'];
		}
		$shelve = Db::table('zh_shelve')
			->alias('s')
			->leftJoin('zh_shelve_detail d', 's.shelve_id = d.shelve_id')
			->leftJoin('zh_goods g', 'd.goods_id = g.goods_id')
			->field('s.shelve_id, s.inc_goods_num, s.red_goods_num, d.goods_num, d.shelve_type, d.goods_id, g.name')
			->where($where1)
			->select();
		$data = [
			'up' => [],
			'down' => [],
		];
		foreach ($shelve as $k => $v) {
			if ($v['shelve_type'] == 1) {
				if (array_key_exists($v['goods_id'], $data['up'])) {
					$data['up'][$v['goods_id']]['up_goods_num'] += $v['goods_num'];
				} else {
					$data['up'][$v['goods_id']] = [
						'goods_id' => $v['goods_id'],
						'goods_name' => $v['name'],
						'up_goods_num' => $v['goods_num'],
					];
				}
			} else {
				if (array_key_exists($v['goods_id'], $data['down'])) {
					$data['down'][$v['goods_id']]['down_goods_num'] += $v['goods_num'];
				} else {
					$data['down'][$v['goods_id']] = [
						'goods_name' => $v['name'],
						'down_goods_num' => $v['goods_num'],
					];
				}
			}
		}
		$order = Db::table('zh_order_master')
			->alias('o')
			->leftJoin('zh_order_detail d', 'o.order_id = d.order_id')
			->field('d.goods_id, d.goods_name, d.goods_price')
		// ->where('d.status', 1)
			->where($where2)
			->select();
		$data_order = [];
		foreach ($order as $k => $v) {
			if (array_key_exists($v['goods_id'], $data_order)) {
				$data_order[$v['goods_id']]['goods_num'] += 1;
				$data_order[$v['goods_id']]['goods_price'] += $v['goods_price'];
			} else {
				$data_order[$v['goods_id']] = [
					'goods_name' => $v['goods_name'],
					'goods_num' => 1,
					'goods_price' => $v['goods_price'],
				];
			}
		}

		$arr = $data['up'];
		foreach ($data['down'] as $k => $v) {
			$arr[$k]['down_goods_num'] = $v['down_goods_num'];
		}
		foreach ($data_order as $k => $v) {
			$arr[$k]['goods_num'] = $v['goods_num'];
			$arr[$k]['goods_price'] = $v['goods_price'];
		}
		$new_arr = [];
		foreach ($arr as $key => $value) {
			if (empty($value['goods_id'])) {
				unset($arr[$key]);
			} else {
				$new_arr[] = $value;
			}
		}
		$location = "";
		if ($dev_sn != '') {
			$location = Db::table('zh_vendor')->where('dev_sn', $dev_sn)->value('alias');
		}
		$name = $location . $date . '补退售货数据';
		$title = ['商品编号', '名称', '上架（件）', '下架（件）', '实际售卖（件）', '售卖金额（元）'];
		exportExcel($title, $arr, $name, './', true);
	}
}
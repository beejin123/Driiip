<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller {
	public function index() {
		$shelve = Db::table('zh_shelve')
			->alias('s')
			->leftJoin('zh_shelve_detail d', 's.shelve_id = d.shelve_id')
			->leftJoin('zh_goods g', 'd.goods_id = g.goods_id')
			->field('s.shelve_id, s.inc_goods_num, s.red_goods_num, d.goods_num, d.shelve_type, d.goods_id, g.name')
		// ->where('s.create_time', '>', '2019-02-01 00:00:00')
			->where('s.dev_sn', '00000102000caf02fe57')
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
			->where('d.status', 1)
		// ->where('o.create_time', '>', '2019-02-01 00:00:00')
			->where('o.dev_sn', '00000102000caf02fe57')
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
		dump($arr);
	}
}
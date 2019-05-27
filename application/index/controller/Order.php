<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Order extends Controller {
	public function index() {
		$vendor = Db::table('zh_vendor')->where('online', 1)->select();
		$warehouse = Db::table('zh_warehouse')
			->alias('w')
			->join('zh_warehouse_master m', 'w.category_type = m.warehouse_master_id')
			->where('w.goods_num', '>', 0)
			->select();
		$order = Db::table('zh_warehouse_order')->where('order_status', 0)->select();
		/*
			dump($order);
			if ($order) {
				foreach ($order as $k => $v) {
					// dump(json_decode($v['order_content'], true));
					foreach (json_decode($v['order_content'], true) as $key => $value) {
						dump($value);
						if(){

						}

					}
				}
			}
		*/
		$this->assign('warehouse', $warehouse);
		$this->assign('vendor', $vendor);
		return $this->fetch();
	}
	public function sure() {
		$obj = request()->param('obj', '');
		$dev_name = request()->param('dev_name', '');
		// dump($dev_name);die();
		$data = json_decode($obj, true);
		if ($obj != '') {
			Db::startTrans();
			try {
				Db::table('zh_warehouse_order')
					->data([
						'order_content' => $obj,
						'order_status' => '0',
						'dev_name' => $dev_name,
					])
					->insert();
				foreach ($data as $k => $v) {
					Db::table('zh_warehouse')
						->where('goods_id', $v['goods_id'])
						->where('category_type', $v['warehouse_master_id'])
						->setDec('goods_num', $v['change_num']);
				}
				Db::commit();
				return 1;
			} catch (\Exception $e) {
				Db::rollback();
				return 0;
			}
		}
	}
	public function order() {
		$order = Db::table('zh_warehouse_order')->select();
		foreach ($order as $k => $v) {
			$str = '';
			foreach (json_decode($v['order_content'], true) as $key => $value) {
				$str .= $value['goods_name'] . '：' . $value['change_num'] . '，';
			}
			$order[$k]['order_content'] = rtrim($str, '，');
		}
		// dump($order);die();
		$this->assign('order', $order);
		return $this->fetch();
	}
	public function sureOut() {
		$warehouse_order_id = request()->param('warehouse_order_id', '');
		if ($warehouse_order_id == "") {
			return 0;
		}
		$res = Db::table('zh_warehouse_order')->where('warehouse_order_id', $warehouse_order_id)->data(['order_status' => 1])->update();
		if ($res) {
			return 1;
		} else {
			return 0;
		}
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
		$warehouse_master = Db::table('zh_warehouse_master')->select();
		// dump($warehouse_master);die();
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
			if ($goods) {
				$warehouse = Db::table('zh_warehouse')->select();
				foreach ($goods as $k => $v) {
					foreach ($warehouse as $key => $value) {
						if ($v['goods_id'] == $value['goods_id']) {
							$goods[$k]['num'][$value['category_type']] = $warehouse[$key]['goods_num'];
						}
					}
				}
			} else {
				$goods = [];
			}
		}
		// dump($goods);die();
		$this->assign('category_type', $category_type);
		$this->assign('goods_key', $goods_key);
		$this->assign('goods', $goods);
		$this->assign('category', $category);
		$this->assign('warehouse_master', $warehouse_master);
		return $this->fetch();
	}
	public function enter() {
		$goods_id = request()->get('goods_id', '');
		$add_num = request()->get('add_num', '');
		$category_type = request()->get('category_type', '');
		$goods_num = Db::table('zh_warehouse')->where(['goods_id' => $goods_id, 'category_type' => $category_type])->value('goods_num');
		if ($goods_num) {
			$res = Db::table('zh_warehouse')->where(['goods_id' => $goods_id, 'category_type' => $category_type])->setInc('goods_num', $add_num);
			if ($res) {
				return true;
			}
			return false;
		} else {
			$goods = Db::table('zh_goods')->where('goods_id', $goods_id)->find();
			if ($goods) {
				$res = Db::table('zh_warehouse')->insert([
					'goods_id' => $goods_id,
					'goods_name' => $goods['name'],
					'goods_num' => $add_num,
					'category_type' => $category_type,
				]);
				if ($res) {
					return true;
				}
			}
			return false;
		}
	}
	public function cut() {
		$goods_id = request()->get('goods_id', '');
		$cut_num = request()->get('cut_num', '');
		$category_type = request()->get('category_type', '');
		$goods_num = Db::table('zh_warehouse')->where(['goods_id' => $goods_id, 'category_type' => $category_type])->value('goods_num');
		if ($goods_num < $cut_num) {
			return false;
		}
		if ($goods_num) {
			$res = Db::table('zh_warehouse')->where(['goods_id' => $goods_id, 'category_type' => $category_type])->setDec('goods_num', $cut_num);
			if ($res) {
				return true;
			}
		}
		return false;
	}
	public function warehouse() {
		$res = Db::table('zh_warehouse_master')->select();
		// dump($res);die();
		$this->assign('res', $res);
		return $this->fetch();
	}
	public function addwarehouse() {
		$name = request()->post('name', '');
		$location = request()->post('location', '');
		if ($name == "") {
			$this->error('仓库名为空');
			return;
		}
		if ($location == "") {
			$this->error('仓库位置为空');
			return;
		}
		$select_res = Db::table('zh_warehouse_master')->where('warehouse_master_name', $name)->select();
		if ($select_res) {
			$this->error('仓库名已存在');
			return;
		}
		$res = Db::table('zh_warehouse_master')->data([
			'warehouse_master_name' => $name,
			'warehouse_master_location' => $location,
		])->insert();
		if ($res) {
			$this->success('仓库新增成功', 'index/order/warehouse');
		} else {
			$this->error('仓库新增失败，稍后重试！');
		}
	}
}
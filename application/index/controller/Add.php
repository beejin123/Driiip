<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Add extends Controller {
	public function index() {
		return $this->fetch();
	}
	public function add() {
		$param = request()->post();
		$file = request()->file('file');
		$info = $file->move(__DIR__ . '/../../../public/articleImage');
		if ($info) {
			$thumb = 'public/articleImage/' . $info->getSaveName();
			$param['thumb'] = $thumb;
			$param['add_time'] = date('Y-m-d H:i:s', time());
			$res = Db::name('article')->insert($param);
			if ($res) {
				return json_encode(array(
					'code' => 200,
					'message' => '文章添加成功',
				));
			} else {
				return json_encode(array(
					'code' => 401,
					'message' => '服务器错误，请稍后重试',
				));
			}
		} else {
			return json_encode(array(
				'code' => 401,
				'message' => '文件上传失败，原因是：' . $file->getError(),
			));
		}
	}
}

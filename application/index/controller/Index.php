<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller {
	public function index() {
		$keyword = request()->get('keyword', '');
		if ($keyword != '') {
			$keyword = trim($keyword);
			$where = "%$keyword%";
		} else {
			$where = '%%';
		}
		$res = Db::name('article')->where('keyword', 'like', $where)->where('deleted', 1)->where('status', 2)->field('art_id,thumb,title')->select();
		$this->assign('article', $res);
		$this->assign('keyword', $keyword);
		return $this->fetch();
	}
	public function test() {
		return $this->fetch();
	}
	public function detail() {
		$art_id = request()->param('id');
		$article = Db::name('article')->where('art_id', $art_id)->find();
		$this->assign('article', $article);
		return $this->fetch();
	}
}

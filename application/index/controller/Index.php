<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller {
	public function index() {
		$title = request()->get('title', '');
		if ($title != '') {
			$title = trim($title);
			$where = "%$title%";
		} else {
			$where = '%%';
		}
		$res = Db::name('article')->where('title', 'like', $where)->where('deleted', 1)->field('art_id,thumb,title')->select();
		$this->assign('article', $res);
		return $this->fetch();
	}
	public function detail() {
		$art_id = request()->param('id');
		$article = Db::name('article')->where('art_id', $art_id)->find();
		$this->assign('article', $article);
		return $this->fetch();
	}
}

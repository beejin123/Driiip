<?php
namespace app\admin\controller;

use app\admin\controller\Base;
use think\Db;

class Article extends Base {
	public function index() {
		return $this->fetch();
	}
	public function list() {
		$list = Db::name('article')->where('deleted', 1)->order('art_id', 'desc')->paginate(10);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function del() {
		$art_id = request()->param('id');
		$res = Db::name('article')->where('art_id', $art_id)->update(array('deleted' => 2));
		if ($res) {
			$this->success('删除成功', 'admin/article/list');
		} else {
			$this->error('删除失败，稍后重试');
		}
	}
	public function edit() {
		$art_id = request()->param('id');
		$article = Db::name('article')->where('art_id', $art_id)->find();
		$this->assign('article', $article);
		return $this->fetch();
	}
	public function editImg() {
		$art_id = request()->param('art_id');
		$file = request()->file('file');
		$info = $file->move(__DIR__ . '/../../../public/articleImage');
		if ($info) {
			$thumb = 'public/articleImage/' . $info->getSaveName();
			$res = Db::name('article')->update(array(
				'art_id' => $art_id,
				'thumb' => $thumb,
			));
			if ($res) {
				return json_encode(array(
					'code' => 200,
					'message' => '缩略图更换成功',
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
				'message' => '缩略图更换失败，原因是：' . $file->getError(),
			));
		}
	}
	public function send() {
		$art_id = request()->param('id');
		$status = request()->param('status');
		$res = Db::name('article')->where('art_id', $art_id)->update(array('status' => $status));
		if ($res) {
			if ($status == 2) {
				$this->success('发布成功', 'admin/article/list');
			} else {
				$this->success('撤回成功', 'admin/article/list');
			}
		} else {
			if ($status == 2) {
				$this->error('发布失败，稍后重试');
			} else {
				$this->error('撤回失败，稍后重试');
			}
		}
	}
	public function editArt() {
		$param = request()->post();
		$res = Db::name('article')->update($param);
		if ($res) {
			return 200;
		} else {
			return 401;
		}
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

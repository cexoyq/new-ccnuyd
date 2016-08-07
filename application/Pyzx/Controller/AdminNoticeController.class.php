<?php
namespace Pyzx\Controller;
use Common\Controller\AdminbaseController;

class AdminNoticeController extends AdminbaseController{
	
	protected $adminnotice_model;
	
    function _initialize() {
        parent::_initialize();
        $this->adminnotice_model = D("Notice");
		$this->auth_rule_model = D("Common/AuthRule");
    }
	
	public function index() {
		$map=array();
		$where['deleted']=0;
		$where['parentid']=0;
    	$count=$this->adminnotice_model->where($where)->count();
		$page = $this->page($count, 15);

		//$msgs=$this->adminnotice_model->where($map)->order(array("createtime"=>"DESC"))->limit($page->firstRow . ',' . $page->listRows)->select();
		$Model = new \Think\Model();
		$msgs=$Model->query("
							SELECT
							msg.id,
							msg.title,
							msg.msg,
							msg.deleted,
							msg.createtime,
							msg.updatetime,
							users.user_nicename as user,
							parent.type
							FROM
							notice AS msg
							INNER JOIN cmf_users AS users ON msg.senderid = users.id
							INNER JOIN notice AS parent ON msg.parentid = parent.id
							WHERE
							msg.parentid <> 0 AND
							msg.deleted = 0
							ORDER BY
							msg.createtime DESC
							LIMIT $page->firstRow, $page->listRows
							");
		$this->assign("Page", $page->show('Admin'));
		$this->assign("msgs",$msgs);
		$this->display();
	}
	
	/*删除消息*/
	function delete(){
		$id=intval(I("get.id"));
		//$result=$this->adminnotice_model->where(array("id"=>$id))->delete();
		$result=$this->adminnotice_model->where(array("id"=>$id))->setField('deleted',1);
		if($result!==false){
			$this->success("删除成功！", U("AdminNotice/index"));
		}else{
			$this->error('删除失败！');
		}
	}
	/*增加消息*/
	function add(){
    	import("Tree");
    	$tree = new \Tree();
    	$parentid = intval(I("get.parentid"));
		$map=array();
		$map["parentid"]=array("EQ",0);
		$map["deleted"]==array("EQ",0);
    	$result = $this->adminnotice_model->where($map)->order(array("listorder" => "ASC"))->select();
    	foreach ($result as $r) {
    		$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
    		$array[] = $r;
    	}
    	$str = "<option value='\$id' \$selected>\$spacer \$type</option>";
    	$tree->init($array);
    	$select_categorys = $tree->get_tree(0, $str);
    	$this->assign("select_categorys", $select_categorys);
    	$this->display();
	}
	/**/
	function add_post(){
		if(!sp_check_verify_code()){
			//$this->error("验证码错误！");
		}
		if (IS_POST) {
			if ($this->adminnotice_model->create()) {
				$this->adminnotice_model->createtime = date("Y-m-d H:i:s" ,time());
				$result=$this->adminnotice_model->add();
				if ($result!==false) {
					$to=empty($_SESSION['admin_notice_index'])?"AdminNotice/index":$_SESSION['admin_notice_index'];
					$this->success("发表成功！",U($to));
				} else {
					$this->error("发表失败！");
				}
			} else {
				$this->error($this->adminnotice_model->getError());
			}
		}
		
	}
}

<?php
namespace Pyzx\Controller;
use Common\Controller\AdminbaseController;

class AdminSyssetController extends AdminbaseController{
	
	protected $adminsysset_model;
	
    function _initialize() {
        parent::_initialize();
        $this->adminsysset_model = D("Common/Sysset");
		$this->auth_rule_model = D("Common/AuthRule");
    }
	
	public function index() {
    	$_SESSION['admin_sysset_index']="AdminSysset/index";
		
		$select_stopyd = $this->arr_to_html("stopyd",array(0=>'禁止普通用户预订',1=>'普通用户可预订'));	//选项框
		$credittype = $this->get_sysset_valueid("credittype");	//文本框
		$select_useryd2 = $this->arr_to_html("useryd2",array(0=>'不需要用户输入电话',1=>'用户需要输入电话'));//选项框
		$openyddate = $this->get_sysset_valueid("openyddate");		//文本框
		$yddate = $this->get_sysset_valueid("yddate");		//文本框
		$select_showname = $this->arr_to_html("showname",array(0=>'显示为已预订',1=>'显示姓名',2=>'显示为色块'));//选项框
		$yd_cd_limit = $this->get_sysset_valueid("yd_cd_limit");	//文本框
		$yd_time_limit = $this->get_sysset_valueid("yd_time_limit");//选项框
		$select_worktime = $this->get_worktime();		//选项框
		
		$v = $this->get_array_sysset();
		$this->assign("select_stopyd", $select_stopyd);
		$this->assign("credittype", $credittype);
		$this->assign("select_useryd2", $select_useryd2);
		$this->assign("openyddate", $openyddate);
		$this->assign("yddate", $yddate);
		$this->assign("select_showname", $select_showname);
		$this->assign("yd_cd_limit", $yd_cd_limit);
		$this->assign("yd_time_limit", $yd_time_limit);
		$this->assign("select_worktime",$select_worktime);
		$test=$this->adminsysset_model->get_sysset_valueid("worktime");
		//echo "测试：$test <br/>";
        $this->display();
	}
	
	public function sys_post(){
		if (IS_POST){
			$where['type'] = 'stopyd';
			$data['valueid'] = I('post.stopyd');
			$this->adminsysset_model->where($where)->save($data);
			
			$where = array();$data=array();
			$where['type']='credittype';
			$data['valueid'] = I('post.credittype');
			$this->adminsysset_model->where($where)->save($data);
			
			$where = array();$data=array();
			$where['type'] = 'useryd2'; 
			$data['valueid'] = I('post.useryd2');
			$this->adminsysset_model->where($where)->save($data);
			
			$where = array();$data=array();
			$where['type'] = 'openyddate';
			$data['valueid'] = I('post.openyddate');
			$this->adminsysset_model->where($where)->save($data);
			
			$where = array();$data=array();
			$where['type'] = 'yddate';
			$data['valueid'] = I('post.yddate');
			$this->adminsysset_model->where($where)->save($data);
			
			$where = array();$data=array();
			$where['type'] = 'showname';
			$data['valueid'] = I('post.showname');
			$this->adminsysset_model->where($where)->save($data);
			
			$where = array();$data=array();
			$where['type'] = 'yd_cd_limit';
			$data['valueid'] = I('post.yd_cd_limit');
			$this->adminsysset_model->where($where)->save($data);
			
			$where = array();$data=array();
			$where['type'] = 'yd_time_limit';
			$data['valueid'] = I('post.yd_time_limit');
			$this->adminsysset_model->where($where)->save($data);
			/*
			$where = array();$data=array();
			$where['type'] = 'worktime';
			$data['valueid'] = I('post.worktime');
			$this->adminsysset_model->where($where)->save($data);
			*/
		}
		$this->success("更新成功！");
	}
	
	public function get_worktime(){
		/*生成选项，调取opentime表，读取工作时间的类型后生成选项*/
		$str="";
		$valueid = $this->get_sysset_valueid("worktime");
		//echo "<br/>valueid:" . $valueid . "<br/>";
		$map = array();
		$map['parentid']=0;
		$model = D("Opentime");
		$result = $model->where($map)->select();
		//echo "result:" . "<br/>";
		//var_dump($result);
		//echo "<br/>/result:" . "<br/>";
		foreach ($result as $r) {
    		$r['selected'] = $r['id'] == $valueid ? 'selected' : '';
			$str = "<option value='{$r['id']}' {$r['selected']}> {$r['name']}</option>";
			//echo "str:";var_dump($str);
			//echo "<br/>";
    		$ret = $ret . $str;
    	}
    	return $ret;
	}
	
	public function get_sysset_valueid($typename){
		//取得系统类型的值，值是选项的id号
		$map = array();
		$map['type'] = $typename;
		$map['parentid'] = 0;
		$valueid = $this->adminsysset_model->where($map)->getField('valueid');
		return $valueid;
	}
	public function get_sysset_parentid($typename){
		//取得系统设置选项的父id号
		$map = array();
		$map['type'] = $typename;
		$map['parentid'] = 0;
		$valueid = $this->adminsysset_model->where($map)->getField('id');
		return $valueid;
	}
	
	public function get_array_sysset(){
		//取得一个数组，内容是所有选项的值，值是此类型的选项的id号
		$value = array();
		$value['showname'] = $this->adminsysset_model->get_sysset_valueid("showname");
		$value['yddate'] = $this->adminsysset_model->get_sysset_valueid("yddate");
		$value['openyddate'] = $this->adminsysset_model->get_sysset_valueid("openyddate");
		$value['useryd2'] = $this->adminsysset_model->get_sysset_valueid("useryd2");
		$value['credit'] = $this->adminsysset_model->get_sysset_valueid("credit");
		$value['credittype'] = $this->adminsysset_model->get_sysset_valueid("credittype");
		$value['stopyd'] = $this->adminsysset_model->get_sysset_valueid("stopyd");
		$value['yd_cd_limit'] = $this->adminsysset_model->get_sysset_valueid("yd_cd_limit");
		$value['yd_time_limit'] = $this->adminsysset_model->get_sysset_valueid("yd_time_limit");
		return $value;
	}
	
	public function arr_to_html($typename,$arr){
		//将关联数组格式化成html的选项
		//arr("1"=>"open","2"=>"close");
		
		//先求取此类型的值
		$value = $this->get_sysset_valueid($typename);
		$str ='';
		foreach ($arr as $k => $v){
			$selected = $k == $value ? 'selected' : '';
			$str =$str . "<option value='${k}' ${selected}>${v}</option>";
		}
		return $str; 
	}
	
	public function get_option_type($typename){
		//根据类型名称取得此类型下所有的选项，并格式化成html的option，是此类型的选项则为已选中
		import("Tree");
    	$tree = new \Tree();
		$options = array();
		//求出此类型的值
    	$valueid = intval($this->get_sysset_valueid($typename));
		//求出此类型的父级的ID号
		$parentid = intval($this->get_sysset_parentid($typename));
		//echo "valueid:" . $valueid . "<br/>";
		//echo "parentid:" . $parentid . "<br/>";
		$map=array();
		//$map['parentid']=array('EQ',$parentid);
		$map['type']=array('EQ',$typename);
		//取出此类型的所有选项数据集，
    	$result = $this->adminsysset_model->where($map)->order(array("listorder" => "ASC"))->select();
		//echo "result:<br/>";
		//var_dump($result);echo "<br/>";
    	foreach ($result as $r) {
			//echo "r['id']:" . $r['id'] . "| r['value']:" . $r['value'] . "<br/>";
    		$r['selected'] = $r['id'] == $valueid ? 'selected' : '';
    		$options[] = $r;
			//echo "r:<br/>";
			//var_dump($r);
			//echo "/r:<br/>";
			//echo "options:<br/>";
			//var_dump($options);
			//echo "/options<br/>";
    	}
    	$str = "<option value='\$id' \$selected>$spacer \$value</option>";
    	$tree->init($options);
    	$select = $tree->get_tree($parentid, $str);
		//echo "str:" . $str . "<br/>";
		//echo "<br/>select_categorys:<br/>";
		//print_r($select);
    	return $select;
	}
	
	
	
	
	
}

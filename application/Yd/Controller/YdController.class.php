<?php
namespace Yd\Controller;
use Common\Controller\HomebaseController;

class YdController extends HomebaseController{
	
	protected $reservation_model;
	function _initialize() {
		parent::_initialize();
		$this->reservation_model =D("Common/Reservation");
	}
	
	/*预订*/
	function index(){
		//var_dump(sp_get_current_user());
		//$r = $this->reservation_model->get_arr_yd('2016-7-16');
		//echo "预订列表：";
		//var_dump($r);
		//echo "<br /> 时间列表：";
		//$r = $this->reservation_model->get_arr_time();
		//var_dump($r);
		//echo "<br /> 场地列表：";
		//$r = $this->reservation_model->get_arr_cd(13);	//13羽毛球馆
		//var_dump($r);
		//echo "<br /> 生成表格：";
		//header("Content-type: text/html; charset=utf-8");
		$cdid=I('get.cdid',13);
		$cdname = $this->reservation_model->get_cd_name($cdid);		//取出场地名称
		
		$_date = I('get.date',date('y-m-d'));
		$table = $this->reservation_model->get_table($_date,$cdid);
		//echo $table;
		$msg2 = $this->reservation_model->get_msg2();
		
		$this->assign("cdname", $cdname);	//场地名称
		$this->assign("table", $table);		//生成表格框架
		$this->assign("msg2",$msg2);	//输出预订的消息内容，在网页里还有定时器每3秒刷新消息
		$this->assign("input_tel",$this->reservation_model->get_input_tel());	//根据系统设置及用户类型，输出电话号码输入框
		$this->assign("input_yd_xm",$this->reservation_model->get_input_xm());	//管理员可以更改预订人的显示名称
		//不使用列表方式显示日期选择$this->assign("nav_date",$nav_date);
		$this->display();
	}
	
	/*AJAX 预订的POST*/
	function ajax_yd(){
		if (IS_POST) {
			$data = I('post.arr');
			//echo json_encode($data);
			echo $this->reservation_model->model_ajax_yd($data);
		}
	}
	
	/*动态刷新预订表*/
	function ajax_refresh(){
		if(IS_POST){
			$data = I('post.refresh');
			//返回选择日期的预订表数据
			//echo $this->reservation_model->model_ajax_refresh($data);
			echo json_encode($this->reservation_model->model_ajax_refresh($data));
		}
	}
	
	/*AJAX的消息传送*/
	function ajax_msg2(){
		echo $this->reservation_model->get_msg2();
	}
}

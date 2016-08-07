<?php
namespace Yd\Controller;
use Common\Controller\HomebaseController;

class IndexController extends HomebaseController{
	
	protected $reservation_model;
	function _initialize() {
		parent::_initialize();
		$this->reservation_model =D("Common/Reservation");
	}
	
	/*用于显示场馆的预订情况*/
	function index(){
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
		date_default_timezone_set('prc');	//设置时区
		$mday = date("Y-M-D");		//*初始化日期*/
		$cgid = 13;		//初始化场馆ID的变量
		if(IS_GET){
			$cgid = I('get.cgid');
			if($cgid == '')
			{
				$cgid = 13;
			} 
		}
		$table = $this->reservation_model->get_table($mday,$cgid);
		//echo $mday;
		$msg2 = $this->reservation_model->get_msg2();
		$this->assign("table", $table);
		//不使用列表方式显示日期选择$this->assign("nav_date",$nav_date);
		$this->display();	
	}

	
}
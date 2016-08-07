<?php
namespace Yd\Controller;
use Common\Controller\HomebaseController;
class UsercenterController extends HomebaseController {
	/*显示前台用户的信息和信用分及系统推送的消息*/
	/*申请解除黑名单，申请增加信用分*/
	function _initialize() {
		parent::_initialize();
		if(sp_is_user_login()){
			$this->assign("user",sp_get_current_user());
		}else{
			/*没有登录*/
			$this->error("您还没有登录！",U("user/login/index"));
			if(IS_AJAX){
				$this->error("您还没有登录！",U("user/login/index"));
			}else{
				header("Location:".U("user/login/index"));
				exit();
			}/*没有登录，上面的是增加部分*/
		}
	}
	function index(){
		echo "hello,world!";
	}
}

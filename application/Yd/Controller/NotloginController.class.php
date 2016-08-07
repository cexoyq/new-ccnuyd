<?php
namespace Yd\Controller;
use Common\Controller\AppframeController;

class NotloginController extends AppframeController {
	
	
	
	function  notlogingetoutplease(){
		exit();
	}
	
	function ccnu(){
		if(IS_POST){
			/*与华中师范大学信息门户登陆对接*/
			/* 设置内部字符编码为 UTF-8 
				* 从gb2312(cp936)转为utf8时，不能够使用I('post.xm')来取得汉字。
				只能用$_REQUEST["xm"]取得汉字。
			*/
			header("Content-Type:text/html; charset=utf-8");
			//mb_internal_encoding("gb2312");
			$xm = mb_convert_encoding($_REQUEST["xm"], "utf-8", "cp936");
			//$xm1 = mb_convert_encoding(I('post.xm'), "utf-8", "cp936"); 不能用I('post.
			//$post_xm=iconv("gb2312","utf-8",$_REQUEST["xm"]);//可以用
			$zjh =  mb_convert_encoding($_REQUEST["zjh"], "utf-8", "cp936");
			echo "用户：$xm <br/> 工号：$zjh <br/> 您好，系统升级中......";
			//$this->display(":ccnu");
			$this->_do_ccnu_login($xm,$zjh);
		}
	}
	
    private function _do_ccnu_login($xm,$zjh){
        /*$username=$_POST['username'];
        $password=$_POST['password'];
        
        if(strpos($username,"@")>0){//邮箱登陆
            $where['user_email']=$username;
        }else{
            $where['user_login']=$username;
        }*/
		$where['user_login'] = $xm;
		$where['zjh'] = $zjh;
        $users_model=M('Users');
        $result = $users_model->where($where)->find();
		
		if(!$result){
			/*如果查不到此人*/
			$data=array(
				'user_login' => $xm,
				'user_nicename' => $xm,
				'user_email' => '',
				'user_pass' => sp_password($zjh),
				'last_login_ip' => get_client_ip(0,true),
				'create_time' => time(),
				'last_login_time' => time(),
				'user_status' => '1',
				'user_type'=>2,		//非管理员用户
				'credit'=>10,		//首次登陆送10信用分
				'zjh'=>$zjh,
			);
			$id= $users_model->add($data);
			$data['id']=$id;
			$result=$data;
			$_SESSION["user"]=$result;
			$this->success("欢迎新用户！！！", U("Yd/Yd/index"));
		}else{
			/*如果查到此人*/
			$_SESSION["user"]=$result;
			//$redirect=empty($_SESSION['login_http_referer'])?__ROOT__."/":$_SESSION['login_http_referer'];
			//$_SESSION['login_http_referer']="";
			$this->success("登录验证成功！",  U("Yd/Yd/index"));
		}
		
        
    }/*email_login*/




	
}
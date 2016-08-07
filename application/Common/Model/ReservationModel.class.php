<?php

/* * 
 * 预订类
	protected $navcat_model;
	
	function _initialize() {
		parent::_initialize();
		$this->navcat_model =D("Common/NavCat");
	}
 */
namespace Common\Model;
use Common\Model\CommonModel;
class ReservationModel extends CommonModel {

	protected $tablePrefix = ''; 	//不需要数据库前缀
	protected $_validate = array(
		//array('name', 'require', '名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		//array('name','','场地已经存在啦 ！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		//array('parentid', 'checkParentid', '菜单只支持四级！', 1, 'callback', 1),
   );
    //自动完成
    protected $_auto = array(
            //array(填充字段,填充内容,填充条件,附加规则)
    );

	/*根据系统设置的选择夏季时间或冬季时间，取得时间列表数组，并根据排序字段排序*/
	/*输出：array(15) { [5]=> string(3) "7-8" [6]=> string(3) "8-9" [7]=> string(4) "9-10" [9]=> string(5) "10-11" [10]=> string(5) "11-12" [11]=> string(5) "12-13" [12]=> string(5) "13-14" [13]=> string(5) "14-15" [14]=> string(5) "15-16" [15]=> string(5) "16-17" [16]=> string(5) "17-18" [17]=> string(5) "18-19" [18]=> string(5) "19-20" [19]=> string(5) "20-21" [20]=> string(5) "21-22" }*/
	public function get_arr_time(){
		$sysset_model = D("Common/Sysset");
		$worktime_id = $sysset_model->get_sysset_valueid("worktime");/*取出系统设置表里的工作时间ID值*/
		/*根据工作时间的ID值，取出时间列表*/
		$opentime_model = D("Opentime");
		$where = array();
		$where['parentid'] = $worktime_id;
		$where['deleted'] = 0;
		$result = $opentime_model->where($where)->order('listorder')->getField('id,name,starttime,endtime');
		return $result;
	}
	
	/*根据选择的场馆，取得场馆里的场地列表，并根据排序字段排序*/
	/*输出：array(9) { [14]=> string(7) "1号场" [15]=> string(7) "2号场" [17]=> string(7) "3号场" [24]=> string(7) "4号场" [25]=> string(7) "5号场" [27]=> string(7) "6号场" [28]=> string(7) "7号场" [29]=> string(7) "8号场" [30]=> string(7) "9号场" }*/
	public function get_arr_cd($cg_id){
		$cg_model = D("Place");
		$where = array();
		$where['parentid'] = $cg_id;
		$where['deleted'] = 0;
		$result = $cg_model->where($where)->order('listorder')->getField('id,name');
		return $result;
	}
	
	/*根据场地ID号取出场地名称*/
	public function get_cd_name($cg_id){
		$cg_model = D("Place");
		$where = array();
		$where['id'] = $cg_id;
		$where['deleted'] = 0;
		$result = $cg_model->where($where)->order('listorder')->getField('name');
		return $result;
	}
	
	/*同上功能，根据场地ID和时间ID，取出预订人的信息，不过是直接查询数据库得到数据*/
	/*array(1) { [1]=> array(3) { ["id"]=> string(1) "1" ["xm"]=> string(6) "熊义" ["user_id"]=> string(1) "1" } }*/
	public function get_ydr_arr($place_id,$opentime_id,$riqi){
		$ret = array();
		$where = array();
		$where['place_id'] = $place_id;
		$where['opentime_id'] = $opentime_id;
		$where['riqi'] = $riqi;
		$result = $this->where($where)->getField('id,xm,user_id');
		//var_dump($result);
		return $result;
	}

	/*根据时间和场地，输出二维表*/
	public function get_table($date,$cg_id){
		/*取得日期的预订数据，保存在变量里，以便下面的二维表中，通过调用get_yd函数，使用场地ID和时间ID查询到预订人的姓名*/
		//不用这个了，直接从数据库取$arr_yd = $this->get_arr_yd($date);
		
		/*X轴用时间，取得时间列表*/
		$time = $this->get_arr_time();
		/*Y轴用场地，取得场地列表*/
		$cd = $this->get_arr_cd($cg_id);
		/*生成表格*/
		$table="<table width='670px' class='table table-bordered table-hover '>";
		$table=$table . "<thead><tr>";
		/*表格的第一行，第一单元格是场地/时间，后面的是时间名称列表*/
		$table = $table . "<th width='60px'><span>场地</span></th>";	
		/*显示X轴时间的名称*/
		foreach ($time as $k=>$v) {
			$table=$table . "<th id='$k' width='40px'><span>{$v['name']}</span></th>";
		}
		$table=$table . "</tr></thead>";
		$table = $table . "<tbody>";
		/*显示表格内容，第一列显示场地名称*/
		foreach ($cd as $cd_k=>$cd_v){	
			/*$cd_k是场地ID，$cd_v是场地名称，第一列表格显示场地名称*/
			$table = $table . "<tr class='tr_yd' cd_id='$cd_k'>";
			/*第一列。显示场地名称*/
			$table = $table . "<td  style='cursor:default !important; text-align:center; border-top-width:1px !important; border-bottom-width:1px !important; border-left-width:1px !important;'><span>$cd_v</span></td>";
			foreach ($time as $time_k=>$time_v){
				/*根据场地ID和时间ID查询到预订人名*/
				$tmp = $this->get_ydr_arr($cd_k,$time_k,$date);
				$id = '';
				$xm = '';
				$user_id = '';
				if($tmp){
					$id = key($tmp);							//预订表里的预订ID号
					$xm = $tmp[$id]['xm'];						//预订人的姓名
					$user_id = $tmp[$id]['user_id'];			//预订人的用户ID号
				}
				/*后面的表格依次根据时间显示预订人名*/
				/*$time_k是时间ID号，$time_v是时间名称*/
				//$table = $table . $this->select_show_name($xm,$user_id,$cd_k,$time_k,$id,$cd_v);
				$starttime = date("H:i",strtotime($time_v['starttime']));
				$endtime = date("H:i",strtotime($time_v['endtime']));
				$table = $table . "<td id='$cd_k-$time_k' class='td_yd' place_id='$cd_k' opentime_id='$time_k' cd_name='$cd_v'  starttime='{$starttime}' endtime='{$endtime}' 'data-toggle'='tooltip' title='场次：{$cd_v} \n 开始时间：{$starttime} \n 结束时间：{$endtime}' >";
				$table = $table . '';	//只生成空的表格框架
				$table = $table . "</td>";
			}
			$table = $table . "</tr>";
		}
		$table = $table . "</tbody></table>";
		return $table;
	}
	
	/*根据系统设置的策略，输出要求输入手机号的HTML框*/
	public function get_input_tel(){
		/*取出本用户的mobile手机号*/
		$tel = '';							//初始化电话号码变量
		$user_model = D("users");
		$input_tel = '';
		$user_id = sp_get_current_userid();	//取得已登陆用户的ID号
		//echo "user_id: $user_id";
		$user_type = $this->chk_admin($user_id);

		switch ($user_type){
			case 1:
				/*是管理员不显示电话输出框*/
				break;
			case 2:
				/*普通用户*/
				$where['id'] = $user_id;
				$tel = $user_model->where($where)->getField('mobile');	//取得手机号
				$input_tel = "<span>联系电话：</span><input type='text' class='user_tel' value='$tel' id='user_tel'  style='width:100px'>";
				break;
		}
		//var_dump($tel);
		return $input_tel;
	}
	
	/*如果是管理员，则显示一个姓名的输入框，预订的时候以这个姓名显示*/
	public function get_input_xm(){
		$input_xm = '';
		$user_id = sp_get_current_userid();	//取得已登陆用户的ID号
		$user_type = $this->chk_admin($user_id);
		switch ($user_type){
			case 1:
				/*是管理员，显示输入姓名的框*/
				$input_xm = "<span>姓名：</span><input type='text' class='user_xm' value='$tel' id='user_xm'>";
				break;
			case 2:
				/*普通用户*/
				
				break;
		}
		return $input_xm;
	}
	
	/*取出预订时显示的消息，取类型为预订通知的消息*/
	public function get_msg2(){
		$msg2_model = D("notice");
		$where = array();
		//$where['id'] = 2;
		$where['parentid'] = 2;
		$result = $msg2_model->where($where)->order('createtime desc')->field('title,msg')->limit(1)->select();
		$msg2 = $result[0]['title'] . "：" . $result[0]['msg'];
		return $msg2;
	}
	
	/* 注意，现在没有使用这个了。直接用的AJAX显示预订表格内信息
	根据系统设置策略，对预订的人名显示进行更改
	普通用户：仅显示本人的姓名，其他人根据策略显示为已预订或姓名
	管理员：显示所有人的姓名
	*/
	public function select_show_name($_xm,$_user_id,$cd_id,$time_id,$id,$cd_name){	//参数为预订人的用户ID号
		//没有预订ID，即没有被预订
		if(empty($id)){	
			$table = $table . "<td class='td_yd can-order' id='$cd_id-$time_id' place_id='$cd_id' opentime_id='$time_id' >";
			$table = $table . $_xm;	//根据系统设置选择显示预订人的姓名还是”已预订“
			$table = $table . "</td>";
			return $table;	
		}
		$user_id = sp_get_current_userid();	//取得已登陆用户的ID号
		//echo "user_id:$user_id";
		$user_type = $this->chk_admin($user_id);
		//echo "user_type:$user_type";
		if ($_user_id == $user_id){
			/*是本用户自己预订的，如果是此用户，则显示自己的姓名*/
			//$xm = $_xm;
			$table = "<td class='td_yd chosen' id='$cd_id-$time_id' place_id='$cd_id' opentime_id='$time_id' yd_id='$id' user_id='$_user_id' >";
			$table = $table . $_xm;	//根据系统设置选择显示预订人的姓名还是”已预订“
			$table = $table . "</td>";
			return $table;		
		}
		if ($user_type == 1){
			//当前用户ID是管理员
			$table = "<td class='td_yd can-order' id='$cd_id-$time_id' place_id='$cd_id' opentime_id='$time_id' yd_id='$id' user_id='$_user_id' >";
			$table = $table . $_xm;	//根据系统设置选择显示预订人的姓名还是”已预订“
			$table = $table . "</td>";
			return $table;
		}
		if ($user_type == -1){
			//当前用户是没有登录的用户
			$table = "<td class='td_yd can-not-order' id='$cd_id-$time_id' place_id='$cd_id' opentime_id='$time_id' yd_id='$id' user_id='$_user_id' >";
			$table = $table . '已订';	//根据系统设置选择显示预订人的姓名还是”已预订“
			$table = $table . "</td>";
			return $table;
		}
		return $table;
	}
	
	/*用户点击单元格后进行ajax_yd，预订函数*/
	/* 返回的ajax json数组定义：
		r['success']=0|1，数字，成功标志
		r['remark']=''，字符串，说明
		r['riqi']=''，日期字符串，预订的日期
		r['cd_name']=''，字符串，场地名称
		r['sj_name']=''，字符串，时间名称
	*/
	public function model_ajax_yd($data){
		/*选判断此场地是否已被预订；再进行预订操作；*/
		$riqi = $data[2];
		$cdid = $data[1];
		$sjid = $data[0];
		$xm = $data[3];
		$tel = $data[4];
		$user = sp_get_current_user();
		$user_id = sp_get_current_userid();
		if($user_id == 0){
			return array();
		}
		$user_type = $this->chk_admin($user_id);
		switch($user_type){
			case 1:
				/*管理员*/
				$tel = '';
				if ($xm == '') {
					$xm = $user['user_nicename'];
				}
				break;
			default:
				/*普通用户*/
				if ($this->sysset_stopuseryd() == 0){
				//根据系统设置，关闭普通用户预订则不允许预订
					return array(0,'系统已关闭预订！');
				}
				$user_nicename = $user['user_nicename'];
				$xm = $user_nicename;
				break;
		}
			//信用分达标、每天的场地限制及时间限制在存储过程里进行判断
			$model = M();
			/*调用存储过程，执行预订*/
			$sqlstr="call proc_yuding('$riqi',$cdid,$sjid,$user_id,'$xm','$tel',@ret_status);";
			$model->execute($sqlstr);
			/*取得out的值*/
			$result=$model->query("select @ret_status");
			$ret_arr = $result[0]["@ret_status"];

		return $ret_arr;
	}
	
	public function model_ajax_refresh($data){
		/*主动刷新客户端的预订展示*/
		/*根据日期查询出预订数据*/
		/*输出：
		array(1) { [1]=> array(8) { ["id"]=> string(1) "1" ["riqi"]=> string(10) "2016-07-16" ["name"]=> string(7) "1号场" ["place_id"]=> string(2) 		"14" ["opentime_id"]=> string(1) "9" ["user_nicename"]=> string(5) "admin" ["xm"]=> string(6) "熊义" ["tel"]=> string(11) "13517246847" } }
		public function get_arr_yd($date){*/
		$riqi = $data[0];
		$result = $this->get_arr_yd($riqi);
		
		//取出系统设置表里的显示姓名类型
		$sysset_model = D("Common/Sysset");
		$showname_type = $sysset_model->get_sysset_valueid("showname");
		
		//echo "showname_type:$showname_type";
		$user_id = sp_get_current_userid();	//取得已登陆用户的ID号
		//echo "user_id: $user_id";
		if ($user_id == 0){
			//用户没有登录
			
		}
		$user_type = $this->chk_admin($user_id);
		foreach($result as $k => $v){
		//遍历数组，echo '键:' . $k . '值' . $v['user_nicename'] . '<br/>';
			//$result[$k]['xm'] = '已预订';	//有效
			if ($result[$k]['user_id'] == $user_id){
				$result[$k]['class'] = 'chosen';		//设置样式类为自己已选
				//$result[$k]['xm'] = $xm;				//是自己，则显示自己的姓名
				continue;
			}
			switch ($showname_type){
				case 0:
					//显示为已预订
					if($user_type == 1){
						//用户类型是管理员
						$result[$k]['class'] = 'can-order';	//设置样式类为可预订
						break;
					}
					//普通用户
					$result[$k]['xm'] = '已订';
					$result[$k]['class'] = 'can-not-order';	//设置样式类为不能预订
					break;
				case 1:
					//显示为用户姓名
					$result[$k]['class'] = 'can-not-order';
					break;
				case 2:
					//显示色块
					$result[$k]['xm'] = '';
					$result[$k]['class'] = 'can-not-order';
					break;
				default:
					//$result[$k]['xm'] = '已订';
					$result[$k]['class'] = 'can-not-order';
			}
			
		}
		return array($result);
	
	}
	
	/*根据日期查询出当天的预订数据，并根据显示预订要求显示对应的名称*/
	/*输出：
array(1) { [1]=> array(8) { ["id"]=> string(1) "1" ["riqi"]=> string(10) "2016-07-16" ["name"]=> string(7) "1号场" ["place_id"]=> string(2) "14" ["opentime_id"]=> string(1) "9" ["user_nicename"]=> string(5) "admin" ["xm"]=> string(6) "熊义" ["tel"]=> string(11) "13517246847" } }
	*/
	public function get_arr_yd($date){
		$where['riqi'] = $date;
		$result = $this->where($where)->join("__PLACE__ ON __RESERVATION__.place_id = __PLACE__.id")->join("__OPENTIME__ ON __RESERVATION__.opentime_id = __OPENTIME__.id")->join("cmf_users ON __RESERVATION__.user_id =cmf_users.id")->getField(
		'reservation.id AS `id`,
		reservation.place_id,place.`name` AS `cd_name`,
		reservation.opentime_id,opentime.starttime,opentime.endtime,
		cmf_users.user_nicename,
		cmf_users.id AS `user_id`,
		reservation.xm,
		reservation.tel'
		);
		//var_dump($result);
		return $result;
	}

	public function chk_admin($user_id){
		/*判断ID号的用户是否为管理员，1表示管理员，2表示普通用户，未登录用户返回空*/
		/*调用存储函数判断*/
		$model = M();
		$sqlstr="select fun_chk_admin($user_id) as chk_admin";
		$result=$model->query($sqlstr);	//array(1) { [0]=> array(1) { ["chk_admin"]=> string(1) "2" } } 
		//var_dump($result);
		//echo "user_type:" . $result[0]['chk_admin'] . "isnull: " . is_null($result[0]['chk_admin']);
		if(empty($result[0]['chk_admin'])){
			return -1;
		}else{
			return $result[0]['chk_admin'];
		}
	}
	
	public function sysset_stopuseryd(){
		//读取系统设置里，是否允许用户预订的字段设置
		//返回1，表示普通用户可预订，返回0，普通用户不能预订
		$sysset_model = D("Common/Sysset");
		$sysset_stopyd = $sysset_model->get_sysset_valueid("stopyd");
		return $sysset_stopyd[0]['stopyd'];
		
	}
	
	/*在存储过程里判断，不需要了
	取得用户的信用分数*/
	function ret_credit($user_id){
		$model = M();
		$sqlstr = "select fun_ret_credit($user_id) as credit";	//as表示别名，不用别名的话，返回的字段是fun_ret_credit(id)这样的形式，不能取得值了
		$result = $model->query($sqlstr);
		if ( empty($result[0]['credit'])){
			return 0;
		}else{
			return $result[0]['credit'];
		}
	}
	
	/*在存储过程里判断，不需要了
	根据系统设置里是否要求使用信用分，来对预订进行判断*/
	function set_credit($user_id,$user_type){
		//$user_id = sp_get_current_userid();	//取得已登陆用户的ID号
		if($user_type == 1){
			//用户是管理员
			return true;
		}
		$user_credit = $this->ret_credit($user_id);	//取得用户的信用分
		
		//取出系统设置表里的是否使用信用分
		$sysset_model = D("Common/Sysset");
		$credit_type = $sysset_model->get_sysset_valueid("credittype");
		
		//取出系统设置里要求的信用分
		$sysset_model = D("Common/Sysset");
		$credit_set = $sysset_model->get_sysset_valueid("credit");
		
		switch($credit_type){
			case 11:	//不使用信用分
				return true;
				break;
			case 12:	//使用信用分
				if ($user_credit >= $credit_set) {
					return true;
				}else{
					return false;
				}
				return false;
				break;
		}
		
	}
	

















}
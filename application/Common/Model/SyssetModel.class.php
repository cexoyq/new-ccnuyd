<?php

/* * 
 * 预订类
	protected $adminsysset_model;
	
	function _initialize() {
		parent::_initialize();
		$this->adminsysset_model =D("Common/Sysset");
	}
	$typename设置名称有：
		stopyd		是否停止预订
		credit		默认的信用分数
		credittype	是否使用信用分
		useryd1		普通用户预订限制1：每人每天订场地的限制
		useryd2		普通用户预订限制2：是否要输入电话才能预订
		openyddate	自动放出预订的日期
		yddate		可以预订的天数
		showname	是否在显示预订信息时显示姓名
		worktime	选择是夏季时间还是冬季时间
 */
namespace Common\Model;
use Common\Model\CommonModel;
class SyssetModel extends CommonModel {

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

	/*取得是否关闭预订参数*/
	/*取得信用类型，是否使用信用分*/
	/*新用户默认信用分数*/
	/*普通用户预订限制1*/
	/*普通用户预订限制2*/
	/*自动放出预订的日期*/
	/*可预订的天数*/
	/*预订后姓名是否展示*/
	/*工作时间选择，有夏季和冬季，需要取Opentime表数据*/
	
	public function get_sysset_valueid($typename){
		/*取得系统类型的值，值是选项的id号*/
		$map = array();
		$map['type'] = $typename;
		$map['parentid'] = 0;
		$valueid = $this->where($map)->getField('valueid');
		return $valueid;
	}





}
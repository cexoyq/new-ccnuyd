<?php

/* * 
 * 菜单
 */
namespace Pyzx\Model;
use Common\Model\CommonModel;
class NoticeModel extends CommonModel {
	protected $tablePrefix = ''; 	//不需要数据库前缀
	protected $_validate = array(
		array('title', 'require', '标题不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		array('msg', 'require', '内容不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		/*array('name','','时间已经存在啦 ！',0,'unique',1), // 在新增的时候验证name字段是否唯一*/
		array('parentid', 'checkParentid', '菜单只支持2级！', 1, 'callback', 1),
   );
    //自动完成
    protected $_auto = array(
            //array(填充字段,填充内容,填充条件,附加规则)
    );

    //验证菜单是否超出2级
    public function checkParentid($parentid) {
        $find = $this->where(array("id" => $parentid))->getField("parentid");
        if ($find) {
            $find2 = $this->where(array("id" => $find))->getField("parentid");
				return false;
        }
        return true;
    }
}
<?php

/* * 
 * 菜单
 */
namespace Pyzx\Model;
use Common\Model\CommonModel;
class PlaceModel extends CommonModel {
	protected $tablePrefix = ''; 	//不需要数据库前缀
	protected $_validate = array(
		array('name', 'require', '名称不能为空！', 1, 'regex', CommonModel:: MODEL_BOTH ),
		//array('name','','场地已经存在啦 ！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		array('parentid', 'checkParentid', '菜单只支持四级！', 1, 'callback', 1),
   );
    //自动完成
    protected $_auto = array(
            //array(填充字段,填充内容,填充条件,附加规则)
    );

    //验证菜单是否超出三级
    public function checkParentid($parentid) {
        $find = $this->where(array("id" => $parentid))->getField("parentid");
        if ($find) {
            $find2 = $this->where(array("id" => $find))->getField("parentid");
            if ($find2) {
                $find3 = $this->where(array("id" => $find2))->getField("parentid");
                if ($find3) {
                    return false;
                }
            }
        }
        return true;
    }
}
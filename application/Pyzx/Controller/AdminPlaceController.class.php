<?php
namespace Pyzx\Controller;
use Common\Controller\AdminbaseController;

class AdminPlaceController extends AdminbaseController{
	
	protected $adminplace_model;
	
    function _initialize() {
        parent::_initialize();
        $this->adminplace_model = D("Place");
		$this->auth_rule_model = D("Common/AuthRule");
    }
	
	public function index() {
    	$_SESSION['admin_place_index']="AdminPlace/index";
        $result = $this->adminplace_model->order(array("listorder" => "ASC"))->select();
        import("Tree");
        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        
        $newmenus=array();
        foreach ($result as $m){
        	$newmenus[$m['id']]=$m;
        	 
        }
        foreach ($result as $n=> $r) {
        	
        	$result[$n]['level'] = $this->_get_level($r['id'], $newmenus);
        	$result[$n]['parentid_node'] = ($r['parentid']) ? ' class="child-of-node-' . $r['parentid'] . '"' : '';
            $result[$n]['str_manage'] = '<a href="' . U("AdminPlace/add", array("parentid" => $r['id'])) . '">'.L('添加场地').'</a> | <a target="_blank" href="' . U("AdminPlace/edit", array("id" => $r['id'])) . '">'.L('EDIT').'</a> | <a class="js-ajax-delete" href="' . U("AdminPlace/delete", array("id" => $r['id']) ). '">'.L('DELETE').'</a> ';

        }

        $tree->init($result);
		/*如果是$str = "<tr id='node-\$id' \$parentid_node>，则子菜单不会显示出来*/
        $str = "<tr id='node-\$id' >
					<td style='padding-left:20px;'><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input input-order'></td>
					<td>\$id</td>
        			<td>\$spacer\$name</td>
					<td>\$str_manage</td>
				</tr>";
        $categorys = $tree->get_tree(0, $str);
        $this->assign("categorys", $categorys);
        $this->display();
	}
	
    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    protected function _get_level($id, $array = array(), $i = 0) {
    
    	if ($array[$id]['parentid']==0 || empty($array[$array[$id]['parentid']]) || $array[$id]['parentid']==$id){
    		return  $i;
    	}else{
    		$i++;
    		return $this->_get_level($array[$id]['parentid'],$array,$i);
    	}
    
    }
	
    //排序
    public function listorders() {
        $status = parent::_listorders($this->adminplace_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }
	
	function add(){
    	import("Tree");
    	$tree = new \Tree();
    	$parentid = intval(I("get.parentid"));
    	$result = $this->adminplace_model->order(array("listorder" => "ASC"))->select();
    	foreach ($result as $r) {
    		$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
    		$array[] = $r;
    	}
    	$str = "<option value='\$id' \$selected>\$spacer \$name</option>";
    	$tree->init($array);
    	$select_categorys = $tree->get_tree(0, $str);
    	$this->assign("select_categorys", $select_categorys);
    	$this->display();
	}
	
    /**
     *  添加
     */
    public function add_post() {
    	if (IS_POST) {
    		if ($this->adminplace_model->create()) {
    			if ($this->adminplace_model->add()!==false) {
    				$name=I("post.name");
    				$mwhere=array("name"=>$name);
    				
    				$find_rule=$this->auth_rule_model->where($mwhere)->find();
    				if(!$find_rule){
    					$this->auth_rule_model->add(array("name"=>$name));//type 1-admin rule;2-user rule
    				}
    				$to=empty($_SESSION['admin_place_index'])?"AdminPlace/index":$_SESSION['admin_place_index'];
    				$this->success("添加成功！", U($to));
    			} else {
    				$this->error("添加失败！");
    			}
    		} else {
    			$this->error($this->adminplace_model->getError());
    		}
    	}
    }
    /**
     *  删除
     */
    public function delete() {
        $id = intval(I("get.id"));
        $count = $this->adminplace_model->where(array("parentid" => $id))->count();
        if ($count > 0) {
            $this->error("该场馆下还有场地，无法删除！");
        }
        if ($this->adminplace_model->delete($id) !== false) {
            $this->success("删除场地/场馆成功！");
        } else {
            $this->error("id:" . $id . "删除失败！");
        }
    }

    /**
     *  编辑
     */
    public function edit() {
        import("Tree");
        $tree = new \Tree();
        $id = intval(I("get.id"));
        $rs = $this->adminplace_model->where(array("id" => $id))->find();
        $result = $this->adminplace_model->order(array("listorder" => "ASC"))->select();
        foreach ($result as $r) {
        	$r['selected'] = $r['id'] == $rs['parentid'] ? 'selected' : '';
        	$array[] = $r;
        }
        $str = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign("data", $rs);
        $this->assign("select_categorys", $select_categorys);
        $this->display();
    }
    
    /**
     *  编辑
     */
    public function edit_post() {
    	if (IS_POST) {
    		if ($this->adminplace_model->create()) {
    			if ($this->adminplace_model->save() !== false) {
    				$name=I("post.name");
    				$mwhere=array("name"=>$name);
    				
    				$find_rule=$this->auth_rule_model->where($mwhere)->find();
    				if(!$find_rule){
    					$this->auth_rule_model->add(array("name"=>$name));//type 1-admin rule;2-user rule
    				}else{
    					$this->auth_rule_model->where($mwhere)->save(array("name"=>$name));//type 1-admin rule;2-user rule
    				}
    				
    				$this->success("更新成功！");
    			} else {
    				$this->error("更新失败！");
    			}
    		} else {
    			$this->error($this->adminplace_model->getError());
    		}
    	}
    }
	
	
	
	
	
	
}

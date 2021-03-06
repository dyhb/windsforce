<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   节点控制器($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

class NodeController extends InitController{

	public function init__(){
		parent::init__();

		if($GLOBALS['___login___']['user_id']!=1){
			$this->E(Dyhb::L('只有用户ID为1的超级管理员才能够访问本页','Controller'));
		}
	}
	
	public function filter_(&$arrMap){
		$arrMap['node_name']=array('like',"%".G::getGpc('node_name')."%");

		$nNodegroupId=G::getGpc('nodegroup_id');
		$sSearch=G::getGpc('search');
		$nNodeParentid=G::getGpc('node_parentid');

		if($nNodegroupId===null){
			$nNodegroupId=0;
		}

		if(!empty($nNodegroupId)){
			$arrMap['nodegroup_id']=$nNodegroupId;
			$this->assign('sNodeName',Dyhb::L('分组','Controller'));
		}else if(empty($sSearch) && !isset($arrMap['node_parentid'])){
			$arrMap['node_parentid']=0;
		}

		if(!empty($nNodeParentid)){
			$arrMap['node_parentid']=$nNodeParentid;
		}
		$this->assign('nNodegroupId',$nNodegroupId);

		Dyhb::cookie('current_node_id',$nNodeParentid);

		if(isset($arrMap['node_parentid'])){
			if(G::isImplementedTo(($oNode=NodeModel::F()->getBynode_id($arrMap['node_parentid'])),'IModel')){
				$this->assign('nNodeLevel',$oNode->node_level+1);
				$this->assign('sNodeName',$oNode->node_name);
			}else{
				$this->assign('nNodeLevel',1);
			}
		}else{
			$this->assign('nNodeLevel',1);
		}
	}

	public function AInsertObject_($oModel){
		$oModel->safeInput();
		$oModel->node_level=$oModel->getLevel();
	}

	public function AUpdateObject_($oModel){
		$oModel->safeInput();
		$oModel->node_level=$oModel->getLevel();
	}

	public function bForeverdelete_(){
		$this->check_appdevelop();
		
		$sId=G::getGpc('id','G');

		$arrIds=explode(',',$sId);
		foreach($arrIds as $nId){
			if($this->is_system_node($nId)){
				$this->E(Dyhb::L('系统节点无法删除','Controller'));
			}

			$nNodes=NodeModel::F('node_parentid=?',$nId)->all()->getCounts();
			$oNode=NodeModel::F('node_id=?',$nId)->query();
			if($nNodes>0){
				$this->E(Dyhb::L('节点%s存在子分类，你无法删除','Controller',null,$oNode->node_name));
			}
		}
	}

	public function get_access(){
		$sAccess=G::getGpc('access','P');

		$nNodeLevel=1;
		if($sAccess=='app'){
			$arrAccessList=NodeModel::F()->where(array('node_level'=>1,'node_parentid'=>0))->asArray()->all()->query();
			$nNodeLevel=2;
		}elseif($sAccess=='module'){
			$arrAccessList=NodeModel::F()->where(array('node_level'=>2))->asArray()->all()->query();
			$nNodeLevel=3;
		}

		$this->assign('arrAccessList',$arrAccessList);
		$this->assign('nNodeLevel',$nNodeLevel);

		$this->display();
	}

	public function getNodegroup(){
		$arrNodegroup=array_merge(array(array('nodegroup_id'=>0,'nodegroup_title'=>Dyhb::L('未分组','Controller'))),
			NodegroupModel::F()->setColumns('nodegroup_id,nodegroup_title')->asArray()->all()->query()
		);
		$this->assign('arrNodegroup',$arrNodegroup);
	}

	public function bIndex_(){
		$this->getNodegroup();
	}

	public function bAdd_(){
		$this->check_appdevelop();
		
		$this->getNodegroup();
	}

	public function bEdit_(){
		$this->check_appdevelop();
		
		$nId=intval(G::getGpc('id','G'));

		if($this->is_system_node($nId)){
			$this->E(Dyhb::L('系统节点无法编辑','Controller'));
		}

		$this->getNodegroup();
	}

	public function bForbid_(){
		$this->check_appdevelop();
		
		$nId=intval(G::getGpc('id','G'));

		if($this->is_system_node($nId)){
			$this->E(Dyhb::L('系统节点无法禁用','Controller'));
		}
	}

	public function clear_menu_cache(){}

	public function aInsert($nId=null){
		$this->clear_menu_cache();
	}

	public function aUpdate($nId=null){
		$this->clear_menu_cache();
	}

	public function aForeverdelete($sId){
		$this->clear_menu_cache();
	}

	public function afterInputChangeAjax($sName=null){
		$this->clear_menu_cache();
	}

	public function sort(){
		$this->check_appdevelop();

		$nSortId=G::getGpc('sort_id');

		if(!empty($nSortId)){
			$arrMap['node_status']=1;
			$arrMap['node_id']=array('in',$nSortId);
			$arrSortList=NodeModel::F()->order('node_sort ASC')->all()->where($arrMap)->query();
		}else{
			$nNodeParentid=G::getGpc('node_parentid');
			if(!empty($nNodeParentid)){
				$nPid=&$nNodeParentid;
			}else{
				$nPid=Dyhb::cookie('current_node_id',$nNodeParentid);;
			}

			if($nPid===null){
				$nPid=0;
			}

			$arrNode=NodeModel::F()->getBynode_id($nPid)->toArray();
			if(isset($arrNode['node_id'])){
				$nLevel=$arrNode['node_level']+1;
			}else{
				$nLevel=1;
			}
			$this->assign('nLevel',$nLevel);

			$arrSortList=NodeModel::F()->where('node_status=1 and node_parentid=? and node_level=?',$nPid,$nLevel)->order('node_sort ASC')->all()->query();
		}
		$this->assign("arrSortList",$arrSortList);

		$this->display();
	}

	public function change_nodegroup(){
		$this->check_appdevelop();
		
		$sId=trim(G::getGpc('id','G'));
		$nNodegroupId=intval(G::getGpc('nodegroup_id','G'));
		
		if(!empty($sId)){
			if($nNodegroupId){
				// 判断节点分组是否存在
				$oNodegroup=NodegroupModel::F('nodegroup_id=?',$nNodegroupId)->getOne();
				if(empty($oNodegroup['nodegroup_id'])){
					$this->E(Dyhb::L('你要移动的节点分组不存在','Controller'));
				}
			}
			
			$arrIds=explode(',', $sId);
			foreach($arrIds as $nId){
				if($this->is_system_node($nId)){
					$this->E(Dyhb::L('系统节点无法移动','Controller'));
				}
				
				$oNode=NodeModel::F('node_id=?',$nId)->getOne();
				$oNode->nodegroup_id=$nNodegroupId;
				$oNode->save(0,'update');
				
				if($oNode->isError()){
					$this->E($oNode->getErrorMessage());
				}
			}

			$this->S(Dyhb::L('移动节点分组成功','Controller'));
		}else{
			$this->E(Dyhb::L('操作项不存在','Controller'));
		}
	}

	public function is_system_node($nId){
		$nId=intval($nId);

		$oNode=NodeModel::F('node_id=?',$nId)->getOne();
		if(empty($oNode['node_id'])){
			return false;
		}

		if($oNode['node_issystem']==1){
			return true;
		}

		return false;
	}

}

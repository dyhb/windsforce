<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   删除一条已发短消息($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

class DelmyonepmController extends Controller{

	public function index($nId='',$nUserId=''){
		$nOldId=$nId;
		
		if(empty($nId)){
			$nId=G::getGpc('id');
		}
		if(empty($nId)){
			$this->E(Dyhb::L('你没有指定要删除的短消息','Controller'));
		}
		
		if(empty($nUserId)){
			$nUserId=$GLOBALS['___login___']['user_id'];
		}
		
		$oPmModel=PmModel::F("pm_id=? AND pm_msgfromid=? AND pm_type='user'",$nId,$nUserId)->query();
		if(empty($oPmModel['pm_id'])){
			$this->E(Dyhb::L('待删除的短消息不存在','Controller'));
		}
		
		$oPmModel->pm_mystatus=0;
		$oPmModel->save(0,'update');
		
		if($oPmModel->isError()){
			$this->E($oPmModel->getErrorMessage());
		}else{
			if(empty($nOldId)){
				$this->S(Dyhb::L('删除短消息成功','Controller'));
			}
		}
	}

	public function myselect(){
		$arrPmIds=G::getGpc('pmid','P');

		if(empty($arrPmIds)){
			$this->E(Dyhb::L('你没有指定要删除的短消息','Controller'));
		}
		
		if($arrPmIds){
			foreach($arrPmIds as $nPmId){
				$this->index($nPmId,$GLOBALS['___login___']['user_id']);
			}
		}
		
		$this->S(Dyhb::L('删除短消息成功','Controller'));
	}

}

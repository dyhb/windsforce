<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   删除我感兴趣的活动($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

class AttentiondeleteController extends Controller{

	public function index(){
		$arrEventid=G::getGpc('key');

		if($arrEventid){
			foreach($arrEventid as $nEventid){
				$oEventattentionuser=EventattentionuserModel::F('event_id=? AND user_id=?',$nEventid,$GLOBALS['___login___']['user_id'])->getOne();

				if(!empty($oEventattentionuser['user_id'])){
					$oDb=Db::RUN();
					
					$sSql="DELETE FROM ".EventattentionuserModel::F()->query()->getTablePrefix()."eventattentionuser WHERE event_id={$nEventid} AND user_id={$GLOBALS['___login___']['user_id']}";

					$oDb->query($sSql);

					// 整理活动感兴趣的数量
					$oEvent=EventModel::F('event_id=?',$nEventid)->getOne();
					if(!empty($oEvent['event_id'])){
						$oEvent->updateEventattentionnum($oEvent['event_id']);

						if($oEvent->isError()){
							$this->E($oEvent->getErrorMessage());
						}
					}
				}
			}
		}else{
			$this->E(Dyhb::L('你没有选择待删除的活动','Controller'));
		}

		$this->S(Dyhb::L('删除我感兴趣的成功','Controller'));
	}

}

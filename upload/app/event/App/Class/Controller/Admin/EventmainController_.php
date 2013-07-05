<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   活动App入口控制器($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

class EventmainController extends InitController{

	public function index($sModel=null,$bDisplay=true){
		$arrOptionData=$GLOBALS['_cache_']['event_option'];

		$this->assign('nId',intval(G::getGpc('id','G')));
		$this->assign('arrOptions',$arrOptionData);
		
		$this->display(Admin_Extend::template('event','eventmain/index'));
	}

	public function update_option(){
		$arrOptions=G::getGpc('options','P');

		foreach($arrOptions as $sKey=>$val){
			$val=trim($val);
			$oOptionModel=EventoptionModel::F('eventoption_name=?',$sKey)->getOne();
			$oOptionModel->eventoption_value=G::html($val);
			$oOptionModel->save(0,'update');

			if($oOptionModel->isError()){
				$this->E($oOptionModel->getErrorMessage());
			}
		}

		if(!Dyhb::classExists('Cache_Extend')){
			require_once(Core_Extend::includeFile('function/Cache_Extend'));
		}
		Cache_Extend::updateCache('event_option');

		$this->S(Dyhb::L('配置更新成功','__APPEVENT_COMMON_LANG__@Controller'));
	}

}

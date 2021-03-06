<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   群组数据相关函数($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

class Groupdata_Extend{

	public static function getGrouphottopic($nNum=0,$nDate=0){
		// 热门帖子时间
		if($nDate==0){
			$nDate=$GLOBALS['_cache_']['group_option']['group_hottopic_date'];
			if($nDate<3600){
				$nDate=3600;
			}
		}

		// 热门帖子数量
		if($nNum==0){
			$nNum=$GLOBALS['_cache_']['group_option']['group_hottopic_num'];
			if($nNum<1){
				$nNum=1;
			}
		}
		
		$arrGrouphottopics=GrouptopicModel::F('create_dateline>? AND grouptopic_status=? AND grouptopic_isaudit=1',CURRENT_TIMESTAMP-$nDate,1)->order('grouptopic_comments DESC')->top($nNum)->get();

		return $arrGrouphottopics;
	}

	public static function getGroupthumbtopic($nNum=0,$nDate=0,$nGroupid=0){
		// 幻灯片帖子时间
		if($nDate==0){
			$nDate=$GLOBALS['_cache_']['group_option']['group_thumbtopic_date'];
			if($nDate<3600){
				$nDate=3600;
			}
		}
		
		// 首页幻灯片帖子数量
		if($nNum==0){
			$nNum=$GLOBALS['_cache_']['group_option']['group_thumbtopic_num'];
			if($nNum<1){
				$nNum=1;
			}
		}

		$arrGroupthumbtopics=GrouptopicModel::F('grouptopic_status=? AND grouptopic_thumb>0 AND grouptopic_isaudit=1 AND create_dateline>?'.($nGroupid>0?' AND group_id='.$nGroupid:''),1,CURRENT_TIMESTAMP-$nDate)->order('create_dateline DESC')->top($nNum)->get();

		return $arrGroupthumbtopics;
	}

	public static function getGrouphotag($nNum=0,$nDate=0){
		// 热门标签时间
		if($nDate==0){
			$nDate=$GLOBALS['_cache_']['group_option']['group_hottag_date'];
			if($nDate<3600){
				$nDate=3600;
			}
		}

		// 热门标签数量
		if($nNum==0){
			$nNum=$GLOBALS['_cache_']['group_option']['group_hottag_num'];
			if($nNum<1){
				$nNum=1;
			}
		}
		
		$arrGrouphottags=GrouptopictagModel::F('create_dateline>?',CURRENT_TIMESTAMP-$nDate)->order('grouptopictag_count DESC')->top($nNum)->get();

		return $arrGrouphottags;
	}

	static public function getGroup($oGroupcategeory){
		if(!$oGroupcategeory['groupcategory_groupmaxnum']){
			$nNum=$GLOBALS['_cache_']['group_option']['group_indexgroupmaxnum'];
			
			if($nNum<1){
				$nNum=1;
			}
		}else{
			$nNum=$oGroupcategeory['groupcategory_groupmaxnum'];
		}

		// 查询条件
		$arrWhere=array();
		$arrWhere['group_status']=1;
		$arrWhere['group_isaudit']=1;

		$arrGroupcategoryindexs=GroupcategoryindexModel::F('groupcategory_id=?',$oGroupcategeory['groupcategory_id'])->getAll();
		if(is_array($arrGroupcategoryindexs)){
			$arrTempdata=array();
			foreach($arrGroupcategoryindexs as $oGroupcategoryindex){
				$arrTempdata[]=$oGroupcategoryindex['group_id'];
			}
			
			$arrWhere['group_id']=array('in',$arrTempdata);
		}else{
			$arrGroups='';
		}

		// 排序
		switch($oGroupcategeory['groupcategory_groupsorttype']){
			case 1:
				$sOrdertype='update_dateline DESC';
				break;
			case 2:
				$sOrdertype='group_totaltodaynum DESC';
				break;
			case 3:
				$sOrdertype='group_usernum DESC';
				break;
			case 0:
			default:
				$sOrdertype='group_isrecommend DESC,create_dateline DESC';
				break;
		}

		if(!isset($arrGroups)){
			$arrGroups=GroupModel::F()->where($arrWhere)->order($sOrdertype)->limit(0,$nNum)->getAll();
		}

		return $arrGroups;
	}

}

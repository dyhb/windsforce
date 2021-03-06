<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   查看帖子控制器($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

/** 导入个人信息函数库 */
require(Core_Extend::includeFile('function/Profile_Extend'));

/** 导入home应用配置值 */
if(!Dyhb::classExists('HomeoptionModel')){
	require_once(WINDSFORCE_PATH.'/app/home/App/Class/Model/HomeoptionModel.class.php');
}

/** 载入home应用配置信息 */
if(!isset($GLOBALS['_cache_']['home_option'])){
	Core_Extend::loadCache('home_option');
}

class ViewController extends Controller{

	protected $_oGrouptopic=null;
	
	public function index(){
		// 获取参数
		$nId=intval(G::getGpc('id','G')); // 帖子ID
		$nPage=intval(G::getGpc('page','G')); // 分页数量
		
		$nSide=Dyhb::cookie('group_grouptopicside')?intval(Dyhb::cookie('group_grouptopicside')):$GLOBALS['_cache_']['group_option']['group_grouptopicside']; // 帖子包含侧边栏
		$nStyle=Dyhb::cookie('group_grouptopicstyle')?intval(Dyhb::cookie('group_grouptopicstyle')):$GLOBALS['_cache_']['group_option']['group_grouptopicstyle']; // 帖子是否为新风格
		
		if(!in_array($nSide,array(1,2))){
			$nSide=1;
		}
		if(!in_array($nStyle,array(1,2))){
			$nStyle=1;
		}

		$oGrouptopic=GrouptopicModel::F('grouptopic_id=? AND grouptopic_status=1',$nId)->getOne();
		if(empty($oGrouptopic['grouptopic_id'])){
			$this->E(Dyhb::L('你访问的主题不存在或已删除','Controller'));
		}

		// 判断帖子小组
		$oGroup=GroupModel::F('group_id=? AND group_status=1 AND group_isaudit=1',$oGrouptopic->group_id)->getOne();

		if(empty($oGroup['group_id'])){
			$this->E(Dyhb::L('小组不存在或在审核中','Controller'));
		}

		try{
			// 验证小组权限
			Groupadmin_Extend::checkGroup($oGroup);
		}catch(Exception $e){
			$this->E($e->getMessage());
		}

		// 判断邮件等外部地址过来的查找评论地址
		$nIsolationCommentid=intval(G::getGpc('isolation_commentid','G'));
		if($nIsolationCommentid){
			$result=GrouptopiccommentModel::getCommenturlByid($nIsolationCommentid);
			if($result===false){
				$this->E(Dyhb::L('该条回帖已被删除、屏蔽或者尚未通过审核','Controller'));
			}

			G::urlGoTo($result);
			exit();
		}
		
		$this->_oGrouptopic=$oGrouptopic;

		// 取得用户是否加入了小组 && 用户在小组中的角色
		$this->get_groupuser($oGrouptopic->group->group_id);

		// 更新点击量
		$oGrouptopic->grouptopic_views=$oGrouptopic->grouptopic_views+1;
		$oGrouptopic->setAutofill(false);
		$oGrouptopic->save(0,'update');

		if($oGrouptopic->isError()){
			$this->E($oGrouptopic->getErrorMessage());
		}

		if($oGrouptopic->grouptopic_thumb>0){
			$oGrouptopic->grouptopic_content='<div class="grouptopicthumb"><div class="grouptopicthumb_title">'.Dyhb::L('主题缩略图','Controller').'</div><p>[attachment]'.$oGrouptopic->grouptopic_thumb.'[/attachment]</p></div>'.$oGrouptopic->grouptopic_content;
		}
		
		$this->assign('oGrouptopic',$oGrouptopic);
		$this->assign('oGroup',$oGrouptopic->group);

		// 读取用户个人资料
		$oUserprofile=UserprofileModel::F('user_id=?',$oGrouptopic->user_id)->getOne();
		
		$this->assign('oUserprofile',$oUserprofile);
		$this->assign('sUsersite',$oUserprofile['userprofile_site']);

		// 读取帖子标签
		$arrGrouptopictags='';
		
		$arrGrouptopictagindexs=GrouptopictagindexModel::F('grouptopic_id=?',$oGrouptopic['grouptopic_id'])->getAll();

		if(is_array($arrGrouptopictagindexs)){
			$arrTagindex=array();
			foreach($arrGrouptopictagindexs as $oGrouptopictagindex){
				$arrTagindex[]=$oGrouptopictagindex['grouptopictag_id'];
			}
			
			if(!empty($arrTagindex)){
				$arrGrouptopictags=GrouptopictagModel::F()->where(array('grouptopictag_id'=>array('in',$arrTagindex)))->order('create_dateline DESC')->getAll();
			}
		}
		
		$this->assign('arrGrouptopictags',$arrGrouptopictags);

		// 判断用户是否回复过帖子
		if($oGrouptopic['grouptopic_onlycommentview']==1){
			$bHavecomment=false;

			if($GLOBALS['___login___']!==false){
				if($oGrouptopic['user_id']==$GLOBALS['___login___']['user_id']){
					$bHavecomment=true;
				}else{
					$oTrygrouptopiccomment=GrouptopiccommentModel::F('user_id=? AND grouptopic_id=?',$GLOBALS['___login___']['user_id'],$oGrouptopic['grouptopic_id'])->getOne();

					if(!empty($oTrygrouptopiccomment['grouptopiccomment_id'])){
						$bHavecomment=true;
					}
				}
			}

			$this->assign('bHavecomment',$bHavecomment);
		}
		
		// 回复列表
		$arrWhere=array();
		$nEverynum=$GLOBALS['_cache_']['group_option']['grouptopic_listcommentnum'];

		$arrWhere['grouptopiccomment_status']=1;
		$arrWhere['grouptopic_id']=$oGrouptopic->grouptopic_id;

		if(!Groupadmin_Extend::checkCommentadminRbac($oGrouptopic->group,array('group@grouptopicadmin@auditcomment'))){
			$arrWhere['grouptopiccomment_auditpass']=1;
		}

		$nTotalComment=GrouptopiccommentModel::F()->where($arrWhere)->all()->getCounts();

		$oPage=Page::RUN($nTotalComment,$nEverynum,G::getGpc('page','G'));

		$arrComments=GrouptopiccommentModel::F()->where($arrWhere)->order('grouptopiccomment_auditpass ASC,grouptopiccomment_stickreply DESC,create_dateline '.($oGrouptopic['grouptopic_ordertype']==1?'DESC':'ASC'))->limit($oPage->returnPageStart(),$nEverynum)->getAll();

		$this->assign('nEverynum',$nEverynum);
		$this->assign('sPageNavbar',$oPage->P('pagination','li','active'));
		$this->assign('arrComments',$arrComments);
		$this->assign('nPage',$nPage);

		// 读取回帖回收站数量
		if(Core_Extend::isAdmin()){
			$nTotalRecyclebinComment=GrouptopiccommentModel::F()->where(array('grouptopic_id'=>$oGrouptopic->grouptopic_id,'grouptopiccomment_status'=>'0'))->all()->getCounts();
			
			$this->assign('nTotalRecyclebinComment',$nTotalRecyclebinComment);
		}

		// 热门帖子
		$arrHotGrouptopics=GrouptopicModel::F('create_dateline>? AND grouptopic_status=? AND group_id=?',CURRENT_TIMESTAMP-86400,1,$oGrouptopic['group_id'])->order('grouptopic_comments DESC')->top($GLOBALS['_cache_']['group_option']['grouptopic_hotnum'])->get();
		
		$this->assign('arrHotGrouptopics',$arrHotGrouptopics);

		// 最新帖子
		$arrNewGrouptopics=GrouptopicModel::F('grouptopic_status=? AND group_id=?',1,$oGrouptopic['group_id'])->order('create_dateline DESC')->limit(0,$GLOBALS['_cache_']['group_option']['grouptopic_newnum'])->getAll();
		
		$this->assign('arrNewGrouptopics',$arrNewGrouptopics);

		// 最新喜欢
		$arrNewGrouptopicloves=GrouptopicloveModel::F('grouptopic_id=?',$oGrouptopic['grouptopic_id'])->order('create_dateline DESC')->limit(0,$GLOBALS['_cache_']['group_option']['grouptopic_lovenum'])->getAll();
		
		$this->assign('arrNewGrouptopicloves',$arrNewGrouptopicloves);

		$this->assign('nStyle',$nStyle);
		$this->assign('nSide',$nSide);
		$this->assign('nDisplaySeccode',$GLOBALS['_cache_']['home_option']['seccode_comment_status']);

		if($nStyle==2){
			$this->display('grouptopic+viewnew');
		}else{
			$this->display('grouptopic+view');
		}
	}

	public function totalTopic($nUserid,$bDigesttopic=false){
		return Groupprofile_Extend::totalTopic($nUserid,$bDigesttopic);
	}

	public function totalComment($nUserid){
		return Groupprofile_Extend::totalComment($nUserid);
	}

	public function get_commentfloor($nIndex,$nEverynum){
		$nPage=intval(G::getGpc('page','G'));

		if($nPage>=2){
			$nIndex=($nPage-1)*$nEverynum+$nIndex;
		}

		switch($nIndex){
			case 1:
				return Dyhb::L('沙发','Controller');
				break;
			case 2:
				return Dyhb::L('板凳','Controller');
				break;
			case 3:
				return Dyhb::L('地板','Controller');
				break;
			default:
				return $nIndex;
				break;
		}
	}

	protected function get_groupuser($nGroupid){
		$nGroupuser=Groupadmin_Extend::getGroupuser($nGroupid);

		$this->assign('nGroupuser',$nGroupuser);
	}
	
	public function view_title_(){
		return $this->_oGrouptopic['grouptopic_title'].' - '.$this->_oGrouptopic->group->group_nikename;
	}

	public function view_keywords_(){
		return $this->view_title_();
	}

	public function view_description_(){
		return $this->view_title_();
	}

}

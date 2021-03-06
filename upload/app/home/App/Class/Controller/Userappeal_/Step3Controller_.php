<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   用户申诉第三步($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

class Step3Controller extends GlobalchildController{

	public function index(){
		if(UserModel::M()->isLogin()){
			$this->U('home://ucenter/index');
		}

		$this->_oParentcontroller->check_seccode(true);
		
		$sRealname=trim(G::getGpc('real_name','P'));
		$sAddress=trim(G::getGpc('address','P'));
		$sIdnumber=trim(G::getGpc('id_number','P'));
		$sAppealemail=trim(G::getGpc('appeal_email','P'));
		$sUserid=trim(G::getGpc('user_id','P'));

		if(empty($sRealname)){
			$this->E(Dyhb::L('真实姓名不能为空','Controller'));
		}

		if(empty($sAppealemail)){
			$this->E(Dyhb::L('申诉结果接收邮箱不能为','Controller'));
		}
		
		Check::RUN();
		if(!Check::C($sAppealemail,'email')){
			$this->E(Dyhb::L('Email格式不正确','Controller'));
		}
		
		$sUserid=G::authcode($sUserid);
		if(empty($sUserid)){
			$this->E(Dyhb::L('页面已过期','Controller'));
		}

		$oUser=UserModel::F('user_email=? AND user_id!=?',$sAppealemail,$sUserid)->getOne();
		if(!empty($oUser->user_id)){
			$this->E(Dyhb::L('该邮箱已经存在','Controller'));
		}

		$oUser=UserModel::F('user_id=?',$sUserid)->getOne();
		if(empty($oUser->user_id)){
			$this->E(Dyhb::L('Email账号不存在','Controller'));
		}

		if($oUser->user_status==0){
			$this->E(Dyhb::L('该账户已经被禁止','Controller'));
		}
		
		$sHashcode=G::randString(32);
		$sUserid=G::authcode($oUser['user_id'],false,null,$GLOBALS['_option_']['appeal_expired']);

		$sGetPasswordUrl=Core_Extend::windsforceOuter('app=home&c=userappeal&a=step4&user_id='.urlencode($sUserid).'&real_name='.urlencode($sRealname).'&address='.urlencode($sAddress).'&id_number='.urlencode($sIdnumber).'&appeal_email='.urlencode($sAppealemail).'&emaillink=1');

		$oMailModel=Dyhb::instance('MailModel');
		$oMailConnect=$oMailModel->getMailConnect();

		$sEmailSubject=$GLOBALS['_option_']['site_name'].Dyhb::L('会员申诉验证码','Controller');
		$sNlbr=$oMailConnect->getIsHtml()===true?'<br/>':"\r\n";
		$sEmailContent='<b>'.Dyhb::L('尊敬的用户','Controller').':</b>'.$sNlbr;
		$sEmailContent.='-----------------------------------------------------'.$sNlbr;
		$sEmailContent.=Dyhb::L('你的登录信息','Controller').':';
		$sEmailContent.=Dyhb::L('用户ID','Controller').'('.$oUser->user_id.')'.$sNlbr;
		$sEmailContent.=Dyhb::L('本次申诉验证码','Controller').':<span style="color:red;font-weight:bold;">'.$sHashcode.'</span>'.$sNlbr;
		$sEmailContent.=Dyhb::L('如果你关闭了申诉页面，你也可以点击下面的链接','Controller').Dyhb::L('申诉链接','Controller').$sNlbr;
		$sEmailContent.="<a href=\"{$sGetPasswordUrl}\">{$sGetPasswordUrl}</a>".$sNlbr.$sNlbr;
		$sEmailContent.="-----------------------------------------------------".$sNlbr;
		$sEmailContent.=Dyhb::L('这是系统用于发送申诉验证码的邮件，请勿回复','Controller').$sNlbr;
		$sEmailContent.=Dyhb::L('申诉验证码过期时间','Controller').':'.$GLOBALS['_option_']['appeal_expired'].Dyhb::L('秒','__COMMON_LANG__@Common').$sNlbr;
		
		$oMailConnect->setEmailTo($sAppealemail);
		$oMailConnect->setEmailSubject($sEmailSubject);
		$oMailConnect->setEmailMessage($sEmailContent);
		$oMailConnect->send();
		if($oMailConnect->isError()){
			$this->E($oMailConnect->getErrorMessage());
		}
		
		$sUserid=G::authcode($oUser['user_id'],false,null,$GLOBALS['_option_']['appeal_expired']);
		$sHashcode=G::authcode($sHashcode,false,null,$GLOBALS['_option_']['appeal_expired']);

		$arrAppealemail=explode('@',$sAppealemail);
		$sAppealemailsite="http://".$arrAppealemail[1];

		$this->assign('sUserid',$sUserid);
		$this->assign('sHashcode',$sHashcode);
		$this->assign('sAppealemailsite',$sAppealemailsite);
		$this->assign('sRealname',$sRealname);
		$this->assign('sAddress',$sAddress);
		$this->assign('sIdnumber',$sIdnumber);
		$this->assign('sAppealemail',$sAppealemail);

		$this->display('userappeal+step3');
	}

	public function step3_title_(){
		return Dyhb::L('填写申诉资料','Controller');
	}

	public function step3_keywords_(){
		return $this->step3_title_();
	}

	public function step3_description_(){
		return $this->step3_title_();
	}

}

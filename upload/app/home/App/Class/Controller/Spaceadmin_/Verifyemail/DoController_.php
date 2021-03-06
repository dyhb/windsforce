<?php
/* [$WindsForce] (C)WindsForce TEAM Since 2012.03.17.
   发送验证信息($Liu.XiangMin)*/

!defined('DYHB_PATH') && exit;

class DoController extends Controller{

	public function index(){
		if($GLOBALS['___login___']['user_isverify']==1){
			$this->E(Dyhb::L('Email已经验证过了，无需重复验证','Controller'));
		}
		
		// 部分验证
		$sEmail=trim($GLOBALS['___login___']['user_email']);
		if(empty($sEmail)){
			$this->E(Dyhb::L('Email地址不能为空','Controller'));
		}

		Check::RUN();
		if(!Check::C($sEmail,'email')){
			$this->E(Dyhb::L('Email格式不正确','Controller'));
		}

		$oUser=UserModel::F('user_email=?',$sEmail)->getOne();
		if(empty($oUser->user_id)){
			$this->E(Dyhb::L('Email账号不存在','Controller'));
		}
		if($oUser->user_status==0){
			$this->E(Dyhb::L('该账户已经被禁止','Controller'));
		}

		// 随机码
		$sUserverifycode=md5(G::randString(32));
		$sUserverifyUrl=Core_Extend::windsforceOuter('app=home&c=spaceadmin&a=checkrevifyemail&email='.urlencode($sEmail).'&hash='.urlencode(G::authcode($sUserverifycode,false,null,$GLOBALS['_option_']['verifyemail_expired'])));

		$oMailModel=Dyhb::instance('MailModel');
		$oMailConnect=$oMailModel->getMailConnect();

		$sEmailSubject=$GLOBALS['_option_']['site_name'].' '.Dyhb::L('Email验证信息','Controller');
		$sNlbr=$oMailConnect->getIsHtml()===true?'<br/>':"\r\n";
		$sEmailContent=Dyhb::L('你需要验证的Email','Controller').':'.$sNlbr;
		$sEmailContent.='Email:'.$sEmail.$sNlbr;
		$sEmailContent.=Dyhb::L('验证Email链接','Controller').':'.$sNlbr;
		$sEmailContent.="<a href=\"{$sUserverifyUrl}\">{$sUserverifyUrl}</a>".$sNlbr.$sNlbr;
		$sEmailContent.="-----------------------------------------------------".$sNlbr;
		$sEmailContent.=Dyhb::L('这是系统用于验证Email的邮件，请勿回复','Controller').$sNlbr;
		$sEmailContent.=Dyhb::L('链接过期时间','Controller').':'.$GLOBALS['_option_']['verifyemail_expired'].Dyhb::L('秒','__COMMON_LANG__@Common').$sNlbr;

		$oMailConnect->setEmailTo($sEmail);
		$oMailConnect->setEmailSubject($sEmailSubject);
		$oMailConnect->setEmailMessage($sEmailContent);
		$oMailConnect->send();
		if($oMailConnect->isError()){
			$this->E($oMailConnect->getErrorMessage());
		}

		// 保存随机码
		$oUser->user_verifycode=$sUserverifycode;
		$oUser->setAutofill(false);
		$oUser->save(0,'update');

		if($oUser->isError()){
			$this->E($oUser->getErrorMessage());
		}

		$this->S(Dyhb::L('邮件已发送到你指定的邮箱','Controller'));
	}

}

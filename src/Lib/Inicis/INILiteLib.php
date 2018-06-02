<?php

require_once('INILiteCls.php');

extract($_POST);
extract($_GET);

$pgid = "CARD";

class INILite
{

	var $m_inipayHome;
	var $m_key;
	var $m_ssl;
	var $m_log;
	var $m_debug;
	var $m_uri;
	var $m_mallencrypt;         // Encrypted
	var $m_qs = array();

	var $m_tid;
	var $m_type;
	var $m_pgId;
	var $m_resultCode;
	var $m_resultMsg;
	var $m_mid;
	var $m_resultprice;
	var $m_currency;
	var $m_pgAuthTime;
	var $m_pgAuthDate;
	var $m_payMethod;

	var $m_buyerName;
	var $m_buyerTel;
	var $m_buyerEmail;

	var $m_goodName;
	var $m_subTid;
	var $m_price;
	var $m_oid;

	var $m_cardNumber;
	var $m_authCode;
	var $m_authDate;
	var $m_cardCode;
	var $m_cardIssuerCode;
	var $m_quotaInterest;
	var $m_cardQuota;

	var $m_directbankcode;
	var $m_rcash_rslt;

	var $m_vacct;
	var $m_vcdbank;
	var $m_nmvacct;
	var $m_nminput;
	var $m_perno;
	var $m_dtinput;

	var $m_nohpp;
	var $m_noars;

	var $m_ocbcardnumber;
	var $m_ocbSaveAuthCode;
	var $m_ocbUseAuthCode;
	var $m_ocbAuthDate;
	var $m_price1;
	var $m_price2;

	var $m_cultureid;

	var $m_pgCancelDate;
	var $m_pgCancelTime;
	var $m_rcash_cancel_noappl;

	var $m_cancelMsg;

	var $m_escrowtype;
	var $m_dlv_ip;
	var $m_dlv_date;
	var $m_dlv_time;
	var $m_dlv_report;
	var $m_dlv_invoice;
	var $m_dlv_name;
	var $m_dlv_excode;
	var $m_dlv_exname;
	var $m_dlv_charge;
	var $m_dlv_invoiceday;
	var $m_dlv_sendname;
	var $m_dlv_sendpost;
	var $m_dlv_sendaddr1;
	var $m_dlv_sendaddr2;
	var $m_dlv_sendtel;
	var $m_dlv_recvname;
	var $m_dlv_recvpost;
	var $m_dlv_recvaddr;
	var $m_dlv_recvtel;
	var $m_dlv_goodscode;
	var $m_dlv_goods;
	var $m_dlv_goodcnt;
	var $m_dcnf_name;

	var $m_resulterrcode;

	//repay param
	var $oldtid;
	var $m_prtc_tid;
	var $m_confirm_price;
	var $m_prtc_remains;
	var $m_prtc_price;
	var $m_prtc_type;


	var $m_sup_price;
	var $m_srvc_price;
	var $m_reg_num;
	var $m_useopt;

	var $m_applnum;
	var $m_cshr_applprice;
	var $m_cshr_supplyprice;
	var $m_cshr_tax;
	var $m_cshr_serviceprice;
	var $m_cshr_type;

	function startAction()
	{
		/*--------------------------------------------------*/
		/* Overhead Operation								*/
		/*--------------------------------------------------*/

		$INIUtil = new INIUtil();

		$INILog = new INILog( $this->m_log, $this->m_debug, $this->m_type );

		if(trim($this->m_mid) == "")
		{
			$this->MakeErrorMsg( ERR_NULL_MID, "필수항목이 누락되었습니다.[MID]");
			return;
		}
		if(!$INILog->StartLog($this->m_inipayHome, $this->m_mid))
		{
			$this->MakeErrorMsg( ERR_OPENLOG, "로그파일을 열수가 없습니다.");
			return;
		}

		/*--------------------------------------------------*/
		/* Http Call										*/
		/*--------------------------------------------------*/
		switch($this->m_type)
		{
			/*************************************************/
			/*				INICIS Securepay											 */
			/*************************************************/
			case("securepay") :
				//Generate TID
				$TID = $INIUtil->MakeTID($this->m_pgId, $this->m_mid, $this->m_tid);
				if(!$TID)
				{
					$err_msg = "TID생성에 실패했습니다.";
					//$err_msg = 'pdId : ' . $this->m_pgId . ', mid : ' . $this->m_mid . ', tid : ' . $this->m_tid . ', TID : ' . $TID;
					$INILog->WriteLog( ERROR, $err_msg );
					$this->MakeErrorMsg( ERR_MAKETID, $err_msg );
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}
				$INILog->WriteLog( INFO, 'Make TID OK '.$this->m_tid );

				//Field Check
				if(trim($this->m_price) == "")
				{
					$err_msg = "[price]필수항목이 누락되었습니다.";
					$INILog->WriteLog( ERROR, $err_msg);
					$this->MakeErrorMsg( ERR_NULL_PRICE, $err_msg );
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}

				//Encrypt
				if(!$INIUtil->MakeEncrypt($this->m_oid."|".$this->m_tid."|".$this->m_price, $this->m_key, $this->m_mallencrypt))
				{
					$err_msg = "암호화에 실패했습니다.";
					$INILog->WriteLog( ERROR, $err_msg );
					$this->MakeErrorMsg( ERR_MAKEENCRYPT, $err_msg );
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}

				//Set Field
				$this->m_uri = HTTP_SECUREPAY_URI;
				$this->m_qs["mid"] = $this->m_mid;
				$this->m_qs["key"] = $this->m_key;
				$this->m_qs["mallencrypt"] = $this->m_mallencrypt;
				$this->m_qs["uid"] = $this->m_uid;
				$this->m_qs["url"] = $this->m_url;
				$this->m_qs["uip"] = $this->m_uip;
				$this->m_qs["paymethod"] = $this->m_payMethod;
				$this->m_qs["goodname"] = $this->m_goodName;
				$this->m_qs["currency"] = $this->m_currency;
				$this->m_qs["buyername"] = $this->m_buyerName;
				$this->m_qs["buyertel"] = $this->m_buyerTel;
				$this->m_qs["buyeremail"] = $this->m_buyerEmail;
				$this->m_qs["sessionkey"] = $this->m_sessionKey;
				$this->m_qs["encrypted"] = $this->m_encrypted;

				$INILog->WriteLog( DEBUG, $this->m_qs );

				//Check Field
				if(!$INIUtil->CheckField( $this->m_qs, $err_code, $err_field))
				{
					$err_msg = "[$err_field]필수항목이 누락되었습니다.";
					$INILog->WriteLog( ERROR, $err_msg );
					$this->MakeErrorMsg( $err_code, $err_msg );
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}
				$INILog->WriteLog( INFO, "Check Field OK" );
				break;
		}

		//initailize httpclient
		$httpclient = new HttpClient( $this->m_ssl );

		//connect
		if( !$httpclient->HttpConnect($INILog) )
		{
			$INILog->WriteLog( ERROR, 'Server Connect Error!!' . $httpclient->getErrorMsg() );
			$resultMsg = $httpclient->getErrorMsg()."서버연결을 할 수가 없습니다.";
			if( $this->m_ssl )
			{
				$resultMsg .= "<br>귀하의 서버는 SSL통신을 지원하지 않습니다. 결제처리파일에서 m_ssl=false로 셋팅하고 시도하세오.";
				$this->MakeErrorMsg( ERR_SSLCONN, $resultMsg);
			}
			else
			{
				$this->MakeErrorMsg( ERR_CONN, $resultMsg);
			}
			$INILog->CloseLog( $this->m_resultMsg );
			return;
		}

		//request
		if( !$httpclient->HttpRequest($this->m_uri, $this->m_qs, $INILog) )
		{
			$INILog->WriteLog( ERROR, 'POST Error!!' . $httpclient->getErrorMsg() );
			$resultMsg = $httpclient->getErrorMsg()."서버에러가 발생했습니다.";
			$this->MakeErrorMsg( ERR_RESPONSE, $resultMsg);
			//NET CANCEL Start---------------------------------
			if( $httpclient->getErrorCode() == READ_TIMEOUT_ERR )
			{
				$INILog->WriteLog( INFO, "Net Cancel Start" );

				//Encrypt
				if(!$INIUtil->MakeEncrypt($this->m_tid, $this->m_key, $this->m_mallencrypt))
				{
					$err_msg = "암호화에 실패했습니다.";
					$INILog->WriteLog( ERROR, $err_msg );
					$this->MakeErrorMsg( ERR_MAKEENCRYPT, $err_msg );
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}

				//Set Field
				$this->m_uri = HTTP_CANCEL_URI;
				unset($this->m_qs);
				$this->m_qs["mid"] = $this->m_mid;
				$this->m_qs["key"] = $this->m_key;
				$this->m_qs["mallencrypt"] = $this->m_mallencrypt;
				$this->m_qs["msg"] = "타임아웃으로 인한 NetCancel";

				if( !$httpclient->HttpConnect($INILog) )
				{
					$INILog->WriteLog( ERROR, 'Server Connect Error!!' . $httpclient->getErrorMsg() );
					$resultMsg = $httpclient->getErrorMsg()."서버연결을 할 수가 없습니다.";
					$this->MakeErrorMsg( ERR_CONN, $resultMsg);
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}
				if( !$httpclient->HttpRequest($this->m_uri, $this->m_qs, $INILog) &&
					($httpclient->getErrorCode() == READ_TIMEOUT_ERR) )
				{
					$INILog->WriteLog( INFO, "Net Cancel FAIL" );
					if( $this->m_type == "securepay")
						$this->MakeErrorMsg( ERR_RESPONSE, "승인여부 확인요망");
					else if( $this->m_type == "cancel")
						$this->MakeErrorMsg( ERR_RESPONSE, "최소여부 확인요망");
				}
				else
				{
					$INILog->WriteLog( INFO, "Net Cancel SUCESS" );
				}
			}
			//NET CANCEL End---------------------------------
			$INILog->CloseLog( $this->m_resultMsg );
			return;
		}

		//error check
		if( $httpclient->getStatus() != 200 )
		{
			$INILog->WriteLog( ERROR, 'Status Error!!' . $httpclient->getStatus().$httpclient->getErrorMsg().$httpclient->getHeaders() );
			$resultMsg = $httpclient->getStatus()."서버에러가 발생했습니다.";
			$this->MakeErrorMsg( ERR_RESPONSE, $resultMsg);

			//NET CANCEL Start---------------------------------
			if( $httpclient->getStatus() != 200 )
			{
				$INILog->WriteLog( INFO, "Net Cancel Start" );

				//Encrypt
				if(!$INIUtil->MakeEncrypt($this->m_tid, $this->m_key, $this->m_mallencrypt))
				{
					$err_msg = "암호화에 실패했습니다.";
					$INILog->WriteLog( ERROR, $err_msg );
					$this->MakeErrorMsg( ERR_MAKEENCRYPT, $err_msg );
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}

				//Set Field
				$this->m_uri = HTTP_CANCEL_URI;
				unset($this->m_qs);
				$this->m_qs["mid"] = $this->m_mid;
				$this->m_qs["key"] = $this->m_key;
				$this->m_qs["mallencrypt"] = $this->m_mallencrypt;
				$this->m_qs["msg"] = "타임아웃으로 인한 NetCancel";

				if( !$httpclient->HttpConnect($INILog) )
				{
					$INILog->WriteLog( ERROR, 'Server Connect Error!!' . $httpclient->getErrorMsg() );
					$resultMsg = $httpclient->getErrorMsg()."서버연결을 할 수가 없습니다.";
					$this->MakeErrorMsg( ERR_CONN, $resultMsg);
					$INILog->CloseLog( $this->m_resultMsg );
					return;
				}
				if( !$httpclient->HttpRequest($this->m_uri, $this->m_qs, $INILog) &&
					($httpclient->getErrorCode() == READ_TIMEOUT_ERR) )
				{
					$INILog->WriteLog( INFO, "Net Cancel FAIL" );
					if( $this->m_type == "securepay")
						$this->MakeErrorMsg( ERR_RESPONSE, "승인여부 확인요망");
					else if( $this->m_type == "cancel")
						$this->MakeErrorMsg( ERR_RESPONSE, "최소여부 확인요망");
				}
				else
				{
					$INILog->WriteLog( INFO, "Net Cancel SUCESS" );
				}
			}
			//NET CANCEL End---------------------------------


			$INILog->CloseLog( $this->m_resultMsg );
			return;
		}


		/*--------------------------------------------------*/
		/* Xml Parsing										*/
		/*--------------------------------------------------*/
		// parsing
		$xml = new XMLParser();
		$xml->xmldata = $xml->Xml2Array($httpclient->getBody());
//		\Cake\Error\Debugger::log($xml->xmldata);

		$INILog->WriteLog( DEBUG, "Parsing OK" );

		// get xml data
		if($this->m_type == "securepay" && $xml->existNData('payment-result'))
		{
			$this->m_resultCode = $xml->getXMLData('resultcode');
			$this->m_resultMsg = mb_convert_encoding($xml->getXMLData('resultmessage'), 'utf-8', 'euc-kr');
			$this->m_mid = $xml->getXMLData('mid');
			$this->m_tid = $xml->getXMLData('tid');

			$this->m_resultprice = $xml->getXMLData('totalprice');
			$this->m_currency = $xml->getXMLData('currency');
			$this->m_payMethod = $xml->getXMLData('paymethod');

		} // End Of securepay
		else
		{
			$this->MakeErrorMsg( ERR_XML, "응답XML이 올바르지 않습니다.");
			$INILog->WriteLog( ERROR, $this->m_resultMsg );
		}

		if( $this->m_resultCode != "00" )
		{
			$arr = split("\]+", $this->m_resultMsg);
			$this->m_resulterrcode = substr($arr[0],1); // []안의 코드만 표시
		}

		$INILog->CloseLog( $this->m_resultMsg );

	} // End of StartAction

	function MakeErrorMsg($err_code, $err_msg)
	{
		$this->m_resultCode = "01";
		$this->m_resulterrcode = $err_code;
		$this->m_resultMsg = "[".$err_code."][".$err_msg."]";
	}
}

?>

<?php

require_once('INILiteCls.php');

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

    function __construct()
    {

    }

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
                $this->m_qs["parentemail"] = $this->m_ParentEmail;
                $this->m_qs["recvname"] = $this->m_recvName;
                $this->m_qs["recvtel"] = $this->m_recvTel;
                $this->m_qs["recvaddr"] = $this->m_recvAddr;
                $this->m_qs["recvpostnum"] = $this->m_recvPostNum;
                $this->m_qs["recvmsg"] = $this->m_recvMsg;
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

            /*************************************************/
            /*				INICIS Cancel													 */
            /*************************************************/
            case("cancel") :
                //Field Check
                if(trim($this->m_tid) == "")
                {
                    $err_msg = "[tid]필수항목이 누락되었습니다.";
                    $INILog->WriteLog( ERROR, $err_msg);
                    $this->MakeErrorMsg( ERR_NULL_TID, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }
                $INILog->WriteLog( DEBUG, "TID: ". $this->m_tid );

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
                $this->m_qs["mid"] = $this->m_mid;
                $this->m_qs["key"] = $this->m_key;
                $this->m_qs["mallencrypt"] = $this->m_mallencrypt;
                $this->m_qs["msg"] = $this->m_cancelMsg;
                $INILog->WriteLog( DEBUG, $this->m_qs );

                break;

            /*************************************************/
            /*				INICIS  부분취소													 */
            /*************************************************/
            case("repay") :
                //Generate TID
                if(!$INIUtil->MakeTID($this->m_pgId, $this->m_mid, $this->m_tid))
                {
                    $err_msg = "TID생성에 실패했습니다.";
                    $INILog->WriteLog( ERROR, $err_msg );
                    $this->MakeErrorMsg( ERR_MAKETID, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }
                //Field Check
                if(trim($this->m_oldtid) == "")
                {
                    $err_msg = "[tid]필수항목이 누락되었습니다.";
                    $INILog->WriteLog( ERROR, $err_msg);
                    $this->MakeErrorMsg( ERR_NULL_TID, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }
                $INILog->WriteLog( DEBUG, "TID: ". $this->m_tid );

                //Encrypt
                if(!$INIUtil->MakeEncrypt($this->m_oldtid."|".$this->m_price."|".$this->m_confirm_price, $this->m_key, $this->m_mallencrypt))
                {
                    $err_msg = "암호화에 실패했습니다.";
                    $INILog->WriteLog( ERROR, $err_msg );
                    $this->MakeErrorMsg( ERR_MAKEENCRYPT, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }

                $INILog->WriteLog( DEBUG, $this->m_mallencrypt );

                //Set Field
                $this->m_uri = HTTP_REPAY_URI;
                $this->m_qs["key"] = $this->m_key;
                $this->m_qs["mid"] = $this->m_mid;
                $this->m_qs["tid"] = $this->m_tid;
                $this->m_qs["uip"] = $this->m_uip;
                $this->m_qs["currency"] = $this->m_currency;
                $this->m_qs["price"] = $this->m_price;
                $this->m_qs["confirm_price"] = $this->m_confirm_price;
                $this->m_qs["buyeremail"] = $this->m_buyerEmail;
                $this->m_qs["mallencrypt"] = $this->m_mallencrypt;
                $INILog->WriteLog( DEBUG, $this->m_qs );

                break;

            /*************************************************/
            /*				INICIS Escrow													 */
            /*************************************************/
            case("escrow") :
                //Field Check
                if(trim($this->m_tid) == "")
                {
                    $err_msg = "[tid]필수항목이 누락되었습니다.";
                    $INILog->WriteLog( ERROR, $err_msg);
                    $this->MakeErrorMsg( ERR_NULL_TID, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }
                $INILog->WriteLog( DEBUG, "TID: ". $this->m_tid );

                //Encrypt
                if(!$INIUtil->MakeEncrypt($this->m_tid, $this->m_key, $this->m_mallencrypt))
                {
                    $err_msg = "암호화에 실패했습니다.";
                    $INILog->WriteLog( ERROR, $err_msg );
                    $this->MakeErrorMsg( ERR_MAKEENCRYPT, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }
                //--------------
                //Common
                //--------------
                $this->m_qs["mid"] = $this->m_mid;
                $this->m_qs["key"] = $this->m_key;
                $this->m_qs["dcnf_name"] = $this->m_dcnf_name;
                $this->m_qs["mallencrypt"] = $this->m_mallencrypt;

                //Set Field
                if( $this->m_escrowtype == "dlv" ) 					$this->m_uri = HTTP_ESCROW_DELIVERY_URI;
                else if( $this->m_escrowtype == "confirm" ) $this->m_uri = HTTP_ESCROW_CONFIRM_URI;
                else if( $this->m_escrowtype == "dcnf" ) 		$this->m_uri = HTTP_ESCROW_DENYCONFIRM_URI;

                $this->m_qs["type"]        = $this->m_escrowtype 			;

                //--------------
                //Delivery
                //--------------
                if( $this->m_escrowtype == "dlv" )
                {
                    $this->m_qs["oid"]        = $this->m_oid;
                    $this->m_qs["dlv_date"]   = $this->m_dlv_date;
                    $this->m_qs["dlv_ip"]   = $this->m_dlv_ip;
                    $this->m_qs["dlv_time"]   = $this->m_dlv_time;
                    $this->m_qs["dlv_report"] = $this->m_dlv_report;
                    $this->m_qs["dlv_invoice"]    = $this->m_dlv_invoice;
                    $this->m_qs["dlv_name"]   = $this->m_dlv_name;
                    $this->m_qs["dlv_excode"] = $this->m_dlv_excode;
                    $this->m_qs["dlv_exname"] = $this->m_dlv_exname;
                    $this->m_qs["dlv_charge"] = $this->m_dlv_charge;
                    $this->m_qs["dlv_invoiceday"]  = $this->m_dlv_invoiceday;
                    $this->m_qs["dlv_sendname"]   = $this->m_dlv_sendname;
                    $this->m_qs["dlv_sendpost"]   = $this->m_dlv_sendpost;
                    $this->m_qs["dlv_sendaddr1"]  = $this->m_dlv_sendaddr1;
                    $this->m_qs["dlv_sendaddr2"]  = $this->m_dlv_sendaddr2;
                    $this->m_qs["dlv_sendtel"]    = $this->m_dlv_sendtel;
                    $this->m_qs["dlv_recvname"]   = $this->m_dlv_recvname;
                    $this->m_qs["dlv_recvpost"]   = $this->m_dlv_recvpost;
                    $this->m_qs["dlv_recvaddr"]   = $this->m_dlv_recvaddr;
                    $this->m_qs["dlv_recvtel"]    = $this->m_dlv_recvtel;
                    $this->m_qs["dlv_goodscode"]  = $this->m_dlv_goodscode;
                    $this->m_qs["dlv_goods"]      = $this->m_dlv_goods;
                    $this->m_qs["dlv_goodcnt"]    = $this->m_dlv_goodcnt;
                    $this->m_qs["price"]	   = $this->m_price;
                }
                //--------------
                //Confirm
                //--------------
                else if( $this->m_escrowtype == "confirm" )
                {
                    $this->m_qs["sessionkey"] = $this->m_sessionKey;
                    $this->m_qs["encrypted"] = $this->m_encrypted;
                }
                //--------------
                //Deny Confirm
                //--------------
                else if( $this->m_escrowtype == "dcnf" )
                {
                    //nothing else;
                }

                $INILog->WriteLog( DEBUG, $this->m_qs );

                break;

            case("receipt") :
                //Generate TID
                if(!$INIUtil->MakeTID($this->m_pgId, $this->m_mid, $this->m_tid))
                {
                    $err_msg = "TID생성에 실패했습니다.";
                    $INILog->WriteLog( ERROR, $err_msg );
                    $this->MakeErrorMsg( ERR_MAKETID, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }
                $INILog->WriteLog( DEBUG, "TID: ". $this->m_tid );

                //Encrypt
                //*merchant key 값을 확인 해야 함
                if(!$INIUtil->MakeEncrypt($this->m_tid."|".$this->m_price."|".$this->m_reg_num, $this->m_key, $this->m_mallencrypt))
                {
                    $err_msg = "암호화에 실패했습니다.";
                    $INILog->WriteLog( ERROR, $err_msg );
                    $this->MakeErrorMsg( ERR_MAKEENCRYPT, $err_msg );
                    $INILog->CloseLog( $this->m_resultMsg );
                    return;
                }

                $INILog->WriteLog( DEBUG, $this->m_mallencrypt );

                //Set Field
                $this->m_uri = HTTP_RECEIPT_URI;
                $this->m_qs["key"] = $this->m_key;
                $this->m_qs["mid"] = $this->m_mid;
                $this->m_qs["uip"] = $this->m_uip;
                $this->m_qs["currency"] = $this->m_currency;
                $this->m_qs["price"] = $this->m_price;
                $this->m_qs["goodname"] = $this->m_goodName;
                $this->m_qs["price"] = $this->m_price;
                $this->m_qs["goodname"] = $this->m_goodName;
                $this->m_qs["sup_price"] = $this->m_sup_price;
                $this->m_qs["tax"] = $this->m_tax;
                $this->m_qs["srvc_price"] = $this->m_srvc_price;
                $this->m_qs["buyername"] = $this->m_buyerName;
                $this->m_qs["buyertel"] = $this->m_buyerTel;
                $this->m_qs["buyeremail"] = $this->m_buyerEmail;
                $this->m_qs["reg_num"] = $this->m_reg_num;
                $this->m_qs["useopt"] = $this->m_useopt;

                $this->m_qs["paymethod"] = $this->m_payMethod;
                $this->m_qs["mallencrypt"] = $this->m_mallencrypt;
                $INILog->WriteLog( DEBUG, $this->m_qs );

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
            $this->m_pgAuthTime = $xml->getXMLData('pgauthtime');
            $this->m_pgAuthDate = $xml->getXMLData('pgauthdate');
            $this->m_payMethod = $xml->getXMLData('paymethod');
            if($xml->existNData('payment-result','buyer'))
            {
                $this->m_buyerName = $xml->getXMLData('buyername');
                $this->m_buyerTel = $xml->getXMLData('buyertel');
                $this->m_buyerEmail = $xml->getXMLData('buyeremail');
            }
            if($xml->existNData('payment-result','paySet'))
            {
                $this->m_goodName = $xml->getXMLData('goodname');
                $this->m_subTid = $xml->getXMLData('subtid');
                $this->m_price = $xml->getXMLData('price');
                $this->m_oid = $xml->getXMLData('oid');

                if($xml->existNData('payment-result','paySet','cardPay'))
                {
                    $this->m_cardNumber = $xml->getXMLData('cardnumber');
                    $this->m_authCode = $xml->getXMLData('authcode');
                    $this->m_authDate = $xml->getXMLData('authdate');
                    $this->m_cardCode = $xml->getXMLData('cardcode');
                    $this->m_cardIssuerCode = $xml->getXMLData('cardissuercode');
                    $this->m_quotaInterest = $xml->getXMLData('quotainterest');
                    $this->m_cardQuota = $xml->getXMLData('cardquota');
                }

                if($xml->existNData('payment-result','paySet','directbankPay'))
                {
                    $this->m_directbankcode = $xml->getXMLData('directbankcode');	// 실시간 은행계좌이체 은행코드
                    $this->m_rcash_rslt = $xml->getXMLData('rcash_rslt');		// 현금영수증 발급코드 (4자리)
                }

                if($xml->existNData('payment-result','paySet','vbankPay'))
                {
                    $this->m_vacct = $xml->getXMLData('vacct');
                    $this->m_vcdbank = $xml->getXMLData('vcdbank');
                    $this->m_nmvacct = $xml->getXMLData('nmvacct');
                    $this->m_nminput = $xml->getXMLData('nminput');
                    $this->m_perno = $xml->getXMLData('perno');
                    $this->m_dtinput = $xml->getXMLData('dtinput');
                }
                if($xml->existNData('payment-result','paySet','hppPay'))
                {
                    $this->m_nohpp = $xml->getXMLData('nohpp');
                }
                if($xml->existNData('payment-result','paySet','ars1588billPay'))
                {
                    $this->m_noars = $xml->getXMLData('noars');
                }
                if($xml->existNData('payment-result','paySet','phonebillPay'))
                {
                    $this->m_noars = $xml->getXMLData('noars');
                }
                if($xml->existNData('payment-result','paySet','ocbpointPay'))
                {
                    $this->m_ocbcardnumber = $xml->getXMLData('ocbcardnumber'); 	// OCB 카드번호
                    $this->m_ocbSaveAuthCode = $xml->getXMLData('ocbsaveauthcode');
                    $this->m_ocbUseAuthCode = $xml->getXMLData('ocbuseauthcode');
                    $this->m_ocbAuthDate = $xml->getXMLData('ocbauthdate');
                    $this->m_price1 = $xml->getXMLData('price1');
                    $this->m_price2 = $xml->getXMLData('price2');
                }
                if($xml->existNData('payment-result','paySet','culturePay'))
                {
                    $this->m_cultureid = $xml->getXMLData('cultureid');		// 컬처랜드 ID, 틴캐시 ID
                }
            }
        } // End Of securepay
        else if($this->m_type == "cancel" && $xml->existNData('cancel-result'))
        {
            $this->m_resultCode = $xml->getXMLData('resultcode');
            $this->m_resultMsg = $xml->getXMLData('resultmessage');
            $this->m_mid = $xml->getXMLData('mid');
            $this->m_tid = $xml->getXMLData('tid');
            $this->m_pgCancelDate = $xml->getXMLData('pgcanceldate');;        // PG 취소 날짜
            $this->m_pgCancelTime = $xml->getXMLData('pgcanceltime');;        // PG 취소 시각
            $this->m_rcash_cancel_noappl= $xml->getXMLData('rcash_cancel_noappl');
        } // End Of cancel
        else if($this->m_type == "repay" && $xml->existNData('payment-result'))
        {
            $this->m_mid = $xml->getXMLData('mid');
            $this->m_tid = $xml->getXMLData('tid');							// 신거래번호
            $this->m_resultCode = $xml->getXMLData('resultcode');			// 결과코드 ("00"이면 성공)
            $this->m_resultMsg = $xml->getXMLData('resultmessage');			// 결과내용 (결과에 대한 설명)
            if($xml->existNData('payment-result','repaySet')){
                $this->m_prtc_tid = $xml->getXMLData('prtc_tid');				// 원거래 번호
                $this->m_prtc_remains = $xml->getXMLData('prtc_remains');		// 최종결제 금액
                $this->m_prtc_price = $xml->getXMLData('prtc_price');			// 부분취소 금액
                $this->m_prtc_type = $xml->getXMLData('prtc_type');				// 부분취소 구분값(또는 재승인 구분값, "0"-재승인, "1"-부분취소)
                $this->m_prtc_cnt = $xml->getXMLData('prtc_cnt');				// 부분취소(재승인) 요청횟수
            }
        } // End Of repay
        else if($this->m_type == "escrow" && $xml->existNData('payment-result'))
        {
            $this->m_resultCode = $xml->getXMLData('resultcode');
            $this->m_resultMsg = $xml->getXMLData('resultmessage');
            $this->m_mid = $xml->getXMLData('mid');
            $this->m_tid = $xml->getXMLData('tid');
            $this->m_pgAuthDate = $xml->getXMLData('pgauthdate');;        // PG 날짜
            $this->m_pgAuthTime = $xml->getXMLData('pgauthtime');;        // PG 시각
            //added 2009.09.17
            if($xml->existNData('payment-result','escrowSet'))
            {
                $this->m_escrowtype = $xml->getXMLData('escrowtype');
            }
        } // End Of Escrow
        else if($this->m_type == "receipt" && $xml->existNData('payment-result'))
        {
            $this->m_tid = $xml->getXMLData('tid');								// 거래번호
            $this->m_resultCode = $xml->getXMLData('resultcode');				// 결과코드 ("00"이면 성공)
            $this->m_resultMsg = $xml->getXMLData('resultmessage');				// 결과내용 (결과에 대한 설명)
            $this->m_pgAuthDate = $xml->getXMLData('pgauthdate');;        // PG 날짜
            $this->m_pgAuthTime = $xml->getXMLData('pgauthtime');;        // PG 시각
            if($xml->existNData('payment-result','paySet','cashPay')){
                $this->m_applnum = $xml->getXMLData('applnum');						// 현금영수증 발행 승인 번호
                $this->m_cshr_applprice = $xml->getXMLData('cshr_applprice');		// 총현금결제 금액 ( or "TotPrice")
                $this->m_cshr_supplyprice = $xml->getXMLData('cshr_supplyprice');	// 공급가
                $this->m_cshr_tax = $xml->getXMLData('cshr_tax');					// 부가세
                $this->m_cshr_serviceprice = $xml->getXMLData('cshr_serviceprice');	// 봉사료
                $this->m_cshr_type = $xml->getXMLData('cshr_type');					// 현금영수증 사용구분
            }
        } // End Of receipt
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

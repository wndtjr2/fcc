<?php

namespace Cake\View\Helper;

use Cake\View\Helper;

class FccTvHelper extends Helper
{
    /*
     * 가격을 화페단위로 출력
     */
    public function currencyStr($price){
        switch(currency){
            case 'WON':
                $price = '￦ '.number_format($price);
                break;
            case 'USD':
                $price = '$ '.$price;
                break;
        }
        return $price;
    }
    /*
     * 주소 정보 출력
     */
    public function addressStr($zipcode,$address,$address2,$city="",$state=""){
        $addr = $zipcode.' '.$address.' '.$address2;
        return $addr;
    }

    //date = date_format($date, n j Y)
    public function dateToKorean($date){
        preg_match('/(\d{1,2})\s(\d{1,2})\s(\d{4})/', $date, $regexDate);
        $date = $regexDate[1]."월 ".$regexDate[2]."일, ".$regexDate[3]."년";
        return $date;
    }
    public function updateQueryStringParameter($uri,$key,$value){
        $separator = strpos($uri,'?') ? '&' : '?';
        if(preg_match("/([?&])$key=.*?(&|$)/",$uri)){
            $uri=preg_replace("/([?&])$key=.*?(&|$)/","$1$key=$value$2",$uri);
        }else{
            $uri.=$separator.$key.'='.$value;
        }
        return $uri;
    }
}

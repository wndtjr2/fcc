<?php
/**
 * Created by PhpStorm.
 * User: hcs
 * Date: 15. 11. 3.
 * Time: 오후 2:21
 */

namespace App\Controller;

use App\Service\DesignerService;
use App\Service\MyOrderService;
use App\Service\AddressService;
use App\Service\TvService;
use Cake\Error\Debugger;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\TableRegistry;

class ModalController extends AppController {

    private $myOrderService;
    private $fccTvService;

    public function initialize() {
        parent::initialize();
        $this->myOrderService = MyOrderService::Instance();
        $this->fccTvService = TvService::Instance();

        $this->Auth->allow();
        $this->Auth->deny(['modalAddress', 'modalAddressNew', 'modalAddressEdit']);
        $this->layout = false;

    }

    public function isAuthorized() {
        return true;
    }

    public function firstLanguage(){
        if($this->request->is('POST')){
            $lang = $this->request->data('lang');
            I18n::locale($lang);
            /*
            $this->Cookie->config([
                'domain' => '.fashioncrowdchallenge.com'
            ]);
            */
            $this->Cookie->write('lang', $lang);
            //Debugger::log($this->Cookie->read('lang'));
            return $this->redirect('/');
        }
    }
    public function accountBye(){}

    public function accountChanged(){}

    public function accountChangeFailed(){}

    public function accountDelete(){}

    public function modalSave(){}

    public function modalOrderReturn(){
        $claimOpenType = $this->myOrderService->getClaimOpenType();
        $this->set("claimOpenType",$claimOpenType);

    }

    public function designerGallery($id){
        if($this->request->is('GET')){
            $images = DesignerService::instance()->getDesignerGalleryImagesById($id);
            $this->set(compact('images'));
        }
    }

    public function modalOrderReturnSuccess(){}

    public function modalOrderCancel(){}

    public function modalOrderCancelComplete(){}

    public function modalAddress(){

        $userInfo = $this->Auth->user();
        $userId = $userInfo['id'];

        $addresslist = AddressService::Instance()->selectAllAddress($userId);

        $countryList = AddressService::Instance()->selectCountryCode();

        $countryArr = array();
        foreach($countryList as $country){
            $countryArr[$country->country_code] = $country->country_name;
        }

        $this->set("countryList", $countryArr);
        $this->set("addressList",$addresslist);
        $this->set("userInfo",$userInfo);
    }

    public function modalCancelOrder(){}

    public function modalAddressNew(){

        $countryList = AddressService::Instance()->selectCountryCode();

        $countryArr = array();
        foreach($countryList as $country){
            $countryArr[$country->country_code] = $country->country_name;
        }

        $userInfo = ($this->Auth->user());

        $this->set(compact('userInfo', 'countryArr'));
    }

    public function modalAddressEdit($addrId){

        $address = AddressService::Instance()->getAddressDetail($addrId, $this->Auth->user('id'));

        $countryList = AddressService::Instance()->selectCountryCode();

        $countryArr = array();
        foreach($countryList as $country){
            $countryArr[$country->country_code] = $country->country_name;
        }

        $userInfo = ($this->Auth->user());

        $this->set(compact('userInfo', 'countryArr', 'address'));
    }

    public function uneedlogin(){}

    public function terms(){}

    public function videoInfo($videoId,$mcVideoId = null){

        $videoProducts = $this->fccTvService->getProductList($videoId);

        $title = $videoProducts['video']->title;
        $description = $videoProducts['video']->video_info;
        $imagePath = FILE_PRD_URI.str_replace("original","1024",$videoProducts['videoMainImage']->path);
        $videoCategory = $this->fccTvService->getVideoCategory();

        $designerInfo = $videoProducts['designerInfo'];

        $videoSchedule = $this->fccTvService->getVideoSchedule($videoId,$mcVideoId);

//        $broadcastDay = date_format($videoSchedule->st_dtm,"d. M Y");
//        $startTime = date_format($videoSchedule->st_dtm,"H:i");
//        $endTime = date_format($videoSchedule->ed_dtm,"H:i");

        $data = array(
            'title' => $title,
            'code' => $videoProducts['video']->code,
            'description' => $description,
            'imagePath' => $imagePath,
            'videoCategory' => $videoCategory,
            'designerInfo' => $designerInfo,
//            'brdDate' =>$broadcastDay,
//            'brdTime' => $startTime." - ".$endTime
        );

        $this->set("data",$data);
    }

    public function commingSoon(){}

    public function alert(){
        $msg = $this->request->data("msg");
        $msg = nl2br($msg);
        $this->set("msg",$msg);
    }

    public function modalPurchase(){
        if($this->request->is('POST')){
            $data = $this->request->data();
            if(!isset($data['product'])){
                throw new BadRequestException('No product information to make alert given.');
            }
            $product = json_decode($data['product']);
            $this->set(compact('product'));
        }
    }
    public function payAlert(){
        $data = $this->request->data();
        $msg = $data["msg"];
        $msg = nl2br($msg);
        if(isset($data['url'])){
            $this->set('url', $data['url']);
        }
        $this->set("msg",$msg);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: brickandmon
 * Date: 2016. 1. 15.
 * Time: 오후 5:06
 */

namespace App\Controller;

use App\Service\AddressService;
use Cake\Error\Debugger;
use Cake\Utility\Xml;
class AddressController extends AppController
{
    private $AddressService;

    private $countryCodeList;

    public function isAuthorized(){
        return true;
    }

    public function initialize(){
        parent::initialize();

        $this->AddressService = AddressService::Instance();

        $countryList = $this->AddressService->selectCountryCode();

        $countryArr = array();
        foreach($countryList as $country){
            $countryArr[$country->country_code] = $country->country_name;
        }

        $this->countryCodeList = $countryArr;
        $this->Auth->allow(['searchAddress','searchAddressProc']);
    }

    public function index(){
        $userId = $this->Auth->user('id');

        $addresslist = $this->AddressService->selectAllAddress($userId);

        $this->set("countryList",$this->countryCodeList);

        $this->set("addressList",$addresslist);
        $this->set("userInfo",$this->Auth->user());
    }
    public function modalAddress(){
        $this->layout = false;
        $this->set("countryList",$this->countryCodeList);
        $this->set("userInfo",$this->Auth->user());
    }


    public function modalEdit(){
        $this->layout = false;
        $addrId = $this->request->query("addrId");
        $userId = $this->Auth->user('id');
        $detailInfo = $this->AddressService->getAddressDetail($addrId,$userId);
        $this->set("data",$detailInfo);
    }

    public function getJson(){
        $this->autoRender = false;

        $userId = $this->Auth->user('id');

        $addresslist = $this->AddressService->selectAllAddress($userId);

        $result = array(
            "addressList"=>$addresslist,
        );

        echo json_encode($result);
    }

    public function add(){
        $this->autoRender = false;

        $userId = $this->Auth->user('id');
        $data = $this->request->data();
        $rtn = $this->AddressService->saveAddress($data,$userId);

        echo json_encode(array('result'=>$rtn));
    }

    public function getDetail(){
        $this->autoRender = false;
        $addrId = $this->request->data('addrId');
        $userId = $this->Auth->user('id');
        $detailInfo = $this->AddressService->getAddressDetail($addrId,$userId);
        echo json_encode($detailInfo);
    }

    public function deleteAddress(){
        $this->autoRender = false;
        $addrId = $this->request->data('addrId');
        $userId = $this->Auth->user('id');
        $rtn = $this->AddressService->deleteAddress($addrId,$userId);
        echo json_encode($rtn);
    }

    public function makedefault(){
        $this->autoRender = false;
        $userId = $this->Auth->user('id');
        $addrId = $this->request->data('addrId');

        $rtn = $this->AddressService->makeDefault($addrId,$userId);
        if($rtn){
            $this->getDetail();
        }else{
            echo json_encode($rtn);
        }
    }
    public function searchAddress(){
        $this->layout = false;
    }
    public function searchAddressProc(){
        $this->autoRender = false;
        $rtn = [];
        if($this->request->is('post')){
            $response = $this->AddressService->searchAddress($this->request->data());

            if($response!=''){
                $rtn = Xml::toArray(Xml::build($response));

            }
        }
        echo json_encode($rtn);
    }
}
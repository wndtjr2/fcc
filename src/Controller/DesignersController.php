<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 16. 4. 11.
 * Time: 오후 4:44
 */

namespace App\Controller;



use App\Service\DesignerService;
use App\Service\ProductService;
use App\Service\EncryptService;

class DesignersController extends AppController
{

    private $DesignerService;

    private $ProductService;

    public function initialize(){
        parent::initialize();

        $this->Auth->allow();

        $this->DesignerService = DesignerService::instance();
        $this->ProductService = ProductService::Instance();
        $this->set('designersSelect','is-select');
    }

    /**
     * 디자이너 리스트
     */
    public function index(){

        $imageSize = 'murl';

        $request = $this->request;

        if($request->is("mobile")){
            $imageSize = 'murl';
        }

        $page = 1;
        if(isset($this->request->query['page'])) {
            $page = ($this->request->query['page'] != '') ? $this->request->query['page'] : 1;
        }
        $limit = 30;

        $brands = $this->DesignerService->getBrandList($imageSize,$page,$limit);

        $this->set("limit",$limit);
        $this->set("brands",$brands['designerInfo']);
        $this->set("pagination",$brands['pagination']);
        //TODO 디자이너 상품 리스트

    }

    /**
     * 모바일 전용
     * 디자이너 페이징
     */
    public function getNextPage(){
        $this->autoRender = false;

        $page = $this->request->data('page');
        $limit = $this->request->data('limit');
        $imageSize = 'surl';

        $designers = $this->DesignerService->getBrandList($imageSize,$page,$limit);

        echo json_encode($designers['designerInfo']);
    }

    /**
     * 디자이너 상세
     */
    public function detail(){
        $designerId = $this->request->query['designerId'];
        if($designerId != "" && $designerId !=null){

            $encryptedId = base64_decode($designerId);

            $productImageSize = 'surl';
            $mainImageSize = 'url';

            $request = $this->request;

            if($request->is("mobile")){
                $mainImageSize = 'murl';
            }

            $collectionName = 'collection';
            $projectName = 'project';


            $id = EncryptService::Instance()->decrypt($encryptedId);
            $designer = $this->DesignerService->getDesignerDetail($id, $productImageSize, $mainImageSize);
            $collection = $designer['gallery'][$collectionName];
            $project = $designer['gallery'][$projectName];

            /*
             * 카드 리스트
             */

            $productList = $this->ProductService->getCardProductList($this->request->query,$this->RequestHandler->ismobile());


            $this->set(compact('designer', 'productImageSize', 'mainImageSize', 'collection', 'project','productList','designerId'));

        } else{
            if(isset($_SERVER['HTTP_REFERER'])){
                $this->redirect($_SERVER['HTTP_REFERER']);
            }else{
                $this->redirect('/');
            }
        }
    }



}
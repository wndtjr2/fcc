<?php
/**
 * Created by PhpStorm.
 * User: eric
 * Date: 16. 4. 11.
 * Time: 오후 4:46
 */

namespace App\Service;

use Cake\Cache\Cache;
use Cake\Error\Debugger;
use Cake\ORM\TableRegistry;

class DesignerService
{

    /**
     * @var \Cake\ORM\Table
     */
    private $Designer;

    /**
     * @var \Cake\ORM\Table
     */
    private $ChImage;

    private $McDesignerCategory;

    public function __construct(){

        $this->Designer = TableRegistry::get('McDesigner');

        $this->ChImage = TableRegistry::get('ChImage');

        $this->McDesignerCategory = TableRegistry::get("McDesignerCategory");
    }

    public static function instance(){
        static $inst = null;
        if($inst === null){
            $inst = new DesignerService();
        }
        return $inst;
    }

    public function getBrandList($imageSize,$page,$limit){
        $cacheCheck = true;
        $result =false;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }
        if($cacheCheck==true) {
            $result = Cache::read("brands" . $imageSize . $page . $limit, "products");
        }
        if($result === false || $result==null || $cacheCheck==false) {
            $imageSizeWithModel = 'ChImageFile.' . $imageSize;

            $contain = [
                'ChImage' => [
                    'ChImageFile' => function ($q) use ($imageSizeWithModel) {
                        return $q->select([
                            'ChImageFile.image_id',
                            $imageSizeWithModel
                        ]);
                    }]
            ];

            $designers = $this->Designer->find()
                ->contain($contain)
                //->select($select)
                ->order(['name' => 'ASC'])->page($page, $limit);


            $designerInfo = array();

            $designerCategory = $this->getDesignerCategory();


            foreach ($designers as $designer) {
                $designerInfo[] = array(
                    'id' => base64_encode($designer->encrypt_id),
                    'name' => $designer->name,
                    'image' => $designer->ch_image->ch_image_file[0]->{$imageSize},
                    'summary' => $designer->summary,
                    'category' => (isset($designerCategory[$designer->id]))?$designerCategory[$designer->id]:array(),
                );
            }

            $scale = 10;

            $totalCount = $designers = $this->Designer->find()->contain($contain)->order(['name' => 'ASC'])->count();
            $totalPageNo = ceil($totalCount / $limit);
            $start_page = ((ceil($page / $scale) - 1) * $scale) + 1;
            $end_page = $start_page + $scale - 1;
            $prev_page = ($start_page > 1) ? $start_page - 1 : 0;
            $next_page = ($totalPageNo > $end_page) ? ($end_page + 1) : 0;
            $end_page = ($end_page >= $totalPageNo) ? $totalPageNo : $end_page;

            $scopeArr = array();
            for ($i = $start_page; $i <= $end_page; $i++) {
                $scopeArr[] = $i;
            }

            $pageArray = array(
                'totalCount' => $totalCount,
                'prev' => $prev_page,
                'next' => $next_page,
                'scope' => $scopeArr,
                'nowPage' => $page
            );
            $result['designerInfo'] = $designerInfo;
            $result['pagination'] = $pageArray;

            if($cacheCheck==true) {
                Cache::write("brands" . $imageSize . $page . $limit, $result, "products");
            }
        }

        return $result;
    }

    public function getDesignerDetail($designerId, $productImageSize, $mainImageSize){

        $cacheCheck = true;
        $designer =false;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }
        if($cacheCheck==true) {
            $designer = Cache::read("designer" . $designerId . $productImageSize . $mainImageSize, "designer");
        }
        if($designer === false || $designer == null || $cacheCheck==false) {
            $this->Designer->hasOne('ChImage', [
                'foreignKey' => 'image_id',
                'bindingKey' => 'main_image_id'
            ]);
            $contain = [
                'ChImage' => [
                    'ChImageFile' => function ($q) use ($mainImageSize) {
                        return $q
                            ->select([
                                'ChImageFile.image_id',
                                'ChImageFile.' . $mainImageSize,
                            ])
                            ->order(['seq' => 'ASC']);
                    }
                ],
                'McDesignerGallery' => function ($q) use ($productImageSize) {
                    return $q
                        ->order(['collection' => 'DESC']);
                }
            ];
            $select = [
                'McDesigner.id',
                'McDesigner.name',
                'McDesigner.contents',
                'McDesigner.summury',
                'McDesigner.main_image_id',
                'ChImage.image_id',
            ];
            $designers = $this->Designer->find()
                ->select($select)
                ->contain($contain)
                ->where(['id' => $designerId])
                ->first();

            //sort object into array
            if(!is_null($designers)){
                $collection = $this->sortGallery($designers, 'collection');
                $project = $this->sortGallery($designers, 'project');

                $designer = [
                    'id' => $designers->id,
                    'name' => $designers->name,
                    'contents' => $designers->contents,
                    'main_image' => $designers->ch_image->ch_image_file[0]->{$mainImageSize},
                    'gallery' => [
                        'collection' => $collection,
                        'project' => $project,
                    ]
                ];
                if($cacheCheck==true) {
                    Cache::write("designer" . $designerId . $productImageSize . $mainImageSize, $designer, "designer");
                }
            }
        }
        return $designer;
    }

    public function sortGallery($designer,$galleryTypes){

        $result = array();
        $types = array();
        $typeIndex = 0;
        $typesMap = array();
        $names = array();
        $nameIndex = 0;
        //$imageIndex = 0;

        foreach ($designer->mc_designer_gallery as $gallery){
            if(strtolower($galleryTypes) == strtolower($gallery->gallery_type)) {
                $type = $gallery->gallery_type;

                if (!in_array($type, $types)) {
                    $types[] = $type;
                    $typesMap[$type] = $typeIndex;
                    $result = array('type' => $type, 'type2' => array());
                    $typeIndex++;
                }
                $name = $gallery->collection;
                if (!in_array($name, $names)) {
                    $names[] = $name;
                    $namesMap[$name] = $nameIndex;
                    if(strtolower($galleryTypes) == 'collection'){
                        $result['type2'][$namesMap[$name]]['name'] = $name;
                    }
                    $result['type2'][$namesMap[$name]]['id'] = $gallery->id;
                    $nameIndex++;
                }
//                foreach($gallery->ch_image->ch_image_file as $image){
//                    $result['type2'][$namesMap[$name]]['image'][] = $image->$imageSize;
//                    $imageIndex++;
//                }
            }
        }
        return $result;
    }

    public function getDesignerGalleryImagesById($id){
        $cacheCheck = true;
        $images =false;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }
        if($cacheCheck==true) {
            $images = Cache::read("Gellery" . $id, "designer");
        }
        if($images===false || $images==null || $cacheCheck==false) {
            $GalleryTable = TableRegistry::get('McDesignerGallery');
            $GalleryTable->primaryKey('image_id');

            $contain = [
                'ChImage' => [
                    'ChImageFile' => function ($q) {
                        return $q->where(['type' => 'image'])->order(['seq' => 'ASC']);
                    }
                ]
            ];
            $gallery = $GalleryTable->find()->contain($contain)->where(['id' => $id])->first();

            //sort gallery

            $images = array();
            if (!is_null($gallery)) {
                foreach ($gallery->ch_image->ch_image_file as $imageFile) {
                    $images[] = $imageFile->lurl;
                }
            }
            if($cacheCheck==true) {
                Cache::write("Gellery" . $id, $images, "designer");
            }
        }
        return $images;

    }

    public function getDesignerCategory(){

        $cacheCheck = true;
        if(!CACHEDUSE) {
            $cacheCheck=false;
        }

        $result = array();
        if($cacheCheck==true) {
            $result = Cache::read("designerCategoryInfo", "designer");
        }
        if($result === false || $result == null || $cacheCheck==false) {
            $designerCategoryList = $this->McDesignerCategory->find();
            $categoryIds = array();
            $enc = EncryptService::Instance()->Instance();
            foreach ($designerCategoryList as $category) {
                $categoryIds[$category->designer_id][] = base64_encode($enc->encrypt($category->category1));
                $categoryIds[$category->designer_id] = array_unique($categoryIds[$category->designer_id]);
            }

            $categorys = ProductService::Instance()->getCategory();

            $sortCategory = array();
            foreach ($categorys as $cate) {
                $sortCategory[$cate['id']] = $cate['name'];
            }
            foreach ($categoryIds as $key => $val) {
                $temp = array();
                foreach ($val as $categoryKey) {
                    $temp[] = $sortCategory[$categoryKey];
                }
                $result[$key] = $temp;
            }

            if($cacheCheck==true) {
                Cache::write("designerCategoryInfo", $result, "designer");
            }
        }
        return $result;
    }
}
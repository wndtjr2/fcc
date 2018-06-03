<?php

namespace App\Util;

/**
 * 이미지 유틸
 * 임시 이미지, 리사이징 등을 처리 한다.
 */
use Cake\Core\Exception\Exception;
use Cake\Error\Debugger;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Imagine\Gmagick\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Cake\Utility\String;
class ImageUtil {

    private static $allowexts = ['jpg', 'jpeg', 'gif', 'png', 'tif', 'tiff'];
    private static $rotateExts = ['jpg', 'jpeg', 'tif', 'tiff'];

    /**
     * @param $tempImage
     * @param $targetImage
     * @param $size
     */
    private static function resizeImage($tempImage, $targetImage, $size){
        //기존 이미지 조회
        $imagine = new Imagine();
        $mImage = $imagine->open($tempImage);
        // resize
        $width = $mImage->getSize()->getWidth();
        $height = $mImage->getSize()->getHeight();

        /*
        if($width >= $height) {
            if ($width > $size || $width < $size) {
                $height = ($size / $width) * $height;
                $mImage->resize(new Box($size, $height));
            }
        }
        else if($width <= $height) {
            if ($height > $size || $height < $size) {
                $width = ($size / $height) * $width;
                $mImage->resize(new Box($width, $size));
            }
        }
        */
        if ($height > $size || $height < $size) {
            $width = ($size / $height) * $width;
            $mImage->resize(new Box($width, $size));
        }

        $mImage->save($targetImage);
    }
    private static function autoRotateImage($imageName) {
        try{
            $exif = @exif_read_data($imageName);
            if (!empty($exif['Orientation'])) {
                $angle = 0;

                switch ($exif['Orientation']) {
                    case 3:
                        $angle = 180;
                        break;

                    case 6:
                        $angle = -90;
                        break;

                    case 8:
                        $angle = 90;
                        break;
                }

                if (preg_match("/.*(\.jpg|\.jpeg)/i", $imageName)) {
                    $source = imagecreatefromjpeg($imageName);

                } else {
                    $source = imagecreatefrompng($imageName);

                }
                $source = @imagerotate($source, $angle, 0);
                @imagejpeg($source, $imageName);

            }
        }catch (Exception $e){
            Debugger::log($e->getMessage());
        }

    }
    /**
     * @param $tempImage
     * @param $targetImage
     * @param $size
     */
    private static function cropImage($tempImage, $targetImage, $size){
        //기존 이미지 조회
        $imagine = new Imagine();
        $mImage = $imagine->open($tempImage);

        $width = $mImage->getSize()->getWidth();
        $height = $mImage->getSize()->getHeight();

        if($width>=$height){
            $cx = ($width-$height) / 2;
            $cy = 0;
            $width = $height;
        }else{
            $cx = 0;
            $cy = ($height-$width) / 2;
            $height = $width;
        }

        $mImage->crop(new Point($cx,$cy),new Box($width,$height));
        if($width >= $height) {
            if ($width > $size || $width < $size) {
                $height = ($size / $width) * $height;
                $mImage->resize(new Box($size, $height));
            }
        }
        else if($width <= $height) {
            if ($height > $size || $height < $size) {
                $width = ($size / $height) * $width;
                $mImage->resize(new Box($width, $size));
            }
        }
        $mImage->save($targetImage);
    }
    /**
     * @param $file
     * @return string
     */
    public static function createTempImage($file , $fileName){
        $ext = mb_strtolower($fileName->ext());
        if (in_array($ext, self::$allowexts)) {
            $tempFileId = String::uuid() . '.' . $ext;
            if(!move_uploaded_file($file->path, TMP . DS . $tempFileId)){
                throw new InternalErrorException("can't move the file...");
            }
            if (in_array($ext, self::$rotateExts)) {
                self::autoRotateImage(TMP . DS . $tempFileId);
            }
        }else{
            throw new BadRequestException('The file extension is not supported ext :' . $ext);
        }
        return $tempFileId;
    }

    private static function deleteTempImage($imageId){
        //Debugger::log(TMP . $imageId);
        unlink(TMP . $imageId);
    }

    public static function deleteBeforeImage($imageId){
        //Debugger::log(WWW_ROOT . 'img' . DS . $imageId);
        @unlink(WWW_ROOT . 'img' . DS . $imageId);
    }
    public static function deleteImage($path){
        @unlink(FILE_UPLOAD_DIR . $path);
        @unlink(FILE_UPLOAD_DIR . $path.'-130');
        @unlink(FILE_UPLOAD_DIR . $path.'-crop');
    }
    /**
     * 이미지를 원본, 100px, 500px 사이즈로 복사 한다.
     * @param string $imageId 이미지 아이디
     * @param string $type 복사 종류
     * @return array
     */
    public static function resizeImagesSet($imageId , $type){
        //대상 이미지
        $tmpImagePath =  TMP . $imageId;

        //이미지 아이디 생성
        $findDot = strrpos($imageId, '.');
        $mImageId = substr($imageId, 0, $findDot) . substr($imageId, $findDot). '-crop';
        $sImageId = substr($imageId, 0, $findDot) . substr($imageId, $findDot). '-130';

        //생성 이미지 prefix 생성
        $prefix =  $type . DS . date('Y') . DS . date('M') . DS . date('d');

        //이미지 Path 생성
        $imagePath = FILE_UPLOAD_DIR . $prefix . DS . $imageId;
        $mediumImagePath = FILE_UPLOAD_DIR . $prefix . DS . $mImageId;
        $smallImagePath = FILE_UPLOAD_DIR . $prefix . DS . $sImageId;

        self::makeDir($prefix);

        //원본 복사
        copy($tmpImagePath , $imagePath);
        // 500 크기로 리사이즈
        //self::resizeImage($tmpImagePath ,$mediumImagePath ,  500);

        // 100 크기로 리사이즈
        self::resizeImage($tmpImagePath ,$smallImagePath,  480);

        self::cropImage($tmpImagePath ,$mediumImagePath,  200);

        //임시 이미지 파일 삭제
        self::deleteTempImage($imageId);
        //3개를 모두 반환
        return [
            'original' =>  $prefix . DS . $imageId,
            'medium' =>  $prefix . DS . $mImageId,
            'small' =>  $prefix . DS . $sImageId
        ];
    }

    public static function resizeBoardImage($imageId, $type){
        $tmpImagePath = TMP . $imageId;

        $findDot = strrpos($imageId, '.');
        $mImageId = substr($imageId, 0, $findDot) . substr($imageId, $findDot);

        $prefix =  $type . DS . date('Y') . DS . date('M') . DS . date('d');

        $mImagePath = FILE_UPLOAD_DIR . $prefix . DS . $mImageId;

        self::makeDir($prefix);

        copy($tmpImagePath , $mImagePath);
        //500 resolution resizing
        self::resizeImage($tmpImagePath ,$mImagePath ,  500);

        return [
            'original' =>  $prefix . DS . $imageId,
            'tmp' => $tmpImagePath
        ];
    }

    /**
     * 이미지를 150px 사이즈로 복사 한다.
     * @param $imageId 이미지 아이디
     * @param $type 복사 종류
     * @return string
     */
    public static function resizeCommentImagesSet($imageId , $type){
        //대상 이미지
        $tmpImagePath =  TMP .$imageId;

        //이미지 아이디 생성
        $findDot = strrpos($imageId, '.');
        $sImageId = substr($imageId, 0, $findDot) . '-150' . substr($imageId, $findDot);

        //생성 이미지 prefix 생성
        $prefix =  $type . DS . date('Y') . DS . date('M') . DS . date('d');

        //이미지 Path 생성
        $imagePath = FILE_UPLOAD_DIR . $prefix . DS . $imageId;
        $smallImagePath = FILE_UPLOAD_DIR . $prefix . DS . $sImageId;

        self::makeDir($prefix);

        //원본 복사
        //copy($tmpImagePath , $imagePath);
        // 150 크기로 리사이즈
        self::resizeImage($tmpImagePath ,$smallImagePath,  150);

        //임시 이미지 파일 삭제
        //self::deleteTempImage($imageId);
        return $prefix . DS . $sImageId;
    }
    /**
     * @param $prefix
     */
    private static function makeDir($prefix)
    {
        $directories = explode(DS, FILE_UPLOAD_DIR . $prefix);
        $count = count($directories);
        $currentDirectory = '/';

        for ($i = 1; $i < $count; $i++) {
            if (!$directories[$i]) {
                continue;
            };
            if (strlen($currentDirectory) > 1)
                $currentDirectory = $currentDirectory . DS;

            $currentDirectory = $currentDirectory . $directories[$i];

            if (!file_exists($currentDirectory)) {
                if (!mkdir($currentDirectory, 0777)) {
                    break;
                }
            }
        }

    }

    public static function UploadImages($imageId , $type){
        //대상 이미지
        $tmpImagePath =  TMP . $imageId;
        //생성 이미지 prefix 생성
        $prefix =  $type . DS . date('Y') . DS . date('M') . DS . date('d');

        //이미지 Path 생성
        $imagePath = FILE_UPLOAD_DIR . $prefix . DS . $imageId;

        self::makeDir($prefix);

        //원본 복사
        copy($tmpImagePath , $imagePath);

        return [
            'original' =>  $prefix . DS . $imageId
        ];
    }
}

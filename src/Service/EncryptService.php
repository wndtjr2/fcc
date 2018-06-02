<?php
/**
 * Created by PhpStorm.
 * User: swoogi
 * Date: 15. 1. 21.
 * Time: 오후 6:09
 */

namespace App\Service;


interface EncryptInterFace {
    public function encrypt($param);
    public function decrypt($param);
}

class EncryptService implements EncryptInterFace
{
    /**
     * @param Controller $controller
     * @return \Cake\Network\Response|void
     */
    private $key;
    private $iv;

    public static function Instance()
    {
        /**
         * 외부 컨트롤러에서 호출시 서비스 객체 반환
         */
        static $inst = null;
        if ($inst === null) {
            $inst = new EncryptService();
        }
        return $inst;
    }

    public function __construct()
    {
        $this->key = pack('C*',
            0x88, 0xE3, 0x4F, 0x8F,
            0x08, 0x17, 0x79, 0xF1,
            0xE9, 0xF3, 0x94, 0x37,
            0x0A, 0xD4, 0x05, 0x89
        );

        $this->iv = pack('C*',
            0x26, 0x8D, 0x66, 0xA7,
            0x35, 0xA8, 0x1A, 0x81,
            0x6F, 0xBA, 0xD9, 0xFA,
            0x36, 0x16, 0x25, 0x01
        );
    }

    private function toPkcs7($value)
    {
        $val = $value;
        if (is_null($val))
            $val = '';
        $padSize = 16 - (strlen($val) % 16);
        return $val . str_repeat(chr($padSize), $padSize);
    }
    private function fromPkcs7 ($value)
    {
        $valueLen = strlen ($value) ;
        if ( $valueLen % 16 > 0 )
            $value = "";
        $padSize = ord ($value{$valueLen - 1}) ;
        if ( ($padSize < 1) or ($padSize > 16) )
            $value = "";
        //Check padding.
        if($value != ''){
            for ($i = 0; $i < $padSize; $i++)
            {
                if ( ord ($value{$valueLen - $i - 1}) != $padSize )
                    $value = "";
            }
        }
        return substr ($value, 0, $valueLen - $padSize) ;
    }

    public function encrypt($param)
    {
        $val = $this->toPkcs7($param);
        $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $val, MCRYPT_MODE_CBC, $this->iv);
        return base64_encode($output);
    }

    public function decrypt($param)
    {
        if($param=="" || $param==" "){
            return "";
        }
        $sParam = $param;
        $val = base64_decode($sParam);
        $rtn = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $val, MCRYPT_MODE_CBC, $this->iv);
        return $this->fromPkcs7($rtn);
    }
}
<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * LinkedIn 서비스 인터페이스
 * User: Eric
 * Date: 15. 6. 24.
 * Time: 오후 1:54
 */

use Cake\Auth;
use Cake\Network\Session;
use Google_Client;

/**
 * 사용자 조회 서비스
 * Class UserService
 * @package App\Service
 */
class YoutubeService {

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new YoutubeService();
        }
        return $inst;
    }

    public function getSubscribers(){
        $google_client = new \Google_Client();
        $channelId = "UCzZbPlbim0tlbMvSD0zO_5g";
        $devkey = "AIzaSyAAdSmPp65YTlZ4KRUAxZ4uuYyd33-Aw_U";
        $google_client->setDeveloperKey($devkey);

        $youtube = new \Google_Service_YouTube($google_client);

        $listChannel = $youtube->channels->listChannels("channel",["part"=>"statistics","id"=>$channelId]);

        $items =$listChannel->getItems();

        return $items[0]['modelData']['statistics']['subscriberCount'];
    }

}

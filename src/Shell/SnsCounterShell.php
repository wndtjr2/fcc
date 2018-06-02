<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Shell;

use App\Service\FacebookService;
use App\Service\YoutubeService;
use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Error\Debugger;

/**
 * Simple console wrapper around Psy\Shell.
 */
class SnsCounterShell extends Shell
{

    public function main()
    {
        //Youtube 채널 구독자 가져오기
        $this->getYoutubeSubscriber();
        //페이스북 페이지 좋아요 가져오기
        $this->getFacebookLikeCounter();
        //인스타그램 팔로워수 가져오기 (엑세스토큰 을 가져오기 위해 쿠키정보를 입력해야 한다..)
//        $this->getInstagramFllowers();        /사용중지
    }

    private function getYoutubeSubscriber(){
        $mySubscriberCount = YoutubeService::Instance()->getSubscribers();
        $this->log("YoutubeCounter :".$mySubscriberCount,'debug');
        Cache::write("YoutubeCounter",$mySubscriberCount,"sns");
    }

    private function getFacebookLikeCounter(){
        $likeCount = FacebookService::Instance()->getLikeCount();
        $this->log("FacebookCounter :".$likeCount,'debug');
        Cache::write("FacebookCounter",$likeCount,"sns");
    }

    private function getInstagramFllowers(){
        $cookieInfo = array(
            'sessionid'=> "IGSCfd102e8b1f6320bd74d21f46658e40c5e7ba7ca709762f837a8ad8e242951dca%3AI5DnjrOIRt6TacL9jW2pKm58w89pUgI8%3A%7B%22asns%22%3A%7B%22115.94.115.181%22%3A3786%2C%22time%22%3A1464067409%7D%7D",
            'mid' =>'V0K7LQAEAAFxlWR_1gMhPQZDzDMD',
            'csrftoken' =>'feff799343262e3d5745de18db53e4ae',
        );

        $accessToken = "3262603014.6cf6b84.0bf144b732134accb42d4e9aaf2cf0c6";

        $http = new Client();

        $response = $http->get("https://api.instagram.com/v1/users/self/?access_token=".$accessToken,[],[
            'cookies' => $cookieInfo
        ]);

        $body = $response->body();

        $toJson = json_decode($body);

        $followers = $toJson->data->counts->follows;

        Cache::write("InstagramCounter",$followers,"sns");
    }
}

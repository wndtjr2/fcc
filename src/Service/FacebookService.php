<?php
namespace App\Service;

error_reporting(E_STRICT);
/**
 * 사용자 조회 서비스 인터페이스
 * User: Winner
 * Date: 15. 2. 6.
 * Time: 오후 1:54
 */

use Cake\Network\Http\Client;
use Cake\ORM\TableRegistry;
use Facebook\Facebook;


/**
 * Interface UserInterface
 * 사용자 인터 페이스
 * @package App\Service
 */
interface FacebookInterface {

    /**
     * @return mixed
     */
    public function getLoginUrl();

}

/**
 * 사용자 조회 서비스
 * Class UserService
 * @package App\Service
 */
class FacebookService implements FacebookInterface {

    /**
     * @var \Cake\ORM\Table
     */
    private $Users;

    /**
     * @var \Cake\ORM\Table
     */
    private $UserAccounts;

    private function __construct() {
        $this->Users = TableRegistry::get("Users");
        $this->UserAccounts = TableRegistry::get('UserAccounts');
    }

    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new FacebookService();
        }
        return $inst;
    }
    // http://fashioncrowdchallenge.com/
    private $fbAppId = FACEBOOK_ID;
    private $fbAppSecret = FACEBOOK_SECRET;
    private $fbRedirectUrl = FACEBOOK_REDIRECT;

    /**
     * get login url from facebook
     * @return string
     */
    public function getLoginUrl(){
        //if session is enabled and none exists then session start
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => 'v2.2'
        ]);
        $helper = $fb->getRedirectLoginHelper();

        $permission = ['email'];
        $loginUrl = $helper->getLoginUrl($this->fbRedirectUrl, $permission);
        return $loginUrl;
    }

    /**
     * get profile from facebook
     *
     * @return \Facebook\GraphNodes\GraphUser
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function getProfile(){
        //if session is enabled and none exists then session start
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
        $fb = new Facebook([
            'app_id' => $this->fbAppId,
            'app_secret' => $this->fbAppSecret,
            'default_graph_version' => 'v2.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookResponseException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($this->fbAppId);
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;
        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        //header('Location: https://example.com/members.php');

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,first_name,last_name,email', $accessToken->getValue());
        } catch(FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $user = $response->getGraphUser();
        return $user;
    }

    public function getLikeCount(){
        $http = new Client();
        $response = $http->get("http://api.facebook.com/restserver.php?method=links.getStats&urls=https://www.facebook.com/fcctvhq");
        $xml = $response->xml->link_stat;
        $json = json_encode($xml);
        $toObj = json_decode($json);

        $likeCount =$toObj->like_count;


        return $likeCount;
    }
}

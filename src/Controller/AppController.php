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
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Controller;
use Cake\I18n\I18n;
use App\Service\CartService;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authorize' => ['Controller'],
            'unauthorizedRedirect' => false,
            'authenticate' => [
                AuthComponent::ALL => ['userModel' => 'UserAccounts'],
                'Fcc' => [
                    'fields' => [
                        'username' => 'email',
                        'type'  => 'signup'
                    ],
                ],
            ],
            'loginAction' => [
                'controller' => 'Auth',
                'action' => 'login'
            ]
        ]);
        $this->loadComponent('RequestHandler');     // $this->RequestHandler->isMobile()로 사용중

        if ($this->RequestHandler->ismobile()) {
            $this->set('isMobile', true);
        }

        $this->loadComponent('Cookie');
        $this->Cookie->configKey('lang', 'encryption', false);  // 암호화 하지 않고 그대로 저장

        //TODO 언어 추가시 수정 필요
        $this->Cookie->write('lang','ko');
        if ($this->Cookie->read('lang') != null) {
            I18n::locale($this->Cookie->read('lang'));
        }else{
            $langArray = [
                'ar' => 'ar',
                'zh' => 'zh',
                'en' => 'en',
                'fr' => 'fr',
                'ja' => 'ja',
                'ko' => 'ko',
                'ru' => 'ru',
                'es' => 'es',
                'pt' => 'pt',
                'in' => 'in'
            ];
            $acceptLang = $this->request->acceptLanguage();
            if (empty($acceptLang) || empty($acceptLang[0])) {
                $this->Cookie->write('lang','en');
                I18n::locale('en');
            } else {
                $st = explode('-',$acceptLang[0]);
                if(array_key_exists($st[0],$langArray)){
                    $this->Cookie->write('lang',$st[0]);
                    I18n::locale($st[0]);
                }else{
                    $this->Cookie->write('lang','en');
                    I18n::locale('en');
                }
            }
        }
        if($this->Auth->user()){
            $userId = $this->Auth->user('id');
            $CartService = CartService::Instance();
            $cartCnt = $CartService->userCartCnt($userId);
        }else{
            $cartCnt = 0;
        }
        $this->set('cartCnt',$cartCnt);

        $fbCounter = Cache::read("FacebookCounter","sns");
        $youtubeCounter = Cache::read("YoutubeCounter","sns");
        $this->set("fbCounter",$fbCounter);
        $this->set("youtubeCounter",$youtubeCounter);
    }
}

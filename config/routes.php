<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Core\Plugin;
use Cake\Routing\Router;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 */
Router::defaultRouteClass('Route');
Router::extensions(['json']);
Router::scope('/', function ($routes) {
    // 메인
    /** 메인 변경 10/08 !!! */
    //$routes->connect('/', ['controller' => 'Fcc', 'action' => 'home']);
    $routes->connect('/', ['controller' => 'FccTv','action'=>'main']);

    // 이메일 인증
    $routes->connect('/verify/:token', ['controller' => 'Users', 'action' => 'verifyEmail'], ['pass' => ['token']]);
    // Countact Us
    $routes->connect('/contact', ['controller' => 'Contacts', 'action' => 'add']);
    // 비밀번호 리셋
    $routes->connect('/resetPassword', ['controller' => 'Auth', 'action' => 'resetPassword']);

    $routes->connect('/fccTv/detail/:videoId',['controller' => 'FccTv', 'action' => 'detail'],['param' =>['videoId']]);

    //검색
    $routes->connect('/fccTv/search', ['controller' => 'FccTv', 'action' => 'searchTvAndProduct']);

    $routes->connect('/about',['controller'=>'Page','action'=>'about']);
    $routes->connect('/press',['controller'=>'Page','action'=>'press']);
    $routes->connect('/terms',['controller'=>'Page','action'=>'terms']);
    $routes->connect('/policy',['controller'=>'Page','action'=>'policy']);
    $routes->connect('/family',['controller'=>'Page','action'=>'family']);

    $routes->connect('/hot',['controller'=>'What','action'=>'index']);
    
    $routes->scope('/faq', function($routes){
        $routes->connect('/', ['controller' => 'Page', 'action' => 'faq']);

        $routes->connect('/intro', ['controller' => 'Page', 'action' => 'faqIntro']);

        $routes->connect('/participation', ['controller' => 'Page', 'action' => 'faqParticipation']);

        $routes->connect('/submission', ['controller' => 'Page', 'action' => 'faqSubmission']);

        $routes->connect('/evaluation', ['controller' => 'Page', 'action' => 'faqEvaluation']);

        $routes->connect('/award',['controller'=>'Page','action'=>'faqAward']);
    });

    /**
     * Connect catchall routes for all controllers.
     *
     * Using the argument `InflectedRoute`, the `fallbacks` method is a shortcut for
     *    `$routes->connect('/:controller', ['action' => 'index'], ['routeClass' => 'InflectedRoute']);`
     *    `$routes->connect('/:controller/:action/*', [], ['routeClass' => 'InflectedRoute']);`
     *
     * Any route class can be used with this method, such as:
     * - DashedRoute
     * - InflectedRoute
     * - Route
     * - Or your own route class
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $routes->fallbacks('InflectedRoute');
});

/**
 * Load all plugin routes.  See the Plugin documentation on
 * how to customize the loading of plugin routes.
 */
Plugin::routes();

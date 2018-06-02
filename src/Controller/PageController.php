<?php
/**
 * Created by PhpStorm.
 * User: hcs
 * Date: 15. 11. 3.
 * Time: 오후 2:21
 */

namespace App\Controller;


class PageController extends AppController {

    public function initialize() {
        parent::initialize();
        $this->Auth->allow();
    }

    public function about(){}

    public function press(){}

    public function terms(){}

    public function policy(){}

    public function faq(){}

    public function faqIntro(){}

    public function faqParticipation(){}

    public function faqSubmission(){}

    public function faqEvaluation(){}

    public function faqAward(){}

    public function family(){}
}
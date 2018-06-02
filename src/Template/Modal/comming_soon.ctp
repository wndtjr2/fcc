<div class="modal__content">


    <div class="information__next2016">
        <h4>
            <span class="t"><img src="/_res/img/ci/h1_logo.png" alt="FCCTV"></span>
            <span class="w">Comming soon</span>
        </h4>
        <p class="text">
            2016년 6월 FCC TV가 여러분을 찾아갑니다.
            국내 TOP 디자이너들의 새로운 모습과 작품을 통해 더욱 새로워진 패션의 세계를 만나보세요.
        </p>
    </div>

    <div class="modal__close"><button type="button" class="modal__close--btn" onclick="closePop();">닫기</button></div>
</div>

<style>
    .modal__content {
        padding: 40px 0 50px;
        border-bottom: #ff4b46 6px solid;
        background: url("/_res/img/popup/festival2016/bg.jpg") center center no-repeat;
        background-size: cover;
    }

    .information__next2016 {
        padding: 0;
    }
    .information__next2016 > h4 { margin-bottom: 15px; font-size: 29px; line-height: 40px; }
    .information__next2016 > h4 .t { display: block; width: 100%; color: #ff4b46; text-align: center;}
    .information__next2016 > h4 .w { display: block; margin-top: 15px; font-size: 15px; font-weight: 400; line-height: 1.2; color: #000000; text-align: center; }

    .information__next2016 .text {
        padding: 0 35px;
        margin-bottom: 10px;
        font-size: 14px;
        line-height: 21px;
        color: #444444;
        text-align: center;
    }

    /*************/
    @media only screen and ( min-width: 1024px ) {
        .modal__content {
            max-width: 480px;
            margin: 0 auto;
            padding: 60px 0;
            background-image: url("/_res/img/popup/festival2016/bg_w.jpg");
        }
        .information__next2016 > h4 { margin-bottom: 20px; font-size: 58px; line-height: 80px; font-weight: 400; }
        .information__next2016 > h4 .t { }
        .information__next2016 > h4 .w { margin-top: 10px; font-size: 20px; }

        .information__next2016 .text {
            padding: 20px 90px 0;
            font-size: 14px;
            line-height: 20px;
        }
    }
</style>

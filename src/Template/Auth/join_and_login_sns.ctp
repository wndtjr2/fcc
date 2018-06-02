<script type="text/javascript">

    //다른 경로 로그인시
    <?php if(isset($sign)) {?>
    window.opener.location.href = "/?type=login&signup=<?php echo $sign?>";
    window.close();
    <?php } ?>

    //정상적인 로그인시
    <?php if(isset($login)){?>
    if(typeof opener.redirectUrl == 'undefined'){
        window.opener.location.href = window.opener.location.origin;
    }else{
        window.opener.location.href = opener.redirectUrl;
    }
    window.close();
    <?php } ?>

    //정상적인 가입시
    <?php if(isset($join)){?>
    window.opener.location.href = "/";
    window.close();
    <?php } ?>

    //이메일 없을시
    <?php if(isset($noMail)){?>
    window.opener.location.href = "/?type=login&signup=noUser";
    window.close();
    <?php }?>

    //SNS 페이지 로그인

    //다른 경로 로그인시
    <?php if(isset($signes)){?>
    window.opener.location.href = "/auth/login?signes=<?=$signes?>";
    window.close();
    <?php }?>

    //이메일 없을시
    <?php if(isset($noMails)){?>
    window.opener.location.href = "/auth/login?signes=noUser";
    window.close();
    <?php }?>

    //SNS 페이지 가입

    //다른경로 가입시
    <?php if(isset($joinSign)) {?>
    window.opener.location.href = "/auth/join?signup=<?php echo $joinSign?>";
    window.close();
    <?php } ?>

    //SNS가입시 비밀번호 없을시
    <?php if(isset($setPassword)) {?>
    var setPasswordUrl = "/auth/setPassword?fN=<?=$firstName?>&lN=<?=$lastName?>&e=<?=$encEmail?>&sn=<?=$sns?>";
    window.opener.location.href = setPasswordUrl;
    window.close();
    <?php } ?>

    //SNS가입시 탈퇴 요청시
    <?php if(isset($boltrequest)) {?>
    window.opener.location.href = "/auth/join?status=bolt";
    window.close();
    <?php } ?>

</script>
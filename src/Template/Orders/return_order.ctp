<script>
    //결제 실패시 결제후 화면 닫고 처음화면으로 이동
    var rescode = "<?=$_POST['rescode']?>";
    //var rescode = 0000;
    if(rescode == 0000){
        window.opener.location.href = "/Orders/returnOrderInfo/" + "<?=$_POST['ref'];?>";
        window.close();
    }else{
        window.close();
    }
</script>
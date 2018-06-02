<script type="text/javascript">
    opener.location.reload();
    window.close();

    <?php if(isset($noEmail)):?>
    window.opener.location.href = "/auth/noEmail";
    window.close();
    <?php endif;?>
</script>
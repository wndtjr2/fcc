<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<script>

<?php if(isset($alertMsg)) :?>
    alert('<?=$alertMsg?>');
<?php endif; ?>
    location.replace('<?=$redirectUrl?>');

</script>
</head>
<body>
</body>
</html>
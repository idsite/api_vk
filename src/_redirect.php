<script type="text/javascript">
    var url = '<?= $url ?>';
    if (window.opener)
    {
        window.opener.location.href = url;
        window.close();
    } else
    {
        window.location.href = url;
    }
</script>
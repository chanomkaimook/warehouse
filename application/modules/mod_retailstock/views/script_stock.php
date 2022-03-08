<script>
    
    $(document).on('change','#select_listmonth',function(){
        let month = $(this).val();
        window.location.replace('stock_month?m='+month);
    })
    $(document).on('click','#listmonth',function(){
        let month = $(this).val();
        window.location.replace('stock_month?m='+month);
    })

</script>
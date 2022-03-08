<script>
    function tableLayout(){
        //
        //  set screen 
        if(screen.availHeight >= 1040){
            var divSet = 1040;
        }
        if(screen.availHeight < 1040){
            var divSet = 768;
        }
        //
        //  add value height element tool on table
        var tool = $('.form').hasClass('tool-table');
        if(tool){
            var addvalue = $('.content .tool-table').outerHeight() - 10;
        }else{
            var addvalue = -11;
        }
        
        var elementadd = addvalue.toFixed(2);
        var tableHeight = Math.round(screen.availHeight * 0.047);
        var percent = tableHeight / 100;
        // var percent = 36 / 100;
        var result = (divSet * percent.toFixed(2)) - elementadd;

        return result;
    }
</script>
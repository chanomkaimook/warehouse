$(function () {

    function fnFormatDetails(oTable, nTr) {
        var aData = oTable.fnGetData(nTr);
        var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
        sOut += '<tr><td>Rendering engine:</td><td>' + aData[1] + ' ' + aData[4] + '</td></tr>';
        sOut += '<tr><td>Link to source:</td><td>Could provide a link here</td></tr>';
        sOut += '<tr><td>Extra info:</td><td>And any further details here (images etc)</td></tr>';
        sOut += '</table>';
        return sOut;
    }

    /*  Insert a 'details' column to the table  */
    var nCloneTh = document.createElement('th');
    var nCloneTd = document.createElement('td');
    nCloneTd.innerHTML = '<i class="fa fa-plus-square-o"></i>';
    nCloneTd.className = "center";

    $('#table2 thead tr').each(function () {
        this.insertBefore(nCloneTh, this.childNodes[0]);
    });

    $('#table2 tbody tr').each(function () {
        this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
    });
	
	var tbl = $('.panel-content #table2').length;
	var tbl2 = $('#tblcontent #table2').length;
	var tbl3 = $('#tblcontent #table2').length;
    /*  Initialse DataTables, with no sorting on the 'details' column  */
		if(tbl > 0){
			 var oTable = $('#table2').dataTable({
				"aoColumnDefs": [{
					"bSortable": false,
					"aTargets": [0]
				}],
				"aaSorting": [
					[1, 'asc']		//[1, 'asc']	default

				]
			});
		}
		if(tbl2 > 0){
			 var oTable = $('#table2').dataTable({
				"aoColumnDefs": [{
					"bSortable": false,
					"aTargets": [0]
				}],
				"aaSorting": [
					[0, 'asc']		//[1, 'asc']	default

				]
			});
		}
		if(tbl3 > 0){
			 var oTable = $('#table3').dataTable({
				"aoColumnDefs": [{
					"bSortable": false,
					"aTargets": [0]
				}],
				"aaSorting": [
					[0, 'asc']		//[1, 'asc']	default

				]
			});
		}
});
var tableToExcel = (function () {
	var uri = 'data:application/vnd.ms-excel;base64,'
	, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
	, base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
	, format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
	return function (table, name, filename) {
			if (!table.nodeType) table = document.getElementById(table)
			var $table = jQuery(table).clone();
			jQuery('>thead>tr>th>a', $table).each(function(){jQuery(this).replaceWith(jQuery(this).html())});
			jQuery('.unexportable', $table).remove();
			var ctx = { worksheet: name || 'Worksheet', table: $table.html() }
			var dlink = document.getElementById("dlink");
			if(!dlink){
				dlink = document.createElement("a");
				dlink.setAttribute('style', 'display:none;');
				dlink.setAttribute('id', 'dlink');
				document.body.appendChild(dlink);
			}
			document.getElementById("dlink").href = uri + base64(format(template, ctx));
			document.getElementById("dlink").download = filename;
			document.getElementById("dlink").click();

	}
})();
var tablesToExcel = (function ($) {
	var uri = 'data:application/vnd.ms-excel;base64,'
	, html_start = `<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">`
	, template_ExcelWorksheet = `<x:ExcelWorksheet><x:Name>{SheetName}</x:Name><x:WorksheetSource HRef="sheet{SheetIndex}.htm"/></x:ExcelWorksheet>`
	, template_ListWorksheet = `<o:File HRef="sheet{SheetIndex}.htm"/>`
	, template_HTMLWorksheet = `
------=_NextPart_dummy
Content-Location: sheet{SheetIndex}.htm
Content-Type: text/html; charset=utf-8

` + html_start + `
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link id="Main-File" rel="Main-File" href="../WorkBook.htm">
	<link rel="File-List" href="filelist.xml">
</head>
<body>{SheetContent}</body>
</html>`
	, template_WorkBook = `MIME-Version: 1.0
X-Document-Type: Workbook
Content-Type: multipart/related; boundary="----=_NextPart_dummy"

------=_NextPart_dummy
Content-Location: WorkBook.htm
Content-Type: text/html; charset=utf-8

` + html_start + `
<head>
<meta name="Excel Workbook Frameset">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="File-List" href="filelist.xml">
<!--[if gte mso 9]><xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>{ExcelWorksheets}</x:ExcelWorksheets>
  <x:ActiveSheet>0</x:ActiveSheet>
 </x:ExcelWorkbook>
</xml><![endif]-->
</head>
<frameset>
	<frame src="sheet0.htm" name="frSheet">
	<noframes><body><p>This page uses frames, but your browser does not support them.</p></body></noframes>
</frameset>
</html>
{HTMLWorksheets}
Content-Location: filelist.xml
Content-Type: text/xml; charset="utf-8"

<xml xmlns:o="urn:schemas-microsoft-com:office:office">
	<o:MainFile HRef="../WorkBook.htm"/>
	{ListWorksheets}
	<o:File HRef="filelist.xml"/>
</xml>
------=_NextPart_dummy--
`
	, base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
	, format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
	return function (tables, filename) {
		var context_WorkBook = {
			ExcelWorksheets:''
		,	HTMLWorksheets: ''
		,	ListWorksheets: ''
		};
		var jq = typeof tables == 'string';
		var tables = jq ? jQuery(tables) : tables;
		
		$.each(tables,function(SheetIndex){
			if(jq){
				var $table = $(this).clone();
			} else {
				var $table = $('' + this).clone();
			}
			jQuery('>thead>tr>th>a', $table).each(function(){jQuery(this).replaceWith(jQuery(this).html())});
			jQuery('.unexportable', $table).remove();
			
			var SheetName = $table.attr('data-SheetName');
			if($.trim(SheetName) === ''){
				SheetName = 'Sheet' + SheetIndex;
			}
			var SheetContent = '';
			var $end_table = $table;
			if($table.hasClass('xls-exportable-bulk')){
				$end_table = $('<table />');
				$table.find('.xls-exportable-bulk-item').each(function(){
					$end_table.append(this.innerHTML);
				})
			}
			SheetContent = $end_table.wrap('<div />').parent().html();
			context_WorkBook.ExcelWorksheets += format(template_ExcelWorksheet, {
				SheetIndex: SheetIndex
			,	SheetName: SheetName
			});
			context_WorkBook.HTMLWorksheets += format(template_HTMLWorksheet, {
				SheetIndex: SheetIndex
			,	SheetContent: SheetContent
			});
			context_WorkBook.ListWorksheets += format(template_ListWorksheet, {
				SheetIndex: SheetIndex
			});
		});
		filename = filename || 'Workbook.xls';
		var blob = new Blob([format(template_WorkBook, context_WorkBook)], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    if (navigator.msSaveBlob) { // IE 10+
        navigator.msSaveBlob(blob, filename);
    } else {
        var link = document.createElement("a");
        if (link.download !== undefined) { // feature detection
            // Browsers that support HTML5 download attribute
            var url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
		
		/* var link = document.createElement("A");
		link.href = uri + base64(format(template_WorkBook, context_WorkBook));
		link.download = filename || 'Workbook.xls';
		link.target = '_blank';
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link); */
	}
})(jQuery);

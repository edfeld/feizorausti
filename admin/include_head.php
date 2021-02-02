<!--meta data-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!--header basics-->
<title><?php echo $row_getDetails['companyName']; ?></title>

<!--stylesheets-->
<link href="Assets/css/bootstrap.css" rel="stylesheet" media="screen"/>
<link href="Assets/css/bootstrap-responsive.css" rel="stylesheet"/>
<link href="Assets/css/styles.css" rel="stylesheet" media="screen"/>

<!--tinymce-->
<script type="text/javascript" src="tinymce/tinymce.min.js"></script>
<script type="text/javascript">
	tinymce.init({
		content_css : "Assets/css/mycontent.css",
		mode: "textareas",theme: "modern", editor_deselector: "nomce",
		plugins: [
			 "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			 "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
			 "table contextmenu directionality emoticons paste textcolor responsivefilemanager fullscreen code"
	   ],
	   toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
	   toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview | code | fullscreen",
	   image_advtab: true,

	   convert_urls: false,

	   external_filemanager_path: "<?php echo $sitedir; ?>/admin/filemanager/",
	   filemanager_title:"Responsive Filemanager" ,
	   external_plugins: { "filemanager" : "<?php echo $sitedir; ?>/admin/filemanager/plugin.min.js"}
	 });
</script>

<!--jquery-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
//document onReady
$(function() {
	//set up the datepicker UI (a textbox with the #datepicker ID)
    $("#datepicker").datepicker();
	$("#datepicker").datepicker("option", "dateFormat", "dd-mm-yy");
});
</script>

<!--auto-select text-->
<script type="text/javascript">
    function selectText(containerid){
        if (document.selection){
            var range = document.body.createTextRange();
            range.moveToElementText(document.getElementById(containerid));
            range.select();
        }
		else if (window.getSelection){
            var range = document.createRange();
            range.selectNode(document.getElementById(containerid));
            window.getSelection().addRange(range);
        }
    }
</script>

<!--filemanager functions-->
<script type="text/javascript">
function responsive_filemanager_callback(field_id){
	console.log(field_id);
	var url=jQuery('#'+field_id).val();
	//alert('update '+field_id+" with "+url);
}

function openFilemanager(typeid, fieldid){
	window.open('filemanager/dialog.php?type=' + typeid + '&field_id=' + fieldid + '&popup=1','filemanager','height=600, width=900');
}
</script>

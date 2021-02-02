// JavaScript Document

$(document).ready(function() {	
	//enter here script to execute on document load
});

// The function we use to appear submenu
function displayDiv(id)
	{	
		hideDiv();	
		if ($(id).is(":hidden")) {
		$(id).slideDown("fast");
		} else {
		$(id).slideUp();
		}
}
function hideDiv()
	{
		var totalMenus = 50; //Change this value to the number of submenus
		for (var i=0;i<=totalMenus;i++){
			$('#help'+i).slideUp("fast");
		}
	}


function LinkAppear(){
	document.getElementById('pageLinkDiv').style.display = 'block'; 
}
function LinkDisappear(){
	document.getElementById('pageLinkDiv').style.display = 'none'; 
}

// javascript.js



/* function QuantityExceed(){
	alert("You have tried to increase the quantity of this item beyond the\nrecorded quantity in the database.\n\nIf you wish to add more of this item, you must edit the recorded\nquantity by clicking on its name and selecting 'Edit Product'");
}
*/ 

// Pops Window
function popupWindow(URL,width,height) {
  day = new Date();
  id = day.getTime();
  var left = (screen.width-width)/2;
  var top = (screen.height-height)/2;
  eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=yes,width=" + width + ",height=" + height + ",left=" + left + ",top=" + top + "');");
}

// Pops Window
function popupStaticWindow(name,URL,width,height) {
  var left = (screen.width-width)/2;
  var top = (screen.height-height)/2;
  eval(name + " = window.open(URL, '" + name + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=yes,width=" + width + ",height=" + height + ",left=" + left + ",top=" + top + "');");
}

// restore tell friend text
function setInputText(field,text) {
	if (field.value == "") {
		field.value = text;
	}
}

// hide tell friend text
function clearInputText(field,text) {
	if (field.value == text) {
		field.value = "";
	}
}

// navigates using menu
function menu_nav(url) {
  if(url!=""){window.location.href=url;}
}

// navigates using menu
function DeleteWarning(type,url,msg) {
	if(type=="VoidOrder"){
		resp = confirm(msg);
	}
	if (resp == true){
		window.location.href=url;
	}
}

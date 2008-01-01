/*
* csrdelft.nl javascript libje...
*/

function vergrootTextarea(id, rows) {
  var textarea = document.getElementById(id);
  //if (!textarea || (typeof(textarea.rows) == "undefined")) return;
  var currentRows=textarea.rows;
  textarea.rows = currentRows + rows;
}
function setjs() {
 if(navigator.product == 'Gecko') {
   document.loginform["interface"].value = 'mozilla';
 }else if(window.opera && document.childNodes) {
   document.loginform["interface"].value = 'opera7';
 }else if(navigator.appName == 'Microsoft Internet Explorer' &&
    navigator.userAgent.indexOf("Mac_PowerPC") > 0) {
    document.loginform["interface"].value = 'konqueror';
 }else if(navigator.appName == 'Microsoft Internet Explorer' &&
 document.getElementById && document.getElementById('ietest').innerHTML) {
   document.loginform["interface"].value = 'ie';
 }else if(navigator.appName == 'Konqueror') {
    document.loginform["interface"].value = 'konqueror';
 }else if(window.opera) {
   document.loginform["interface"].value = 'opera';
 }
}
function nickvalid() {
   var nick = document.loginform.Nickname.value;
   if(nick.match(/^[A-Za-z0-9\[\]\{\}^\\\|\_\-`]{1,32}$/))
      return true;
   alert('Kies een geldige nickname!');
   //document.loginform.Nickname.value = nick.replace(/[^A-Za-z0-9\[\]\{\}^\\\|\_\-`]/g, '');
   return false;
}
function setcharset() {
	if(document.charset && document.loginform["Character set"])
		document.loginform['Character set'].value = document.charset
}
function bevestig(tekst){
	return confirm(tekst);
}

function forumEdit(post){
	var scripttag=document.createElement('SCRIPT');
	scripttag.type = 'text/javascript';
	scripttag.src = '/forum/bewerken/formulier/'+post;
	document.body.appendChild(scripttag);
}

function youtubeDisplay(ytID){
	var ytDiv = document.getElementById('youtube'+ytID);
	ytDiv.innerHTML='<object width="425" height="350">'+
		'<param name="movie" value="http://www.youtube.com/v/'+ ytID + '&autoplay=1"></param>'+
		'<embed src="http://www.youtube.com/v/'+ ytID + '&autoplay=1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
}


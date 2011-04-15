

function trigEvent(sender, comment) {
  /*
     triggers event


  */
  // comparison between escape() encodeURI() ...
  // http://xkr.us/articles/javascript/encode-compare/

  SERVICE_URL="http://localhost/generis/core/kernel/eventLoggerService.php";

  sender  = encodeURIComponent(sender);
  comment = encodeURIComponent(comment);
  toSend = "sender="+sender+"&comment="+comment
  xhr = new XMLHttpRequest();
  //TODO: how to get a good path for the web service?
  //This cannot be set here as I don't know from where this JS is called!
  xhr.open("POST",SERVICE_URL, true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.setRequestHeader("Content-length", toSend.length);
  xhr.setRequestHeader("Connection", "close");
  xhr.send(toSend);
}

function trigMouseEvent(sender, comment, coordinates, domElementId)
{	
	SERVICE_URL="http://localhost/generis/core/kernel/eventLoggerService.php";
	
	sender = encodeURIComponent(sender);
	comment = encodeURIComponent(comment);
	coordinates = encodeURIComponent(coordinates[0]) + ',' + encodeURIComponent(coordinates[1]);
	domElement = encodeURIComponent(domElementId);
	toSend = 'sender=' + sender + '&comment=' + comment + '&coordinates=' + coordinates + '&domElement=' + domElement;
	
	xhr = new XMLHttpRequest();
	xhr.open('POST', SERVICE_URL, true);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.setRequestHeader('Content-length', toSend.length);
	xhr.setRequestHeader('Connection', 'close');
	xhr.send(toSend);
}

function trigKeyboardEvent(sender, comment, key)
{
	SERVICE_URL="http://localhost/generis/core/kernel/eventLoggerService.php";
	
	sender = encodeURIComponent(sender);
	comment = encodeURIComponent(comment);
	key = encodeURIComponent(key);
	toSend = 'sender=' + sender + '&comment=' + comment + '&key=' + key;
	
	xhr = new XMLHttpRequest();
	xhr.open('POST', SERVICE_URL, true);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.setRequestHeader('Content-length', toSend.length);
	xhr.setRequestHeader('Connection', 'close'); 
	xhr.send(toSend);
}
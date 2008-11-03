var x = 0;

function start() {
        images = document.getElementsByTagName('img');

	if (images.length == 0) {
		var t = setTimeout("window.location.reload()", 25000); 
		return;
	}

	if (x >= images.length) {
		// window.location.reload();
		x = 0;
	}

	var timeout = images[x].getAttribute('dur').replace(/s/, "000");
        document.getElementsByTagName('body')[0].style.background = 'url('+images[x].getAttribute('src')+') no-repeat';
	x++;
	var t=setTimeout("start()", timeout);
} 

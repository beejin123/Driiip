$(document).ready(function(){
	var ws = new WebSocket("ws://127.0.0.1:8282");

	ws.onopen = function(){
		console.log("服务开启"); 
	};

	ws.onmessage = function (evt){ 
		var info = evt.data;
		var obj = JSON.parse(info);
		console.log(obj);
	};

	ws.onclose = function(){ 
		console.log("服务关闭"); 
	};
})
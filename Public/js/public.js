/*
 * 自动隐藏信息框
 * @param msg 信息内容
 * @param sec 信息框多少秒后自动消失,默认为4.5s
 */
function Toast( msg, sec, fn ){
	var browserWidth			= window.innerWidth || document.documentElement.clientWidth,
		ID						= "HiddenMessageDialog" + new Date().getTime();
	var dialogBoxWidth			= (function(){
										var len = msg.length , w = 0;
										for (var i = 0; i < len; i++) {
											w += msg.charCodeAt(i) <= 255 ? 9.6 : 19.2;
										}
										return  w + 40 >= browserWidth  ? browserWidth : w + 40;
								  })();
	var dialogbox 				= document.createElement("div");
	dialogbox.style.position	= "fixed";
	dialogbox.style.background	= "rgb(44,50,50)";
	dialogbox.style.width		= dialogBoxWidth + "px";
	dialogbox.style.height		= "50px";
	dialogbox.style.left		= (browserWidth - dialogBoxWidth ) /2  + "px";
	dialogbox.style.bottom		= "60px";
	dialogbox.style.fontFamily	= "微软雅黑";
	dialogbox.style.textAlign	= "center";
	dialogbox.style.lineHeight	= "50px";
	dialogbox.style.fontSize	= "1.2em";
	dialogbox.style.color		= "#DDDDDD";
	dialogbox.style.borderRadius= "25px";
	dialogbox.style.webkitBorderRadius = "25px"; 
	dialogbox.style.mozBorderRadius = "25px"; 
	dialogbox.style.zIndex		= "999999999";
	dialogbox.setAttribute( "id" , ID );
	var textNode = document.createTextNode( msg );
	dialogbox.appendChild( textNode );
	document.body.appendChild( dialogbox );
	setTimeout(function(){
		var timer = 100;
		var tt = setInterval(function(){
			dialogbox.style.opacity = timer / 100 + "";
			dialogbox.style.webkitOpacity = timer / 100 + "";
			dialogbox.style.mozOpacity = timer / 100 + "";
			timer -= 20;
			(timer<0) && (document.body.removeChild(document.getElementById(ID)),typeof fn === 'function' && fn(),clearInterval(tt));
		},100);
	},  sec * 1000 || 4500 );
}
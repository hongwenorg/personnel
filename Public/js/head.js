$.get('/index.php/Home/Content/head',function(data){
		$('.head_01').prepend(data);
});
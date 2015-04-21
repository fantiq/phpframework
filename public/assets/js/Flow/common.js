$(function(){
	funcs.init();
	funcs.style();
});

var funcs={

	init:function(){
		$('.index').removeClass('active');
		$(".flow").addClass("active");
	},
	style:function(){
		$(".oa-flow-left-nav-item").hover(function(){
			if(! $(this).hasClass("item-active")) $(this).addClass('item-hover');
		},function(){
			$(this).removeClass('item-hover');
		});
	}

};
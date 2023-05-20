$(document).ready( function(){
	$(".open").click(function(e){
		e.preventDefault();
		$(this).siblings(".description").slideToggle();
	});
	
	$("input[type=checkbox][data-targetSelector]").change(function(){
		toggleDiv();
	});
});


function toggleDiv(){
	$("[data-targetSelector]").each(function(k,v){
		var target = $(v).attr("data-targetSelector");

		if( $(v).is(':checked') ){
			$(target).show();
		}else{
			$(target).hide();
		}
	});
}

 $(document).ready(function() {

$(function (){	

	var pattern = $('#bg-patt li a');
	
	$(pattern).click(function () {
		var patt = $(this).attr('href');	
		
		$('body').removeClass().addClass(patt);		
			return false;
	});
	
		
});

$(function (){	

	var cstyles = $('.color-styles li a');
	
	$(cstyles).click(function () {
		var patt = $(this).attr('href');	
		
		$('body').removeClass().addClass(patt);		
			return false;
	});
	
		
});

	
$('.closerer').click(function () {
	$('#optwrap').animate({"left": "-=212px"}, "4000");
	$(this).hide();
	$('.opener').show();
	return false;
});

$('.opener').click(function () {
	$('#optwrap').animate({"left": "+=212px"}, "4000");
	$(this).hide();
	$('.closerer').show();
	return false;
});

$('.opener').hover(function (){
	$(this).stop().animate({ 
			opacity : '1'
		}, 300);
			}, function() {
				$(this).stop().animate({ 
					opacity : '1' 
				}, 500);
});



}); // end of jquery
  
  
  
  
  
  
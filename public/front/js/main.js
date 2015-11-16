// *** TO BE CUSTOMISED ***

var style_cookie_name = "red" ;
var style_cookie_duration = 30 ;

// *** END OF CUSTOMISABLE SECTION ***
$(document).ready(function(){
   $('.tabs-yscroll').click(function(){
                    var target = $(this).get(0).href.split('#')[1];
                    var scroll = $(window).scrollTop();
                $('html, body').stop().animate({
                    scrollTop: $('.tab-content').offset().top-100
                }, 1000);
                }); 
});
function switch_style ( css_title )
{
  var i, link_tag ;
  for (i = 0, link_tag = document.getElementsByTagName("link") ;
	i < link_tag.length ; i++ ) {
	if ((link_tag[i].rel.indexOf( "stylesheet" ) != -1) &&
	  link_tag[i].title) {
	  link_tag[i].disabled = true ;
	  if (link_tag[i].title == css_title) {
		link_tag[i].disabled = false ;
	  }
	}
	set_cookie( style_cookie_name, css_title,
	  style_cookie_duration );
  }
}
function set_style_from_cookie()
{
  var css_title = get_cookie( style_cookie_name );
  if (css_title.length) {
	switch_style( css_title );
  }
}
function set_cookie ( cookie_name, cookie_value,
	lifespan_in_days, valid_domain )
{
	var domain_string = valid_domain ?
					   ("; domain=" + valid_domain) : '' ;
	document.cookie = cookie_name +
					   "=" + encodeURIComponent( cookie_value ) +
					   "; max-age=" + 60 * 60 *
					   24 * lifespan_in_days +
					   "; path=/" + domain_string ;
}
function get_cookie ( cookie_name )
{
	var cookie_string = document.cookie ;
	if (cookie_string.length != 0) {
		var cookie_value = cookie_string.match (
						'(^|;)[\s]*' +
						cookie_name +
						'=([^;]*)' );
		return decodeURIComponent ( cookie_value[2] ) ;
	}
	return '' ;
}

$(function() {
	$( "#accordion" ).accordion();
});

$(document).ready( function() {
	$('#tabs').easytabs();
});

$(document).ready(function() {
	$('.testimonials')
		.cycle({
		fx: 'fade' // choose your transition type, ex: fade, scrollUp, scrollRight, shuffle
	});
});
$(document).ready(function() {
	$('.tweets')
		.cycle({
		fx: 'scrollUp' // choose your transition type, ex: fade, scrollUp, scrollRight, shuffle
	});
});

jQuery(document).ready(function(){
	jQuery('ul.sf-menu').superfish();
});

$(document).ready(function(){
	$('#slider').bxSlider({
		auto: true,
		speed: 1000,
		pause: 5000
	});
});

jQuery(document).ready(function() {
    jQuery('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = jQuery(this).attr('href');
 
        // Show/Hide Tabs
        jQuery('.tabs ' + currentAttrValue).fadeIn(1000).siblings().hide();
 
        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
 
        e.preventDefault();
    });
});
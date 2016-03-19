// JavaScript Document
//window.sessionStorage.clear();

var Application = function(){
	
	var handelLoginRequired = function(){
		
		$(".loginrequired").click(function(){
			if(loggedin==1){
				window.location = $(this).data("href");
			}else{
                                window.sessionStorage.setItem('orderType',$(this).data('order-type'));
                                window.sessionStorage.setItem('orderTypeId',$(this).data('id'));
				$('#loginModal').modal('show');			
			}
			
		})
		
	},
          //for top order button
          ordernowBtnFocus = function(){
              $('#orderbtn-top').click(function(){
                  if($('#pincodetxt').length){
                    $('html, body').animate({
                              scrollTop: $('#pincodetxt').offset().top-500,
                          }, 500, function() {
                       });
                    $('#pincodetxt').focus(); 
                  }else{
                      window.location = baseUrl
                  }
              });
          }
	
	return {
						
		/* Common Calls  */
        init: function () {
			handelLoginRequired();
                        ordernowBtnFocus();
		}
	}
	
}();

$(document).ready(function(e) {
	
    Application.init();
	
});
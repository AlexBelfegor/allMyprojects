$(document).ready(function() {

	$("#phone").mask("+38(999) 999-99-99");

	$('.click-basket').click(function ()
	{
		var elem = $(".basket-show .headervspl").css("visibility");

		if(elem == "visible")
		{			$('.basket').removeClass('basket-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{			$('.basket').addClass('basket-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});

	$('.click-vhod').click(function ()
	{
		var elem = $(".vhod-show .headervspl").css("visibility");

		if(elem == "visible")
		{
			$('.vhod').removeClass('vhod-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.vhod').addClass('vhod-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});

	$('.click-registr').click(function ()
	{
		var elem = $(".registr-show .headervspl").css("visibility");

		if(elem == "visible")
		{
			$('.registr').removeClass('registr-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.registr').addClass('registr-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});

	/*click request phone*/
	$('.click-zakzv').click(function ()
	{
		var elem = $(".zakzv-show .headervspl").css("visibility");

		if(elem == "visible")
		{
			$('.zakzv').removeClass('zakzv-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.zakzv').addClass('zakzv-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});

	$( ".zakzv .vsplwrap" ).on( "mouseleave", function(event) {
	  	var elem = $(".zakzv-show .headervspl").css("visibility");

		if(elem == "visible")
		{
			$('.zakzv').removeClass('zakzv-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.zakzv').addClass('zakzv-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});

	/*click send review*/
	$('.click-ostotz').click(function ()
	{
		var elem = $(".ostotz-show .headervspl").css("visibility");

		if(elem == "visible")
		{
			$('.ostotz').removeClass('ostotz-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.ostotz').addClass('ostotz-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});

	$( ".ostotz .vsplwrap" ).on( "mouseleave", function(event) {
		var elem = $(".ostotz-show .headervspl").css("visibility");

		if(elem == "visible")
		{
			$('.ostotz').removeClass('ostotz-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.ostotz').addClass('ostotz-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});

	$('.click-ostotz').click(function(){
		var destination = $('.header').offset().top;
		$("html,body").animate({scrollTop: destination}, 1000);
		return false;
	});

	/*callback*/
	$("#form-send-delivery").submit(function () {
	    var f = $(this);

        $(".error").remove();

	    $.post("/callback/contact/", f.serialize(), function (response)
	    {
			switch(response.status)
			{
				case 'success' :
				{
		        	$("#form-send-delivery").html("");
		        	$.colorbox({href:"/callback/forms/popup_success.html",onOpen:function(){$("#cboxClose").css({'display':'none'});}});
				}   break;
				case 'fail' :
				{
		        	$.each(response.error, function( index, value )
		        	{
						if(value != "")
						{
	                    	$("input[name='email']").after("<div class='error'>"+value+"</div>");
						}
					});
				}   break;
			}
	    }, "json");

	    return false;
	});

	$("#form-send-phone-callback").submit(function () {
	    var f = $(this);

        $(".error").remove();

	    $.post("/callback/phone/", f.serialize(), function (response)
	    {
			switch(response.status)
			{
				case 'success' :
				{
		        	//$("#form-send-phone-callback").html("");
		        	$('.zakzv').removeClass('zakzv-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		        	$("input[name='phone']").val("");
		        	$.colorbox({href:"/callback/forms/popup_success.html",onOpen:function(){$("#cboxClose").css({'display':'none'});}});
				}   break;
				case 'fail' :
				{
		        	$.each(response.error, function( index, value )
		        	{
						if(value != "")
						{
	                    	$("input[name='phone']").after("<div class='error'>"+value+"</div>");
						}
					});
				}   break;
			}
	    }, "json");

	    return false;
	});

	$("#form-send-review-callback").submit(function () {
	    var f = $(this);

        $('.form-input').css({'border':'1px solid #D7C9B9'});

	    $.post("/reviews/senddata/?type=reviewadd", f.serialize(), function (response)
	    {
			switch(response.data.status)
			{
				case 'ok' :
				{
		        	$('.ostotz').removeClass('ostotz-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		        	$(".form-input").val("");
		        	$.colorbox({href:"/callback/forms/popup_success.html",onOpen:function(){$("#cboxClose").css({'display':'none'});}});
				}   break;
				case 'fail' :
				{
                    $.each(response.data.error, function( index, value )
		        	{
						if(value != "")
						{
	                    	$("textarea[name='"+index+"']").css({'border':'1px solid #cc0000'});
	                    	//$("input[name='phone']").after("<div class='error'>"+value+"</div>");
						}
					});
				}   break;
			}
	    }, "json");

	    return false;
	});

    /*delete cart item*/
    function UpdateCartOnMain(data)
    {
        $(".cart-total").html(data.total);
        $(".cart-count").html(data.count);

        if(data.count == 0)
        {        	$(".headervspl").addClass( "cart-hidden" );        }
    }

    $(".deltovbasket").click(function () {
		var id = $(this).attr('rel');
        var parent = $(this).closest('.onebaskettov');

        $.post('/callback/delete_item/', {id: id}, function (response) {
			UpdateCartOnMain(response.data);
	  		parent.remove();
        }, "json");
    });

	/*search data*/
	$.fn.delayKeyup = function(callback, ms){
	    var timer = 0;
	    var el = $(this);
	    $(this).keyup(function(){
	    	clearTimeout (timer);
	    	timer = setTimeout(function(){
	    	    callback(el)
	    	}, ms);
	    });
	    return $(this);
	};

	$('#search').delayKeyup(function(el){

        var string = el.val();

        $.get('/callback/search_data/', {limit: 10,string: string}, function (response)
        {
	        $(".show-item").remove();

	        if(response.empty==1)
	        {	        	$('#list-items').css({"display": "none"}).animate({opacity: 1}, 1500);	        }
	        else
	        {	        	$('#list-items').css({"display": "block"}).animate({opacity: 1}, 1500);
            }

        	$.each(response.data, function( index, value )
        	{
				var first_element = (index==0) ? " onebaskettov1" : "";

				return $( "<div class='show-item onebaskettov"+first_element+"'>" )
        		.append( "<div class='img'><a href='"+value.full_url+"'><img src="+value.img+" /></a></div>" )
	        	.append( "<div class='textbask'><p><a href='"+value.full_url+"'>"+value.name+"</a></p><p class='pricetovbasket'>"+value.price+"</p></div>" )
	        	.append( "<div class='clr'></div>" )
	        	.appendTo( ".list-items" );
			});

        }, "json");

	},100).focusout(function() {
		setTimeout(function(){
	    	$('#list-items').css({"display": "none"}).animate({opacity: 1}, 1500);
	    	$(".show-item").remove();
	    }, 500);
    });

});

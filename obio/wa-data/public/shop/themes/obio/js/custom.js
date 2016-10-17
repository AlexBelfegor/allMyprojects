$(function () {

	/*phone mask*/
	if($(".phone").length)
	{
		$(".phone").mask("+38(999) 999-99-99");
    }

    if($(".onetrig").length)
	{		$(".onetrig").mouseover(function(){
			$(this).children('img').addClass('flip animated');
		});

		$(".onetrig").mouseout(function(){
			$(this).children('img').removeClass('flip');
		});
	}

	/*sticky*/
	if($("#myAffix").length)
	{		$("#myAffix").affix({
			offset:
			{
				top: 117
			}
		});

		$("#myAffix").on('affixed.bs.affix');
	}

	/*wow*/
	wow = new WOW(
	{
		animateClass: 'animated',
		offset:       100
	});

	wow.init();

	/*rollOverImage*/
	window.rollOverImage = function(e, t, n) {
	    function i() {
	        e.src = t
	    }

	    function a() {
	        e.src = t
	    }
	    var r = window.event || n;
	    e && ("mouseover" == r.type ? imageTimeout = setTimeout(i, 200) : "mouseout" == r.type && (clearTimeout(imageTimeout), a()))
	}, window.clearDefault = function(e) {
	    e.defaultValue == e.value && (e.value = "")
	};

	/*send phone*/
	$(".form-send-phone-callback").submit(function () {
	    var f = $(this);

        $(".error").remove();

	    $.post("/callback/phone/", f.serialize(), function (response)
	    {
			switch(response.status)
			{
				case 'success' :
				{
		        	$('.form-send-phone-callback').css({opacity: 1,display:'none'}).animate({opacity: 1}, 1500);
		        	$(".form-send-phone-callback").after("<div class='success'>Спасибо мы свяжемся с вами в скором времени.</div>");
		        	$("input[name='phone']").val("");
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

    /*slider in product*/
    if($('.tovargal .bxslider').length)
    {
		$('.tovargal .bxslider').bxSlider({
			minSlides: 1,
			maxSlides: 1,
			slideWidth: 460,
			slideMargin: 0,
			moveSlides: 1,
			pagerCustom: '#bx-pager',
			infiniteLoop: false,
			controls: false
        });

        $('#bx-pager .bxslider').bxSlider({
			minSlides: 1,
			maxSlides: 6,
			slideWidth: 60,
			slideMargin: 20,
			moveSlides: 1,
			mode: 'horizontal',
			infiniteLoop: false,
			pager: false,
			controls: true
        });
    }

    /*zoom in product*/
    if ($ && $.fn.zoom)
    {
        $('.zoom').zoom();
        $('.zoom1').zoom({ on:'grab' });
    }

	/*remove from shopping cart*/
    $(".RemoveFromCartProduct").click(function () {
		var id = $(this).attr('rel');
        var parent = $(this).closest('.onetov');

        $.post('/callback/delete_item/', {id: id}, function (response) {
			UpdateCartOnMain(response.data);
	  		parent.remove();
        }, "json");
    });

    /*delete cart item*/
    function UpdateCartOnMain(data)
    {
        $(".cart-total").html(data.total);
        $(".cart-count").html(data.count);

        if(data.count == 0)
        {
        	$(".dropdown-basket").addClass( "cart-hidden" );
        	$(".click-basket").attr("href", "#" );
        }
        else
        {
        	$(".cart-link, .click-basket").attr("href", "/cart/" );
        }
    }

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
	        $(".show-items").html("");

	        if(response.empty==0)
	        {
	        	$( ".show-items" ).html( response.data );
	        }

        }, "json");

	});

	$('.search-bl').hover(function () {

    }, function () {
		setTimeout(function(){
	    	$("#search").val("");
	    	$(".show-items").html("");
	    }, 500);
    });

});


$(document).ready(function() {

	/*click login*/
	/*$('.click-basket').click(function ()
	{
		var elem = $(".basket-show .headervspl").css("visibility");

		if(elem == "visible")
		{
			$('.basket').removeClass('basket-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.basket').addClass('basket-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

		return false;
	});*/

	$('.click-vhod, .otprotztopreset').click(function ()
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

	$( ".vhod .vsplwrap" ).on( "mouseleave", function(event) {

		$('.vhod').removeClass('vhod-show').css({opacity: 1}).animate({opacity: 1}, 1500);

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
		//var elem = $(".ostotz-show .headervspl").css("visibility");

		/*if(elem == "visible")
		{*/
			$('.ostotz').removeClass('ostotz-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		/*}
		else
		{
			$('.ostotz').addClass('ostotz-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }*/

		return false;
	});

	$('.click-ostotz').click(function(){
		var destination = $('.header').offset().top;
		$("html,body").animate({scrollTop: destination}, 1000);
		return false;
	});

	/*callback*/
	$(".form-send-delivery").submit(function () {
	    var f = $(this);

        $(".error").remove();

	    $.post("/callback/contact/", f.serialize(), function (response)
	    {
			switch(response.status)
			{
				case 'success' :
				{
		        	$(f).html("");
		        	$.colorbox({href:"/callback/forms/popup_success.html",onOpen:function(){$("#cboxClose").css({'display':'none'});}});
				}   break;
				case 'fail' :
				{
		        	$.each(response.error, function( index, value )
		        	{
						if(value != "")
						{
	                    	$("input[name='email']",f).after("<div class='error'>"+value+"</div>");
						}
					});
				}   break;
			}
	    }, "json");

	    return false;
	});

	/*click request phone bottom*/
	$('.active-callme a').click(function ()
	{
		$('.phone-1').mask('+38(999) 999-99-99', {placeholder: "(099) 999-99-99"});

		$( ".vsplwrap" ).mouseenter(function() {
	    	$(".phone").focus();
		});

		var elem = $(".active-callme .headervspl-bottom").css("visibility");

		if(elem == "visible")
		{
			$('.active-callme').removeClass('active-callme-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.active-callme').addClass('active-callme-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

        return false;
	});

	/*check hidden*/
	var intervalID;

	$( ".active-callme .headervspl-bottom" ).on( "mouseleave", function(event) {

		intervalID = setInterval(function(){
			$('.active-callme').removeClass('active-callme-show').css({opacity: 1}).animate({opacity: 1}, 1500);

			clearInterval(intervalID);

			return false;
		}, 1000);

		return false;
	});

	$( ".active-callme .headervspl-bottom" ).on( "mouseenter", function(event) {

		clearInterval(intervalID);

		return false;
	});

	/*$( ".active-callme .vsplwrap" ).on( "mouseleave", function(event) {

		$('.active-callme').removeClass('active-callme-show').css({opacity: 1}).animate({opacity: 1}, 1500);

		return false;
	});*/

	$('.active-action a').click(function ()
	{
		var elem = $(".active-action .headervspl-bottom").css("visibility");

		if(elem == "visible")
		{
			$('.active-action').removeClass('active-action-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		}
		else
		{
			$('.active-action').addClass('active-action-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }

        return false;
	});

	/*check hidden*/
	var intervalID2;

	$( ".active-action .headervspl-bottom" ).on( "mouseleave", function(event) {

		intervalID2 = setInterval(function(){
			$('.active-action').removeClass('active-action-show').css({opacity: 1}).animate({opacity: 1}, 1500);

			clearInterval(intervalID2);

			return false;
		}, 1000);

		return false;
	});

	$( ".active-action .headervspl-bottom" ).on( "mouseenter", function(event) {

		clearInterval(intervalID2);

		return false;
	});

	/*$( ".active-action .vsplwrap" ).on( "mouseleave", function(event) {

		$('.active-action').removeClass('active-action-show').css({opacity: 1}).animate({opacity: 1}, 1500);

		return false;
	});*/

	/*click request phone*/
	if($("input[name='customer[phone]']").length)
	{
		$("input[name='customer[phone]']").mask('+38 999 999-99-99', {placeholder: "(099) 999-99-99"});

		/*$(".cart-button").click(function ()
		{

	        alert(1);
	        return false;
		});*/

    }

	$('.click-zakzv').click(function ()
	{
		$('.phone-1').mask('+38(999) 999-99-99', {placeholder: "(099) 999-99-99"});

		$( ".vsplwrap" ).mouseenter(function() {
	    	$(".phone").focus();
		});

		/*
		$check_live = true;
		$( ".zakzvvnutrbl" ).mouseover(function() {
		    if($check_live)
		    {
		    	$("#phone").focus();
			    $check_live = false;
		    }
		});*/

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

	  	//var elem = $(".zakzv-show .headervspl").css("visibility");

		/*if(elem == "visible")
		{*/
			$('.zakzv').removeClass('zakzv-show').css({opacity: 1}).animate({opacity: 1}, 1500);
		/*}
		else
		{
			$('.zakzv').addClass('zakzv-show').css({opacity: 1}).animate({opacity: 1}, 1500);
        }*/

		return false;
	});

	$(".form-send-phone-callback").submit(function () {
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
        {
        	$(".headervspl").addClass( "cart-hidden" );
        	$(".cart-link, .click-basket").attr("href", "#" );
        }
        else
        {
        	$(".cart-link, .click-basket").attr("href", "/cart/" );
        }
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
	        {
	        	$('#list-items').css({"display": "none"}).animate({opacity: 1}, 1500);
	        }
	        else
	        {
	        	$('#list-items').css({"display": "block"}).animate({opacity: 1}, 1500);
            }

        	$.each(response.data, function( index, value )
        	{
				var first_element = (index==0) ? " onebaskettov1" : "";

				return $( "<div class='show-item onebaskettov"+first_element+"'>" )
        		.append( "<div class='img'><a href='"+value.full_url+"'><img src="+value.img+" /></a></div>" )
	        	.append( "<a href='"+value.full_url+"'><div class='textbask'><p>"+value.name+"</p><p class='pricetovbasket'>"+value.price+"</p></div></a>" )
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

	//show promo code
	if($("input[name='coupon_code']").length)
	{
		if($("input[name='coupon_code']").val() != "")
		{
			$(".promo-show").addClass('open');
			$(".discount").addClass('open');
		}
	}

	$('.discount .promo a').on('click', function()
	{
		var $form = $(".promo-show");

		if( $form.hasClass('open') )
		{
			$form.animate({
				height: 'toggle'
				}, 100, function() {
					$form.hide();
					$form.removeClass('open');
					$(".discount").removeClass('open');
			});
		}
		else
		{
			$form.animate({
				height: 'toggle'
				}, 300, function() {
					$form.show();
					$(".discount").addClass('open');
					$form.addClass('open');
			});
		}
	});

	if($(".slideToggle").length)
	{
		slider_open = false;
		height = '190px';

		$('.slideToggle').live('click', function(){
			if(slider_open){
				$('.home-text').animate({'height':height}, 300);
				slider_open = false;
				$('.slider-end').animate({'height':'190px'}, 300);
				$('.slideToggle span').html("Подробнее...");
			} else {
				$('.home-text').css('height', '100%');
				auto_height = $('.home-text').height();
				$('.home-text').css('height', height);
				$('.home-text').animate({'height':auto_height}, 300);
				slider_open = true;
				$('.slideToggle span').html("Свернуть...");
			}
		});
	}

	/*- - - - - - - -*/
	/*   GA enhance  */
	/*- - - - - - - -*/
	/*banners - promo_click*/
	function onPromoClick (id, name, creative, position)
	{
		ga('ec:addPromo', {
			'id': id,
			'name': name,
			'creative': creative,
			'position': position
		});
		ga('ec:setAction', 'promo_click');
		ga('send', 'event', 'Internal Promotions', 'click', name);
	}

    /*show products - detail*/
	if($(".slide-product").length || $(".product-list").length || $(".onepohogtov").length || $(".onebaskettov").length)
	{
		/*
		slide-product - слайдеры на главной
		product-list - категория
		onepohogtov - список "с этим товаром можно купить"
		onebaskettov - корзина выпадашка
		*/

		i = 0;

		$(".slide-product, .product-list li, .onepohogtov, .onebaskettov").each(function()
		{
			var product_catalog = new Array();
	        var id = $(this).data("id");
	        var name = $(this).data("name");
	        var category = $(this).data("category");
	        var brand = $(this).data("brand");
	        var variant = $(this).data("variant");
	        var list = $(this).data("list");
            var position = $(this).data("position");

            if(position)
            {
            	i = position;
            }
            else
            {
            	i = i+1;
            }

	        product_catalog = {
				'id': id,
				'name': name,
				'category': category,
				'brand': brand,
				'variant': variant,
				'list': list,
				'position': i,
			};

	        /*if (window.console && console.log)
	        {
	        	console.log(product_catalog);
            }*/

			ga('ec:addImpression', product_catalog);
			ga('ec:setAction', 'detail');
		})
	}

	/*click product - click*/
	if($(".onProductClick").length)
	{
	    $(".onProductClick").click(function ()
	    {
	        var parent = $(this).closest('li');

		    if( parent.data("id") == undefined )
		    {
		        var parent = $(this).closest('.onepohogtov'); /*for recently list products*/
		    }

            var id = parent.data("id");
	        var name = parent.data("name");
	        var price = parent.data("price");
	        var brand = parent.data("brand");
	        var category = parent.data("category");
	        var position = parent.data("position");
	        var list = parent.data("list");

			var addProduct = new Array();

			addProduct = {
				'id': id,
				'name': name,
				'price': price,
				'brand': brand,
				'category': category,
				'position': position,
			}

	        /*if (window.console && console.log)
	        {
	        	console.log(addProduct);
			}*/

			ga('ec:addProduct', addProduct);
			ga('ec:setAction', 'click', {list: list});
			ga("send", "event", list, "click", "");
	    });
	}

	/*add to cart - add*/
	if($(".AddToCartProduct").length)
	{
	    $(".AddToCartProduct").click(function ()
	    {
	        var parent = $(this).closest('.buy_btn');
            var id = parent.data("id");
	        var name = parent.data("name");
	        var price = parent.data("price");
	        var brand = parent.data("brand");
	        var category = parent.data("category");
	        var variant = parent.data("variant");
	        var dimension1 = $('.sku-feature').val();// error

            var addProduct = new Array();

			addProduct = {
				"id": id,
				"name": name,
				"price": price,
				"brand": brand,
				"category": category,
				"variant": variant,
				"dimension1": dimension1,
				"position": 0,
				"quantity": 1
			}

	        /*if (window.console && console.log)
	        {
	        	console.log(addProduct);
			}*/

			ga('ec:addProduct', addProduct);
			ga("ec:setAction", "add");
			ga("send", "event", "detail view", "click", "addToCart");
	    });
	}

	/*remove from cart - remove*/
	if($(".RemoveFromCartProduct").length)
	{
	    $(".RemoveFromCartProduct").click(function ()
	    {
	        var parent = $(this).closest('.get-product-data');

		    if( parent.data("id") == undefined )
		    {
		        var parent = $(this).closest('.onebaskettov'); /*для корзины выпадашки*/
		    }

	        var id = parent.data("id");
	        var name = parent.data("name");
	        var price = parent.data("price");
	        var brand = parent.data("brand");
	        var category = parent.data("category");
	        var variant = parent.data("variant");
	        var dimension1 = parent.data("dimension1");

			var addProduct = new Array();

			addProduct = {
				"id": id,
				"name": name,
				"price": price,
				"brand": brand,
				"category": category,
				"variant": variant,
				"dimension1": dimension1,
				"position": 0,
				"quantity": 1
			}

	        /*if (window.console && console.log)
	        {
	        	console.log(addProduct);
			}*/

			ga('ec:addProduct', addProduct);
			ga("ec:setAction", "remove");
			ga("send", "event", "detail view", "click", "removeFromCart");
	    });
	}

	/*cart - checkout*/
	/*добавление оплаты и доставки в опции*/
	if($(".checkout_option").length)
	{
	    $(".checkout_option").click(function ()
	    {
            var f = $("form.checkout-form");
            var current = $(".checkout-step .checkout-step-content:visible");
            var current_step_id =  current.find(".checkout-content").data('step-id');

            if (!f.find('input[name="'+current_step_id+'_id"]:checked').not(':disabled').length)
            {
                return false;
            }
            else
            {
            	var option_name = f.find('input[name="'+current_step_id+'_id"]:checked').data("name");
            }

			if(current_step_id == "shipping")
			{
				var step = 3;
			}
			else if(current_step_id == "payment")
			{
				var step = 4;
			}

			var checkout_option = new Array();

			checkout_option = {
				"step": step,
				"option": option_name,
			}

	        /*if (window.console && console.log)
	        {
	        	console.log(checkout_option);
			}*/

			ga("ec:setAction", "checkout", {"step": step});

			ga("ec:setAction", "checkout_option", checkout_option);
			ga('send', 'event', 'Checkout', 'Option');

			$(".products-cart").each(function()
			{
				var addProduct = new Array();

		        var id = $(this).data("id");
		        var name = $(this).data("name");
		        var category = $(this).data("category");
		        var brand = $(this).data("brand");
		        var variant = $(this).data("variant");
		        var dimension1 = $(this).data("dimension1");
		        var price = $(this).data("price");
	            var quantity = $(this).data("quantity");

				addProduct = {
				    'id': id,
				    'name': name,
				    'price': price,
				    'brand': brand,
				    'category': category,
				    'variant': variant,
				    'dimension1': dimension1,
				    'position': i,
				    'quantity': quantity
				}

	            i = i+1;

		        /*if (window.console && console.log)
		        {
		        	console.log(addProduct);
				}*/

				ga('ec:addProduct', addProduct);
			})
	    });
	}

	/*перед отправкой формы в корзине*/
	if($(".purchase").length)
	{
	    $(".purchase").click(function ()
	    {
			var purchase = new Array();

			var total = $(".itogprice").data("total");
			var id_order = $(".itogprice").data("id_order");
			var coupon = $(".itogprice").data("coupon");

			purchase = {
				'id': ''+id_order+'',
				'revenue': ''+total+'',
				'tax': 0,
				'shipping': 0,
				'coupon': ''+coupon+''
			}

	        /*if (window.console && console.log)
	        {
	        	console.log(purchase);
			}*/

			ga('ec:setAction', 'purchase', purchase);
	    });
	}

	//get data from cart
	if($(".get-product-data").length || $(".products-cart").length)
	{
		i = 0;

		$(".get-product-data, .products-cart").each(function()
		{
			var addProduct = new Array();

	        var id = $(this).data("id");
	        var name = $(this).data("name");
	        var category = $(this).data("category");
	        var brand = $(this).data("brand");
	        var variant = $(this).data("variant");
	        var dimension1 = $(this).data("dimension1");
	        var price = $(this).data("price");
            var quantity = $(this).data("quantity");

			addProduct = {
			    'id': id,
			    'name': name,
			    'price': price,
			    'brand': brand,
			    'category': category,
			    'variant': variant,
			    'dimension1': dimension1,
			    'position': i,
			    'quantity': quantity
			}

            i = i+1;

	        /*if (window.console && console.log)
	        {
	        	console.log(addProduct);
			}*/

			ga('ec:addProduct', addProduct);
		})
	}

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

});


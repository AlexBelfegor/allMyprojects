$(function () {

   	/*init owl big carusel*/
   	if($(".owl-carousel").length)
   	{
   		initialize_owl_big($('.owl-carousel'),0);
   	}

   	/*init owl small carusel*/
   	if($(".owl-carousel1").length)
   	{
   		initialize_owl_small($('.owl-carousel1'),0);
    }

	/*owl big*/
	function initialize_owl_big(el,resize)
	{
	    el.owlCarousel({
	        loop:true,
	        margin:0,
	        nav:true,
	        dots: false,
	        responsiveClass:true,
	        responsive:{
	            0:{
	                items:1
	            },
	            600:{
	                items:1
	            },
	            940:{
	                items:4,
	                loop:true
	            }
	        }
	    })

		if(resize == 1)
		{
			el.each(function(){
				$(this).data('owlCarousel').onResize();
			});
		}
	}

	/*owl small*/
	function initialize_owl_small(el,resize)
	{
	    el.owlCarousel({
	        loop:true,
	        margin:1,
	        nav:true,
	        dots: false,
	        responsiveClass:true,
	        responsive:{
	            0:{
	                items:1
	            },
	            600:{
	                items:1
	            },
	            940:{
	                items:1,
	                loop:true
	            }
	        }
	    })

		if(resize == 1)
		{
			el.each(function(){
				$(this).data('owlCarousel').onResize();
			});
		}
	}

	/*destroy owl carusel*/
	function destroy_owl(el)
	{
		if(el.data('owlCarousel'))
	    {	    	el.data('owlCarousel').destroy();
	  	}
	}

	/*if use tabs pane reload owl carusel*/
	/*in product recently*/
	$('a[href="#products-recently"]').on('shown.bs.tab', function () {
    	initialize_owl_big($('.owl-carousel'),1);
    	initialize_owl_small($('.owl-carousel1'),1);
    }).on('hide.bs.tab', function () {
        destroy_owl($('.owl-carousel'));
        destroy_owl($('.owl-carousel1'));
    });

	/*in product similar*/
	$('a[href="#products-similar"]').on('shown.bs.tab', function () {
    	initialize_owl_big($('.owl-carousel'),1);
    	initialize_owl_small($('.owl-carousel1'),1);
    }).on('hide.bs.tab', function () {
        destroy_owl($('.owl-carousel'));
        destroy_owl($('.owl-carousel1'));
    });

	/*if use tabs pane reload owl carusel*/
	/*in home kateg1*/
	$('a[href="#kateg1"]').on('shown.bs.tab', function () {
    	initialize_owl_big($('.owl-carousel'),1);
    	initialize_owl_small($('.owl-carousel1'),1);
    }).on('hide.bs.tab', function () {
        destroy_owl($('.owl-carousel'));
        destroy_owl($('.owl-carousel1'));
    });

	/*in home kateg2*/
	$('a[href="#kateg2"]').on('shown.bs.tab', function () {
    	initialize_owl_big($('.owl-carousel'),1);
    	initialize_owl_small($('.owl-carousel1'),1);
    }).on('hide.bs.tab', function () {
        destroy_owl($('.owl-carousel'));
        destroy_owl($('.owl-carousel1'));
    });

	/*in home kateg3*/
	$('a[href="#kateg3"]').on('shown.bs.tab', function () {
    	initialize_owl_big($('.owl-carousel'),1);
    	initialize_owl_small($('.owl-carousel1'),1);
    }).on('hide.bs.tab', function () {
        destroy_owl($('.owl-carousel'));
        destroy_owl($('.owl-carousel1'));
    });

	/*in home kateg4*/
	$('a[href="#kateg4"]').on('shown.bs.tab', function () {
    	initialize_owl_big($('.owl-carousel'),1);
    	initialize_owl_small($('.owl-carousel1'),1);
    }).on('hide.bs.tab', function () {
        destroy_owl($('.owl-carousel'));
        destroy_owl($('.owl-carousel1'));
    });

	/*in home kateg5*/
	$('a[href="#kateg5"]').on('shown.bs.tab', function () {
    	initialize_owl_big($('.owl-carousel'),1);
    	initialize_owl_small($('.owl-carousel1'),1);
    }).on('hide.bs.tab', function () {
        destroy_owl($('.owl-carousel'));
        destroy_owl($('.owl-carousel1'));
    });

});


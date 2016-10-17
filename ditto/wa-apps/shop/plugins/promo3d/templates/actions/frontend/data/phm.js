jQuery(document).ready(function () {
    bindPhm3D();
});

function bindPhm3D()
{
        jQuery('.photo3d-preview-image').each( function () {

        var width = parseInt(jQuery(this).css('width').replace('px', ''));
        var text = width >= 300 ? "Загрузка подождите" : "Загрузка подождите";
        var domain = '';// Specify only if 3D images are loading from different domain ('http://yourdomain.com/');
        var imageName = jQuery(this).attr('src').replace(domain, '').replace('.jpg', '');
        var path = domain + imageName + '.3d/';
        var spath = imageName.split('/');
        imageName = spath[spath.length-1];

        phm3DPrepare(this, {speed:0.15, spacing:2, initialText:text,
                            frames:36, compact:false, maxZoom:250, zoomStep:50, brake:0.005,
                            footage:5, path:path,
                            scriptPath:domain + '/wa-apps/shop/plugins/promo3d/templates/actions/frontend/data/',
                            startImmediately:true,
							autoPlay:false,
                            image:imageName +'.3d.100.jpg'});
    });
}

phm3DPrepare = function (image, options) {
	if(typeof(options.scriptPath) == 'undefined')
		options.scriptPath = 'wa-apps/shop/plugins/promo3d/templates/actions/frontend/data/'

	var $img = jQuery(image);
	var width = $img.attr('width');//parseInt($img.css('width').replace('px', ''));
	var height = $img.attr('height'); //parseInt($img.css('height').replace('px', ''));

	options.maxZoom = $img.metadata().maxZoom;

	if(typeof($img.metadata().startImmediately) != "undefined")
		options.startImmediately = $img.metadata().startImmediately;

	if(typeof($img.metadata().autoPlay) != "undefined")
		options.autoPlay = $img.metadata().autoPlay;

    if(typeof($img.metadata().frames) != "undefined")
        options.frames = $img.metadata().frames;

	var compact = width >= 380 ? '' : (width >= 300 ? ' compact' : (width >= 195 ? ' small' : ' tiny'));

    var $control = jQuery("<div class='photo3d-control-div" + compact + "'>" +
        "<a style='display:none;' class='photo3d-start-3d-button" + compact + "' href='javascript:;'><div border='0' class='photo3d-360-view-icon-click" + compact + "' /><span class='photo3d-360-view-text-click" + compact + "'>" + options.initialText + "</span><div class='clear'></a>" +
    "</div>");

    var $control3d = jQuery("<div class='photo3d-control3d-div" + compact + "'>" +
        "<div class='photo3d-360-view-icon" + compact + "'></div>" +
        "<a class='photo3d-icon-button photo3d-play-backward-button" + compact + "' href='javascript:;'>&nbsp;</a>" +
        "<a class='photo3d-icon-button photo3d-stop-button" + compact + "' href='javascript:;'>&nbsp;</a>" +
        "<a class='photo3d-icon-button photo3d-play-forward-button" + compact + "' href='javascript:;'>&nbsp;</a>" +

	"<div class='photo3d-no-zoom" + compact + "'>" +
		"<div class='photo3d-zoom-icon" + compact + "'></div>" +
		"<div class='photo3d-zoom-slider-container" + compact + "'><div class='photo3d-zoom-slider" + compact + "'></div></div>" +
		"<div class='clear'></div>" +
	"</div>" +

        "<a class='photo3d-icon-button photo3d-hand-move-mode-button disabled" + compact + "' href='javascript:;'>&nbsp;</a>" +
        "<a class='photo3d-icon-button photo3d-hand-rotate-mode-button pressed" + compact + "' href='javascript:;'>&nbsp;</a>" +
        "<div class='clear'></div>" +
    "</div>");

    var curClass = $img.attr('class');
    $img.removeClass(curClass);
    var ssCurClass = curClass.split(' ');
    ssCurClass[0] = ssCurClass[0] + "-initialized";
    var sCurClass = "";
    for(var i = 0; i < ssCurClass.length; i++)
    {
        if(i > 0) sCurClass += " ";
        sCurClass += ssCurClass[i];
    }
    $img.addClass(sCurClass);
    var $div = jQuery("<div class='image-3d-container'></div>");
    $div.css({ width: width + 'px', height: height + 'px'});
    $img.replaceWith($div);
    $div.append($img);
    $div.append($control);

    $control.css({ left: ($div.width() - $control.width()) / 2, height:'44px' });


	if(options.startImmediately)
	{
		click_it();
	}
	else
	{
		jQuery('.photo3d-start-3d-button', $control).click(function (e) {
			if (!jQuery(this).is('.on')) {
				click_it(e);
				return false;
			}
		});
	}

    jQuery('.photo3d-play-forward-button', $control3d).click(function (e) {
		playForward();
    });

	function playForward() {
        $img.trigger('play');
        jQuery('.photo3d-play-forward-button', $control3d).addClass('pressed');
        jQuery('.photo3d-play-backward-button', $control3d).removeClass('pressed');

	}

    jQuery('.photo3d-play-backward-button', $control3d).click(function (e) {
        $img.trigger('play', -1);
        jQuery('.photo3d-play-forward-button', $control3d).removeClass('pressed');
        jQuery('.photo3d-play-backward-button', $control3d).addClass('pressed');
    });

    jQuery('.photo3d-stop-button', $control3d).click(function (e) {
        $img.trigger('stop');
        jQuery('.photo3d-play-forward-button', $control3d).removeClass('pressed');
        jQuery('.photo3d-play-backward-button', $control3d).removeClass('pressed');
    });

    jQuery('.photo3d-hand-move-mode-button', $control3d).click(function (e) {
        if (jQuery('.photo3d-hand-move-mode-button', $control3d).hasClass('disabled')) {
            return;
        }
        $img.trigger('moveMode');
        jQuery('.photo3d-hand-move-mode-button', $control3d).addClass('pressed');
        jQuery('.photo3d-hand-rotate-mode-button', $control3d).removeClass('pressed');
    });

    jQuery('.photo3d-hand-rotate-mode-button', $control3d).click(function (e) {
        $img.trigger('rotateMode');
        jQuery('.photo3d-hand-move-mode-button', $control3d).removeClass('pressed');
        jQuery('.photo3d-hand-rotate-mode-button', $control3d).addClass('pressed');
    });

    options.onStop = function () {
        jQuery('.photo3d-play-forward-button', $control3d).removeClass('pressed');
        jQuery('.photo3d-play-backward-button', $control3d).removeClass('pressed');
    };

    options.onZoomChange = function (newZoom, fromUI) {
        var currentZoom = parseInt(jQuery('.photo3d-zoom-slider', $control3d).slider('value'));

        if (!fromUI && currentZoom != newZoom) {
            jQuery('.photo3d-zoom-slider', $control3d).slider('value', newZoom);
        }

        if (currentZoom != newZoom) {
            if (newZoom == 100) {
                jQuery('.photo3d-hand-move-mode-button', $control3d).addClass('disabled');
                $img.trigger('rotateMode');
                jQuery('.photo3d-hand-move-mode-button', $control3d).removeClass('pressed');
                jQuery('.photo3d-hand-rotate-mode-button', $control3d).addClass('pressed');
            }
            else {
                jQuery('.photo3d-hand-move-mode-button', $control3d).removeClass('disabled');
                $img.trigger('moveMode');
                jQuery('.photo3d-hand-move-mode-button', $control3d).addClass('pressed');
                jQuery('.photo3d-hand-rotate-mode-button', $control3d).removeClass('pressed');
            }
        }
    }
    var sliderOptions = { min: 100, max: 100, step: 100,
        slide: function (e, ui) {
            $img.trigger('setZoom', ui.value);
        }
    };

    if (options.maxZoom)
        sliderOptions.max = options.maxZoom;

    if (options.zoomStep)
        sliderOptions.step = options.zoomStep;

    if (sliderOptions.max == sliderOptions.min) {
        jQuery('.photo3d-zoom-slider-container', $control3d).hide();
        jQuery('.photo3d-zoom-icon', $control3d).hide();
    }


    jQuery('.photo3d-zoom-slider', $control3d).slider(sliderOptions);

	function showPreloader ($target, space) {

            var $loadingLabel = jQuery('<div class="image-3d-loading-label"></div>'),
                loadingCss = { position: 'absolute', width: 146, height: 99, top: space.y / 2 - 30, left: space.x / 2 - 60, 'z-index': 10 };

            $loadingLabel.css(loadingCss);
            $loadingLabel.activity({ segments: 12, width: 3, space: 4, length: 10, color: '#eeeeee', speed: 1.0 });

            $target.append($loadingLabel);
            /*exit;*/
            jQuery('#image').css({'display':'none'})

            return $loadingLabel;
        }

    function click_it(e) {

		var $loadingLabel = showPreloader($div, {x:$img.width(), y:$img.height()});

		var src = $img.attr('src');
		var imageName = options.image || src.replace(/^(.*)\.(jpg|jpeg|png|gif)$/, '$1' + (options.suffix || '.3d') + '.' + 100 + '.$2')

		var $tempImage = jQuery(new Image()).hide().bind('load',

		function () {

			$loadingLabel.remove();

			var onoff = !$div.hasClass('on');

			$div[onoff ? 'addClass' : 'removeClass']('on');

			options.footage = parseInt(this.width / $img.width());

			if (onoff) {
				$control.replaceWith($control3d);
				$control3d.css({ left: ($div.width() - $control3d.width()) / 2 });
				$img.phm3dview(options);
			}


			if(options.autoPlay)
				playForward();

			return false;
		});

		$tempImage.attr('src', options.path + imageName);

		return false;
    }

}

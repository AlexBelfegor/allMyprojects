$(document).ajaxComplete(function(event, xhr, settings) {
	if ( typeof(filterValuesNames) !== "undefined" && (settings.url.indexOf("[]=") != -1 || settings.url.indexOf("?&_=") != -1) ) {

		if ( settings.url.indexOf("[]=") != -1 ) {
			var valuesUrl = JSON.parse(filterValuesNames),
				params = settings.url.replace('?', '').split('&'),
				codes = [];

			$.each(params, function(i, val) {
				if (val.indexOf("_=") == -1 && val.indexOf("sort") == -1 && val.indexOf("order") == -1 && val.indexOf("[unit]") == -1) {
					codes.push(val);
				}
		    });

			if (codes.length == 1) {
				var featureCode_tmp = codes[0].split('[]='),
					featureCode = featureCode_tmp[0],
					valueId = featureCode_tmp[1];
					valueUrl = valuesUrl[featureCode][valueId];

				if (valueUrl) {
					if (!!(history.pushState && history.state !== undefined)) {
						window.history.replaceState({}, '', categoryUrl + '_' + valueUrl + '/');
					}
				} else {
					if (!!(history.pushState && history.state !== undefined)) {
						window.history.replaceState({}, '', categoryUrl + settings.url);
					}
				}
			} else {
				if (!!(history.pushState && history.state !== undefined)) {
					window.history.replaceState({}, '', categoryUrl + settings.url);
				}
			}
		} else if ( settings.url.indexOf("?&_=") != -1 ) {
			if (!!(history.pushState && history.state !== undefined)) {
				window.history.replaceState({}, '', categoryUrl);
			}
		}

		$('.category-name').html($(xhr.responseText).find('.category-name').html());
		$('.category-desc').html($(xhr.responseText).find('.category-desc').html());
		document.title = $(xhr.responseText).filter('.html-title').html();

	}
});

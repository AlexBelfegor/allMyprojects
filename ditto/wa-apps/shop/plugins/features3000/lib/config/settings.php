<?php
return array(
	'enabled_theme_template'  => array(
        'value'        => false,
        'title'        => 'Шаблон из темы',
        'description'  => 'При включении шаблон фильтра будет браться из директории темы, имя шаблона <u><b>filter3000.html</b></u>. Если выключено, то из поставки плагина <u><b>templates/actions/frontend/FrontendFilter.html</b></u>',
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'enabled_price_hack' => array(
        'value'        => false,
        'title'        => 'Хак для фильтра цены',
        'description'  => 'Хак нужен, когда в магазине используется несколько валют, на данный момент Shop-Script 5 делает фильтрацию цены только по основной валюте. Для этого фильтр цены , перед отправкой формы фильтра, конвертирует цену в основную валюту.',
        'control_type' => waHtmlControl::CHECKBOX,
    ),
  
);
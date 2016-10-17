<?php

/*
 * @author Gaponov Igor <gapon2401@gmail.com>
 */

class shopQuickorderPlugin extends shopPlugin
{

    private static $quickorder_settings;

    /**
     * Get html of quickorder form
     * @param array $product - product fields
     * @return html
     */
    public static function quickorderForm($product)
    {
        // Если товар есть в наличии
        if (self::productAvailible($product)) {
            $form = "";
            // Получаем поля формы быстрой покупки
            $fields = shopQuickorderPlugin::getFields();
            // Если полей не существует, то не показываем кнопку быстрой покупки
            if ($fields) {
                // Добавляем форму и поля
                $form .= shopQuickorderPlugin::getFormHtml($product);
            }
            return $form;
        }
    }

    public function frontendHead()
    {
        $settings = self::getQuickorderSettings();
        return "<script type='text/javascript' src='" . wa()->getAppStaticUrl('shop') . "plugins/quickorder/js/quickorder.js'></script>
                <script type='text/javascript'>jQuery(document).ready(function($) { $.quickorder.locale = '" . wa()->getLocale() . "'; $.quickorder.aftercallback = '" . (!empty($settings['js_code']) ? preg_replace(array("/[\r\n]/", "/\'/"), array('', '\"'), $settings['js_code']) : "") . "'; });</script>
                <link rel='stylesheet' href='" . wa()->getAppStaticUrl('shop') . "plugins/quickorder/css/quickorderFrontend.css" . "'>";
    }

    public function frontendCart()
    {
        $settings = self::getQuickorderSettings();
        if (!empty($settings['enable_frontend_cart_hook'])) {
            return self::submitCart();
        }
    }

    /**
     * Check, if product is availible
     * @param array $product
     * @return boolean
     */
    private static function productAvailible($product)
    {
        $product_available = true;
        // Если вариантов товара больше одного, то проверку наличия осуществляем в js
        // Если товар один, проверяем его наличие
        if (isset($product['skus'])) {
            if (count($product['skus']) > 1) {
                return $product_available;
            } else {
                $sku = $product['skus'][$product['sku_id']];
                $product_available = $product['status'] && $sku['available'] && (htmlspecialchars(wa('shop')->getConfig()->getGeneralSettings('ignore_stock_count')) || $sku['count'] === null || $sku['count'] > 0);
            }
        } else {
            $product_available = htmlspecialchars(wa('shop')->getConfig()->getGeneralSettings('ignore_stock_count')) || $product['count'] === null || $product['count'] > 0;
        }
        return $product_available;
    }

    public function frontendProduct($product)
    {
        //Настройки
        $settings = shopQuickorderPlugin::getQuickorderSettings();

        if (isset($product['data'])) {
            $product = $product['data'];
        }
        // Если товар есть в наличии
        if (self::productAvailible($product) && $settings['enable_frontend_product_hook']) {
            // Получаем поля формы быстрой покупки
            $fields = shopQuickorderPlugin::getFields();
            // Если полей не существует, то не показываем кнопку быстрой покупки
            if ($fields) {
                $output = array();
                // Добавляем форму и поля
                $output['cart'] = shopQuickorderPlugin::getFormHtml($product);
                $output['menu'] = '';
                $output['block_aux'] = '';
                $output['block'] = '';
                return $output;
            }
        }
    }

    /*BW*/
    public function frontendHeadSimple()
    {
        $settings = self::getQuickorderSettings();

        return "<script type='text/javascript' src='" . wa()->getAppStaticUrl('shop') . "plugins/quickorder/js/quickorder.js'></script>
                <script type='text/javascript'>jQuery(document).ready(function($) { $.quickorder.locale = '" . wa()->getLocale() . "'; $.quickorder.aftercallback = '" . (!empty($settings['js_code']) ? preg_replace(array("/[\r\n]/", "/\'/"), array('', '\"'), $settings['js_code']) : "") . "'; });</script>
        ";
    }

    public function frontendPopup($product)
    {
        //Настройки
        $settings = shopQuickorderPlugin::getQuickorderSettings();

        if (isset($product['data'])) {
            $product = $product['data'];
        }
        // Если товар есть в наличии
        if (self::productAvailible($product)) {
            // Получаем поля формы быстрой покупки
            $fields = shopQuickorderPlugin::getFields();
            // Если полей не существует, то не показываем кнопку быстрой покупки
            if ($fields)
            {
                $output = shopQuickorderPlugin::frontendHeadSimple();
                $output .= shopQuickorderPlugin::getFormHtmlSimple($product);

                echo $output;
            }
        }
    }

    private static function getFormHtmlSimple($product = null, $cart_submit = false)
    {
        // Чтобы заработала локаль
        waSystem::pushActivePlugin('quickorder', 'shop');
        // Все поля контакта
        $fields = waContactFields::getAll();
        // Используемые поля
        $active_fields = shopQuickorderPlugin::getFields();
        // Поля контакта
        $contact_fields = wa()->getUser()->isAuth() ? wa()->getUser()->load() : array();
        //Настройки
        $settings = self::getQuickorderSettings();
        $url = wa()->getRouteUrl('shop/frontend/sendQuickorder');

        $html = "<div class='form'>";
        $html .= "<div class='quickorder-body'>";

        foreach ($active_fields as $f) {
            // Если поле не относится к адресу
            if ($f['field_type'] !== 'address') {
                // Если поле существует
                $html .= shopQuickorderPlugin::getFieldHtmlSimple($fields, $f, $contact_fields);
            } else {
                $address_fields = $fields['address']->getFields();
                $html .= shopQuickorderPlugin::getFieldHtmlSimple($address_fields, $f, $contact_fields);
            }
        }
        // Комментарий пользователя к заказу
        if (isset($settings['enable_user_comment']) && $settings['enable_user_comment']) {
            $html .= "<div class='quickorder-row'><div class='quickorder-name'>" . _wp('Comment to the order') . "</div>";
            $html .= "<div class='quickorder-value'><textarea name='quickorder_user_comment'></textarea>";
            $html .= "</div></div>"; // End of div.quickorder-row
        }
        // Текст в форме оформления заказа
        if (isset($settings['form_text'])) {
            $html .= $settings['form_text'];
        }
        if (!$cart_submit) {
            $html .= "<input type='hidden' name='product[product_id]' value='" . $product['id'] . "' />";
            $html .= "<input type='hidden' name='product[sku_id]' value='" . $product['sku_id'] . "' />";
        }
        $html .= "<em class='errormsg'></em>";
        $html .= "<div class='submit'><input type='submit' value='Купить в один клик' class='quickorder-button' onclick=\"ga('send', 'event', 'knopka', 'click', 'spasibo_click');return true;\" /></div>";
        $html .= "</div>"; // End of div.quickorder-body
        $html .= "</div>"; // End of div.quickorder-wrap
        // Чтобы не ломать локаль в шаблонах магазина
        waSystem::popActivePlugin();
        return $html;
    }

    private static function getFieldHtmlSimple($fields, $field, $contact_fields)
    {
        $html = "";
        // Если поле существует
        if (isset($fields[$field['field_value']])) {
            // В случае, если поле является полем адреса, то получаем последний адрес из списка
            $contact_address = ($field['field_type'] == 'address' && !empty($contact_fields['address']) ? array_pop($contact_fields['address']) : array());

            $name = ($field['field_name'] ? $field['field_name'] : $fields[$field['field_value']]->getName());
            $html .= "<div class='input'><label>".$name." ".(!isset($field['required']) || $field['required'] ? "<span>*</span>" : "")."</label>";
            $html .= "<div>";
            // Если имеются опции
            if ($fields[$field['field_value']] instanceof waContactSelectField) {
                $options = $fields[$field['field_value']]->getOptions();
                $html .= "<select ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." name='fields[" . ($field['field_type'] == 'address' ? "address" . ($contact_address ? "." . $contact_address['ext'] : "") . "::" : "") . $fields[$field['field_value']]->getId() . "]'>";
                foreach ($options as $opt_id => $opt) {
                    $html .= "<option value='" . $opt_id . "' " . (isset($contact_fields[$field['field_value']]) ? ($contact_fields[$field['field_value']] == $opt_id ? "selected='selected'" : "") : (isset($contact_address['data'][$field['field_value']]) && $contact_address['data'][$field['field_value']] == $opt_id ? "selected='selected'" : "")) . ">" . $opt . "</option>";
                }
                $html .= "</select>";
            }
            // Если передано поле контакта, а не адреса, и оно имеет несколько значений,
            // то выводим их списком
            elseif (!empty($contact_fields[$field['field_value']]) && is_array($contact_fields[$field['field_value']]) && $field['field_type'] !== 'address') {
                $ext = $fields[$field['field_value']]->getParameter('ext');
                foreach ($contact_fields[$field['field_value']] as $cf_id => $cf) {
                    $html .= "<input placeholder='".$name."' type='text' ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." name='fields[" . $fields[$field['field_value']]->getId() . "][" . $cf_id . "][value]' value='" . $cf['value'] . "'>";
                    if ($cf['ext']) {
                        $html .= "<input type='hidden' name='fields[" . $fields[$field['field_value']]->getId() . "][" . $cf_id . "][ext]' value='" . $ext[strtolower($cf['ext'])] . "'>";
                    }
                }
            }
            // Если передано обычное строковое поле
            else {
                $value = (!empty($contact_fields[$field['field_value']]) ? $contact_fields[$field['field_value']] : (isset($contact_address['data'][$field['field_value']]) ? $contact_address['data'][$field['field_value']] : ""));
                // Если передано поле типа TEXT, то выводим значение в textarea
                if ($fields[$field['field_value']] instanceof waContactTextField) {
                    $html .= "<textarea placeholder='".$name."' ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." name='fields[" . ($field['field_type'] == 'address' ? "address" . ($contact_address ? "." . $contact_address['ext'] : "") . "::" : "") . $fields[$field['field_value']]->getId() . "]'>" . $value . "</textarea>";
                } else {
                    $html .= "<input placeholder='".$name."' type='text' ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." name ='fields[" . ($field['field_type'] == 'address' ? "address" . ($contact_address ? "." . $contact_address['ext'] : "") . "::" : "") . $fields[$field['field_value']]->getId() . "]' value='" . $value . "' />";
                }
            }
            $html .= "</div></div>"; // End of div.quickorder-value
        }

        return $html;
    }
    /*BW end*/

    /**
     * Get html-code of quickorder-form
     * @param array $product
     * @return string
     */
    private static function getFormHtml($product = null, $cart_submit = false)
    {
        // Чтобы заработала локаль
        waSystem::pushActivePlugin('quickorder', 'shop');
        // Все поля контакта
        $fields = waContactFields::getAll();
        // Используемые поля
        $active_fields = shopQuickorderPlugin::getFields();
        // Поля контакта
        $contact_fields = wa()->getUser()->isAuth() ? wa()->getUser()->load() : array();
        //Настройки
        $settings = self::getQuickorderSettings();
        $url = wa()->getRouteUrl('shop/frontend/sendQuickorder');
        $html = "<div class='" . ($cart_submit ? "quickorder-custom-button-cart " : "quickorder-custom-button ").(!empty($settings['style']) ? htmlentities($settings['style']) : "") . "'>";
        $html .= "<a href='javascript:void(0)' onclick='$.quickorder.dialog.show(this" . ($cart_submit ? ", \"" . wa()->getRouteUrl('shop/frontend/getCartQuickorder') . "\"" : "") . ");' class='" . ($cart_submit ? "quickorder-button-cart" : "quickorder-button") . "'>" . ($cart_submit ? $settings['cart_button_name'] : $settings['button_name']) . "</a>";
        $html .= "</div>";
        $html .= "<div class='quickorder-custom-form ".(!empty($settings['style']) ? htmlentities($settings['style']) : "")."'>";
        $html .= "<div class='quickorder-wrap' data-action = '" . $url . "'>
                    <div class='quickorder-header'>
                        <span>" . ($cart_submit ? $settings['cart_button_name'] : $settings['button_name']) . "</span>
                        <i class='close' onclick='$.quickorder.dialog.hide(this);'></i>
                    </div>
                    <div class='quickorder-body'>
                    ";
        $html .= "<div class='quickorder-name'>" . _wp('Item') . ":</div>
                  <div class='quickorder-value quickorder-order'>" . ($cart_submit ? "<i class='icon16 qp loading temp-loader'></i>" : "<div class='quickorder-item'><span class='quickorder-order-image'>" . shopQuickorderPluginHelper::productImgHtml($product, "48x48") . "</span><span class='quickorder-order-name'>" . htmlspecialchars($product['name']) . "</span></div>") . "</div>";
        foreach ($active_fields as $f) {
            // Если поле не относится к адресу
            if ($f['field_type'] !== 'address') {
                // Если поле существует
                $html .= shopQuickorderPlugin::getFieldHtml($fields, $f, $contact_fields);
            } else {
                $address_fields = $fields['address']->getFields();
                $html .= shopQuickorderPlugin::getFieldHtml($address_fields, $f, $contact_fields);
            }
        }
        // Комментарий пользователя к заказу
        if (isset($settings['enable_user_comment']) && $settings['enable_user_comment']) {
            $html .= "<div class='quickorder-row'><div class='quickorder-name'>" . _wp('Comment to the order') . "</div>";
            $html .= "<div class='quickorder-value'><textarea name='quickorder_user_comment'></textarea>";
            $html .= "</div></div>"; // End of div.quickorder-row
        }
        // Текст в форме оформления заказа
        if (isset($settings['form_text'])) {
            $html .= $settings['form_text'];
        }
        if (!$cart_submit) {
            $html .= "<input type='hidden' name='product[product_id]' value='" . $product['id'] . "' disabled='disabled' />";
            $html .= "<input type='hidden' name='product[sku_id]' value='" . $product['sku_id'] . "' disabled='disabled' />";
        }
        $html .= "<em class='errormsg'></em>";
        $html .= "<div class='quickorder-submit'><input type='submit' value='" . _wp('Buy it') . "' class='quickorder-button' onclick='ga(\"send\", \"event\", \"knopka\", \"click\", \"spasibo_click\");'/></div>";
        $html .= "</div>"; // End of div.quickorder-body
        $html .= "</div>"; // End of div.quickorder-wrap
        $html .= "</div>"; // End of div.quickorder-custom-form
        // Чтобы не ломать локаль в шаблонах магазина
        waSystem::popActivePlugin();
        return $html;
    }

    /**
     * Get html-code of field
     * @param array $fields - all contact fields
     * @param string $field_id - field id
     * @return string - html code
     */
    private static function getFieldHtml($fields, $field, $contact_fields)
    {
        $html = "";
        // Если поле существует
        if (isset($fields[$field['field_value']])) {
            // В случае, если поле является полем адреса, то получаем последний адрес из списка
            $contact_address = ($field['field_type'] == 'address' && !empty($contact_fields['address']) ? array_pop($contact_fields['address']) : array());
            $html .= "<div class='quickorder-row'><div class='quickorder-name ".(!isset($field['required']) || $field['required'] ? "required" : "")."'>" . ($field['field_name'] ? $field['field_name'] : $fields[$field['field_value']]->getName()) . "</div>";
            $html .= "<div class='quickorder-value'>";
            // Если имеются опции
            if ($fields[$field['field_value']] instanceof waContactSelectField) {
                $options = $fields[$field['field_value']]->getOptions();
                $html .= "<select ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." disabled='disabled' name='fields[" . ($field['field_type'] == 'address' ? "address" . ($contact_address ? "." . $contact_address['ext'] : "") . "::" : "") . $fields[$field['field_value']]->getId() . "]'>";
                foreach ($options as $opt_id => $opt) {
                    $html .= "<option value='" . $opt_id . "' " . (isset($contact_fields[$field['field_value']]) ? ($contact_fields[$field['field_value']] == $opt_id ? "selected='selected'" : "") : (isset($contact_address['data'][$field['field_value']]) && $contact_address['data'][$field['field_value']] == $opt_id ? "selected='selected'" : "")) . ">" . $opt . "</option>";
                }
                $html .= "</select>";
            }
            // Если передано поле контакта, а не адреса, и оно имеет несколько значений,
            // то выводим их списком
            elseif (!empty($contact_fields[$field['field_value']]) && is_array($contact_fields[$field['field_value']]) && $field['field_type'] !== 'address') {
                $ext = $fields[$field['field_value']]->getParameter('ext');
                foreach ($contact_fields[$field['field_value']] as $cf_id => $cf) {
                    $html .= "<input type='text' disabled='disabled' ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." name='fields[" . $fields[$field['field_value']]->getId() . "][" . $cf_id . "][value]' value='" . $cf['value'] . "'>";
                    if ($cf['ext']) {
                        $html .= "<input type='hidden' disabled='disabled' name='fields[" . $fields[$field['field_value']]->getId() . "][" . $cf_id . "][ext]' value='" . $ext[strtolower($cf['ext'])] . "'>";
                    }
                }
            }
            // Если передано обычное строковое поле
            else {
                $value = (!empty($contact_fields[$field['field_value']]) ? $contact_fields[$field['field_value']] : (isset($contact_address['data'][$field['field_value']]) ? $contact_address['data'][$field['field_value']] : ""));
                // Если передано поле типа TEXT, то выводим значение в textarea
                if ($fields[$field['field_value']] instanceof waContactTextField) {
                    $html .= "<textarea disabled='disabled' ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." name='fields[" . ($field['field_type'] == 'address' ? "address" . ($contact_address ? "." . $contact_address['ext'] : "") . "::" : "") . $fields[$field['field_value']]->getId() . "]'>" . $value . "</textarea>";
                } else {
                    $html .= "<input disabled='disabled' type='text' ".(!isset($field['required']) || $field['required'] ? "class='f-required'" : "")." name ='fields[" . ($field['field_type'] == 'address' ? "address" . ($contact_address ? "." . $contact_address['ext'] : "") . "::" : "") . $fields[$field['field_value']]->getId() . "]' value='" . $value . "' />";
                }
            }
            $html .= "</div></div>"; // End of div.quickorder-value
        }
        return $html;
    }

    /**
     * Get path to file
     * @param string $file - filename or path
     * @param $original - if true - return original path to file
     * @return string - protected path to file
     */
    public static function path($file, $original = false)
    {
        $path = wa()->getDataPath('plugins/quickorder/' . $file, false, 'shop', true);
        if ($original) {
            return dirname(__FILE__) . '/config/' . $file;
        }
        if (!file_exists($path)) {
            waFiles::copy(dirname(__FILE__) . '/config/' . $file, $path);
        }
        return $path;
    }

    /**
     * Get fields
     * @return array
     */
    public static function getFields()
    {
        $fields = include shopQuickorderPlugin::path("fields.php");
        return $fields;
    }

    /**
     * Get quickorder button for the cart
     * @return type
     */
    public static function submitCart()
    {
        $form = "";
        // Получаем поля формы быстрой покупки
        $fields = shopQuickorderPlugin::getFields();
        // Если полей не существует, то не показываем кнопку быстрой покупки
        if ($fields) {
            // Добавляем форму и поля
            $form .= shopQuickorderPlugin::getFormHtml(null, true);
        }
        return $form;
    }

    /**
     * Get plugin settings
     */
    public static function getQuickorderSettings()
    {
        if (!self::$quickorder_settings) {
            self::$quickorder_settings = include shopQuickorderPlugin::path('config.php');
        }
        return self::$quickorder_settings;
    }

}
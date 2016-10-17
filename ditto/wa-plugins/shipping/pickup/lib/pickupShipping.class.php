<?php

/**
 *
 * @property-read array $rate_zone
 * @property-read string $rate_by
 * @property-read string $currency
 * @property-read array $rate
 * @property-read string $delivery_time
 * @property-read string $prompt_address
 */
class pickupShipping extends waShipping
{
    /**
     * Example of direct usage HTML templates instead waHtmlControl
     * (non-PHPdoc)
     * @see waShipping::getSettingsHTML()
     * @param array $params
     * @return string HTML
     */
    public function getSettingsHTML($params = array())
    {
        $values = $this->getSettings();
        if (!empty($params['value'])) {
            $values = array_merge($values, $params['value']);
        }

        $view = wa()->getView();

        $app_config = wa()->getConfig();
        if (method_exists($app_config, 'getCurrencies')) {
            $view->assign('currencies', $app_config->getCurrencies());
        }

        $namespace = '';
        if (!empty($params['namespace'])) {
            if (is_array($params['namespace'])) {
                $namespace = array_shift($params['namespace']);
                while (($namspace_chunk = array_shift($params['namespace'])) !== null) {
                    $namespace .= "[{$namspace_chunk}]";
                }
            } else {
                $namespace = $params['namespace'];
            }
        }

        $view->assign('namespace', $namespace);
        $view->assign('values', $values);
        $view->assign('p', $this);

        $html = '';
        $html .= $view->fetch($this->path.'/templates/settings.html');
        $html .= parent::getSettingsHTML($params);

        return $html;
    }

    protected function calculate()
    {
        $currency = $this->currency;
        $rates = $this->rate;

        $deliveries = array();
        $i = 1;    // start from index 1
        foreach ($rates as $rate) {
            $deliveries[$i++] = array(
                'name'         => $rate['location'],
                'currency'     => $currency,
                'rate'         => $rate['cost'],
                'est_delivery' => ''
            );
        }

        return $deliveries;
    }

    public function allowedCurrency()
    {
        return $this->currency;
    }

    public function getPrintForms(waOrder $order = null)
    {
        return array(
            'delivery_list' => array(
                'name'        => _wd('shipping_pickup', 'Packing list'),
                'description' => _wd('shipping_pickup', 'Order summary for pickup shipping'),
            ),
        );
    }

    public function displayPrintForm($id, waOrder $order, $params = array())
    {
        if ($id = 'delivery_list') {
            $view = wa()->getView();
            $main_contact_info = array();
            foreach (array('email', 'phone',) as $f) {
                if (($v = $order->contact->get($f, 'top,html'))) {
                    $main_contact_info[] = array(
                        'id'    => $f,
                        'name'  => waContactFields::get($f)->getName(),
                        'value' => is_array($v) ? implode(', ', $v) : $v,
                    );
                }
            }

            $formatter = new waContactAddressSeveralLinesFormatter();
            $shipping_address = array();
            foreach (waContactFields::get('address')->getFields() as $k => $v) {
                if (isset($order->params['shipping_address.'.$k])) {
                    $shipping_address[$k] = $order->params['shipping_address.'.$k];
                }
            }

            $shipping_address_text = array();
            foreach (array('country_name', 'region_name', 'zip', 'city', 'street') as $k) {
                if (!empty($order->shipping_address[$k])) {
                    $shipping_address_text[] = $order->shipping_address[$k];
                }
            }

            $shipping_address_text = implode(', ', $shipping_address_text);
            $map = '';
            if ($shipping_address_text) {
                $map_adapter = $this->getSettings('map');
                if (!$map_adapter) {
                    $map_adapter = 'google';
                }
                try {
                    $map = wa()->getMap($map_adapter)->getHTML($shipping_address_text, array(
                        'width' => '100%', 'height' => '350pt', 'zoom' => 16
                    ));
                } catch (waException $e) {
                    $map = '';
                }
            }
            $view->assign('map', $map);

            $shipping_address = $formatter->format(array('data' => $shipping_address));
            $shipping_address = $shipping_address['value'];

            $view->assign(compact('shipping_address_text', 'shipping_address', 'main_contact_info', 'order', 'params'));
            $view->assign('p', $this);
            return $view->fetch($this->path.'/templates/form.html');
        } else {
            throw new waException('Print form not found');
        }
    }

    public function allowedWeightUnit()
    {
        return 'kg';
    }

    public function requestedAddressFields()
    {
        return $this->prompt_address ? array() : false;
    }
}

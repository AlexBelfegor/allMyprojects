<?php

class shopManagerPluginReportsAction extends waViewAction
{
    public function execute()
    {
        list($start_date, $end_date) = self::getTimeframeParams();
        $order_model = new shopManagerOrderModel();
        $data = $order_model->getReportsByManager($start_date, $end_date);
        $total = array(
            'profit' => 0,
            'purchase' => 0,
            'shipping' => 0,
            'sales' => 0,
            'tax' => 0,
            'count' => 0
        );
        $total['count_deleted'] = $order_model->countDeleted($start_date, $end_date);

        $payments_count = $order_model->countPayments($start_date, $end_date);
        $plugin_model = new shopPluginModel();
        $plugins = $plugin_model->getByField('id', array_keys($payments_count), 'id');
        $payments = array();
        foreach ($payments_count as $payment_id => $p) {
            if (isset($plugins[$payment_id])) {
                $payments[$payment_id] = $plugins[$payment_id];
            } else {
                $payments[$payment_id] = array(
                    'name' => $payment_id ? 'способ оплаты удалён' : 'Не указан'
                );
            }
            $payments[$payment_id]['count'] = $p['count'];
            $payments[$payment_id]['total'] = $p['total'];
        }

        $this->view->assign('payments', $payments);


        $deleted_orders = $order_model->countDeletedByManager($start_date, $end_date);
        $users = shopManagerPlugin::getUsers();
        foreach ($data as $manager_id => &$row) {
            $total['sales'] += $row['sales'];
            $total['profit'] += $row['profit'];
            $total['purchase'] += $row['purchase'];
            $total['shipping'] += $row['shipping'];
            $total['tax'] += $row['tax'];
            $total['count'] += $row['count'];
            $row['count_deleted'] = ifset($deleted_orders[$manager_id], 0);
            $row['sales_no_shipping'] = $row['sales'] - $row['shipping'];
        }
        unset($row);

        $total['count_all'] = $total['count'] + $total['count_deleted'];
        /**
         * @var shopManagerPlugin $plugin
         */
        $plugin = wa()->getPlugin('manager');
        $bonus_type = $plugin->getSettings('bonus_type');
        $user_type = $plugin->getSettings('bonus_user_type');
        if ($user_type == 'value') {
            $user_type = $bonus_type;
        }

        $is_bonus = $plugin->getSettings('bonus_model');
        if ($is_bonus == 'common') {
            $bonus_total = $plugin->getSettings('bonus_total');
            if ($bonus_total) {
                if ($bonus_type == 'sales_no_shipping') {
                    $bonus_value = $total['sales'] - $total['shipping'];
                } elseif (isset($total[$bonus_type])) {
                    $bonus_value = $total[$bonus_type];
                } else {
                    $bonus_value = 0;
                }
                $total['bonus'] = $bonus_total = $bonus_value * (float)$bonus_total / 100;
                $total['cost'] = $total['count'] ? $total['bonus'] / $total['count'] : 0;

                $bonus_users = $plugin->getSettings('bonus');
                $users_total = 0;
                $users_count = 0;
                foreach ($bonus_users as $manager_id => $n) {
                    if (isset($data[$manager_id])) {
                        if ($n === '') {
                            $n = $bonus_users[$manager_id] = 100;
                        }
                        $users_total += $n * $data[$manager_id][$user_type];
                        $users_count++;
                    }
                }

                foreach ($data as $row) {
                    if (!isset($bonus_users[$row['manager_id']])) {
                        $bonus_users[$row['manager_id']] = 100;
                        $users_total += 100 * $row[$user_type];
                        $users_count++;
                    }
                }

                foreach ($data as &$row) {
                    if (!empty($bonus_users[$row['manager_id']])) {
                        $row['efficiency'] = (float)$bonus_users[$row['manager_id']];
                        $row['efficiency_real'] = $row['efficiency'] * $row[$user_type] / max(1, $users_total);
                        $row['bonus'] = $bonus_total * $row['efficiency_real'];
                        $row['efficiency_real'] *= $users_count;
                    }
                }
                unset($row);
            } else {
                $is_bonus = false;
            }
        } elseif ($is_bonus == 'personal') {
            $bonus_users = $plugin->getSettings('bonus');
            $total['bonus'] = 0;
            foreach ($data as &$row) {
                if (!empty($bonus_users[$row['manager_id']])) {
                    $row['bonus'] = $row[$bonus_type] * (float)$bonus_users[$row['manager_id']] / 100;
                    $total['bonus'] += $row['bonus'];
                }
            }
            unset($row);
        }

        $this->view->assign(array(
            'total' => $total,
            'data' => $data,
            'users' => $users,
            'is_bonus' => $is_bonus
        ));
    }

    public static function getTimeframeParams()
    {
        $timeframe = waRequest::request('timeframe');
        if ($timeframe === 'all') {
            $start_date = null;
            $end_date = null;
        } else if ($timeframe == 'custom') {
            $from = waRequest::request('from', 0, 'int');
            $start_date = $from ? date('Y-m-d', $from) : null;

            $to = waRequest::request('to', 0, 'int');
            $end_date = $to ? date('Y-m-d', $to) : null;
        } else {
            if (!wa_is_int($timeframe)) {
                $timeframe = 30;
            }
            $start_date = date('Y-m-d', time() - $timeframe*24*3600);
            $end_date = null;
        }
        return array($start_date, $end_date);
    }

}
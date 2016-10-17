<?
return array(
   'cron_callback_update_products_discount'=>'04 30 * * * /usr/bin/php -q '.wa()->getConfig()->getPath('root').DIRECTORY_SEPARATOR.'cli.php shop CallbackCronDiscount',
   'cron_callback_update_products_stock'=>'05 00 * * * /usr/bin/php -q '.wa()->getConfig()->getPath('root').DIRECTORY_SEPARATOR.'cli.php shop CallbackCronInstock',
);
<?
return array(
   'cron_update_redirect'=>'04 30 * * * /usr/bin/php -q '.wa()->getConfig()->getPath('root').DIRECTORY_SEPARATOR.'cli.php shop BwredirectCronImport',
);

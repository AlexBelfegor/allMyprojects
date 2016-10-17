<?php

require_once __DIR__.'/src/Earnberry.php';

Earnberry::createInstance('52a0a2e2cd440dbb638b4567', 'd85604153656179ac77da409c517a199b575c2bf');
Earnberry::setEncoding('WINDOWS-1251');

Earnberry::getInstance()->setApiUrl('http://earnberry.dev.ggg.org.ua/');

try {
    $t = new EarnberryTransaction();
    //$t->setCurrency('VND');
    //$t->setDryRun(true);
    $t->addProduct('pik-1', 'Aik', 'Tuhlo', 350, 1);
    $t->setTrackingId(14);
    $res = $t->send();
    var_dump($res);
}
catch (EarnberryException $e)
{
    echo $e->getMessage().PHP_EOL;
}
<?php
$db = new shopError301Model();
$db->query("ALTER TABLE `".$db->table."` ADD UNIQUE INDEX `unqkey` (`type`, `url`, `parent`);");     
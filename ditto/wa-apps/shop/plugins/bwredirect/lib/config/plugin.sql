DROP TABLE IF EXISTS `shop_product_promo`;
CREATE TABLE `shop_product_bwredirect` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`url_from` VARCHAR( 1000 ) NOT NULL ,
`url_to` VARCHAR( 1000 ) NOT NULL ,
`redirect` VARCHAR( 10 ) NOT NULL ,
`status` TINYINT NOT NULL DEFAULT '0'
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

ALTER TABLE `shop_product_bwredirect` ADD `datetime` DATETIME NOT NULL AFTER `status`

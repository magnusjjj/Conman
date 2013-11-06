CREATE TABLE  IF NOT EXISTS  `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `event` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE  IF NOT EXISTS  `log_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_user_id` int(11) NOT NULL COMMENT 'owner user id',
  `to_user_id` int(11) NOT NULL COMMENT 'recipient user id',
  PRIMARY KEY (`id`),
  KEY `when` (`when`,`from_user_id`,`to_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Ticket transfer log';

CREATE TABLE  IF NOT EXISTS  `log_transfer_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_transfer_id` int(11) NOT NULL,
  `from_order_id` int(11) NOT NULL COMMENT 'owner''s affected order id',
  `to_order_id` int(11) NOT NULL COMMENT 'recipient''s new order id',
  `alternative_id` int(11) NOT NULL,
  `ammount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `log_transfer_id` (`log_transfer_id`),
  KEY `from_order_id` (`from_order_id`),
  KEY `to_order_id` (`to_order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='binding products (alternative_id) and amount to a ticket tra';

CREATE TABLE  IF NOT EXISTS  `members` (
  `PersonID` int(11) NOT NULL AUTO_INCREMENT,
  `socialSecurityNumber` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `gender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `firstName` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `coAddress` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `streetAddress` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `zipCode` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Sverige',
  `phoneNr` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `altPhoneNr` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `eMail` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `memberFee` int(11) NOT NULL DEFAULT '20',
  `membershipBegan` datetime NOT NULL,
  `membershipEnds` datetime NOT NULL,
  `registeredAtEvent` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Personligen av Styrelsen',
  `memberSince` datetime NOT NULL,
  `hkMemberID` int(11) DEFAULT NULL,
  PRIMARY KEY (`PersonID`),
  UNIQUE KEY `socialSecurityNumber` (`socialSecurityNumber`),
  UNIQUE KEY `hkMemberID` (`hkMemberID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Hikari-kai member list';

CREATE TABLE  IF NOT EXISTS  `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payson_token` varchar(38) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'NOTPAYED',
  `code_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE  IF NOT EXISTS `orders_alternatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `cost` int(11) NOT NULL,
  `template_override` varchar(255) CHARACTER SET latin1 NOT NULL,
  `extra` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ammount` int(11) NOT NULL,
  `max_per_user` int(11) NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `max_in_view` int(11) NOT NULL DEFAULT '10',
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE  IF NOT EXISTS `orders_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `used_by` int(11) NOT NULL,
  `reduction` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE  IF NOT EXISTS `orders_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_value_id` int(11) DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE  IF NOT EXISTS  `orders_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_alternative_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  `given` tinyint(4) NOT NULL,
  `ammount` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE  IF NOT EXISTS `payapi_save` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `extref_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `extref_other` varchar(255) NOT NULL,
  `originalresponse` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE  IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(20) NOT NULL,
  `member_id` int(11) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `entrance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE  IF NOT EXISTS  `verificationcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ssn` char(11) NOT NULL,
  `code` char(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


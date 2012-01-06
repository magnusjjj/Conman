CREATE TABLE IF NOT EXISTS `members` (
  `PersonID` int(11) NOT NULL auto_increment,
  `socialSecurityNumber` varchar(12) collate utf8_unicode_ci NOT NULL,
  `gender` char(1) collate utf8_unicode_ci NOT NULL,
  `firstName` varchar(128) collate utf8_unicode_ci NOT NULL,
  `lastName` varchar(128) collate utf8_unicode_ci NOT NULL,
  `coAddress` varchar(128) collate utf8_unicode_ci NOT NULL,
  `streetAddress` varchar(128) collate utf8_unicode_ci NOT NULL,
  `zipCode` varchar(6) collate utf8_unicode_ci NOT NULL,
  `city` varchar(64) collate utf8_unicode_ci NOT NULL,
  `country` varchar(64) collate utf8_unicode_ci NOT NULL default 'Sverige',
  `phoneNr` varchar(64) collate utf8_unicode_ci NOT NULL,
  `altPhoneNr` varchar(50) collate utf8_unicode_ci NOT NULL,
  `eMail` varchar(250) collate utf8_unicode_ci NOT NULL,
  `memberFee` int(11) NOT NULL default '20',
  `membershipBegan` datetime NOT NULL,
  `membershipEnds` datetime NOT NULL,
  `registeredAtEvent` varchar(128) collate utf8_unicode_ci NOT NULL default 'Personligen av Styrelsen',
  `memberSince` datetime NOT NULL,
  `hkMemberID` int(11) default NULL,
  PRIMARY KEY  (`PersonID`),
  UNIQUE KEY `socialSecurityNumber` (`socialSecurityNumber`),
  UNIQUE KEY `hkMemberID` (`hkMemberID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Hikari-kai member list';

-- --------------------------------------------------------

--
-- Struktur för tabell `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `payson_token` varchar(38) NOT NULL,
  `status` varchar(10) NOT NULL default 'NOTPAYED',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur för tabell `orders_alternatives`
--

CREATE TABLE IF NOT EXISTS `orders_alternatives` (
  `id` int(11) NOT NULL auto_increment,
  `parent` int(11) default NULL,
  `name` varchar(255) character set latin1 NOT NULL,
  `cost` int(11) NOT NULL,
  `template_override` varchar(255) character set latin1 NOT NULL,
  `extra` varchar(255) character set latin1 NOT NULL,
  `ammount` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Struktur för tabell `orders_settings`
--

CREATE TABLE IF NOT EXISTS `orders_settings` (
  `id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `order_value_id` int(11) default NULL,
  `value` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Struktur för tabell `orders_values`
--

CREATE TABLE IF NOT EXISTS `orders_values` (
  `id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `order_alternative_id` int(11) NOT NULL,
  `value` varchar(255) collate utf8_bin NOT NULL,
  `given` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Struktur för tabell `payson_trace`
--

CREATE TABLE IF NOT EXISTS `payson_trace` (
  `id` int(11) NOT NULL auto_increment,
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur för tabell `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(80) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(20) NOT NULL,
  `member_id` int(11) NOT NULL,
  `admin` int(11) NOT NULL default '0',
  `entrance` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur för tabell `verificationcodes`
--

CREATE TABLE IF NOT EXISTS `verificationcodes` (
  `id` int(11) NOT NULL auto_increment,
  `ssn` char(11) NOT NULL,
  `code` char(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

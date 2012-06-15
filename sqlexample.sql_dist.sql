SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `event` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Hikari-kai member list' AUTO_INCREMENT=1914 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payson_token` varchar(38) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'NOTPAYED',
  `code_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2807 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_alternatives`
--

CREATE TABLE IF NOT EXISTS `orders_alternatives` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_codes`
--

CREATE TABLE IF NOT EXISTS `orders_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `used_by` int(11) NOT NULL,
  `reduction` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1394 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_settings`
--

CREATE TABLE IF NOT EXISTS `orders_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_value_id` int(11) DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `orders_values`
--

CREATE TABLE IF NOT EXISTS `orders_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_alternative_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_bin NOT NULL,
  `given` tinyint(4) NOT NULL,
  `ammount` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6857 ;

-- --------------------------------------------------------

--
-- Table structure for table `payson_trace`
--

CREATE TABLE IF NOT EXISTS `payson_trace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1835 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(20) NOT NULL,
  `member_id` int(11) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `entrance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1869 ;

-- --------------------------------------------------------

--
-- Table structure for table `verificationcodes`
--

CREATE TABLE IF NOT EXISTS `verificationcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ssn` char(11) NOT NULL,
  `code` char(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2837 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

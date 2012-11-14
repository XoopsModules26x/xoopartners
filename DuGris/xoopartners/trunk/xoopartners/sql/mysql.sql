CREATE TABLE `xoopartners` (
  `xoopartners_id` int(5) NOT NULL AUTO_INCREMENT,
  `xoopartners_category` int(5) NOT NULL,
  `xoopartners_title` varchar(100) NOT NULL,
  `xoopartners_description` text NOT NULL,
  `xoopartners_image` text NOT NULL,
  `xoopartners_url` varchar(100) NOT NULL,
  `xoopartners_order` tinyint(3) NOT NULL DEFAULT '0',
  `xoopartners_display` tinyint(1) NOT NULL DEFAULT '1',
  `xoopartners_visit` int(11) NOT NULL DEFAULT '0',
  `xoopartners_view` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xoopartners_id`),
  KEY `xoopartners_category` (`xoopartners_category`),
  KEY `xoopartners_title` (`xoopartners_title`),
  KEY `xoopartners_order` (`xoopartners_order`),
  KEY `xoopartners_display` (`xoopartners_display`)
) ENGINE=MyISAM;

CREATE TABLE `xoopartners_categories` (
  `xoopartners_category_id` int(5) NOT NULL AUTO_INCREMENT,
  `xoopartners_category_parent_id` int(5) NOT NULL,
  `xoopartners_category_title` varchar(100) NOT NULL,
  `xoopartners_category_description` text NOT NULL,
  `xoopartners_category_image` varchar(100) NOT NULL DEFAULT '0',
  `xoopartners_category_order` tinyint(5) NOT NULL,
  `xoopartners_category_display` tinyint(1) NOT NULL DEFAULT '1',
  `xoopartners_category_partners` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xoopartners_category_id`),
  KEY `xoopartners_category_parent_id` (`xoopartners_category_parent_id`,`xoopartners_category_title`),
  KEY `xoopartners_category_title` (`xoopartners_category_title`),
  KEY `xoopartners_category_order` (`xoopartners_category_order`),
  KEY `xoopartners_category_display` (`xoopartners_category_display`)
) ENGINE=MyISAM;

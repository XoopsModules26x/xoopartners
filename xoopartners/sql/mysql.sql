CREATE TABLE `xoopartners` (
  `xoopartners_id` int(5) NOT NULL AUTO_INCREMENT,
  `xoopartners_category` int(5) NOT NULL,
  `xoopartners_title` varchar(100) NOT NULL,
  `xoopartners_description` text NOT NULL,
  `xoopartners_image` text NOT NULL,
  `xoopartners_url` varchar(100) NOT NULL,
  `xoopartners_order` tinyint(3) NOT NULL DEFAULT '0',
  `xoopartners_online` tinyint(1) NOT NULL DEFAULT '1',
  `xoopartners_visit` int(11) NOT NULL DEFAULT '0',
  `xoopartners_hits` int(11) NOT NULL DEFAULT '0',
  `xoopartners_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `xoopartners_rates` float NOT NULL DEFAULT '0',
  `xoopartners_like` int(11) NOT NULL DEFAULT '0',
  `xoopartners_dislike` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xoopartners_id`)
) ENGINE=MyISAM;

CREATE TABLE `xoopartners_categories` (
  `xoopartners_category_id` int(5) NOT NULL AUTO_INCREMENT,
  `xoopartners_category_parent_id` int(5) NOT NULL,
  `xoopartners_category_title` varchar(100) NOT NULL,
  `xoopartners_category_description` text NOT NULL,
  `xoopartners_category_image` varchar(100) NOT NULL DEFAULT '0',
  `xoopartners_category_order` tinyint(5) NOT NULL,
  `xoopartners_category_online` tinyint(1) NOT NULL DEFAULT '1',
  `xoopartners_category_partners` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xoopartners_category_id`)
) ENGINE=MyISAM;

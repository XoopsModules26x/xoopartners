CREATE TABLE `xoopartners` (
  `xoopartners_id`          INT(5)       NOT NULL AUTO_INCREMENT,
  `xoopartners_category`    INT(5)       NOT NULL,
  `xoopartners_uid`         INT(8)       NOT NULL DEFAULT '0',
  `xoopartners_title`       VARCHAR(100) NOT NULL,
  `xoopartners_description` TEXT         NOT NULL,
  `xoopartners_image`       TEXT         NOT NULL,
  `xoopartners_url`         VARCHAR(100) NOT NULL,
  `xoopartners_order`       TINYINT(3)   NOT NULL DEFAULT '0',
  `xoopartners_online`      TINYINT(1)   NOT NULL DEFAULT '1',
  `xoopartners_visit`       INT(11)      NOT NULL DEFAULT '0',
  `xoopartners_hits`        INT(11)      NOT NULL DEFAULT '0',
  `xoopartners_accepted`    TINYINT(1)   NOT NULL DEFAULT '0',
  `xoopartners_rates`       FLOAT(5, 2)  NOT NULL DEFAULT '0.00',
  `xoopartners_like`        INT(11)      NOT NULL DEFAULT '0',
  `xoopartners_dislike`     INT(11)      NOT NULL DEFAULT '0',
  `xoopartners_comments`    INT(11)      NOT NULL DEFAULT '0',
  `xoopartners_published`   INT(10)      NOT NULL,
  PRIMARY KEY (`xoopartners_id`)
)
  ENGINE = MyISAM;

CREATE TABLE `xoopartners_categories` (
  `xoopartners_category_id`          INT(5)       NOT NULL AUTO_INCREMENT,
  `xoopartners_category_parent_id`   INT(5)       NOT NULL,
  `xoopartners_category_title`       VARCHAR(100) NOT NULL,
  `xoopartners_category_description` TEXT         NOT NULL,
  `xoopartners_category_image`       VARCHAR(100) NOT NULL DEFAULT '0',
  `xoopartners_category_order`       INT(5)       NOT NULL,
  `xoopartners_category_online`      TINYINT(1)   NOT NULL DEFAULT '1',
  `xoopartners_category_partners`    TINYINT(1)   NOT NULL DEFAULT '0',
  PRIMARY KEY (`xoopartners_category_id`)
)
  ENGINE = MyISAM;

CREATE TABLE `xoopartners_rld` (
  `xoopartners_rld_id`      INT(11)    NOT NULL AUTO_INCREMENT,
  `xoopartners_rld_partner` INT(11)    NOT NULL DEFAULT '0',
  `xoopartners_rld_uid`     INT(11)    NOT NULL DEFAULT '0',
  `xoopartners_rld_time`    INT(10)    NOT NULL DEFAULT '0',
  `xoopartners_rld_ip`      MEDIUMTEXT NOT NULL,
  `xoopartners_rld_rates`   TINYINT(2) NOT NULL DEFAULT '0',
  `xoopartners_rld_like`    TINYINT(1) NOT NULL DEFAULT '0',
  `xoopartners_rld_dislike` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`xoopartners_rld_id`)
)
  ENGINE = MyISAM
  COMMENT = 'RLD = Rates / Like / Dislike';

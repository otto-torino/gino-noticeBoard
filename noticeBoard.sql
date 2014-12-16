--
-- Permissions
--
INSERT INTO `auth_permission` (`class`, `code`, `label`, `description`, `admin`) VALUES
('noticeBoard', 'can_admin', 'Amministrazione albo pretorio', 'gestione completa modulo', 1);

--
-- Table structure for table `notice_board_category`
--

CREATE TABLE IF NOT EXISTS `notice_board_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


--
-- Table structure for table `notice_board_deliberative`
--

CREATE TABLE IF NOT EXISTS `notice_board_deliberative` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `notice_board_item`
--

CREATE TABLE IF NOT EXISTS `notice_board_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `deliberative` int(11) DEFAULT NULL,
  `protocol_number` varchar(128) NOT NULL,
  `act_date` date NOT NULL,
  `act_number` varchar(128) NOT NULL,
  `publication_date_begin` date NOT NULL,
  `publication_date_end` date NOT NULL,
  `object` varchar(255) NOT NULL,
  `notes` text,
  `insertion_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `notice_board_item_attachment`
--

CREATE TABLE IF NOT EXISTS `notice_board_item_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noticeboarditem_id` int(11) NOT NULL,
  `noticeboardcategory_id` int(11) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `filesize` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

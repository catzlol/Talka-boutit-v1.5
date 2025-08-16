-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 19, 2024 at 12:06 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admin_id` int NOT NULL,
  `action` varchar(60) NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aeo_table`
--

DROP TABLE IF EXISTS `aeo_table`;
CREATE TABLE IF NOT EXISTS `aeo_table` (
  `winnerip` varchar(1000) NOT NULL,
  PRIMARY KEY (`winnerip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `avatar`
--

DROP TABLE IF EXISTS `avatar`;
CREATE TABLE IF NOT EXISTS `avatar` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `head_color` varchar(6) NOT NULL,
  `torso_color` varchar(6) NOT NULL,
  `right_arm_color` varchar(6) NOT NULL,
  `left_arm_color` varchar(6) NOT NULL,
  `right_leg_color` varchar(6) NOT NULL,
  `left_leg_color` varchar(6) NOT NULL,
  `face` int NOT NULL DEFAULT '0',
  `shirt` int NOT NULL DEFAULT '0',
  `pants` int NOT NULL DEFAULT '0',
  `tshirt` int NOT NULL DEFAULT '0',
  `hat1` int NOT NULL DEFAULT '0',
  `hat2` int NOT NULL DEFAULT '0',
  `hat3` int NOT NULL DEFAULT '0',
  `hat4` int NOT NULL DEFAULT '0',
  `hat5` int NOT NULL DEFAULT '0',
  `tool` int NOT NULL DEFAULT '0',
  `head` int NOT NULL DEFAULT '0',
  `cache` int NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `avatar`
--

INSERT INTO `avatar` (`user_id`, `head_color`, `torso_color`, `right_arm_color`, `left_arm_color`, `right_leg_color`, `left_leg_color`, `face`, `shirt`, `pants`, `tshirt`, `hat1`, `hat2`, `hat3`, `hat4`, `hat5`, `tool`, `head`, `cache`) VALUES
(1, 'f3b700', '85ad00', 'f3b700', 'f3b700', '1c4399', '1c4399', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 'f3b700', 'c60000', 'f3b700', 'f3b700', '1c4399', '1c4399', 0, 0, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

DROP TABLE IF EXISTS `awards`;
CREATE TABLE IF NOT EXISTS `awards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `beta_buy`
--

DROP TABLE IF EXISTS `beta_buy`;
CREATE TABLE IF NOT EXISTS `beta_buy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `gross` decimal(5,2) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `beta_users`
--

DROP TABLE IF EXISTS `beta_users`;
CREATE TABLE IF NOT EXISTS `beta_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(26) NOT NULL,
  `usernameL` varchar(100) NOT NULL,
  `password` varchar(70) NOT NULL,
  `IP` varchar(46) NOT NULL,
  `birth` date NOT NULL,
  `gender` enum('male','female','hidden') NOT NULL DEFAULT 'hidden',
  `date` date DEFAULT NULL,
  `last_online` datetime NOT NULL,
  `daily_bits` datetime NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `views` int NOT NULL,
  `bucks` int NOT NULL DEFAULT '1',
  `bits` int NOT NULL DEFAULT '10',
  `primary_group` int DEFAULT '-1',
  `power` int NOT NULL DEFAULT '0',
  `avatar_id` int NOT NULL,
  `unique_key` varchar(20) NOT NULL,
  `theme` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `beta_users`
--

INSERT INTO `beta_users` (`id`, `username`, `usernameL`, `password`, `IP`, `birth`, `gender`, `date`, `last_online`, `daily_bits`, `description`, `views`, `bucks`, `bits`, `primary_group`, `power`, `avatar_id`, `unique_key`, `theme`) VALUES
(1, 'catzlol', 'catzlol', '$2y$10$iQDJuUg1YFsROtKBldqCHeW4l9XSLIDPT4H610IFathUOgB8U/XJm', '127.0.0.1', '2023-01-01', 'hidden', '2024-07-20', '2024-08-02 05:51:49', '2024-08-02 05:51:49', '', 9, 1, 30, -1, 0, 0, '1b6a90c2b95275b1bff2', 0),
(2, 'other', 'other', '$2y$10$mCzaXQtFxNyCOdyu73kIJeJFrxbJtPsk0aj3YRyGixUDaiaPBlDuG', '127.0.0.1', '2023-01-01', 'male', '2024-07-20', '2024-08-02 05:55:54', '2024-08-02 05:53:25', '', 11, 1, 30, -1, 0, 0, 'dc097fbc2dce69a58b32', 0);

-- --------------------------------------------------------

--
-- Table structure for table `clans`
--

DROP TABLE IF EXISTS `clans`;
CREATE TABLE IF NOT EXISTS `clans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `name` varchar(26) NOT NULL,
  `tag` varchar(4) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `members` int NOT NULL,
  `approved` enum('yes','no','declined') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clans_members`
--

DROP TABLE IF EXISTS `clans_members`;
CREATE TABLE IF NOT EXISTS `clans_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rank` int NOT NULL DEFAULT '1',
  `status` enum('in','out','banned') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clans_ranks`
--

DROP TABLE IF EXISTS `clans_ranks`;
CREATE TABLE IF NOT EXISTS `clans_ranks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `power` int NOT NULL,
  `name` varchar(26) NOT NULL,
  `perm_ranks` enum('yes','no') NOT NULL,
  `perm_posts` enum('yes','no') NOT NULL,
  `perm_members` enum('yes','no') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clans_walls`
--

DROP TABLE IF EXISTS `clans_walls`;
CREATE TABLE IF NOT EXISTS `clans_walls` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `post` varchar(100) NOT NULL,
  `time` datetime NOT NULL,
  `type` enum('pinned','normal','deleted') NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `crate`
--

DROP TABLE IF EXISTS `crate`;
CREATE TABLE IF NOT EXISTS `crate` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `serial` int NOT NULL DEFAULT '0',
  `payment` enum('bits','bucks') NOT NULL DEFAULT 'bits',
  `price` int NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `own` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crate`
--

INSERT INTO `crate` (`id`, `user_id`, `item_id`, `serial`, `payment`, `price`, `date`, `own`) VALUES
(1, 1, 1, 1, 'bits', 0, '2024-07-20 22:20:57', 'yes'),
(2, 2, 8, 1, 'bits', 0, '2024-07-20 22:35:35', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `email` varchar(100) NOT NULL,
  `verified` enum('yes','no') NOT NULL DEFAULT 'no',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `user_id`, `email`, `verified`, `date`) VALUES
(1, 1, 'butthole@gmail.com', 'no', '2024-07-20 22:20:57'),
(2, 2, 'yes@no.com', 'no', '2024-07-20 22:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `forum_banners`
--

DROP TABLE IF EXISTS `forum_banners`;
CREATE TABLE IF NOT EXISTS `forum_banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_boards`
--

DROP TABLE IF EXISTS `forum_boards`;
CREATE TABLE IF NOT EXISTS `forum_boards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(26) NOT NULL,
  `description` varchar(128) DEFAULT NULL,
  `userid` int NOT NULL,
  `mods` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

DROP TABLE IF EXISTS `forum_posts`;
CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `thread_id` int NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `points` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forum_threads`
--

DROP TABLE IF EXISTS `forum_threads`;
CREATE TABLE IF NOT EXISTS `forum_threads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `board_id` int NOT NULL,
  `title` text NOT NULL,
  `body` text NOT NULL,
  `locked` enum('yes','no') NOT NULL DEFAULT 'no',
  `pinned` enum('yes','no') NOT NULL DEFAULT 'no',
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `views` int NOT NULL,
  `latest_post` int NOT NULL,
  `points` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
CREATE TABLE IF NOT EXISTS `friends` (
  `id` int NOT NULL AUTO_INCREMENT,
  `from_id` int NOT NULL,
  `to_id` int NOT NULL,
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `from_id`, `to_id`, `status`) VALUES
(1, 1, 2, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
CREATE TABLE IF NOT EXISTS `games` (
  `id` int NOT NULL AUTO_INCREMENT,
  `creator_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `playing` int NOT NULL DEFAULT '0',
  `visits` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` varchar(15) NOT NULL,
  `uid` varchar(20) NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gc_chats`
--

DROP TABLE IF EXISTS `gc_chats`;
CREATE TABLE IF NOT EXISTS `gc_chats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_chats`
--

INSERT INTO `gc_chats` (`id`, `name`, `created_at`) VALUES
(1, 'catz', '2024-07-22 10:22:25'),
(2, 'other', '2024-07-22 13:34:53');

-- --------------------------------------------------------

--
-- Table structure for table `gc_members`
--

DROP TABLE IF EXISTS `gc_members`;
CREATE TABLE IF NOT EXISTS `gc_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chat_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_members`
--

INSERT INTO `gc_members` (`id`, `chat_id`, `user_id`, `joined_at`) VALUES
(1, 1, 1, '2024-07-22 10:22:25'),
(2, 1, 1, '2024-07-22 10:22:25'),
(3, 1, 2, '2024-07-22 10:22:25'),
(4, 2, 1, '2024-07-22 13:34:53'),
(5, 2, 1, '2024-07-22 13:34:53'),
(6, 2, 2, '2024-07-22 13:34:53');

-- --------------------------------------------------------

--
-- Table structure for table `gc_messages`
--

DROP TABLE IF EXISTS `gc_messages`;
CREATE TABLE IF NOT EXISTS `gc_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chat_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `message` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gc_messages`
--

INSERT INTO `gc_messages` (`id`, `chat_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, NULL, 'hi', '2024-07-22 11:47:03'),
(2, 1, NULL, 'a', '2024-07-22 11:47:21'),
(3, 1, NULL, 'a', '2024-07-22 11:48:03'),
(4, 1, NULL, 'hi', '2024-07-22 11:50:07'),
(5, 1, NULL, 'a', '2024-07-22 11:50:10'),
(6, 1, NULL, 'a', '2024-07-22 11:50:11'),
(7, 1, NULL, 'hi', '2024-07-22 11:53:38'),
(8, 1, NULL, 'a', '2024-07-22 11:58:00'),
(9, 1, NULL, 'a', '2024-07-22 11:58:37'),
(10, 1, NULL, 'few', '2024-07-22 11:58:37'),
(11, 1, NULL, 'gew', '2024-07-22 11:58:38'),
(12, 1, 1, 'hi', '2024-07-22 12:05:30'),
(13, 1, 2, 'waa', '2024-07-22 12:06:30'),
(14, 0, 1, 'a', '2024-07-22 12:11:14'),
(15, 0, 1, 'a', '2024-07-22 12:11:18'),
(16, 0, 1, 'asfsaasas', '2024-07-22 12:11:22'),
(17, 1, 1, 'ok', '2024-07-22 12:11:31'),
(18, 0, 2, 'yes', '2024-07-22 12:11:56'),
(19, 1, 2, 'a', '2024-07-22 12:12:48'),
(20, 1, 2, 'a', '2024-07-22 12:12:50'),
(21, 1, 2, 'no', '2024-07-22 12:12:55'),
(22, 1, 1, 'ok', '2024-07-22 12:16:48'),
(23, 1, 2, 'fuck you', '2024-07-22 12:17:07'),
(24, 1, 1, 'lmfao', '2024-07-22 12:17:18'),
(25, 1, 1, 'YO', '2024-07-22 12:22:12'),
(26, 1, 1, 'hi', '2024-07-22 12:44:51'),
(27, 1, 1, 'real', '2024-07-22 13:34:22'),
(28, 2, 1, 'yes', '2024-07-22 13:35:05'),
(29, 2, 1, 'afs', '2024-07-22 13:35:09'),
(30, 2, 1, 'asffas', '2024-07-22 13:35:09'),
(31, 2, 1, 'fas', '2024-07-22 13:35:10'),
(32, 2, 1, 'fas', '2024-07-22 13:35:10'),
(33, 2, 1, 'sa', '2024-07-22 13:35:10'),
(34, 2, 1, 'as', '2024-07-22 13:35:10'),
(35, 2, 1, 'fas', '2024-07-22 13:35:11'),
(36, 2, 1, 'fasf', '2024-07-22 13:35:11'),
(37, 2, 1, 'as', '2024-07-22 13:35:11'),
(38, 2, 1, 'as', '2024-07-22 13:35:11'),
(39, 2, 1, 'fas\'assa', '2024-07-22 13:35:12'),
(40, 2, 1, 'fas', '2024-07-22 13:35:12'),
(41, 2, 1, 'asf', '2024-07-22 13:35:12'),
(42, 2, 1, 'as', '2024-07-22 13:35:13'),
(43, 2, 1, 'fas', '2024-07-22 13:35:13'),
(44, 2, 1, 'as', '2024-07-22 13:35:13'),
(45, 2, 1, 'sa', '2024-07-22 13:35:13'),
(46, 2, 1, 'as', '2024-07-22 13:35:13'),
(47, 2, 1, 'as', '2024-07-22 13:35:14'),
(48, 2, 1, 'asf', '2024-07-22 13:35:14'),
(49, 2, 1, 'sa', '2024-07-22 13:35:14');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `creator_id` int NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `type` enum('hat','shirt') NOT NULL,
  `robux` int NOT NULL,
  `tickets` int NOT NULL,
  `method` enum('free','both','robux','tickets','offsale') NOT NULL,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_comments`
--

DROP TABLE IF EXISTS `item_comments`;
CREATE TABLE IF NOT EXISTS `item_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` int NOT NULL,
  `item_id` int NOT NULL,
  `comment` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
CREATE TABLE IF NOT EXISTS `list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `uploaded` enum('Yes','No') NOT NULL,
  `bits` int NOT NULL DEFAULT '-1',
  `bucks` int NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action` varchar(100) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`id`, `action`, `date`) VALUES
(1, 'User 1 logged in from 47.221.57.19', '2024-07-22 00:57:14'),
(2, 'User 1 logged in from 47.221.57.19', '2024-07-22 05:01:15'),
(3, 'User 2 logged in from 47.221.57.19', '2024-07-22 10:37:33'),
(4, 'User 2 logged in from 127.0.0.1', '2024-07-22 11:02:32'),
(5, 'User 2 logged in from 47.221.57.19', '2024-07-22 11:02:45'),
(6, 'User 1 logged in from 47.221.57.19', '2024-07-22 12:35:05'),
(7, 'User 1 logged in from 127.0.0.1', '2024-07-22 12:57:46'),
(8, 'User 1 logged in from 47.221.57.19', '2024-07-22 12:58:11'),
(9, 'User 1 logged in from 47.221.57.19', '2024-08-01 23:51:49'),
(10, 'User 2 logged in from 47.221.57.19', '2024-08-01 23:53:25'),
(11, 'User 2 logged in from 47.221.57.19', '2024-08-01 23:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

DROP TABLE IF EXISTS `membership`;
CREATE TABLE IF NOT EXISTS `membership` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `membership` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `length` int NOT NULL,
  `active` enum('yes','no') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `membership_values`
--

DROP TABLE IF EXISTS `membership_values`;
CREATE TABLE IF NOT EXISTS `membership_values` (
  `value` int NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  `daily_bucks` int NOT NULL,
  `sets` int NOT NULL,
  `items` int NOT NULL,
  `create_clans` int NOT NULL,
  `join_clans` int NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `author_id` varchar(26) NOT NULL,
  `recipient_id` int NOT NULL,
  `date` date NOT NULL,
  `title` varchar(52) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `read` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `misc`
--

DROP TABLE IF EXISTS `misc`;
CREATE TABLE IF NOT EXISTS `misc` (
  `featured_game_id` varchar(1) NOT NULL,
  `alert` text NOT NULL,
  KEY `featured_game_id` (`featured_game_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `moderation`
--

DROP TABLE IF EXISTS `moderation`;
CREATE TABLE IF NOT EXISTS `moderation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `admin_id` int NOT NULL,
  `offensive_content` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `admin_note` text NOT NULL,
  `ban_content` text NOT NULL,
  `issued` datetime NOT NULL,
  `length` int NOT NULL,
  `active` enum('yes','no') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `gross` decimal(5,2) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(100) NOT NULL,
  `receipt` varchar(60) NOT NULL,
  `product` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reg_keys`
--

DROP TABLE IF EXISTS `reg_keys`;
CREATE TABLE IF NOT EXISTS `reg_keys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key_content` varchar(1000) NOT NULL,
  `used` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `r_type` varchar(10) NOT NULL,
  `r_id` int NOT NULL,
  `r_reason` text,
  `seen` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shop_items`
--

DROP TABLE IF EXISTS `shop_items`;
CREATE TABLE IF NOT EXISTS `shop_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `name` varchar(52) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `bucks` int NOT NULL DEFAULT '-1',
  `bits` int NOT NULL DEFAULT '-1',
  `type` varchar(10) NOT NULL COMMENT 'HAT | FACE | TOOL | SHIRT | TSHIRT | PANTS ',
  `date` date NOT NULL,
  `last_updated` date NOT NULL,
  `offsale` enum('yes','no') NOT NULL DEFAULT 'no',
  `collectible` enum('yes','no') NOT NULL DEFAULT 'no',
  `collectable-edition` enum('yes','no') NOT NULL DEFAULT 'no',
  `collectible_q` int NOT NULL DEFAULT '0',
  `zoom` varchar(11) DEFAULT NULL,
  `approved` enum('yes','no','declined') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_announcements`
--

DROP TABLE IF EXISTS `site_announcements`;
CREATE TABLE IF NOT EXISTS `site_announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `announcement_text` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `special_sellers`
--

DROP TABLE IF EXISTS `special_sellers`;
CREATE TABLE IF NOT EXISTS `special_sellers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `serial` int NOT NULL,
  `bucks` int NOT NULL,
  `active` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
CREATE TABLE IF NOT EXISTS `statuses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `body` varchar(124) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
  `id` int NOT NULL AUTO_INCREMENT,
  `crap` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
CREATE TABLE IF NOT EXISTS `themes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `theme selected` enum('defualt','theme1') NOT NULL DEFAULT 'defualt',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userdms`
--

DROP TABLE IF EXISTS `userdms`;
CREATE TABLE IF NOT EXISTS `userdms` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `touser` int NOT NULL,
  `fromuser` int NOT NULL,
  `message` text NOT NULL,
  `datetime` datetime DEFAULT CURRENT_TIMESTAMP,
  `ishidden` int DEFAULT '0',
  `unread` int DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userdms`
--

INSERT INTO `userdms` (`ID`, `touser`, `fromuser`, `message`, `datetime`, `ishidden`, `unread`) VALUES
(1, 1, 2, 'This is a new chat!', '2024-07-20 22:35:50', 0, 0),
(2, 1, 2, 'New Chat!', '2024-07-20 22:35:54', 0, 0),
(3, 2, 1, 'ok', '2024-07-20 22:38:32', 0, 0),
(4, 1, 2, 'yes', '2024-07-21 15:16:01', 0, 0),
(5, 2, 1, 'a', '2024-07-22 10:36:45', 0, 0),
(6, 1, 2, 'queer', '2024-07-22 10:37:56', 0, 0),
(7, 2, 1, 'why', '2024-07-22 12:14:19', 0, 0),
(8, 2, 1, 'sdg', '2024-07-22 13:37:24', 0, 0),
(9, 2, 1, 'd', '2024-07-22 13:37:24', 0, 0),
(10, 2, 1, 'sgd', '2024-07-22 13:37:24', 0, 0),
(11, 2, 1, 'gew', '2024-07-22 13:39:13', 0, 0),
(12, 2, 1, 'gwe', '2024-07-22 13:39:13', 0, 0),
(13, 2, 1, 'e', '2024-07-22 13:39:14', 0, 0),
(14, 2, 1, 'gew', '2024-07-22 13:39:14', 0, 0),
(15, 2, 1, 'g', '2024-07-22 13:39:14', 0, 0),
(16, 2, 1, 'g', '2024-07-22 13:39:14', 0, 0),
(17, 2, 1, 'wg', '2024-07-22 13:39:14', 0, 0),
(18, 2, 1, 'ew', '2024-07-22 13:39:15', 0, 0),
(19, 2, 1, 'we', '2024-07-22 13:39:15', 0, 0),
(20, 2, 1, 'weg', '2024-07-22 13:39:15', 0, 0),
(21, 2, 1, 'w', '2024-07-22 13:39:15', 0, 0),
(22, 2, 1, 'gw', '2024-07-22 13:39:15', 0, 0),
(23, 2, 1, 'gwe', '2024-07-22 13:39:16', 0, 0),
(24, 2, 1, 'wegg', '2024-07-22 13:39:16', 0, 0),
(25, 2, 1, 'w', '2024-07-22 13:39:16', 0, 0),
(26, 2, 1, 'g', '2024-07-22 13:39:16', 0, 0),
(27, 2, 1, 'w', '2024-07-22 13:39:16', 0, 0),
(28, 2, 1, 'e', '2024-07-22 13:39:16', 0, 0),
(29, 2, 1, 'gwe', '2024-07-22 13:39:16', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(70) NOT NULL,
  `age` enum('under13','over13') NOT NULL,
  `safechat` enum('safe','supersafe') NOT NULL,
  `email` varchar(100) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `join_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `robux` int NOT NULL DEFAULT '0',
  `tickets` int NOT NULL DEFAULT '0',
  `description` text,
  `last_seen` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_rewards`
--

DROP TABLE IF EXISTS `user_rewards`;
CREATE TABLE IF NOT EXISTS `user_rewards` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `reward_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

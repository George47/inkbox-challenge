-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2019 at 12:27 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inkbox`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `vendor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` float DEFAULT '0',
  `handle` varchar(75) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inventory_quantity` int(11) NOT NULL DEFAULT '0',
  `sku` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `design_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `published_state` enum('inactive','active') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `title`, `vendor`, `type`, `size`, `price`, `handle`, `inventory_quantity`, `sku`, `design_url`, `published_state`, `created_at`, `updated_at`) VALUES
(1, 'Major Tom', 'inkbox', 'Product', '5x2', 27, 'majortom', 999, 'NBV3663', 'http://d2d11z2jyoa884.cloudfront.net/product_images/major-tom-1_lifestyle_1_20191125032516_500.png', 'active', '2019-11-26 05:25:47', '2019-11-26 05:25:47'),
(2, 'Geimfari', 'inkbox', 'Product', '3x3', 29.99, 'geimfari', 999, 'QSC51', 'http://d2d11z2jyoa884.cloudfront.net/product_images/geimfari_lifestyle_1_20191124013004_500.png', 'active', '2019-11-24 07:19:20', '2019-11-26 05:25:55'),
(3, 'Skobo', 'inkbox', 'Product', '4x4', 27, 'skobo', 999, 'QSD6', 'http://d2d11z2jyoa884.cloudfront.net/product_images/skobo_lifestyle_1_20191125101517_500.png', 'active', '2019-11-26 05:22:54', '2019-11-26 05:22:54'),
(4, 'Bebo', 'inkbox', 'Product', '1x1', 19, 'bebo', 999, 'QSA38', 'http://d2d11z2jyoa884.cloudfront.net/product_images/bebo_lifestyle_1_20191126120004_500.png', 'active', '2019-11-26 05:23:59', '2019-11-26 05:23:59'),
(5, 'Michelangelo', 'inkbox', 'Product', '2x5', 27, 'michelangelo', 999, 'NB13247', 'http://d2d11z2jyoa884.cloudfront.net/new_designs/angelo_r_-_michelangelo_-_4x4.jpg', 'active', '2019-11-26 05:28:07', '2019-11-26 05:28:07'),
(6, 'Project Semicolon', 'inkbox', 'Product', '2x2', 22, 'projectsemicolon', 999, 'NBV35718', 'http://d2d11z2jyoa884.cloudfront.net/product_images/project-semicolon-1_lifestyle_1_20191125064507_500.png', 'active', '2019-11-26 05:28:55', '2019-11-26 05:28:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `title` (`title`),
  ADD KEY `vendor` (`vendor`),
  ADD KEY `type` (`type`),
  ADD KEY `sku` (`sku`),
  ADD KEY `size` (`size`),
  ADD KEY `published_state` (`published_state`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `updated_at` (`updated_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2026 at 06:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dau_gia`
--

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `bid_amount` decimal(20,0) DEFAULT NULL,
  `bid_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`id`, `user_id`, `product_id`, `bid_amount`, `bid_time`) VALUES
(31, 13, 39, 1100000000, '2026-05-25 15:05:28');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `amount` decimal(20,0) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'paid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `amount`, `status`, `created_at`) VALUES
(1, 2, 3, 1005000, 'paid', '2025-11-21 17:44:10'),
(2, 2, 5, 11, 'paid', '2025-11-21 17:49:31'),
(3, 2, 6, 12, 'paid', '2025-11-21 17:59:11'),
(4, 3, 9, 12, 'paid', '2025-11-21 18:20:38'),
(5, 4, 22, 10050000000, 'paid', '2025-11-21 21:14:44'),
(6, 5, 27, 122222, 'paid', '2025-11-21 21:26:02'),
(7, 6, 28, 11000000, 'paid', '2025-11-23 10:06:27'),
(8, 7, 30, 21, 'paid', '2025-11-28 06:58:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(20,0) DEFAULT NULL,
  `min_increment` decimal(20,0) DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT 0,
  `category` varchar(50) DEFAULT 'Khác'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `image`, `price`, `min_increment`, `end_time`, `is_paid`, `category`) VALUES
(38, 'Siêu xe', 'uploads/1779646930_640-ngoai-that-xe-lamborghini-sian.jpg', 1000000000, 100000000, '2026-05-26 01:22:00', 0, 'Xe sang'),
(39, 'biệt thự', 'uploads/1779721481_Screenshot 2025-11-22 040056.png', 1100000000, 100000000, '2026-05-26 22:04:00', 0, 'Bất động sản');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(10) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`) VALUES
(8, 'trong', '$2y$10$yZwaSbijvSAP7eMgARUT4uDTVS7FWrJo5OIF6wtpSCGVCDX9QXvLm', 'trong', 'tranhuutrong1302@gmail.com', 'user'),
(9, 'trong 2', '$2y$10$C4uDV6ECwE1pnKFW64gVc.tMWGPK2pDOsJog2oVD3grjJsPIlIIDW', 'ngan', 'nguyenngockimngan6756@gmail.com', 'user'),
(13, 'test1', '$2y$10$IXTi01R019hsLa2iQPKpfu4rM29vrtm26lnbV7Y/wruY1udlXHwg.', 'tran huu trong', 'trongth3384@ut.edu.vn', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

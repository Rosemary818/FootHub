-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2025 at 12:05 PM
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
-- Database: `foothub`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_id`, `quantity`) VALUES
(13, 1, 4, 1),
(14, 1, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `payment_status`, `payment_method`, `date`) VALUES
(1, 1, 500.00, 'created', 'Razorpay', '2025-10-19 03:58:47'),
(2, 1, 799.00, NULL, NULL, '2025-10-19 04:29:22'),
(3, 1, 799.00, NULL, NULL, '2025-10-19 04:29:46'),
(4, 1, NULL, NULL, NULL, '2025-10-19 04:30:05'),
(5, 1, 799.00, NULL, NULL, '2025-10-19 04:31:08'),
(6, 1, 799.00, NULL, NULL, '2025-10-19 04:31:10'),
(7, 1, 1198.00, 'success', 'Razorpay', '2025-10-19 06:41:14'),
(8, 1, 1198.00, 'created', 'Razorpay', '2025-10-19 06:45:51'),
(9, 1, 1198.00, 'success', 'Razorpay', '2025-10-19 06:45:53'),
(10, 1, 399.00, 'success', 'Razorpay', '2025-10-19 07:19:05'),
(11, 1, 399.00, 'created', 'Razorpay', '2025-10-19 07:20:27'),
(12, 1, 399.00, 'created', 'Razorpay', '2025-10-19 07:20:29'),
(13, 1, 399.00, 'success', 'Razorpay', '2025-10-19 07:20:29'),
(14, 1, 399.00, 'success', 'Razorpay', '2025-10-19 07:23:18'),
(15, 1, 1499.00, 'success', 'Razorpay', '2025-10-19 07:26:15'),
(16, 1, 1499.00, 'created', 'Razorpay', '2025-10-19 07:28:14');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category`, `price`, `image`, `stock`) VALUES
(1, 'Classic Leather Sandals', 'Sandals', 799.00, 'https://aeroblu.in/cdn/shop/files/3102_Brown3.png?v=1755759630', 20),
(2, 'Casual Flip Flops', 'Flip Flops', 399.00, 'https://www.westside.com/cdn/shop/files/300977985MUSHROOM_1.jpg?v=1757334639&width=1445', 48),
(3, 'Sport Sneakers', 'Shoes', 1299.00, 'https://uspoloassn.in/cdn/shop/files/1_35c50345-d945-4d04-b53e-1dede98bc67e.jpg', 30),
(4, 'Running Shoes', 'Shoes', 1499.00, 'https://static.nike.com/a/images/t_PDP_936_v1/f_auto,q_auto:eco/6b88cd96-20c5-43c1-8645-38d1aaac0946/PEGASUS+EASYON.png', 24),
(5, 'Summer Slides', 'Sandals', 499.00, 'https://www.campusshoes.com/cdn/shop/files/SL-443_OFFWHT-TBLU_1.jpg?v=1756366734', 40),
(6, 'Formal Loafers', 'Shoes', 1599.00, 'https://img.tatacliq.com/images/i11/1348Wx2000H/MP000000017647895_1348Wx2000H_202305232233481.jpeg', 15),
(7, 'Beach Sandals', 'Sandals', 599.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGH5nWZUVlItzMPTH5qafeQSEWh_Vs4Jtg7g&s', 35),
(8, 'Canvas Sneakers', 'Shoes', 999.00, 'https://m.media-amazon.com/images/I/71LACWqVmKL._UY1000_.jpg', 30),
(9, 'Ankle Boots', 'Boots', 1899.00, 'https://www.voganow.com/cdn/shop/files/VNLFB-792-SUD-01-ZP-SL_1.jpg?v=1754552923', 20),
(10, 'High Heels', 'Heels', 1799.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR2HktEux8TSndKOlVMysLLeLYvnGJxw1bJhQ&s', 15);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `gender`, `reset_token`, `reset_expiry`) VALUES
(1, 'Rose Mary Biju', 'rosemarybiju413@gmail.com', '$2y$10$ZoG8Lx6kmECDrWZxAyV8YuH00h943hAWEHc1eMSYYlnfMAYuKO/JC', '9447485018', 'Female', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `product_id`) VALUES
(7, 1, 4),
(8, 1, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

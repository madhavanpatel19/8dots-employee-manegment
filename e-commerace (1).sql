-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 08:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-commerace`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `about_id` int(10) NOT NULL,
  `about_heading` text NOT NULL,
  `about_short_desc` text NOT NULL,
  `about_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`about_id`, `about_heading`, `about_short_desc`, `about_desc`) VALUES
(1, 'About Us - Our Story', '\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters,\r\n', 'Rhone was the collective vision of a small group of weekday warriors. For years, we were frustrated by the lack of activewear designed for men and wanted something better. With that in mind, we set out to design premium apparel that is made for motion and engineered to endure.\r\n\r\nAdvanced materials and state of the art technology are combined with heritage craftsmanship to create a new standard in activewear. Every product tells a story of premium performance, reminding its wearer to push themselves physically without having to sacrifice comfort and style.\r\n\r\nBeyond our product offering, Rhone is founded on principles of progress and integrity. Just as we aim to become better as a company, we invite men everywhere to raise the bar and join us as we move Forever Forward.');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(10) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,
  `admin_image` text NOT NULL,
  `admin_contact` varchar(255) NOT NULL,
  `admin_country` text NOT NULL,
  `admin_job` varchar(255) NOT NULL,
  `admin_about` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `admin_email`, `admin_pass`, `admin_image`, `admin_contact`, `admin_country`, `admin_job`, `admin_about`) VALUES
(2, 'Test Name', 'admin@ave.com', '123', 'admin.jpg', '077885221', 'Morocco', 'Front-End Developer', ' Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical '),
(3, 'Madhavan', 'madhavan@gmail.com', '123', 'madhavan.jpg', '987654321', 'india', 'Front-End Developer', ' hello');

-- --------------------------------------------------------

--
-- Table structure for table `bundle_product_relation`
--

CREATE TABLE `bundle_product_relation` (
  `rel_id` int(10) NOT NULL,
  `rel_title` varchar(255) NOT NULL,
  `product_id` int(10) NOT NULL,
  `bundle_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bundle_product_relation`
--

INSERT INTO `bundle_product_relation` (`rel_id`, `rel_title`, `product_id`, `bundle_id`) VALUES
(8, 'jacket bundle relation -1', 4, 11),
(9, 'jacket bundle relation -2', 5, 11),
(10, 'jacket bundle relation -3', 6, 11);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(9, 33, 47, 1, '2025-08-26 12:22:05'),
(10, 33, 27, 3, '2025-08-26 12:22:34'),
(13, 32, 48, 1, '2025-08-26 12:26:39'),
(38, 1, 13, 1, '2025-09-03 09:59:55'),
(39, 1, 55, 1, '2025-09-03 09:59:58'),
(40, 1, 56, 1, '2025-09-03 09:59:59'),
(41, 3, 38, 1, '2025-09-03 11:48:19'),
(42, 3, 40, 3, '2025-09-03 11:48:23'),
(43, 1, 38, 1, '2025-09-10 05:22:26'),
(44, 2, 27, 1, '2025-09-10 05:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(10) NOT NULL,
  `cat_title` text NOT NULL,
  `cat_top` text NOT NULL,
  `cat_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_title`, `cat_top`, `cat_image`) VALUES
(7, 'fruits', 'yes', 'Fruits.jpg'),
(8, 'vegetable', 'yes', 'vegetables.jpg'),
(9, 'grocery', 'no', 'grocery-food.jpg'),
(10, 'Fashion', 'no', 'Fashion_Thumb.png'),
(11, 'artwork', 'no', 'artwork.jpg'),
(12, 'Kitchenware', 'no', 'Kitchenware.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `client_register`
--

CREATE TABLE `client_register` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` int(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `confirm_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_register`
--

INSERT INTO `client_register` (`id`, `fullname`, `email`, `contact`, `password`, `confirm_password`) VALUES
(1, 'madhavan ', 'madhi@gmail.com', 2147483647, '$2y$10$d0lmR7Ab/Mp1MaDH3UQOy.XiPJKjdOvKpHN1G/sw2vJnxG70e45O.', ''),
(2, 'rajveer', 'rajveer@gmail.com', 2147483647, '$2y$10$rchP6cSXN/UAbHMf4/dc/eWlono8kaobJ2WFA8xxcDgbWzekb6RhS', ''),
(3, 'veer', 'abc@gmail.com', 2147483647, '$2y$10$2MsvwGodHmDy5dw/N9l0dOX5aQusJJUxHbi3VnHp..PWMGnyJuoE2', '');

-- --------------------------------------------------------

--
-- Table structure for table `contactform`
--

CREATE TABLE `contactform` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contactform`
--

INSERT INTO `contactform` (`id`, `name`, `email`, `message`) VALUES
(39, 'Madhavan', 'madhavan@gmail.com', 'cdfv'),
(40, 'Madhavan', 'user@gmail.com', 'vfgvg'),
(41, 'rajveer ', 'madhavan@gmail.com', 'hello'),
(42, 'hello', 'hineri0211@gmail.com', 'dgb '),
(43, 'hello', 'user@gmail.com', 'egrfng'),
(44, 'raj', 'admin@freshmart.com', 'heelo'),
(45, 'Madhavan', 'hineri0211@gmail.comfgn', 'gnng'),
(46, 'hello', 'madhavan@gmail.com', 'frbt'),
(47, 'veershing', 'verr@gmail.com', 'hello'),
(48, 'hello', 'rajveer@gmail.com', 'heelo '),
(49, 'hello', 'rajveer@gmail.com', 'heelo '),
(50, 'hello', 'rajveer@gmail.com', 'heelo '),
(51, 'hello', 'rajveer@gmail.com', 'heelo '),
(52, 'hello', 'rajveer@gmail.com', 'heelo'),
(53, 'hello', 'rajveer@gmail.com', 'heelo'),
(54, 'hello', 'rajveer@gmail.com', 'heelo'),
(55, 'hello', 'rajveer@gmail.com', 'heelo'),
(56, 'hello', 'rajveer@gmail.com', 'heelo'),
(57, 'Madhavan', 'madhavan@gmail.com', 'heelo'),
(58, 'Madhavan', 'madhavan@gmail.com', 'heelo'),
(59, 'Madhavan', 'madhavan@gmail.com', 'heelo'),
(60, 'Madhavan', 'madhavan@gmail.com', 'heelo'),
(61, 'hello', 'madhavan@gmail.com', 'hrr'),
(62, 'Madhavan', 'madhavan@gmail.com', 'efcv'),
(63, 'Madhavan', 'madhavan@gmail.com', 'efcv'),
(64, 'Madhavan', 'madhavan@gmail.com', 'efcv'),
(65, 'Madhavan', 'madhavan@gmail.com', 'efcv'),
(66, 'rajveer ', 'madhavan@gmail.com', 'heeelo'),
(67, 'hello', 'rajveer@gmail.com', 'vfbtbt'),
(68, 'hello', 'rajveer@gmail.com', 'vfbtbt'),
(69, 'hello', 'rajveer@gmail.com', 'vfbtbt'),
(70, 'hello', 'rajveer@gmail.com', 'vfbtbt'),
(71, 'Madhavan', 'rajveer@gmail.com', 'cdvfr'),
(72, 'Madhavan', 'rajveer@gmail.com', 'cdvfr'),
(73, 'Madhavan', 'rajveer@gmail.com', 'cdvfr'),
(74, 'nikunj ', 'nikunj@123gmail.com', 'HELOO HOW ARE YOOU'),
(75, 'hello', 'madhavan@gmail.com', '1213'),
(76, 'Madhavan', 'admin@example.com', 'hello madhavan'),
(77, 'Madhavan', 'admin@example.com', 'hello madhavan');

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contact_id` int(10) NOT NULL,
  `contact_email` text NOT NULL,
  `contact_heading` text NOT NULL,
  `contact_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact_us`
--

INSERT INTO `contact_us` (`contact_id`, `contact_email`, `contact_heading`, `contact_desc`) VALUES
(1, 'sad.ahmed22224@gmail.com', 'Contact  To Us', 'If you have any questions, please feel free to contact us, our customer service center is working for you 24/7.');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL,
  `coupon_title` varchar(255) NOT NULL,
  `coupon_price` varchar(255) NOT NULL,
  `coupon_code` varchar(255) NOT NULL,
  `coupon_limit` int(100) NOT NULL,
  `coupon_used` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `product_id`, `coupon_title`, `coupon_price`, `coupon_code`, `coupon_limit`, `coupon_used`) VALUES
(3, 9, 'Remind T-shirt', '40', '333444', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(10) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_pass` varchar(255) NOT NULL,
  `customer_country` text NOT NULL,
  `customer_city` text NOT NULL,
  `customer_contact` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `customer_image` text NOT NULL,
  `customer_ip` varchar(255) NOT NULL,
  `customer_confirm_code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_address`, `customer_image`, `customer_ip`, `customer_confirm_code`) VALUES
(2, 'user', 'user@ave.com', '123', 'United State', 'New York', '0092334566931', 'new york', 'user.jpg', '::1', '');

-- --------------------------------------------------------

--
-- Table structure for table `customer_orders`
--

CREATE TABLE `customer_orders` (
  `order_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `due_amount` int(100) NOT NULL,
  `invoice_no` int(100) NOT NULL,
  `qty` int(10) NOT NULL,
  `size` text NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer_orders`
--

INSERT INTO `customer_orders` (`order_id`, `customer_id`, `due_amount`, `invoice_no`, `qty`, `size`, `order_date`, `order_status`) VALUES
(21, 2, 400, 909940689, 2, 'Meduim', '2017-02-27 11:06:37', 'complete');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_address`
--

CREATE TABLE `delivery_address` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `street_address` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `contact_number` int(11) NOT NULL,
  `order_id` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_address`
--

INSERT INTO `delivery_address` (`id`, `client_id`, `name`, `street_address`, `city`, `state`, `pincode`, `contact_number`, `order_id`) VALUES
(41, 1, 'madhavan ', '1234', '2142', '24', 24, 24, 'ORD68b6b7f881d5e'),
(42, 1, 'madhavan ', '1234', '2142', '24', 24, 24, 'ORD68b6b89c22850'),
(43, 1, 'madhavan ', '1234', '2142', '24', 24, 24, 'ORD68b6b8d35b345'),
(44, 1, 'rajveer', 'botad', '2134', 'qv', 0, 0, 'ORD68b6c487c8c50'),
(45, 1, 'rajveer', 'botad', '2134', 'qv', 0, 0, 'ORD68b6ccea976fe'),
(46, 1, 'rajveer', 'botad', '2134', 'qv', 0, 0, 'ORD68b6cd34dc05d'),
(47, 1, 'madhavan ', '124', '2134', 'gujrat', 654321, 987654321, 'ORD68b6cee98295d'),
(48, 1, 'madhavan ', 'botad', 'ahmedabad', '2312', 654321, 1234567890, 'ORD68b6d142c5da0'),
(49, 1, 'madhavan ', 'botad', 'ahmedabad', 'gujrat', 654321, 1234567890, 'ORD68b6d4b87f6c9'),
(50, 1, 'madhavan ', '1234', '124', '124', 654321, 1234567890, 'ORD68b6ddaa1416c'),
(51, 1, 'madhavan ', 'botad', 'ahmedabad', 'gujrat', 654321, 1234567890, 'ORD68b6de210b1c9'),
(52, 1, 'madhavan ', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b6e01116326'),
(53, 1, 'madhavan ', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b6e01d12cfe'),
(54, 1, 'madhavan ', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b6e03d94827'),
(55, 1, 'madhavan ', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b6e07cbb832'),
(56, 1, 'madhavan ', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b6e115344c4'),
(57, 1, 'madhavan ', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b6e14680840'),
(58, 1, 'madhavan ', '124', 'ahmedabad', 'gujrat', 654321, 1234567890, 'ORD68b6e189584c5'),
(59, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7ca90dc3ab'),
(60, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7ca97270c0'),
(61, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7cae207340'),
(62, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7cc5e37259'),
(63, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7cc90cf9cc'),
(64, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7cedb059bf'),
(65, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7cef095cb3'),
(66, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7d01d687c9'),
(67, 1, 'madhavan ', 'botad', '2134', '1234', 654321, 1234567890, 'ORD68b7d0f437c23'),
(68, 1, 'rajveer', 'botad', '2134', 'gujrat', 654321, 1234567890, 'ORD68b7d1cdaf8c0'),
(69, 1, 'rajveer', '1242', 'ahmedabad', '123', 654321, 1234567890, 'ORD68b7d34525523'),
(70, 1, 'rajveer', '1242', 'ahmedabad', '123', 654321, 1234567890, 'ORD68b7d353eda94'),
(71, 1, 'rajveer', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b7d9d4d4c1a'),
(72, 1, 'rajveer', '1234', '123', 'gujrat', 654321, 1234567890, 'ORD68b7d9e3c45bc'),
(73, 1, 'rajveer', '123', '312', 'gujrat', 654321, 1234567890, 'ORD68b808ebe81ea'),
(74, 1, 'rajveer', '123', '312', 'gujrat', 654321, 1234567890, 'ORD68b80e2f7b73b'),
(75, 1, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 654321, 1234567890, 'ORD68b80e8f91bc5'),
(76, 1, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 654321, 1234567890, 'ORD68b80ffa24541'),
(77, 2, 'rajveer', 'botad', 'ahmedabad', '2312', 123456, 987654321, 'ORD68b810fc0af9a'),
(78, 2, 'rajveer', 'botad', 'ahmedabad', '2312', 123456, 987654321, 'ORD68b81185b90aa'),
(79, 1, 'veer', 'botad', 'botad', 'gujrat', 98765, 1243568709, 'ORD68b811b84d7c6'),
(80, 1, 'veer', 'botad', 'botad', 'gujrat', 98765, 1243568709, 'ORD68b81309d0195'),
(81, 3, 'veer', 'botad', 'botad', 'gujarat', 654321, 987654321, 'ORD68b82b210d874'),
(82, 3, 'veer', 'botad', 'botad', 'gujarat', 654321, 987654321, 'ORD68b82b579f4e1'),
(83, 3, 'veer', 'botad', 'botad', 'gujarat', 654321, 987654321, 'ORD68b82ba17bfe7'),
(84, 3, 'veer', 'botad', 'botad', 'gujarat', 654321, 987654321, 'ORD68b82ca21e926'),
(85, 3, 'veer', 'botad', 'botad', 'gujarat', 654321, 987654321, 'ORD68b82e7ad3e6d'),
(86, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b82e9812f7d'),
(87, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b82f15b9be9'),
(88, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b830ff840de'),
(89, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b83128b6ccb'),
(90, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b832c037775'),
(91, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b832ed78411'),
(92, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b834d4dd956'),
(93, 2, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 132344, 1234567890, 'ORD68b834dab2cac'),
(94, 1, 'rajveer', 'botad', '123', 'gujrat', 654321, 1234567890, 'ORD68b8361b4175a'),
(95, 1, 'rajveer', 'botad', '123', 'gujrat', 654321, 1234567890, 'ORD68b8367a4b247'),
(96, 1, 'rajveer', 'botad', '123', 'gujrat', 654321, 1234567890, 'ORD68b8368a8d164'),
(97, 1, 'rajveer', 'botad', '123', 'gujrat', 654321, 1234567890, 'ORD68b8376e009ed'),
(98, 1, 'rajveer', 'botad', '123', 'gujrat', 654321, 1234567890, 'ORD68b83822975d3'),
(99, 1, 'rajveer', 'botad', 'ahmedabad', 'gujrat', 654321, 1234567890, 'ORD68b9236e15df3'),
(100, 1, 'rajveer', '232', 'botad', 'gujrat', 132344, 1234567890, 'ORD68c10b2c78d87'),
(101, 2, 'rajveer123', 'botad', 'botad', 'gujrat', 654321, 1234567890, 'ORD68c10dd85ec7a'),
(102, 2, 'rajveer123', 'botad', 'botad', 'gujrat', 654321, 1234567890, 'ORD68c10e245a4aa');

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_types`
--

CREATE TABLE `enquiry_types` (
  `enquiry_id` int(10) NOT NULL,
  `enquiry_title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `enquiry_types`
--

INSERT INTO `enquiry_types` (`enquiry_id`, `enquiry_title`) VALUES
(1, 'Order and Delivery Support'),
(2, 'Technical Support'),
(3, 'Price Concern');

-- --------------------------------------------------------

--
-- Table structure for table `manufacturers`
--

CREATE TABLE `manufacturers` (
  `manufacturer_id` int(10) NOT NULL,
  `manufacturer_title` text NOT NULL,
  `manufacturer_top` text NOT NULL,
  `manufacturer_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `manufacturers`
--

INSERT INTO `manufacturers` (`manufacturer_id`, `manufacturer_title`, `manufacturer_top`, `manufacturer_image`) VALUES
(2, 'Adidas', 'no', 'image2.jpg'),
(3, 'Nike', 'no', 'image3.jpg'),
(4, 'Philip Plein', 'no', 'manufacturer.jpg'),
(5, 'Lacost', 'no', 'image6.jpg'),
(6, 'Gucci', 'yes', 'akshay-kumar.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `order_id`, `fullname`, `address`, `city`, `state`, `pincode`, `mobile`, `payment_method`, `total`, `created_at`, `order_status`) VALUES
(11, 1, 'ORD68c10b2c78d87', 'rajveer', '232', 'botad', 'gujrat', '132344', '1234567890', 'cod', 338.00, '2025-09-10 05:22:58', 'Accepted'),
(12, 2, 'ORD68c10dd85ec7a', 'rajveer123', 'botad', 'botad', 'gujrat', '654321', '1234567890', 'cod', 1455.00, '2025-09-10 05:34:22', ''),
(13, 2, 'ORD68c10e245a4aa', 'rajveer123', 'botad', 'botad', 'gujrat', '654321', '1234567890', 'cod', 120.00, '2025-09-10 05:35:36', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_name`, `price`, `qty`, `subtotal`) VALUES
(39, 'ORD68c10b2c78d87', 'banana', 23.00, 1, 23.00),
(40, 'ORD68c10b2c78d87', 'Mango', 150.00, 1, 150.00),
(41, 'ORD68c10b2c78d87', 'Watermelon', 40.00, 1, 40.00),
(42, 'ORD68c10b2c78d87', 'Jug 1pc', 125.00, 1, 125.00),
(43, 'ORD68c10dd85ec7a', 'Shoes', 1230.00, 1, 1230.00),
(44, 'ORD68c10dd85ec7a', 'Watermelon', 40.00, 2, 80.00),
(45, 'ORD68c10dd85ec7a', 'grapes', 25.00, 1, 25.00),
(46, 'ORD68c10dd85ec7a', 'minda 1kg', 120.00, 1, 120.00),
(47, 'ORD68c10e245a4aa', 'minda 1kg', 120.00, 1, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) NOT NULL,
  `invoice_no` int(10) NOT NULL,
  `amount` int(10) NOT NULL,
  `payment_mode` text NOT NULL,
  `ref_no` int(10) NOT NULL,
  `code` int(10) NOT NULL,
  `payment_date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `invoice_no`, `amount`, `payment_mode`, `ref_no`, `code`, `payment_date`) VALUES
(2, 1607603019, 447, 'UBL/Omni Paisa', 5678, 33, '11/1/2016'),
(3, 314788500, 345, 'UBL/Omni Paisa', 443, 865, '11/1/2016');

-- --------------------------------------------------------

--
-- Table structure for table `pending_orders`
--

CREATE TABLE `pending_orders` (
  `order_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `invoice_no` int(10) NOT NULL,
  `product_id` text NOT NULL,
  `qty` int(10) NOT NULL,
  `size` text NOT NULL,
  `order_status` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(10) NOT NULL,
  `p_cat_id` int(10) NOT NULL,
  `cat_id` int(10) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `product_title` text NOT NULL,
  `product_img1` text NOT NULL,
  `product_price` int(10) NOT NULL,
  `product_psp_price` int(100) NOT NULL,
  `product_keywords` text NOT NULL,
  `product_label` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `product_features` text DEFAULT NULL,
  `product_video` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `p_cat_id`, `cat_id`, `date`, `product_title`, `product_img1`, `product_price`, `product_psp_price`, `product_keywords`, `product_label`, `status`, `product_features`, `product_video`) VALUES
(11, 7, 5, '2017-02-20 06:21:03', 'jacket bundle', 'jacket-1.jpg', 300, 200, 'jacket bundle', 'Sale', 'bundle', NULL, NULL),
(13, 0, 7, '2025-08-25 11:46:13', 'banana', 'banana.jpg', 23, 21, '2', 'banana', 'product', NULL, NULL),
(15, 0, 8, '2025-08-26 06:49:37', 'Brinjal 1kg ', 'brinjal.jpg', 35, 21, '2', '35', 'product', NULL, NULL),
(16, 0, 8, '2025-08-26 07:01:13', 'Cabbage 1pc (Approx 400g - 700g)', 'cabbage.jpg', 25, 21, '2', 'Cabbage 1pc (Approx 400g - 700g)', 'product', NULL, NULL),
(17, 0, 8, '2025-08-26 07:03:14', 'Capsicum 500gm', 'capci.jpg', 80, 80, '3', '', 'product', NULL, NULL),
(18, 0, 8, '2025-08-26 07:04:01', 'Carrot 500gm ', 'carrot.jpg', 20, 20, '3', '', 'product', NULL, NULL),
(19, 0, 8, '2025-08-26 07:05:17', 'Coliflower 1pc(Approx 200g - 400g)', 'coliflower.jpg', 35, 35, '4', '', 'product', NULL, NULL),
(20, 0, 8, '2025-08-26 07:05:53', 'Garlic 1kg', 'garlic.jpg', 35, 35, '5', '', 'product', NULL, NULL),
(21, 0, 8, '2025-08-26 07:08:10', 'Bottle gourd 1pc (Approx 150g - 300g)', 'lauki.jpg', 100, 1000, '6', '', 'product', NULL, NULL),
(22, 0, 8, '2025-08-26 07:09:09', 'Onion 1kg', 'onion.jpg', 15, 15, '7', '', 'product', NULL, NULL),
(23, 0, 8, '2025-08-26 07:09:57', 'Potato 1kg', 'potato.jpg', 25, 25, '8', '', 'product', NULL, NULL),
(24, 0, 8, '2025-08-26 07:10:23', 'Tomato 1kg', 'tomato.jpg', 10, 10, '9', '', 'product', NULL, NULL),
(25, 0, 9, '2025-08-26 07:13:40', 'Ashirwaad atta 1kg', 'atta.jpg', 350, 350, '1', '', 'product', NULL, NULL),
(26, 0, 9, '2025-08-26 07:14:06', 'Besan 1kg', 'besan.webp', 120, 120, '2', '', 'product', NULL, NULL),
(27, 0, 9, '2025-08-26 11:52:02', 'minda 1kg', 'cookware set.jpg', 120, 120, '3', 'kichanware', 'product', NULL, NULL),
(28, 0, 9, '2025-08-26 07:15:03', 'sooji 1kg', 'sooji.jpg', 86, 86, '3', '', 'product', NULL, NULL),
(29, 0, 9, '2025-08-26 07:15:48', 'Makki 1kg', 'makki.jpg', 35, 35, '4', '', 'product', NULL, NULL),
(30, 0, 9, '2025-08-26 07:16:25', 'Bajra 1kg', 'bajra.jpg', 45, 45, '4', '', 'product', NULL, NULL),
(31, 0, 9, '2025-08-26 07:16:50', 'Toor 1kg', 'toor.jpg', 110, 110, '5', '', 'product', NULL, NULL),
(32, 0, 9, '2025-08-26 07:17:16', 'Chana dal 1kg', 'chana dal.jpg', 55, 55, '6', '', 'product', NULL, NULL),
(33, 0, 10, '2025-09-01 05:27:25', 'Shirt', 'f1.jpg', 130, 0, '1', 'fashion', 'product', '', ''),
(34, 0, 10, '2025-09-01 05:43:39', 't-shirt', 'f2.jpg', 123, 0, '2', 'fashion', 'product', '<br />\r\n<b>Warning</b>:  Undefined variable $p_features in <b>C:xampphtdocsE-commeraceadmin_areaedit_product.php</b> on line <b>154</b><br />\r\n', ''),
(35, 0, 10, '2025-09-01 05:28:44', 'goggles', 'f3.jpg', 1220, 0, '4', 'fashion', 'product', '<br />\r\n<b>Warning</b>:  Undefined variable $p_features in <b>C:xampphtdocsE-commeraceadmin_areaedit_product.php</b> on line <b>154</b><br />\r\n', '<br />\r\n<b>Warning</b>:  Undefined variable $p_video in <b>C:xampphtdocsE-commeraceadmin_areaedit_product.php</b> on line <b>161</b><br />\r\n'),
(36, 0, 10, '2025-09-01 05:29:05', 'Shoes', 'f4.jpg', 1230, 0, '4', 'fashion', 'product', '<br />\r\n<b>Warning</b>:  Undefined variable $p_features in <b>C:xampphtdocsE-commeraceadmin_areaedit_product.php</b> on line <b>154</b><br />\r\n', '<br />\r\n<b>Warning</b>:  Undefined variable $p_video in <b>C:xampphtdocsE-commeraceadmin_areaedit_product.php</b> on line <b>161</b><br />\r\n'),
(37, 0, 12, '2025-08-26 07:43:45', 'Cup 1pc', 'cup.jpg', 35, 35, '1', '', 'product', NULL, NULL),
(38, 0, 12, '2025-08-26 07:44:15', 'Jug 1pc', 'jug.jpg', 125, 125, '2', '', 'product', NULL, NULL),
(40, 0, 12, '2025-08-26 07:45:30', 'Knif', 'knif.jpg', 80, 80, '4', '', 'product', NULL, NULL),
(41, 0, 12, '2025-08-26 07:52:17', 'Spoon 5pc', 'spoon.jpg', 100, 100, '5', 'Spoon set', 'product', NULL, NULL),
(42, 0, 12, '2025-08-26 07:52:38', 'Spoon set', 'spoon set.jpg', 150, 150, '6', 'Spoon set', 'product', NULL, NULL),
(43, 0, 12, '2025-08-26 07:47:07', 'Kitchen utensils hanging rack', 'kitchen utensils hanging rack.jpg', 450, 450, '7', '', 'product', NULL, NULL),
(44, 0, 12, '2025-08-26 07:47:27', 'Pizza plate 1 pc', 'Pizza plate.jpg', 700, 700, '8', '', 'product', NULL, NULL),
(45, 0, 11, '2025-08-26 07:48:45', 'arts work', 'art1.jpg', 3500, 3500, '1', '', 'product', NULL, NULL),
(46, 0, 11, '2025-08-26 07:49:06', 'arts work', 'art2.jpg', 5000, 5000, '2', '', 'product', NULL, NULL),
(47, 0, 11, '2025-08-26 07:49:26', 'arts work', 'art3.jpg', 7500, 7500, '3', '', 'product', NULL, NULL),
(48, 0, 11, '2025-08-26 07:49:52', 'arts work', 'art4.jpg', 10000, 10000, '4', '', 'product', NULL, NULL),
(51, 0, 7, '2025-08-26 07:54:12', 'Cherry', 'cherry.jpg', 100, 100, '2', '', 'product', NULL, NULL),
(52, 0, 7, '2025-08-26 07:54:36', 'custard', 'custard.jpg', 40, 40, '3', '', 'product', NULL, NULL),
(53, 0, 7, '2025-08-26 07:55:12', 'grapes', 'grapes.jpg', 25, 25, '3', '', 'product', NULL, NULL),
(54, 0, 7, '2025-08-26 07:55:39', 'Guava', 'guava.jpg', 40, 40, '4', '', 'product', NULL, NULL),
(55, 0, 7, '2025-08-26 07:56:13', 'Mango', 'mango.jpg', 150, 150, '5', '', 'product', NULL, NULL),
(56, 0, 7, '2025-08-26 07:56:39', 'Watermelon', 'melon.jpg', 40, 40, '6', '', 'product', NULL, NULL),
(57, 0, 7, '2025-08-26 07:57:04', 'Orange', 'orange.jpg', 50, 50, '6', '', 'product', NULL, NULL),
(58, 0, 7, '2025-08-26 07:57:29', 'Papaya', 'papaya.jpg', 40, 40, '7', '', 'product', NULL, NULL),
(59, 0, 7, '2025-08-26 07:58:01', 'Pineapple', 'pineapple.jpg', 120, 120, '8', '', 'product', NULL, NULL),
(60, 0, 7, '2025-08-26 07:58:30', 'strawberry', 'strawberry.jpg', 210, 210, '9', '', 'product', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `p_cat_id` int(10) NOT NULL,
  `p_cat_title` text NOT NULL,
  `p_cat_top` text NOT NULL,
  `p_cat_image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`p_cat_id`, `p_cat_title`, `p_cat_top`, `p_cat_image`) VALUES
(6, 'Sweater', 'no', 'sweater.jpg'),
(7, 'jackets', 'yes', 'jacket.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `store`
--

CREATE TABLE `store` (
  `store_id` int(10) NOT NULL,
  `store_title` varchar(255) NOT NULL,
  `store_image` varchar(255) NOT NULL,
  `store_desc` text NOT NULL,
  `store_button` varchar(255) NOT NULL,
  `store_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `store`
--

INSERT INTO `store` (`store_id`, `store_title`, `store_image`, `store_desc`, `store_button`, `store_url`) VALUES
(4, 'London Store', 'store (3).jpg', '<p style=\"text-align: center;\"><strong>180-182 RECENTS STREET, LONDON, W1B 5BT</strong></p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut libero erat, aliquet eget mauris ut, dictum sagittis libero. Nam at dui dapibus, semper dolor ac, malesuada mi. Duis quis lobortis arcu. Vivamus sed sodales orci, non varius dolor.</p>', 'View Map', 'http://www.thedailylux.com/ecommerce'),
(5, 'New York Store', 'store (1).png', '<p style=\"text-align: center;\"><strong>109 COLUMBUS CIRCLE, NEW YORK, NY10023</strong></p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut libero erat, aliquet eget mauris ut, dictum sagittis libero. Nam at dui dapibus, semper dolor ac, malesuada mi. Duis quis lobortis arcu. Vivamus sed sodales orci, non varius dolor.</p>', 'View Map', 'http://www.thedailylux.com/ecommerce'),
(6, 'Paris Store', 'store (2).jpg', '<p style=\"text-align: center;\"><strong>2133 RUE SAINT-HONORE, 75001 PARIS&nbsp;</strong></p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut libero erat, aliquet eget mauris ut, dictum sagittis libero. Nam at dui dapibus, semper dolor ac, malesuada mi. Duis quis lobortis arcu. Vivamus sed sodales orci, non varius dolor.</p>', 'View Map', 'http://www.thedailylux.com/ecommerce');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `term_id` int(10) NOT NULL,
  `term_title` varchar(100) NOT NULL,
  `term_link` varchar(100) NOT NULL,
  `term_desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`term_id`, `term_title`, `term_link`, `term_desc`) VALUES
(1, 'Rules And Regulations', 'rules', '<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance.&nbsp;</p>'),
(2, 'Refund Policy', 'link2', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).Why do we use it?It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on'),
(3, 'Pricing and Promotions Policy', 'link3', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).Why do we use it?It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(10) NOT NULL,
  `customer_id` int(10) NOT NULL,
  `product_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `customer_id`, `product_id`) VALUES
(2, 2, 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`about_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `bundle_product_relation`
--
ALTER TABLE `bundle_product_relation`
  ADD PRIMARY KEY (`rel_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `client_register`
--
ALTER TABLE `client_register`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contactform`
--
ALTER TABLE `contactform`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_orders`
--
ALTER TABLE `customer_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `delivery_address`
--
ALTER TABLE `delivery_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiry_types`
--
ALTER TABLE `enquiry_types`
  ADD PRIMARY KEY (`enquiry_id`);

--
-- Indexes for table `manufacturers`
--
ALTER TABLE `manufacturers`
  ADD PRIMARY KEY (`manufacturer_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `pending_orders`
--
ALTER TABLE `pending_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`p_cat_id`);

--
-- Indexes for table `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`store_id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`term_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `about_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bundle_product_relation`
--
ALTER TABLE `bundle_product_relation`
  MODIFY `rel_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `client_register`
--
ALTER TABLE `client_register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contactform`
--
ALTER TABLE `contactform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contact_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_orders`
--
ALTER TABLE `customer_orders`
  MODIFY `order_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `delivery_address`
--
ALTER TABLE `delivery_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `enquiry_types`
--
ALTER TABLE `enquiry_types`
  MODIFY `enquiry_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `manufacturers`
--
ALTER TABLE `manufacturers`
  MODIFY `manufacturer_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pending_orders`
--
ALTER TABLE `pending_orders`
  MODIFY `order_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `p_cat_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `store`
--
ALTER TABLE `store`
  MODIFY `store_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `term_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2022 at 12:02 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zychat`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_body` text NOT NULL,
  `posted_by` varchar(60) NOT NULL,
  `posted_to` varchar(60) NOT NULL,
  `date_added` datetime NOT NULL,
  `removed` varchar(3) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_body`, `posted_by`, `posted_to`, `date_added`, `removed`, `post_id`) VALUES
(15, '        good morning man', 'ali_ahmed', 'mahmoud_ali', '2022-05-13 11:15:27', 'no', 34),
(16, '        hi', 'zain_khalid', 'zain_khalid', '2022-05-17 09:40:28', 'no', 49),
(17, '        cool', 'ali_ahmed', 'zain_khalid', '2022-05-18 10:27:25', 'no', 50),
(18, '        what\'s up', 'ali_ahmed', 'zain_khalid', '2022-05-18 10:27:44', 'no', 51),
(19, '        good', 'ali_ali', 'mahmoud_ali', '2022-05-18 15:54:11', 'no', 52);

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` int(11) NOT NULL,
  `user_to` varchar(60) NOT NULL,
  `user_from` varchar(60) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`id`, `user_to`, `user_from`, `date`) VALUES
(29, 'ayman_omar', 'zain_khalid', '2022-05-17 09:48:48'),
(40, 'ali_ali', 'mahmoud_ali', '2022-05-18 16:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `username`, `post_id`) VALUES
(28, 'ali_ahmed', 34),
(33, 'zain_khalid', 50),
(34, 'ali_ali', 52);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_to` varchar(100) NOT NULL,
  `user_from` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL,
  `opened` varchar(3) NOT NULL,
  `viewed` varchar(3) NOT NULL,
  `deleted` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_to`, `user_from`, `body`, `date`, `opened`, `viewed`, `deleted`) VALUES
(47, 'mahmoud_ali', 'ali_ahmed', 'hi', '2022-05-13 11:16:09', 'yes', 'yes', 'no'),
(48, 'mahmoud_ali', 'zain_khalid', 'hi', '2022-05-15 05:15:42', 'yes', 'yes', 'no'),
(49, 'ali_ahmed', 'zain_khalid', 'dasf', '2022-05-15 05:22:51', 'no', 'yes', 'no'),
(50, 'ali_ahmed', 'zain_khalid', 'hi', '2022-05-17 06:14:51', 'no', 'yes', 'no'),
(51, 'ali_ahmed', 'zain_khalid', 'dsfa', '2022-05-17 06:22:20', 'no', 'yes', 'no'),
(52, 'ali_ahmed', 'zain_khalid', 'ds', '2022-05-17 06:24:45', 'no', 'yes', 'no'),
(53, 'ali_ahmed', 'zain_khalid', 'dsa', '2022-05-17 06:24:48', 'no', 'yes', 'no'),
(54, 'ali_ahmed', 'zain_khalid', '1', '2022-05-17 06:24:58', 'no', 'yes', 'no'),
(55, 'ali_ahmed', 'zain_khalid', 'dfadf', '2022-05-17 06:25:41', 'no', 'yes', 'no'),
(56, 'ali_ahmed', 'zain_khalid', 'dfadf', '2022-05-17 06:27:22', 'no', 'yes', 'no'),
(57, 'ali_ahmed', 'zain_khalid', 'we', '2022-05-17 06:27:26', 'no', 'yes', 'no'),
(58, 'ali_ahmed', 'zain_khalid', '2', '2022-05-17 06:27:54', 'no', 'yes', 'no'),
(59, 'ali_ahmed', 'zain_khalid', '4', '2022-05-17 06:30:34', 'no', 'yes', 'no'),
(60, 'ali_ahmed', 'zain_khalid', '44', '2022-05-17 06:34:24', 'no', 'yes', 'no'),
(61, 'ali_ahmed', 'zain_khalid', 'dsf', '2022-05-17 06:43:50', 'no', 'yes', 'no'),
(62, 'ali_ahmed', 'zain_khalid', 'fd', '2022-05-17 06:43:55', 'no', 'yes', 'no'),
(63, 'ali_ahmed', 'zain_khalid', '55', '2022-05-17 06:46:37', 'no', 'yes', 'no'),
(64, 'ali_ahmed', 'zain_khalid', 'df', '2022-05-17 06:53:57', 'no', 'yes', 'no'),
(65, 'ali_ahmed', 'zain_khalid', 'ds', '2022-05-17 06:54:26', 'no', 'yes', 'no'),
(67, 'ali_ahmed', 'zain_khalid', 'qwt', '2022-05-17 07:09:06', 'no', 'yes', 'no'),
(68, 'ali_ahmed', 'zain_khalid', 'where are you', '2022-05-17 07:11:45', 'no', 'yes', 'no'),
(69, 'ali_ahmed', 'zain_khalid', 'hi again', '2022-05-17 09:41:10', 'no', 'yes', 'no'),
(70, 'ali_ahmed', 'zain_khalid', 'klfjsd\r\n', '2022-05-17 09:48:37', 'no', 'yes', 'no'),
(71, 'zain_khalid', 'mahmoud_ali', 'hi \r\nhow are you', '2022-05-18 10:38:39', 'no', 'no', 'no'),
(72, 'zain_khalid', 'mahmoud_ali', 'where are you\r\nzain', '2022-05-18 10:42:25', 'no', 'no', 'no'),
(73, 'zain_khalid', 'mahmoud_ali', '1\r\n2\r\n3', '2022-05-18 10:42:42', 'no', 'no', 'no'),
(74, 'mahmoud_ali', 'ali_ali', 'hi\r\n', '2022-05-18 15:54:00', 'yes', 'yes', 'no'),
(75, 'ali_ali', 'mahmoud_ali', 'hi', '2022-05-18 15:55:08', 'no', 'no', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_to` varchar(100) NOT NULL,
  `user_from` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL,
  `opened` varchar(3) NOT NULL,
  `viewed` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_to`, `user_from`, `message`, `link`, `datetime`, `opened`, `viewed`) VALUES
(9, 'mahmoud_ali', 'ali_ahmed', 'Ali Ahmed liked your post', 'post.php?id=34', '2022-05-13 11:15:12', 'no', 'yes'),
(10, 'mahmoud_ali', 'ali_ahmed', 'Ali Ahmed commented on your post', 'post.php?id=34', '2022-05-13 11:15:27', 'no', 'yes'),
(11, 'mahmoud_ali', 'ali_ahmed', 'Ali Ahmed posted on your profile', 'post.php?id=35', '2022-05-13 11:16:01', 'no', 'yes'),
(12, 'zain_khalid', 'ali_ahmed', 'Ali Ahmed commented on your post', 'post.php?id=50', '2022-05-18 10:27:25', 'no', 'no'),
(13, 'zain_khalid', 'ali_ahmed', 'Ali Ahmed commented on your post', 'post.php?id=51', '2022-05-18 10:27:44', 'no', 'no'),
(14, 'mahmoud_ali', 'ali_ali', 'Ali Ali liked your post', 'post.php?id=52', '2022-05-18 15:54:05', 'yes', 'yes'),
(15, 'mahmoud_ali', 'ali_ali', 'Ali Ali commented on your post', 'post.php?id=52', '2022-05-18 15:54:11', 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `body` text NOT NULL,
  `added_by` varchar(60) NOT NULL,
  `user_to` varchar(60) NOT NULL,
  `date_added` datetime NOT NULL,
  `user_closed` varchar(3) NOT NULL,
  `deleted` varchar(3) NOT NULL,
  `likes` int(11) NOT NULL,
  `image` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `body`, `added_by`, `user_to`, `date_added`, `user_closed`, `deleted`, `likes`, `image`) VALUES
(49, 'hi', 'zain_khalid', 'none', '2022-05-15 02:40:34', 'no', 'no', 0, 'assets/images/posts62804c0265b1021599.jpg'),
(50, 'New Wallpaper\r\ncool', 'zain_khalid', 'none', '2022-05-15 05:49:49', 'no', 'no', 1, 'assets/images/posts6280785d19fe2m22.jpg'),
(51, 'good morning ', 'zain_khalid', 'none', '2022-05-17 09:48:05', 'no', 'no', 0, 'assets/images/posts628353357993010848.jpg'),
(52, 'Hi everyone', 'mahmoud_ali', 'none', '2022-05-18 11:22:39', 'no', 'no', 1, 'assets/images/posts6284badfd7143leg3.jpg'),
(53, 'hjkhljknkjnl', 'mahmoud_ali', 'none', '2022-05-18 16:01:01', 'no', 'no', 0, 'assets/images/posts6284fc1dbbacd٧٣٦.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `signup_date` date NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `num_posts` int(11) NOT NULL,
  `num_likes` int(11) NOT NULL,
  `user_closed` varchar(3) NOT NULL,
  `friend_array` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `signup_date`, `profile_pic`, `num_posts`, `num_likes`, `user_closed`, `friend_array`) VALUES
(24, 'Mahmoud', 'Ali', 'mahmoud_ali', 'mahmoud@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2022-05-13', 'assets/images/profile_pics/mahmoud_alid65d5e42be41ee0a0b34dd7e3ca1d20dn.jpeg', 3, 0, 'no', ',zain_khalid,ali_ahmed,'),
(25, 'Ali', 'Ahmed', 'ali_ahmed', 'ali@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2022-05-13', 'assets/images/profile_pics/ali_ahmed1adeab274fcd52c38c69fca3acecf887n.jpeg', 1, 1, 'no', ',zain_khalid,ayman_omar,mahmoud_ali,'),
(26, 'Zain', 'Khalid', 'zain_khalid', 'zain@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', '2022-05-14', 'assets/images/profile_pics/zain_khalid5525461e2b0c693c1deb43c7f0f65d07n.jpeg', 3, 1, 'no', ',ali_ahmed,mahmoud_ali,'),
(27, 'Ayman', 'Omar', 'ayman_omar', 'ayman@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2022-05-17', 'assets/images/profile_pics/ayman_omarddac5534087b074324780435c41d9f85n.jpeg', 0, 0, 'no', ',ali_ahmed,'),
(28, 'Ali', 'Ali', 'ali_ali', 'ali1@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2022-05-18', 'assets/images/profile_pics/defaults/2.png', 0, 1, 'no', ',');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 28, 2026 lúc 04:43 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `warehouse_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `departments`
--

INSERT INTO `departments` (`id`, `code`, `name`, `description`, `created_at`) VALUES
(1, 'BLD', 'Ban Lãnh Đạo', '', '2026-10-21 08:01:01'),
(2, 'IT', 'Phòng IT', '', '2026-10-21 08:03:25'),
(5, 'BPC', 'Ban Pháp Chế', '', '2026-10-21 08:15:07'),
(6, 'BKSNB', 'Ban Kiểm Soát Nội Bộ', '', '2026-10-21 08:16:14'),
(7, 'QLTK', 'Ban Quản Lý Thiết Kế', '', '2026-10-21 08:17:22'),
(8, 'BKT', 'Ban Kế Toán', '', '2026-10-21 08:21:16'),
(9, 'KTDT', 'Ban Kinh Tế Đấu Thầu', '', '2026-10-21 10:02:40'),
(10, 'ĐTDA', 'Ban Đầu Tư Dự Án', '', '2026-10-21 10:08:56'),
(11, 'BHC', 'Ban Hành Chính', '', '2026-10-21 10:12:38'),
(12, 'BNS', 'Ban Nhân Sự', '', '2026-10-21 10:15:02'),
(13, 'BMC', 'Ban Marcom', '', '2026-10-21 10:18:35'),
(14, 'CT H&H', 'CT H&H', '', '2026-10-21 10:20:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `device_code` varchar(50) NOT NULL COMMENT 'Mã thiết bị duy nhất',
  `type` varchar(50) NOT NULL COMMENT 'Lo?i thi?t b?: Laptop, PC, mßy in, mßy CNC...',
  `brand` varchar(50) NOT NULL COMMENT 'Hãng sản xuất: Dell, HP, Lenovo...',
  `model` varchar(100) NOT NULL COMMENT 'Tên model',
  `serial_number` varchar(100) DEFAULT NULL COMMENT 'Số serial',
  `import_date` date NOT NULL COMMENT 'Ngày nhập kho',
  `warranty_expiry` date DEFAULT NULL COMMENT 'Ngày hết hạn bảo hành',
  `condition_state` enum('new','used','broken','under_warranty') NOT NULL DEFAULT 'new' COMMENT 'Tình trạng: Mới, Đang dùng, Hỏng, Bảo hành',
  `status` enum('in_stock','allocated','maintenance','liquidated') NOT NULL DEFAULT 'in_stock' COMMENT 'Trạng thái: Trong kho, Đang cấp phát, Bảo trì, Thanh lý',
  `current_employee_id` int(11) DEFAULT NULL COMMENT 'Nhân viên đang giữ thiết bị',
  `current_department_id` int(11) DEFAULT NULL COMMENT 'Phòng ban đang sử dụng',
  `is_temporary` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1: Cấp phát tạm thời, 0: Dài hạn',
  `expected_return_date` date DEFAULT NULL COMMENT 'Ngày dự kiến trả (nếu tạm thời)',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `devices`
--

INSERT INTO `devices` (`id`, `device_code`, `type`, `brand`, `model`, `serial_number`, `import_date`, `warranty_expiry`, `condition_state`, `status`, `current_employee_id`, `current_department_id`, `is_temporary`, `expected_return_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, '1', 'Laptop', 'Dell', 'Dell Latitude 3420', NULL, '2026-10-21', NULL, 'used', 'allocated', 1, 2, 0, NULL, NULL, '2026-10-21 08:06:25', '2026-10-21 08:06:57'),
(16, 'TB87E959CBE9A2B7CEB75DDE5F', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 4, 1, 0, NULL, NULL, '2026-10-21 11:28:00', '2026-10-21 11:59:16'),
(17, 'TB4D73EED24F927131286E3C1F', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 148, 1, 0, NULL, NULL, '2026-10-21 14:29:06', '2026-10-21 14:32:36'),
(19, 'TB675FDD25E41C340D691BC167', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 6, 1, 0, NULL, NULL, '2026-10-21 14:36:38', '2026-10-21 14:36:59'),
(20, 'TBE7265CD8E557ABD80AD9C456', 'Laptop', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 7, 1, 0, NULL, NULL, '2026-10-21 14:38:32', '2026-10-21 14:38:49'),
(21, 'TB81DDFBD18CBC64C124E43900', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 8, 5, 0, NULL, NULL, '2026-10-21 14:40:29', '2026-10-21 14:43:50'),
(22, 'TBCACB08E8CDC0A49EC1C9F66F', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 9, 5, 0, NULL, NULL, '2026-10-21 15:39:01', '2026-10-21 15:39:31'),
(23, 'TB4E681EB525651A13071C696C', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 10, 5, 0, NULL, NULL, '2026-10-21 15:46:23', '2026-10-21 15:47:06'),
(24, 'TBD786796B95B67F0992A10CB0', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 149, 5, 0, NULL, NULL, '2026-10-21 15:48:27', '2026-10-21 15:49:43'),
(25, 'TB2D3A1C3ED1C7CF293F571617', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 11, 6, 0, NULL, NULL, '2026-10-21 15:50:47', '2026-10-21 15:51:29'),
(26, 'TBFD09758F8FDC7A8124488DD1', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 12, 6, 0, NULL, NULL, '2026-10-21 15:52:24', '2026-10-21 15:52:42'),
(27, 'TB43BB04A78190BFDD41E5176C', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 15, 7, 0, NULL, 'Vỏ case máy tính XIGMATEK XA- 20/XA-22 (ATX); Nguồn XIGMATEK 500W', '2026-10-21 15:54:50', '2026-10-21 15:55:15'),
(28, 'TB6C9D2D725EF573C936608FE6', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 16, 7, 0, NULL, NULL, '2026-10-21 15:56:31', '2026-10-21 15:56:51'),
(29, 'TBA98FC6820500E3B69E8F24A7', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 17, 7, 0, NULL, NULL, '2026-10-21 15:57:57', '2026-10-21 15:58:14'),
(30, 'TB658A4EC15F206F8992816C5F', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 18, 7, 0, NULL, NULL, '2026-10-21 15:59:44', '2026-10-21 16:00:01'),
(31, 'TB5CAD678DA8C4B346DE03229C', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 14, 7, 0, NULL, 'Vỏ cây máy tính Xigmatek XA-20 /22; Nguồn máy tính ATX500', '2026-10-21 16:02:18', '2026-10-21 16:02:53'),
(32, 'TBFDE786614EADF65DC7FABDB4', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 20, 7, 0, NULL, NULL, '2026-10-21 16:05:12', '2026-10-21 16:05:29'),
(33, 'TB7DA2BC2BFB6902BDA8AC56AD', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 21, 7, 0, NULL, NULL, '2026-10-21 16:06:38', '2026-10-21 16:08:28'),
(34, 'TB3663D1F2F864AC51C72AEA01', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 13, 7, 0, NULL, NULL, '2026-10-21 16:11:01', '2026-10-21 16:11:31'),
(35, 'TB50E4AEE119DFD4E944DB497C', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 23, 7, 0, NULL, NULL, '2026-10-21 16:13:12', '2026-10-21 16:13:28'),
(36, 'TB7C3319D109F2521A5D011335', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 151, 7, 0, NULL, NULL, '2026-10-21 16:16:02', '2026-10-21 16:17:10'),
(37, 'TB8665CDD7EF9C1B66DB3AF3F0', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 24, 7, 0, NULL, NULL, '2026-10-21 16:18:29', '2026-10-21 16:18:46'),
(38, 'TB82410AEAFC54E1B6783E24C1', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 26, 7, 0, NULL, NULL, '2026-10-21 16:20:02', '2026-10-21 16:20:15'),
(39, 'TB89E0C8823C73C36B67901837', 'PC', '', '', NULL, '2026-10-21', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-21 16:22:06', '2026-10-21 16:22:06'),
(40, 'TBE1270BABD2290463C248A06D', 'Ổ cứng di động', '', 'HDD Box adata AV320-2T-2.5', NULL, '2026-10-21', NULL, 'used', 'allocated', 151, 7, 0, NULL, 'Sử dụng chung trong công tác lưu chuyển dữ liệu thuộc phạm vi chuyên môn', '2026-10-21 16:23:22', '2026-10-21 16:23:43'),
(41, 'TBAF19323B97B5C7572DA7A66D', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 68, 10, 0, NULL, NULL, '2026-10-21 16:46:19', '2026-10-21 16:46:47'),
(42, 'TBEE29C8C9019FB675F619D14E', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 69, 10, 0, NULL, NULL, '2026-10-21 16:48:12', '2026-10-21 16:48:34'),
(43, 'TB0E5B187903D221901ED904D8', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'allocated', 70, 10, 0, NULL, 'Jonsbo CR-1000 RGB; Case Xigmatek  AERO 2F - 2 FAN; Nguồn Ximareck 450W', '2026-10-21 16:50:37', '2026-10-21 16:50:50'),
(44, 'TBE28C323AB372DBD0DAFFB51F', 'PC', '', '', NULL, '2026-10-21', NULL, 'used', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-21 16:51:47', '2026-10-23 11:14:20'),
(45, 'TB093E732E7FFF9324726098AE', 'Laptop', '', 'Acer aspire A315-54K', NULL, '2026-10-21', NULL, 'used', 'allocated', 72, 10, 0, NULL, NULL, '2026-10-21 16:53:10', '2026-10-21 16:53:24'),
(46, 'TB297576BE6D1BF2BD413D5E5F', 'Laptop', '', 'AMD Radeon Graphics/14 inch full HD/Windows  11 Home/bạc/Vỏ nhựa,', NULL, '2026-10-21', NULL, 'used', 'allocated', 73, 2, 0, NULL, NULL, '2026-10-21 17:11:01', '2026-10-21 17:11:14'),
(47, 'TBF2A296668F6C25DB50929144', 'Laptop', '', 'HP-8B78', NULL, '2026-10-21', NULL, 'used', 'allocated', 83, 12, 0, NULL, NULL, '2026-10-21 17:14:34', '2026-10-27 16:23:54'),
(48, 'TB82B064284F2F1E090D5C8A12', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 92, 13, 0, NULL, NULL, '2026-10-22 08:06:29', '2026-10-22 08:07:55'),
(49, 'TBF3DFB66CEEA63FC6E3367246', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 93, 13, 0, NULL, NULL, '2026-10-22 08:09:18', '2026-10-22 08:09:35'),
(50, 'TBCE83569BD7C6BCB56CDE7E21', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 92, 13, 0, NULL, NULL, '2026-10-22 08:11:04', '2026-10-22 08:14:50'),
(51, 'TB5FFE9AC42A047BC80D69F951', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 95, 13, 0, NULL, NULL, '2026-10-22 08:12:51', '2026-10-22 08:13:06'),
(52, 'TBBFD786E0BAB3E011DBBEC2A7', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 96, 14, 0, NULL, 'Vỏ case Xigmatek XS-29 mATX Office Black, Nguồn máy tính Xigmatek X-Power III 350 300W', '2026-10-22 08:16:40', '2026-10-22 08:16:57'),
(53, 'TB26FBA5CD0A47223E44C9D8CB', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 97, 14, 0, NULL, NULL, '2026-10-22 08:20:04', '2026-10-22 08:20:29'),
(54, 'TBD877AA9D86EC6DCB41FFBF87', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 98, 14, 0, NULL, NULL, '2026-10-22 08:21:21', '2026-10-22 08:21:34'),
(55, 'TB5D6270C23E3C6E8D50300550', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 99, 14, 0, NULL, NULL, '2026-10-22 08:22:22', '2026-10-22 08:22:39'),
(56, 'TB112A5E8404BA39B160776AD3', 'Laptop', '', 'Lenovo IdeaPad Gaming', NULL, '2026-10-22', NULL, 'used', 'allocated', 100, 14, 0, NULL, NULL, '2026-10-22 08:24:02', '2026-10-22 08:24:24'),
(57, 'TB21C07A0EB6AAC652FEE3055D', 'Laptop', '', 'Laptop xách tay Dell Latitude 3520', NULL, '2026-10-22', NULL, 'used', 'allocated', 101, 14, 0, NULL, '\'Laptop xách tay Dell Latitude 3520, Intel Core i5-1135G7, 8GB Ram, 256GB SSD, 15.6\'\' FHD, WC, Wl+BT, Fedora, 1Y, chuột', '2026-10-22 08:25:56', '2026-10-22 08:26:12'),
(58, 'TB70F034AE00BA73257D6ED98F', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 102, 14, 0, NULL, NULL, '2026-10-22 08:27:36', '2026-10-22 08:27:54'),
(59, 'TB5450260DAE5D918B9DF294C8', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 103, 14, 0, NULL, NULL, '2026-10-22 08:28:42', '2026-10-22 08:29:11'),
(60, 'TBB86E0EC09FCA69FF2EAEE3A6', 'PC', '', 'Dell Optiplex 7010', NULL, '2026-10-22', NULL, 'used', 'allocated', 104, 14, 0, NULL, NULL, '2026-10-22 08:30:11', '2026-10-22 08:30:27'),
(61, 'TB3296896572B15567E4370016', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 105, 14, 0, NULL, NULL, '2026-10-22 08:31:24', '2026-10-22 08:31:36'),
(62, 'TB0D8BB52C98FD0EE9CEC35263', 'Laptop', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 106, 14, 0, NULL, NULL, '2026-10-22 08:32:01', '2026-10-22 08:32:10'),
(63, 'TB759B4AC7F18BA3DC38B81D27', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 27, 8, 0, NULL, NULL, '2026-10-22 08:52:21', '2026-10-22 08:54:27'),
(64, 'TBF1EA30852F1768A2F8471433', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 28, 8, 0, NULL, NULL, '2026-10-22 08:55:51', '2026-10-22 08:56:07'),
(65, 'TB3C1F2B89619E4F829FE4A794', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 29, 8, 0, NULL, 'Vỏ Cây Orien HP-T05', '2026-10-22 08:57:11', '2026-10-22 08:57:47'),
(66, 'TBE890F9A14839543C30A3124F', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 30, 8, 0, NULL, NULL, '2026-10-22 08:59:57', '2026-10-22 09:00:15'),
(67, 'TB0A4AC2C34442D0201481532A', 'PC', '', 'Dell OH4VK7', NULL, '2026-10-22', NULL, 'used', 'allocated', 31, 8, 0, NULL, NULL, '2026-10-22 09:01:11', '2026-10-22 09:01:28'),
(68, 'TB1BEE25C470E9A9426D58A107', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 32, 8, 0, NULL, NULL, '2026-10-22 09:02:22', '2026-10-22 09:02:33'),
(69, 'TB2D95A054702E24E4BCA89FA5', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 33, 8, 0, NULL, NULL, '2026-10-22 09:04:05', '2026-10-22 09:04:19'),
(70, 'TB7693443262681BF8588C393B', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 34, 8, 0, NULL, 'Hdd Segate 1T ,Vỏ case máy tính Orient T05, VGA Nividia Geforce  GT730- 4G, Nguồn máy tính Orien 500w', '2026-10-22 09:09:35', '2026-10-22 09:09:54'),
(71, 'TB1B45F8FF0841CCEC58C3DC63', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 35, 8, 0, NULL, '\"Ổ cứng HDD Seagate Barracuda\r\n1TB 3.5 inch 7200RPM, SATA; Chuột phím Rapoo USB; Vỏ Cây Orien T05; Nguồn Xigmatek X350\r\n\"', '2026-10-22 09:11:25', '2026-10-22 09:11:42'),
(72, 'TB3AA36684C424D8ADA0C53CA9', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 36, 8, 0, NULL, NULL, '2026-10-22 09:13:20', '2026-10-22 09:13:51'),
(73, 'TB3E2FD5FC1C5ECFA90A2FD823', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 35, 8, 0, NULL, NULL, '2026-10-22 09:14:50', '2026-10-22 09:15:08'),
(74, 'TB7B2C1111FD1CA2FE99767C97', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 37, 8, 0, NULL, NULL, '2026-10-22 09:18:17', '2026-10-22 09:18:35'),
(75, 'TBD39FE47AEB64D01CEB67979B', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 38, 8, 0, NULL, NULL, '2026-10-22 09:20:15', '2026-10-22 09:20:29'),
(76, 'TB4E6345B09C872D1FB10F35CA', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 39, 8, 0, NULL, NULL, '2026-10-22 09:23:40', '2026-10-22 09:23:54'),
(77, 'TB4802AC20CD19B80B7BA0F37B', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 40, 8, 0, NULL, NULL, '2026-10-22 09:24:40', '2026-10-22 09:24:51'),
(78, 'TB7AA7DC5C2611C4CEE6C24653', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 41, 8, 0, NULL, NULL, '2026-10-22 09:31:14', '2026-10-22 09:31:54'),
(80, 'TB5BF3A93F165D3E88224BCFDA', 'CASE', '', 'CASE MÁY CHỦ', NULL, '2026-10-22', NULL, 'used', 'allocated', 43, 8, 0, NULL, NULL, '2026-10-22 09:34:19', '2026-10-22 09:34:28'),
(81, 'TB8ADB1FCBA646BF67890AB246', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 44, 8, 0, NULL, NULL, '2026-10-22 09:35:33', '2026-10-22 09:35:51'),
(82, 'TB4146F39A12263779B09DF296', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 45, 8, 0, NULL, NULL, '2026-10-22 09:36:51', '2026-10-22 09:37:19'),
(83, 'TBBD88D3FC65438E7F782E9C65', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 46, 8, 0, NULL, NULL, '2026-10-22 09:39:26', '2026-10-22 09:39:38'),
(84, 'TB86A78695321E9CB7DB1054AE', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 47, 8, 0, NULL, NULL, '2026-10-22 09:40:30', '2026-10-22 09:40:43'),
(85, 'TB78686C11259697B8BC7D193D', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 48, 8, 0, NULL, NULL, '2026-10-22 09:41:29', '2026-10-22 09:41:46'),
(86, 'TB06E7C06C2B0047CC23D82A03', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 42, 8, 0, NULL, NULL, '2026-10-22 09:42:55', '2026-10-22 09:43:30'),
(87, 'TB158950A2CC478B778C957E28', 'Laptop', '', 'Asus Expert P1503CVA _P1503CVA-i5SE16-50W Xám', NULL, '2026-10-22', NULL, 'used', 'allocated', 49, 8, 0, NULL, NULL, '2026-10-22 09:45:10', '2026-10-22 09:45:45'),
(88, 'TBCF30FA6CB9252618C3EA8C0E', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 50, 8, 0, NULL, 'Vỏ máy tính Xigmatek XS-29, Nguồn máy tính Xigmatek X-Power III 350 (250W,230V)', '2026-10-22 09:47:07', '2026-10-22 09:47:34'),
(89, 'TB4DFB9BC57BD976E5F9D6DF89', 'LK', '', '03 ổ cứng SSD Transcend 1TB', NULL, '2026-10-22', NULL, 'used', 'allocated', 34, 8, 0, NULL, NULL, '2026-10-22 09:47:55', '2026-10-22 09:48:36'),
(90, 'TB09A501A27D9C6CF23D217FA1', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 52, 8, 0, NULL, NULL, '2026-10-22 09:49:22', '2026-10-22 09:49:34'),
(91, 'TB4A68E0B03109E609BAA64F64', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 53, 8, 0, NULL, NULL, '2026-10-22 09:50:15', '2026-10-22 09:50:28'),
(92, 'TBCB326475E59581D474485D7F', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 54, 8, 0, NULL, NULL, '2026-10-22 09:51:19', '2026-10-22 09:51:31'),
(94, 'TBA47EB7674FDD9D0A1E759A06', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 75, 11, 0, NULL, 'vỏ SX29MATX, X-power III 350-300W,', '2026-10-22 09:54:52', '2026-10-22 09:55:06'),
(95, 'TB453637D1B1F72AC6BE5A13EA', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 76, 11, 0, NULL, NULL, '2026-10-22 09:55:45', '2026-10-22 09:55:58'),
(96, 'TB8DD4DA5A0ABAEBB5BDE2AE06', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 77, 11, 0, NULL, NULL, '2026-10-22 09:56:32', '2026-10-22 09:57:07'),
(97, 'TBEDB7B33C51C37BD255003E94', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 78, 11, 0, NULL, NULL, '2026-10-22 09:57:59', '2026-10-22 09:58:15'),
(98, 'TBF82602E1F5D6A7B36510C8B7', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 79, 11, 0, NULL, NULL, '2026-10-22 09:59:02', '2026-10-22 09:59:15'),
(99, 'TBEB54E2C8727453EAD286D54D', 'Laptop', '', 'Lenovo', NULL, '2026-10-22', NULL, 'used', 'allocated', 80, 11, 0, NULL, NULL, '2026-10-22 09:59:59', '2026-10-22 10:00:12'),
(100, 'TB87E895774218DBA0C819AAF7', 'Laptop', '', 'Dell 02MJVY,', NULL, '2026-10-22', NULL, 'used', 'allocated', 81, 11, 0, NULL, NULL, '2026-10-22 10:00:58', '2026-10-22 10:01:10'),
(102, 'TB6BA7535D328F3DF8A308FF79', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 83, 12, 0, NULL, NULL, '2026-10-22 10:03:06', '2026-10-22 10:03:16'),
(103, 'TB879663D9C5C0DC7AFB4F9EDB', 'Laptop', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 84, 12, 0, NULL, NULL, '2026-10-22 10:03:58', '2026-10-22 10:04:14'),
(105, 'TB37162B2D191F459C3A4A829C', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 86, 12, 0, NULL, NULL, '2026-10-22 10:06:21', '2026-10-22 10:06:37'),
(106, 'TB7D228C901C42DAA889F93510', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 87, 12, 0, NULL, 'Vỏ case máy tính Orient T03, Nguồn Orient ATX500', '2026-10-22 10:08:43', '2026-10-22 10:08:58'),
(107, 'TB4618ED6C65CFC703B1801793', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 88, 12, 0, NULL, 'SSD 120Gb WD, Seagate 1T, Vỏ case máy tính Orient T03, Nguồn Orient ATX500', '2026-10-22 10:10:12', '2026-10-22 10:10:20'),
(108, 'TB3AEE50E9CEFB9639AD883249', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 89, 12, 0, NULL, NULL, '2026-10-22 10:11:51', '2026-10-22 10:12:00'),
(109, 'TBD5B39F4D740F8C9F196ED8CF', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 90, 12, 0, NULL, NULL, '2026-10-22 10:12:49', '2026-10-22 10:13:03'),
(110, 'TB36AC7F300075392574B5CF8F', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 55, 9, 0, NULL, NULL, '2026-10-22 10:14:45', '2026-10-22 10:14:55'),
(111, 'TB9E3CE970F2CBF80B6680B1AE', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 56, 9, 0, NULL, NULL, '2026-10-22 10:15:37', '2026-10-22 10:16:15'),
(112, 'TBDC7E5A5C91AAC5F181B58347', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 57, 9, 0, NULL, NULL, '2026-10-22 10:19:48', '2026-10-22 10:20:03'),
(113, 'TBB01C02D0E54FD9B6B5CCCE2B', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 58, 9, 0, NULL, 'Nguồn máy tính Ximateck 350W', '2026-10-22 10:39:49', '2026-10-22 10:40:04'),
(114, 'TB733B563479BF6C136F3DE834', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 59, 9, 0, NULL, NULL, '2026-10-22 10:41:03', '2026-10-22 10:41:23'),
(115, 'TB6AF68C6B469F82A11A19DF55', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 60, 9, 0, NULL, NULL, '2026-10-22 10:42:06', '2026-10-22 10:42:25'),
(116, 'TB2AEF6778A86AE90538528088', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 61, 9, 0, NULL, NULL, '2026-10-22 10:43:07', '2026-10-22 10:43:21'),
(117, 'TB89C2B3A029BDB59A25572F38', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 62, 9, 0, NULL, NULL, '2026-10-22 10:43:58', '2026-10-22 10:44:13'),
(118, 'TB6D4F1EA5387F09BF4776B4BA', 'PC', '', 'CB xin dùng máy cá nhân - gửi đề xuất qua mail', NULL, '2026-10-22', NULL, 'used', 'allocated', 63, 9, 0, NULL, NULL, '2026-10-22 10:48:55', '2026-10-22 10:49:04'),
(120, 'TBAC02D21FB92CE872D2FD6816', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 65, 9, 0, NULL, NULL, '2026-10-22 10:53:00', '2026-10-22 10:53:18'),
(121, 'TB99B4599669F9A26C5C711F56', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 66, 9, 0, NULL, NULL, '2026-10-22 10:54:06', '2026-10-22 10:54:28'),
(122, 'TB9DF9D6CED7517CFA1DC0D2AA', 'PC', '', '', NULL, '2026-10-22', NULL, 'used', 'allocated', 67, 9, 0, NULL, NULL, '2026-10-22 10:55:06', '2026-10-22 10:55:55'),
(123, 'TB46FE5A2E10C0368A383A73B8', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:46:50', '2026-10-23 10:46:50'),
(124, 'TB9C6BF0F40DA8505FBA6C0B47', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:47:46', '2026-10-23 10:47:46'),
(125, 'TBD820BB55A79B9A262D218651', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:48:36', '2026-10-23 10:48:36'),
(126, 'TB9720104AA361D2F9E949A078', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:49:48', '2026-10-23 10:49:48'),
(127, 'TB00BEDB0EFFD0D0D7524D85B2', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:50:36', '2026-10-23 10:50:36'),
(128, 'TBF56E07316DEE13B4D866B6E4', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:51:22', '2026-10-23 10:51:22'),
(129, 'TBF6E56941161995934283B657', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:51:53', '2026-10-23 10:51:53'),
(130, 'TB88074DE4443485607BC72B75', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, 'Cạnh a Hưng', '2026-10-23 10:52:44', '2026-10-23 10:53:44'),
(131, 'TB83B9FAD9CB23307230CCE1C4', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, 'Cạnh a Hưng', '2026-10-23 10:53:36', '2026-10-23 10:53:36'),
(132, 'TBF0EA33E790A85608EDF73DBF', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, 'Máy cạnh chị Ngát KT', '2026-10-23 10:54:38', '2026-10-23 10:54:38'),
(133, 'TBEF9E8754D06B01AA9F73BED9', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, 'Seagate 1TB 3.5 inch 7200RPM, SATA; Nguồn máy tính Xigmatek X350; Vỏ Case Orient ATX T03/05;\r\n\r\n\r\n\"Cao Thị Tuyết Nhung -\r\n CV PLHĐ đã nghỉ ngày 06/10/2026\"\r\n=> Đag lưu tại Ban', '2026-10-23 10:57:19', '2026-10-23 10:57:19'),
(134, 'TB054C195D61E9572922FB8044', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:58:19', '2026-10-23 10:58:19'),
(135, 'TBB1458ECCB213FC72E8E52E00', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 10:58:54', '2026-10-23 10:58:54'),
(136, 'TB584D79983F7586A8BF4969A2', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 11:00:30', '2026-10-23 11:00:30'),
(137, 'TB620E8A5616C44DEC4BAD3B01', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 11:01:03', '2026-10-23 11:01:03'),
(138, 'TB42F9402ADB177D159D9961D0', 'PC', '', '', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 11:02:56', '2026-10-23 11:02:56'),
(139, 'TB73E3CC6B197773F9F19C2608', 'PC', '', 'Không có ổ cứng', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 11:03:11', '2026-10-23 11:03:11'),
(140, 'TBD9F3F5729703949B91FAE189', 'Laptop', 'HP', 'HP - PAVILION', NULL, '2026-10-23', NULL, 'used', 'allocated', 91, 13, 0, NULL, NULL, '2026-10-23 16:23:16', '2026-10-26 09:02:55'),
(141, 'TB83604B9BFBF4AC4E3A262980', 'Laptop', '', 'HP - PROBOOK', NULL, '2026-10-23', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-23 16:56:59', '2026-10-23 16:56:59'),
(142, 'TBA8458CCE399BF177E4E41296', 'MH', 'HKC', 'MB20S1 - 20 INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:29:26', '2026-10-26 15:31:53'),
(143, 'TB9F86737E86060A69578002BA', 'MH', 'AOC', 'E2270SWN - 21.5 INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:30:59', '2026-10-26 15:30:59'),
(144, 'TB9942A93D9AB63FD81F2922DE', 'MH', 'DELL', 'E2225H-21.5', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:31:39', '2026-10-26 15:31:39'),
(145, 'TB2596EC025994ABA69887C302', 'MH', 'SPING', 'SGP195S - 19.5 INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:32:25', '2026-10-26 15:33:25'),
(146, 'TB2E0A4E24A06A22FC032115C6', 'MH', 'SAMSUNG', 'S22C310EAE - 22INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:33:47', '2026-10-26 15:33:47'),
(147, 'TB89763C13BFBB2B9830F60062', 'MH', 'SAMSUNG', 'S20D300NH - 20INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:34:09', '2026-10-26 15:34:09'),
(148, 'TB7E72EF419B9C3FECCF51CFA6', 'MH', 'ACER', 'V226HQL - 21.5 INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:34:29', '2026-10-26 15:34:29'),
(149, 'TB807C248DC000194F7CBA3410', 'MH', 'DAHUA', 'DHI-LM24-B221 - 21.5 INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:34:57', '2026-10-26 15:34:57'),
(150, 'TBA65DE7AC182CAA735EFF3055', 'MH', 'DELL', 'E2225HSM - 21.5 INCH', NULL, '2026-10-26', NULL, 'new', 'in_stock', NULL, NULL, 0, NULL, NULL, '2026-10-26 15:35:16', '2026-10-26 15:35:16'),
(151, 'TB3D8DE4E1950D0C54E43E585A', 'PC', '', '', NULL, '2026-10-28', NULL, 'used', 'allocated', 25, 7, 0, NULL, NULL, '2026-10-28 09:03:15', '2026-10-28 09:04:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `device_history`
--

CREATE TABLE `device_history` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL COMMENT 'import, allocate, transfer, revoke, maintenance_start, maintenance_done, upgrade, liquidate',
  `action_date` datetime NOT NULL DEFAULT current_timestamp(),
  `performed_by` int(11) NOT NULL,
  `from_employee_id` int(11) DEFAULT NULL,
  `to_employee_id` int(11) DEFAULT NULL,
  `from_department_id` int(11) DEFAULT NULL,
  `to_department_id` int(11) DEFAULT NULL,
  `description` text NOT NULL COMMENT 'Mô tả chi tiết hành động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `device_history`
--

INSERT INTO `device_history` (`id`, `device_id`, `ticket_id`, `action_type`, `action_date`, `performed_by`, `from_employee_id`, `to_employee_id`, `from_department_id`, `to_department_id`, `description`) VALUES
(1, 1, NULL, 'import', '2026-10-21 08:06:25', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới Laptop Dell Dell Latitude 3420 (Mã: 1)'),
(2, 1, 1, 'allocate', '2026-10-21 08:06:57', 1, NULL, 1, NULL, 2, 'Cấp phát thiết bị theo phiếu PX-20261021-79A3 (Phụ kiện: Chuột, lót chuột). Lý do: Cấp máy nhân sự mới'),
(17, 16, NULL, 'import', '2026-10-21 11:28:00', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 16: PC  '),
(18, 16, 2, 'allocate', '2026-10-21 11:59:16', 1, NULL, 4, NULL, 1, 'Cấp phát thiết bị theo phiếu PX-20261021-6D0D. Lý do: '),
(19, 17, NULL, 'import', '2026-10-21 14:29:06', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 17: PC  '),
(21, 17, 3, 'allocate', '2026-10-21 14:32:36', 1, NULL, 148, NULL, 1, 'Cấp phát thiết bị theo phiếu PX-20261021-435D (Phụ kiện: Chuột, phím). Lý do: '),
(22, 19, NULL, 'import', '2026-10-21 14:36:38', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 19: PC  '),
(23, 19, 4, 'allocate', '2026-10-21 14:36:59', 1, NULL, 6, NULL, 1, 'Cấp phát thiết bị theo phiếu PX-20261021-09C6 (Phụ kiện: Chuột, phím). Lý do: '),
(24, 20, NULL, 'import', '2026-10-21 14:38:32', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 20: Laptop  '),
(25, 20, 5, 'allocate', '2026-10-21 14:38:49', 1, NULL, 7, NULL, 1, 'Cấp phát thiết bị theo phiếu PX-20261021-A6C3 (Phụ kiện: Chuột, phím). Lý do: '),
(26, 21, NULL, 'import', '2026-10-21 14:40:29', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 21: PC  '),
(27, 21, 6, 'allocate', '2026-10-21 14:43:50', 1, NULL, 8, NULL, 5, 'Cấp phát thiết bị theo phiếu PX-20261021-85BB (Phụ kiện: phím). Lý do: '),
(28, 22, NULL, 'import', '2026-10-21 15:39:01', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 22: PC  '),
(29, 22, 7, 'allocate', '2026-10-21 15:39:31', 1, NULL, 9, NULL, 5, 'Cấp phát thiết bị theo phiếu PX-20261021-53EB (Phụ kiện: Chuột, phím). Lý do: '),
(30, 23, NULL, 'import', '2026-10-21 15:46:23', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 23: PC  '),
(31, 23, 8, 'allocate', '2026-10-21 15:47:06', 1, NULL, 10, NULL, 5, 'Cấp phát thiết bị theo phiếu PX-20261021-5D2A (Phụ kiện: Chuột, phím). Lý do: '),
(32, 24, NULL, 'import', '2026-10-21 15:48:27', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 24: PC  '),
(33, 24, 9, 'allocate', '2026-10-21 15:49:43', 1, NULL, 149, NULL, 5, 'Cấp phát thiết bị theo phiếu PX-20261021-F581 (Phụ kiện: Chuột, phím). Lý do: '),
(34, 25, NULL, 'import', '2026-10-21 15:50:47', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 25: PC  '),
(35, 25, 10, 'allocate', '2026-10-21 15:51:29', 1, NULL, 11, NULL, 6, 'Cấp phát thiết bị theo phiếu PX-20261021-9E6E. Lý do: '),
(36, 26, NULL, 'import', '2026-10-21 15:52:24', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 26: PC  '),
(37, 26, 11, 'allocate', '2026-10-21 15:52:42', 1, NULL, 12, NULL, 6, 'Cấp phát thiết bị theo phiếu PX-20261021-6CB6 (Phụ kiện: Chuột, phím). Lý do: '),
(38, 27, NULL, 'import', '2026-10-21 15:54:50', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 27: PC  '),
(39, 27, 12, 'allocate', '2026-10-21 15:55:15', 1, NULL, 15, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-0F74 (Phụ kiện: Chuột, phím). Lý do: '),
(40, 28, NULL, 'import', '2026-10-21 15:56:31', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 28: PC  '),
(41, 28, 13, 'allocate', '2026-10-21 15:56:51', 1, NULL, 16, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-12C0 (Phụ kiện: Chuột, phím). Lý do: '),
(42, 29, NULL, 'import', '2026-10-21 15:57:57', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 29: PC  '),
(43, 29, 14, 'allocate', '2026-10-21 15:58:14', 1, NULL, 17, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-E31D (Phụ kiện: Chuột, phím). Lý do: '),
(44, 30, NULL, 'import', '2026-10-21 15:59:44', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 30: PC  '),
(45, 30, 15, 'allocate', '2026-10-21 16:00:01', 1, NULL, 18, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-43DB (Phụ kiện: Chuột, phím). Lý do: '),
(46, 31, NULL, 'import', '2026-10-21 16:02:18', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 31: PC  '),
(47, 31, 16, 'allocate', '2026-10-21 16:02:53', 1, NULL, 14, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-3A57 (Phụ kiện: Chuột, phím). Lý do: '),
(48, 32, NULL, 'import', '2026-10-21 16:05:12', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 32: PC  '),
(49, 32, 17, 'allocate', '2026-10-21 16:05:29', 1, NULL, 20, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-B4A2 (Phụ kiện: Chuột, phím). Lý do: '),
(50, 33, NULL, 'import', '2026-10-21 16:06:38', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 33: PC  '),
(51, 33, 18, 'allocate', '2026-10-21 16:08:28', 1, NULL, 21, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-1C86 (Phụ kiện: Chuột, phím). Lý do: '),
(52, 34, NULL, 'import', '2026-10-21 16:11:01', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 34: PC  '),
(53, 34, 19, 'allocate', '2026-10-21 16:11:31', 1, NULL, 13, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-D43E (Phụ kiện: Chuột, phím). Lý do: '),
(54, 35, NULL, 'import', '2026-10-21 16:13:12', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 35: PC  '),
(55, 35, 20, 'allocate', '2026-10-21 16:13:28', 1, NULL, 23, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-AF15 (Phụ kiện: Chuột, phím). Lý do: '),
(56, 36, NULL, 'import', '2026-10-21 16:16:02', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 36: PC  '),
(57, 36, 21, 'allocate', '2026-10-21 16:17:10', 1, NULL, 151, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-E19B (Phụ kiện: Chuột, phím). Lý do: '),
(58, 37, NULL, 'import', '2026-10-21 16:18:29', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 37: PC  '),
(59, 37, 22, 'allocate', '2026-10-21 16:18:46', 1, NULL, 24, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-9A04 (Phụ kiện: Chuột, phím). Lý do: '),
(60, 38, NULL, 'import', '2026-10-21 16:20:02', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 38: PC  '),
(61, 38, 23, 'allocate', '2026-10-21 16:20:15', 1, NULL, 26, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-83C2 (Phụ kiện: Chuột, phím). Lý do: '),
(62, 39, NULL, 'import', '2026-10-21 16:22:06', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 39: PC  '),
(63, 40, NULL, 'import', '2026-10-21 16:23:22', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 40: Ổ cứng di động  HDD Box adata AV320-2T-2.5'),
(64, 40, 24, 'allocate', '2026-10-21 16:23:43', 1, NULL, 151, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261021-A6DD. Lý do: '),
(65, 41, NULL, 'import', '2026-10-21 16:46:19', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 41: PC  '),
(66, 41, 25, 'allocate', '2026-10-21 16:46:47', 1, NULL, 68, NULL, 10, 'Cấp phát thiết bị theo phiếu PX-20261021-7B0B (Phụ kiện: Chuột, phím). Lý do: '),
(67, 42, NULL, 'import', '2026-10-21 16:48:12', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 42: PC  '),
(68, 42, 26, 'allocate', '2026-10-21 16:48:34', 1, NULL, 69, NULL, 10, 'Cấp phát thiết bị theo phiếu PX-20261021-9C8C (Phụ kiện: Chuột, phím). Lý do: '),
(69, 43, NULL, 'import', '2026-10-21 16:50:37', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 43: PC  '),
(70, 43, 27, 'allocate', '2026-10-21 16:50:50', 1, NULL, 70, NULL, 10, 'Cấp phát thiết bị theo phiếu PX-20261021-6C74 (Phụ kiện: Chuột, phím). Lý do: '),
(71, 44, NULL, 'import', '2026-10-21 16:51:47', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 44: PC  '),
(72, 44, 28, 'allocate', '2026-10-21 16:52:02', 1, NULL, 71, NULL, 10, 'Cấp phát thiết bị theo phiếu PX-20261021-35A0 (Phụ kiện: Chuột, phím). Lý do: '),
(73, 45, NULL, 'import', '2026-10-21 16:53:10', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 45: Laptop  Acer aspire A315-54K'),
(74, 45, 29, 'allocate', '2026-10-21 16:53:24', 1, NULL, 72, NULL, 10, 'Cấp phát thiết bị theo phiếu PX-20261021-6D09. Lý do: '),
(75, 46, NULL, 'import', '2026-10-21 17:11:01', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 46: Laptop  AMD Radeon Graphics/14 inch full HD/Windows  11 Home/bạc/Vỏ nhựa,'),
(76, 46, 30, 'allocate', '2026-10-21 17:11:14', 1, NULL, 73, NULL, 2, 'Cấp phát thiết bị theo phiếu PX-20261021-D8BD. Lý do: '),
(77, 47, NULL, 'import', '2026-10-21 17:14:34', 2, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 47: Laptop  HP-8B78'),
(78, 47, 31, 'allocate', '2026-10-21 17:14:51', 2, NULL, 91, NULL, 13, 'Cấp phát thiết bị theo phiếu PX-20261021-6BFC. Lý do: '),
(79, 48, NULL, 'import', '2026-10-22 08:06:29', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 48: PC  '),
(80, 48, 32, 'allocate', '2026-10-22 08:07:55', 1, NULL, 92, NULL, 13, 'Cấp phát thiết bị theo phiếu PX-20261022-3919 (Phụ kiện: Chuột, phím). Lý do: '),
(81, 49, NULL, 'import', '2026-10-22 08:09:18', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 49: PC  '),
(82, 49, 33, 'allocate', '2026-10-22 08:09:35', 1, NULL, 93, NULL, 13, 'Cấp phát thiết bị theo phiếu PX-20261022-6821 (Phụ kiện: Chuột, phím). Lý do: '),
(83, 50, NULL, 'import', '2026-10-22 08:11:04', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 50: PC  '),
(84, 50, 34, 'allocate', '2026-10-22 08:11:19', 1, NULL, 94, NULL, 13, 'Cấp phát thiết bị theo phiếu PX-20261022-0236 (Phụ kiện: Chuột, phím). Lý do: '),
(85, 51, NULL, 'import', '2026-10-22 08:12:51', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 51: PC  '),
(86, 51, 35, 'allocate', '2026-10-22 08:13:06', 1, NULL, 95, NULL, 13, 'Cấp phát thiết bị theo phiếu PX-20261022-ABC8. Lý do: '),
(87, 50, 36, 'transfer', '2026-10-22 08:14:50', 1, 94, 92, 13, 13, 'Chuyển giao thiết bị theo phiếu PCG-20261022-5EC2 (Phụ kiện: Chuột, phím). Lý do: '),
(88, 52, NULL, 'import', '2026-10-22 08:16:40', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 52: PC  '),
(89, 52, 37, 'allocate', '2026-10-22 08:16:57', 1, NULL, 96, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-F5FA (Phụ kiện: Chuột, phím). Lý do: '),
(90, 53, NULL, 'import', '2026-10-22 08:20:04', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 53: PC  '),
(91, 53, 38, 'allocate', '2026-10-22 08:20:29', 1, NULL, 97, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-9CD7 (Phụ kiện: Chuột, phím). Lý do: '),
(92, 54, NULL, 'import', '2026-10-22 08:21:21', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 54: PC  '),
(93, 54, 39, 'allocate', '2026-10-22 08:21:34', 1, NULL, 98, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-F3BB (Phụ kiện: Chuột, phím). Lý do: '),
(94, 55, NULL, 'import', '2026-10-22 08:22:22', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 55: PC  '),
(95, 55, 40, 'allocate', '2026-10-22 08:22:39', 1, NULL, 99, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-5734 (Phụ kiện: Chuột, phím). Lý do: '),
(96, 56, NULL, 'import', '2026-10-22 08:24:02', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 56: Laptop  Lenovo IdeaPad Gaming'),
(97, 56, 41, 'allocate', '2026-10-22 08:24:24', 1, NULL, 100, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-6514 (Phụ kiện: chuột). Lý do: '),
(98, 57, NULL, 'import', '2026-10-22 08:25:56', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 57: Laptop  Laptop xách tay Dell Latitude 3520'),
(99, 57, 42, 'allocate', '2026-10-22 08:26:12', 1, NULL, 101, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-F0DC. Lý do: '),
(100, 58, NULL, 'import', '2026-10-22 08:27:36', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 58: PC  '),
(101, 58, 43, 'allocate', '2026-10-22 08:27:54', 1, NULL, 102, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-C0C0. Lý do: '),
(102, 59, NULL, 'import', '2026-10-22 08:28:42', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 59: PC  '),
(103, 59, 44, 'allocate', '2026-10-22 08:29:11', 1, NULL, 103, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-F7CC (Phụ kiện: Chuột, phím). Lý do: '),
(104, 60, NULL, 'import', '2026-10-22 08:30:11', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 60: PC  Dell Optiplex 7010'),
(105, 60, 45, 'allocate', '2026-10-22 08:30:27', 1, NULL, 104, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-0A39. Lý do: '),
(106, 61, NULL, 'import', '2026-10-22 08:31:24', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 61: PC  '),
(107, 61, 46, 'allocate', '2026-10-22 08:31:36', 1, NULL, 105, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-8144. Lý do: '),
(108, 62, NULL, 'import', '2026-10-22 08:32:01', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 62: Laptop  '),
(109, 62, 47, 'allocate', '2026-10-22 08:32:10', 1, NULL, 106, NULL, 14, 'Cấp phát thiết bị theo phiếu PX-20261022-5A2A. Lý do: '),
(110, 63, NULL, 'import', '2026-10-22 08:52:21', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 63: PC  '),
(111, 63, 48, 'allocate', '2026-10-22 08:54:27', 1, NULL, 27, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-EB8E. Lý do: '),
(112, 64, NULL, 'import', '2026-10-22 08:55:51', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 64: PC  '),
(113, 64, 49, 'allocate', '2026-10-22 08:56:07', 1, NULL, 28, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-7A86 (Phụ kiện: Chuột, phím). Lý do: '),
(114, 65, NULL, 'import', '2026-10-22 08:57:11', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 65: PC  '),
(115, 65, 50, 'allocate', '2026-10-22 08:57:47', 1, NULL, 29, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-D8F5 (Phụ kiện: Chuột, phím). Lý do: '),
(116, 66, NULL, 'import', '2026-10-22 08:59:57', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 66: PC  '),
(117, 66, 51, 'allocate', '2026-10-22 09:00:15', 1, NULL, 30, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-EA59 (Phụ kiện: Chuột, phím). Lý do: '),
(118, 67, NULL, 'import', '2026-10-22 09:01:11', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 67: PC  Dell OH4VK7'),
(119, 67, 52, 'allocate', '2026-10-22 09:01:28', 1, NULL, 31, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-2198 (Phụ kiện: Chuột, phím). Lý do: '),
(120, 68, NULL, 'import', '2026-10-22 09:02:22', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 68: PC  '),
(121, 68, 53, 'allocate', '2026-10-22 09:02:33', 1, NULL, 32, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-EA9C (Phụ kiện: Chuột, phím). Lý do: '),
(122, 69, NULL, 'import', '2026-10-22 09:04:05', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 69: PC  '),
(123, 69, 54, 'allocate', '2026-10-22 09:04:19', 1, NULL, 33, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-2FFE (Phụ kiện: Chuột, phím). Lý do: '),
(124, 70, NULL, 'import', '2026-10-22 09:09:35', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 70: PC  '),
(125, 70, 55, 'allocate', '2026-10-22 09:09:54', 1, NULL, 34, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-E1EB. Lý do: '),
(126, 71, NULL, 'import', '2026-10-22 09:11:25', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 71: PC  '),
(127, 71, 56, 'allocate', '2026-10-22 09:11:42', 1, NULL, 35, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-C039 (Phụ kiện: Chuột, phím). Lý do: '),
(128, 72, NULL, 'import', '2026-10-22 09:13:20', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 72: PC  '),
(129, 72, 57, 'allocate', '2026-10-22 09:13:51', 1, NULL, 36, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-5CE5 (Phụ kiện: Chuột, phím). Lý do: '),
(130, 73, NULL, 'import', '2026-10-22 09:14:50', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 73: PC  '),
(131, 73, 58, 'allocate', '2026-10-22 09:15:08', 1, NULL, 35, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-A95D (Phụ kiện: Chuột, phím). Lý do: '),
(132, 74, NULL, 'import', '2026-10-22 09:18:17', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 74: PC  '),
(133, 74, 59, 'allocate', '2026-10-22 09:18:35', 1, NULL, 37, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-DF84 (Phụ kiện: Chuột, phím). Lý do: '),
(134, 75, NULL, 'import', '2026-10-22 09:20:15', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 75: PC  '),
(135, 75, 60, 'allocate', '2026-10-22 09:20:29', 1, NULL, 38, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-8421 (Phụ kiện: Chuột, phím). Lý do: '),
(136, 76, NULL, 'import', '2026-10-22 09:23:40', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 76: PC  '),
(137, 76, 61, 'allocate', '2026-10-22 09:23:54', 1, NULL, 39, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-5958 (Phụ kiện: Chuột, phím). Lý do: '),
(138, 77, NULL, 'import', '2026-10-22 09:24:40', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 77: PC  '),
(139, 77, 62, 'allocate', '2026-10-22 09:24:51', 1, NULL, 40, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-AC1D (Phụ kiện: Chuột, phím). Lý do: '),
(140, 78, NULL, 'import', '2026-10-22 09:31:14', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 78: PC  '),
(141, 78, 63, 'allocate', '2026-10-22 09:31:54', 1, NULL, 41, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-C56D (Phụ kiện: Chuột, phím). Lý do: '),
(144, 80, NULL, 'import', '2026-10-22 09:34:19', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 80: CASE  CASE MÁY CHỦ'),
(145, 80, 65, 'allocate', '2026-10-22 09:34:28', 1, NULL, 43, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-763A. Lý do: '),
(146, 81, NULL, 'import', '2026-10-22 09:35:33', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 81: PC  '),
(147, 81, 66, 'allocate', '2026-10-22 09:35:51', 1, NULL, 44, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-8FCC (Phụ kiện: Chuột, phím). Lý do: '),
(148, 82, NULL, 'import', '2026-10-22 09:36:51', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 82: PC  '),
(149, 82, 67, 'allocate', '2026-10-22 09:37:19', 1, NULL, 45, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-D4A3 (Phụ kiện: Chuột, phím). Lý do: '),
(150, 83, NULL, 'import', '2026-10-22 09:39:26', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 83: PC  '),
(151, 83, 68, 'allocate', '2026-10-22 09:39:38', 1, NULL, 46, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-76FB (Phụ kiện: Chuột, phím). Lý do: '),
(152, 84, NULL, 'import', '2026-10-22 09:40:30', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 84: PC  '),
(153, 84, 69, 'allocate', '2026-10-22 09:40:43', 1, NULL, 47, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-FB44 (Phụ kiện: Chuột, phím). Lý do: '),
(154, 85, NULL, 'import', '2026-10-22 09:41:29', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 85: PC  '),
(155, 85, 70, 'allocate', '2026-10-22 09:41:46', 1, NULL, 48, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-B391 (Phụ kiện: Chuột, phím). Lý do: '),
(156, 86, NULL, 'import', '2026-10-22 09:42:55', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 86: PC  '),
(157, 86, 71, 'allocate', '2026-10-22 09:43:30', 1, NULL, 42, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-E2AF (Phụ kiện: Chuột, phím). Lý do: '),
(158, 87, NULL, 'import', '2026-10-22 09:45:10', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 87: Laptop  Asus Expert P1503CVA _P1503CVA-i5SE16-50W Xám'),
(159, 87, 72, 'allocate', '2026-10-22 09:45:45', 1, NULL, 49, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-AB01 (Phụ kiện: chuột). Lý do: '),
(160, 88, NULL, 'import', '2026-10-22 09:47:07', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 88: PC  '),
(161, 88, 73, 'allocate', '2026-10-22 09:47:34', 1, NULL, 50, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-FB9C (Phụ kiện: Chuột, phím). Lý do: '),
(162, 89, NULL, 'import', '2026-10-22 09:47:55', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 89: LK  03 ổ cứng SSD Transcend 1TB'),
(163, 89, 74, 'allocate', '2026-10-22 09:48:36', 1, NULL, 34, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-3D6F. Lý do: '),
(164, 90, NULL, 'import', '2026-10-22 09:49:22', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 90: PC  '),
(165, 90, 75, 'allocate', '2026-10-22 09:49:34', 1, NULL, 52, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-6212. Lý do: '),
(166, 91, NULL, 'import', '2026-10-22 09:50:15', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 91: PC  '),
(167, 91, 76, 'allocate', '2026-10-22 09:50:28', 1, NULL, 53, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-A5EB. Lý do: '),
(168, 92, NULL, 'import', '2026-10-22 09:51:19', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 92: PC  '),
(169, 92, 77, 'allocate', '2026-10-22 09:51:31', 1, NULL, 54, NULL, 8, 'Cấp phát thiết bị theo phiếu PX-20261022-AF16. Lý do: '),
(172, 94, NULL, 'import', '2026-10-22 09:54:52', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 94: PC  '),
(173, 94, 79, 'allocate', '2026-10-22 09:55:06', 1, NULL, 75, NULL, 11, 'Cấp phát thiết bị theo phiếu PX-20261022-1FC8 (Phụ kiện: Chuột, phím). Lý do: '),
(174, 95, NULL, 'import', '2026-10-22 09:55:45', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 95: PC  '),
(175, 95, 80, 'allocate', '2026-10-22 09:55:58', 1, NULL, 76, NULL, 11, 'Cấp phát thiết bị theo phiếu PX-20261022-CE85 (Phụ kiện: Chuột, phím). Lý do: '),
(176, 96, NULL, 'import', '2026-10-22 09:56:32', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 96: PC  '),
(177, 96, 81, 'allocate', '2026-10-22 09:57:07', 1, NULL, 77, NULL, 11, 'Cấp phát thiết bị theo phiếu PX-20261022-9CEA. Lý do: '),
(178, 97, NULL, 'import', '2026-10-22 09:57:59', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 97: PC  '),
(179, 97, 82, 'allocate', '2026-10-22 09:58:15', 1, NULL, 78, NULL, 11, 'Cấp phát thiết bị theo phiếu PX-20261022-8B42 (Phụ kiện: Chuột, phím). Lý do: '),
(180, 98, NULL, 'import', '2026-10-22 09:59:02', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 98: PC  '),
(181, 98, 83, 'allocate', '2026-10-22 09:59:15', 1, NULL, 79, NULL, 11, 'Cấp phát thiết bị theo phiếu PX-20261022-CAB1 (Phụ kiện: Chuột, phím). Lý do: '),
(182, 99, NULL, 'import', '2026-10-22 09:59:59', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 99: Laptop  Lenovo'),
(183, 99, 84, 'allocate', '2026-10-22 10:00:12', 1, NULL, 80, NULL, 11, 'Cấp phát thiết bị theo phiếu PX-20261022-E3F9 (Phụ kiện: chuột). Lý do: '),
(184, 100, NULL, 'import', '2026-10-22 10:00:58', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 100: Laptop  Dell 02MJVY,'),
(185, 100, 85, 'allocate', '2026-10-22 10:01:10', 1, NULL, 81, NULL, 11, 'Cấp phát thiết bị theo phiếu PX-20261022-7A8D (Phụ kiện: Chuột, phím). Lý do: '),
(188, 102, NULL, 'import', '2026-10-22 10:03:06', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 102: PC  '),
(189, 102, 87, 'allocate', '2026-10-22 10:03:16', 1, NULL, 83, NULL, 12, 'Cấp phát thiết bị theo phiếu PX-20261022-B525 (Phụ kiện: Chuột, phím). Lý do: '),
(190, 103, NULL, 'import', '2026-10-22 10:03:58', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 103: Laptop  '),
(191, 103, 88, 'allocate', '2026-10-22 10:04:14', 1, NULL, 84, NULL, 12, 'Cấp phát thiết bị theo phiếu PX-20261022-8764 (Phụ kiện: chuột). Lý do: '),
(194, 105, NULL, 'import', '2026-10-22 10:06:21', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 105: PC  '),
(195, 105, 90, 'allocate', '2026-10-22 10:06:37', 1, NULL, 86, NULL, 12, 'Cấp phát thiết bị theo phiếu PX-20261022-1CF4 (Phụ kiện: Chuột, phím). Lý do: '),
(196, 106, NULL, 'import', '2026-10-22 10:08:43', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 106: PC  '),
(197, 106, 91, 'allocate', '2026-10-22 10:08:58', 1, NULL, 87, NULL, 12, 'Cấp phát thiết bị theo phiếu PX-20261022-0828 (Phụ kiện: Chuột, phím). Lý do: '),
(198, 107, NULL, 'import', '2026-10-22 10:10:12', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 107: PC  '),
(199, 107, 92, 'allocate', '2026-10-22 10:10:20', 1, NULL, 88, NULL, 12, 'Cấp phát thiết bị theo phiếu PX-20261022-186F (Phụ kiện: Chuột, phím). Lý do: '),
(200, 108, NULL, 'import', '2026-10-22 10:11:51', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 108: PC  '),
(201, 108, 93, 'allocate', '2026-10-22 10:12:00', 1, NULL, 89, NULL, 12, 'Cấp phát thiết bị theo phiếu PX-20261022-1B72 (Phụ kiện: Chuột, phím). Lý do: '),
(202, 109, NULL, 'import', '2026-10-22 10:12:49', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 109: PC  '),
(203, 109, 94, 'allocate', '2026-10-22 10:13:03', 1, NULL, 90, NULL, 12, 'Cấp phát thiết bị theo phiếu PX-20261022-E872 (Phụ kiện: Chuột, phím). Lý do: '),
(204, 110, NULL, 'import', '2026-10-22 10:14:45', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 110: PC  '),
(205, 110, 95, 'allocate', '2026-10-22 10:14:55', 1, NULL, 55, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-4BAF (Phụ kiện: Chuột, phím). Lý do: '),
(206, 111, NULL, 'import', '2026-10-22 10:15:37', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 111: PC  '),
(207, 111, 96, 'allocate', '2026-10-22 10:16:15', 1, NULL, 56, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-B579 (Phụ kiện: Chuột, phím). Lý do: '),
(208, 112, NULL, 'import', '2026-10-22 10:19:48', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 112: PC  '),
(209, 112, 97, 'allocate', '2026-10-22 10:20:03', 1, NULL, 57, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-CCC8 (Phụ kiện: Chuột, phím). Lý do: '),
(210, 113, NULL, 'import', '2026-10-22 10:39:49', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 113: PC  '),
(211, 113, 98, 'allocate', '2026-10-22 10:40:04', 1, NULL, 58, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-04A7 (Phụ kiện: Chuột, phím). Lý do: '),
(212, 114, NULL, 'import', '2026-10-22 10:41:03', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 114: PC  '),
(213, 114, 99, 'allocate', '2026-10-22 10:41:23', 1, NULL, 59, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-8246 (Phụ kiện: Chuột, phím). Lý do: '),
(214, 115, NULL, 'import', '2026-10-22 10:42:06', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 115: PC  '),
(215, 115, 100, 'allocate', '2026-10-22 10:42:25', 1, NULL, 60, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-266D (Phụ kiện: Chuột, phím). Lý do: '),
(216, 116, NULL, 'import', '2026-10-22 10:43:07', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 116: PC  '),
(217, 116, 101, 'allocate', '2026-10-22 10:43:21', 1, NULL, 61, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-5E55 (Phụ kiện: Chuột, phím). Lý do: '),
(218, 117, NULL, 'import', '2026-10-22 10:43:58', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 117: PC  '),
(219, 117, 102, 'allocate', '2026-10-22 10:44:13', 1, NULL, 62, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-5543 (Phụ kiện: Chuột, phím). Lý do: '),
(220, 118, NULL, 'import', '2026-10-22 10:48:55', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 118: PC  CB xin dùng máy cá nhân - gửi đề xuất qua mail'),
(221, 118, 103, 'allocate', '2026-10-22 10:49:04', 1, NULL, 63, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-2598 (Phụ kiện: Chuột, phím). Lý do: '),
(224, 120, NULL, 'import', '2026-10-22 10:53:00', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 120: PC  '),
(225, 120, 105, 'allocate', '2026-10-22 10:53:18', 1, NULL, 65, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-C83F (Phụ kiện: Chuột, phím). Lý do: '),
(226, 121, NULL, 'import', '2026-10-22 10:54:06', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 121: PC  '),
(227, 121, 106, 'allocate', '2026-10-22 10:54:28', 1, NULL, 66, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-B9F9 (Phụ kiện: Chuột, phím). Lý do: '),
(228, 122, NULL, 'import', '2026-10-22 10:55:06', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 122: PC  '),
(229, 122, 107, 'allocate', '2026-10-22 10:55:55', 1, NULL, 67, NULL, 9, 'Cấp phát thiết bị theo phiếu PX-20261022-376F (Phụ kiện: Chuột, phím). Lý do: '),
(230, 123, NULL, 'import', '2026-10-23 10:46:50', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 123: PC  '),
(231, 124, NULL, 'import', '2026-10-23 10:47:46', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 124: PC  '),
(232, 125, NULL, 'import', '2026-10-23 10:48:36', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 125: PC  '),
(233, 126, NULL, 'import', '2026-10-23 10:49:48', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 126: PC  '),
(234, 127, NULL, 'import', '2026-10-23 10:50:36', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 127: PC  '),
(235, 128, NULL, 'import', '2026-10-23 10:51:22', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 128: PC  '),
(236, 129, NULL, 'import', '2026-10-23 10:51:53', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 129: PC  '),
(237, 130, NULL, 'import', '2026-10-23 10:52:44', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 130: PC  '),
(238, 131, NULL, 'import', '2026-10-23 10:53:36', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 131: PC  '),
(239, 132, NULL, 'import', '2026-10-23 10:54:38', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 132: PC  '),
(240, 133, NULL, 'import', '2026-10-23 10:57:19', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 133: PC  '),
(241, 134, NULL, 'import', '2026-10-23 10:58:19', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 134: PC  '),
(242, 135, NULL, 'import', '2026-10-23 10:58:54', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 135: PC  '),
(243, 136, NULL, 'import', '2026-10-23 11:00:30', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 136: PC  '),
(244, 137, NULL, 'import', '2026-10-23 11:01:03', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 137: PC  '),
(245, 138, NULL, 'import', '2026-10-23 11:02:56', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 138: PC  '),
(246, 139, NULL, 'import', '2026-10-23 11:03:11', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 139: PC  Không có ổ cứng'),
(247, 44, 108, 'revoke', '2026-10-23 11:14:20', 1, 71, NULL, 10, NULL, 'Thu hồi thiết bị theo phiếu PTH-20261023-A8BB. Tình trạng lúc nhận: Tốt (trả về kho) | Phụ kiện nhận lại: Chuột, phím, màn. Ghi chú: '),
(248, 140, NULL, 'import', '2026-10-23 16:23:16', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 140: Laptop HP HP - PAVILION'),
(249, 141, NULL, 'import', '2026-10-23 16:56:59', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 141: Laptop  HP - PROBOOK'),
(250, 140, 109, 'allocate', '2026-10-26 09:02:55', 1, NULL, 91, NULL, 13, 'Cấp phát thiết bị theo phiếu PX-20261026-B698 (Phụ kiện: chuột). Lý do: Cấp laptop'),
(251, 142, NULL, 'import', '2026-10-26 15:29:26', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 142: MH  HKC MB20S1 - 20inch'),
(252, 143, NULL, 'import', '2026-10-26 15:30:59', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 143: MH AOC E2270SWN - 21.5 INCH'),
(253, 144, NULL, 'import', '2026-10-26 15:31:39', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 144: MH DELL E2225H-21.5'),
(254, 145, NULL, 'import', '2026-10-26 15:32:25', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 145: MH SPING SGP195S'),
(255, 146, NULL, 'import', '2026-10-26 15:33:47', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 146: MH SAMSUNG S22C310EAE - 22INCH'),
(256, 147, NULL, 'import', '2026-10-26 15:34:09', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 147: MH SAMSUNG S20D300NH - 20INCH'),
(257, 148, NULL, 'import', '2026-10-26 15:34:29', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 148: MH ACER V226HQL - 21.5 INCH'),
(258, 149, NULL, 'import', '2026-10-26 15:34:57', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 149: MH DAHUA DHI-LM24-B221 - 21.5 INCH'),
(259, 150, NULL, 'import', '2026-10-26 15:35:16', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 150: MH DELL E2225HSM - 21.5 INCH'),
(263, 47, 113, 'transfer', '2026-10-27 16:23:54', 1, 91, 83, 13, 12, 'Chuyển giao thiết bị theo phiếu PCG-20261027-6949. Lý do: '),
(264, 151, NULL, 'import', '2026-10-28 09:03:15', 1, NULL, NULL, NULL, NULL, 'Nhập kho thiết bị mới STT 151: PC  '),
(265, 151, 114, 'allocate', '2026-10-28 09:04:28', 1, NULL, 25, NULL, 7, 'Cấp phát thiết bị theo phiếu PX-20261028-7B3A. Lý do: ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `device_monitors`
--

CREATE TABLE `device_monitors` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `monitor_no` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `size` decimal(4,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `device_monitors`
--

INSERT INTO `device_monitors` (`id`, `device_id`, `monitor_no`, `brand`, `model`, `size`) VALUES
(16, 16, 1, 'Duaha', NULL, 24.0),
(19, 17, 1, 'Duaha', NULL, 24.0),
(20, 19, 1, 'samsung', NULL, 21.0),
(21, 20, 1, 'Asus', NULL, 14.0),
(22, 21, 1, 'Skyview', NULL, 21.0),
(23, 22, 1, 'Sky view', NULL, 21.0),
(24, 23, 1, 'Sing PC', NULL, 1.0),
(25, 24, 1, 'SingPC LED', NULL, 21.0),
(26, 25, 1, 'LCD_DELL', NULL, 21.5),
(27, 26, 1, 'LCD_DELL', NULL, 21.5),
(28, 27, 1, 'Acer EK220Q', NULL, 21.5),
(29, 28, 1, 'Acer EK220Q', NULL, 21.0),
(30, 29, 1, 'Samsung', NULL, 21.0),
(31, 30, 1, 'ASUS VY249HF-R', NULL, 23.8),
(32, 31, 1, 'AOC', NULL, 18.5),
(33, 32, 1, 'Samsung', NULL, 18.5),
(34, 35, 1, 'Acer', NULL, 21.5),
(35, 34, 1, 'ASUS VY249HF-R', NULL, 23.8),
(36, 33, 1, 'ASUS VY249HF-R', NULL, 23.8),
(37, 36, 1, 'LCD_DELL', NULL, 21.5),
(38, 37, 1, 'LCD_DELL', NULL, 21.5),
(39, 38, 1, 'Asus', NULL, 23.7),
(41, 41, 1, 'FHD-MSI', NULL, 23.6),
(42, 42, 1, 'Dell', NULL, 21.5),
(43, 43, 1, 'FHD-MSI', NULL, 23.4),
(45, 48, 1, 'LG 29WQ600-W/kèm cáp HDMI', NULL, 29.0),
(46, 49, 1, 'Dell', NULL, 27.0),
(47, 49, 2, 'Dell', NULL, 27.0),
(48, 50, 1, 'Dell', NULL, 23.8),
(49, 51, 1, 'SGP200S SingPC', NULL, 20.0),
(50, 52, 1, 'Philips', NULL, 19.0),
(51, 53, 1, 'Huntkey', NULL, 23.0),
(52, 54, 1, 'AOC', NULL, 19.0),
(53, 55, 1, 'Dell', NULL, 21.0),
(54, 58, 1, 'Sing PC', NULL, 21.5),
(55, 59, 1, 'Dell', NULL, 22.0),
(56, 60, 1, 'Skyview', NULL, 22.0),
(57, 61, 1, 'Samsung', NULL, 24.0),
(58, 63, 1, 'Dell', NULL, 21.0),
(59, 65, 1, 'Samsung', NULL, 19.5),
(60, 66, 1, 'SingPC', NULL, 19.0),
(61, 67, 1, 'Dell', NULL, 20.0),
(62, 68, 1, 'Skyview', NULL, 19.0),
(63, 69, 1, 'SingPC', NULL, 19.0),
(64, 70, 1, 'HKC', NULL, 19.5),
(65, 71, 1, 'SingPC', NULL, 19.5),
(66, 72, 1, 'Skyview', NULL, 18.5),
(67, 73, 1, 'dahua', NULL, 23.8),
(68, 74, 1, 'SingPC', NULL, 19.0),
(70, 75, 1, 'Dell', NULL, 21.0),
(71, 77, 1, 'Dell', NULL, 21.0),
(72, 78, 1, 'Samsung', NULL, 19.0),
(73, 81, 1, 'LCD_DELL', NULL, 21.5),
(75, 82, 1, 'LCD_DELL', NULL, 21.5),
(76, 83, 1, 'LCD_DELL', NULL, 21.5),
(77, 84, 1, 'SingPC', NULL, 19.5),
(78, 85, 1, 'Samsung', NULL, 21.5),
(79, 86, 1, 'LCD- Dell', NULL, 21.5),
(80, 88, 1, 'Gigabyte GS24F14_EK1', NULL, 24.0),
(81, 88, 2, 'Gigabyte GS24F14_EK1', NULL, 24.0),
(82, 90, 1, 'LCD_DELL', NULL, 21.5),
(83, 91, 1, 'k', NULL, 19.5),
(84, 92, 1, 'AOC', NULL, 19.5),
(86, 94, 1, 'Duaha', NULL, 24.0),
(87, 95, 1, 'Aoc', NULL, 18.5),
(88, 96, 1, 'SingPC LED', NULL, 21.5),
(89, 97, 1, 'Skyview', NULL, 19.0),
(90, 98, 1, 'SingPC', NULL, 19.0),
(91, 102, 1, 'Samsung', NULL, 20.0),
(93, 105, 1, 'Dell', NULL, 19.0),
(94, 106, 1, 'HKC', NULL, 19.0),
(95, 107, 1, 'SingPC', NULL, 19.0),
(96, 108, 1, 'Skyview', NULL, 1.0),
(97, 109, 1, 'Provision', NULL, 1.0),
(98, 110, 1, 'Acer', NULL, 19.0),
(99, 111, 1, 'Sing PC', NULL, 18.5),
(100, 112, 1, 'Starview', NULL, 21.0),
(101, 113, 1, 'duhua', NULL, 22.0),
(102, 114, 1, 'HP', NULL, 18.5),
(103, 115, 1, 'LCD_DELL', NULL, 21.5),
(104, 116, 1, 'Asus', NULL, 21.5),
(106, 117, 1, 'LCD_DELL', NULL, 21.5),
(108, 120, 1, 'LCD_DELL', NULL, 21.5),
(109, 121, 1, 'LCD_DELL', NULL, 21.5),
(110, 122, 1, 'AOC', NULL, 1.0),
(112, 151, 1, 'Samsung', NULL, 24.0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `device_specs`
--

CREATE TABLE `device_specs` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `cpu` varchar(100) DEFAULT NULL COMMENT 'B? vi x? l² CPU',
  `mainboard` varchar(100) DEFAULT NULL COMMENT 'Chỉ áp dụng cho PC',
  `ram_gb` int(11) DEFAULT NULL COMMENT 'Dung lu?ng RAM tÝnh b?ng GB',
  `ram_type` varchar(50) DEFAULT NULL COMMENT 'Lo?i RAM: DDR4, DDR5...',
  `ssd_capacity` varchar(50) DEFAULT NULL COMMENT 'Dung lượng SSD: 256GB, 512GB...',
  `hdd_capacity` varchar(50) DEFAULT NULL COMMENT 'Dung lượng HDD: 1TB, Không...',
  `gpu` varchar(100) DEFAULT NULL COMMENT 'Card đồ họa rời hoặc Onboard',
  `screen_size` varchar(50) DEFAULT NULL COMMENT 'Kích thước màn hình (cho Laptop)',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `device_specs`
--

INSERT INTO `device_specs` (`id`, `device_id`, `cpu`, `mainboard`, `ram_gb`, `ram_type`, `ssd_capacity`, `hdd_capacity`, `gpu`, `screen_size`, `notes`) VALUES
(1, 1, 'i3-1115G4', NULL, 8, 'DDR4 3200MHz', 'SSD - 256G', NULL, NULL, NULL, 'Chuột, lót chuột'),
(15, 16, 'Core i7 12700 F', 'H610MK', 16, 'LD4A-3200-16GSBB16 DDR4 ( (16GBx2)', 'Kingston SSD 1T', NULL, 'Asus DUAL-RTX3050-6G', NULL, 'Chuột fuhen L102, Key Fuhen L411'),
(16, 17, 'i5-12400', 'Asus H610M -K', 16, 'Ram kingston  16GB-DDR4-3200', 'SSD Kingmax PQ3480 PCIe Gen3x4 M.2 512GB', NULL, NULL, NULL, 'Chuột fuhen L102, Key Fuhen L411'),
(18, 19, 'i3 8100', 'H310M', NULL, 'DDR4 - 4gb bus 2400', 'SSD 240gb', NULL, NULL, NULL, 'phím, chuột'),
(19, 20, 'i5-13500H', 'OLED A1405VA-KM095W', NULL, '16Gb (2x8Gb)  DDR4 3200', 'Apacer 512Gb SSD', NULL, 'Intel UHD Graphics 610-128MB', NULL, 'phím, chuột'),
(20, 21, 'i7 12700 F', 'B760MD', NULL, 'LD4A-3200-16GSBB16 DDR4 ( (16GBx2= 32GB)', 'Kingston SSD 1T,', NULL, 'Nividia Geforce -RTX3050-6G', NULL, 'phím'),
(21, 22, 'i3-12100', 'Prime H610MK-D4', 8, 'DDR3 - 8GB', 'SSD 256GB kingmax apacer', NULL, 'Intel UHD Graphic 730 - 128MB', NULL, 'phím, chuột'),
(22, 23, 'i5-12400', 'H510M-K', 16, 'DDR4- 16GB', 'SSD - 512GB', NULL, 'Nvidia Gefoce 1650', NULL, 'phím, chuột'),
(23, 24, 'i3-10105', 'ASUS PRIME H510M-K', 16, 'Ram DDR4 16GB (8GB *2) bus 2666', NULL, 'HDD 1T', NULL, NULL, 'phím, chuột'),
(24, 25, 'i5-12400', 'Asus H610M -K', 8, '8G-DDR4', '512GB SSD Zeus', NULL, NULL, NULL, 'phím, chuột'),
(25, 26, 'i5-12400', 'Asus H610M -K', 8, '8G-DDR4', '512GB SSD Zeus', NULL, NULL, NULL, 'phím, chuột'),
(26, 27, 'i7 12700 F', 'ASUS TUF B760M-E DDR4', 32, 'KingsMax Fury Beast RGB (KF432C16BB12AK2/32) 32GB ', '512GB SSD Zeus', 'HDD Seagate BarraCuda 2TB', 'Nividia Geforce  RTX3050-6G', NULL, 'phím, chuột'),
(27, 28, 'Core i5 - 10400F', 'Prime H510-F', 16, 'DDR4 - 16GB (8GB*2)', 'Team SSD 512GB', NULL, 'GT710 - 1G', NULL, 'phím, chuột'),
(28, 29, 'i5 - 9400F', 'H310M', 16, 'DDR4 - 16GB', 'Apacer SSD 512GB', NULL, 'Nividia Geforce  GT710 - 1G', NULL, 'phím, chuột'),
(29, 30, 'i7 12700 F', 'ASUS TUF B760M-E DDR4', NULL, 'Kingston Fury Beast RGB (KF432C16BB12AK2/32) 32GB ', '512GB SSD Zeus', 'HDD Seagate BarraCuda 2TB', 'Nividia Geforce-RTX3050-6G', NULL, 'phím, chuột'),
(30, 31, 'i3-10105', 'H510M-K', 8, '8G-DDR4-3200', 'Giga SSD256G', 'HDD 1TB Toshiba', NULL, NULL, 'phím, chuột'),
(31, 32, 'i5 - 10400F', 'H510M DDR4', 16, '16G/3200', 'SSD512GB', NULL, 'Nividia Geforce   GT7102GD3HLP 2GB, case Xigmatek SX29MATX, nguồn X-Power III 350-300W', NULL, 'phím, chuột'),
(32, 33, 'i7 12700 F', 'ASUS TUF B760M-E DDR4', 32, 'Kingston Fury Beast RGB (KF432C16BB12AK2/32) 32GB', '512GB SSD Zeus', 'HDD Seagate BarraCuda 2TB', 'Nividia Geforce-RTX3050-6G', NULL, 'phím, chuột'),
(33, 34, 'i7 12700 F', 'ASUS TUF B760M-K DDR4', 32, 'Kingston Fury Beast RGB (KF432C16BB12AK2/32) 32GB', '512GB SSD Zeus', 'HDD Seagate BarraCuda 2TB', 'Nividia Geforce-RTX3050-6G', NULL, 'phím, chuột'),
(34, 35, 'i5 10400F', 'Asus Prime H510M -K', 16, 'Lexar DDR4- 16G/3200', 'SSD Lexar 512G SATAIII', 'HDD2TB', 'Nividia Geforce GT710-2GB', NULL, 'phím, chuột'),
(35, 36, 'i5-12400', 'Asus H610M -K DD4', 8, '8G-DDR4', '512GB SSD Zeus', NULL, 'Intel UHD730/128MB', NULL, 'phím, chuột'),
(36, 37, 'i5-12400', 'Asus H610M -K', 16, '16GB-DDR4-3200', '512GB SSD Zeus', NULL, 'Nividia Geforce-RTX3050-8G', NULL, NULL),
(37, 38, 'i5-12400', 'H610M -K', 16, '16G-DDR4-3200', 'SSD 512GB SSD Zeus', NULL, 'VGA Gigabyte 8GB GV- N3050WF2OC - V2-8GD)', NULL, 'phím, chuột'),
(38, 39, 'i5-10400F', 'Asus H510M -K', 16, 'DDR4-16 GB- 3200', 'SSD Lexar NQ100 -512 GB', 'HDD OOS2000G 2 TB', 'NVIDIA Geforce GT 710- 2 GB - GDDR5', NULL, 'phím, chuột'),
(39, 40, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 41, 'i5-10400', 'Prime H510M-K', 8, 'DDR4 - 8GB', 'SSD - 512GB', NULL, 'Intel UHD Graphics 630', NULL, 'phím SingPC, chuột'),
(41, 42, 'i5-10400F', 'Giga B560M', 16, 'kington-16G/DDR4-3200', 'SSD   M2  256 GB', 'PCIe-HDD WD 1T GB', 'Nividia VGA GAINWARD GTX 1050Ti 4GB GDDR5 128 bít DVI HDMI', NULL, 'phím, chuột'),
(42, 43, 'i5-10400F', 'Giga B560M', 16, 'kington-16G/DDR4-3200', 'SSD  M2  256 GB', 'PCIe-HDD 1TB', NULL, NULL, 'phím, chuột'),
(43, 44, 'i3 - 9100', 'Dell OR6JMP', 8, 'DDR4 - 8GB', 'SSD 128GB', 'HDD 1TB ST', 'Intel UHD Graphics 630-128MB', NULL, 'phím, chuột'),
(44, 45, 'i3-7020', NULL, 4, '4GB', 'Kingston SSD256', NULL, 'Acer Intel HD Graphics 620', 'Acer laptop', 'phím, chuột'),
(45, 46, 'Go14 E1404FA - NK177W (Ryzen 5 7520U', NULL, 16, NULL, '512GB SSD', NULL, NULL, 'Asus laptop', 'chuột'),
(46, 47, 'Core i5 1335U', NULL, 8, '8GB Ram', 'SSD512GB', NULL, 'Intel Graphics 128', NULL, NULL),
(47, 48, 'i7 14700F', 'VT_GIGABYTE_GA B760M D2H DDR4', 16, 'Kingston Fury 32GB D4-3200U C16 Beast Black (KF432', '1000GB Kingston SSD NV3 M2 2280 NVMe ( SNV3S/1000G', NULL, 'Nividia geforce RTX3060-GDDR5X-8GB', NULL, 'phím, chuột'),
(48, 49, 'i9 - 9820', 'Giga Byte X299', 32, 'DDR4 - 32GB', 'SSD 250GB', 'HDD 4T x 2', 'Nividia geforce RTX1080-GDDR6-12GB', NULL, 'phím, chuột'),
(49, 50, 'i7 - 10700F', 'Giga Byte B560M', 32, 'DDR4 - 32GB', 'NVME SSD - 256GB', 'HDD1TB WDC', 'Nividia geforce GTX1660-GDDR6-6GB', NULL, 'phím, chuột'),
(50, 51, 'Bộ Xl intel pentium G4440 3M bộ nhớ đệm, 3,30GHz', 'Biostar B250ET2', NULL, 'Ram kingmax DDR4-2666, 3M bộ nhớ đệm, 3,30 GHz', 'SSD120GB', 'HDD500GB', 'Intel UHD Grpahics 510-128MB', NULL, 'phím, chuột'),
(51, 52, 'Intel core i5-12400 2.5Ghz', 'Asus Prime H610MK D4', 12, '\"12GB DDR4-3200  (1 thanh 8GB + 1 thanh 4GB)\"', 'SSD SSTC 512GB SATA III 2.5 inch', NULL, NULL, NULL, 'phím, chuột'),
(52, 53, 'i3-10105', 'Asrock H510M', 12, '12GB', 'SSD M2-250GB', NULL, NULL, NULL, 'phím, chuột'),
(53, 54, 'Pentrium G4400', 'Bio H110 MHPro', 8, '8GB', '240GB Kingston', NULL, NULL, NULL, 'phím, chuột'),
(54, 55, 'i3-8100', 'MSI H310M', 8, '8GB (nâng cấp Ram DDR4-buss 2666 20/03/26)', '240GB Kingston', NULL, NULL, NULL, 'phím, chuột'),
(55, 56, NULL, '15S - 1011U', 16, 'DDR4 - 16GB', '512GB SSD', NULL, 'Nvidia GeForce RTX 2050 4GB GDDR6', 'HP 15.6 inch', 'chuột'),
(56, 57, 'i5-1135G7', NULL, 8, '8GB Ram', '256GB SSD', NULL, NULL, NULL, NULL),
(57, 58, 'i3-12100', 'Asus Prime H610M -K', 8, 'Ram kingston  8G-DDR4-3200', 'SSD  Kingmax M2  256 GB  PCIe', NULL, 'Intel UHD Graphics 730-128MB', NULL, 'phím, chuột'),
(58, 59, 'i5-10400', 'AsusTek Prime H510MK-R2,0', 8, 'DDR4, 8GB', 'SSD Lexar 256GB', NULL, 'intel UHD 630 - 128MB', NULL, 'phím, chuột'),
(59, 60, 'I5-2400S 2.5Ghz', NULL, 16, 'NC Ram 8G x2 (ngày 13.06.25)', 'SSD256GB (nâng cấp 05/09/24)', NULL, NULL, NULL, 'phím, chuột'),
(60, 61, 'i3 - 9100', 'H310M', 8, 'DDR4 - 8GB', '\"128P3 - Toshiba 1TB \"', NULL, NULL, NULL, 'phím, chuột'),
(61, 62, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 63, 'i3 - 9100', 'Asrock H310CM', 8, 'DDR4 - 8GB', 'Lexar SSD 128GB', 'HDD 1TB Seagate', 'intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(63, 64, 'i3 - 8100, 3.6GHz, i3 - 9100', 'H310M-E', 8, 'DDR4 - 8GB', 'Kingmax SSD 240GB', NULL, 'intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(64, 65, 'CPU Intel Pentium G4560 (3.5GHz)', 'Mainboard HP 82F2K', 8, '\"RAM desktop KINGMAX 8GB DDR4 2400MHz \"', 'SSD Lexar 128Gb Sata', 'HDD WOC1TB', 'Graphics :HD 610 -128MB', NULL, 'phím, chuột'),
(65, 66, 'i3 - 3240', 'H61M-E', 8, 'DDR3 - 8GB', 'Apacer SSD 120GB)', NULL, 'intel UHD Graphic 630-240MB', NULL, 'phím, chuột'),
(66, 67, 'i3 - 8100', NULL, 8, 'DDR4 - 8GB', 'SSD 128GB', 'HDD 1TB', 'intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(67, 68, 'Celeron G1630', 'H61MGV3', 4, 'DDR3 - 4GB', 'SSD 120GB', 'HDD 250GB', NULL, NULL, NULL),
(68, 69, 'G4600', 'B250M HC', 4, 'DDR4 - 4GB', 'Apacer SSD 240GB', NULL, 'intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(69, 70, 'i5-10400', 'H510 M - ProE', 16, '16GB - DDR4', NULL, 'HDD 1TB Seagate', NULL, NULL, 'phím, chuột'),
(70, 71, 'i3-8100', 'Mainboard Asus PRIME H310M-K', 8, '\"RAM 8GB-DDR4 \"', 'Kingston SSD240', 'HDD1TB Toshiba', 'intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(71, 72, 'i3-8100', 'H310-Pro', 4, 'RAM 4G-DDR4', '\"Kingston 240Gb \"', NULL, 'intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(72, 73, 'i5-10400', 'ASUS H510M-F', 8, 'RAM 8GB-DDR4', 'Giga SSD 120BG', 'HDD 1T Seagate', 'intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(73, 74, 'Intel celeron G5905', 'ASUS H510-K', 8, '\"RAM 8GB \"', 'SSD256GB', NULL, 'intel UHD Graphic 610-128MB', NULL, 'phím, chuột'),
(74, 75, 'G4400', 'Asus Ex-H110M-K', 4, 'RAM 4GB-DDR4', '\"SSD Kingmax 240GB  \"', NULL, 'intel UHD Graphic 510-128MB', NULL, 'phím, chuột'),
(75, 76, 'I5-3470S', 'Dell optimex 7010', 8, 'RAM 8GB', NULL, NULL, 'intel UHD Graphic 610-32MB', NULL, 'phím, chuột'),
(76, 77, 'CPU Intel Core i3-7100 3.9 GHz / 3MB', 'H110M-K', NULL, 'Ram kingmax--8G/DDR4-2666', 'SS120GB+SSD512GB (nâng cấp 14/09/2024)', NULL, NULL, NULL, 'phím, chuột'),
(77, 78, 'G4400', 'BioStar 250 ET2', 8, 'RAM 8GB-DDR4', 'WD240GB Toshiba', 'HHD500', 'intel prenium G440', NULL, 'phím, chuột'),
(79, 80, 'i5 10400', 'Prime 510-F', 16, '16GB', 'SSD512GB', 'HDD1TB', 'intel UHD Graphic 730-128MB', NULL, NULL),
(80, 81, 'Core i5-12400', 'Prime H610M -K', 8, '8G-DDR4', 'Kingmax 512GB', 'HDD1TB', 'intel UHD Graphic 730-8GB', NULL, 'phím, chuột'),
(81, 82, 'Core i5-12400', 'Asus H610M -K', 8, '8G-DDR4', '512GB SSD Zeus', NULL, 'intel UHD Graphic 730-8GB', NULL, 'phím, chuột'),
(82, 83, 'Intel Core i3-12100', 'Asus H610M -K', 8, '8G-DDR4-3200', 'SSD256 GB  PCIe', NULL, 'intel UHD Graphic 730-128GB', NULL, 'phím, chuột'),
(83, 84, 'intel premium G440', 'AsusTek-H110M-K', 8, '8G-DDR4', 'Kingmax 240GB', 'HDD WDC-500GB', 'intel UHD Graphic 510-128GB', NULL, 'phím, chuột'),
(84, 85, 'Intel Core i3-12100', 'Asus H610M -K', 8, 'Ram kingston  8G-DDR4-3200', 'SSD  Kingmax M2  256 GB  PCIe', NULL, NULL, NULL, 'phím, chuột'),
(85, 86, 'Core i3 - 12100F', 'Asus Prime H610M-K D4', 8, 'DDR4-8GB', 'SSD256GB', NULL, 'card on: Intel UHD Graphic 630-128MB', NULL, 'phím, chuột'),
(86, 87, 'i5-13500H', NULL, 16, '16GB', '512GB', NULL, NULL, 'Asus 15.6', 'chuột'),
(87, 88, 'Intel Core i5 14400 2.50GHz 20MB 1700', 'Asus Prime H610M-K R2.0', 16, 'SSTC 16GB DDR4 3200Mhz UDIMM,', 'SSD_ Kingmax PQ3480 512GB Lite PCIe Gen3x4 M.2 SSD', NULL, NULL, NULL, NULL),
(88, 89, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 90, 'Core i5-12400', 'Asus H610M -K', 8, '8G-DDR4', '512GB SSD Zeus', NULL, 'intel UHD Graphic 730-8GB', NULL, 'phím, chuột'),
(90, 91, 'Pen G4600', 'BioStar B250MHC', 4, 'Ram 4GB', 'SSD apacer 240gb', NULL, NULL, NULL, NULL),
(91, 92, 'Intel core i3-8100', 'Micro-Star H310 Mpro', 4, '4G-DDR4-2400', 'Kingston SSD240GB', NULL, 'intel UHD Graphics 630-128MB', NULL, NULL),
(93, 94, 'Core i3 - 10105', 'H510M DDR4', 8, 'Ram 8G/3200', 'SSD256FB', NULL, NULL, NULL, 'phím, chuột'),
(94, 95, 'pentium g4400', 'H110MH', 8, 'DDR3 - 4gb x2 (8gb)', 'gigabyte SSD 120GB', NULL, NULL, NULL, 'phím, chuột'),
(95, 96, 'Intel core i3 10105', 'Asus Prime H510M-K', 8, '8GB - DDR4', 'SSD - WD Green - 240 GB', NULL, '\"Card on : Intel UHD Graphics 630 - 128 MB Card rời : Nvidia Geforce GTX 1050 ti - GDDR5 - 4GB\"', NULL, NULL),
(96, 97, 'Core i3 - 9100F', 'Gyabyte H310M', 8, 'DDR4 - 8GB', 'SSD 120GB', 'HDD 1TB', 'Nividia Geforce  GT710-1G R DDR5', NULL, 'phím, chuột'),
(97, 98, 'Intel corei3 8100', 'asustek prime h310M-E R2.0', 4, 'DDR4 4 GB-2666', 'SSD Kingmax 240 GB', NULL, 'Card on: Intel UHD Graphics 630', NULL, 'phím, chuột'),
(98, 99, 'Core i5 -8250U', 'Lenovo', 4, 'DDR4-4GB-2400MHz', NULL, NULL, 'Card on : Intel UHD Graphics 620 - 128 MB', 'Lenovo', NULL),
(99, 100, 'i7 - 8750H', NULL, 16, 'DDR4 - 16GB', 'SSD 512GB', NULL, NULL, '15.6 inch', 'chuột'),
(101, 102, 'Core i3-12100', 'Main H610M-K', 16, 'RAM 16GB', 'Micron SSD 256GB', NULL, 'Intel UHD Graphics 730-128MB', NULL, 'phím, chuột'),
(102, 103, 'I7-1165G7', 'Asus vivobook S15', 16, 'Ram 16GB', '500GB', NULL, 'Nvidra Mx350', '15.6 inch', 'chuột'),
(104, 105, 'Core i3-7100', 'Asus H110M-CS', 8, 'Ram DDR4 8GB bus 2666', 'P3-128GB, SSD', NULL, 'Intel UHD Graphics 630-128MB', NULL, 'phím, chuột'),
(105, 106, 'Core i3-10105', 'AsUS H510', 8, 'Ram DDR4 8GB bus 2666', 'SSD 240Gb WD', 'Seagate 1T', 'Intel UHD Graphics 630-128MB', NULL, NULL),
(106, 107, 'i5 - 7400', 'HP - 82F2', 8, '8G/DDR4-2666', 'Nâng cấp SSD256 (26.12.24)', 'HDD 1T', 'Intel UHD Graphics 630-128MB', NULL, 'phím, chuột'),
(107, 108, 'Intel Pentium G4400 3.3GHz', 'BiosStar B250MHC', 4, 'RAM 4GB', 'SSD Apacer 240GB', NULL, 'Intel UHD Graphics 510-128MB', NULL, NULL),
(108, 109, 'i3 - 9100F', 'H310M', 8, 'DDR4 - 8GB', 'SSD 128GB', 'HDD 1T', 'Card dời: Nvidia Geforce GT710-1GB-GDDR5', NULL, 'phím, chuột'),
(109, 110, 'i3 - 8100', 'MicroStar H310M Pro', 4, 'DDR4 - 4GB', 'SSD 120GB', NULL, 'Intel UHD Graphics 630-128MB', NULL, NULL),
(110, 111, 'i3 - 8100', 'H310M DS2', 8, 'DDR4 - 8GB', 'Kingston SSD 240GB', NULL, 'Intel UHD Graphics 630-128MB', NULL, 'phím, chuột'),
(111, 112, 'CPU Intel Core i5-10400F', 'H510M-F', 16, 'Ram DDR4 16GB Bus 3200', 'SSD Gega 120GB', 'HDD 1T', 'VGA Gigabyte 4GB GTX1650', NULL, NULL),
(112, 113, 'i5-10400', 'H510 M - K', 8, 'Ram 8G /2666', 'SSD 240Gb WD', NULL, 'Intel UHD Graphics 630-128MB', NULL, NULL),
(113, 114, 'i3 - 3240', 'H61M-P20', 6, '6GB', 'SSD 256GB', NULL, 'Microsoft basic display 32MB', NULL, NULL),
(114, 115, 'Core i5-12400', 'Asus H610M -K', 8, '8G-DDR4', '512GB SSD Zeus', NULL, 'Intel UHD Graphics 730', NULL, NULL),
(115, 116, 'Core i5-12400', 'Asus H610M -K', 8, '8G-DDR4', '512GB SSD Zeus', NULL, 'Intel UHD Graphics 730', NULL, 'phím, chuột'),
(116, 117, 'Core i5-12400', 'Asus H610M -K', 8, '8G-DDR4', 'Kingmax 512GB SSD Zeus', NULL, 'Intel UHD Graphics 730', NULL, 'phím, chuột'),
(117, 118, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 120, 'i5-12400 Box', 'Asus H610M -K', 8, '8G-DDR4-3200', 'PQ3480 PCIe Gen3x4 M.2 512GB SSD Kingmax', NULL, 'Intel UHD Graphics 730', NULL, 'phím, chuột'),
(120, 121, 'Intel Core i3-12100', 'Asus H610M -K', 8, 'Ram kingston  8G-DDR4-3200', 'SSD  Kingmax M2  256 GB  PCIe', NULL, 'Intel UHD Graphics 730', NULL, NULL),
(121, 122, 'Core i5-10400', 'H510M-K', 8, '8G-DDR4-3200', 'SSD Giga 120GB', 'HDD 1TB', 'Geforce GT730', NULL, 'phím, chuột'),
(122, 123, 'i5 - 10400F', 'Prime H510-F', 16, '\"DDR4 - 16GB (8GB*2)\"', NULL, 'HDD 2T', 'Nividia Geforce  GT710 - 1G', NULL, NULL),
(123, 124, 'i5 - 9400F', 'Giga H310M', 16, 'DDR4 - 16GB', 'Apacer SSD 240GB', 'HDD 1T Toshiba', NULL, NULL, NULL),
(124, 125, 'i5 10400F', 'Micro Star PRO H510M-B', 16, 'DDR4-16GB', 'NVME 512GB', NULL, 'NVIDIA - GeFore GT710 - GDDR5 - 2GB', NULL, NULL),
(125, 126, 'i3-3240', 'H61M-P20', 6, 'DDR3- 6GB-10700Hz', 'SSD 256GD', NULL, NULL, NULL, NULL),
(126, 127, 'i5 14400F', 'Asus H610M -K DDR4', 32, 'DDR4- 32G/3200 (2 thanh 16GB)', 'Ổ cứng gắn trong/ SSD Kingston NV3 500GB M.2 2280 ', NULL, 'Asus DUAL-RTX3050-6G', NULL, NULL),
(127, 128, 'i5-12400', 'Asus H610M -K DD4', 8, '8G-DDR4', '512GB SSD Zeus', NULL, 'Intel UHD730/128MB', NULL, NULL),
(128, 129, 'Intel Core i3-12100', 'Asus H610M -K', 8, 'Ram kingston  8G-DDR4-3200', 'SSD  Kingmax M2  256 GB  PCIe', NULL, NULL, NULL, NULL),
(129, 130, 'i7 14700F', 'VT_GIGABYTE_GA B760M D2H DDR4', 32, 'Kingston Fury 32GB D4-3200U C16 Beast Black (KF432', '1000GB Kingston SSD NV3 M2 2280 NVMe ( SNV3S/1000G', NULL, 'Nividia geforce RTX3060-GDDR5X-8GB', NULL, NULL),
(130, 131, 'i7 - 10700F', 'Giga Byte B560M', 32, 'DDR4 - 32GB', 'NVME SSD - 256GB', 'HDD1TB WDC', 'Nividia geforce GTX1660-GDDR6-6GB', NULL, NULL),
(131, 132, 'i3-12100', 'Asus H610M-K', 8, '8GB (DDR4)', 'SSDM.2 256GB', NULL, NULL, NULL, NULL),
(132, 133, 'i3-10105', 'ASUS PRIME H510M-K', 16, 'Ram DDR4 16GB (8GB *2) bus 2666', 'Ổ cứng SSD 120G 2.5\" Sata', 'HDD 1T', NULL, NULL, NULL),
(133, 134, 'i5 7400', NULL, 8, '8gb', '256GB SSD', NULL, 'Intel UHD Graphics 630-128MB', NULL, NULL),
(134, 135, 'i5-3450', NULL, 8, '8G-DDR4', '256GB SSD', NULL, NULL, NULL, NULL),
(135, 136, 'intel premium CPU 3240', NULL, 8, '8G-DDR4', 'SSD 128GB', 'HDD 512gb', NULL, NULL, NULL),
(136, 137, 'i3-7100', NULL, 8, '8G-DDR4', NULL, 'HDD 1T', NULL, NULL, NULL),
(137, 138, 'intel premium G440', NULL, 8, '8G-DDR4', NULL, 'HDD 512gb', 'UAD510-128mb', NULL, NULL),
(138, 139, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(139, 140, 'i3 - 1125G4', NULL, 4, '4GB', '256GB SSD', NULL, NULL, NULL, NULL),
(140, 141, 'i5-1340P', NULL, 16, '16GB', '512GB SSD', NULL, NULL, '15.6 inch', NULL),
(141, 142, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(142, 143, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 144, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 145, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 146, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 147, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 148, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 149, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(149, 150, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 151, 'i5-12400', NULL, 16, 'DDR4 - 16GB', '512', NULL, 'Nividia Geforce-RTX3050-6G', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `status` enum('working','resigned') NOT NULL DEFAULT 'working' COMMENT 'working: Đang làm việc, resigned: Đã nghỉ việc',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `employees`
--

INSERT INTO `employees` (`id`, `code`, `fullname`, `email`, `phone`, `department_id`, `position`, `status`, `created_at`) VALUES
(1, '1', 'Phạm Trần Thuận', '', '', 2, 'Chuyên viên Helpdesk', 'working', '2026-10-21 08:04:00'),
(4, 'NVB995ECB3971D041E80', 'Nguyễn Thúc Cẩn', '', '', 1, '', 'working', '2026-10-21 08:13:32'),
(5, 'NV9459350F04BF8CC14C', 'Chu Văn Khanh', '', '', 2, 'PTGĐ', 'working', '2026-10-21 08:13:48'),
(6, 'NVD2B024728B73046718', 'Lê Đình Tuấn', '', '', 1, '', 'working', '2026-10-21 08:14:16'),
(7, 'NVDF0C6EAAAAF557BCFC', 'Ông Nguyễn Thành Chung', '', '', 1, 'PTGĐ Xây Dựng', 'working', '2026-10-21 08:14:29'),
(8, 'NV73842153376C5C3676', 'TB Đậu Quốc Dũng', '', '', 5, '', 'working', '2026-10-21 08:15:22'),
(9, 'NVC52E165FE98E228895', 'Trịnh Diễm Ngọc', '', '', 5, 'CV PC', 'working', '2026-10-21 08:15:32'),
(10, 'NV7A1E9FFE73562851E7', 'Lê Đức Thọ', '', '', 5, 'CV PL Hợp đồng', 'working', '2026-10-21 08:15:44'),
(11, 'NV7295557A7CF83B63BB', 'Hoàng Ngọc Duẩn', '', '', 6, 'TV BKS Nội Bộ', 'working', '2026-10-21 08:16:28'),
(12, 'NVB818F25967B47F3C5B', 'Đặng Đức Đạt', '', '', 6, 'CV', 'working', '2026-10-21 08:16:37'),
(13, 'NVC3ED6A3BD628703D06', 'Bùi Hoàng Kiên', '', '', 7, '', 'working', '2026-10-21 08:17:36'),
(14, 'NV0BE98FDE78292AE993', 'Ngô Hà Trang', '', '', 7, '', 'working', '2026-10-21 08:17:45'),
(15, 'NVB95A387B45F8545CDC', 'Lê  Trọng Nghĩa', '', '', 7, 'KTS Cảnh Quan', 'working', '2026-10-21 08:18:07'),
(16, 'NVC0C6A0E676D7EBDEA4', 'Đào Minh Tuấn', '', '', 7, 'KTS Kết cấu', 'working', '2026-10-21 08:18:26'),
(17, 'NV201D6FFB355801C449', 'Vương Văn Long', '', '', 7, 'CV QLTK điện-điện nhẹ', 'working', '2026-10-21 08:18:43'),
(18, 'NV3BBB2A354E718B130C', 'Lê Huy', '', '', 7, 'Trưởng phòng QLTK Kiến Trúc', 'working', '2026-10-21 08:18:58'),
(20, 'NV62EABED3663BD0DFAA', 'Trần Văn Tâm', '', '', 7, 'CV kết cấu', 'working', '2026-10-21 08:19:24'),
(21, 'NV9340D0C43E2720F25C', 'Phạm Liên Hương', '', '', 7, 'QLTK Nội Thất', 'working', '2026-10-21 08:19:35'),
(23, 'NV7E481CB36D42C2B326', 'Nguyễn Đình Hưng', '', '', 7, 'QLTK Nội Thất', 'working', '2026-10-21 08:20:09'),
(24, 'NV5BCCD7A5010A9BDF50', 'Đỗ Anh Tuấn', '', '', 7, 'QLTK Nội thất onsite', 'working', '2026-10-21 08:20:19'),
(25, 'NVD393287BA975BDB9E1', 'Vũ Thành Trung', '', '', 7, 'CV QLTK Kiến trúc công trình - ONB 12/03/2026', 'working', '2026-10-21 08:20:28'),
(26, 'NVC219371743F9B1EC73', 'Đỗ Tất Đạt', '', '', 7, 'CV QLTK Hạ Tầng', 'working', '2026-10-21 08:20:42'),
(27, 'NVDB9C9646C94A6D42A9', 'Nguyễn Thu Ngân', '', '', 8, 'Trưởng Ban kiêm Kế toán trưởng', 'working', '2026-10-21 08:21:57'),
(28, 'NVC786734A0CF3BBF531', 'Trần Phương Linh', '', '', 8, 'CV Tài chính', 'working', '2026-10-21 08:58:55'),
(29, 'NV0D08EF76039480F742', 'Dương Đắc Bình', '', '', 8, 'TTS', 'working', '2026-10-21 08:59:12'),
(30, 'NVE04B134939BA70FCF3', 'Nguyễn Thị Thanh Hằng', '', '', 8, 'Kế toán Quản trị hợp nhất', 'working', '2026-10-21 08:59:28'),
(31, 'NV5811D70E1C237CBBAE', 'Phùng Thị Lan Anh', '', '', 8, 'KT Thuế đi làm ngày 01/08/2025', 'working', '2026-10-21 08:59:45'),
(32, 'NV9D538F433745B7EDE9', 'Hoàng  Phương Thảo', '', '', 8, 'Kế toán thuế - ONB 16/03/2026', 'working', '2026-10-21 09:00:07'),
(33, 'NVEBDA140D3EAD8F1B43', 'Trần Thị Ngọc Hân', '', '', 8, 'KT Xuân Đài Bay', 'working', '2026-10-21 09:00:28'),
(34, 'NVFC524D11042FE5127D', 'Trần Long Biên', '', '', 8, 'Giám đốc Công ty Fonte kiêm Kế toán trưởng Công ty MangGo', 'working', '2026-10-21 09:00:50'),
(35, 'NV61CDB7B7A79ABB0C8D', 'Mai Thị Kim Dung', '', '', 8, 'KT Thuế', 'working', '2026-10-21 09:01:08'),
(36, 'NVCA7C6AA5380BB06509', 'Lê Diệu Linh', '', '', 8, 'KT Vân Đồn 02/06/2025', 'working', '2026-10-21 09:01:27'),
(37, 'NV752DD94044F9053EA6', 'Nguyễn Thị Tố Uyên', '', '', 8, 'Kế toán nội bộ Meta Tour', 'working', '2026-10-21 09:01:48'),
(38, 'NV54AF06A798466157CC', 'Lê Thị Huyền Trang', '', '', 8, 'CV Tài chính', 'working', '2026-10-21 09:02:06'),
(39, 'NV8DB49C102D5ABAEE53', 'Nguyễn Thị Phương Thủy', '', '', 8, 'KTT Meta Tour', 'working', '2026-10-21 09:02:20'),
(40, 'NV567565658B06377222', 'Phạm Duy Long', '', '', 8, 'TN Tài chính', 'working', '2026-10-21 09:02:38'),
(41, 'NV28160FBE71DBB3334C', 'Trần Thị Ngát', '', '', 8, 'KTTH Vân Đồn', 'working', '2026-10-21 09:02:51'),
(42, 'NVD9FE56AAB956BEADF9', 'Máy cạnh chị Ngát KT', '', '', 8, '', 'working', '2026-10-21 09:03:19'),
(43, 'NVEE04794BCDEBA37C44', 'CASE MÁY CHỦ', '', '', 8, '', 'working', '2026-10-21 09:03:34'),
(44, 'NVF2C0322B9DCDA25FE5', 'Hoàng Thị Huệ', '', '', 8, 'Thủ quỹ kiêm kế toán ngân hàng', 'working', '2026-10-21 09:03:59'),
(45, 'NVF943A21322FE6D4EDE', 'Vũ T Lan Hương', '', '', 8, 'KTTH Vân Đồn', 'working', '2026-10-21 09:04:13'),
(46, 'NV016F5FAB7EDF730C6D', 'Lê Thị Ngọc', '', '', 8, 'Kế toán trưởng Vân đồn', 'working', '2026-10-21 09:04:28'),
(47, 'NV97F27619816FB50108', 'Trương Thị Thu', '', '', 8, 'KTTH', 'working', '2026-10-21 09:04:41'),
(48, 'NV7CBBBCC8B7C03A022A', 'Lưu tại ban KT', '', '', 8, '', 'working', '2026-10-21 09:05:11'),
(49, 'NV028BF1B995C8672115', 'Làm máy chủ di động theo đề xuất được duyệt 13/04/2026 - Lê Minh Nguyệt  thủ quỹ', '', '', 8, '', 'working', '2026-10-21 09:05:34'),
(50, 'NV6B361B4CF2BA27FE86', 'Máy chủ của Ban TCKT - Theo Chỉ đạo của TGĐ và KTT', '', '', 8, '', 'working', '2026-10-21 09:05:45'),
(52, 'NVD2EC90E2416A72DB1C', 'Nguyễn Thị Tuyền', '', '', 8, 'KT Kingsun - ONB 20/03/2025', 'working', '2026-10-21 09:06:51'),
(53, 'NV1764B2B19D7C597E6C', 'KT Kinh Bắc', '', '', 8, '', 'working', '2026-10-21 09:07:03'),
(54, 'NV4A4CCECD1CCBC12BD2', 'Cao Thị Thanh', '', '', 8, 'KT Meta Tour', 'working', '2026-10-21 09:07:13'),
(55, 'NV02B94604CBD047064A', 'Vũ Ngọc Khánh', '', '', 9, 'TN TQT', 'working', '2026-10-21 10:03:14'),
(56, 'NV3FEDA51ABDE305AFCE', 'Mr Hòa - Trưởng nhóm Thầu nghỉ 27/08/2026', '', '', 9, '', 'working', '2026-10-21 10:04:44'),
(57, 'NV184159EFB1F400B483', 'Lê Thùy Linh', '', '', 9, 'Trưởng phòng TTKH', 'working', '2026-10-21 10:05:00'),
(58, 'NVE3009EA3DD97D015E0', 'Hoàng Thị Mỹ', '', '', 9, 'Trưởng Ban', 'working', '2026-10-21 10:05:34'),
(59, 'NV84C48338A539AD2F91', 'Ms Giang', '', '', 9, 'Thực tập sinh 11/05/26', 'working', '2026-10-21 10:05:52'),
(60, 'NV6F240059EF266D658F', 'Thái Doãn Hùng', '', '', 9, 'CV Đấu thầu tổng hợp', 'working', '2026-10-21 10:06:12'),
(61, 'NV9CFC0979F85067647D', 'Hoàng Vương', '', '', 9, 'CV KTĐT ONB 05/10/2026', 'working', '2026-10-21 10:06:26'),
(62, 'NVEBDE497C7DA0F44C99', 'Phạm Tùng Lâm', '', '', 9, 'TQT Mep', 'working', '2026-10-21 10:06:40'),
(63, 'NVCA6655B83B16012983', 'Mai Văn chính', '', '', 9, 'CV ĐT tổng hợp (Mep)', 'working', '2026-10-21 10:07:14'),
(64, 'NV5BAC74CB1884A5901E', 'Lưu tại Ban', '', '', 9, '', 'working', '2026-10-21 10:07:39'),
(65, 'NV1355CDFB852A984C5D', 'Nguyễn Thanh', '', '', 9, 'CV TQT', 'working', '2026-10-21 10:07:55'),
(66, 'NV263E3E533EB55C99B8', 'Phạm Thị Thái', '', '', 9, 'CV QLHĐ', 'working', '2026-10-21 10:08:07'),
(67, 'NV40F052EC41A444E6FC', 'Tống Thị Thanh', '', '', 9, '', 'working', '2026-10-21 10:08:17'),
(68, 'NV31FCC77DDEFB0BEE4A', 'Nguyễn Thị Lan Phương', '', '', 10, 'Trưởng Ban', 'working', '2026-10-21 10:09:18'),
(69, 'NVF2EC9C316E2EFC19DA', 'Phạm Duy Tân', '', '', 10, 'admin ban', 'working', '2026-10-21 10:09:31'),
(70, 'NV268C5779BA0C04D391', 'Lê Anh Đức', '', '', 10, 'CV GPMB', 'working', '2026-10-21 10:09:44'),
(71, 'NV790618C456FF471CF4', 'Mai Thùy Dương', '', '', 10, 'TP ĐT&Thủ tục', 'working', '2026-10-21 10:09:59'),
(72, 'NVBD786E3B62515EB1E2', 'Bùi Vũ', '', '', 10, 'CV ĐT&TT', 'working', '2026-10-21 10:11:34'),
(73, 'NV3A821F2B957D6F31CD', 'Vũ Minh Đức', '', '', 2, 'TP CNTT', 'working', '2026-10-21 10:12:04'),
(75, 'NVD9CB53585290D855FD', 'Tống Thị Cẩm Tú', '', '', 11, 'Quản lý phòng HC', 'working', '2026-10-21 10:13:00'),
(76, 'NV40EE9FC08FCB807A9A', 'Nguyễn Thị Phương Lan', '', '', 11, 'NV Lễ tân', 'working', '2026-10-21 10:13:15'),
(77, 'NVE64240A6C1706F40DA', 'Trịnh Thanh Hà', '', '', 11, 'VTLT', 'working', '2026-10-21 10:13:33'),
(78, 'NV689AB5E5B610670886', 'Sắp xếp để tổ lái xe thực hiện hồ sơ thanh toán', '', '', 11, '', 'working', '2026-10-21 10:13:53'),
(79, 'NV5D6051115133F8FA5F', 'Phùng Thị Huyền Mai', '', '', 11, 'NV Lễ Lân', 'working', '2026-10-21 10:14:07'),
(80, 'NVC3C5ED16DB7BAAE29B', 'Đưa TGĐ sử dụng trong các lần đi công tác vân Đồn', '', '', 11, '', 'working', '2026-10-21 10:14:17'),
(81, 'NVCDE0148900343D081E', 'Laptop phòng họp vip', '', '', 11, '', 'working', '2026-10-21 10:14:25'),
(82, 'NV38AD84476A80919033', 'Lưu tại phòng', '', '', 11, '', 'working', '2026-10-21 10:14:34'),
(83, 'NV64CD2C8ABD85910F20', 'Mrs Hoàng Thị Thạch Thảo', '', '', 12, 'Phó Ban', 'working', '2026-10-21 10:15:20'),
(84, 'NV43E48379013E65BCD7', 'Trần Thị Lành', '', '', 12, 'TN C&B và vận hành NS', 'working', '2026-10-21 10:15:36'),
(85, 'NV7A6A38803003F5A1B4', 'Lưu tại ban', '', '', 12, '', 'working', '2026-10-21 10:16:12'),
(86, 'NVE52AC9EC32267598CD', 'Hoàng T Thu Hương', '', '', 12, 'CV C&B', 'working', '2026-10-21 10:16:23'),
(87, 'NV7B358CCB428404AEA9', 'Hoàng Thị Yến', '', '', 12, 'CV C&B', 'working', '2026-10-21 10:16:39'),
(88, 'NV68A5147B1B99CD405E', 'Nguyễn Thị Nhiên', '', '', 12, 'CV NS TH', 'working', '2026-10-21 10:16:51'),
(89, 'NV90D07E698D8E0F35CA', 'Trần Thị Hồng Phúc', '', '', 12, 'CV Tuyển dụng', 'working', '2026-10-21 10:17:28'),
(90, 'NV435AA8CF5593D8759B', 'Nguyễn Hồng Hạnh', '', '', 12, 'Cv Tuyển dụng', 'working', '2026-10-21 10:17:45'),
(91, 'NV418B5011725C7C9683', 'Phó Ban', '', '', 13, '', 'working', '2026-10-21 10:19:16'),
(92, 'NVEE93942896F7304BC4', 'Lưu tại Ban', '', '', 13, '', 'working', '2026-10-21 10:19:43'),
(93, 'NV8C76C1D90EB07EB300', 'Vũ Quang Hưng', '', '', 13, 'TP TK và QA', 'working', '2026-10-21 10:19:57'),
(94, 'NV6787FA7C279F1142BA', 'Vũ Long', '', '', 13, 'CV Thiết kế', 'working', '2026-10-21 10:20:10'),
(95, 'NVEAE9003F055152194F', 'Nguyễn Thị Hồng Ngân', '', '', 13, '', 'working', '2026-10-21 10:20:34'),
(96, 'NVA6FE6A24A6FA3F2C66', 'Trưởng phòng DVKH', '', '', 14, '', 'working', '2026-10-21 10:21:34'),
(97, 'NVC561D210A2281706B4', 'Nguyễn Thị Hạnh', '', '', 14, '', 'working', '2026-10-21 10:21:41'),
(98, 'NVBBF46E618DAB35509A', 'Đào Thị Hồng', '', '', 14, '', 'working', '2026-10-21 10:21:49'),
(99, 'NV45C9F60BF582D2FF6B', 'Nguyễn Hồng Hạnh', '', '', 14, 'CV Thu hồi công nợ', 'working', '2026-10-21 10:22:05'),
(100, 'NVE14BA7318AFCA82C8A', 'Phạm Thị Lan Anh', '', '', 14, 'CV DVKH', 'working', '2026-10-21 10:22:21'),
(101, 'NV09B418EDBAF7641D66', 'Hoàng Tuấn Anh', '', '', 14, 'CV BG &DVKH', 'working', '2026-10-21 10:22:34'),
(102, 'NVA7919A73BFDD8CD285', 'Nguyễn Vinh Phúc', '', '', 14, 'CV DVKH', 'working', '2026-10-21 10:22:55'),
(103, 'NVB7E3EEA2CD39414E90', 'Phạm Thị Ánh Hồng', '', '', 14, 'CV Quản lý đại lý', 'working', '2026-10-21 10:23:09'),
(104, 'NV2EC8EB94C06E8E1D9C', 'Mai Thị Thùy Linh', '', '', 14, '', 'working', '2026-10-21 10:23:18'),
(105, 'NV73989C113A5FD1D74F', 'Nguyễn Quang Chiều', '', '', 14, 'KT H&H', 'working', '2026-10-21 10:26:35'),
(106, 'NVA5358280311DB799A9', 'Trần Thị Mai Thu', '', '', 14, 'KT EIG', 'working', '2026-10-21 10:26:51'),
(148, 'NV4AB8247E931A97F331', 'Chu Văn Khanh', '', '', 1, 'PTGĐ', 'working', '2026-10-21 14:30:17'),
(149, 'NVFA3818D0106A63E57A', 'Lưu tại Ban', '', '', 5, '', 'working', '2026-10-21 15:49:17'),
(151, 'NV35150A9D31EC3914E7', 'Lưu tại Ban', '', '', 7, '', 'working', '2026-10-21 16:16:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `maintenances`
--

CREATE TABLE `maintenances` (
  `id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `maintenance_type` enum('repair','upgrade','routine') NOT NULL DEFAULT 'repair' COMMENT 'Sửa chữa, Nâng cấp linh kiện, Bảo trì định kỳ',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `cost` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Chi phí bảo dưỡng/sửa chữa/nâng cấp',
  `provider` varchar(100) DEFAULT NULL COMMENT 'Đơn vị thực hiện',
  `description` text NOT NULL COMMENT 'Chi tiết sự cố hoặc nội dung nâng cấp linh kiện',
  `status` enum('in_progress','completed','cannot_repair') NOT NULL DEFAULT 'in_progress' COMMENT 'Đang xử lý, Hoàn thành, Không thể sửa chữa',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `code` varchar(80) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `name`) VALUES
(1, 'dashboard.view', 'Xem bảng điều khiển'),
(2, 'devices.view', 'Xem kho thiết bị'),
(3, 'devices.create', 'Thêm thiết bị'),
(4, 'devices.update', 'Sửa thiết bị'),
(5, 'devices.delete', 'Xóa thiết bị'),
(6, 'devices.export', 'Xuất danh sách thiết bị'),
(7, 'tickets.view', 'Xem phiếu'),
(8, 'tickets.create', 'Lập phiếu'),
(9, 'tickets.print', 'In phiếu'),
(10, 'maintenance.view', 'Xem bảo trì'),
(11, 'maintenance.create', 'Tạo phiếu bảo trì'),
(12, 'maintenance.update', 'Cập nhật bảo trì'),
(13, 'departments.view', 'Xem phòng ban'),
(14, 'departments.create', 'Thêm phòng ban'),
(15, 'departments.update', 'Sửa phòng ban'),
(16, 'departments.delete', 'Xóa phòng ban'),
(17, 'employees.view', 'Xem nhân viên'),
(18, 'employees.create', 'Thêm nhân viên'),
(19, 'employees.update', 'Sửa nhân viên'),
(20, 'employees.delete', 'Xóa nhân viên'),
(21, 'employees.revoke', 'Thu hồi thiết bị nhân viên'),
(22, 'reports.view', 'Xem báo cáo');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `code` varchar(40) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`) VALUES
(1, 'admin', 'Quản trị viên'),
(2, 'warehouse', 'Nhân viên kho'),
(3, 'customer', 'Khách hàng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(3, 1),
(3, 2),
(3, 7),
(3, 13),
(3, 17),
(3, 22);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `ticket_code` varchar(50) NOT NULL COMMENT 'Mã phiếu',
  `ticket_type` enum('allocation','transfer','revocation') NOT NULL COMMENT 'allocation: Cấp phát, transfer: Chuyển giao, revocation: Thu hồi',
  `ticket_date` date NOT NULL COMMENT 'Ngày thực hiện',
  `created_by` int(11) NOT NULL COMMENT 'Người tạo phiếu / thủ kho',
  `from_employee_id` int(11) DEFAULT NULL COMMENT 'Người giao / người bị thu hồi / người chuyển',
  `from_department_id` int(11) DEFAULT NULL,
  `to_employee_id` int(11) DEFAULT NULL COMMENT 'Người nhận cấp phát / chuyển giao',
  `to_department_id` int(11) DEFAULT NULL,
  `is_temporary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Cấp phát tạm thời',
  `expected_return_date` date DEFAULT NULL COMMENT 'Hạn trả dự kiến',
  `status` enum('draft','pending','completed','cancelled') NOT NULL DEFAULT 'completed' COMMENT 'Trạng thái phiếu',
  `reason` text DEFAULT NULL COMMENT 'Lý do cấp/chuyển/thu hồi',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_code`, `ticket_type`, `ticket_date`, `created_by`, `from_employee_id`, `from_department_id`, `to_employee_id`, `to_department_id`, `is_temporary`, `expected_return_date`, `status`, `reason`, `notes`, `created_at`) VALUES
(1, 'PX-20261021-79A3', 'allocation', '2026-10-21', 1, NULL, NULL, 1, 2, 0, NULL, 'completed', 'Cấp máy nhân sự mới', NULL, '2026-10-21 08:06:57'),
(2, 'PX-20261021-6D0D', 'allocation', '2026-10-21', 1, NULL, NULL, 4, 1, 0, NULL, 'completed', NULL, NULL, '2026-10-21 11:59:16'),
(3, 'PX-20261021-435D', 'allocation', '2026-10-21', 1, NULL, NULL, 148, 1, 0, NULL, 'completed', NULL, NULL, '2026-10-21 14:32:36'),
(4, 'PX-20261021-09C6', 'allocation', '2026-10-21', 1, NULL, NULL, 6, 1, 0, NULL, 'completed', NULL, NULL, '2026-10-21 14:36:59'),
(5, 'PX-20261021-A6C3', 'allocation', '2026-10-21', 1, NULL, NULL, 7, 1, 0, NULL, 'completed', NULL, NULL, '2026-10-21 14:38:49'),
(6, 'PX-20261021-85BB', 'allocation', '2026-10-21', 1, NULL, NULL, 8, 5, 0, NULL, 'completed', NULL, NULL, '2026-10-21 14:43:50'),
(7, 'PX-20261021-53EB', 'allocation', '2026-10-21', 1, NULL, NULL, 9, 5, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:39:31'),
(8, 'PX-20261021-5D2A', 'allocation', '2026-10-21', 1, NULL, NULL, 10, 5, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:47:06'),
(9, 'PX-20261021-F581', 'allocation', '2026-10-21', 1, NULL, NULL, 149, 5, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:49:43'),
(10, 'PX-20261021-9E6E', 'allocation', '2026-10-21', 1, NULL, NULL, 11, 6, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:51:29'),
(11, 'PX-20261021-6CB6', 'allocation', '2026-10-21', 1, NULL, NULL, 12, 6, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:52:42'),
(12, 'PX-20261021-0F74', 'allocation', '2026-10-21', 1, NULL, NULL, 15, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:55:15'),
(13, 'PX-20261021-12C0', 'allocation', '2026-10-21', 1, NULL, NULL, 16, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:56:51'),
(14, 'PX-20261021-E31D', 'allocation', '2026-10-21', 1, NULL, NULL, 17, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 15:58:14'),
(15, 'PX-20261021-43DB', 'allocation', '2026-10-21', 1, NULL, NULL, 18, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:00:01'),
(16, 'PX-20261021-3A57', 'allocation', '2026-10-21', 1, NULL, NULL, 14, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:02:53'),
(17, 'PX-20261021-B4A2', 'allocation', '2026-10-21', 1, NULL, NULL, 20, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:05:29'),
(18, 'PX-20261021-1C86', 'allocation', '2026-10-21', 1, NULL, NULL, 21, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:08:28'),
(19, 'PX-20261021-D43E', 'allocation', '2026-10-21', 1, NULL, NULL, 13, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:11:31'),
(20, 'PX-20261021-AF15', 'allocation', '2026-10-21', 1, NULL, NULL, 23, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:13:28'),
(21, 'PX-20261021-E19B', 'allocation', '2026-10-21', 1, NULL, NULL, 151, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:17:10'),
(22, 'PX-20261021-9A04', 'allocation', '2026-10-21', 1, NULL, NULL, 24, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:18:46'),
(23, 'PX-20261021-83C2', 'allocation', '2026-10-21', 1, NULL, NULL, 26, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:20:15'),
(24, 'PX-20261021-A6DD', 'allocation', '2026-10-21', 1, NULL, NULL, 151, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:23:43'),
(25, 'PX-20261021-7B0B', 'allocation', '2026-10-21', 1, NULL, NULL, 68, 10, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:46:47'),
(26, 'PX-20261021-9C8C', 'allocation', '2026-10-21', 1, NULL, NULL, 69, 10, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:48:34'),
(27, 'PX-20261021-6C74', 'allocation', '2026-10-21', 1, NULL, NULL, 70, 10, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:50:50'),
(28, 'PX-20261021-35A0', 'allocation', '2026-10-21', 1, NULL, NULL, 71, 10, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:52:02'),
(29, 'PX-20261021-6D09', 'allocation', '2026-10-21', 1, NULL, NULL, 72, 10, 0, NULL, 'completed', NULL, NULL, '2026-10-21 16:53:24'),
(30, 'PX-20261021-D8BD', 'allocation', '2026-10-21', 1, NULL, NULL, 73, 2, 0, NULL, 'completed', NULL, NULL, '2026-10-21 17:11:14'),
(31, 'PX-20261021-6BFC', 'allocation', '2026-10-21', 2, NULL, NULL, 91, 13, 0, NULL, 'completed', NULL, NULL, '2026-10-21 17:14:51'),
(32, 'PX-20261022-3919', 'allocation', '2026-10-22', 1, NULL, NULL, 92, 13, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:07:55'),
(33, 'PX-20261022-6821', 'allocation', '2026-10-22', 1, NULL, NULL, 93, 13, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:09:35'),
(34, 'PX-20261022-0236', 'allocation', '2026-10-22', 1, NULL, NULL, 94, 13, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:11:19'),
(35, 'PX-20261022-ABC8', 'allocation', '2026-10-22', 1, NULL, NULL, 95, 13, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:13:06'),
(36, 'PCG-20261022-5EC2', 'transfer', '2026-10-22', 1, 94, 13, 92, 13, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:14:50'),
(37, 'PX-20261022-F5FA', 'allocation', '2026-10-22', 1, NULL, NULL, 96, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:16:57'),
(38, 'PX-20261022-9CD7', 'allocation', '2026-10-22', 1, NULL, NULL, 97, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:20:29'),
(39, 'PX-20261022-F3BB', 'allocation', '2026-10-22', 1, NULL, NULL, 98, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:21:34'),
(40, 'PX-20261022-5734', 'allocation', '2026-10-22', 1, NULL, NULL, 99, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:22:39'),
(41, 'PX-20261022-6514', 'allocation', '2026-10-22', 1, NULL, NULL, 100, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:24:24'),
(42, 'PX-20261022-F0DC', 'allocation', '2026-10-22', 1, NULL, NULL, 101, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:26:12'),
(43, 'PX-20261022-C0C0', 'allocation', '2026-10-22', 1, NULL, NULL, 102, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:27:54'),
(44, 'PX-20261022-F7CC', 'allocation', '2026-10-22', 1, NULL, NULL, 103, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:29:11'),
(45, 'PX-20261022-0A39', 'allocation', '2026-10-22', 1, NULL, NULL, 104, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:30:27'),
(46, 'PX-20261022-8144', 'allocation', '2026-10-22', 1, NULL, NULL, 105, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:31:36'),
(47, 'PX-20261022-5A2A', 'allocation', '2026-10-22', 1, NULL, NULL, 106, 14, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:32:10'),
(48, 'PX-20261022-EB8E', 'allocation', '2026-10-22', 1, NULL, NULL, 27, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:54:27'),
(49, 'PX-20261022-7A86', 'allocation', '2026-10-22', 1, NULL, NULL, 28, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:56:07'),
(50, 'PX-20261022-D8F5', 'allocation', '2026-10-22', 1, NULL, NULL, 29, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 08:57:47'),
(51, 'PX-20261022-EA59', 'allocation', '2026-10-22', 1, NULL, NULL, 30, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:00:15'),
(52, 'PX-20261022-2198', 'allocation', '2026-10-22', 1, NULL, NULL, 31, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:01:28'),
(53, 'PX-20261022-EA9C', 'allocation', '2026-10-22', 1, NULL, NULL, 32, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:02:33'),
(54, 'PX-20261022-2FFE', 'allocation', '2026-10-22', 1, NULL, NULL, 33, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:04:19'),
(55, 'PX-20261022-E1EB', 'allocation', '2026-10-22', 1, NULL, NULL, 34, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:09:54'),
(56, 'PX-20261022-C039', 'allocation', '2026-10-22', 1, NULL, NULL, 35, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:11:42'),
(57, 'PX-20261022-5CE5', 'allocation', '2026-10-22', 1, NULL, NULL, 36, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:13:51'),
(58, 'PX-20261022-A95D', 'allocation', '2026-10-22', 1, NULL, NULL, 35, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:15:08'),
(59, 'PX-20261022-DF84', 'allocation', '2026-10-22', 1, NULL, NULL, 37, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:18:35'),
(60, 'PX-20261022-8421', 'allocation', '2026-10-22', 1, NULL, NULL, 38, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:20:29'),
(61, 'PX-20261022-5958', 'allocation', '2026-10-22', 1, NULL, NULL, 39, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:23:54'),
(62, 'PX-20261022-AC1D', 'allocation', '2026-10-22', 1, NULL, NULL, 40, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:24:51'),
(63, 'PX-20261022-C56D', 'allocation', '2026-10-22', 1, NULL, NULL, 41, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:31:54'),
(64, 'PX-20261022-583C', 'allocation', '2026-10-22', 1, NULL, NULL, 42, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:33:19'),
(65, 'PX-20261022-763A', 'allocation', '2026-10-22', 1, NULL, NULL, 43, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:34:28'),
(66, 'PX-20261022-8FCC', 'allocation', '2026-10-22', 1, NULL, NULL, 44, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:35:51'),
(67, 'PX-20261022-D4A3', 'allocation', '2026-10-22', 1, NULL, NULL, 45, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:37:19'),
(68, 'PX-20261022-76FB', 'allocation', '2026-10-22', 1, NULL, NULL, 46, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:39:38'),
(69, 'PX-20261022-FB44', 'allocation', '2026-10-22', 1, NULL, NULL, 47, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:40:43'),
(70, 'PX-20261022-B391', 'allocation', '2026-10-22', 1, NULL, NULL, 48, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:41:46'),
(71, 'PX-20261022-E2AF', 'allocation', '2026-10-22', 1, NULL, NULL, 42, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:43:30'),
(72, 'PX-20261022-AB01', 'allocation', '2026-10-22', 1, NULL, NULL, 49, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:45:45'),
(73, 'PX-20261022-FB9C', 'allocation', '2026-10-22', 1, NULL, NULL, 50, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:47:34'),
(74, 'PX-20261022-3D6F', 'allocation', '2026-10-22', 1, NULL, NULL, 34, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:48:36'),
(75, 'PX-20261022-6212', 'allocation', '2026-10-22', 1, NULL, NULL, 52, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:49:34'),
(76, 'PX-20261022-A5EB', 'allocation', '2026-10-22', 1, NULL, NULL, 53, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:50:28'),
(77, 'PX-20261022-AF16', 'allocation', '2026-10-22', 1, NULL, NULL, 54, 8, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:51:31'),
(78, 'PX-20261022-65EB', 'allocation', '2026-10-22', 1, NULL, NULL, NULL, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:53:50'),
(79, 'PX-20261022-1FC8', 'allocation', '2026-10-22', 1, NULL, NULL, 75, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:55:06'),
(80, 'PX-20261022-CE85', 'allocation', '2026-10-22', 1, NULL, NULL, 76, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:55:58'),
(81, 'PX-20261022-9CEA', 'allocation', '2026-10-22', 1, NULL, NULL, 77, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:57:07'),
(82, 'PX-20261022-8B42', 'allocation', '2026-10-22', 1, NULL, NULL, 78, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:58:15'),
(83, 'PX-20261022-CAB1', 'allocation', '2026-10-22', 1, NULL, NULL, 79, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 09:59:15'),
(84, 'PX-20261022-E3F9', 'allocation', '2026-10-22', 1, NULL, NULL, 80, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:00:12'),
(85, 'PX-20261022-7A8D', 'allocation', '2026-10-22', 1, NULL, NULL, 81, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:01:10'),
(86, 'PX-20261022-2F30', 'allocation', '2026-10-22', 1, NULL, NULL, 82, 11, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:02:11'),
(87, 'PX-20261022-B525', 'allocation', '2026-10-22', 1, NULL, NULL, 83, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:03:16'),
(88, 'PX-20261022-8764', 'allocation', '2026-10-22', 1, NULL, NULL, 84, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:04:14'),
(89, 'PX-20261022-B945', 'allocation', '2026-10-22', 1, NULL, NULL, 85, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:05:09'),
(90, 'PX-20261022-1CF4', 'allocation', '2026-10-22', 1, NULL, NULL, 86, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:06:37'),
(91, 'PX-20261022-0828', 'allocation', '2026-10-22', 1, NULL, NULL, 87, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:08:58'),
(92, 'PX-20261022-186F', 'allocation', '2026-10-22', 1, NULL, NULL, 88, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:10:20'),
(93, 'PX-20261022-1B72', 'allocation', '2026-10-22', 1, NULL, NULL, 89, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:12:00'),
(94, 'PX-20261022-E872', 'allocation', '2026-10-22', 1, NULL, NULL, 90, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:13:03'),
(95, 'PX-20261022-4BAF', 'allocation', '2026-10-22', 1, NULL, NULL, 55, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:14:55'),
(96, 'PX-20261022-B579', 'allocation', '2026-10-22', 1, NULL, NULL, 56, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:16:15'),
(97, 'PX-20261022-CCC8', 'allocation', '2026-10-22', 1, NULL, NULL, 57, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:20:03'),
(98, 'PX-20261022-04A7', 'allocation', '2026-10-22', 1, NULL, NULL, 58, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:40:04'),
(99, 'PX-20261022-8246', 'allocation', '2026-10-22', 1, NULL, NULL, 59, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:41:23'),
(100, 'PX-20261022-266D', 'allocation', '2026-10-22', 1, NULL, NULL, 60, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:42:25'),
(101, 'PX-20261022-5E55', 'allocation', '2026-10-22', 1, NULL, NULL, 61, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:43:21'),
(102, 'PX-20261022-5543', 'allocation', '2026-10-22', 1, NULL, NULL, 62, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:44:13'),
(103, 'PX-20261022-2598', 'allocation', '2026-10-22', 1, NULL, NULL, 63, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:49:04'),
(104, 'PX-20261022-2545', 'allocation', '2026-10-22', 1, NULL, NULL, 64, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:52:22'),
(105, 'PX-20261022-C83F', 'allocation', '2026-10-22', 1, NULL, NULL, 65, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:53:18'),
(106, 'PX-20261022-B9F9', 'allocation', '2026-10-22', 1, NULL, NULL, 66, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:54:28'),
(107, 'PX-20261022-376F', 'allocation', '2026-10-22', 1, NULL, NULL, 67, 9, 0, NULL, 'completed', NULL, NULL, '2026-10-22 10:55:55'),
(108, 'PTH-20261023-A8BB', 'revocation', '2026-10-23', 1, 71, 10, NULL, NULL, 0, NULL, 'completed', NULL, NULL, '2026-10-23 11:14:20'),
(109, 'PX-20261026-B698', 'allocation', '2026-10-26', 1, NULL, NULL, 91, 13, 0, NULL, 'completed', 'Cấp laptop', NULL, '2026-10-26 09:02:55'),
(110, 'PTH-20261027-1513', 'revocation', '2026-10-27', 1, 64, 9, NULL, NULL, 0, NULL, 'completed', 'Thu hồi thiết bị khi nhân viên bàn giao/nghỉ việc', NULL, '2026-10-27 15:42:43'),
(111, 'PTH-20261027-D06A', 'revocation', '2026-10-27', 1, 85, 12, NULL, NULL, 0, NULL, 'completed', 'Thu hồi thiết bị khi nhân viên bàn giao/nghỉ việc', NULL, '2026-10-27 15:49:58'),
(112, 'PTH-20261027-07F9', 'revocation', '2026-10-27', 1, 42, 8, NULL, NULL, 0, NULL, 'completed', NULL, NULL, '2026-10-27 15:52:41'),
(113, 'PCG-20261027-6949', 'transfer', '2026-10-27', 1, 91, 13, 83, 12, 0, NULL, 'completed', NULL, NULL, '2026-10-27 16:23:54'),
(114, 'PX-20261028-7B3A', 'allocation', '2026-10-28', 1, NULL, NULL, 25, 7, 0, NULL, 'completed', NULL, NULL, '2026-10-28 09:04:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ticket_details`
--

CREATE TABLE `ticket_details` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `device_id` int(11) NOT NULL,
  `accessories` varchar(255) DEFAULT NULL COMMENT 'Phụ kiện đi kèm: Chuột, phím, sạc, cáp HDMI...',
  `returned_condition` enum('good','broken','missing_accessories') DEFAULT NULL COMMENT 'Tình trạng khi thu hồi',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ticket_details`
--

INSERT INTO `ticket_details` (`id`, `ticket_id`, `device_id`, `accessories`, `returned_condition`, `notes`) VALUES
(1, 1, 1, 'Chuột, lót chuột', NULL, NULL),
(2, 2, 16, NULL, NULL, NULL),
(3, 3, 17, 'Chuột, phím', NULL, NULL),
(4, 4, 19, 'Chuột, phím', NULL, NULL),
(5, 5, 20, 'Chuột, phím', NULL, NULL),
(6, 6, 21, 'phím', NULL, NULL),
(7, 7, 22, 'Chuột, phím', NULL, NULL),
(8, 8, 23, 'Chuột, phím', NULL, NULL),
(9, 9, 24, 'Chuột, phím', NULL, NULL),
(10, 10, 25, NULL, NULL, NULL),
(11, 11, 26, 'Chuột, phím', NULL, NULL),
(12, 12, 27, 'Chuột, phím', NULL, NULL),
(13, 13, 28, 'Chuột, phím', NULL, NULL),
(14, 14, 29, 'Chuột, phím', NULL, NULL),
(15, 15, 30, 'Chuột, phím', NULL, NULL),
(16, 16, 31, 'Chuột, phím', NULL, NULL),
(17, 17, 32, 'Chuột, phím', NULL, NULL),
(18, 18, 33, 'Chuột, phím', NULL, NULL),
(19, 19, 34, 'Chuột, phím', NULL, NULL),
(20, 20, 35, 'Chuột, phím', NULL, NULL),
(21, 21, 36, 'Chuột, phím', NULL, NULL),
(22, 22, 37, 'Chuột, phím', NULL, NULL),
(23, 23, 38, 'Chuột, phím', NULL, NULL),
(24, 24, 40, NULL, NULL, NULL),
(25, 25, 41, 'Chuột, phím', NULL, NULL),
(26, 26, 42, 'Chuột, phím', NULL, NULL),
(27, 27, 43, 'Chuột, phím', NULL, NULL),
(28, 28, 44, 'Chuột, phím', NULL, NULL),
(29, 29, 45, NULL, NULL, NULL),
(30, 30, 46, NULL, NULL, NULL),
(31, 31, 47, NULL, NULL, NULL),
(32, 32, 48, 'Chuột, phím', NULL, NULL),
(33, 33, 49, 'Chuột, phím', NULL, NULL),
(34, 34, 50, 'Chuột, phím', NULL, NULL),
(35, 35, 51, NULL, NULL, NULL),
(36, 36, 50, 'Chuột, phím', NULL, NULL),
(37, 37, 52, 'Chuột, phím', NULL, NULL),
(38, 38, 53, 'Chuột, phím', NULL, NULL),
(39, 39, 54, 'Chuột, phím', NULL, NULL),
(40, 40, 55, 'Chuột, phím', NULL, NULL),
(41, 41, 56, 'chuột', NULL, NULL),
(42, 42, 57, NULL, NULL, NULL),
(43, 43, 58, NULL, NULL, NULL),
(44, 44, 59, 'Chuột, phím', NULL, NULL),
(45, 45, 60, NULL, NULL, NULL),
(46, 46, 61, NULL, NULL, NULL),
(47, 47, 62, NULL, NULL, NULL),
(48, 48, 63, NULL, NULL, NULL),
(49, 49, 64, 'Chuột, phím', NULL, NULL),
(50, 50, 65, 'Chuột, phím', NULL, NULL),
(51, 51, 66, 'Chuột, phím', NULL, NULL),
(52, 52, 67, 'Chuột, phím', NULL, NULL),
(53, 53, 68, 'Chuột, phím', NULL, NULL),
(54, 54, 69, 'Chuột, phím', NULL, NULL),
(55, 55, 70, NULL, NULL, NULL),
(56, 56, 71, 'Chuột, phím', NULL, NULL),
(57, 57, 72, 'Chuột, phím', NULL, NULL),
(58, 58, 73, 'Chuột, phím', NULL, NULL),
(59, 59, 74, 'Chuột, phím', NULL, NULL),
(60, 60, 75, 'Chuột, phím', NULL, NULL),
(61, 61, 76, 'Chuột, phím', NULL, NULL),
(62, 62, 77, 'Chuột, phím', NULL, NULL),
(63, 63, 78, 'Chuột, phím', NULL, NULL),
(65, 65, 80, NULL, NULL, NULL),
(66, 66, 81, 'Chuột, phím', NULL, NULL),
(67, 67, 82, 'Chuột, phím', NULL, NULL),
(68, 68, 83, 'Chuột, phím', NULL, NULL),
(69, 69, 84, 'Chuột, phím', NULL, NULL),
(70, 70, 85, 'Chuột, phím', NULL, NULL),
(71, 71, 86, 'Chuột, phím', NULL, NULL),
(72, 72, 87, 'chuột', NULL, NULL),
(73, 73, 88, 'Chuột, phím', NULL, NULL),
(74, 74, 89, NULL, NULL, NULL),
(75, 75, 90, NULL, NULL, NULL),
(76, 76, 91, NULL, NULL, NULL),
(77, 77, 92, NULL, NULL, NULL),
(79, 79, 94, 'Chuột, phím', NULL, NULL),
(80, 80, 95, 'Chuột, phím', NULL, NULL),
(81, 81, 96, NULL, NULL, NULL),
(82, 82, 97, 'Chuột, phím', NULL, NULL),
(83, 83, 98, 'Chuột, phím', NULL, NULL),
(84, 84, 99, 'chuột', NULL, NULL),
(85, 85, 100, 'Chuột, phím', NULL, NULL),
(87, 87, 102, 'Chuột, phím', NULL, NULL),
(88, 88, 103, 'chuột', NULL, NULL),
(90, 90, 105, 'Chuột, phím', NULL, NULL),
(91, 91, 106, 'Chuột, phím', NULL, NULL),
(92, 92, 107, 'Chuột, phím', NULL, NULL),
(93, 93, 108, 'Chuột, phím', NULL, NULL),
(94, 94, 109, 'Chuột, phím', NULL, NULL),
(95, 95, 110, 'Chuột, phím', NULL, NULL),
(96, 96, 111, 'Chuột, phím', NULL, NULL),
(97, 97, 112, 'Chuột, phím', NULL, NULL),
(98, 98, 113, 'Chuột, phím', NULL, NULL),
(99, 99, 114, 'Chuột, phím', NULL, NULL),
(100, 100, 115, 'Chuột, phím', NULL, NULL),
(101, 101, 116, 'Chuột, phím', NULL, NULL),
(102, 102, 117, 'Chuột, phím', NULL, NULL),
(103, 103, 118, 'Chuột, phím', NULL, NULL),
(105, 105, 120, 'Chuột, phím', NULL, NULL),
(106, 106, 121, 'Chuột, phím', NULL, NULL),
(107, 107, 122, 'Chuột, phím', NULL, NULL),
(108, 108, 44, 'Chuột, phím, màn', 'good', NULL),
(109, 109, 140, 'chuột', NULL, NULL),
(113, 113, 47, NULL, NULL, NULL),
(114, 114, 151, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(40) NOT NULL DEFAULT 'warehouse' COMMENT 'Mã vai trò RBAC',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Hoạt động, 0: Khóa',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `email`, `role`, `status`, `created_at`) VALUES
(1, 'admin', '$2y$10$2yKvpupzQNm41o.MhNcV4OWwIt1OQkIRphDPOmewPgLhs35TdBxqK', 'Quản trị viên Hệ thống', 'admin@warehouse.local', 'admin', 1, '2026-10-20 15:58:11'),
(2, 'thukho', '$2y$10$2yKvpupzQNm41o.MhNcV4OWwIt1OQkIRphDPOmewPgLhs35TdBxqK', 'Thủ Kho', 'thukho@warehouse.local', 'warehouse', 1, '2026-10-20 15:58:11'),
(3, 'ducvm', '$2y$10$o9pmZEGLAuNz03Kgkx9QXOP4KgIixRwD9mBalS4cutoJ/TAnHTXXS', 'Vũ Minh Đức', 'duc.vm@everland.vn', 'admin', 1, '2026-10-23 11:02:22'),
(4, 'username', '$2y$10$oZSfzP0scs26y1hnblfe/eAwV9ohB3TKw5guDCDm5Dx55SvtvoiU6', 'Khách hàng', '', 'customer', 1, '2026-10-28 10:40:11'),
(5, 'thuanpt', '$2y$10$Rbkv7HgwbRxI3U9e4u84XuSNbdZ2vVKztqXGlFgU4LML//thL8bVC', 'Phạm Trần Thuận', 'phamtranthuan2003@gmail.com', 'admin', 1, '2026-10-28 10:42:31');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_code` (`device_code`),
  ADD KEY `fk_dev_employee` (`current_employee_id`),
  ADD KEY `fk_dev_department` (`current_department_id`);

--
-- Chỉ mục cho bảng `device_history`
--
ALTER TABLE `device_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dh_device` (`device_id`),
  ADD KEY `fk_dh_ticket` (`ticket_id`),
  ADD KEY `fk_dh_user` (`performed_by`);

--
-- Chỉ mục cho bảng `device_monitors`
--
ALTER TABLE `device_monitors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_device_monitor_no` (`device_id`,`monitor_no`);

--
-- Chỉ mục cho bảng `device_specs`
--
ALTER TABLE `device_specs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_id` (`device_id`);

--
-- Chỉ mục cho bảng `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_emp_department` (`department_id`);

--
-- Chỉ mục cho bảng `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_maint_device` (`device_id`),
  ADD KEY `fk_maint_user` (`created_by`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_role_permission_permission` (`permission_id`);

--
-- Chỉ mục cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_code` (`ticket_code`),
  ADD KEY `fk_tick_user` (`created_by`),
  ADD KEY `fk_tick_from_emp` (`from_employee_id`),
  ADD KEY `fk_tick_from_dept` (`from_department_id`),
  ADD KEY `fk_tick_to_emp` (`to_employee_id`),
  ADD KEY `fk_tick_to_dept` (`to_department_id`);

--
-- Chỉ mục cho bảng `ticket_details`
--
ALTER TABLE `ticket_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_td_ticket` (`ticket_id`),
  ADD KEY `fk_td_device` (`device_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT cho bảng `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT cho bảng `device_history`
--
ALTER TABLE `device_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=266;

--
-- AUTO_INCREMENT cho bảng `device_monitors`
--
ALTER TABLE `device_monitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT cho bảng `device_specs`
--
ALTER TABLE `device_specs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT cho bảng `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT cho bảng `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT cho bảng `ticket_details`
--
ALTER TABLE `ticket_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `fk_dev_department` FOREIGN KEY (`current_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dev_employee` FOREIGN KEY (`current_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `device_history`
--
ALTER TABLE `device_history`
  ADD CONSTRAINT `fk_dh_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dh_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dh_user` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `device_monitors`
--
ALTER TABLE `device_monitors`
  ADD CONSTRAINT `fk_monitor_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `device_specs`
--
ALTER TABLE `device_specs`
  ADD CONSTRAINT `fk_spec_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_emp_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `maintenances`
--
ALTER TABLE `maintenances`
  ADD CONSTRAINT `fk_maint_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_maint_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_role_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_role_permission_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tick_from_dept` FOREIGN KEY (`from_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tick_from_emp` FOREIGN KEY (`from_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tick_to_dept` FOREIGN KEY (`to_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tick_to_emp` FOREIGN KEY (`to_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tick_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `ticket_details`
--
ALTER TABLE `ticket_details`
  ADD CONSTRAINT `fk_td_device` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_td_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

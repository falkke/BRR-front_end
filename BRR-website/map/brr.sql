-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 03, 2019 at 01:14 AM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brr`
--

-- --------------------------------------------------------

--
-- Table structure for table `arrive_check`
--

CREATE TABLE `arrive_check` (
  `ID` int(11) NOT NULL,
  `station` varchar(20) NOT NULL,
  `LAT` double NOT NULL,
  `LNG` double NOT NULL,
  `arrive` tinyint(1) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `distance` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `arrive_check`
--

INSERT INTO `arrive_check` (`ID`, `station`, `LAT`, `LNG`, `arrive`, `time`, `distance`) VALUES
(0, 'Start', 59.6390965, 16.5230476, 1, '2019-01-02 16:21:36', 4.5),
(115, 'First Station', 59.6658163, 16.512089, 0, '2019-01-02 16:21:28', 4.5),
(164, 'Second Station', 59.6555268, 16.5187831, 0, '2019-01-02 16:21:30', 4.5),
(341, 'Third Station', 59.6626066, 16.5187809, 0, '2019-01-02 16:21:33', 4.5),
(475, 'Fourth Station', 59.6389951, 16.523055, 0, '2019-01-02 13:31:30', 4.5);

-- --------------------------------------------------------

--
-- Table structure for table `points`
--

CREATE TABLE `points` (
  `ID` int(11) NOT NULL,
  `LAT` double NOT NULL,
  `LNG` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `points`
--

INSERT INTO `points` (`ID`, `LAT`, `LNG`) VALUES
(1, 59.6390965, 16.5230476),
(2, 59.63933, 16.523),
(3, 59.6394695, 16.5231278),
(4, 59.6398441, 16.5239099),
(5, 59.6400224, 16.5240132),
(6, 59.6402483, 16.5241188),
(7, 59.6407431, 16.5237844),
(8, 59.6408759, 16.5238796),
(9, 59.6410086, 16.5239435),
(10, 59.641344, 16.5240323),
(11, 59.6424695, 16.524004),
(12, 59.6425116, 16.5240017),
(13, 59.6429062, 16.5239694),
(14, 59.6434059, 16.5232451),
(15, 59.6440385, 16.522652),
(16, 59.6445911, 16.5221465),
(17, 59.6452321, 16.5218139),
(18, 59.6455393, 16.5218022),
(19, 59.645871, 16.5218812),
(20, 59.6462667, 16.5217104),
(21, 59.6466188, 16.5214966),
(22, 59.6467559, 16.5211865),
(23, 59.6468547, 16.5209724),
(24, 59.6470791, 16.5207276),
(25, 59.6472262, 16.5207242),
(26, 59.6474919, 16.5205244),
(27, 59.6476758, 16.5204888),
(28, 59.6481793, 16.5207798),
(29, 59.6482468, 16.5207966),
(30, 59.6484808, 16.520615),
(31, 59.6485928, 16.5205362),
(32, 59.6489453, 16.5204484),
(33, 59.6497831, 16.52033),
(34, 59.6499841, 16.5202647),
(35, 59.6504338, 16.5198777),
(36, 59.650636, 16.5198501),
(37, 59.6507774, 16.5198175),
(38, 59.6509988, 16.5198116),
(39, 59.6512053, 16.5198764),
(40, 59.6514955, 16.5198761),
(41, 59.6517756, 16.5199985),
(42, 59.6519061, 16.520254),
(43, 59.6523643, 16.5209753),
(44, 59.6528111, 16.5216005),
(45, 59.6530642, 16.521504),
(46, 59.653161, 16.5213597),
(47, 59.653347, 16.5210995),
(48, 59.6535383, 16.5209605),
(49, 59.6537359, 16.5205478),
(50, 59.653944, 16.5203542),
(51, 59.654084, 16.5201651),
(52, 59.6543727, 16.5199119),
(53, 59.6545047, 16.5197668),
(54, 59.6548434, 16.5192005),
(55, 59.6549628, 16.5190401),
(56, 59.6552093, 16.5189907),
(57, 59.6555268, 16.5187831),
(58, 59.6556899, 16.5187303),
(59, 59.6558553, 16.518543),
(60, 59.6562884, 16.51819),
(61, 59.6563683, 16.5179524),
(62, 59.6568704, 16.5170615),
(63, 59.6571176, 16.516904),
(64, 59.6579474, 16.5162521),
(65, 59.6582317, 16.5161299),
(66, 59.6585834, 16.5162196),
(67, 59.6588556, 16.5161755),
(68, 59.6589885, 16.5159597),
(69, 59.6592762, 16.5155658),
(70, 59.6594412, 16.5154468),
(71, 59.6596278, 16.5153523),
(72, 59.6598567, 16.5155111),
(73, 59.6600254, 16.5157437),
(74, 59.6602819, 16.5158237),
(75, 59.6604248, 16.5156546),
(76, 59.6606548, 16.5157253),
(77, 59.6608447, 16.5159101),
(78, 59.6609655, 16.516195),
(79, 59.6611757, 16.516567),
(80, 59.6613794, 16.5167469),
(81, 59.6615496, 16.5170478),
(82, 59.6614384, 16.5180256),
(83, 59.6614985, 16.518566),
(84, 59.6615671, 16.5189687),
(85, 59.6617524, 16.5189918),
(86, 59.6619855, 16.518903),
(87, 59.6621755, 16.5186748),
(88, 59.6623174, 16.5185024),
(89, 59.6625173, 16.518641),
(90, 59.6626066, 16.5187809),
(91, 59.6626966, 16.5189609),
(92, 59.663062, 16.5194547),
(93, 59.6632202, 16.519955),
(94, 59.6633426, 16.5201252),
(95, 59.6635547, 16.5202527),
(96, 59.6637244, 16.5206337),
(97, 59.6641275, 16.5211545),
(98, 59.6643193, 16.5212505),
(99, 59.664561, 16.5216357),
(100, 59.6647578, 16.5214723),
(101, 59.6649168, 16.5212694),
(102, 59.665067, 16.5209252),
(103, 59.6654621, 16.5206683),
(104, 59.6656184, 16.5203063),
(105, 59.6658057, 16.520065),
(106, 59.6657839, 16.5196698),
(107, 59.665675, 16.5192691),
(108, 59.665658, 16.5188552),
(109, 59.6655629, 16.5184241),
(110, 59.665423, 16.5181519),
(111, 59.6653888, 16.5177839),
(112, 59.6657896, 16.517632),
(113, 59.6660185, 16.517582),
(114, 59.6662854, 16.5168374),
(115, 59.6663696, 16.5163731),
(116, 59.6664126, 16.5159226),
(117, 59.6663921, 16.5154911),
(118, 59.6663187, 16.5151562),
(119, 59.6663587, 16.5147225),
(120, 59.6662255, 16.5143997),
(121, 59.6662055, 16.5139549),
(122, 59.6661471, 16.5136272),
(123, 59.6659803, 16.5133051),
(124, 59.66589, 16.5127856),
(125, 59.6657561, 16.5123502),
(126, 59.6658163, 16.512089),
(127, 59.6658563, 16.511889),
(128, 59.6658563, 16.511399),
(129, 59.6659649, 16.511119),
(130, 59.6660854, 16.5106933),
(131, 59.6661354, 16.5103033),
(132, 59.665806, 16.509807),
(133, 59.665366, 16.509527),
(134, 59.665029, 16.5089977),
(135, 59.664899, 16.5087277),
(136, 59.6646794, 16.508523),
(137, 59.6644988, 16.5086053),
(138, 59.6642505, 16.5080862),
(139, 59.6636005, 16.5060962),
(140, 59.6633792, 16.5055265),
(141, 59.6627976, 16.5050035),
(142, 59.6624672, 16.5048694),
(143, 59.6618953, 16.5049621),
(144, 59.6610939, 16.5045196),
(145, 59.6608124, 16.5042804),
(146, 59.6606423, 16.5040612),
(147, 59.6602457, 16.5033266),
(148, 59.659265, 16.5010797),
(149, 59.6590239, 16.5007152),
(150, 59.6588377, 16.500394),
(151, 59.658553, 16.500008),
(152, 59.658413, 16.4995673),
(153, 59.6583062, 16.4991763),
(154, 59.6581541, 16.4990766),
(155, 59.6571991, 16.4990078),
(156, 59.657038, 16.4986487),
(157, 59.655258, 16.4939955),
(158, 59.655028, 16.4936974),
(159, 59.6553438, 16.4932643),
(160, 59.6560517, 16.4923781),
(161, 59.6561172, 16.4922669),
(162, 59.6560947, 16.4921747),
(163, 59.6560522, 16.49152),
(164, 59.6559577, 16.4910056),
(165, 59.6556966, 16.4905913),
(166, 59.6555727, 16.4904887),
(167, 59.6554114, 16.4905256),
(168, 59.6552027, 16.4906966),
(169, 59.6550495, 16.4907524),
(170, 59.6544593, 16.4907535),
(171, 59.6540145, 16.4906695),
(172, 59.6538909, 16.4904857),
(173, 59.6538009, 16.4902209),
(174, 59.65359, 16.4900805),
(175, 59.6530935, 16.4892501),
(176, 59.6529115, 16.4890555),
(177, 59.6528515, 16.4887993),
(178, 59.6528015, 16.4887493),
(179, 59.6527015, 16.4887593),
(180, 59.6526015, 16.4888893),
(181, 59.6525076, 16.4889593),
(182, 59.6524576, 16.4889593),
(183, 59.6523596, 16.4889299),
(184, 59.6522896, 16.48892),
(185, 59.6522096, 16.4888),
(186, 59.6521096, 16.4886),
(187, 59.6520096, 16.4884),
(188, 59.6520096, 16.488402),
(189, 59.6519096, 16.4882901),
(190, 59.6518096, 16.4882001),
(191, 59.6517596, 16.4881001),
(192, 59.6517496, 16.4875901),
(193, 59.6517386, 16.4874801),
(194, 59.6517086, 16.4873601),
(195, 59.6516886, 16.4873001),
(196, 59.6510203, 16.4874588),
(197, 59.6506016, 16.4877073),
(198, 59.6503718, 16.4879907),
(199, 59.6502642, 16.4884117),
(200, 59.6502502, 16.4888333),
(201, 59.6502702, 16.4893933),
(202, 59.6502812, 16.4896333),
(203, 59.65027, 16.4899333),
(204, 59.6502001, 16.4901593),
(205, 59.6498589, 16.4904793),
(206, 59.6495932, 16.4906593),
(207, 59.6494212, 16.4907511),
(208, 59.6491694, 16.490657),
(209, 59.6488585, 16.4904032),
(210, 59.6487585, 16.4901596),
(211, 59.6485144, 16.4889755),
(212, 59.6484289, 16.4884922),
(213, 59.6483718, 16.4882514),
(214, 59.648337, 16.4880914),
(215, 59.648217, 16.4878214);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

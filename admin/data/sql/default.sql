-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 03, 2020 at 10:57 AM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sto`
--

-- --------------------------------------------------------

--
-- Table structure for table `sto_attachments`
--

CREATE TABLE `sto_attachments` (
  `id` bigint(20) NOT NULL,
  `path` tinytext NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `attachment_to` varchar(128) DEFAULT NULL,
  `attachment_to_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_classes`
--

CREATE TABLE `sto_classes` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) DEFAULT 1,
  `code` varchar(60) NOT NULL,
  `name` varchar(255) NOT NULL,
  `note` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_classes_teacher`
--

CREATE TABLE `sto_classes_teacher` (
  `class_id` bigint(20) NOT NULL,
  `teacher` bigint(20) NOT NULL,
  `year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_exam`
--

CREATE TABLE `sto_exam` (
  `id` bigint(20) NOT NULL,
  `task_code` varchar(255) NOT NULL,
  `exam_status` varchar(128) NOT NULL DEFAULT 'pending',
  `revisions` int(11) NOT NULL DEFAULT 0,
  `exam_code` varchar(255) NOT NULL,
  `invigilator` bigint(20) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `exam_time_start` time DEFAULT NULL,
  `exam_time_end` time DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_exam_classes_id`
--

CREATE TABLE `sto_exam_classes_id` (
  `exam_id` bigint(20) NOT NULL,
  `class_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_exam_room_id`
--

CREATE TABLE `sto_exam_room_id` (
  `exam_id` bigint(20) NOT NULL,
  `room_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_exam_student_assign`
--

CREATE TABLE `sto_exam_student_assign` (
  `exam_id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_options`
--

CREATE TABLE `sto_options` (
  `id` bigint(10) NOT NULL,
  `site_id` bigint(20) NOT NULL DEFAULT 1,
  `option_name` varchar(255) NOT NULL,
  `option_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_post`
--

CREATE TABLE `sto_post` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) DEFAULT 1,
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL,
  `author` bigint(20) DEFAULT NULL,
  `type` varchar(60) DEFAULT 'post',
  `status` varchar(60) NOT NULL DEFAULT 'draft' COMMENT 'draft, public, trash, pending',
  `parent_id` bigint(20) DEFAULT NULL,
  `content` longtext NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_question`
--

CREATE TABLE `sto_question` (
  `id` bigint(20) NOT NULL,
  `task_code` varchar(255) NOT NULL,
  `status` enum('publish','draft','pending','delete','hidden') NOT NULL,
  `question` text NOT NULL,
  `question_note` text DEFAULT NULL,
  `question_type` enum('choice','essai') NOT NULL,
  `answer_alpha` char(1) DEFAULT NULL,
  `score_default` int(10) NOT NULL DEFAULT 10,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_question_choice`
--

CREATE TABLE `sto_question_choice` (
  `alpha` char(1) NOT NULL,
  `question_id` bigint(20) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_question_task`
--

CREATE TABLE `sto_question_task` (
  `site_id` bigint(20) DEFAULT 1,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject_code` varchar(60) NOT NULL,
  `note` longtext DEFAULT NULL,
  `created_by` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_religion`
--

CREATE TABLE `sto_religion` (
  `site_id` bigint(20) DEFAULT 1,
  `code` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sto_religion`
--

INSERT INTO `sto_religion` (`site_id`, `code`, `name`) VALUES
(1, 'BEL', 'Believers'),
(1, 'BUD', 'Buddha'),
(1, 'CAT', 'Catholic'),
(1, 'CON', 'Confucianism'),
(1, 'HIN', 'Hindu'),
(1, 'ISL', 'Islam'),
(1, 'PRO', 'Protestant');

-- --------------------------------------------------------

--
-- Table structure for table `sto_room`
--

CREATE TABLE `sto_room` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) DEFAULT NULL,
  `code` varchar(128) NOT NULL,
  `name` varchar(255) NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_sites`
--

CREATE TABLE `sto_sites` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `host` varchar(255) DEFAULT NULL,
  `additional_host` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `status` varchar(60) DEFAULT 'active' COMMENT 'active, delete, banned, pending',
  `metadata` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sto_sites`
--

INSERT INTO `sto_sites` (`id`, `name`, `host`, `additional_host`, `token`, `status`, `metadata`) VALUES
(1, 'Default System Wide', '', NULL, NULL, 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sto_student`
--

CREATE TABLE `sto_student` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) DEFAULT 1,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'password_hash(\r\n    sha1(string $plain_text)\r\n)',
  `dissalow_admin` tinyint(1) NOT NULL DEFAULT 0,
  `generation` year(4) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `status` varchar(255) DEFAULT 'active' COMMENT 'active, delete, banned, pending',
  `religion` varchar(5) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_student_answer`
--

CREATE TABLE `sto_student_answer` (
  `id` bigint(20) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `question_id` bigint(20) NOT NULL,
  `student_exam_id` bigint(20) NOT NULL,
  `answer_alpha` char(1) DEFAULT NULL,
  `answer_essai` longtext DEFAULT NULL,
  `score` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_student_exam`
--

CREATE TABLE `sto_student_exam` (
  `id` bigint(20) NOT NULL,
  `exam_id` bigint(20) NOT NULL COMMENT 'This Must Be as `exam.id` but does not have relations for history record',
  `score` varchar(10) DEFAULT NULL COMMENT 'For Score Of Task & Test',
  `evaluator` bigint(20) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_student_logs`
--

CREATE TABLE `sto_student_logs` (
  `id` bigint(20) NOT NULL,
  `student_id` bigint(10) DEFAULT NULL,
  `type` varchar(255) NOT NULL COMMENT 'log type: login, logout, change_password, etc..',
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_student_meta`
--

CREATE TABLE `sto_student_meta` (
  `meta_id` bigint(20) NOT NULL,
  `student_id` bigint(10) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_student_online`
--

CREATE TABLE `sto_student_online` (
  `id` bigint(10) NOT NULL DEFAULT 0 COMMENT 'student.id',
  `online` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_online_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_subject`
--

CREATE TABLE `sto_subject` (
  `site_id` bigint(20) DEFAULT 1,
  `code` varchar(60) NOT NULL,
  `name` varchar(255) NOT NULL,
  `note` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_supervisor`
--

CREATE TABLE `sto_supervisor` (
  `id` bigint(20) NOT NULL,
  `site_id` bigint(20) DEFAULT 1,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position_code` varchar(40) NOT NULL,
  `disallow_admin` tinyint(1) NOT NULL DEFAULT 0,
  `join_date` date DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `role` varchar(255) DEFAULT NULL COMMENT 'superadmin, admin, teacher, invigilator, contributor, staff',
  `status` varchar(255) NOT NULL DEFAULT 'active' COMMENT 'active, delete, banned, pending',
  `religion` varchar(5) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_supervisor_logs`
--

CREATE TABLE `sto_supervisor_logs` (
  `id` bigint(20) NOT NULL,
  `supervisor_id` bigint(20) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT 'log type: login, logout, change_password, etc..',
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_supervisor_meta`
--

CREATE TABLE `sto_supervisor_meta` (
  `meta_id` bigint(20) NOT NULL,
  `supervisor_id` bigint(10) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_supervisor_online`
--

CREATE TABLE `sto_supervisor_online` (
  `id` bigint(10) NOT NULL DEFAULT 0 COMMENT 'supervisor.id',
  `online` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_online_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_supervisor_position`
--

CREATE TABLE `sto_supervisor_position` (
  `site_id` bigint(20) DEFAULT 1,
  `code` varchar(40) NOT NULL,
  `name` varchar(255) NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_translations`
--

CREATE TABLE `sto_translations` (
  `dictionary_code` varchar(128) NOT NULL,
  `iso_3` varchar(3) NOT NULL,
  `translation` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_translations_dictionary`
--

CREATE TABLE `sto_translations_dictionary` (
  `code` varchar(128) NOT NULL,
  `translate` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sto_translations_language`
--

CREATE TABLE `sto_translations_language` (
  `iso_3` varchar(3) NOT NULL,
  `iso_2` varchar(2) NOT NULL,
  `language_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sto_translations_language`
--

INSERT INTO `sto_translations_language` (`iso_3`, `iso_2`, `language_name`) VALUES
('abk', 'ab', 'Abkhazian'),
('aar', 'aa', 'Afar'),
('afr', 'af', 'Afrikaans'),
('aka', 'ak', 'Akan'),
('alb', 'sq', 'Albanian'),
('amh', 'am', 'Amharic'),
('ara', 'ar', 'Arabic'),
('arg', 'an', 'Aragonese'),
('arm', 'hy', 'Armenian'),
('asm', 'as', 'Assamese'),
('ava', 'av', 'Avaric'),
('ave', 'ae', 'Avestan'),
('aym', 'ay', 'Aymara'),
('aze', 'az', 'Azerbaijani'),
('bam', 'bm', 'Bambara'),
('bak', 'ba', 'Bashkir'),
('baq', 'eu', 'Basque'),
('bel', 'be', 'Belarusian'),
('ben', 'bn', 'Bengali'),
('bih', 'bh', 'Bihari'),
('bis', 'bi', 'Bislama'),
('bos', 'bs', 'Bosnian'),
('bre', 'br', 'Breton'),
('bul', 'bg', 'Bulgarian'),
('bur', 'my', 'Burmese'),
('cat', 'ca', 'Catalan'),
('khm', 'km', 'Central Khmer'),
('cha', 'ch', 'Chamorro'),
('che', 'ce', 'Chechen'),
('nya', 'ny', 'Chichewa'),
('chi', 'zh', 'Chinese'),
('chu', 'cu', 'Church Slavic'),
('chv', 'cv', 'Chuvash'),
('cor', 'kw', 'Cornish'),
('cos', 'co', 'Corsican'),
('cre', 'cr', 'Cree'),
('hrv', 'hr', 'Croatian'),
('cze', 'cs', 'Czech'),
('dan', 'da', 'Danish'),
('dut', 'nl', 'Dutch'),
('dzo', 'dz', 'Dzongkha'),
('eng', 'en', 'English'),
('epo', 'eo', 'Esperanto'),
('est', 'et', 'Estonian'),
('ewe', 'ee', 'Ewe'),
('fao', 'fo', 'Faroese'),
('fij', 'fj', 'Fijian'),
('fin', 'fi', 'Finnish'),
('fre', 'fr', 'French'),
('ful', 'ff', 'Fulah'),
('gla', 'gd', 'Gaelic'),
('glg', 'gl', 'Galician'),
('lug', 'lg', 'Ganda'),
('geo', 'ka', 'Georgian'),
('ger', 'de', 'German'),
('gre', 'el', 'Greek'),
('grn', 'gn', 'Guarani'),
('guj', 'gu', 'Gujarati'),
('hat', 'ht', 'Haitian'),
('hau', 'ha', 'Hausa'),
('heb', 'he', 'Hebrew'),
('her', 'hz', 'Herero'),
('hin', 'hi', 'Hindi'),
('hmo', 'ho', 'Hiri Motu'),
('hun', 'hu', 'Hungarian'),
('ice', 'is', 'Icelandic'),
('ido', 'io', 'Ido'),
('ibo', 'ig', 'Igbo'),
('ind', 'id', 'Indonesian'),
('ina', 'ia', 'Interlingua (International Auxiliary Language Association)'),
('ile', 'ie', 'Interlingue'),
('iku', 'iu', 'Inuktitut'),
('ipk', 'ik', 'Inupiaq'),
('gle', 'ga', 'Irish'),
('ita', 'it', 'Italian'),
('jpn', 'ja', 'Japanese'),
('jav', 'jv', 'Javanese'),
('kal', 'kl', 'Kalaallisut'),
('kan', 'kn', 'Kannada'),
('kau', 'kr', 'Kanuri'),
('kas', 'ks', 'Kashmiri'),
('kaz', 'kk', 'Kazakh'),
('kik', 'ki', 'Kikuyu'),
('kin', 'rw', 'Kinyarwanda'),
('kir', 'ky', 'Kirghiz'),
('kom', 'kv', 'Komi'),
('kon', 'kg', 'Kongo'),
('kor', 'ko', 'Korean'),
('kua', 'kj', 'Kuanyama'),
('kur', 'ku', 'Kurdish'),
('lao', 'lo', 'Lao'),
('lat', 'la', 'Latin'),
('lav', 'lv', 'Latvian'),
('lim', 'li', 'Limburgan'),
('lin', 'ln', 'Lingala'),
('lit', 'lt', 'Lithuanian'),
('lub', 'lu', 'Luba-Katanga'),
('ltz', 'lb', 'Luxembourgish'),
('mac', 'mk', 'Macedonian'),
('mlg', 'mg', 'Malagasy'),
('may', 'ms', 'Malay'),
('mal', 'ml', 'Malayalam'),
('div', 'dv', 'Maldivian'),
('mlt', 'mt', 'Maltese'),
('glv', 'gv', 'Manx'),
('mao', 'mi', 'Maori'),
('mar', 'mr', 'Marathi'),
('mah', 'mh', 'Marshallese'),
('mon', 'mn', 'Mongolian'),
('nau', 'na', 'Nauru'),
('nav', 'nv', 'Navajo'),
('ndo', 'ng', 'Ndonga'),
('nep', 'ne', 'Nepali'),
('nde', 'nd', 'North Ndebele'),
('sme', 'se', 'Northern Sami'),
('nor', 'no', 'Norwegian'),
('nob', 'nb', 'Norwegian Bokmål'),
('nno', 'nn', 'Norwegian Nynorsk'),
('iii', 'ii', 'Nuosu'),
('oci', 'oc', 'Occitan'),
('oji', 'oj', 'Ojibwa'),
('ori', 'or', 'Oriya'),
('orm', 'om', 'Oromo'),
('oss', 'os', 'Ossetic'),
('pli', 'pi', 'Pali'),
('pus', 'ps', 'Pashto'),
('per', 'fa', 'Persian'),
('pol', 'pl', 'Polish'),
('por', 'pt', 'Portuguese'),
('pan', 'pa', 'Punjabi'),
('que', 'qu', 'Quechua'),
('rum', 'ro', 'Romanian'),
('roh', 'rm', 'Romansh'),
('run', 'rn', 'Rundi'),
('rus', 'ru', 'Russian'),
('smo', 'sm', 'Samoan'),
('sag', 'sg', 'Sango'),
('san', 'sa', 'Sanskrit'),
('srd', 'sc', 'Sardinian'),
('srp', 'sr', 'Serbian'),
('sna', 'sn', 'Shona'),
('snd', 'sd', 'Sindhi'),
('sin', 'si', 'Sinhala'),
('slo', 'sk', 'Slovak'),
('slv', 'sl', 'Slovenian'),
('som', 'so', 'Somali'),
('sot', 'st', 'Sotho'),
('nbl', 'nr', 'South Ndebele'),
('spa', 'es', 'Spanish'),
('sun', 'su', 'Sundanese'),
('swa', 'sw', 'Swahili'),
('ssw', 'ss', 'Swati'),
('swe', 'sv', 'Swedish'),
('tgl', 'tl', 'Tagalog'),
('tah', 'ty', 'Tahitian'),
('tgk', 'tg', 'Tajik'),
('tam', 'ta', 'Tamil'),
('tat', 'tt', 'Tatar'),
('tel', 'te', 'Telugu'),
('tha', 'th', 'Thai'),
('tib', 'bo', 'Tibetan'),
('tir', 'ti', 'Tigrinya'),
('ton', 'to', 'Tonga (Tonga Islands)'),
('tso', 'ts', 'Tsonga'),
('tsn', 'tn', 'Tswana'),
('tur', 'tr', 'Turkish'),
('tuk', 'tk', 'Turkmen'),
('twi', 'tw', 'Twi'),
('uig', 'ug', 'Uighur'),
('ukr', 'uk', 'Ukrainian'),
('urd', 'ur', 'Urdu'),
('uzb', 'uz', 'Uzbek'),
('ven', 've', 'Venda'),
('vie', 'vi', 'Vietnamese'),
('vol', 'vo', 'Volapük'),
('wln', 'wa', 'Walloon'),
('wel', 'cy', 'Welsh'),
('fry', 'fy', 'Western Frisian'),
('wol', 'wo', 'Wolof'),
('xho', 'xh', 'Xhosa'),
('yid', 'yi', 'Yiddish'),
('yor', 'yo', 'Yoruba'),
('zha', 'za', 'Zhuang'),
('zul', 'zu', 'Zulu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sto_attachments`
--
ALTER TABLE `sto_attachments`
  ADD PRIMARY KEY (`id`,`path`(255)),
  ADD UNIQUE KEY `path_unique` (`path`) USING HASH,
  ADD KEY `name` (`name`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `attachment_to` (`attachment_to`),
  ADD KEY `attachment_to_id` (`attachment_to_id`),
  ADD KEY `path` (`path`(255),`attachment_to`);

--
-- Indexes for table `sto_classes`
--
ALTER TABLE `sto_classes`
  ADD PRIMARY KEY (`id`,`code`) USING BTREE,
  ADD UNIQUE KEY `code_site_id_unique` (`code`,`site_id`),
  ADD KEY `name` (`name`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `sto_classes_teacher`
--
ALTER TABLE `sto_classes_teacher`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `class_id_year_unique` (`class_id`,`year`) USING BTREE,
  ADD KEY `teacher` (`teacher`),
  ADD KEY `class_code` (`class_id`);

--
-- Indexes for table `sto_exam`
--
ALTER TABLE `sto_exam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_code` (`task_code`),
  ADD KEY `exam_code` (`exam_code`),
  ADD KEY `invigilator` (`invigilator`),
  ADD KEY `exam_status` (`exam_status`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sto_exam_classes_id`
--
ALTER TABLE `sto_exam_classes_id`
  ADD PRIMARY KEY (`exam_id`,`class_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `class_id` (`class_id`) USING BTREE;

--
-- Indexes for table `sto_exam_room_id`
--
ALTER TABLE `sto_exam_room_id`
  ADD PRIMARY KEY (`exam_id`,`room_id`),
  ADD UNIQUE KEY `room_id` (`room_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `sto_exam_student_assign`
--
ALTER TABLE `sto_exam_student_assign`
  ADD PRIMARY KEY (`exam_id`,`student_id`) USING BTREE,
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`student_id`) USING BTREE;

--
-- Indexes for table `sto_options`
--
ALTER TABLE `sto_options`
  ADD PRIMARY KEY (`id`,`site_id`) USING BTREE,
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD UNIQUE KEY `option_name_site_id` (`id`,`site_id`) USING BTREE,
  ADD KEY `sto_options_site_id` (`site_id`);

--
-- Indexes for table `sto_post`
--
ALTER TABLE `sto_post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`,`site_id`) USING BTREE,
  ADD KEY `title` (`title`),
  ADD KEY `author` (`author`),
  ADD KEY `type` (`type`),
  ADD KEY `status` (`status`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `sto_post_parent_id` (`parent_id`);

--
-- Indexes for table `sto_question`
--
ALTER TABLE `sto_question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `score` (`score_default`),
  ADD KEY `task_code` (`task_code`),
  ADD KEY `answer_alpha` (`answer_alpha`),
  ADD KEY `question_type` (`question_type`),
  ADD KEY `status` (`status`),
  ADD KEY `question_type_status` (`question_type`,`status`) USING BTREE;

--
-- Indexes for table `sto_question_choice`
--
ALTER TABLE `sto_question_choice`
  ADD PRIMARY KEY (`alpha`,`question_id`),
  ADD KEY `question_id_index` (`question_id`) USING BTREE;

--
-- Indexes for table `sto_question_task`
--
ALTER TABLE `sto_question_task`
  ADD PRIMARY KEY (`code`) USING BTREE,
  ADD UNIQUE KEY `code_subject_unique` (`code`,`subject_code`) USING BTREE,
  ADD UNIQUE KEY `code_by_site_id` (`site_id`,`code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `code` (`code`) USING BTREE,
  ADD KEY `name_created_by` (`name`,`created_by`) USING BTREE,
  ADD KEY `name` (`name`) USING BTREE,
  ADD KEY `subject_code` (`subject_code`) USING BTREE,
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `sto_religion`
--
ALTER TABLE `sto_religion`
  ADD PRIMARY KEY (`code`) USING BTREE,
  ADD UNIQUE KEY `name` (`name`,`site_id`) USING BTREE,
  ADD UNIQUE KEY `code` (`code`,`site_id`) USING BTREE;

--
-- Indexes for table `sto_room`
--
ALTER TABLE `sto_room`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_room_per_site` (`site_id`,`code`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `code` (`code`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `sto_sites`
--
ALTER TABLE `sto_sites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `host` (`host`) USING BTREE,
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `additional_host` (`additional_host`) USING BTREE,
  ADD UNIQUE KEY `host_additional` (`host`,`additional_host`) USING BTREE,
  ADD KEY `name` (`name`);

--
-- Indexes for table `sto_student`
--
ALTER TABLE `sto_student`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`,`site_id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`,`site_id`) USING BTREE,
  ADD KEY `name` (`full_name`),
  ADD KEY `religion` (`religion`),
  ADD KEY `gender` (`gender`),
  ADD KEY `dissalow_admin` (`dissalow_admin`),
  ADD KEY `registration_date` (`registration_date`) USING BTREE,
  ADD KEY `sto_student_site_id` (`site_id`),
  ADD KEY `status` (`status`) USING BTREE;

--
-- Indexes for table `sto_student_answer`
--
ALTER TABLE `sto_student_answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `answer_alpha` (`answer_alpha`) USING BTREE,
  ADD KEY `sto_student_answer_student_id` (`student_id`),
  ADD KEY `score` (`score`),
  ADD KEY `sto_student_answer_exam_id` (`student_exam_id`);

--
-- Indexes for table `sto_student_exam`
--
ALTER TABLE `sto_student_exam`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exam_id` (`exam_id`) USING BTREE,
  ADD KEY `evaluator` (`evaluator`) USING BTREE;

--
-- Indexes for table `sto_student_logs`
--
ALTER TABLE `sto_student_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `student_id` (`student_id`) USING BTREE;

--
-- Indexes for table `sto_student_meta`
--
ALTER TABLE `sto_student_meta`
  ADD PRIMARY KEY (`meta_id`),
  ADD UNIQUE KEY `meta_student_unique` (`meta_name`,`student_id`);

--
-- Indexes for table `sto_student_online`
--
ALTER TABLE `sto_student_online`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online` (`online`) USING BTREE;

--
-- Indexes for table `sto_subject`
--
ALTER TABLE `sto_subject`
  ADD PRIMARY KEY (`code`) USING BTREE,
  ADD KEY `name` (`name`),
  ADD KEY `code_site_id_unique` (`code`,`site_id`) USING BTREE,
  ADD KEY `sto_subject_site_id` (`site_id`);

--
-- Indexes for table `sto_supervisor`
--
ALTER TABLE `sto_supervisor`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`,`site_id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`,`site_id`) USING BTREE,
  ADD KEY `role` (`role`),
  ADD KEY `name` (`full_name`),
  ADD KEY `religion` (`religion`),
  ADD KEY `gender` (`gender`),
  ADD KEY `position_code` (`position_code`),
  ADD KEY `join_date` (`join_date`),
  ADD KEY `dissalow_admin` (`disallow_admin`),
  ADD KEY `site_id` (`site_id`) USING BTREE,
  ADD KEY `status` (`status`);

--
-- Indexes for table `sto_supervisor_logs`
--
ALTER TABLE `sto_supervisor_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `sto_supervisor_meta`
--
ALTER TABLE `sto_supervisor_meta`
  ADD PRIMARY KEY (`meta_id`),
  ADD UNIQUE KEY `supervisor_meta_name` (`supervisor_id`,`meta_name`) USING BTREE,
  ADD KEY `meta_name` (`meta_name`),
  ADD KEY `supervisor_id` (`supervisor_id`) USING BTREE;

--
-- Indexes for table `sto_supervisor_online`
--
ALTER TABLE `sto_supervisor_online`
  ADD UNIQUE KEY `supervisor_id` (`id`) USING BTREE,
  ADD KEY `online` (`online`);

--
-- Indexes for table `sto_supervisor_position`
--
ALTER TABLE `sto_supervisor_position`
  ADD PRIMARY KEY (`code`,`name`) USING BTREE,
  ADD UNIQUE KEY `code_site_id_unique` (`site_id`,`code`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `sto_translations`
--
ALTER TABLE `sto_translations`
  ADD PRIMARY KEY (`iso_3`,`dictionary_code`) USING BTREE,
  ADD KEY `language_code` (`dictionary_code`),
  ADD KEY `iso_3` (`iso_3`) USING BTREE;

--
-- Indexes for table `sto_translations_dictionary`
--
ALTER TABLE `sto_translations_dictionary`
  ADD PRIMARY KEY (`code`) USING BTREE,
  ADD UNIQUE KEY `translate` (`translate`) USING HASH;

--
-- Indexes for table `sto_translations_language`
--
ALTER TABLE `sto_translations_language`
  ADD PRIMARY KEY (`iso_3`,`iso_2`),
  ADD KEY `language_name` (`language_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sto_attachments`
--
ALTER TABLE `sto_attachments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_classes`
--
ALTER TABLE `sto_classes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_exam`
--
ALTER TABLE `sto_exam`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_options`
--
ALTER TABLE `sto_options`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_post`
--
ALTER TABLE `sto_post`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_question`
--
ALTER TABLE `sto_question`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_room`
--
ALTER TABLE `sto_room`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_sites`
--
ALTER TABLE `sto_sites`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_student`
--
ALTER TABLE `sto_student`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_student_answer`
--
ALTER TABLE `sto_student_answer`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_student_exam`
--
ALTER TABLE `sto_student_exam`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_student_logs`
--
ALTER TABLE `sto_student_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_student_meta`
--
ALTER TABLE `sto_student_meta`
  MODIFY `meta_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_supervisor`
--
ALTER TABLE `sto_supervisor`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_supervisor_logs`
--
ALTER TABLE `sto_supervisor_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sto_supervisor_meta`
--
ALTER TABLE `sto_supervisor_meta`
  MODIFY `meta_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sto_classes`
--
ALTER TABLE `sto_classes`
  ADD CONSTRAINT `sto_classes_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_classes_teacher`
--
ALTER TABLE `sto_classes_teacher`
  ADD CONSTRAINT `sto_classes_teacher_class_id` FOREIGN KEY (`class_id`) REFERENCES `sto_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_classes_teacher_teacher` FOREIGN KEY (`teacher`) REFERENCES `sto_supervisor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_exam`
--
ALTER TABLE `sto_exam`
  ADD CONSTRAINT `sto_exam_invigilator` FOREIGN KEY (`invigilator`) REFERENCES `sto_supervisor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_exam_task_code` FOREIGN KEY (`task_code`) REFERENCES `sto_question_task` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_exam_classes_id`
--
ALTER TABLE `sto_exam_classes_id`
  ADD CONSTRAINT `sto_exam_classes_id_class_id` FOREIGN KEY (`class_id`) REFERENCES `sto_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_exam_classes_id_exam_id` FOREIGN KEY (`exam_id`) REFERENCES `sto_exam` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_exam_room_id`
--
ALTER TABLE `sto_exam_room_id`
  ADD CONSTRAINT `sto_exam_room_id_exam_id` FOREIGN KEY (`exam_id`) REFERENCES `sto_exam` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_exam_room_id_room_id` FOREIGN KEY (`room_id`) REFERENCES `sto_room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_exam_student_assign`
--
ALTER TABLE `sto_exam_student_assign`
  ADD CONSTRAINT `sto_exam_student_assign_id` FOREIGN KEY (`exam_id`) REFERENCES `sto_exam` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_exam_student_assign_student_id` FOREIGN KEY (`student_id`) REFERENCES `sto_student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_options`
--
ALTER TABLE `sto_options`
  ADD CONSTRAINT `sto_options_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_post`
--
ALTER TABLE `sto_post`
  ADD CONSTRAINT `sto_post_author` FOREIGN KEY (`author`) REFERENCES `sto_supervisor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_post_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `sto_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_post_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_question`
--
ALTER TABLE `sto_question`
  ADD CONSTRAINT `sto_question_task_code` FOREIGN KEY (`task_code`) REFERENCES `sto_question_task` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_question_choice`
--
ALTER TABLE `sto_question_choice`
  ADD CONSTRAINT `sto_question_choice_question_id` FOREIGN KEY (`question_id`) REFERENCES `sto_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_question_task`
--
ALTER TABLE `sto_question_task`
  ADD CONSTRAINT `sto_question_task_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_question_task_subject_code` FOREIGN KEY (`subject_code`) REFERENCES `sto_subject` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_room`
--
ALTER TABLE `sto_room`
  ADD CONSTRAINT `sto_room_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_student`
--
ALTER TABLE `sto_student`
  ADD CONSTRAINT `sto_student_religion_code` FOREIGN KEY (`religion`) REFERENCES `sto_religion` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_student_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_student_answer`
--
ALTER TABLE `sto_student_answer`
  ADD CONSTRAINT `sto_student_answer_exam_id` FOREIGN KEY (`student_exam_id`) REFERENCES `sto_exam` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_student_answer_question_id` FOREIGN KEY (`question_id`) REFERENCES `sto_question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_student_answer_student_id` FOREIGN KEY (`student_id`) REFERENCES `sto_student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_student_exam`
--
ALTER TABLE `sto_student_exam`
  ADD CONSTRAINT `sto_student_exam_evaluator` FOREIGN KEY (`evaluator`) REFERENCES `sto_supervisor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_student_logs`
--
ALTER TABLE `sto_student_logs`
  ADD CONSTRAINT `sto_student_logs_student_id` FOREIGN KEY (`student_id`) REFERENCES `sto_student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_student_meta`
--
ALTER TABLE `sto_student_meta`
  ADD CONSTRAINT `sto_student_meta_student_id` FOREIGN KEY (`student_id`) REFERENCES `sto_student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_student_online`
--
ALTER TABLE `sto_student_online`
  ADD CONSTRAINT `sto_student_online_student_id` FOREIGN KEY (`id`) REFERENCES `sto_student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_subject`
--
ALTER TABLE `sto_subject`
  ADD CONSTRAINT `sto_subject_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_supervisor`
--
ALTER TABLE `sto_supervisor`
  ADD CONSTRAINT `sto_supervisor_religion_code` FOREIGN KEY (`religion`) REFERENCES `sto_religion` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_supervisor_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_supervisor_meta`
--
ALTER TABLE `sto_supervisor_meta`
  ADD CONSTRAINT `sto_supervisor_meta_supervisor_id` FOREIGN KEY (`supervisor_id`) REFERENCES `sto_supervisor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_supervisor_online`
--
ALTER TABLE `sto_supervisor_online`
  ADD CONSTRAINT `sto_supervisor_online_id` FOREIGN KEY (`id`) REFERENCES `sto_supervisor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sto_supervisor_position`
--
ALTER TABLE `sto_supervisor_position`
  ADD CONSTRAINT `sto_supervisor_position_site_id` FOREIGN KEY (`site_id`) REFERENCES `sto_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sto_translations`
--
ALTER TABLE `sto_translations`
  ADD CONSTRAINT `sto_translations_dictionary_code` FOREIGN KEY (`dictionary_code`) REFERENCES `sto_translations_dictionary` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sto_translations_iso_3` FOREIGN KEY (`iso_3`) REFERENCES `sto_translations_language` (`iso_3`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

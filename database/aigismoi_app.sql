-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 11, 2025 at 06:40 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aigismoi_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `album_drive_photos`
--

CREATE TABLE `album_drive_photos` (
  `id` bigint UNSIGNED NOT NULL,
  `album_id` bigint UNSIGNED NOT NULL,
  `file_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint UNSIGNED NOT NULL,
  `headline` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` text COLLATE utf8mb4_unicode_ci,
  `features` json DEFAULT NULL,
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gradient_from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gradient_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opacity` decimal(3,2) NOT NULL DEFAULT '0.75',
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `broadcast_histories`
--

CREATE TABLE `broadcast_histories` (
  `id` bigint UNSIGNED NOT NULL,
  `registration_id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `broadcast_templates`
--

CREATE TABLE `broadcast_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` json NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_post`
--

CREATE TABLE `category_post` (
  `post_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checkin_logs`
--

CREATE TABLE `checkin_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `registration_id` bigint UNSIGNED NOT NULL,
  `checkin_time` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collaborators`
--

CREATE TABLE `collaborators` (
  `id` bigint UNSIGNED NOT NULL,
  `collaborator_category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upload',
  `logo_url_remote` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collaborator_categories`
--

CREATE TABLE `collaborator_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'partner',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_sections`
--

CREATE TABLE `custom_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `section_template_id` bigint UNSIGNED NOT NULL,
  `content` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `name` json NOT NULL,
  `theme` json DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_paid_event` tinyint(1) NOT NULL DEFAULT '0',
  `description` json DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `venue` json DEFAULT NULL,
  `google_maps_iframe` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offline',
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_link` text COLLATE utf8mb4_unicode_ci,
  `meeting_info` json DEFAULT NULL,
  `daily_schedules` json DEFAULT NULL,
  `personnel` json DEFAULT NULL,
  `sponsors` json DEFAULT NULL,
  `field_config` json DEFAULT NULL,
  `quota` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `requires_account` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `visibility` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `external_registration_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_recordings` json DEFAULT NULL,
  `inquiry_form_id` bigint UNSIGNED DEFAULT NULL,
  `feedback_form_id` bigint UNSIGNED DEFAULT NULL,
  `confirmation_template_id` bigint UNSIGNED DEFAULT NULL,
  `is_feedback_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `invitation_letter_body` longtext COLLATE utf8mb4_unicode_ci,
  `invitation_letter_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invitation_files` json DEFAULT NULL,
  `invitation_wa_template` text COLLATE utf8mb4_unicode_ci,
  `invitation_email_subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invitation_email_body` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_agendas`
--

CREATE TABLE `event_agendas` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speaker` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_agenda_event`
--

CREATE TABLE `event_agenda_event` (
  `id` bigint UNSIGNED NOT NULL,
  `event_agenda_id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_email_templates`
--

CREATE TABLE `event_email_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `banner_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_programmes`
--

CREATE TABLE `event_programmes` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speaker` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_programme_event`
--

CREATE TABLE `event_programme_event` (
  `id` bigint UNSIGNED NOT NULL,
  `event_programme_id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exhibitor_attendee`
--

CREATE TABLE `exhibitor_attendee` (
  `id` bigint UNSIGNED NOT NULL,
  `exhibitor_id` bigint UNSIGNED NOT NULL,
  `attendee_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_exhibitors`
--

CREATE TABLE `favorite_exhibitors` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `exhibitor_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `is_loved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_forms`
--

CREATE TABLE `feedback_forms` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fields` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_submissions`
--

CREATE TABLE `feedback_submissions` (
  `id` bigint UNSIGNED NOT NULL,
  `feedback_form_id` bigint UNSIGNED NOT NULL,
  `registration_id` bigint UNSIGNED NOT NULL,
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_videos`
--

CREATE TABLE `gallery_videos` (
  `id` bigint UNSIGNED NOT NULL,
  `video_gallery_id` bigint UNSIGNED NOT NULL,
  `series_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `youtube_embed_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_forms`
--

CREATE TABLE `inquiry_forms` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fields` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_submissions`
--

CREATE TABLE `inquiry_submissions` (
  `id` bigint UNSIGNED NOT NULL,
  `inquiry_form_id` bigint UNSIGNED NOT NULL,
  `data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE `invitations` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `is_sent_email` tinyint(1) NOT NULL DEFAULT '0',
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `is_sent_whatsapp` tinyint(1) NOT NULL DEFAULT '0',
  `whatsapp_sent_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','confirmed','represented','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `responded_at` timestamp NULL DEFAULT NULL,
  `representative_data` json DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint UNSIGNED NOT NULL,
  `label` json NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_09_09_153812_create_permission_tables', 1),
(6, '2025_09_10_053220_create_pages_table', 1),
(7, '2025_09_10_062445_create_media_table', 1),
(8, '2025_09_10_062538_create_albums_table', 1),
(9, '2025_09_10_100801_create_events_table', 1),
(10, '2025_09_10_123959_create_registrations_table', 1),
(11, '2025_09_11_064509_create_inquiry_forms_table', 1),
(12, '2025_09_11_064514_create_inquiry_submissions_table', 1),
(13, '2025_09_11_065949_add_inquiry_form_id_to_events_table', 1),
(14, '2025_09_11_091254_refactor_registrations_for_standard_fields', 1),
(15, '2025_09_11_120518_create_notifications_table', 1),
(16, '2025_09_11_160724_add_details_to_events_table', 1),
(17, '2025_09_12_062552_create_posts_table', 1),
(18, '2025_09_12_075902_create_categories_table', 1),
(19, '2025_09_12_092806_add_details_to_posts_table', 1),
(20, '2025_09_12_132219_add_soft_deletes_to_posts_table', 1),
(21, '2025_09_12_171903_create_settings_table', 1),
(22, '2025_09_13_044053_add_user_id_to_registrations_table', 1),
(23, '2025_09_13_234325_add_field_config_to_events_table', 1),
(24, '2025_09_14_013304_add_profile_fields_to_users_table', 1),
(25, '2025_09_15_054100_create_section_templates_table', 1),
(26, '2025_09_15_151141_create_menu_items_table', 1),
(27, '2025_09_18_155402_add_online_event_fields_to_events_table', 1),
(28, '2025_09_19_055434_change_meeting_link_to_text_in_events_table', 1),
(29, '2025_09_19_064926_add_attendance_type_to_registrations_table', 1),
(30, '2025_09_19_093533_create_feedback_forms_table', 1),
(31, '2025_09_19_093542_create_feedback_submissions_table', 1),
(32, '2025_09_19_093549_add_feedback_fields_to_events_table', 1),
(33, '2025_09_19_130716_add_feedback_email_sent_at_to_registrations_table', 1),
(34, '2025_09_20_192859_create_broadcast_templates_table', 1),
(35, '2025_09_25_021638_add_uuid_to_users_table', 1),
(36, '2025_09_25_065401_create_exhibitor_attendee_table', 1),
(37, '2025_09_25_071330_add_description_to_users_table', 1),
(38, '2025_09_25_071915_add_logo_path_to_users_table', 1),
(39, '2025_09_27_004500_add_exhibitor_fields_to_users_table', 1),
(40, '2025_09_27_005457_add_social_media_links_to_users_table', 1),
(41, '2025_09_27_052747_add_booth_number_to_users_table', 1),
(42, '2025_09_27_061002_create_favorite_exhibitors_table', 1),
(43, '2025_09_27_070057_add_rating_and_love_to_favorite_exhibitors_table', 1),
(44, '2025_09_27_103426_add_banner_path_to_broadcast_templates_table', 1),
(45, '2025_09_27_104916_create_event_email_templates_table', 1),
(46, '2025_09_28_084203_add_confirmation_template_id_to_events_table', 1),
(47, '2025_09_28_110620_make_event_id_nullable_in_event_email_templates_table', 1),
(48, '2025_09_29_115545_create_jobs_table', 1),
(49, '2025_09_30_185152_add_profile_data_to_users_table', 1),
(50, '2025_10_01_231900_add_daily_schedules_to_events_table', 1),
(51, '2025_10_02_114513_add_rfid_tag_to_users_table', 1),
(52, '2025_10_02_164428_add_rfid_registered_at_to_registrations_table', 1),
(53, '2025_10_02_171953_create_checkin_logs_table', 1),
(54, '2025_10_03_021131_add_requires_account_to_events_table', 1),
(55, '2025_10_03_032521_update_youtube_recordings_structure_in_events_table', 1),
(56, '2025_10_04_022003_create_pending_broadcasts_table', 1),
(57, '2025_10_04_031731_add_progress_to_pending_broadcasts_table', 1),
(58, '2025_10_12_035350_create_welcome_sections_table', 1),
(59, '2025_10_12_043550_create_section_items_table', 1),
(60, '2025_10_13_183311_create_banners_table', 1),
(61, '2025_10_13_213127_add_gradient_columns_to_banners_table', 1),
(62, '2025_10_14_011458_add_opacity_to_banners_table', 1),
(63, '2025_10_14_203040_create_custom_sections_table', 1),
(64, '2025_10_14_203150_add_custom_section_id_to_welcome_sections_table', 1),
(65, '2025_10_15_121052_create_video_galleries_table', 1),
(66, '2025_10_15_121102_create_gallery_videos_table', 1),
(67, '2025_10_15_133750_add_location_to_menu_items_table', 1),
(68, '2025_10_17_021427_create_pending_event_broadcasts_table', 1),
(69, '2025_10_17_094840_create_broadcast_histories_table', 1),
(70, '2025_10_17_151019_add_position_to_banners_table', 1),
(71, '2025_10_18_220130_create_social_media_types_table', 1),
(72, '2025_10_18_220140_create_social_wall_items_table', 1),
(73, '2025_10_18_230211_alter_social_media_types_for_icon_class', 1),
(74, '2025_10_20_171249_create_category_post_table', 1),
(75, '2025_10_30_175742_add_url_to_banners_table', 1),
(76, '2025_11_01_043021_modify_type_enum_in_posts_table', 1),
(77, '2025_11_01_054003_add_kebijakan_to_posts_type_enum', 1),
(78, '2025_11_01_105358_add_slug_to_albums_table', 1),
(79, '2025_11_12_023920_add_google_maps_iframe_to_events_table', 1),
(80, '2025_11_17_114448_add_visibility_to_events_table', 1),
(81, '2025_11_17_163146_create_invitations_table', 1),
(82, '2025_11_17_225659_add_invitation_config_to_events_table', 1),
(83, '2025_11_18_000708_add_invitation_templates_to_events_table', 1),
(84, '2025_11_18_082701_create_finance_tables', 1),
(85, '2025_11_18_082802_create_ticketing_tables', 1),
(86, '2025_11_18_082829_create_commerce_tables', 1),
(87, '2025_11_18_082856_update_existing_tables_for_payment', 1),
(88, '2025_11_18_104559_change_payable_id_to_string_in_transactions_table', 1),
(89, '2025_11_21_014540_make_user_id_nullable_in_transactions_table', 1),
(90, '2025_11_23_003106_add_external_registration_link_to_events_table', 1),
(91, '2025_11_27_072745_create_event_agendas_table', 1),
(92, '2025_11_27_073517_create_event_programmes_table', 1),
(93, '2025_11_27_074056_make_events_optional_and_multiple_in_agendas_and_programmes', 1),
(94, '2025_11_28_040014_create_collaborators_tables', 1),
(95, '2025_12_03_111859_create_album_drive_photos_table', 1),
(96, '2025_12_03_131623_add_drive_id_to_posts_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `title` json NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` json NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_broadcasts`
--

CREATE TABLE `pending_broadcasts` (
  `id` bigint UNSIGNED NOT NULL,
  `template_id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `processed_count` int NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_event_broadcasts`
--

CREATE TABLE `pending_event_broadcasts` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `template_id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `progress` int NOT NULL DEFAULT '0',
  `total_recipients` int NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage pages', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(2, 'manage users', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(3, 'manage events', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(4, 'manage media', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(5, 'manage roles_permissions', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(6, 'checkin attendees', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(7, 'manage forms', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(8, 'manage news', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(9, 'manage categories', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(10, 'manage application settings', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(11, 'manage section templates', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(12, 'manage menus', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(13, 'manage welcome', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(14, 'manage broadcasts', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(15, 'send global broadcasts', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(16, 'manage social wall', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(17, 'manage own profile/booth', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(18, 'view registrant list', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(19, 'chat with attendees', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(20, 'export registrant data', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(21, 'use qr scanner', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(22, 'manage tenant users', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(23, 'manage tenant settings', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(24, 'manage tenant profile', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(25, 'manage products', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(26, 'manage orders', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(27, 'view sales report', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` json NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured_image_drive_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `subcategory_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('article','video','audio','press_release','kebijakan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'article',
  `content` json DEFAULT NULL,
  `media_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_favicon_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visibility_options` json DEFAULT NULL,
  `seo_meta` json DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(15,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_orders`
--

CREATE TABLE `product_orders` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `admin_fee` decimal(15,2) NOT NULL DEFAULT '0.00',
  `final_amount` decimal(15,2) NOT NULL,
  `status` enum('pending','paid','processing','shipped','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_order_items`
--

CREATE TABLE `product_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `product_order_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `price_at_purchase` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `ticket_tier_id` bigint UNSIGNED DEFAULT NULL,
  `payment_status` enum('unpaid','paid','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `total_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','confirmed','cancelled','attended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` json NOT NULL,
  `checked_in_at` timestamp NULL DEFAULT NULL,
  `rfid_registered_at` timestamp NULL DEFAULT NULL,
  `feedback_email_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `attendance_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(2, 'Administrator', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(3, 'Event Manager', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(4, 'Article Manager', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(5, 'Exhibitor', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(6, 'Tenant', 'web', '2025-12-11 06:39:35', '2025-12-11 06:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(1, 3),
(3, 3),
(4, 3),
(14, 3),
(8, 4),
(9, 4),
(16, 4),
(17, 5),
(18, 5),
(19, 5),
(20, 5),
(21, 5),
(24, 6),
(25, 6),
(26, 6),
(27, 6);

-- --------------------------------------------------------

--
-- Table structure for table `section_items`
--

CREATE TABLE `section_items` (
  `id` bigint UNSIGNED NOT NULL,
  `welcome_section_id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_templates`
--

CREATE TABLE `section_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `html_content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `css_content` text COLLATE utf8mb4_unicode_ci,
  `fields` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_media_types`
--

CREATE TABLE `social_media_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_class` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_wall_items`
--

CREATE TABLE `social_wall_items` (
  `id` bigint UNSIGNED NOT NULL,
  `social_media_type_id` bigint UNSIGNED NOT NULL,
  `embed_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_holder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','active','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_tiers`
--

CREATE TABLE `ticket_tiers` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `quota` int NOT NULL DEFAULT '0',
  `max_per_user` int NOT NULL DEFAULT '1',
  `sales_start_at` datetime DEFAULT NULL,
  `sales_end_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `payable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payable_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `midtrans_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','paid','failed','expired','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_instansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe_instansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanda_tangan` text COLLATE utf8mb4_unicode_ci,
  `profile_data` json DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfid_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_instansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booth_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_galleries`
--

CREATE TABLE `video_galleries` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('percentage','fixed_amount') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `usage_limit` int NOT NULL DEFAULT '0',
  `usage_per_user` int NOT NULL DEFAULT '1',
  `min_purchase_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `valid_from` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_usages`
--

CREATE TABLE `voucher_usages` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `transaction_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(15,2) NOT NULL,
  `used_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `welcome_sections`
--

CREATE TABLE `welcome_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `component` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `custom_section_id` bigint UNSIGNED DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `welcome_sections`
--

INSERT INTO `welcome_sections` (`id`, `name`, `component`, `custom_section_id`, `order`, `is_visible`, `created_at`, `updated_at`) VALUES
(1, '{\"en\":\"Main Banner\"}', 'banner', NULL, 1, 1, '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(2, '{\"en\":\"Upcoming Events\"}', 'events', NULL, 2, 1, '2025-12-11 06:39:35', '2025-12-11 06:39:35'),
(3, '{\"en\":\"Latest News\"}', 'news', NULL, 3, 1, '2025-12-11 06:39:35', '2025-12-11 06:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint UNSIGNED NOT NULL,
  `tenant_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account_holder` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('requested','approved','rejected','transferred') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'requested',
  `admin_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `proof_of_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `albums_slug_unique` (`slug`);

--
-- Indexes for table `album_drive_photos`
--
ALTER TABLE `album_drive_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `album_drive_photos_album_id_foreign` (`album_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `broadcast_histories`
--
ALTER TABLE `broadcast_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broadcast_histories_registration_id_foreign` (`registration_id`),
  ADD KEY `broadcast_histories_event_id_foreign` (`event_id`);

--
-- Indexes for table `broadcast_templates`
--
ALTER TABLE `broadcast_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broadcast_templates_event_id_foreign` (`event_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `category_post`
--
ALTER TABLE `category_post`
  ADD PRIMARY KEY (`post_id`,`category_id`),
  ADD KEY `category_post_category_id_foreign` (`category_id`);

--
-- Indexes for table `checkin_logs`
--
ALTER TABLE `checkin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `checkin_logs_registration_id_foreign` (`registration_id`);

--
-- Indexes for table `collaborators`
--
ALTER TABLE `collaborators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collaborators_collaborator_category_id_foreign` (`collaborator_category_id`);

--
-- Indexes for table `collaborator_categories`
--
ALTER TABLE `collaborator_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_sections`
--
ALTER TABLE `custom_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_sections_section_template_id_foreign` (`section_template_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `events_slug_unique` (`slug`),
  ADD KEY `events_inquiry_form_id_foreign` (`inquiry_form_id`),
  ADD KEY `events_feedback_form_id_foreign` (`feedback_form_id`),
  ADD KEY `events_confirmation_template_id_foreign` (`confirmation_template_id`);

--
-- Indexes for table `event_agendas`
--
ALTER TABLE `event_agendas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_agenda_event`
--
ALTER TABLE `event_agenda_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_agenda_event_event_agenda_id_foreign` (`event_agenda_id`),
  ADD KEY `event_agenda_event_event_id_foreign` (`event_id`);

--
-- Indexes for table `event_email_templates`
--
ALTER TABLE `event_email_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_email_templates_event_id_foreign` (`event_id`);

--
-- Indexes for table `event_programmes`
--
ALTER TABLE `event_programmes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_programme_event`
--
ALTER TABLE `event_programme_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_programme_event_event_programme_id_foreign` (`event_programme_id`),
  ADD KEY `event_programme_event_event_id_foreign` (`event_id`);

--
-- Indexes for table `exhibitor_attendee`
--
ALTER TABLE `exhibitor_attendee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `exhibitor_attendee_exhibitor_id_attendee_id_unique` (`exhibitor_id`,`attendee_id`),
  ADD KEY `exhibitor_attendee_attendee_id_foreign` (`attendee_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `favorite_exhibitors`
--
ALTER TABLE `favorite_exhibitors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `favorite_exhibitors_user_id_exhibitor_id_unique` (`user_id`,`exhibitor_id`),
  ADD KEY `favorite_exhibitors_exhibitor_id_foreign` (`exhibitor_id`);

--
-- Indexes for table `feedback_forms`
--
ALTER TABLE `feedback_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback_submissions`
--
ALTER TABLE `feedback_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_submissions_feedback_form_id_foreign` (`feedback_form_id`),
  ADD KEY `feedback_submissions_registration_id_foreign` (`registration_id`);

--
-- Indexes for table `gallery_videos`
--
ALTER TABLE `gallery_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gallery_videos_video_gallery_id_foreign` (`video_gallery_id`);

--
-- Indexes for table `inquiry_forms`
--
ALTER TABLE `inquiry_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inquiry_forms_slug_unique` (`slug`);

--
-- Indexes for table `inquiry_submissions`
--
ALTER TABLE `inquiry_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inquiry_submissions_inquiry_form_id_foreign` (`inquiry_form_id`);

--
-- Indexes for table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invitations_uuid_unique` (`uuid`),
  ADD KEY `invitations_event_id_foreign` (`event_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pending_broadcasts`
--
ALTER TABLE `pending_broadcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pending_broadcasts_template_id_foreign` (`template_id`);

--
-- Indexes for table `pending_event_broadcasts`
--
ALTER TABLE `pending_event_broadcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pending_event_broadcasts_event_id_foreign` (`event_id`),
  ADD KEY `pending_event_broadcasts_template_id_foreign` (`template_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `posts_slug_unique` (`slug`),
  ADD KEY `posts_user_id_foreign` (`user_id`),
  ADD KEY `posts_category_id_foreign` (`category_id`),
  ADD KEY `posts_subcategory_id_foreign` (`subcategory_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `product_orders`
--
ALTER TABLE `product_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_orders_user_id_foreign` (`user_id`),
  ADD KEY `product_orders_tenant_id_foreign` (`tenant_id`);

--
-- Indexes for table `product_order_items`
--
ALTER TABLE `product_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_order_items_product_order_id_foreign` (`product_order_id`),
  ADD KEY `product_order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registrations_event_id_email_unique` (`event_id`,`email`),
  ADD UNIQUE KEY `registrations_uuid_unique` (`uuid`),
  ADD KEY `registrations_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `section_items`
--
ALTER TABLE `section_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_items_welcome_section_id_foreign` (`welcome_section_id`);

--
-- Indexes for table `section_templates`
--
ALTER TABLE `section_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_templates_slug_unique` (`slug`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `social_media_types`
--
ALTER TABLE `social_media_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_media_types_name_unique` (`name`);

--
-- Indexes for table `social_wall_items`
--
ALTER TABLE `social_wall_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `social_wall_items_social_media_type_id_foreign` (`social_media_type_id`),
  ADD KEY `social_wall_items_user_id_foreign` (`user_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_slug_unique` (`slug`),
  ADD KEY `tenants_user_id_foreign` (`user_id`);

--
-- Indexes for table `ticket_tiers`
--
ALTER TABLE `ticket_tiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_tiers_event_id_foreign` (`event_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_payable_type_payable_id_index` (`payable_type`,`payable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `users_rfid_tag_unique` (`rfid_tag`);

--
-- Indexes for table `video_galleries`
--
ALTER TABLE `video_galleries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vouchers_code_unique` (`code`),
  ADD KEY `vouchers_event_id_foreign` (`event_id`);

--
-- Indexes for table `voucher_usages`
--
ALTER TABLE `voucher_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voucher_usages_voucher_id_foreign` (`voucher_id`),
  ADD KEY `voucher_usages_user_id_foreign` (`user_id`);

--
-- Indexes for table `welcome_sections`
--
ALTER TABLE `welcome_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `welcome_sections_custom_section_id_foreign` (`custom_section_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `album_drive_photos`
--
ALTER TABLE `album_drive_photos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `broadcast_histories`
--
ALTER TABLE `broadcast_histories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `broadcast_templates`
--
ALTER TABLE `broadcast_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checkin_logs`
--
ALTER TABLE `checkin_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collaborators`
--
ALTER TABLE `collaborators`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collaborator_categories`
--
ALTER TABLE `collaborator_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_sections`
--
ALTER TABLE `custom_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_agendas`
--
ALTER TABLE `event_agendas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_agenda_event`
--
ALTER TABLE `event_agenda_event`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_email_templates`
--
ALTER TABLE `event_email_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_programmes`
--
ALTER TABLE `event_programmes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_programme_event`
--
ALTER TABLE `event_programme_event`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exhibitor_attendee`
--
ALTER TABLE `exhibitor_attendee`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorite_exhibitors`
--
ALTER TABLE `favorite_exhibitors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_forms`
--
ALTER TABLE `feedback_forms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_submissions`
--
ALTER TABLE `feedback_submissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_videos`
--
ALTER TABLE `gallery_videos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry_forms`
--
ALTER TABLE `inquiry_forms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry_submissions`
--
ALTER TABLE `inquiry_submissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_broadcasts`
--
ALTER TABLE `pending_broadcasts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_event_broadcasts`
--
ALTER TABLE `pending_event_broadcasts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_order_items`
--
ALTER TABLE `product_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `section_items`
--
ALTER TABLE `section_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `section_templates`
--
ALTER TABLE `section_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_media_types`
--
ALTER TABLE `social_media_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_wall_items`
--
ALTER TABLE `social_wall_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_tiers`
--
ALTER TABLE `ticket_tiers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_galleries`
--
ALTER TABLE `video_galleries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher_usages`
--
ALTER TABLE `voucher_usages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `welcome_sections`
--
ALTER TABLE `welcome_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `album_drive_photos`
--
ALTER TABLE `album_drive_photos`
  ADD CONSTRAINT `album_drive_photos_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `broadcast_histories`
--
ALTER TABLE `broadcast_histories`
  ADD CONSTRAINT `broadcast_histories_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `broadcast_histories_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `broadcast_templates`
--
ALTER TABLE `broadcast_templates`
  ADD CONSTRAINT `broadcast_templates_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_post`
--
ALTER TABLE `category_post`
  ADD CONSTRAINT `category_post_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_post_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `checkin_logs`
--
ALTER TABLE `checkin_logs`
  ADD CONSTRAINT `checkin_logs_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `collaborators`
--
ALTER TABLE `collaborators`
  ADD CONSTRAINT `collaborators_collaborator_category_id_foreign` FOREIGN KEY (`collaborator_category_id`) REFERENCES `collaborator_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_sections`
--
ALTER TABLE `custom_sections`
  ADD CONSTRAINT `custom_sections_section_template_id_foreign` FOREIGN KEY (`section_template_id`) REFERENCES `section_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_confirmation_template_id_foreign` FOREIGN KEY (`confirmation_template_id`) REFERENCES `event_email_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_feedback_form_id_foreign` FOREIGN KEY (`feedback_form_id`) REFERENCES `feedback_forms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_inquiry_form_id_foreign` FOREIGN KEY (`inquiry_form_id`) REFERENCES `inquiry_forms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_agenda_event`
--
ALTER TABLE `event_agenda_event`
  ADD CONSTRAINT `event_agenda_event_event_agenda_id_foreign` FOREIGN KEY (`event_agenda_id`) REFERENCES `event_agendas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_agenda_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_email_templates`
--
ALTER TABLE `event_email_templates`
  ADD CONSTRAINT `event_email_templates_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_programme_event`
--
ALTER TABLE `event_programme_event`
  ADD CONSTRAINT `event_programme_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_programme_event_event_programme_id_foreign` FOREIGN KEY (`event_programme_id`) REFERENCES `event_programmes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exhibitor_attendee`
--
ALTER TABLE `exhibitor_attendee`
  ADD CONSTRAINT `exhibitor_attendee_attendee_id_foreign` FOREIGN KEY (`attendee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exhibitor_attendee_exhibitor_id_foreign` FOREIGN KEY (`exhibitor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorite_exhibitors`
--
ALTER TABLE `favorite_exhibitors`
  ADD CONSTRAINT `favorite_exhibitors_exhibitor_id_foreign` FOREIGN KEY (`exhibitor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_exhibitors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_submissions`
--
ALTER TABLE `feedback_submissions`
  ADD CONSTRAINT `feedback_submissions_feedback_form_id_foreign` FOREIGN KEY (`feedback_form_id`) REFERENCES `feedback_forms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_submissions_registration_id_foreign` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery_videos`
--
ALTER TABLE `gallery_videos`
  ADD CONSTRAINT `gallery_videos_video_gallery_id_foreign` FOREIGN KEY (`video_gallery_id`) REFERENCES `video_galleries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiry_submissions`
--
ALTER TABLE `inquiry_submissions`
  ADD CONSTRAINT `inquiry_submissions_inquiry_form_id_foreign` FOREIGN KEY (`inquiry_form_id`) REFERENCES `inquiry_forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pending_broadcasts`
--
ALTER TABLE `pending_broadcasts`
  ADD CONSTRAINT `pending_broadcasts_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `event_email_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pending_event_broadcasts`
--
ALTER TABLE `pending_event_broadcasts`
  ADD CONSTRAINT `pending_event_broadcasts_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pending_event_broadcasts_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `event_email_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_orders`
--
ALTER TABLE `product_orders`
  ADD CONSTRAINT `product_orders_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`),
  ADD CONSTRAINT `product_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `product_order_items`
--
ALTER TABLE `product_order_items`
  ADD CONSTRAINT `product_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_order_items_product_order_id_foreign` FOREIGN KEY (`product_order_id`) REFERENCES `product_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_items`
--
ALTER TABLE `section_items`
  ADD CONSTRAINT `section_items_welcome_section_id_foreign` FOREIGN KEY (`welcome_section_id`) REFERENCES `welcome_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `social_wall_items`
--
ALTER TABLE `social_wall_items`
  ADD CONSTRAINT `social_wall_items_social_media_type_id_foreign` FOREIGN KEY (`social_media_type_id`) REFERENCES `social_media_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `social_wall_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_tiers`
--
ALTER TABLE `ticket_tiers`
  ADD CONSTRAINT `ticket_tiers_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `vouchers_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `voucher_usages`
--
ALTER TABLE `voucher_usages`
  ADD CONSTRAINT `voucher_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `voucher_usages_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`);

--
-- Constraints for table `welcome_sections`
--
ALTER TABLE `welcome_sections`
  ADD CONSTRAINT `welcome_sections_custom_section_id_foreign` FOREIGN KEY (`custom_section_id`) REFERENCES `custom_sections` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

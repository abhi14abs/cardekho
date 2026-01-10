/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.42 : Database - car_website
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`car_website` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `car_website`;

/*Table structure for table `admin_users` */

DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `admin_users` */

insert  into `admin_users`(`id`,`username`,`password_hash`,`email`,`created_at`) values 
(1,'admin','$2y$12$e9g5zpygVWsUXCwtmq8u1.ZhRXG7vbuVru8VNJSPEydJeLkbx7LmS','admin@carsdekho.com','2026-01-09 22:28:15');

/*Table structure for table `cars` */

DROP TABLE IF EXISTS `cars`;

CREATE TABLE `cars` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand` varchar(50) NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(300) DEFAULT NULL,
  `category` enum('most_searched','latest','hatchback','sadan','suv') DEFAULT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `cars` */

insert  into `cars`(`id`,`brand`,`model`,`year`,`price`,`image_url`,`category`,`description`,`is_active`,`created_at`,`updated_at`) values 
(1,'Maruti Suzuki','Swift',2023,650000.00,'uploads/cars/1768062457_35cdc10ada922e4146d663ef36869c70.jpg','most_searched','The new Swift comes with a sporty design and advanced features.',1,'2026-01-10 21:47:46','2026-01-10 22:16:35'),
(2,'Hyundai','Creta',2024,1100000.00,'uploads/cars/1768062540_553730-middle.png','most_searched','The ultimate SUV with premium interiors and powerful engine options.',1,'2026-01-10 21:47:46','2026-01-10 21:59:00'),
(3,'Tata','Nexon',2023,800000.00,'uploads/cars/1768062595_OIP.webp','most_searched','5-star safety rated compact SUV with dynamic performance.',1,'2026-01-10 21:47:46','2026-01-10 21:59:55'),
(4,'Mahindra','Thar',2023,1400000.00,'uploads/cars/1768062703_desktop-wallpaper-mahindra-thar-2020-mahindra-thar-2021.jpg','most_searched','Explore the impossible with the legendary off-roader.',1,'2026-01-10 21:47:46','2026-01-10 22:01:43'),
(5,'Honda','City',2024,1200000.00,'uploads/cars/1768062761_Honda-City-PNG-Pic-Background.png','latest','Experience supreme comfort and elegance with the new City.',1,'2026-01-10 21:47:46','2026-01-10 22:02:41'),
(6,'Kia','Seltos',2024,1090000.00,'uploads/cars/1768062811_OIP (1).webp','latest','Badass by design. The new Seltos is here to rule the roads.',1,'2026-01-10 21:47:46','2026-01-10 22:03:31'),
(7,'Toyota','Fortuner',2023,3500000.00,'uploads/cars/1768063032_1776011.jpg','most_searched','The power to lead. Unmatched dominance on all terrains.',1,'2026-01-10 21:47:46','2026-01-10 22:15:56'),
(8,'BMW','3 Series',2023,4500000.00,'uploads/cars/1768063197_1615926.jpg','latest','Sheer driving pleasure. Luxury meets performance.',1,'2026-01-10 21:47:46','2026-01-10 22:16:00');

/*Table structure for table `customer_responses` */

DROP TABLE IF EXISTS `customer_responses`;

CREATE TABLE `customer_responses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `car_types` json NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `customer_responses` */

insert  into `customer_responses`(`id`,`name`,`phone`,`email`,`address`,`car_types`,`created_at`) values 
(1,'Abhishek Singh','8887745734','abhi14abs@gmail.com','ewarsdf8yui','[\"Sadan\"]','2026-01-09 23:12:10'),
(2,'Abhishek Singh','8887745734','abhi14abs@gmail.com','juyufjh','[\"Hatchback\"]','2026-01-09 23:14:18');

/*Table structure for table `website_content` */

DROP TABLE IF EXISTS `website_content`;

CREATE TABLE `website_content` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section` varchar(50) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `content` text,
  `image_url` varchar(300) DEFAULT NULL,
  `link_url` varchar(300) DEFAULT NULL,
  `display_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `website_content` */

insert  into `website_content`(`id`,`section`,`title`,`content`,`image_url`,`link_url`,`display_order`,`is_active`,`created_at`,`updated_at`) values 
(1,'banner','Find Your Perfect Car','Browse through thousands of new and used cars with the best deals','uploads/content/1768063400_Cars-Full-HD-Free-Download-Wallpapers.jpg','',0,1,'2026-01-10 20:13:38','2026-01-10 22:13:20'),
(2,'header',NULL,'Call us: +91 98765 43210',NULL,NULL,0,1,'2026-01-10 20:13:38','2026-01-10 20:13:38'),
(3,'header',NULL,'support@cardekho.com',NULL,NULL,0,1,'2026-01-10 20:13:38','2026-01-10 20:13:38');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

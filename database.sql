-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: mobilegear
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `mobilegear`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `mobilegear` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `mobilegear`;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orderitems_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,45,1,2999.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,2999.00,'pending','abs','2026-10-03 17:25:32');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT 'placeholder.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Apple Leather Case','Back Cover','Apple','Official premium tanned leather finish.',5900.00,'https://m.media-amazon.com/images/I/61YVCxpFlxL._AC_SL640_.jpg','2026-10-03 16:13:54'),(2,'Apple Clear Case with MagSafe','Back Cover','Apple','Clear polycarbonate case with MagSafe support.',4900.00,'https://cdn.mwave.com.au/images/400/apple_iphone_16_pro_max_clear_case_with_magsafe_ac78381_34180.jpg','2026-10-03 16:13:54'),(3,'Spigen Rugged Armor','Back Cover','Spigen','Drop-tested rugged protection.',1800.00,'https://cdn.shopify.com/s/files/1/0808/0067/files/ld_ip15_ruggedarmor_magfit_02.jpg?v=1699904333','2026-10-03 16:13:54'),(4,'Ringke Fusion Clear Case','Back Cover','Ringke','Minimalist clear back cover.',999.00,'https://m.media-amazon.com/images/I/41gjT01KOCL._SL500_.jpg','2026-10-03 16:13:54'),(5,'Generic Soft TPU Case','Back Cover','Generic','Basic soft TPU protection.',299.00,'https://casebuddy.com/cdn/shop/files/iphone-15-transparent-soft-tpu-silicone-cover-casebuddy-australia-1.jpg?v=1715686868&width=1000','2026-10-03 16:13:54'),(6,'dbrand Teardown Skin','Skin','dbrand','See-through internal component look.',2499.00,'https://cdn.db.io/images/dlg3xw1d/production/5761b9500e6d01a3e9ac7801d905c05826893e70-2048x1366.jpg','2026-10-03 16:13:54'),(7,'dbrand Leather Texture Skin','Skin','dbrand','Faux leather premium feel.',1999.00,'https://static0.anpoimages.com/wordpress/wp-content/uploads/2021/11/dbrand-leather-skin-hero.png','2026-10-03 16:13:54'),(8,'Slickwraps Carbon Fiber','Skin','Slickwraps','Textured carbon fiber finish.',1299.00,'https://www.slickwraps.com/cdn/shop/products/iphone-11-pro-carbon-series-skins-slickwraps-7.jpg?v=1670596681&width=800','2026-10-03 16:13:54'),(9,'Basic Matte Black Skin','Skin','dbrand','Precision cut matte black texture.',599.00,'https://www.decalz.in/cdn/shop/files/iphone13ProMatrixskins.jpg?v=1742807629&width=800','2026-10-03 16:13:54'),(10,'Generic Clear Back Film','Skin','Generic','Basic clear back protector film.',199.00,'https://worthwrap.in/cdn/shop/files/Worthwrap_Transparent_Mobile_Skin-2.webp?v=1755872335&width=1680','2026-10-03 16:13:54'),(11,'Apple 35W Dual USB-C Port Adapter','Charger','Apple','Official fast charging wall adapter.',5800.00,'https://m.media-amazon.com/images/I/51XUDlOy1bL.jpg_BO30,255,255,255_UF900,850_SR1910,1000,0,C_QL100_.jpg','2026-10-03 16:13:54'),(12,'Anker 20W Nano Charger','Charger','Anker','Compact fast charger.',1499.00,'https://apos.audio/cdn/shop/files/apos-audio-anker-accessory-anker-nano-charger-20w-black-stone-1215454419.png?v=1767840548&width=1600','2026-10-03 16:13:54'),(13,'Apple MagSafe Battery Pack','Power Bank','Apple','Magnetic wireless battery pack.',8900.00,'https://techjourneyman.com/img/blog/magsafe-battery-pack-standalone.webp','2026-10-03 16:13:54'),(14,'Anker PowerCore 10000','Power Bank','Anker','High-capacity wired power bank.',2499.00,'https://technianews.com/wp-content/uploads/2024/06/1719241865_Anker-PowerCore-10K.jpg','2026-10-03 16:13:54'),(15,'Apple AirPods (2nd Gen)','Audio','Apple','Wireless earbuds.',12900.00,'https://m.media-amazon.com/images/I/41tTVEPFRoL.jpg_BO30,255,255,255_UF750,750_SR1910,1000,0,C_QL100_.jpg','2026-10-03 16:13:54'),(16,'Samsung Smart View Wallet Case','Back Cover','Samsung','Protective cover with display window.',4999.00,'https://images.samsung.com/is/image/samsung/p6pim/de/ef-zs711cwegww/gallery/de-galaxy-s23-fe-smart-view-wallet-case-zs711-ef-zs711cwegww-538611505?$Q90_1368_1094_F_JPG$','2026-10-03 16:13:54'),(17,'Samsung Silicone Grip Case','Back Cover','Samsung','Smooth silicone with secure strap.',3499.00,'https://images.samsung.com/is/image/samsung/p6pim/fi/2302/gallery/fi-galaxy-s23-ultra-silicone-grip-case-ef-gs918-ef-gs918tbegww-534766621?$1164_776_PNG$','2026-10-03 16:13:54'),(18,'Spigen Tough Armor','Back Cover','Spigen','Dual-layer drop protection.',1999.00,'https://www.spigen.com/cdn/shop/files/detail_web_sp623_tougharmor_grn_01.jpg?v=1745277838&width=1946','2026-10-03 16:13:54'),(19,'Ringke Onyx','Back Cover','Ringke','Textured back cover.',899.00,'https://www.ringkestore.com/cdn/shop/files/GS25FE_ONX_BK_Main.jpg?crop=region&crop_height=2688&crop_left=0&crop_top=218&crop_width=2688&v=1758136759&width=2688','2026-10-03 16:13:54'),(20,'Generic Clear Bumper Case','Back Cover','Generic','Basic clear bumper.',249.00,'https://m.media-amazon.com/images/I/71QjZuZQuGL._AC_SL3840_.jpg','2026-10-03 16:13:54'),(21,'dbrand Glitch Camo Skin','Skin','dbrand','Unique digital camo pattern.',2499.00,'https://m.media-amazon.com/images/I/71nPm2hzpmL._AC_SL2048_.jpg','2026-10-03 16:13:54'),(22,'dbrand Swarm Texture Skin','Skin','dbrand','Hexagonal textured grip skin.',1999.00,'https://static.beebom.com/wp-content/uploads/2019/09/2.-Signature-Swarm.jpg?quality=75&strip=all','2026-10-03 16:13:54'),(23,'Slickwraps Brushed Metal','Skin','Slickwraps','Industrial metallic finish.',1299.00,'https://www.slickwraps.com/cdn/shop/files/Metal_Steel_9da49d50-ddd3-496b-addc-24bd0532b33c.jpg?v=1711894736&width=576','2026-10-03 16:13:54'),(24,'Basic Matte White Skin','Skin','dbrand','Clean minimalist white wrap.',599.00,'https://rukminim2.flixcart.com/image/530/710/xif0q/mobile-skin/m/d/7/flpkt8394-glossy-white-skin-for-back-sides-compatible-with-yes-original-imah4gx9yuhs6pc7.jpeg?q=90','2026-10-03 16:13:54'),(25,'Generic Carbon Fiber Sticker','Skin','Generic','Basic carbon fiber texture.',199.00,'https://rukminim2.flixcart.com/image/530/710/jxgflow0/screen-guard/back-screen-guard/8/6/y/case-creation-back-carbonn-sticker-251-original-imafhwefdbtzkpyy.jpeg?q=90','2026-10-03 16:13:54'),(26,'Samsung 45W Super Fast Wall Charger','Charger','Samsung','Official super fast wall charger.',3499.00,'https://images.samsung.com/is/image/samsung/p6pim/hu/ep-t4511nbegeu/gallery/hu-45w-power-adapter-ep-t4511-551564-ep-t4511nbegeu-547146232?$Q90_1368_1094_F_JPG$','2026-10-03 16:13:54'),(27,'Anker 25W Wall Charger','Charger','Anker','Compact fast charger.',1299.00,'https://api2.zoomit.ir/media/anker-312-a2642-67e22784750a80f4f1e038d2?w=1920&q=75','2026-10-03 16:13:54'),(28,'Samsung Wireless Battery Pack 10,000mAh','Power Bank','Samsung','Magnetic/wireless charging capable.',4999.00,'https://i5.walmartimages.com/asr/f53f2598-c2c2-403d-8ccb-3759ccf893bc.1e2299c901edff2389eb35d54ea7c8f8.jpeg','2026-10-03 16:13:54'),(29,'Anker PowerCore 10000','Power Bank','Anker','High-capacity wired power bank.',2499.00,'https://assets.gadgetandgear.com/upload/product/20230504_1683189719_951231.jpeg','2026-10-03 16:13:54'),(30,'Samsung Galaxy Buds FE','Audio','Samsung','Immersive audio and noise cancellation.',8999.00,'https://images.samsung.com/is/image/samsung/p6pim/br/sm-r400nzapzto/gallery/br-galaxy-buds-fe-sm-r400nzapzto-538446335?$Q90_1368_1094_F_JPG$','2026-10-03 16:13:54'),(31,'JBL Charge 5','Speaker','JBL','Waterproof portable Bluetooth speaker.',12999.00,'https://m.media-amazon.com/images/I/71K3xqapRCL._AC_SL400_.jpg','2026-10-03 16:13:54'),(32,'Sony SRS-XE200','Speaker','Sony','Durable outdoor Bluetooth speaker.',8990.00,'https://m.media-amazon.com/images/I/81XL9aCyQ2L._AC_US500_.jpg','2026-10-03 16:13:54'),(33,'boAt Stone 1200','Speaker','boAt','Powerful portable speaker with RGB lights.',5999.00,'https://m.media-amazon.com/images/I/41EgfIMKJZL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(34,'JBL Clip 5','Speaker','JBL','Ultra-portable speaker with carabiner.',4499.00,'https://m.media-amazon.com/images/I/71HdLDJEEUL._SL1500_.jpg','2026-10-03 16:13:54'),(35,'Sony SRS-XB100','Speaker','Sony','Compact speaker with extra bass.',3990.00,'https://m.media-amazon.com/images/I/41-usn6CObL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(36,'boAt Stone 350','Speaker','boAt','Lightweight cylinder travel speaker.',1499.00,'https://m.media-amazon.com/images/I/41Hhj9eGdJL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(37,'Fitbit Charge 6','Smart Watch','Fitbit','Advanced health and fitness tracker.',14990.00,'https://m.media-amazon.com/images/I/31Vb8MtZZ9L._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(38,'Amazfit GTR 4','Smart Watch','Amazfit','Long battery life and classic design.',14900.00,'https://m.media-amazon.com/images/I/414WIlQMvaL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(39,'Titan Smart 3','Smart Watch','Titan','Premium smartwatch with AMOLED display.',11995.00,'https://m.media-amazon.com/images/I/410M25g-LkL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(40,'Galaxy Watch 4 (Bluetooth)','Smart Watch','Samsung','Advanced fitness and wellness tracking.',9999.00,'https://m.media-amazon.com/images/I/318tbKdpunL._SX300_SY300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(41,'Redmi Watch 4','Smart Watch','Xiaomi','Large display and long battery life.',8999.00,'https://m.media-amazon.com/images/I/418EBXEZJDL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(42,'Noise ColorFit Ultra 3','Smart Watch','Noise','Premium design with clear display.',5999.00,'https://m.media-amazon.com/images/I/51cV0jzFrSL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(43,'Fire-Boltt Invincible Plus','Smart Watch','Fire-Boltt','Feature-rich budget smartwatch.',4499.00,'https://m.media-amazon.com/images/I/41GrO5Kgk4L._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(44,'Fastrack Limitless FS1','Smart Watch','Fastrack','Trendy smartwatch for youth.',3999.00,'https://m.media-amazon.com/images/I/41+p812eJ-L._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54'),(45,'Noise ColorFit Pro 5','Smart Watch','Noise','Affordable smartwatch with essential features.',2999.00,'https://m.media-amazon.com/images/I/61uZIlqWxKL._SX522_.jpg','2026-10-03 16:13:54'),(46,'boAt Xtend Pro','Smart Watch','boAt','Affordable smartwatch with Bluetooth calling.',1499.00,'https://m.media-amazon.com/images/I/41RAGSyYLaL._SY300_SX300_QL70_FMwebp_.jpg','2026-10-03 16:13:54');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@mobilegear.com',NULL,NULL,NULL,NULL,NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2026-10-03 16:13:54'),(3,'KashyapGajjar','abc@123.gmail.com',NULL,NULL,NULL,NULL,NULL,'$2y$10$0zEuXm.2YXw4KopXW/m0DukKk8KgbDbJyr6hxvO9L/iaLXsZ1QGgK','customer','2026-10-03 17:25:19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-03 23:21:58

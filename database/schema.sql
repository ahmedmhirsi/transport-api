    CREATE DATABASE IF NOT EXISTS reservation_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reservation_api;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `webhook_events`;
DROP TABLE IF EXISTS `webhooks`;
DROP TABLE IF EXISTS `api_keys`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `transport_requests`;
DROP TABLE IF EXISTS `trajets`;
DROP TABLE IF EXISTS `vehicles_park`;
DROP TABLE IF EXISTS `transport_providers`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `rate_limits`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','employee','manager') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transport_providers` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_name` VARCHAR(100) NULL,
  `contact_name` VARCHAR(100) NULL,
  `address` VARCHAR(100) NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vehicles_park` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `provider_id` INT NULL,
  `immatriculation` VARCHAR(100) NULL,
  `location` VARCHAR(255) NULL,
  `type` VARCHAR(100) NULL,
  `model` VARCHAR(100) NULL,
  `capacity` INT NULL,
  FOREIGN KEY (`provider_id`) REFERENCES `transport_providers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_provider_id ON vehicles_park(provider_id);

CREATE TABLE `trajets` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `provider_id` INT NULL,
  `vehicle_id` INT NULL,
  `departure` VARCHAR(100) NULL,
  `destination` VARCHAR(100) NULL,
  `departure_date` DATE NULL,
  `departure_time` TIME NULL,
  `arrival_time` TIME NULL,
  `capacity` INT NULL,
  `available_seats` INT NULL,
  `status` ENUM('active','completed','cancelled') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`provider_id`) REFERENCES `transport_providers`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles_park`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transport_requests` (
  `request_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `manager_email` VARCHAR(255) NOT NULL,
  `employee_id` VARCHAR(255) NOT NULL,
  `target_week` VARCHAR(20) NOT NULL,
  `day_of_week` VARCHAR(15) NOT NULL,
  `shift_start` TIME NULL,
  `shift_end` TIME NULL,
  `notes` TEXT NULL,
  `status` ENUM('Pending','Approved','Rejected','Selected') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `sent_status` VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_manager_email ON transport_requests(manager_email);
CREATE INDEX idx_employee_id ON transport_requests(employee_id);
CREATE INDEX idx_status ON transport_requests(status);

CREATE TABLE `reservations` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `trajet_id` INT NULL,
  `seats` INT NOT NULL,
  `status` ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`trajet_id`) REFERENCES `trajets`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `api_keys` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `application_name` VARCHAR(100) NULL,
  `api_key_hash` VARCHAR(255) NOT NULL,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_used_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `webhooks` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `application_name` VARCHAR(100) NULL,
  `endpoint_url` VARCHAR(500) NOT NULL,
  `secret` VARCHAR(255) NOT NULL,
  `active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `webhook_events` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `webhook_id` INT NULL,
  `event_type` VARCHAR(100) NULL,
  `payload` JSON NULL,
  `status` ENUM('pending','sent','failed') DEFAULT 'pending',
  `attempts` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `sent_at` TIMESTAMP NULL,
  FOREIGN KEY (`webhook_id`) REFERENCES `webhooks`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `rate_limits` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `identifier` VARCHAR(255) NOT NULL,
  `hits` INT NOT NULL DEFAULT 1,
  `expires_at` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_rate_limits_identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_rate_limits_identifier_expires ON rate_limits(identifier, expires_at);

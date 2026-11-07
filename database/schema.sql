-- ============================================================
--  Chanda Collection App — Database schema
--  MariaDB 10.4+ / MySQL 8+
--  Import:  mysql -u root < database/schema.sql
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+05:30';

CREATE DATABASE IF NOT EXISTS `cca`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `cca`;

-- ------------------------------------------------------------
-- users — all four roles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role`                 ENUM('super_admin','admin','volunteer','bhaktpremi') NOT NULL DEFAULT 'bhaktpremi',
  `name`                 VARCHAR(120) NOT NULL,
  `mobile`               VARCHAR(15)  NOT NULL,
  `email`                VARCHAR(190) DEFAULT NULL,
  `password_hash`        VARCHAR(255) NOT NULL,
  `photo`                VARCHAR(255) DEFAULT NULL,
  `locality`             VARCHAR(120) DEFAULT NULL,
  `message`              VARCHAR(280) DEFAULT NULL,
  `show_public`          TINYINT(1) NOT NULL DEFAULT 1,
  `is_anonymous_default` TINYINT(1) NOT NULL DEFAULT 0,
  `status`               ENUM('active','disabled') NOT NULL DEFAULT 'active',
  `last_login_at`        DATETIME DEFAULT NULL,
  `created_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  -- Generated guard: allows many NULLs but at most ONE super admin.
  `super_admin_guard`    TINYINT GENERATED ALWAYS AS (IF(`role` = 'super_admin', 1, NULL)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_mobile` (`mobile`),
  UNIQUE KEY `uq_users_super_admin` (`super_admin_guard`),
  KEY `idx_users_role_status` (`role`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- festivals — one row per festival edition
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `festivals` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(120) NOT NULL,
  `year`          SMALLINT UNSIGNED NOT NULL,
  `theme`         VARCHAR(60) NOT NULL DEFAULT 'saffron',
  `target_amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `start_date`    DATE DEFAULT NULL,
  `end_date`      DATE DEFAULT NULL,
  `is_active`     TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_festivals_year` (`year`),
  KEY `idx_festivals_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- donations — pending → approved / rejected / cancelled
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `donations` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `festival_id`  BIGINT UNSIGNED NOT NULL,
  `donor_id`     BIGINT UNSIGNED NOT NULL,
  `amount`       DECIMAL(12,2) NOT NULL,
  `mode`         ENUM('cash','upi','gateway') NOT NULL DEFAULT 'upi',
  `utr_no`       VARCHAR(64) DEFAULT NULL,
  `screenshot`   VARCHAR(255) DEFAULT NULL,
  `collected_by` BIGINT UNSIGNED DEFAULT NULL,
  `status`       ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `reason`       VARCHAR(255) DEFAULT NULL,
  `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0,
  `receipt_no`   VARCHAR(32) DEFAULT NULL,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_by`  BIGINT UNSIGNED DEFAULT NULL,
  `approved_at`  DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_donations_receipt` (`receipt_no`),
  KEY `idx_donations_festival_status` (`festival_id`, `status`),
  KEY `idx_donations_donor` (`donor_id`),
  KEY `idx_donations_collected_by` (`collected_by`),
  KEY `idx_donations_created` (`created_at`),
  CONSTRAINT `fk_donations_festival` FOREIGN KEY (`festival_id`) REFERENCES `festivals` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_donations_donor`    FOREIGN KEY (`donor_id`)    REFERENCES `users` (`id`)     ON DELETE RESTRICT,
  CONSTRAINT `fk_donations_collector` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`)    ON DELETE SET NULL,
  CONSTRAINT `fk_donations_approver`  FOREIGN KEY (`approved_by`)  REFERENCES `users` (`id`)    ON DELETE SET NULL,
  CONSTRAINT `chk_donations_amount` CHECK (`amount` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- settings — key/value app configuration
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `skey`       VARCHAR(64) NOT NULL,
  `svalue`     TEXT DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`skey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- security_pin — single row (id = 1)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `security_pin` (
  `id`              TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `pin_hash`        VARCHAR(255) NOT NULL,
  `failed_attempts` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until`    DATETIME DEFAULT NULL,
  `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_security_pin_single` CHECK (`id` = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- audit_logs — who did what, when, from where
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED DEFAULT NULL,
  `action`     VARCHAR(80) NOT NULL,
  `details`    TEXT DEFAULT NULL,
  `ip`         VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_created` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- login_attempts — rate limiting
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `identifier`   VARCHAR(190) NOT NULL,
  `ip`           VARCHAR(45) NOT NULL,
  `attempts`     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `last_attempt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`identifier`, `ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- MySQL Database Schema for Role-Based Access Control (RBAC) Project
-- Perfect for importing via phpMyAdmin in XAMPP

CREATE DATABASE IF NOT EXISTS `role_based_access_control` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `role_based_access_control`;

-- 1. Create Roles Table
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `is_system` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Create Permissions Table
CREATE TABLE `permissions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'General',
  `is_system` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Create Role Permissions Junction Table
CREATE TABLE `role_permissions` (
  `role_id` INT(11) UNSIGNED NOT NULL,
  `permission_id` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `fk_role_permissions_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_role_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Create Users Table
CREATE TABLE `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role_id` INT(11) UNSIGNED DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'Active',
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- =========================================================
-- SEED DATA
-- =========================================================

-- 1. Insert Default Permissions
INSERT INTO `permissions` (`id`, `name`, `description`, `category`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'view_dashboard', 'Can access the main dashboard overview', 'Dashboard', 1, NOW(), NOW()),
(2, 'manage_users', 'Can list, create, edit and delete users', 'User Management', 1, NOW(), NOW()),
(3, 'manage_roles', 'Can list, create, edit and delete roles', 'Role Management', 1, NOW(), NOW()),
(4, 'manage_permissions', 'Can list, create, edit and delete permission rules', 'Permission Management', 1, NOW(), NOW());

-- 2. Insert Default Roles
INSERT INTO `roles` (`id`, `name`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'Unrestricted access to all resources and settings.', 1, NOW(), NOW()),
(2, 'Manager', 'Can view metrics and manage team users.', 1, NOW(), NOW()),
(3, 'User', 'Standard user account with read-only dashboard access.', 1, NOW(), NOW());

-- 3. Map Permissions to Roles (Role-Permissions Junction)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), -- Super Admin -> view_dashboard
(1, 2), -- Super Admin -> manage_users
(1, 3), -- Super Admin -> manage_roles
(1, 4), -- Super Admin -> manage_permissions
(2, 1), -- Manager -> view_dashboard
(2, 2), -- Manager -> manage_users
(3, 1); -- User -> view_dashboard

-- 4. Insert Default Users
-- Default passwords:
-- Admin: admin@rbac.com -> admin
-- Manager: manager@rbac.com -> manager
-- User: user@rbac.com -> user
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Alex Admin', 'admin@rbac.com', '$2y$10$fV2o1b3f6L0m9yV/bUis1eq9z86tM.eS7vP51O8R7B3pC.O6Gk.tC', 1, 'Active', '2026-06-01 09:00:00', NOW()),
(2, 'Morgan Manager', 'manager@rbac.com', '$2y$10$S9Wv8mD7.P8wNq7aXzGWeunP8V5f9z8H4z8Y/7N6C3pC.O6Gk.tC', 2, 'Active', '2026-06-05 10:30:00', NOW()),
(3, 'Sam User', 'user@rbac.com', '$2y$10$Wq3v8mD7.P8wNq7aXzGWeunP8V5f9z8H4z8Y/7N6C3pC.O6Gk.tC', 3, 'Active', '2026-06-10 14:15:00', NOW());

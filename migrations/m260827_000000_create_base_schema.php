<?php

use yii\db\Migration;

/**
 * Base schema for the app's core tables (properties, leases, bills, users,
 * the Tanzania location hierarchy, and the list_source taxonomy system).
 *
 * No migration ever created these tables - they exist on the live
 * database but the migrations/ directory only ever had incremental
 * ALTERs on top of them. This fills that gap by reconstructing them from
 * the actual live schema, so a fresh clone can run `php yii migrate` and
 * get a working database instead of an empty one.
 *
 * (The `m000000_000000_base` name was tried first, since the project's
 * `migration` table already had a row with exactly that name - but that
 * turned out to be `BaseMigrateController::BASE_MIGRATION`, a reserved
 * sentinel value Yii itself writes into every project's migration table
 * to mark "before any migrations", not evidence of a real missing file.
 * A migration actually named that string gets silently skipped by
 * `migrateUp()`/`migrateDown()`, which is why this one is named and dated
 * normally instead, ahead of every other existing migration.)
 *
 * Does NOT cover: the `migration` table itself (Yii-managed), the RBAC
 * tables `auth_item`/`auth_item_child`/`auth_rule`/`auth_assignment`
 * (created via Yii's own bundled RBAC migrations - see README), or
 * `notification`, `maintenance_request`, `property_photo`, `audit_log`,
 * `property_inquiry` (each already has its own dedicated migration).
 *
 * Uses raw SQL wrapped in FOREIGN_KEY_CHECKS=0 rather than Yii's
 * createTable()/addForeignKey() builder, because these ~18 tables have a
 * dense, partly-circular FK graph (e.g. every table references `users`,
 * `users` self-references for created_by/updated_by) - disabling FK
 * checks for the duration of table creation sidesteps having to hand-order
 * every statement, the same technique mysqldump itself uses.
 */
class m260827_000000_create_base_schema extends Migration
{
    public function safeUp()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0');

        $this->execute("
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `national_id` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `role` enum('admin','manager','technician','accountant','tenant') NOT NULL DEFAULT 'tenant',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `status` enum('inactive','active','blocked') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `list_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `list_Name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `sort_by` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `code` (`code`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `list_source_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `list_source_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `country` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `country_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `country_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `region` (
  `region_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`region_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `country_id` (`country_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `region_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) ON DELETE CASCADE,
  CONSTRAINT `region_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `region_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `district` (
  `district_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `region_id` int(11) NOT NULL,
  `district_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`district_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `region_id` (`region_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `district_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `region` (`region_id`) ON DELETE CASCADE,
  CONSTRAINT `district_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `district_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `street` (
  `street_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `street_name` varchar(255) NOT NULL,
  `region_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`street_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `region_id` (`region_id`),
  KEY `district_id` (`district_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `street_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `region` (`region_id`) ON DELETE CASCADE,
  CONSTRAINT `street_ibfk_2` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`) ON DELETE CASCADE,
  CONSTRAINT `street_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `street_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `ward` (
  `ward_id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `ward_name` varchar(255) NOT NULL,
  `region_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`ward_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `region_id` (`region_id`),
  KEY `district_id` (`district_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `ward_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `region` (`region_id`) ON DELETE CASCADE,
  CONSTRAINT `ward_ibfk_2` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`) ON DELETE CASCADE,
  CONSTRAINT `ward_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `ward_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `country_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `street_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `country_id` (`country_id`),
  KEY `district_id` (`district_id`),
  KEY `region_id` (`region_id`),
  KEY `street_id` (`street_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `location_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) ON DELETE CASCADE,
  CONSTRAINT `location_ibfk_2` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`) ON DELETE CASCADE,
  CONSTRAINT `location_ibfk_3` FOREIGN KEY (`region_id`) REFERENCES `region` (`region_id`) ON DELETE CASCADE,
  CONSTRAINT `location_ibfk_4` FOREIGN KEY (`street_id`) REFERENCES `street` (`street_id`) ON DELETE CASCADE,
  CONSTRAINT `location_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `location_ibfk_6` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `property_type_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `identifier_code` varchar(200) DEFAULT NULL,
  `street_id` int(11) DEFAULT NULL,
  `property_status_id` int(11) NOT NULL,
  `document_url` text DEFAULT NULL,
  `ownership_type_id` int(11) NOT NULL,
  `usage_type_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `identifier_code` (`identifier_code`),
  KEY `property_status` (`property_status_id`),
  KEY `ownership_type` (`ownership_type_id`),
  KEY `usage_type` (`usage_type_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `street_id` (`street_id`),
  KEY `property_type_id` (`property_type_id`),
  CONSTRAINT `property_ibfk_1` FOREIGN KEY (`property_type_id`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_ibfk_3` FOREIGN KEY (`property_status_id`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_ibfk_4` FOREIGN KEY (`ownership_type_id`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_ibfk_5` FOREIGN KEY (`usage_type_id`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_ibfk_6` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `property_ibfk_7` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `property_ibfk_8` FOREIGN KEY (`street_id`) REFERENCES `street` (`street_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `property_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `unit_amount` decimal(15,2) NOT NULL,
  `period` varchar(50) DEFAULT NULL,
  `min_monthly_rent` decimal(15,2) DEFAULT NULL,
  `max_monthly_rent` decimal(15,2) DEFAULT NULL,
  `property_id` int(11) NOT NULL,
  `price_type` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `property_id` (`property_id`),
  KEY `price_type` (`price_type`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `property_price_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_price_ibfk_2` FOREIGN KEY (`price_type`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_price_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `property_price_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `property_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `location_id` (`location_id`),
  KEY `property_id` (`property_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `property_location_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  CONSTRAINT `property_location_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `property` (`id`),
  CONSTRAINT `property_location_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `list_source` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `property_attribute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `attribute_name` varchar(255) NOT NULL,
  `attribute_name_dataType_id` int(11) NOT NULL,
  `property_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `attribute_name_dataType` (`attribute_name_dataType_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `property_type_id` (`property_type_id`),
  CONSTRAINT `property_attribute_ibfk_1` FOREIGN KEY (`attribute_name_dataType_id`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_attribute_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `property_attribute_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `property_attribute_ibfk_4` FOREIGN KEY (`property_type_id`) REFERENCES `list_source` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `property_attribute_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `property_attribute_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `property_attribute_id` (`property_attribute_id`),
  KEY `answer_id` (`answer_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `property_attribute_answer_ibfk_1` FOREIGN KEY (`property_attribute_id`) REFERENCES `property_attribute` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_attribute_answer_ibfk_2` FOREIGN KEY (`answer_id`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_attribute_answer_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `property_attribute_answer_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `property_extra_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `property_id` int(11) NOT NULL,
  `attribute_answer_id` int(11) DEFAULT NULL,
  `property_attribute_id` int(11) DEFAULT NULL,
  `attribute_answer_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `property_id` (`property_id`),
  KEY `attribute_answer_id` (`attribute_answer_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `fk_property_attribute` (`property_attribute_id`),
  CONSTRAINT `fk_property_attribute` FOREIGN KEY (`property_attribute_id`) REFERENCES `property_attribute` (`id`),
  CONSTRAINT `property_extra_data_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_extra_data_ibfk_2` FOREIGN KEY (`attribute_answer_id`) REFERENCES `property_attribute_answer` (`id`) ON DELETE CASCADE,
  CONSTRAINT `property_extra_data_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `property_extra_data_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `lease` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_price_id` int(11) NOT NULL,
  `lease_doc_url` text DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `lease_start_date` date NOT NULL,
  `lease_end_date` date NOT NULL,
  `duration_months` int(11) DEFAULT NULL,
  `security_deposit_amount` decimal(15,2) DEFAULT NULL,
  `security_deposit_status` int(11) DEFAULT NULL,
  `security_deposit_returned_at` date DEFAULT NULL,
  `security_deposit_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `property_id` (`property_id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `property_price_id` (`property_price_id`),
  KEY `status` (`status`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  KEY `fk-lease-security_deposit_status` (`security_deposit_status`),
  CONSTRAINT `fk-lease-security_deposit_status` FOREIGN KEY (`security_deposit_status`) REFERENCES `list_source` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lease_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lease_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `lease_ibfk_3` FOREIGN KEY (`property_price_id`) REFERENCES `property_price` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lease_ibfk_4` FOREIGN KEY (`status`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lease_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `lease_ibfk_6` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `lease_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `due_date` date NOT NULL,
  `paid_date` date DEFAULT NULL,
  `bill_status` int(11) NOT NULL,
  `receipt_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `lease_id` (`lease_id`),
  KEY `bill_status` (`bill_status`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`lease_id`) REFERENCES `lease` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bill_ibfk_3` FOREIGN KEY (`bill_status`) REFERENCES `list_source` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bill_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `bill_ibfk_5` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `expense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `property_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `expense_type` varchar(255) DEFAULT NULL,
  `document_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `property_id` (`property_id`),
  KEY `created_by` (`created_by`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `property` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expense_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `expense_ibfk_3` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute("
CREATE TABLE `tenant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->execute('SET FOREIGN_KEY_CHECKS=1');

        $this->seedListSource();
    }

    /**
     * Every dropdown/status field in this app (property type, ownership,
     * usage, lease/bill/maintenance status, etc.) is driven off list_source
     * rows - without this seed the schema is structurally complete but the
     * app is unusable (no property types to pick, no statuses to assign).
     * Parent rows are inserted first so their real auto-generated ids can
     * be used for the children's parent_id, since a fresh install won't
     * get the same ids the live database happens to have today.
     * (Security Deposit Status and its children are seeded separately by
     * m260830_020000_add_security_deposit, not here.)
     */
    private function seedListSource()
    {
        $parents = [
            // [uuid, list_Name, code, category, sort_by, description]
            ['List_1', 'Usage Type', 'LIST857', 'Usage Type', '1', 'None'],
            ['List_4', 'Property Type', 'LIST768', 'Property Type', '4', 'None'],
            ['List_6', 'Ownership', 'LIST295', 'Ownership', '6', 'none'],
            ['List_8', 'Status ', 'LIST156', 'Status', '8', 'None'],
            ['List_11', 'Gender', 'LIST531', 'Gender', '10', ''],
            ['List_13', 'Data Type', 'LIST431', 'Data Type', '10', 'None'],
            ['List_17', 'Fuel Type', 'LIST213', 'Fuel Type', '10', 'none'],
            ['List_21', 'Color', 'LIST120', 'Color', '10', 'None'],
            ['List_25', 'Lease Status', 'LIST109', 'Lease Status', '10', 'None'],
            ['List_29', 'Bill Status', 'LIST298', 'Bill Status', '10', 'None'],
            ['List_33', 'Maintenance Status', 'LIST584', 'Maintenance Status', '1', ''],
            ['List_38', 'Maintenance Priority', 'LIST256', 'Maintenance Priority', '1', ''],
        ];

        $idByUuid = [];
        foreach ($parents as [$uuid, $name, $code, $category, $sort, $desc]) {
            $this->insert('list_source', [
                'uuid' => $uuid,
                'list_Name' => $name,
                'code' => $code,
                'category' => $category,
                'sort_by' => $sort,
                'description' => $desc,
                'parent_id' => null,
            ]);
            $idByUuid[$uuid] = $this->db->getLastInsertID();
        }

        $children = [
            // [uuid, list_Name, code, category, sort_by, description, parentUuid]
            ['List_2', 'Rented', 'LIST510', 'Usage Type', '2', 'none', 'List_1'],
            ['List_3', 'Sale', 'LIST218', 'Sale', '3', 'none', 'List_1'],
            ['List_24', 'Storage', 'LIST718', 'Usage Type', '10', 'None', 'List_1'],
            ['List_5', 'Car', 'LIST223', 'Car', '5', 'None', 'List_4'],
            ['List_32', 'House', 'LIST495', 'Property Type', '10', 'none', 'List_4'],
            ['List_7', 'Storage', 'LIST359', 'Ownership', '7', 'none', 'List_6'],
            ['List_9', 'Active', 'LIST233', 'Status', '9', 'None', 'List_8'],
            ['List_10', 'Under Maintainance', 'LIST163', 'Status', '10', 'None', 'List_8'],
            ['List_12', 'Female', 'LIST670', 'Gender', '10', '', 'List_11'],
            ['List_15', 'text', 'LIST368', 'Data Type', '10', 'None', 'List_13'],
            ['List_16', 'boolean', 'LIST552', 'Data Type', '10', 'None', 'List_13'],
            ['List_20', 'select', 'LIST132', 'select', '10', 'none', 'List_13'],
            ['List_18', 'Diesel', 'LIST144', 'Fuel Type', '10', 'None', 'List_17'],
            ['List_19', 'Petrol', 'LIST970', 'Fuel Type', '10', 'None', 'List_17'],
            ['List_22', 'Red', 'LIST101', 'Color', '10', 'None', 'List_21'],
            ['List_23', 'Blue', 'LIST161', 'Color', '10', 'none', 'List_21'],
            ['List_26', 'Active', 'LIST416', 'Lease Status', '10', 'None', 'List_25'],
            ['List_27', 'Pending', 'LIST782', 'Lease Status', '10', 'None', 'List_25'],
            ['List_28', 'Terminated', 'LIST372', 'Lease Status', '10', 'None', 'List_25'],
            ['List_43', 'Renewed', 'LIST384', 'Lease Status', '1', '', 'List_25'],
            ['List_30', 'Pending', 'LIST296', 'Bill Status', '10', 'None', 'List_29'],
            ['List_31', 'Paid', 'LIST979', 'Bill Status', '10', 'None', 'List_29'],
            ['List_34', 'Open', 'LIST400', 'Open', '1', '', 'List_33'],
            ['List_35', 'In Progress', 'LIST556', 'In Progress', '1', '', 'List_33'],
            ['List_36', 'Resolved', 'LIST148', 'Resolved', '1', '', 'List_33'],
            ['List_37', 'Closed', 'LIST420', 'Closed', '1', '', 'List_33'],
            ['List_39', 'Low', 'LIST180', 'Low', '1', '', 'List_38'],
            ['List_40', 'MUSSA', 'LIST610', 'Medium', '1', 'AALIPIEE KWANZA', 'List_38'],
            ['List_42', 'Urgent', 'LIST961', 'Urgent', '1', '', 'List_38'],
        ];

        foreach ($children as [$uuid, $name, $code, $category, $sort, $desc, $parentUuid]) {
            $this->insert('list_source', [
                'uuid' => $uuid,
                'list_Name' => $name,
                'code' => $code,
                'category' => $category,
                'sort_by' => $sort,
                'description' => $desc,
                'parent_id' => $idByUuid[$parentUuid],
            ]);
        }
    }

    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        foreach ([
            'tenant', 'expense', 'bill', 'lease', 'property_extra_data',
            'property_attribute_answer', 'property_attribute', 'property_location',
            'property_price', 'property', 'location', 'ward', 'street', 'district',
            'region', 'country', 'list_source', 'users',
        ] as $table) {
            $this->dropTable($table);
        }
        $this->execute('SET FOREIGN_KEY_CHECKS=1');
    }
}

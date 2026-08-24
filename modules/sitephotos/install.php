<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'site_timeline_photos')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'site_timeline_photos` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `file_name` VARCHAR(255) NOT NULL,
      `original_name` VARCHAR(255) NOT NULL,
      `title` VARCHAR(255) NULL,
      `description` TEXT NULL,
      `area` TEXT NULL,
      `rfi` TEXT NULL,
      `drawing` TEXT NULL,
      `project_id` INT(11) NOT NULL DEFAULT 1,
      `uploaded_by` INT(11) UNSIGNED NOT NULL,
      `uploaded_at` DATETIME NOT NULL,
      PRIMARY KEY (`id`),
      KEY `uploaded_by` (`uploaded_by`),
      KEY `uploaded_at` (`uploaded_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'site_timeline_photo_comments')) {
  $CI->db->query('CREATE TABLE `' . db_prefix() . 'site_timeline_photo_comments` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `timeline_photo_id` INT(11) UNSIGNED NOT NULL,
    `staffid` INT(11) UNSIGNED NOT NULL,
    `comment` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `timeline_photo_id` (`timeline_photo_id`),
    KEY `staffid` (`staffid`),
    KEY `created_at` (`created_at`)
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

?>
<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'site_timeline_photos')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'site_timeline_photos` (
      `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `file_name` VARCHAR(255) NOT NULL,
      `original_name` VARCHAR(255) NOT NULL,
      `title` VARCHAR(255) NULL,
      `description` TEXT NULL,
      `uploaded_by` INT(11) UNSIGNED NOT NULL,
      `uploaded_at` DATETIME NOT NULL,
      PRIMARY KEY (`id`),
      KEY `uploaded_by` (`uploaded_by`),
      KEY `uploaded_at` (`uploaded_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

?>
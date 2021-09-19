UPDATE `tbl_users` SET `password` = MD5(`password`);

ALTER TABLE `tbl_books` ADD `sub_cat_id` INT(11) NOT NULL AFTER `cat_id`;
ALTER TABLE `tbl_favourite` CHANGE `id` `fa_id` INT(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tbl_users` 
ADD `device_id` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `auth_id`,
ADD `is_duplicate` INT(1) NOT NULL DEFAULT '0' AFTER `device_id`;

ALTER TABLE `tbl_settings` 
  ADD `app_update_status` VARCHAR(10) NOT NULL DEFAULT 'false' AFTER `app_privacy_policy`, 
  ADD `app_new_version` DOUBLE NOT NULL DEFAULT '1' AFTER `app_update_status`, 
  ADD `app_update_desc` TEXT NOT NULL AFTER `app_new_version`, 
  ADD `app_redirect_url` TEXT NOT NULL AFTER `app_update_desc`, 
  ADD `cancel_update_status` VARCHAR(10) NOT NULL DEFAULT 'false' AFTER `app_redirect_url`,
  ADD `app_faq` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `cancel_update_status`,
  ADD `account_delete_intruction` TEXT NOT NULL AFTER `app_faq`,
  ADD `api_sub_cat_order_by` TEXT NOT NULL AFTER `account_delete_intruction`,
  ADD `api_sub_cat_post_order_by` TEXT NOT NULL AFTER `api_sub_cat_order_by`,
  ADD `native_ad` VARCHAR(20) NOT NULL DEFAULT 'false' AFTER `api_sub_cat_post_order_by`, 
  ADD `native_ad_type` VARCHAR(30) NOT NULL DEFAULT 'admob' AFTER `native_ad`, 
  ADD `native_ad_id` TEXT NOT NULL AFTER `native_ad_type`, 
  ADD `native_facebook_id` TEXT NOT NULL AFTER `native_ad_id`,
  ADD `native_cat_position` INT(10) NOT NULL DEFAULT '1' AFTER `native_facebook_id`,
  ADD `native_position` INT(10) NOT NULL DEFAULT '1' AFTER `native_cat_position`,
  ADD `native_position_grid` INT(10) NOT NULL DEFAULT '1' AFTER `native_position`;
 
 ALTER TABLE `tbl_smtp_settings`
  ADD `smtp_type` VARCHAR(20) NOT NULL DEFAULT 'server' AFTER `id`,
  ADD `smtp_ghost` VARCHAR(150) NOT NULL AFTER `port_no`, 
  ADD `smtp_gemail` VARCHAR(150) NOT NULL AFTER `smtp_ghost`, 
  ADD `smtp_gpassword` TEXT NOT NULL AFTER `smtp_gemail`, 
  ADD `smtp_gsecure` VARCHAR(20) NOT NULL AFTER `smtp_gpassword`, 
  ADD `gport_no` INT(10) NOT NULL AFTER `smtp_gsecure`;
   

CREATE TABLE `tbl_sub_category` (
  `sid` int(11) NOT NULL,
  `cat_id` varchar(255) NOT NULL,
  `sub_cat_name` varchar(255) NOT NULL,
  `sub_cat_image` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tbl_sub_category`
  ADD PRIMARY KEY (`sid`);

ALTER TABLE `tbl_sub_category`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `tbl_active_log` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `date_time` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `tbl_active_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tbl_active_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;  
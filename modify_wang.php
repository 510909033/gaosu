1：way_user_bind_car表添加字段
	ALTER TABLE `way_user_bind_car` ADD `car_qrocde_path` VARCHAR( 100 ) NOT NULL DEFAULT '' COMMENT '车辆唯一二维码标识，public目录下，开始不要加/';
	
	ALTER TABLE `way_user_bind_car` ADD `qrcode_version` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '二维码版本，扫描车辆二维码时有效' AFTER `verify`;
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	 
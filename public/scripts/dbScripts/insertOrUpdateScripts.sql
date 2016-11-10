
  /* main_privileges : adding Analytics tab for HR Role and group
   * @author : Pratibha Mishra
   */
  INSERT INTO `hrmsdev`.`main_privileges` (`id`, `role`, `group_id`, `object`, `addpermission`, `editpermission`, `deletepermission`, `viewpermission`, `uploadattachments`, `viewattachments`, `createdby`, `modifiedby`, `createddate`, `modifieddate`, `isactive`) VALUES (NULL, '14', '3', '8', 'Yes', 'Yes', 'Yes', 'Yes', 'No', 'No', '1', '1', '2016-01-14 00:00:00', '2016-01-14 00:00:00', '1');

  /*
   * Removing unused tabs
   * @author : Pratibha Mishra
   */
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =21;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =107;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =108;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =115;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =118;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =123;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =126;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =127;
  UPDATE  `hrmsdev`.`main_menu` SET  `isactive` =  '0' WHERE  `main_menu`.`id` =139;


  /*
   * Alter table for import employees, new fields asked by HR.
   * Insering new tables for the same.
   * @author : Pratibha Mishra
   */

   ALTER TABLE  `main_empworkdetails` ADD  `skype_id` VARCHAR( 50 ) NULL COMMENT  'skypeId of the employee' AFTER  `contact_number`
   ALTER TABLE  `main_empexperiancedetails` ADD  `hr_email_id` VARCHAR( 60 ) NULL COMMENT  'previous organization HR email id' AFTER `reason_for_leaving` ;
   ALTER TABLE  `main_empcommunicationdetails` CHANGE  `emergency_number`  `emergency_number_1` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
   ALTER TABLE  `main_empcommunicationdetails` CHANGE  `emergency_name`  `emergency_name_1` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
   ALTER TABLE  `main_empcommunicationdetails` CHANGE  `emergency_email`  `emergency_email_1` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
   ALTER TABLE  `main_empcommunicationdetails` ADD  `relation_emergency_1` VARCHAR( 50 ) NULL AFTER  `emergency_email_1` ;
   ALTER TABLE  `main_empcommunicationdetails` ADD  `emergency_number_2` VARCHAR( 100 ) NULL DEFAULT NULL AFTER  `relation_emergency_1` ,
  ADD  `emergency_name_2` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `emergency_number_2` ,
  ADD  `emergency_email_2` VARCHAR( 100 ) NULL DEFAULT NULL AFTER  `emergency_name_2` ,
  ADD  `relation_emergency_2` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `emergency_email_2` ;
  ALTER TABLE  `main_empsalarydetails` ADD  `pf_number` INT( 12 ) NULL DEFAULT NULL AFTER  `accountnumber` ,
  ADD  `pan_number` VARCHAR( 10 ) NULL DEFAULT NULL AFTER  `pf_number` ,
  ADD  `driver_license_number` VARCHAR( 20 ) NULL DEFAULT NULL AFTER  `pan_number` ;
  ALTER TABLE  `main_employees` ADD  `pip_startdate` DATE NULL DEFAULT NULL AFTER  `emp_status_id` ,
  ADD  `pip_enddate` DATE NULL DEFAULT NULL AFTER  `pip_startdate` ;
  ALTER TABLE  `main_users` ADD  `middlename` VARCHAR( 50 ) NULL DEFAULT NULL AFTER  `lastname` ;
  ALTER TABLE  `main_empexperiancedetails` CHANGE  `hr_email_id`  `hr_email_id` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
  ALTER TABLE `main_empworkdetails` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
  ALTER TABLE `main_empworkinfo` CHANGE  `work_status`  `work_status` INT( 11 ) NULL DEFAULT NULL ;

  /*
  ALTER TABLE  `main_empworkdetails` ADD  `skype_id` VARCHAR( 60 ) NULL DEFAULT NULL AFTER  `contact_number` ;
  */

  --
  -- Table structure for table `main_empworkstatus`
  --

  CREATE TABLE IF NOT EXISTS `main_empworkstatus` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(20) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

  INSERT INTO `hrmsdev`.`main_empworkstatus` (`id`, `name`) VALUES (NULL, 'Project'), (NULL, 'Bench'), (NULL, 'Support');
  --
  -- Table structure for table `main_empworkinfo`
  --

  CREATE TABLE IF NOT EXISTS `main_empworkinfo` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `work_status` int(11) NOT NULL,
    `project_name` varchar(60) DEFAULT NULL,
    `functional_area` varchar(100) DEFAULT NULL,
    `technology` varchar(100) DEFAULT NULL,
    `createdby` int(11) DEFAULT NULL,
    `createddate` date DEFAULT NULL,
    `modifiedby` int(11) DEFAULT NULL,
    `modifieddate` date DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



  --Data Cleanup before import
  -- Do not use unless require all data to be cleared
  DELETE FROM `main_users` WHERE `id` NOT IN (1, 2, 3);
  ALTER TABLE main_users AUTO_INCREMENT =4;

  DELETE FROM `main_employees` WHERE `user_id` NOT IN (1, 2, 3);
  ALTER TABLE main_employees AUTO_INCREMENT =4;

  DELETE FROM `main_employees_summary` WHERE `user_id` NOT IN ( 1, 2, 3 );
  ALTER TABLE main_employees_summary AUTO_INCREMENT =4;

  DELETE FROM `main_empcertificationdetails` WHERE `user_id` NOT IN ( 1, 2, 3 );
  ALTER TABLE main_empcertificationdetails AUTO_INCREMENT =4;

  DELETE FROM `main_empcommunicationdetails` WHERE `user_id` NOT IN ( 1, 2, 3 );
  ALTER TABLE main_empcommunicationdetails AUTO_INCREMENT =4;

  DELETE FROM `main_empdependencydetails` WHERE `user_id` NOT IN ( 1, 2, 3 );
  ALTER TABLE main_empdependencydetails AUTO_INCREMENT =4;

  DELETE FROM `main_empeducationdetails` WHERE `user_id` NOT IN ( 1, 2, 3 );
  ALTER TABLE main_empeducationdetails AUTO_INCREMENT =4;

  DELETE FROM `main_empexperiancedetails` WHERE `user_id` NOT IN ( 1, 2, 3 );
  ALTER TABLE main_empexperiancedetails AUTO_INCREMENT =4;

  DELETE FROM  `main_emppersonaldetails` WHERE  `user_id` NOT IN ( 1, 2, 3 ) ;
  ALTER TABLE main_emppersonaldetails AUTO_INCREMENT =4;

  UPDATE  `tbl_employmentstatus` SET  `employemnt_status` =  'Ex employee' WHERE  `tbl_employmentstatus`.`id` =9;# MySQL returned an empty result set (i.e. zero rows).
  UPDATE  `main_employmentstatus` SET  `workcode` =  'Ex employee' WHERE  `main_employmentstatus`.`id` =10;# MySQL returned an empty result set (i.e. zero rows).
  ALTER TABLE  `main_empworkdetails` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT ;# MySQL returned an empty result set (i.e. zero rows).
  ALTER TABLE  `main_empworkinfo` CHANGE  `work_status`  `work_status` INT( 11 ) NULL DEFAULT NULL ;# MySQL returned an empty result set (i.e. zero rows).
  DELETE FROM  `main_emaillogs` ;# 521 rows affected.
  ALTER TABLE main_emaillogs AUTO_INCREMENT =4;# MySQL returned an empty result set (i.e. zero rows).


  -- Updating roles ann job titles as per new requirement.

  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Asst Admin Manager' WHERE  `main_jobtitles`.`id` =24;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'CCO' WHERE  `main_jobtitles`.`id` =5;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'CEO' WHERE  `main_jobtitles`.`id` =6;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'COO' WHERE  `main_jobtitles`.`id` =4;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'CTO' WHERE  `main_jobtitles`.`id` =7;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Office Boy Cum Jr Admin' WHERE  `main_jobtitles`.`id` =25;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Jr Software Engineer' WHERE  `main_jobtitles`.`id` =16;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Jr Front End Developer' WHERE  `main_jobtitles`.`id` =18;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Jr QA Engineer' WHERE  `main_jobtitles`.`id` =17;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Jr Sales Executive' WHERE  `main_jobtitles`.`id` =32;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Manager Research & Social Media' WHERE  `main_jobtitles`.`id` =27;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'QA Engineer' WHERE  `main_jobtitles`.`id` =20;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Sr Front End Developer' WHERE  `main_jobtitles`.`id` =15;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Sr Marketing Manager' WHERE  `main_jobtitles`.`id` =26;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Sr Project Manager' WHERE  `main_jobtitles`.`id` =9;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Sr QA Engineer' WHERE  `main_jobtitles`.`id` =14;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Sr Software Engineer' WHERE  `main_jobtitles`.`id` =13;
  UPDATE `main_jobtitles` SET  `jobtitlename` =  'Tech Lead' WHERE  `main_jobtitles`.`id` =12;
  INSERT INTO `main_jobtitles` (
`id` ,
`jobtitlecode` ,
`jobtitlename` ,
`jobdescription` ,
`minexperiencerequired` ,
`jobpaygradecode` ,
`jobpayfrequency` ,
`comments` ,
`createdby` ,
`modifiedby` ,
`createddate` ,
`modifieddate` ,
`isactive`
)
VALUES (
NULL ,  'SrPE',  'Sr Principle Engineer', NULL , NULL ,  'H',  '1', NULL , NULL , NULL ,  '',  '',  '1');

UPDATE `tbl_employmentstatus` SET  `employemnt_status` =  'On Probation
' WHERE  `tbl_employmentstatus`.`id` =5;

UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'Contract' WHERE  `main_employmentstatus`.`id` =8;
UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'Full Time' WHERE  `main_employmentstatus`.`id` =9;
UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'Ex employee' WHERE  `main_employmentstatus`.`id` =10;
UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'Part Time' WHERE  `main_employmentstatus`.`id` =11;
UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'Permanent' WHERE  `main_employmentstatus`.`id` =12;
UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'On Probation' WHERE  `main_employmentstatus`.`id` =13;
UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'Resigned' WHERE  `main_employmentstatus`.`id` =14;
UPDATE  `hrmsdev`.`main_employmentstatus` SET  `workcode` =  'Suspended' WHERE  `main_employmentstatus`.`id` =15;


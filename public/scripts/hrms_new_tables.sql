-- MySQL dump 10.13  Distrib 5.5.49, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: hrms
-- ------------------------------------------------------
-- Server version	5.5.49-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `okr_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `okr_audit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `event_on_table` varchar(255) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `event_time` datetime NOT NULL,
  `event_meta` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=550 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assessmentcycle`
--

DROP TABLE IF EXISTS `assessmentcycle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessmentcycle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(255) NOT NULL,
  `assessment_period_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `creator_user_id` int(11) NOT NULL,
  `modified_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessmentcycle`
--

LOCK TABLES `assessmentcycle` WRITE;
/*!40000 ALTER TABLE `assessmentcycle` DISABLE KEYS */;
INSERT INTO `assessmentcycle` VALUES 
(1,'2016 Q1',1,'2016-04-01 00:00:00','2016-06-30 23:59:59',113,113,'2016-04-01 00:00:00','2016-04-01 00:00:00'),
(2,'2016 Q2',1,'2016-07-01 00:00:00','2016-09-30 23:59:59',113,113,'2016-04-01 00:00:00','2016-04-01 00:00:00'),
(3,'2016 Q3',1,'2016-10-01 00:00:00','2016-12-31 23:59:59',113,113,'2016-04-01 00:00:00','2016-04-01 00:00:00'),
(4,'2016 Q4',1,'2016-01-01 00:00:00','2017-03-31 23:59:59',113,113,'2016-04-01 00:00:00','2016-04-01 00:00:00');
/*!40000 ALTER TABLE `assessmentcycle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessmentfrequency`
--

DROP TABLE IF EXISTS `assessmentfrequency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessmentfrequency` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` enum('monthly','quarterly','half-yearly','yearly') DEFAULT 'quarterly',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessmentfrequency`
--

LOCK TABLES `assessmentfrequency` WRITE;
/*!40000 ALTER TABLE `assessmentfrequency` DISABLE KEYS */;
INSERT INTO `assessmentfrequency` VALUES (1,'monthly'),(2,'quarterly'),(3,'half-yearly'),(4,'yearly');
/*!40000 ALTER TABLE `assessmentfrequency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessmentmeasurementcriteria`
--

DROP TABLE IF EXISTS `assessmentmeasurementcriteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessmentmeasurementcriteria` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `min_score` int(4) NOT NULL,
  `max_score` int(4) NOT NULL,
  `added_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessmentmeasurementcriteria`
--

LOCK TABLES `assessmentmeasurementcriteria` WRITE;
/*!40000 ALTER TABLE `assessmentmeasurementcriteria` DISABLE KEYS */;
INSERT INTO `assessmentmeasurementcriteria` VALUES (1,'10 point shceme',0,10,6,6);
/*!40000 ALTER TABLE `assessmentmeasurementcriteria` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_assessmentmeasurementcriteria_update` BEFORE UPDATE ON `assessmentmeasurementcriteria` FOR EACH ROW
BEGIN
SET @json = "{";
SET @first = true;
IF (OLD.name != NEW.name) THEN
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_name\"", ":", "\"", OLD.name, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_name\"", ":", "\"", NEW.name, "\"");
END IF;



IF (OLD.min_score != NEW.min_score) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_min_score\"", ":", "\"", OLD.min_score, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_min_score\"", ":", "\"", NEW.min_score, "\"");
END IF;

IF (OLD.max_score != NEW.max_score) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_max_score\"", ":", "\"", OLD.max_score, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_max_score\"", ":", "\"", NEW.max_score, "\"");
END IF;

IF (OLD.updated_by != NEW.updated_by) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_updated_by\"", ":", "\"", OLD.updated_by, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_updated_by\"", ":", "\"", NEW.updated_by, "\"");
END IF;


SET @json = CONCAT(@json, "}");

INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('ASSESSMENT_MEASUREMENT_CRITERIA_CHANGE', 'assessmentmeasurementcriteria', OLD.id, NOW(), @json);

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `assessmentperiod`
--

DROP TABLE IF EXISTS `assessmentperiod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessmentperiod` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `frequency_assessment_id` int(11) NOT NULL,
  `start_date_financial_year` datetime NOT NULL,
  `end_date_financial_year` datetime NOT NULL,
  `creator_user_id` int(11) NOT NULL,
  `modified_user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessmentperiod`
--

LOCK TABLES `assessmentperiod` WRITE;
/*!40000 ALTER TABLE `assessmentperiod` DISABLE KEYS */;
INSERT INTO `assessmentperiod` VALUES (1,2,'2016-04-01 00:00:00','2017-03-31 23:59:59',113,113,'2016-02-24 14:36:03','2016-02-24 14:36:03');
/*!40000 ALTER TABLE `assessmentperiod` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_assessmentperiod_update` BEFORE UPDATE ON `assessmentperiod` FOR EACH ROW
BEGIN
SET @json = "{";
SET @first = true;
IF (OLD.frequency_assessment_id!=NEW.frequency_assessment_id) THEN
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_frequency_assessment_id\"", ":", "\"", OLD.frequency_assessment_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_frequency_assessment_id\"", ":", "\"", NEW.frequency_assessment_id, "\"");
END IF;

IF (OLD.modified_user_id != NEW.modified_user_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_modified_user_id\"", ":", "\"", OLD.modified_user_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_modified_user_id\"", ":", "\"", NEW.modified_user_id, "\"");
END IF;

SET @json = CONCAT(@json, "}");

INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('ASSESSMENT_PERIOD_CHANGE', 'assessmentperiod', OLD.id, NOW(), @json);

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `assessmentrights`
--

DROP TABLE IF EXISTS `assessmentrights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessmentrights` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `added_by` int(11) NOT NULL,
  `added_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goalassessments`
--

DROP TABLE IF EXISTS `goalassessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goalassessments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment` text,
  `goal_id` int(11) NOT NULL,
  `score` int(4) NOT NULL,
  `assessed_by` int(11) NOT NULL,
  `assessment_scheme_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_goalassessments_update` BEFORE UPDATE ON `goalassessments` FOR EACH ROW
BEGIN
SET @json = "{";
SET @first = true;
IF (OLD.comment != NEW.comment) THEN
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_comment\"", ":", "\"", OLD.comment, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_comment\"", ":", "\"", NEW.comment, "\"");
END IF;

IF (OLD.goal_id != NEW.goal_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_goal_id\"", ":", "\"", OLD.goal_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_goal_id\"", ":", "\"", NEW.goal_id, "\"");
END IF;


IF (OLD.score != NEW.score) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_score\"", ":", "\"", OLD.score, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_score\"", ":", "\"", NEW.score, "\"");
END IF;

IF (OLD.assessed_by != NEW.assessed_by) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_assessed_by\"", ":", "\"", OLD.assessed_by, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_assessed_by\"", ":", "\"", NEW.assessed_by, "\"");
END IF;

IF (OLD.assessment_scheme_id != NEW.assessment_scheme_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_assessment_scheme_id\"", ":", "\"", OLD.assessment_scheme_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_assessment_scheme_id\"", ":", "\"", NEW.assessment_scheme_id, "\"");
END IF;


SET @json = CONCAT(@json, "}");

INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('GOAL_ASSESSMENT_CHANGE', 'goalassessments', OLD.id, NOW(), @json);

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `goalcomments`
--

DROP TABLE IF EXISTS `goalcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goalcomments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment` text,
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goalfollowers`
--

DROP TABLE IF EXISTS `goalfollowers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goalfollowers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goals`
--

DROP TABLE IF EXISTS `goals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `descrption` text,
  `owner_id` int(11) NOT NULL,
  `goal_type_id` int(11) NOT NULL,
  `goal_align_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `assessment_cycle_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_archieved` tinyint(1) NOT NULL DEFAULT '0',
  `is_self_assessment_done` tinyint(1) NOT NULL DEFAULT '0',
  `is_manager_assessment_done` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_goal_insert` AFTER INSERT ON `goals` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"goal_id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"descrption\"", ":", "\"", NEW.descrption, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"owner_id\"", ":", "\"", NEW.owner_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"goal_type_id\"", ":", "\"", NEW.goal_type_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"goal_align_id\"", ":", "\"", NEW.goal_align_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"start_date\"", ":", "\"", NEW.start_date, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"end_date\"", ":", "\"", NEW.end_date, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"assessment_cycle_id\"", ":", "\"", NEW.assessment_cycle_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"created_by\"", ":", "\"", NEW.created_by, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"created_at\"", ":", "\"", NEW.created_at, "\"");
    

SET @json = CONCAT(@json, "}");


INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('GOAL_ADDED', 'goals', NEW.id, NOW(), @json);

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_goal_update` BEFORE UPDATE ON `goals` FOR EACH ROW
BEGIN
SET @json = "{";
SET @first = true;
IF (OLD.name!=NEW.name) THEN
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_name\"", ":", "\"", OLD.name, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_name\"", ":", "\"", NEW.name, "\"");
END IF;

IF (OLD.descrption != NEW.descrption) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_descrption\"", ":", "\"", OLD.descrption, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_descrption\"", ":", "\"", NEW.descrption, "\"");
END IF;

IF (OLD.owner_id != NEW.owner_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_owner_id\"", ":", "\"", OLD.owner_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_owner_id\"", ":", "\"", NEW.owner_id, "\"");
END IF;


SET @json = CONCAT(@json, "}");

INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('GOAL_UPDATE', 'goals', OLD.id, NOW(), @json);

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `goaltags`
--

DROP TABLE IF EXISTS `goaltags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goaltags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `tag_id` int(4) NOT NULL,
  `goal_id` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `goaltypes`
--

DROP TABLE IF EXISTS `goaltypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goaltypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` enum('company','individual') DEFAULT 'individual',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goaltypes`
--

LOCK TABLES `goaltypes` WRITE;
/*!40000 ALTER TABLE `goaltypes` DISABLE KEYS */;
INSERT INTO `goaltypes` VALUES (1,'company'),(2,'individual');
/*!40000 ALTER TABLE `goaltypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `milestoneassessment`
--

DROP TABLE IF EXISTS `milestoneassessment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `milestoneassessment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `milestone_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `score` int(11) NOT NULL,
  `assessed_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `assessment_scheme_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `before_milestoneassessment_update` BEFORE UPDATE ON `milestoneassessment` FOR EACH ROW
BEGIN
SET @json = "{";
SET @first = true;
IF (OLD.comment != NEW.comment) THEN
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_comment\"", ":", "\"", OLD.comment, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_comment\"", ":", "\"", NEW.comment, "\"");
END IF;

IF (OLD.milestone_id != NEW.milestone_id) THEN
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_milestone_id\"", ":", "\"", OLD.milestone_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_milestone_id\"", ":", "\"", NEW.milestone_id, "\"");
END IF;

IF (OLD.goal_id != NEW.goal_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_goal_id\"", ":", "\"", OLD.goal_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_goal_id\"", ":", "\"", NEW.goal_id, "\"");
END IF;


IF (OLD.score != NEW.score) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_score\"", ":", "\"", OLD.score, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_score\"", ":", "\"", NEW.score, "\"");
END IF;

IF (OLD.assessed_by != NEW.assessed_by) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_assessed_by\"", ":", "\"", OLD.assessed_by, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_assessed_by\"", ":", "\"", NEW.assessed_by, "\"");
END IF;

IF (OLD.assessment_scheme_id != NEW.assessment_scheme_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_assessment_scheme_id\"", ":", "\"", OLD.assessment_scheme_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_assessment_scheme_id\"", ":", "\"", NEW.assessment_scheme_id, "\"");
END IF;

SET @json = CONCAT(@json, "}");

INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('MILESTONE_ASSESSMENT_CHANGE', 'milestoneassessment', OLD.id, NOW(), @json);

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `milestonecomments`
--

DROP TABLE IF EXISTS `milestonecomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `milestonecomments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment` text,
  `goal_id` int(11) NOT NULL,
  `milestone_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `milestonecomments`
--

LOCK TABLES `milestonecomments` WRITE;
/*!40000 ALTER TABLE `milestonecomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `milestonecomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `milestonemeasurementcriteria`
--

DROP TABLE IF EXISTS `milestonemeasurementcriteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `milestonemeasurementcriteria` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` enum('unit','percentage') DEFAULT 'percentage',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `milestonemeasurementcriteria`
--

LOCK TABLES `milestonemeasurementcriteria` WRITE;
/*!40000 ALTER TABLE `milestonemeasurementcriteria` DISABLE KEYS */;
INSERT INTO `milestonemeasurementcriteria` VALUES (1,'unit'),(2,'percentage');
/*!40000 ALTER TABLE `milestonemeasurementcriteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `milestones`
--

DROP TABLE IF EXISTS `milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `milestones` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `goal_id` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `progress` int(11) DEFAULT NULL,
  `goal_align_id` int(11) NOT NULL,
  `measurement_criteria_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-02 17:03:04


/*
-- Query: SELECT * FROM cue_hrms_staging.goals
LIMIT 0, 1000

-- Date: 2016-06-30 20:33
*/
INSERT INTO `goals` (`id`,`name`,`descrption`,`owner_id`,`goal_type_id`,`goal_align_id`,`start_date`,`end_date`,`assessment_cycle_id`,`created_by`,`updated_by`,`created_at`,`updated_at`,`is_archieved`,`is_self_assessment_done`,`is_manager_assessment_done`) VALUES (1,'Huge company goal','Description of huge company goal 1... Description of huge company goal 1... Description of hugee company goal 1... Description of huge company goal 1... Description of huge company goal 1... ',2,1,0,'2016-04-01 00:00:00','2016-06-30 23:00:00',1,2,2,'2016-06-30 20:30:34','2016-06-30 20:30:34',0,0,0);
INSERT INTO `goals` (`id`,`name`,`descrption`,`owner_id`,`goal_type_id`,`goal_align_id`,`start_date`,`end_date`,`assessment_cycle_id`,`created_by`,`updated_by`,`created_at`,`updated_at`,`is_archieved`,`is_self_assessment_done`,`is_manager_assessment_done`) VALUES (2,'Techno company goal','Description of techno company goal... Description of techno company goal... Description of techno company goal... Description of techno company goal... Description of techno company goal... ',112,1,1,'2016-04-01 00:00:00','2016-06-30 23:00:00',1,112,112,'2016-06-30 20:30:34','2016-06-30 20:30:34',0,0,0);
INSERT INTO `goals` (`id`,`name`,`descrption`,`owner_id`,`goal_type_id`,`goal_align_id`,`start_date`,`end_date`,`assessment_cycle_id`,`created_by`,`updated_by`,`created_at`,`updated_at`,`is_archieved`,`is_self_assessment_done`,`is_manager_assessment_done`) VALUES (3,'Ops company goal','Description of ops company goal... Description of ops company goal... Description of ops company goal... Description of ops company goal... Description of ops company goal... ',113,1,1,'2016-04-01 00:00:00','2016-06-30 23:00:00',1,2,2,'2016-06-30 20:30:34','2016-06-30 20:30:34',0,0,0);
INSERT INTO `goals` (`id`,`name`,`descrption`,`owner_id`,`goal_type_id`,`goal_align_id`,`start_date`,`end_date`,`assessment_cycle_id`,`created_by`,`updated_by`,`created_at`,`updated_at`,`is_archieved`,`is_self_assessment_done`,`is_manager_assessment_done`) VALUES (4,'Sales company goal','Description of sales company goal... Description of sales company goal... Description of sales company goal... Description of sales company goal... Description of sales company goal... ',114,1,1,'2016-04-01 00:00:00','2016-06-30 23:00:00',1,2,2,'2016-06-30 20:30:34','2016-06-30 20:30:34',0,0,0);


DROP TRIGGER IF EXISTS before_goal_update;

DELIMITER ;;
CREATE TRIGGER `before_goal_update` BEFORE UPDATE ON `goals` FOR EACH ROW
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


IF (OLD.goal_type_id != NEW.goal_type_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_goal_type_id\"", ":", "\"", OLD.goal_type_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_goal_type_id\"", ":", "\"", NEW.goal_type_id, "\"");
END IF;



IF (OLD.goal_align_id != NEW.goal_align_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_goal_align_id\"", ":", "\"", OLD.goal_align_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_goal_align_id\"", ":", "\"", NEW.goal_align_id, "\"");
END IF;


IF (OLD.start_date != NEW.start_date) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_start_date\"", ":", "\"", OLD.start_date, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_start_date\"", ":", "\"", NEW.start_date, "\"");
END IF;

IF (OLD.end_date != NEW.end_date) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_end_date\"", ":", "\"", OLD.end_date, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_end_date\"", ":", "\"", NEW.end_date, "\"");
END IF;

IF (OLD.assessment_cycle_id != NEW.assessment_cycle_id) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_assessement_cycle_id\"", ":", "\"", OLD.assessment_cycle_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_assessement_cycle_id\"", ":", "\"", NEW.assessment_cycle_id, "\"");
END IF;

IF (OLD.is_archieved != NEW.is_archieved) THEN
    IF (!@first) THEN
       SET @json = CONCAT(@json, ",");
    END IF;
    SET @first = false;
    SET @json = CONCAT(@json, "\"old_is_archieved\"", ":", "\"", OLD.is_archieved, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_is_archieved\"", ":", "\"", NEW.is_archieved, "\"");
END IF;

SET @json = CONCAT(@json, "}");

INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('GOAL_UPDATE', 'goals', OLD.id, NOW(), @json);

END;;
DELIMITER ;





DROP TRIGGER IF EXISTS before_assessmentperiod_update;

DELIMITER ;;
CREATE TRIGGER `before_assessmentperiod_update` BEFORE UPDATE ON `assessmentperiod` FOR EACH ROW
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

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS before_assessmentmeasurementcriteria_update;

DELIMITER ;;
CREATE TRIGGER `before_assessmentmeasurementcriteria_update` BEFORE UPDATE ON `assessmentmeasurementcriteria` FOR EACH ROW
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

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS before_goalassessments_update;

DELIMITER ;;
CREATE TRIGGER `before_goalassessments_update` BEFORE UPDATE ON `goalassessments` FOR EACH ROW
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

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS before_milestoneassessment_update;

DELIMITER ;;
CREATE TRIGGER `before_milestoneassessment_update` BEFORE UPDATE ON `milestoneassessment` FOR EACH ROW
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

END;;
DELIMITER ;

DROP TRIGGER IF EXISTS after_goal_insert;

DELIMITER ;;
CREATE TRIGGER `after_goal_insert` AFTER INSERT ON `goals` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"goal_id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"goal_name\"", ":", "\"", NEW.name, "\"");
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

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS after_goalcomments_insert;

DELIMITER ;;
CREATE TRIGGER `after_goalcomments_insert` AFTER INSERT ON `goalcomments` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"goal_id\"", ":", "\"", NEW.goal_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"comment\"", ":", "\"", NEW.comment, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"user_id\"", ":", "\"", NEW.user_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"created_at\"", ":", "\"", NEW.created_at, "\"");
       

SET @json = CONCAT(@json, "}");


INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('GOAL_COMMENT_ADDED', 'goalcomments', NEW.id, NOW(), @json);

END;;
DELIMITER ;

DROP TRIGGER IF EXISTS before_goalcomments_update;

DELIMITER ;;
CREATE TRIGGER `before_goalcomments_update` BEFORE UPDATE ON `goalcomments` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"old_id\"", ":", "\"", OLD.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"old_goal_id\"", ":", "\"", OLD.goal_id, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"new_goal_id\"", ":", "\"", NEW.goal_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"old_comment\"", ":", "\"", OLD.comment, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"new_comment\"", ":", "\"", NEW.comment, "\"");
    SET @json = CONCAT(@json, ",");

    
    SET @json = CONCAT(@json, "\"old_user_id\"", ":", "\"", OLD.user_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_user_id\"", ":", "\"", NEW.user_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"old_created_at\"", ":", "\"", OLD.created_at, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"new_created_at\"", ":", "\"", NEW.created_at, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"old_updated_at\"", ":", "\"", OLD.updated_at, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"new_updated_at\"", ":", "\"", NEW.updated_at, "\"");
    
SET @json = CONCAT(@json, "}");


INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('GOAL_COMMENT_UPDATED', 'goalcomments', NEW.id, NOW(), @json);

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS after_milestonecomments_insert;

DELIMITER ;;
CREATE TRIGGER `after_milestonecomments_insert` AFTER INSERT ON `milestonecomments` FOR EACH ROW
BEGIN
SET @json = "{";
    
   
    SET @json = CONCAT(@json, "\"id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"milestone_id\"", ":", "\"", NEW.milestone_id, "\"");
    SET @json = CONCAT(@json, ",");

   
    SET @json = CONCAT(@json, "\"goal_id\"", ":", "\"", NEW.goal_id, "\"");
    SET @json = CONCAT(@json, ",");


    SET @json = CONCAT(@json, "\"comment\"", ":", "\"", NEW.comment, "\"");
    SET @json = CONCAT(@json, ",");

    

    SET @json = CONCAT(@json, "\"user_id\"", ":", "\"", NEW.user_id, "\"");
    SET @json = CONCAT(@json, ",");
    

   

    SET @json = CONCAT(@json, "\"created_at\"", ":", "\"", NEW.created_at, "\"");
    SET @json = CONCAT(@json, ",");

    
    SET @json = CONCAT(@json, "\"updated_at\"", ":", "\"", NEW.updated_at, "\"");


SET @json = CONCAT(@json, "}");


INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('MILESTONE_COMMENT_ADDED', 'milestonecomments', NEW.id, NOW(), @json);

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS before_milestonecomments_update;

DELIMITER ;;
CREATE TRIGGER `before_milestonecomments_update` BEFORE UPDATE ON `milestonecomments` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"old_id\"", ":", "\"", OLD.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"old_milestone_id\"", ":", "\"", OLD.milestone_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_milestone_id\"", ":", "\"", NEW.milestone_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"old_goal_id\"", ":", "\"", OLD.goal_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_goal_id\"", ":", "\"", NEW.goal_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"old_comment\"", ":", "\"", OLD.comment, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_comment\"", ":", "\"", NEW.comment, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"old_user_id\"", ":", "\"", NEW.user_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_user_id\"", ":", "\"", OLD.user_id, "\"");
    SET @json = CONCAT(@json, ",");
    

    SET @json = CONCAT(@json, "\"old_created_at\"", ":", "\"", OLD.created_at, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_created_at\"", ":", "\"", NEW.created_at, "\"");
    SET @json = CONCAT(@json, ",");

    
    SET @json = CONCAT(@json, "\"old_updated_at\"", ":", "\"", OLD.updated_at, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"new_updated_at\"", ":", "\"", NEW.updated_at, "\"");


SET @json = CONCAT(@json, "}");


INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('MILESTONE_COMMENT_UPDATED', 'milestonecomments', NEW.id, NOW(), @json);

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS before_goalfollowers_delete;

DELIMITER ;;
CREATE TRIGGER `before_goalfollowers_delete` BEFORE DELETE ON `goalfollowers` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"id\"", ":", "\"", OLD.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"goal_id\"", ":", "\"", OLD.goal_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"user_id\"", ":", "\"", OLD.user_id, "\"");
    SET @json = CONCAT(@json, ",");

   

    SET @json = CONCAT(@json, "\"created_at\"", ":", "\"", OLD.created_at, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"updated_at\"", ":", "\"", OLD.updated_at, "\"");


SET @json = CONCAT(@json, "}");


INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('UNFOLLOWING_A_GOAL', 'goalfollowers', OLD.id, NOW(), @json);

END;;
DELIMITER ;

DROP TRIGGER IF EXISTS before_goalfollowers_insert;

DELIMITER ;;
CREATE TRIGGER `before_goalfollowers_insert` BEFORE INSERT ON `goalfollowers` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"goal_id\"", ":", "\"", NEW.goal_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"user_id\"", ":", "\"", NEW.user_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"created_at\"", ":", "\"", NEW.created_at, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"updated_at\"", ":", "\"", NEW.updated_at, "\"");


SET @json = CONCAT(@json, "}");


INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('FOLLOWING_A_GOAL', 'goalfollowers', NEW.id, NOW(), @json);

END;;
DELIMITER ;


DROP TRIGGER IF EXISTS before_assessmentcycle_insert;

DELIMITER ;;
CREATE TRIGGER `before_assessmentcycle_insert` BEFORE INSERT ON `assessmentcycle` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"display_name\"", ":", "\"", NEW.display_name, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"assessment_period_id\"", ":", "\"", NEW.assessment_period_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"modified_user_id\"", ":", "\"", NEW.modified_user_id, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"start_date\"", ":", "\"", NEW.start_date, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"end_date\"", ":", "\"", NEW.end_date, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"created_at\"", ":", "\"", NEW.created_at, "\"");
    SET @json = CONCAT(@json, ",");

    SET @json = CONCAT(@json, "\"modified_at\"", ":", "\"", NEW.modified_at, "\"");
    SET @json = CONCAT(@json, ",");


SET @json = CONCAT(@json, "}");



INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('ADDING_ASSESSMENT_CYCLE', 'assessmentcycle', NEW.id, NOW(), @json);

END;;
DELIMITER ;

DROP TRIGGER IF EXISTS before_assessmentcycle_update;

DELIMITER ;;
CREATE TRIGGER `before_assessmentcycle_update` BEFORE UPDATE ON `assessmentcycle` FOR EACH ROW
BEGIN
SET @json = "{";
    
    SET @json = CONCAT(@json, "\"new_id\"", ":", "\"", NEW.id, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"old_id\"", ":", "\"", OLD.id, "\"");
    SET @json = CONCAT(@json, ",");

    
    SET @json = CONCAT(@json, "\"new_display_name\"", ":", "\"", NEW.display_name, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"old_display_name\"", ":", "\"", OLD.display_name, "\"");
    SET @json = CONCAT(@json, ",");


    SET @json = CONCAT(@json, "\"new_assessment_period_id\"", ":", "\"", NEW.assessment_period_id, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"old_.assessment_period_id\"", ":", "\"", OLD.assessment_period_id, "\"");
    SET @json = CONCAT(@json, ",");


    SET @json = CONCAT(@json, "\"new_modified_user_id\"", ":", "\"", NEW.modified_user_id, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"old_modified_user_id\"", ":", "\"", OLD.modified_user_id, "\"");
    SET @json = CONCAT(@json, ",");


    SET @json = CONCAT(@json, "\"new_start_date\"", ":", "\"", NEW.start_date, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"old_start_date\"", ":", "\"", OLD.start_date, "\"");
    SET @json = CONCAT(@json, ",");


    SET @json = CONCAT(@json, "\"new_end_date\"", ":", "\"", NEW.end_date, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"old_end_date\"", ":", "\"", OLD.end_date, "\"");
    SET @json = CONCAT(@json, ",");


    SET @json = CONCAT(@json, "\"new_created_at\"", ":", "\"", NEW.created_at, "\"");
    SET @json = CONCAT(@json, ",");
    SET @json = CONCAT(@json, "\"old_created_at\"", ":", "\"", OLD.created_at, "\"");
    SET @json = CONCAT(@json, ",");


    SET @json = CONCAT(@json, "\"new_modified_at\"", ":", "\"", NEW.modified_at, "\"");
    SET @json = CONCAT(@json, ",");
    
    SET @json = CONCAT(@json, "\"old_modified_at\"", ":", "\"", OLD.modified_at, "\"");
    

SET @json = CONCAT(@json, "}");



INSERT INTO okr_audit (`event_name`, `event_on_table`, `entity_id`, `event_time`, event_meta) 
VALUES ('UPDATE_ASSESSMENT_CYCLE', 'assessmentcycle', NEW.id, NOW(), @json);

END;;
DELIMITER ;


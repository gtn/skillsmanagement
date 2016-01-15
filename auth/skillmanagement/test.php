<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Authentication Plugin: Moodle Network Authentication
 * Multiple host authentication support for Moodle Network.
 *
 * @package auth_mnet
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once($CFG->dirroot.'/course/lib.php');

$user = $DB->get_record('user', array('id'=>33));
/*CREATE COURSE CATEGORY*/
                //check if course category has already been created
                $category_already_exists = false;
                $max_sort = 0;
                $categoryid = 0;
                $category_sortoder= 0;
                $categorycontext = 0;
                $categorycontextpath = '';
                
                $course_categories = $DB->get_records('course_categories');
                foreach($course_categories as $category){
                	if(strcmp($category->name, 'Skillmanagement')==0){
                		$category_already_exists = true;
                		$categoryid = $category->id;
                		$category_sortoder = $category->sortorder;
                		
                		$context = $DB->get_record('context', array('instanceid'=>$categoryid, 'contextlevel'=>40));
                		$categorycontext = $context->id;
                		$categorycontextpath = $context->path;
                		
                	} elseif($category->sortorder > $max_sort){
                		$max_sort = $category->sortorder;
                	}
                }
                
                //if not->create
                if(!$category_already_exists){
                	/*INSERT IN COURSE_CATEGORIES*/
                	$insert = new stdClass();
                	$insert->name = 'Skillmanagement';
                	$insert->description = '<p>Dieser Kursbereich wird für automatisch generierte Kurse von Skillmanagement verwendet.</p>';
                	$insert->descriptionformat = 1;
                	$insert->sortorder = $max_sort + 10000;
                	$insert->timemodified = time();
                	$insert->depth = 1;
                	
                	$insertedid = $DB->insert_record('course_categories', $insert);
                	$inserted = $DB->get_record('course_categories', array('id'=>$insertedid));
                	
                	$categoryid = $inserted->id;
                	$category_sortoder = $inserted->sortorder;
                	
                	$update = new stdClass();
                	$update->id = $inserted->id;
                	$update->path = '/'.$inserted->id;
                	
                	$DB->update_record('course_categories', $update);
                	
                	/*INSERT IN CONTEXT*/
                	$insert = new stdClass();
                	$insert->contextlevel = 40;
                	$insert->instanceid = $inserted->id;
                	$insert->depth = 2;
                	
                	$insertedid = $DB->insert_record('context', $insert);
                	$inserted = $DB->get_record('context', array('id'=>$insertedid));
                	
                	$categorycontext = $inserted->id;
                	$categorycontextpath = '/1/'.$inserted->id;
                	
                	$update = new stdClass();
                	$update->id = $inserted->id;
                	$update->path = '/1/'.$inserted->id;
                	
                	$DB->update_record('context', $update);
                }
                
                /*TESTED AND WORKING CORRECTLY TILL HERE*/
                
                $categorycourses = $DB->get_records_sql('SELECT * FROM {course} WHERE category=?', array($categoryid));
                
                
                /*CREATE COURSE*/
                
                //insert in course-table
               	$insert = new stdClass();
              	$insert->category = $categoryid;
              	$insert->sortorder = $category_sortoder + count($categorycourses) + 1;
              	$insert->fullname = 'Skillmanagement_'.$user->id.'_'.$user->lastname;
              	$insert->shortname = 'skillmgmt'.$user->id;
              	$insert->format = 'weeks';
              	$insert->summaryformat = 1;
              	$insert->startdate = time();
              	$insert->timecreated = time();
              	$insert->timemodified = time();
              	$insert->cacherev = time();
              	
              	$user_course = create_course($insert);
                $user_course_id = $user_course->id;
              	/*$user_course_id = $DB->insert_record('course', $insert);
              	$user_course = $DB->get_record('course', array('id'=>$user_course_id));
              	
              	//insert in context-table
              	$insert = new stdClass();
              	$insert->contextlevel = 50;
                $insert->instanceid = $user_course->id;
                $insert->depth = 3;
                
                $user_course_context_id = $DB->insert_record('context', $insert);
                $user_course_context = $DB->get_record('context', array('id'=>$user_course_context_id));
                
                $update = new stdClass();
                $update->id = $user_course_context->id;
                $update->path = $categorycontextpath.'/'.$user_course_context->id;
                
                $DB->update_record('context', $update);
                
                /*INSERT INTO enrol*/
                /*MANUAL*/
              	/*$insert = new stdClass();
                $insert->enrol = 'manual';
                $insert->status = 0;
                $insert->courseid = $user_course->id;
                $insert->sortorder = 0;
                $insert->expirythreshold = 86400;
                $insert->roleid = 5;
                $insert->timecreated = time();
                $insert->timemodified = time();
                
                $DB->insert_record('enrol', $insert);
                
                /*GUEST*/
               /*	$insert = new stdClass();
                $insert->enrol = 'guest';
                $insert->status = 1;
                $insert->courseid = $user_course->id;
                $insert->sortorder = 1;
                $insert->expirythreshold = 0;
                $insert->roleid = 0;
                $insert->timecreated = time();
                $insert->timemodified = time();
                
                $DB->insert_record('enrol', $insert);
                
                /*SELF*/
              	/*$insert = new stdClass();
                $insert->enrol = 'self';
                $insert->status = 1;
                $insert->courseid = $user_course->id;
                $insert->sortorder = 2;
                $insert->expirythreshold = 86400;
                $insert->roleid = 5;
                $insert->customint1 = 0;
                $insert->customint2 = 0;
                $insert->customint3 = 0;
                $insert->customint4 = 1;
                $insert->customint5 = 0;
                $insert->customint6 = 1;
                $insert->timecreated = time();
                $insert->timemodified = time();
                
                $DB->insert_record('enrol', $insert);
                
                /*CREATE EXABIS BLOCK INSTANCES IN COURSE*/
                 $blocknames = array(
           		 	BLOCK_POS_LEFT => array(),
            		BLOCK_POS_RIGHT => array('exacomp', 'exaport', 'exastud')
        		);
                
                $page = new moodle_page();
   				$page->set_course($user_course);
    			$page->blocks->add_blocks($blocknames);
    			//$page->blocks->add_block_at_end_of_default_region('exaport');
    			//$page->blocks->add_block_at_end_of_default_region('exastud');
                
               // add_block_at_end_of_default_region('exacomp');
               // add_block_at_end_of_default_region('exaport');
               // add_block_at_end_of_default_region('exastud');
                
                /*CREATE SECOND USER WITH SAME PW*/
                $user_trainer = $DB->get_record('user', array('id'=>$user->id));
                
                $user_trainee = $DB->get_record('user', array('username'=>'teilnehmer_'.$user_trainer->username));
				$user_trainee->confirmed = 1;
				$user_trainee->idnumber = $user_trainer->id;
                
                $DB->update_record('user', $user_trainee);
				
				$user_trainer->idnumber = $user_trainee->id;
				$DB->update_record('user', $user_trainer);
                
                /*ENROL user_trainer AND user_trainee to $user_course */
                
               	$enrolment = array('roleid' => 3,'userid' => $user_trainer->id,
					'courseid' => $user_course->id,'timestart' => time(),
					'timeend' => 0,'suspend' => 0);
                
               	$enrol = enrol_get_plugin('manual');

           	 	$enrolinstances = enrol_get_instances($enrolment['courseid'], true);
			
           	 	foreach ($enrolinstances as $courseenrolinstance) {
					if ($courseenrolinstance->enrol == "manual") {
						$instance = $courseenrolinstance;
						break;
					}
				}
           		if (empty($instance)) {
					$errorparams = new stdClass();
					$errorparams->courseid = $enrolment['courseid'];
					throw new moodle_exception('wsnoinstance', 'enrol_manual', $errorparams);
				}
           	 	// Check that the plugin accept enrolment (it should always the case, it's hard coded in the plugin).
				if (!$enrol->allow_enrol($instance)) {
					$errorparams = new stdClass();
					$errorparams->roleid = $enrolment['roleid'];
					$errorparams->courseid = $enrolment['courseid'];
					$errorparams->userid = $enrolment['userid'];
					throw new moodle_exception('wscannotenrol', 'enrol_manual', '', $errorparams);
				}
				
				$enrol->enrol_user($instance, $enrolment['userid'], $enrolment['roleid'],
						$enrolment['timestart'], $enrolment['timeend'], $enrolment['suspend']);

				//enrol participant
				$enrolment = array('roleid' => 5,'userid' => $user_trainee->id,
					'courseid' => $user_course->id,'timestart' => time(),
					'timeend' => 0,'suspend' => 0);
                
               	$enrol = enrol_get_plugin('manual');

           	 	$enrolinstances = enrol_get_instances($enrolment['courseid'], true);
			
           	 	foreach ($enrolinstances as $courseenrolinstance) {
					if ($courseenrolinstance->enrol == "manual") {
						$instance = $courseenrolinstance;
						break;
					}
				}
           		if (empty($instance)) {
					$errorparams = new stdClass();
					$errorparams->courseid = $enrolment['courseid'];
					throw new moodle_exception('wsnoinstance', 'enrol_manual', $errorparams);
				}
           	 	// Check that the plugin accept enrolment (it should always the case, it's hard coded in the plugin).
				if (!$enrol->allow_enrol($instance)) {
					$errorparams = new stdClass();
					$errorparams->roleid = $enrolment['roleid'];
					$errorparams->courseid = $enrolment['courseid'];
					$errorparams->userid = $enrolment['userid'];
					throw new moodle_exception('wscannotenrol', 'enrol_manual', '', $errorparams);
				}
				
				$enrol->enrol_user($instance, $enrolment['userid'], $enrolment['roleid'],
					$enrolment['timestart'], $enrolment['timeend'], $enrolment['suspend']);
			
?>
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
 * Authentication Plugin: Skillmanagement Authentication
 *
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package auth_email
 */

 /**
 WARNING: CHANGED CODE FROM LOGIN/INDEX.PHP ON LINE 233 TO REDIRECT USER TO COURSE PAGE AFTER LOGIN
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/mod/label/lib.php');
require_once($CFG->dirroot.'/lib/classes/session/manager.php');

/**
 * Skillmanagement authentication plugin.
 */
class auth_plugin_skillmanagement extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_skillmanagement() {
        $this->authtype = 'skillmanagement';
        $this->config = get_config('auth/skillmanagement');
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        global $CFG, $DB;
		
        if ($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
  		    return validate_internal_user_password($user, $password);
        }

        return false;
    }
	
    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object  (with system magic quotes)
     * @param  string  $newpassword Plaintext password (with system magic quotes)
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword) {
    	//TODO Update PW within TN
        $user = get_complete_user_data('id', $user->id);
        // This will also update the stored hash to the latest algorithm
        // if the existing hash is using an out-of-date algorithm (or the
        // legacy md5 algorithm).
        return update_internal_user_password($user, $newpassword);
    }

    function can_signup() {
        return true;
    }

    /**
     * Sign up a new user ready for confirmation.
     * Password is passed in plaintext.
     *
     * @param object $user new user object
     * @param boolean $notify print notice with link and terminate
     */
    function user_signup($user, $notify=true) {
        global $DB, $CFG;

        $password = $user->password;
        $user->password = hash_internal_user_password($user->password);
        if (empty($user->calendartype)) {
            $user->calendartype = $CFG->calendartype;
        }

        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');

        $user->id = user_create_user($user, false, false);

        // Save any custom profile field information.
        profile_save_data($user);

		$this->create_skillsmanagement($user->id, false);

		//purge_all_caches();
		
		$this->user_confirm($user->username, $user->secret);
		// @\core\session\manager::login_user($user);
		complete_user_login($DB->get_record('user', ['id' => $user->id]));
		//$this->user_login($user->username, $user->password);

		$user_course = $DB->get_record('course', ['shortname' => 'SKILLSMGMT-'.$user->id]);
		//redirect($CFG->wwwroot .'/login/confirm.php?data='. $user->secret .'/'. $user->username);
		redirect($CFG->wwwroot.'/course/view.php?id='.$user_course->id);
		return true;
    }

	public function create_skillsmanagement($userid, $testing_mode) {
		global $DB, $CFG;

        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');
		require_once($CFG->dirroot.'/blocks/exacomp/lib/lib.php');

		$user = $DB->get_record('user', ['id' => $userid]);
		if (!$user) {
			throw new moodle_exception('user not found');
		}

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

		$user_course = $DB->get_record('course', ['shortname' => 'SKILLSMGMT-'.$user->id]);
		if (!$user_course) {
			$categorycourses = $DB->get_records_sql('SELECT * FROM {course} WHERE category=?', array($categoryid));


			/*CREATE COURSE*/

			//insert in course-table
			$insert = new stdClass();
			$insert->category = $categoryid;
			$insert->sortorder = $category_sortoder + count($categorycourses) + 1;
			$insert->fullname = 'Skillsmanagement_'.$user->id.'_'.$user->lastname;
			$insert->shortname = 'SKILLSMGMT-'.$user->id;
			$insert->format = 'weeks';
			$insert->summaryformat = 1;
			$insert->startdate = time();
			$insert->timecreated = time();
			$insert->timemodified = time();
			$insert->cacherev = time();

			$user_course = create_course($insert);

			/*CREATE EXABIS BLOCK INSTANCES IN COURSE*/
			$page = new moodle_page();
			$page->set_course($user_course);
			$page->blocks->add_region('side-post');
			$page->blocks->add_block('exacomp', 'side-post', 4, false, 'course-view-*');
			$page->blocks->add_block('exaport', 'side-post', 4, false, 'course-view-*');
			$page->blocks->add_block('exastud', 'side-post', 4, false, 'course-view-*');

			/*CREATE COURSE DESCRIPTION*/
			$label = new stdClass();
			$label->intro = get_string('course_description', 'auth_skillmanagement');
			$label->intro .= html_writer::empty_tag('br');
			$label->intro .= html_writer::empty_tag('img', array('src'=>new moodle_url('/auth/skillmanagement/pix/intro.png'), 'alt'=>'intro'));

			$label->course = $user_course->id;
			$label->introformat = 1;
			$labelid = label_add_instance($label);

			/*CREATE COURSE MODULE*/
			$label_module = $DB->get_record('modules', array('name'=>'label'));
			$section = $DB->get_record('course_sections', array('course'=>$user_course->id, 'section'=>0));

			$cm = new stdClass();
			$cm->course = $user_course->id;
			$cm->module = $label_module->id;
			$cm->instance = $labelid;
			$cm->section = $section->id;
			$cm->added = time();

			$cmid = $DB->insert_record('course_modules', $cm);
			course_add_cm_to_section($user_course->id, $cmid, 0);

			$course_context = context_course::instance($user_course->id);
		}

		$source = $DB->get_record(\block_exacomp::DB_DATASOURCES, [ 'source' => 'SKILLSMGMT-'.$user->id ]);
		if (!$source) {
			// import new for this user
			\block_exacomp_data_importer::do_import_file(__DIR__.'/skills_mgmt_data.xml');

			// last imported source
			$source = $DB->get_record_sql("SELECT * FROM {".\block_exacomp::DB_DATASOURCES."} ORDER BY id DESC LIMIT 1");

			// change source
			$source->source = 'SKILLSMGMT-'.$user->id;
			$source->name = 'Skills Management for '.fullname($user);
			$DB->update_record(\block_exacomp::DB_DATASOURCES, $source);
		}

		// last imported schooltypes
		$schooltype_ids = $DB->get_records_menu(\block_exacomp::DB_SCHOOLTYPES, ['source' => $source->id], null, 'sourceid AS id, id AS val');

		block_exacomp_set_mdltype($schooltype_ids,$user_course->id);

		if ($user->lang == 'en') {
			$subjects = block_exacomp_get_subjects_for_schooltype($user_course->id, $schooltype_ids[492])
				+ block_exacomp_get_subjects_for_schooltype($user_course->id, $schooltype_ids[493]);
		} else {
			$subjects = block_exacomp_get_subjects_for_schooltype($user_course->id, $schooltype_ids[72])
				+ block_exacomp_get_subjects_for_schooltype($user_course->id, $schooltype_ids[73]);
		}
		$coursetopics = array();
		foreach($subjects as $subject) {
			$topics = block_exacomp_get_all_topics($subject->id);
			foreach($topics as $topic)
				$coursetopics[] = $topic->id;
		}
		block_exacomp_set_coursetopics($user_course->id,$coursetopics);


    	/*DELETE OTHER MODULES FROM COURSE*/
    	//delete module recent activity
    	/*$recent_activity = $DB->get_record('block_instances', array('parentcontextid'=>$course_context->id, 'blockname'=>'recent_activity'));
    	course_delete_module($recent_activity->id);

    	$search_forum = $DB->get_record('block_instances', array('parentcontextid'=>$course_context->id, 'blockname'=>'search_forums'));
    	course_delete_module($search_forum->id);

    	$news_items = $DB->get_record('block_instances', array('parentcontextid'=>$course_context->id, 'blockname'=>'news_items'));
    	course_delete_module($news_items->id);

    	$calendar = $DB->get_record('block_instances', array('parentcontextid'=>$course_context->id, 'blockname'=>'calendar_upcoming'));
    	course_delete_module($calendar->id);

    	/*CREATE SECOND USER WITH SAME PW*/
        $user_student = $DB->get_record('user', array('username'=>'employee_ '.$user->username));
		if (!$user_student) {
			$user_student = new stdClass();
			$user_student->firstname = get_string('firstname', 'auth_skillmanagement');
			$user_student->lastname = get_string('lastname', 'auth_skillmanagement');
			$user_student->confirmed = 1;
			$user_student->password = 'dummy'; // will be overwritten later
			$user_student->email = $user->email;
			$user_student->auth = 'skillmanagement';
			$user_student->username = 'employee_ '.$user->username;
			$user_student->lang = $user->lang;
			$user_student->mnethostid = 1;

			$user_student->id = user_create_user($user_student, false, false);
			profile_save_data($user_student);
		}

        $DB->update_record('user', [
			'confirmed' => 1,
			'password' => $user->password,
			// 'idnumber' => $user->id, // not sure why?!? connect student to teacher?!?
			'id' => $user_student->id,
		]);

		/*
        $DB->update_record('user', [
			'confirmed' => 1,
			// 'idnumber' => $user_student->id, // not sure why?!? connect student to teacher?!?
			'id' => $user->id,
		]);
		*/

       	/*ENROL user_trainer AND user_trainee to $user_course */

        $enrolment = array('roleid' => 3,'userid' => $user->id,
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
		$enrolment = array('roleid' => 5,'userid' => $user_student->id,
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
	}

    /**
     * Returns true if plugin allows confirming of new users.
     *
     * @return bool
     */
    function can_confirm() {
        return true;
    }

    /**
     * Confirm the new user as registered.
     *
     * @param string $username
     * @param string $confirmsecret
     */
    function user_confirm($username, $confirmsecret) {
        global $DB, $CFG;
		require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');
        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->confirmed) {
                return AUTH_CONFIRM_ALREADY;

            } else if ($user->auth != $this->authtype) {
                return AUTH_CONFIRM_ERROR;

            } else if ($user->secret == $confirmsecret) {   // They have provided the secret key to get in
                $DB->set_field("user", "confirmed", 1, array("id"=>$user->id));
                if ($user->firstaccess == 0) {
                    $DB->set_field("user", "firstaccess", time(), array("id"=>$user->id));
                }
                					
                return AUTH_CONFIRM_OK;
            }
        } else {
            return AUTH_CONFIRM_ERROR;
        }
    }

    function prevent_local_passwords() {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return null; // use default internal method
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set() {
        return true;
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
        // set to defaults if undefined
        if (!isset($config->recaptcha)) {
            $config->recaptcha = false;
        }

        // save settings
        set_config('recaptcha', $config->recaptcha, 'auth/skillmanagement');
        return true;
    }

    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     * @return bool
     */
    function is_captcha_enabled() {
        global $CFG;
        return isset($CFG->recaptchapublickey) && isset($CFG->recaptchaprivatekey) && get_config("auth/{$this->authtype}", 'recaptcha');
    }
    /**
     * overwrite
     * @see auth_plugin_base::user_delete()
     * 
     * Delete user course and employee_user
     */
    function user_delete($olduser) {
    	global $DB;
    	$user = $DB->get_record('user', array('idnumber'=>$olduser->id));
    	
    	if($user)
    		user_delete_user($user);
    		
    	$course = $DB->get_record('course', array('shortname'=>'skillmgmt'.$olduser->id));
    	
    	if($course)
    		delete_course($course);
    	
    	return true;
    }

}



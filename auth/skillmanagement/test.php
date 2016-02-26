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

/*
$user = $DB->get_record('user', array('id'=>33));

if (!$user) {
	throw new moodle_exception('user not found');
}
*/


$auth = get_auth_plugin('skillmanagement');
$auth->create_skillsmanagement($USER->id, true);
die('ok');

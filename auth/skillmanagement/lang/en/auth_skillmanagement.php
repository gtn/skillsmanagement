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
 * Strings for component 'auth_skillmanagement', language 'en'.
 *
 * @package   auth_skillmanagement
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['pluginname'] = 'Skillmanagement: self-registration';

$string['auth_skillmanagementdescription'] = '<p>Skillmanagement self-registration enables a user to create their own account via a \'Create new account\' button on the login page. The user then receives an email containing a secure link to a page where they can confirm their account. Future logins just check the username and password against the stored values in the Moodle database. Additionally a course is created where the user is enrolled as teacher, and a additional account "Participant" is created.</p>
<p>Note: In addition to enabling the plugin, email-based self-registration must also be selected from the self registration drop-down menu on the \'Manage authentication\' page.</p>';
$string['auth_skillmanagementnoemail'] = 'Tried to send you an email but failed!';
$string['auth_skillmanagementrecaptcha'] = 'Adds a visual/audio confirmation form element to the signup page for Skillmanagement self-registering users. This protects your site against spammers and contributes to a worthwhile cause. See http://www.google.com/recaptcha/learnmore for more details. <br /><em>PHP cURL extension is required.</em>';
$string['auth_skillmanagementrecaptcha_key'] = 'Enable reCAPTCHA element';
$string['auth_skillmanagementsettings'] = 'Settings';
$string['course_description'] = '<p><h2>Define, deploy and develop skills with your employees!</h2></p><br/><p>1. The first step is to define the skills/outcomes needed in your organization. We have imported some demo-data for you to see how the module works.<br/>Click on the link <b>"Manage competencies" on the right hand side</b> to add your own.</p><br/><img src="http://www.skills-management.org/arrow.png"><br/><br/><br/><p>2. Follow the instructions on the configuration tabs to work with skills/outcomes.</p><p>3. Login with the second user (student_[yourloginname]) that was created to see how the competence profile develops. You can also access the competence profile of the employee-user by selection "Employee of company" from the competence-grid-tab.</p>';
$string['firstname'] = 'Employee';
$string['lastname'] = 'Of Company';
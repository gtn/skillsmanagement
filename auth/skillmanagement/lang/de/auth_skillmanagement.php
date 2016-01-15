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

$string['auth_skillmanagementdescription'] = '<p>Skillmanagement-Selbstregistrierung erm&ouml;glicht einem Benutzer selbst einen Zugang zu Ihrer Moodle-Instanz zu erstellen. 
	Der Benutzer erh&auml;lt eine Nachricht mit der er den Account freischalten kann. Zusätzlich wird ein Kurs erstellt, in dem der Benutzer als Lehrer eingeschrieben ist, und ein Teilnehmer erstellt, mit dem man sich mit "Teilnehmer" und dem bei der Registrierung gew&auml:hlten Passwort, anmelden kann.</p>
	<p> Achtung: Zus&auml;tzlich zur Aktivitierung des Plugins muss bei den Einstellungen auf der Seite \'Übersicht\' im Auswahlfenster \'Selbstregistrierung\' "E-Mail basierte Selbstregistrierung" ausgew&auml;hlt werden.</p>';
$string['auth_skillmanagementnoemail'] = 'An die angegebene E-Mailadresse konnte keine Nachricht versendet werden.';
$string['auth_skillmanagementrecaptcha'] = 'F&uuml;gen Sie reCaptcha zu der Skillmanagement-Selbstregistrierung hinzu, um Ihre Moodle-Installation vor Spam-Benutzern zu sch&uuml;tzen. Besuchen Sie http://www.google.com/recaptcha/learnmore f&uuml;r mehr Details <br /><em>PHP cURL Erweiterung wird ben&ouml;tigt.</em>';
$string['auth_skillmanagementrecaptcha_key'] = 'reCAPTCHA-Element aktivieren';
$string['auth_skillmanagementsettings'] = 'Einstellungen';
$string['pluginname'] = 'Skillmanagement-Selbstregistrierung';
$string['course_description'] = '<p><h2>Definieren Sie Kompetenzfelder für Unternehmen und Team!</h2></p><br/><p>1. Im ersten Schritt werden die Kompetenzen definiert, die in der Organisation benötigt werden. Wir haben für Sie bereits Demo-Daten importiert um die Funktionsweise des Moduls darzustellen. <br/>Klicken Sie auf den Link <b>"Kompetenzen-Verwaltung"</b> um eigene Kompetenzen hinzuzufügen.</p><br/><img src="http://www.skills-management.org/arrow.png"><br/><br/><br/><p>2. Folgen Sie der Anleitung in den Konfigurations-Tabs um mit Ihren Kompetenzen/Skills zu arbeiten.</p><p>3. Wechseln Sie auf den zweiten autogenerierten User (student_[IhrLoginName] um zu sehen, wie sich das Kompetenzprofil entwickelt. Sie können auch direkt auf diesen User zugreifen, indem Sie im Kompetenz-Raster den Mitarbeiter "Mitarbeiter/in" auswählen.</p>';
$string['firstname'] = 'Mitarbeiter';
$string['lastname'] = '/in';
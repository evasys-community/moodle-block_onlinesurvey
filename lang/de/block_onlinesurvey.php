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
 * Plugin "Evaluations (EvaSys)"
 *
 * @package    block_onlinesurvey
 * @copyright  2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/*************************/
/* General.
/*************************/

$string['pluginname'] = 'Evaluationen (EvaSys)';
$string['lti'] = 'LTI';
$string['soap'] = 'SOAP';


/*************************/
/* Appearance settings.
/*************************/

$string['setting_heading_appearance'] = 'Darstellung';
$string['setting_heading_appearance_desc'] = 'Die Einstellungen in diesem Abschnitt legen die Darstellung des EvaSys Blocks fest.';

$string['setting_blocktitle'] = 'Titel';
$string['setting_blocktitle_desc'] = 'Der hier eingegebene Text wird als Block-Titel verwendet.';

$string['setting_presentation'] = 'Darstellungsmodus';
$string['setting_presentation_desc'] = 'Im kompakten Darstellungsmodus wird im EvaSys Block lediglich die Anzahl der offenen Umfragen über eine Grafik ausgewiesen. Im detaillierten Darstellungsmodus werden die Links auf die Onlineumfragen bereits im EvaSys Block dargestellt. In beiden Modi kann eine vergrößerte Listenansicht mit Klick auf die Grafik oder die Lupe geöffnet werden.';
$string['setting_presentation_brief'] = 'Kompakt';
$string['setting_presentation_detailed'] = 'Detailliert';

$string['setting_survey_hide_empty'] = 'Leeren Block verbergen';
$string['setting_survey_hide_empty_desc'] = 'Wenn aktiviert, wird der EvaSys-Block verborgen, wenn keine Umfragen für den Nutzer vorhanden sind. Wenn nicht aktiviert, wird in der Kompaktansicht eine Grafik mit einem Häkchen und dem Text „Keine offenen Evaluationen“ angezeigt, in der Detailansicht eine leere Liste.<br /><em>Bitte beachten Sie: Wenn Sie im LTI-Template eingestellt haben, dass Studierende auch bereits abgeschlossene Umfragen und/oder Umfrageergebnisse einsehen dürfen, dann sollten Sie den Block nicht verbergen. Andernfalls könnten die Studierenden nach Teilnahme an der letzten Umfrage die Daten nicht mehr einsehen.</em>';

$string['setting_survey_show_popupinfo'] = 'Pop-up Meldung aktiv';
$string['setting_survey_show_popupinfo_desc'] = 'Wenn aktiviert, wird bei jedem Log-in eines Studierenden in Moodle ein Pop-up mit Informationen zu offenen Onlineumfragen (so vorhanden) angezeigt.';

$string['setting_survey_popupinfo_title'] = 'Pop-up Titel';
$string['setting_survey_popupinfo_title_desc'] = 'Falls erforderlich kann mit dieser Einstellung der Titel des Pop-up angepasst werden.';
$string['setting_survey_popupinfo_title_default'] = 'Offene Evaluationen';

$string['setting_survey_popupinfo_content'] = 'Pop-up Inhalt';
$string['setting_survey_popupinfo_content_desc'] = 'Falls erforderlich kann mit dieser Einstellung der Inhalt des Pop-up angepasst werden.';
$string['setting_survey_popupinfo_content_default'] = '<p>Liebe(r) Studierende,</p>
<p>aktuell sind Sie für eine oder mehrere Onlineumfragen zur Evaluation der von Ihnen besuchten Lehrveranstaltungen freigeschaltet. Mit Ihrer Teilnahme helfen Sie uns sehr, unser Studienangebot zu verbessern.<br />
Die Links zu den Befragungen werden Ihnen im Block "Evaluationen" angezeigt.</p>
<p>Vielen Dank für Ihre Unterstützung!<br />
Ihr Evaluationsteam</p>';


/*************************/
/* Communication settings.
/*************************/

$string['setting_heading_communication'] = 'Kommunikation';
$string['setting_heading_communication_desc'] = 'Die Einstellungen in diesem Abschnitt legen die Kommunikation des EvaSys Blocks mit EvaSys fest.';

$string['setting_communication_interface'] = 'Kommunikationsprotokoll';
$string['setting_communication_interface_desc'] = 'Hier aktivieren Sie, ob Moodle per LTI oder SOAP mit EvaSys kommunizieren soll.<br /><em>Bitte nehmen Sie im zugehörigen folgenden Abschnitt die notwendigen Einstellungen für das hier gewählte Kommunikationsprotokoll vor.</em>';

$string['setting_useridentifier'] = 'Nutzer-Identifikator';
$string['setting_useridentifier_desc'] = 'Wählen Sie, ob die E-Mail-Adresse oder der Anmeldename eines Nutzers als eindeutiger Nutzer-Identifikator verwendet werden soll.';

$string['setting_customfieldnumberinevasys'] = 'Benutzerdatenfeld in EvaSys';
$string['setting_customfieldnumberinevasys_desc'] = 'Bei Verwendung des Anmeldenamens als Nutzer-Identifikator kann für Studierende eines der ersten drei Benutzerdatenfelder in EvaSys zur Authentifizierung genutzt werden.<br /><em>Bitte beachten Sie: Diese Einstellung ist nur für Studierende relevant. Soll für Dozierende der Anmeldename als Nutzer-Identifikator verwendet werden, muss dieser in EvaSys in den Nutzereigenschaften im Feld "Externe ID" hinterlegt sein.</em>';
$string['setting_customfieldnumber'] = 'Benutzerdatenfeld Nr.';

$string['setting_survey_timeout'] = 'Verbindungstimeout';
$string['setting_survey_timeout_desc'] = 'Maximale Antwortzeit (in Sekunden) des EvaSys-Servers. Falls der EvaSys-Server innerhalb dieser Zeit nicht antwortet, wird die Anfrage abgebrochen und es werden dem Nutzer keine Umfragen angezeigt.';


/*************************/
/* SOAP settings.
/*************************/

$string['setting_heading_soap'] = 'SOAP Einstellungen';
$string['setting_heading_soap_desc'] = 'Die Einstellungen in diesem Abschnitt legen die Kommunikation des EvaSys Blocks mit EvaSys fest.<br /><em>Diese Einstellungen sind nur erforderlich, wenn Sie "SOAP" in der Einstellung "Kommunikationsprotokoll" ausgewählt haben.</em>';

$string['setting_survey_server'] = 'EvaSys SOAP WSDL URL';
$string['setting_survey_server_desc'] = 'URL der WSDL Datei auf dem EvaSys-Server (https://[SERVERNAME]/evasys/services/soapserver-v51.wsdl).<br /><em>Achtung: Wird EvaSys mit mehreren Servern betrieben (Dual Server Option), muss hier der Backend-Server angegeben werden, auf dem Nutzer, wie Administratoren arbeiten. Das verhindert eine zu hohe Last auf dem Onlineumfragenserver.</em>';

$string['setting_survey_login'] = 'EvaSys SOAP Pfad für Onlineumfragen';
$string['setting_survey_login_desc'] = 'URL des EvaSys Online-Umfrage Logins (https://[SERVERNAME]/evasys/).';

$string['setting_survey_user'] = 'EvaSys SOAP Benutzername';
$string['setting_survey_user_desc'] = 'Benutzername des EvaSys SOAP Benutzers.';

$string['setting_survey_pwd'] = 'EvaSys SOAP Passwort';
$string['setting_survey_pwd_desc'] = 'Passwort des EvaSys SOAP Benutzers.';

$string['setting_soap_request_eachtime'] = 'Daten per SOAP bei jeder Ausgabe anfordern';
$string['setting_soap_request_eachtime_desc'] = 'Wenn aktiviert, werden bei jeder Ausgabe des EvaSys Blocks die auszugebenden Daten von EvaSys abgerufen. Wenn nicht aktiviert, werden die Daten nur einmal pro Session (d.h. nur einmal nach jedem Moodle Login) abgerufen.';


/*************************/
/* LTI settings.
/*************************/

$string['setting_heading_lti'] = 'LTI Einstellungen';
$string['setting_heading_lti_desc'] = 'Die Einstellungen in diesem Abschnitt legen die Kommunikation des EvaSys Blocks mit EvaSys fest.<br /><em>Diese Einstellungen sind nur erforderlich, wenn Sie "LTI" in der Einstellung "Kommunikationsprotokoll" ausgewählt haben.</em>';

$string['setting_survey_lti_url'] = 'EvaSys LTI Provider URL';
$string['setting_survey_lti_url_desc'] = 'URL der LTI Provider PHP Datei auf dem EvaSys-Server (https://[SERVERNAME]/customer/lti/lti_provider.php).';

$string['setting_survey_lti_password'] = 'EvaSys LTI Passwort';
$string['setting_survey_lti_password_desc'] = 'Passwort der EvaSys LTI Schnittstelle.';

$string['setting_lti_customparameters'] = 'EvaSys LTI Custom Parameter';
$string['setting_lti_customparameters_desc'] = 'Hier werden die Custom Parameter hinterlegt, mit deren Hilfe Einstellungen für die Anzeige der Umfragen festgelegt werden können, z.B. ob in der Studierendenansicht auch bereits ausgefüllte Umfragen angezeigt werden sollen (learner_show_completed_surveys=1) oder ob in der Dozierendenansicht auch die Reporte der Umfragen abgerufen werden können (instructor_show_report=1). Jeder Parameter wird in einer eigenen Zeile eingegeben. Für ausführliche Informationen zu den verfügbaren Parametern konsultieren Sie bitte das EvaSys LTI Handbuch.';

$string['setting_lti_instructormapping'] = 'LTI Rollenzuweisung "Instructor"';
$string['setting_lti_instructormapping_desc'] = 'Definiert, welche Moodle-Rollen der LTI-Rolle „Instructor“ entsprechen sollen und somit den EvaSys Block als Dozierende angezeigt bekommen sollen.';

$string['setting_lti_learnermapping'] = 'LTI Rollenzuweisung "Learner"';
$string['setting_lti_learnermapping_desc'] = 'Definiert, welche Moodle-Rollen der LTI-Rolle „Learner“ entsprechen sollen und somit den EvaSys Block als Studierende angezeigt bekommen sollen.';


/*************************/
/* Expert settings.
/*************************/

$string['setting_heading_expert'] = 'Experten-Einstellungen';
$string['setting_heading_expert_desc'] = 'Die Einstellungen in diesem Abschnitt müssen im Normalfall nicht angepasst werden und sind speziellen Einsatzzwecken vorbehalten.';

$string['setting_survey_debug'] = 'Debug Modus';
$string['setting_survey_debug_desc'] = 'Wenn aktiviert, werden Debug- und Fehlermeldungen innerhalb des EvaSys Blocks angezeigt.';

$string['setting_additionalcss'] = 'Zusätzliches CSS für iframe';
$string['setting_additionalcss_desc'] = 'Hier können Sie CSS code eingeben, welcher der Seite welche im EvaSys Block geladen wird, hinzugefügt wird. Sie können diese Einstellung dazu nutzen, um den Inhalt des EvaSys Blocks an Ihre Bedürfnisse anzupassen.';

$string['setting_additionalclass'] = 'Zusätzliche Klasse für den Block';
$string['setting_additionalclass_desc'] = 'Wenn aktiviert, wird eine zusätzliche CSS Klasse in Ergänzung zur Klasse block_onlinesurvey zum Block hinzugefügt werden für den Fall dass Umfragen vorliegen. Wenn Umfragen vorliegen wird die Klasse block_onlinesurvey_surveysexist hinzugefügt. Wenn keine Umfragen vorliegen, wird diese Klasse nicht im Block gesetzt sein. Sie können diese Einstellung dazu nutzen, um den EvaSys Block an Ihre Bedürfnisse anzupassen.';

$string['setting_lti_regex_learner'] = 'LTI - Regulärer Ausdruck "Learner"';
$string['setting_lti_regex_learner_desc'] = 'Regulärer Ausdruck, der den Inhalt der LTI-Response nach offenen Onlineumfragen durchsucht. Er muss nur angepasst werden, wenn eigene Templates erstellt oder tiefergehend angepasst wurden, die in den Funktionen von den Standardtemplates abweichen.';

$string['setting_lti_regex_instructor'] = 'LTI - Regulärer Ausdruck "Instructor"';
$string['setting_lti_regex_instructor_desc'] = 'Regulärer Ausdruck, der den Inhalt der LTI-Response nach offenen Onlineumfragen durchsucht. Er muss nur angepasst werden, wenn eigene Templates erstellt oder tiefergehend angepasst wurden, die in den Funktionen von den Standardtemplates abweichen.';


/*************************/
/* Capabilities.
/*************************/

$string['onlinesurvey:addinstance'] = 'Instanz des Blocks Evaluationen (EvaSys) hinzufügen';
$string['onlinesurvey:myaddinstance'] = 'Instanz des Blocks Evaluationen (EvaSys) zu meiner Seite hinzufügen';
$string['onlinesurvey:view'] = 'Block Evaluationen (EvaSys) anzeigen';
$string['onlinesurvey:view_debugdetails'] = 'Debug-Details anzeigen';


/*************************/
/* Block content.
/*************************/

$string['surveys_exist'] = 'Offene Evaluationen';
$string['surveys_exist_not'] = 'Keine offenen Evaluationen';
$string['allsurveys'] = 'Alle Umfragen';


/*************************/
/* Block error messages.
/*************************/

$string['error_config_not_accessible'] = 'Konfiguration nicht zugreifbar';
$string['error_debugmode_missing_capability'] = 'Der Block befindet sich im Debug-Modus. Ihnen fehlen die Rechte, um Inhalte gelistet zu bekommen.';
$string['error_lti_learnermapping_missing'] = 'Learner Rollenmapping fehlt';
$string['error_lti_password_missing'] = 'LTI Passwort fehlt';
$string['error_lti_settings_error'] = 'LTI - Einstellungsfehler';
$string['error_lti_url_missing'] = 'URL des LTI-Providers fehlt';
$string['error_occured'] = '<b>Ein Fehler ist aufgetreten:</b><br /> {$a} <br />';
$string['error_soap_settings_error'] = 'SOAP - Einstellungsfehler';
$string['error_survey_curl_timeout_msg'] = 'Die Umfragen konnten leider nicht abgefragt werden.';
$string['error_survey_login_missing'] = 'Pfad für Onlineumfragen fehlt';
$string['error_survey_pwd_missing'] = 'SOAP-Kennwort fehlt';
$string['error_survey_server_missing'] = 'URL des EvaSys Servers fehlt';
$string['error_survey_user_missing'] = 'SOAP-Benutzername fehlt';
$string['error_userid_not_found'] = 'User-ID nicht gefunden';
$string['error_warning_message'] = '<b>Warnung:</b><br />{$a}<br />';
$string['error_wsdl_namespace'] = 'WSDL Namespace Fehler beim Parsen<br />';


/*************************/
/* Privacy.
/*************************/

$string['privacy:metadata:block_onlinesurvey'] = 'Das EvaSys Block Plugin speichert selbst keine personenbezogenen Daten, überträgt aber personenbezogene Daten von Moodle an die angebundene EvaSys Instanz.';
$string['privacy:metadata:block_onlinesurvey:email'] = 'Die E-Mail Adresse des Nutzers, welche an EvaSys übertragen wird um zu prüfen ob Umfragen vorliegen.';
$string['privacy:metadata:block_onlinesurvey:username'] = 'Der Anmeldename des Nutzers, welcher an EvaSys übertragen wird um zu prüfen ob Umfragen vorliegen.';


/*************************/
/* Misc.
/*************************/

$string['setting_blocktitle_multilangnote'] = 'Wenn erforderlich, können mehrere Sprachen (z.B. Englisch und Deutsch) mit der Moodle Multilanguage filter syntax eingegeben werden (siehe https://docs.moodle.org/en/Multi-language_content_filter).';

<?php

/*
EvaSys Online Surveys - Moodle Block
Copyright (C) 2018 Soon Systems GmbH on behalf of Electric Paper Evaluationssysteme GmbH

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Contact:
Soon-Systems GmbH
Syrlinstr. 5
89073 Ulm
Deutschland

E-Mail: info@soon-systems.de
*/

$string['pluginname'] = 'Evaluationen (EvaSys)';

// settings page - general
$string['blocktitle'] = 'Titel';
$string['blocktitle_description'] = '';

$string['communication_interface'] = 'Kommunikationsweg';
$string['communication_interface_description'] = 'Die Kommunikation mit EvaSys kann per LTI oder SOAP erfolgen. Bitte nehmen Sie im unteren Bereich dieser Maske die notwendigen Einstellungen für den hier gewählten Kommunikationsweg vor.';
$string['soap'] = 'SOAP';
$string['lti'] = 'LTI';

$string['useridentifier'] = 'Nutzer-Identifikator';
$string['useridentifier_description'] = 'Als eindeutiger Identifikator eines Nutzers kann wahlweise der Nutzername oder die E-Mail-Adresse übermittelt werden.';

$string['customfieldnumberinevasys'] = 'Benutzerdatenfeld in EvaSys';
$string['customfieldnumberinevasys_description'] = 'Bei Verwendung des Nutzernamens als Identifikator können Sie hier festlegen, welches der ersten drei EvaSys-Benutzerdatenfelder zur Authentifizierung verwendet werden soll.';
$string['customfieldnumber'] = 'Benutzerdatenfeld Nr.';

// #8984
$string['presentation'] = 'Darstellungsmodus';
$string['presentation_description'] = 'In der Kompaktdarstellung wird im Block die Anzahl offener Umfragen mit Hilfe einer Grafik angezeigt. In der detaillierten Darstellung wird ein Liste der verfügbaren Umfragen anzeigt. Für beide Darstellungen lässt sich per Klick eine vergrößerte Listenansicht aufrufen.';
$string['presentation_brief'] = 'Kompakt';
$string['presentation_detailed'] = 'Detailliert';
// END #8984

// #8977
$string['survey_hide_empty'] = 'Leeren Block verbergen';
$string['survey_hide_empty_description'] = 'Wenn aktiviert, wird der EvaSys-Block ausgeblendet sofern keine Umfragen für den Nutzer vorhanden sind.';
// END #8977
        
$string['survey_show_popupinfo'] = 'Pop-up-Meldung aktiv';
$string['survey_show_popupinfo_description'] = 'Wenn aktiviert, wird Teilnehmern nach dem Login eine Pop-up-Meldung zum Hinweis auf offene Umfragen angezeigt.';

$string['survey_timeout'] = 'Verbindungstimeout in Sekunden';
$string['survey_timeout_description'] = '';

$string['survey_debug'] = 'DEBUG';
$string['survey_debug_description'] = '';

$string['additionalcss'] = 'Zusätzliches CSS für iframe';
$string['additionalcss_description'] = 'Dieses CSS wird am Ende des HEAD im iframe eingefügt.';
// END: settings page - general

// settings page - SOAP
$string['generalheadingsoap'] = 'SOAP';
$string['soap_general_information'] = 'Die folgenden Angaben sind nur erforderlich, wenn Sie "SOAP" zur Kommunikation ausgewählt haben.';

$string['survey_server'] = 'EvaSys Server (SOAP)';
$string['survey_server_description'] = '';

$string['survey_login'] = 'EvaSys Pfad f&uuml;r Onlineumfragen (SOAP)';
$string['survey_login_description'] = '';

$string['survey_user'] = 'EvaSys SOAP-Benutzername';
$string['survey_user_description'] = '';

$string['survey_pwd'] = 'EvaSys SOAP-Kennwort';
$string['survey_pwd_description'] = '';

$string['soap_request_eachtime'] = 'SOAP Request bei Seitenaufruf';
$string['soap_request_eachtime_description'] = 'Wenn aktiviert, wird bei jedem Aufruf der Startseite der Inhalt des Blocks per SOAP aktualisiert. Wenn nicht aktiviert, wird der Block nur beim Login / zu Beginn einer Session aktualisiert.';
// END: settings page - SOAP

// settings page - LTI
$string['generalheadinglti'] = 'LTI';
$string['lti_general_information'] = 'Die folgenden Angaben sind nur erforderlich, wenn Sie "LTI" zur Kommunikation ausgewählt haben.';

$string['survey_lti_url'] = 'URL des LTI-Providers';
$string['survey_lti_url_description'] = '';

// "survey_lti_resourcekey" currently not used -> kept for future
$string['survey_lti_resourcekey'] = 'Anwenderschlüssel';
$string['survey_lti_resourcekey_description'] = '';

$string['survey_lti_password'] = 'LTI-Passwort';
$string['survey_lti_password_description'] = '';

$string['lti_customparameters'] = 'Custom Parameter';
$string['lti_customparameters_description'] = 'Custom Parameter sind Einstellungen, die vom Tool-Provider verwendet werden. Ein Custom-Parameter kann z.B. verwendet werden, um eine bestimmte Information des Providers anzuzeigen. Jeder Parameter sollte in einer eigenen Zeile eingegeben werden, wobei das Format „Name=Wert“ verwendet wird, z.B. "learner_show_completed_surveys=1". Für weitere Informationen konsultieren Sie bitte das EvaSys LTI-Handbuch.';

// lti_regard_coursecontext is not yet supported by EvaSys LTI provider -> kept for future
$string['regard_coursecontext'] = 'Kurskontext berücksichtigen';
$string['regard_coursecontext_description'] = 'Kurskontext berücksichtigen: falls ausgewählt, werden nur Umfragen zum aktuellen Kurs gelistet (sofern) vorhanden';

$string['lti_instructormapping'] = 'Rollenzuweisung "Instructor"';
$string['lti_instructormapping_description'] = 'Hier legen sie fest, welche Moodle-Rollen der LTI-Rolle "Instructor" (= Dozent/in) zugeordnet werden sollen.';

$string['lti_learnermapping'] = 'Rollenzuweisung "Learner"';
$string['lti_learnermapping_description'] = 'Hier legen Sie fest, welche Moodle-Rollen der LTI-Rolle "Learner" (= Studierende/r) zugeordnet werden sollen.';

$string['lti_regex_learner'] = 'Regulärer Ausdruck "Learner"';
$string['lti_regex_learner_description'] = 'Regulärer Ausdruck, der den Inhalt des LTI-Ergebnisses für "Learner" nach offenen Onlineumfragen durchsucht.';

$string['lti_regex_instructor'] = 'Regulärer Ausdruck "Instructor"';
$string['lti_regex_instructor_description'] = 'Regulärer Ausdruck, der den Inhalt des LTI-Ergebnisses für "Instructor" nach offenen Onlineumfragen durchsucht.';
// END: settings page - LTI

// capabilities
$string['onlinesurvey:addinstance'] = 'Instanz des Blocks Evaluationen (EvaSys) hinzufügen';
$string['onlinesurvey:myaddinstance'] = 'Instanz des Blocks Evaluationen (EvaSys) zu meiner Seite hinzufügen';
$string['onlinesurvey:view'] = 'Block Evaluationen (EvaSys) anzeigen';
$string['onlinesurvey:view_debugdetails'] = 'Debug-Details anzeigen';
// END: capabilities

// Block content
$string['tech_error'] = 'Es besteht ein technisches Problem mit dem EvaSys Server.<p>';
$string['conn_works'] = 'Verbindung zum EvaSys-Server erfolgreich getestet.<p>';

// #8977
$string['no_surveys'] = 'Keine offenen Evaluationen';
$string['surveys_exist'] = 'Offene Evaluationen';
// END #8977

$string['popupinfo_dialog_title'] = 'Offene Evaluationen';
$string['popupinfo'] = 'Liebe(r) Studierende,<br />
<br />
aktuell sind Sie für eine oder mehrere Onlineumfragen zur Evaluation der von Ihnen besuchten Lehrveranstaltungen freigeschaltet. Mit Ihrer Teilnahme helfen Sie uns sehr, unser Studienangebot zu verbessern.<br />
Die Links zu den Befragungen werden Ihnen im Block "Evaluationen" angezeigt.<br />
<br />
Vielen Dank für Ihre Unterstützung!<br />
<br />
Ihr Evaluationsteam';

$string['survey_list_header'] = '';

$string['soap_settings_error'] = 'SOAP - Einstellungsfehler';
$string['survey_server_missing'] = 'URL des EvaSys Servers fehlt';
$string['survey_login_missing'] = 'Pfad für Onlineumfragen fehlt';
$string['survey_user_missing'] = 'SOAP-Benutzername fehlt';
$string['survey_pwd_missing'] = 'SOAP-Kennwort fehlt';

$string['lti_settings_error'] = 'LTI - Einstellungsfehler';
$string['lti_url_missing'] = 'URL des LTI-Providers fehlt';
$string['lti_resourcekey_missing'] = 'Anwenderschlüssel fehlt';
$string['lti_password_missing'] = 'LTI Passwort fehlt';
$string['lti_learnermapping_missing'] = 'Learner Rollenmapping fehlt';
$string['userid_not_found'] = 'User-ID nicht gefunden';
$string['config_not_accessible'] = 'Konfiguration nicht zugreifbar';
$string['error_occured'] = '<b>Ein Fehler ist aufgetreten:</b><br /> {$a} <br />';
$string['warning_message'] = '<b>Warnung:</b><br />{$a}<br />';
$string['wsdl_namespace'] = 'WSDL Namespace Fehler beim Parsen<br />';

$string['debugmode_missing_capability'] = 'Der Block befindet sich im Debug-Modus. Ihnen fehlen die Rechte, um Inhalte gelistet zu bekommen.';
// END: Block content

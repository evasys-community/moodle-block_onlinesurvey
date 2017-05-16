# EvaSys - Moodle Portal Connector (Moodle Block):

The present EvaSys Moodle Portal Connector (called ‘Block’ in Moodle terminology) provides Moodle users with the functionality of displaying links to online surveys which have been created for student Moodle users in EvaSys. Making use of SingleSignOn, the students do not have to enter passwords manually in order to take part in surveys.

The internationalization interface of Moodle is supported. In the present version English and German language sets are available.

The Moodle plugin is tested on Moodle 2.6, 2.7, 2.8, 2.9, 3.0 and 3.1. in order to work properly, the PHP5 SOAP extension must be activated.

Please note: since version 1.4 of the integration the PHP cURL module needs to be activated too.

EvaSys 5.1 (1952+) or higher is required in order for the integration to work.

Installation:

01) Copy the onlinesurvey folder into the \blocks folder so that you have a \blocks\onlinesurvey folder. 
02) Log in as administrator and you will see the notification screen with a list of the new plugins.
03) Click [Upgrade Moodle database now] on the bottom of the page to proceed with the installation.
04) Click the button labeled [Continue] to get to the next step of the installation.
05) The option descriptions follow:
		- EvaSys server:				URL of the web service description file of your EvaSys installation     (http://[SERVERNAME]/evasys/services/soapserver-v51.wsdl)
		- EvaSys login:					URL of the EvaSys online survey login (http://[SERVERNAME]/evasys/)
		- EvaSys SOAP user:				User name of the EvaSys SOAP user (default ’soap’)
		- EvaSys SOAP password:			Password of the EvaSys SOAP user (default ’server’)
		- EvaSys connection timeout:	max response time of the EvaSys server
		- Hide block when no
		  open surveys are found:		hides the block in case no open online surveys are found
		- DEBUG:						Turn on / off error reporting on the user interface
06) Click on the [Save changes] button to continue.

The full documentation is available as PDF "EvaSys - Moodle Portal Connector" in the download package.

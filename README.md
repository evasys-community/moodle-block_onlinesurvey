moodle-block_onlinesurvey
=====================================
The moodle plugin allows you to view open surveys within a block in Moodle.

Detail:
By using the "onlinesurvey" plug-in, open surveys for a user are displayed within a block in Moodle. The data request for open surveys of a user can be carried out via SOAP or LTI.
The connection to a user can be established either on the basis of the user name or the email address.
If the user name is used, a specified EvaSys custom field can serve as a method of authentication.
In the case of LTI, it is possible to define customized LTI parameters and to map the LTI specific roles "Instructor" and "Learner" in Moodle, e.g. "Learner" --> "Student".
It is also possible to use a pop-up message to alert participants about open surveys.


Requirements
------------
This plug-in requires Moodle version 3.1 or higher and EvaSys version 7.1 (2151).


Installation
------------

Please install the plug-in into the directory "blocks":
/blocks/onlinesurvey


Usage & Settings
----------------
After installation, the plug-in has to be configured.
To do this, please go to:
Site administration--> Plugins --> Blocks --> Evaluations (EvaSys)

There are three sections:

### General Settings

Here you can enter information about the block title that is displayed, the type of communication, user identification, connection timeout and the display of the pop-up dialog.

### SOAP Settings

Connection data for SOAP is entered here.

### LTI Settings

Connection data for LTI is entered here. Furthermore, additional parameters can be defined which will be transferred as well. You can also define role mappings.
If the pop-up dialog shall be used, a regular expression must be specified in order to determine whether the LTI result contains open surveys.

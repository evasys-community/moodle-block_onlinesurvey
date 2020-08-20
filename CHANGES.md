moodle-block_onlinesurvey
=========================

Changes
-------

### v3.8-r3

* 2020-08-19 - Improvement: Adapt Behat tests to latest changes in the plugin and the EvaSys Demo system.
* 2020-08-19 - Improvement: Make Moodle codechecker a little bit happier.
* 2020-08-19 - Release: Remove DE language pack from the codebase as this language is now managed in AMOS.

### v3.8-r2

* 2020-08-18 - Release: Update version in documentation PDF files.
* 2020-08-17 - Improvement: Make Moodle codechecker a little bit happier.
* 2020-08-17 - Improvement: Replace deprecated LTI library function call with the recommended LTI function call.
* 2020-08-17 - Improvement: Add upstream README.md file to the FontAwesome library which is shipped with this plugin.
* 2020-08-17 - Improvement: Include local library in settings.php only if it's really needed.
* 2020-08-17 - Improvement: Add capability check to the block's iframe content.

### v3.8-r1

* 2020-07-26 - Improvement: Update Travis CI configuration to use moodle-plugin-ci from Moodle HQ.
* 2020-07-26 - Release: Add final documentation PDF files.
* 2020-06-04 - Release: Add updated documentation PDF files.
* 2020-05-22 - Cleanup: Remove admin setting to add an additional class to the block if surveys exist. This was done as disabling the setting does not really give any benefits anymore due to the fact that the same code was used by other features of this plugin as well.
* 2020-05-22 - Feature: Add admin setting to offer the enlarged list view even if there aren't any open surveys.
* 2020-05-21 - Improvement: Add note when expert settings are processed.
* 2020-05-21 - Improvement: Improve the height of the iFrame when there aren't any surveys and when there are surveys.
* 2020-05-12 - Feature: Add admin setting to show a spinner icon while the list of open surveys is being loaded from EvaSys.
* 2020-01-17 - Improvement: Update Travis CI configuration from upstream.
* 2020-01-16 - Release: Update README.md section about supported themes and added Classic which is part of Moodle core since Moodle 3.7.
* 2020-01-16 - Bugfix: Due to MDL-65936, it is not possible anymore to include FontAwesome with pure CSS (i.e. without SCSS) from Moodle 3.8 on. Thus, we have to ship our own packaged version of FontAwesome for the show_surveys.php iFrame unfortunately.
* 2020-01-15 - Release: Raise Moodle Core versions which are tested by Travis for the upcoming release.
* 2020-01-14 - Improvement: Add Behat tests for automated testing. However, these need a working EvaSys backend which has to be setup separately.
* 2020-01-09 - Improvement: Raise the SOAP API Version from 51 to 61 to improve the SOAP survey result display for users who do not have and never had any surveys in EvaSys.
* 2020-01-09 - Release: Raise required Moodle core version to 3.5 as this is the oldest currently supported core version.
* 2019-10-05 - Cleanup: Move the contents of lib.php to locallib.php to slightly improve the performance of the plugin.
* 2019-10-01 - Improvement: Improve the look and feel of the block in compact mode (LTI + SOAP) and in detailed mode (SOAP).
* 2019-09-27 - Feature: Add theme name as body class to iframe.
* 2019-09-26 - Feature: Add admin setting to add an additional class to the block if surveys exist.
* 2019-09-23 - Feature: Add admin setting to configure the pop-up title and content including support of multilang text.
* 2019-03-17 - Cleanup: Remove deprecated function config_save().
* 2019-03-16 - Cleanup: Remove legacy CSS file, move constants to lib.php.
* 2019-03-16 - Improvement: Travis CI: Output warnings for codechecker but do not fail the build.
* 2019-03-16 - Cleanup: Add missing require_login check in show_surveys.php.
* 2019-03-16 - Cleanup: Fix invalid use of curl.
* 2019-03-16 - Bugfix: In SOAP mode, the list of surveys CSS rules for fonts in iframe were not applied.
* 2019-03-09 - Improvement: Improve and amend README.md.
* 2019-03-05 - Improvement: Ship german documentation file with the plugin.
* 2019-02-21 - Improvement: Add Travis CI support.
* 2019-02-20 - Cleanup: Allow all page formats that make sense.
* 2019-02-20 - Cleanup: Remove unneeded db/upgrade.php.
* 2019-02-20 - Improvement: Add missing Privacy API support.
* 2019-02-20 - Cleanup: Restructure settings page and language pack.
* 2019-02-18 - Cleanup: Remove unused support for LTI consumer key and LTI course context.
* 2019-02-18 - Cleanup: Fix plugin file tree and fix coding style. 
* 2019-02-18 - Bugfix: Correct handling of connection timeouts to EvaSys.

### v2.1

* 2018-09-10 - Feature: Add admin setting for adding additional CSS which will be added to the iframe.
* 2018-09-10 - Improvement: When connecting to EvaSys via SOAP, reload the survey list everytime the block is shown instead of only when the user logs in.
* 2018-09-10 - Feature: Add admin setting for hiding the block if no surveys are available.
* 2018-09-10 - Feature: Add admin setting for showing the survey list in a compact mode.

### Changes up to v2.0

Earlier changes to this plugin were not documented individually.

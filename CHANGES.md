moodle-block_onlinesurvey
=========================

Changes
-------

### Unreleased

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

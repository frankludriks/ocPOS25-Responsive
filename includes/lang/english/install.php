<?php
// includes/lang/english/install.php


define('TITLE', 'Install OllaCart Point of Sale');
define('WELCOME_TEXT', 'Welcome to OllaCart Point of Sale!  Verify that the settings in includes/db.php are correct, then click to continue.');
define('DATABASE_STRUCTURE_FAILURE', '<span class="failure">Required database tables not found.  Please verify the database settings in includes/db.php.  If the database tables have prefixes, set the prefix in includes/db_tables.php</span>');
define('EXTEND_DB', 'If these settings are correct, click continue to extend the database.');
define('GRAPHS_NOT_WRITEABLE', '<span class="error">Graph files not writeable.</span>  Reports update graph files.  Please change permissions on the files in the graphs directory to 777 and retry.');
define('CONT', 'Continue');
define('INSTALL', 'Install');
define('RETRY', 'Retry');
define('ADDED', 'Successfully added');
define('ERROR', ' Error: ');
define('FAILED_TO_ADD', '<span class="failure">Failed to add</span>');
define('TO', 'to');
define('TABLE', 'table');
define('ALREADY_EXISTS', 'already exists');
define('CREATED', 'Successfully created');
define('FAILED_TO_CREATE', '<span class="failure">Failed to create</span>');
define('ADMIN_CREATED', '<font color="red">Default username and password are both "admin".  Please be sure to change the password.</font>');
define('END_ERRORS', 'One or more database extensions failed.  Please verify database settings and permissions.');
define('END_SUCCESS', 'Database extensions successful!');
define('INSTALL_LINK', 'Installation Wrap-Up Instructions');
define('THIS_IS_IIS', '<span class="failure">It appears that the web server software is Microsoft IIS server.</span>  Note that OllaCart Point of Sale may work on IIS, but is not supported.<br /><br />');
define('NOT_APACHE', '<span class="failure">It appears that the web server software is not running the Apache web server.</span>  Note that OllaCart Point of Sale may work on other web server platforms, but is only tested on Apache. Other configurations are not officially supported.<br /><br />');

define('FINISHED_INSTRUCTIONS', '<b>Next Steps:</b>
<ol>
    <li>ocPOS has many great features!  Review includes/db.php, adjusting values as needed.</li>
    <li><font size="2"><b>Delete or rename install.php</b></font></li>
</ol>
<b>Finished!</b>');
?>

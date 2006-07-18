<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "phpmailer.class.lng.php") die('Error: Hacking Attempt');
$lang = array(
'mailer_authenticate' => 'SMTP error: Authentification failed.',
'mailer_connect_host' => 'SMTP error: Could not connect to SMTP host.',
'mailer_data_not_accepted' => 'SMTP error: Data not accepted.',
'mailer_encoding' => 'Unknown encoding:',
'mailer_execute' => 'Could not execute this command:',
'mailer_file_access' => 'Acces on this file failed:',
'mailer_file_open' => 'file error: Could not open file:',
'mailer_from_failed' => 'This sender adress is not correct:',
'mailer_instantiate' => 'Could not initialise mail function.',
'mailer_mailer_not_supported' => '-mailer is not supported.',
'mailer_provide_address' => 'Please enter at least one adressee email adress.',
'mailer_recipients_failed' => 'SMTP error: These adressees are not correct:'
);
?>
<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "posts.php") die('Error: Hacking Attempt');

if ($_GET['job'] == 'add') {
	echo head();
	?>
	<?php
	echo foot();
}
elseif ($_GET['job'] == 'add2') {
}
?>

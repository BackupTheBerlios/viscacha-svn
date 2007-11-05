<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

($code = $plugins->load('admin_members_jobs')) ? eval($code) : null;

if ($job == 'emailsearch') {
	echo head();

	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();

	$result = $db->query("SELECT id, title, name FROM {$db->pre}groups WHERE guest = '0' ORDER BY admin DESC, guest ASC, core ASC");
	?>
<form name="form" method="post" action="admin.php?action=members&job=emailsearch2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="3">
   	<span style="float: right;"><a class="button" href="admin.php?action=members&job=newsletter_archive">Newsletter Archive</a></span>
   	Newsletter and E-mail Manager
   </td>
  </tr>
  <tr>
	<td class="mbox" width="50%" colspan="3">
	<b>Help:</b>
	<ul>
	<li>You can type "%" and "_" as wildcards into the keyword. An "_" replaces one single character, a "%" replaces any characters. The wildcards can only be used with the relational operators <b>!=</b> and <b>=</b>.</li>
	<li>
	<b>=</b> means <i>equal</i>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>&lt;</b> means <i>less than</i>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>&gt;</b> means <i>greater than</i>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>!=</b> means <i>not equal</i>.</li>
	</ul>
	</td>
  </tr>
  <tr>
   <td class="mbox" colspan="2" width="50%">Exactness:</td>
   <td class="mbox" colspan="1" width="50%">
   <input type="radio" name="type" value="0"> <b>or</b> (at least one of the input have to lead to a match)<br>
   <input type="radio" name="type" value="1" checked="checked">  <b>and</b> (the whole input have to lead to a match)
   </td>
  </tr>
  <tr>
   <td class="ubox" width="45%">&nbsp;</td>
   <td class="ubox" width="5%">Relational operator</td>
   <td class="ubox" width="50%">&nbsp;</td>
  </tr>
  <tr>
   <td class="mbox">ID:</td>
   <td class="mbox" align="center"><select size="1" name="compare[id]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="id" size="12"></td>
  </tr>
  <tr>
   <td class="mbox">E-mail address:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="mail" size="50"></td>
  </tr>
  <tr>
   <td class="mbox">Date of registry:</td>
   <td class="mbox" align="center"><select size="1" name="compare[regdate]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="regdate[1]" size="3">. <input type="text" name="regdate[2]" size="3">. <input type="text" name="regdate[3]" size="5"> (DD. MM. YYYY)</td>
  </tr>
  <tr>
   <td class="mbox">Posts:</td>
   <td class="mbox" align="center"><select size="1" name="compare[posts]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="posts" size="10"></td>
  </tr>
  <tr>
   <td class="mbox">Gender:</td>
   <td class="mbox" align="center"><select size="1" name="compare[gender]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select name="gender" size="1">
   <option selected="selected" value="">Whatever</option>
   <option value="x">Not specified</option>
   <option value="m">Male</option>
   <option value="w">Female</option>
   </select></td>
  </tr>
  <tr>
   <td class="mbox">Birthday:</td>
   <td class="mbox" align="center"><select size="1" name="compare[birthday]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="birthday[1]" size="3">. <input type="text" name="birthday[2]" size="3">. <input type="text" name="birthday[3]" size="5"> (DD. MM. YYYY)</td>
  </tr>
  <tr>
   <td class="mbox">Last visit:</td>
   <td class="mbox" align="center"><select size="1" name="compare[lastvisit]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="lastvisit[1]" size="3">. <input type="text" name="lastvisit[2]" size="3">. <input type="text" name="lastvisit[3]" size="5"> (DD. MM. YYYY)</td>
  </tr>
  <tr>
   <td class="mbox">Groups:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><select size="3" name="groups" multiple="multiple">
	  <option selected="selected" value="">whatever</option>
	  <?php while ($row = $gpc->prepare($db->fetch_assoc($result))) { ?>
		<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	  <?php } ?>
	</select></td>
  </tr>
  <tr>
   <td class="mbox">Language:</td>
   <td class="mbox" align="center"><select size="1" name="compare[language]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select name="language">
	<option selected="selected" value="">whatever</option>
	<?php foreach ($language as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['language']; ?></option>
	<?php } ?>
</select></td>
  </tr>
  <tr>
   <td class="mbox">Status:</td>
   <td class="mbox" align="center"><select size="1" name="compare[confirm]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select size="1" name="confirm">
	  <option selected="selected" value="">whatever</option>
	  <option value="11">Activated</option>
	  <option value="10">User has to activate the account per e-mail</option>
	  <option value="01">User account has to be activated by the admin</option>
	  <option value="00">User has neither from the admin nor per e-mail been activated</option>
	</select></td>
  </tr>
  <tr>
   <td class="ubox" align="center" colspan="4"><input type="submit" value="Search"></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'emailsearch2') {
	echo head();

	define('DONT_CARE', md5(microtime()));
	$fields = 	array(
		'id' => int,
		'mail' => str,
		'regdate' => arr_int,
		'posts' => int,
		'gender' => str,
		'birthday' => arr_none,
		'lastvisit' => arr_int,
		'groups' => arr_int,
		'language' => int,
		'confirm' => none
	);

	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();

	$type = $gpc->get('type', int);
	if ($type == 0) {
		$sep = ' OR ';
	}
	else {
		$sep = ' AND ';
	}

	$compare = $gpc->get('compare', arr_int);
	foreach ($compare as $key => $cmp) {
		if ($cmp == -1) {
			$compare[$key] = '<';
		}
		elseif ($cmp == 1) {
			$compare[$key] = '>';
		}
		elseif ($cmp == 2) {
			$compare[$key] = '!=';
		}
		else {
			$compare[$key] = '=';
		}
	}
	$sqlwhere = array();
	$input = array();
	foreach ($fields as $key => $data) {
		$value = $gpc->get($key, none);
		if (is_array($value)) {
			$value = implode('', $value);
		}
		if (strpos($value, '%') !== false || strpos($value, '_') !== false) {
			$value = $gpc->get($key, none, DONT_CARE);
			$value = $gpc->save_str($value);
		}
		else {
			$value = $gpc->get($key, $data, DONT_CARE);
		}
		if ($key == 'regdate' || $key == 'lastvisit') {
			if (is_array($value) && array_sum($value) != 0) { // for php version >= 5.1.0
				$input[$key] =  @mktime(0, 0, 0, intval($value[2]), intval($value[1]), intval($value[3]));
				if ($input[$key] == -1 || $input[$key] == false) { // -1 for php version < 5.1.0, false for php version >= 5.1.0
					$input[$key] = DONT_CARE;
				}
			}
			else {
				$input[$key] = DONT_CARE;
			}
		}
		elseif ($key == 'birthday') {
			$value[1] = intval(trim($value[1]));
			if ($value[1] < 1 || $value[1] > 31) {
				$value[1] = '%';
			}
			$value[2] = intval(trim($value[2]));
			if ($value[2] < 1 || $value[2] > 12) {
				$value[2] = '%';
			}
			if (strlen($value[3]) == 2) {
				if ($value[3] > 40) {
					$value[3] += 1900;
				}
				else {
					$value[3] += 2000;
				}
			}
			else {
				$value[3] = intval(trim($value[3]));
			}
			if ($value[3] < 1900 || $value[3] > 2100) {
				$value[3] = '%';
			}
			if ($value[1] == '%' && $value[2] == '%' && $value[3] == '%') {
				$input[$key] = DONT_CARE;
			}
			else {
				$input[$key] = $value[3].'-'.$value[2].'-'.$value[1];
			}
		}
		elseif ($key == 'gender') {
			if (empty($value)) {
				$input[$key] = DONT_CARE;
			}
			elseif ($value == 'x') {
				$input[$key] = '';
			}
			else {
				$input[$key] = $value;
			}
		}
		else {
			if (!isset($_REQUEST[$key])) {
				$_REQUEST[$key] = null;
			}
			if (empty($_REQUEST[$key]) && $_REQUEST[$key] != '0') {
				$input[$key] = DONT_CARE;
			}
			else {
				$input[$key] = $value;
			}
		}

		if (!isset($compare[$key])) {
			$compare[$key] = '=';
		}

		if ($input[$key] != DONT_CARE) {
			if (strpos($input[$key], '%') !== false || strpos($input[$key], '_') !== false) {
				if ($compare[$key] == '=') {
					$compare[$key] = 'LIKE';
				}
				elseif ($compare[$key] == '!=') {
					$compare[$key] = 'NOT LIKE';
				}
			}
			$sqlwhere[] = " `{$key}` {$compare[$key]} '{$input[$key]}' ";
		}
	}

	if (count($sqlwhere) > 0) {
		$query = 'SELECT id FROM '.$db->pre.'user WHERE '.implode($sep, $sqlwhere);
		$result = $db->query($query, __LINE__, __FILE__);
		$users = array();
		while ($row = $db->fetch_assoc($result)) {
			$users[] = $row['id'];
		}
		$count = $db->num_rows($result);
	}
	else {
		$count = 0;
	}
	?>
	<form name="form" action="admin.php?action=members" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr>
		  <td class="obox" colspan="2">Newsletter and E-mail Manager</td>
		</tr>
		<?php if ($count == 0) { ?>
		<tr>
		  <td class="mbox" colspan="2">No search results!</td>
		</tr>
		<?php } else { ?>
		<tr>
		  <td class="mbox" width="50%">Filter Recipients:</td>
		  <td class="mbox" width="50%">
		    <select name="filter">
		      <option value="0">Include users that have subscribed to important admin emails</option>
		      <option value="1">Include users that have subscribed to all admin emails</option>
		      <option value="2">Include all users</option>
		    </select>
		  </td>
		</tr>
		<tr>
		  <td class="ubox" colspan="2" align="center">
		    <input type="hidden" name="users" value="<?php echo implode(',', $users); ?>" />
		  	<select name="job">
		  		<option value="emaillist">Export E-mail Addresses</option>
		  		<option value="newsletter">Send Newsletter</option>
		  	</select> <input type="submit" name="submit" value="Go">
		  </td>
		</tr>
		<?php } ?>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'emaillist') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	$users = $gpc->get('users', none);
	$filter = $gpc->get('filter', int);

	$filter = '';
	if ($filter == 1) {
		$filter = " opt_newsletter = '1' AND ";
	}
	else if ($filter == 0) {
		$filter = " (opt_newsletter = '2' OR opt_newsletter = '1') AND ";
	}

	if (empty($users) == false) {
		$delete = array_merge($delete, explode(',', $users));
	}
	$ids = array();
	foreach ($delete as $id) {
		if (is_id($id)) {
			$ids[] = $id;
		}
	}

	$result = $db->query("SELECT DISTINCT mail FROM {$db->pre}user WHERE {$filter} id IN (".implode(',', $ids).")", __LINE__, __FILE__);
	$emails = array();
	while ($row = $db->fetch_assoc($result)) {
		$emails[] = $row['mail'];
	}

	$template = $gpc->get('template', none);
	if (empty($template)) {
		$template = ',';
	}
	?>
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox">Export E-mail Addresses</td>
  </tr>
  <tr>
   <td class="mbox"><textarea class="fullwidth" cols="125" rows="25"><?php echo implode($template, $emails); ?></textarea></td>
  </tr>
 </table>
<br />
<form name="form" method="post" action="admin.php?action=members&amp;job=emaillist">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Change Settings</td>
  </tr>
  <tr>
   <td class="mbox" width="50%">Separator:<br><span class="stext">Seperator between e-mail-addresses. Default: Comma</span></td>
   <td class="mbox" width="50%"><textarea name="template" cols="10" rows="2"></textarea></td>
  </tr>
  <tr>
   <td class="ubox" width="100%" colspan="2" align="center">
    <input type="hidden" name="users" value="<?php echo implode(',', $ids); ?>">
   	<input type="submit" name="Submit" value="Submit">
   </td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'newsletter') {
	$delete = $gpc->get('delete', arr_int);
	$users = $gpc->get('users', none);
	$filter = $gpc->get('filter', int);

	$filter = '';
	if ($filter == 1) {
		$filter = " opt_newsletter = '1' AND ";
	}
	else if ($filter == 0) {
		$filter = " (opt_newsletter = '2' OR opt_newsletter = '1') AND ";
	}

	if (empty($users) == false) {
		$delete = array_merge($delete, explode(',', $users));
	}
	$ids = array();
	foreach ($delete as $id) {
		if (is_id($id)) {
			$ids[] = $id;
		}
	}

	echo head();
	if (count($ids) == 0) {
		error('admin.php?action=members&job=emailsearch', 'No Members found.');
	}

	$result = $db->query("SELECT id FROM {$db->pre}user WHERE {$filter} id IN (".implode(',', $ids).") GROUP BY mail", __LINE__, __FILE__);
	$ids = array();
	while ($row = $db->fetch_assoc($result)) {
		$ids[] = $row['id'];
	}
?>
<form name="form" method="post" action="admin.php?action=members&amp;job=newsletter2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Compose Newsletter</td>
  </tr>
  <tr>
   <td class="mbox" width="40%">Recipients:</td>
   <td class="mbox" width="60%"><?php echo count($ids); ?></td>
  </tr>
  <tr>
   <td class="mbox" width="40%">From:</td>
   <td class="mbox" width="60%">
   	E-Mail: <input type="text" name="from_mail" size="60" value="<?php echo $config['forenmail']; ?>"><br />
   	Name: <input type="text" name="from_name" size="60" value="<?php echo $config['fname']; ?>">
   </td>
  </tr>
  <tr>
   <td class="mbox" width="40%">Subject:</td>
   <td class="mbox" width="60%"><input type="text" name="title" size="70"></td>
  </tr>
  <tr>
   <td class="mbox" width="40%">Format:</td>
   <td class="mbox" width="60%">
   	<input type="radio" name="type" value="h"> (x)HTML
   	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
   	<input type="radio" name="type" value="p" checked="checked"> Plain text
   </td>
  </tr>
  <tr>
   <td class="mbox" width="40%">
   	Message:<br />
   	<span class="stext">
   	In the message, you may use <code>{$user.id}</code>, <code>{$user.name}</code> or {$user.mail}.
   	</span>
   </td>
   <td class="mbox" width="60%"><textarea name="message" rows="10" cols="70"></textarea></td>
  </tr>
  <tr>
   <td class="mbox" width="40%">
   	Email to send at once:<br />
   	<span class="stext">This is the number of messages to be sent in a batch. You should keep this relatively low.</span>
   </td>
   <td class="mbox" width="60%"><input type="text" name="batch" size="10" value="20"></td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center">
    <input type="hidden" name="users" value="<?php echo implode(',', $ids); ?>">
   	<input type="submit" name="Submit" value="Submit">
   </td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'newsletter2') {

	$data = array(
		'users' => $gpc->get('users', none),
		'from_mail' => $gpc->get('from_mail', none, $config['forenmail']),
		'from_name' => $gpc->get('from_name', none, $config['fname']),
		'title' => $gpc->get('title', none),
		'message' => $gpc->get('message', none),
		'batch' => $gpc->get('batch', int, 20),
		'type' => $gpc->get('type', none, 'p')
	);

	if (!check_mail($data['from_mail'])) {
		$data['from_mail'] = $config['forenmail'];
	}

	$dbd = array_map(array($db, 'escape_string'), $data);

	$db->query("INSERT INTO {$db->pre}newsletter (receiver, sender, title, content, time, type) VALUES ('{$dbd['users']}','{$dbd['from_name']} <{$dbd['from_mail']}>','{$dbd['title']}','{$dbd['message']}','".time()."', '{$dbd['type']}')", __LINE__, __FILE__);
	$nid = $db->insert_id();

	$data['chunks'] = array_chunk(explode(',', $data['users']), $data['batch']);

	$cache = new CacheItem('newsletter_'.$nid);
	$cache->set($data);
	$cache->export();

	echo head();
	ok('admin.php?action=members&job=newsletter3&id='.$nid, 'The data has been saved successfully. The e-mails will be sent step by step, now.');
}
elseif ($job == 'newsletter3') {
	$page = $gpc->get('page', int, 1);
	$id = $gpc->get('id', int);

	$cache = new CacheItem('newsletter_'.$id);
	$cache->import();
	$data = $cache->get();

	require_once("classes/mail/class.phpmailer.php");
	require_once('classes/mail/extended.phpmailer.php');
	$mail = new PHPMailer();
	$mail->From = $data['from_mail'];
	$mail->FromName = $data['from_name'];
	$mail->Subject = $data['title'];
	if ($config['smtp'] == 1) {
		$mail->Mailer = "smtp";
		$mail->IsSMTP();
		$mail->Host = $config['smtp_host'];
		if ($config['smtp_auth'] == 1) {
			$mail->SMTPAuth = true;
			$mail->Username = $config['smtp_username'];
			$mail->Password = $config['smtp_password'];
		}
	}
	elseif ($config['sendmail'] == 1) {
		$mail->IsSendmail();
		$mail->Mailer   = "sendmail";
		$mail->Sendmail = $config['sendmail_host'];
	}
	else {
		$mail->IsMail();
	}
	$ids = implode(',', $data['chunks'][$page-1]);
	$result = $db->query("SELECT id, name, mail FROM {$db->pre}user WHERE id IN ($ids)", __LINE__, __FILE__);
	while ($row = $db->fetch_assoc($result)) {
		$message = str_replace('{$user.id}', $row['id'], $data['message']);
		$message = str_replace('{$user.name}', $row['name'], $message);
		$message = str_replace('{$user.mail}', $row['mail'], $message);
		if ($data['type'] == 'h') {
			$mail->IsHTML(true);
	    	$mail->Body = $message;
	    	// No alternative Body atm
	    	// $plain = preg_replace('~<br(\s*/)?\>(\r\n|\r|\n){0,1}~i', "\r\n", $message);
	    	// $mail->AltBody = strip_tags($plain);
	    }
	    else {
	    	$mail->Body = $message;
	    }
	    $mail->AddAddress($row['mail']);
		$mail->Send();
	    $mail->ClearAddresses();
	}

	$steps = count($data['chunks']);
	$page2 = $page+1;
	$all = count(explode(',', $data['users']));
	$sec = 3;
	$left = (($steps-$page)*$sec)/60;
	if ($left == 0) {
		$left = 'Task Finished!';
	}
	else if ($left < 60) {
		 $left = ceil($left*60).' Seconds';
	}
	else if ($left > 60) {
		 $left = round($left/60, 1).' Hours';
	}
	else {
		$left = ceil($left).' Minutes';
	}

	if ($steps > $page) {
		$percent = (100/$all)*(($page)*$data['batch']);
		$url = 'admin.php?action=members&amp;job=newsletter3&amp;id='.$id.'&amp;page='.$page2;
	}
	else {
		$percent = 100;
		$url = 'admin.php?action=members&amp;job=newsletter_view&amp;id='.$id;
		$cache->delete();
	}

	$htmlhead .= '<meta http-equiv="refresh" content="'.$sec.'; url='.$url.'">';
	echo head();
	?>
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox">Sending Newsletter: Step <?php echo $page; ?> of <?php echo $steps; ?></td>
	  </tr>
	  <tr>
	   <td class="mbox">
	   	<div style="width: 600px; border: 1px solid black; background-color: white;"><div style="width: <?php echo ceil($percent)*6; ?>px; background-color: steelblue;">&nbsp;</div></div>
	   	Progress: <?php echo round($percent, 1); ?>%<br />
	   	Estimated time left: <?php echo $left; ?>
	   </td>
	  </tr>
	  <tr>
	  	<td class="ubox" align="center"><a href="<?php echo $url; ?>">Click here if you are not redirected automatically in 5 seconds.</a></td>
	  </tr>
	 </table>
	<?php
	echo foot();
}
elseif ($job == 'newsletter_archive') {
	$result = $db->query('SELECT * FROM '.$db->pre.'newsletter ORDER BY time', __LINE__, __FILE__);
	echo head();
?>
<form name="form" method="post" action="admin.php?action=members&amp;job=newsletter_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="4">Newsletter Archive</td>
  </tr>
  <tr>
   <td class="ubox">Delete<br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> All</span></td>
   <td class="ubox">Subject</td>
   <td class="ubox">Date, Time</td>
   <td class="ubox">Recipients</td>
  </tr>
<?php while ($row = $db->fetch_assoc($result)) { ?>
  <tr>
   <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
   <td class="mbox"><a href="admin.php?action=members&amp;job=newsletter_view&id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
   <td class="mbox"><?php echo gmdate('d.m.Y, H:i', times($row['time'])); ?></td>
   <td class="mbox"><?php echo count(explode(',', $row['receiver'])); ?></td>
  </tr>
<?php } ?>
  <tr>
   <td class="ubox" colspan="4" align="center"><input type="submit" name="Submit" value="Delete"></td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'newsletter_view') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}newsletter WHERE id = '{$id}' LIMIT 1");
	$row = $gpc->prepare($db->fetch_assoc($result));
	echo head();
?>
<form name="form" method="post" action="admin.php?action=members&job=newsletter_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Newsletter <?php echo $row['id']; ?></td>
  </tr>
  <tr>
   <td class="mbox">Title:</td>
   <td class="mbox"><?php echo $row['title']; ?></td>
  </tr>
  <tr>
   <td class="mbox">From:</td>
   <td class="mbox"><?php echo $row['sender']; ?></td>
  </tr>
  <tr>
   <td class="mbox">Recipients:</td>
   <td class="mbox"><?php echo count(explode(',', $row['receiver'])); ?></td>
  </tr>
  <tr>
   <td class="mbox">Time sent:</td>
   <td class="mbox"><?php echo gmdate('d.m.Y, H:i', times($row['time'])); ?></td>
  </tr>
  <tr>
   <td class="mbox">Format:</td>
   <td class="mbox"><?php echo iif($row['type'] == 'h', '(x)HTML', 'Plain text'); ?></td>
  </tr>
  <tr>
   <td class="ubox" colspan="2">Text:</td>
  </tr>
  <tr>
   <td class="mbox" colspan="2">
   <?php if ($row['type'] == 'h') { ?>
   <iframe src="admin.php?action=members&amp;job=newsletter_html&amp;id=<?php echo $row['id']; ?>" width="700" height="500"></iframe>
   <?php } else { ?>
   <pre><?php echo $row['content']; ?></pre>
   <?php } ?>
   </td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="hidden" name="delete[]" value="<?php echo $row['id']; ?>"><input type="submit" name="Submit" value="Delete"></td>
  </tr>
 </table>
</form>
<?php
	echo foot();
}
elseif ($job == 'newsletter_html') {
	$id = $gpc->get('id', int);
	$result = $db->query("SELECT * FROM {$db->pre}newsletter WHERE id = '{$id}' LIMIT 1");
	$row = $db->fetch_assoc($result);
	echo $row['content'];
}
elseif ($job == 'newsletter_delete') {
	echo head();
	$del = $gpc->get('delete', arr_int);
	if (count($del) > 0) {
		$ids = implode(',', $del);
		$db->query("DELETE FROM {$db->pre}newsletter WHERE id IN ($ids)", __LINE__, __FILE__);
		$anz = $db->affected_rows();
		ok('admin.php?action=members&job=newsletter_archive', $anz.' newsletters have been deleted!');
	}
	else {
		error('admin.php?action=members&job=newsletter_archive', 'No newsletter deleted.');
	}

}
elseif ($job == 'merge') {
	echo head();
	?>
<form name="form2" method="post" action="admin.php?action=members&job=merge2">
<table class="border">
<tr><td class="obox" colspan="2">Merge Users</td></tr>
<tr><td class="ubox" colspan="2">
Here you can merge two user accounts into one.
The "base-member" persists and its datas are set as default.
The contributions, PNs etc. from the "needless-member" will be transcribed to the base member.
Missing datas from the base member will be taken from the needless member.
Afterwards the needless member will be deleted.
</td></tr>
<tr>
<td class="mbox">Base-member:</td>
<td class="mbox">
	<input type="text" name="name1" id="name1" onkeyup="ajax_searchmember(this, 'sugg1');" size="40" /><br />
	<span class="stext">Suggestions: <span id="sugg1"></span></span>
</td>
</tr>
<td class="mbox">Needless-member:</td>
<td class="mbox">
	<input type="text" name="name2" id="name2" onkeyup="ajax_searchmember(this, 'sugg2');" size="40" /><br />
	<span class="stext">Suggestions: <span id="sugg2"></span></span>
</td>
</tr>
<tr>
<td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Delete"></td>
</tr>
</table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'merge2') {
	echo head();
	$base = $gpc->get('name1', str);
	$old = $gpc->get('name2', str);

	// Step 1: Getting data
	$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE name = "'.$base.'" LIMIT 1');
	$result2 = $db->query('SELECT * FROM '.$db->pre.'user WHERE name = "'.$old.'" LIMIT 1');
	if ($db->num_rows($result) != 1 || $db->num_rows($result2) != 1) {
		error('admin.php?action=members&job=merge', 'At least one of the selected users could not be found.');
	}
	$base = $db->fetch_assoc($result);
	$old = $db->fetch_assoc($result2);

	// Step 2: Update abos
	$db->query("UPDATE {$db->pre}abos SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Step 4: Update mods
	$db->query("UPDATE {$db->pre}moderators SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Step 5: Update pms
	$db->query("UPDATE {$db->pre}pm SET pm_to = '".$base['id']."' WHERE pm_to = '".$old['id']."'");
	$db->query("UPDATE {$db->pre}pm SET pm_from = '".$base['id']."' WHERE pm_from = '".$old['id']."'");
	// Step 6: Update posts
	$db->query("UPDATE {$db->pre}replies SET name = '".$base['id']."' WHERE name = '".$old['id']."' AND email = ''");
	// Step 7:Update topics
	$db->query("UPDATE {$db->pre}topics SET name = '".$base['id']."' WHERE name = '".$old['id']."'");
	$db->query("UPDATE {$db->pre}topics SET last_name = '".$base['id']."' WHERE last_name = '".$old['id']."'");
	// Step 8: Update uploads
	$db->query("UPDATE {$db->pre}uploads SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Step 9: Delete pic
	removeOldImages('uploads/pics/', $old['id']);
	// Step 10: Update votes
	$db->query("UPDATE {$db->pre}votes SET mid = '".$base['id']."' WHERE mid = '".$old['id']."'");
	// Setp 11: Update User data
	$newdata = array();
	$base = $gpc->save_str($base);
	$old = $gpc->save_str($old);
	if ($base['regdate'] > $old['regdate']) {
		$newdata[] = "regdate = '{$old['regdate']}'";
	}
	if (empty($base['fullname']) && !empty($old['fullname'])) {
		$newdata[] ="fullname = '{$old['fullname']}'";
	}
	if (empty($base['hp']) && !empty($old['hp'])) {
		$newdata[] ="hp = '{$old['hp']}'";
	}
	if (empty($base['signature']) && !empty($old['signature'])) {
		$newdata[] ="signature = '{$old['signature']}'";
	}
	if (empty($base['about']) && !empty($old['about'])) {
		$newdata[] ="about = '{$old['about']}'";
	}
	if (!empty($old['notice'])) {
		if (empty($base['notice'])) {
			$notice = $old['notice'];
		}
		else {
			$notice = $base['notice'].'[VSEP]'.$old['notice'];
		}
		$newdata[] ="notice = '{$notice}'";
	}
	if (empty($base['location']) && !empty($old['location'])) {
		$newdata[] ="location = '{$old['location']}'";
	}
	if (empty($base['pic']) && !empty($old['pic'])) {
		$newdata[] ="pic = '{$old['pic']}'";
	}
	if (empty($base['yahoo']) && !empty($old['yahoo'])) {
		$newdata[] ="yahoo = '{$old['yahoo']}'";
	}
	if (empty($base['msn']) && !empty($old['msn'])) {
		$newdata[] ="msn = '{$old['msn']}'";
	}
	if (empty($base['skype']) && !empty($old['skype'])) {
		$newdata[] ="skype = '{$old['skype']}'";
	}
	if (empty($base['jabber']) && !empty($old['jabber'])) {
		$newdata[] ="jabber = '{$old['jabber']}'";
	}
	if (empty($base['aol']) && !empty($old['aol'])) {
		$newdata[] ="aol = '{$old['aol']}'";
	}
	if (empty($base['icq']) && !empty($old['icq'])) {
		$newdata[] ="icq = '{$old['icq']}'";
	}
	if ($base['birthday'] == '0000-00-00' && $old['birthday'] != '0000-00-00') {
		$newdata[] ="birthday = '{$old['birthday']}'";
	}
	if (empty($base['timezone']) && !empty($old['timezone'])) {
		$newdata[] ="timezone = '{$old['timezone']}'";
	}
	$g1 = explode(',', $base['groups']);
	natsort($g1);
	$base['groups'] = implode(',', $g1);
	$g2 = explode(',', $old['groups']);
	$g = array_merge($g1, $g2);
	$g = array_unique($g);
	natsort($g);
	$groups = implode(',', $g);
	if ($groups != $base['groups']) {
		$groups = saveCommaSeparated($groups);
		$newdata[] ="groups = '{$groups}'";
	}
	if (count($newdata) > 0) {
		$db->query("UPDATE {$db->pre}user SET ".implode(', ', $newdata)." WHERE id = '".$base['id']."' LIMIT 1");
	}
	// Step 12: Delete old user
	$db->query("DELETE FROM {$db->pre}user WHERE id = '".$old['id']."'");

	$cache = $scache->load('memberdata');
	$cache = $cache->delete();

	ok('admin.php?action=members&job=manage', "{$old['name']}'s data is converted to {$base['name']}'s Account.");
}
elseif ($job == 'manage') {
	send_nocache_header();
	echo head();
	$sort = $gpc->get('sort', str);
	$order = $gpc->get('order', int);
	$letter = $gpc->get('letter', str);
	$page = $gpc->get('page', int, 1);

	$count = $db->fetch_num($db->query('SELECT COUNT(*) FROM '.$db->pre.'user'));
	$temp = pages($count[0], "admin.php?action=members&job=manage&sort=".$sort."&amp;letter=".$letter."&amp;order=".$order."&amp;", 25);

	if ($order == '1') $order = 'desc';
	else $order = 'asc';

	if ($sort == 'regdate') $sort = 'regdate';
	elseif ($sort == 'location') $sort = 'location';
	elseif ($sort == 'posts') $sort = 'posts';
	elseif ($sort == 'lastvisit') $sort = 'lastvisit';
	else $sort = 'name';

	$start = $page*25;
	$start = $start-25;

	$result = $db->query('SELECT * FROM '.$db->pre.'user ORDER BY '.$sort.' '.$order.' LIMIT '.$start.',25');
	?>
	<form name="form" action="admin.php?action=members&job=delete" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr>
		  <td class="obox" colspan="8">
		  <?php if ($my->settings['admin_interface'] == 1) { ?>
		  <span style="float: right;">
		  <a class="button" href="admin.php?action=members&amp;job=register">Add new Member</a>
		  <a class="button" href="admin.php?action=members&amp;job=memberrating">Memberratings</a>
		  <a class="button" href="admin.php?action=members&amp;job=merge">Merge Users</a>
		  <a class="button" href="admin.php?action=members&amp;job=recount">Recount Post Counts</a>
		  </span>
		  <?php } ?>
		  Member List</td>
		</tr>
		<tr>
		  <td class="ubox" colspan="8"><span style="float: right;"><?php echo $temp; ?></span><?php echo $count[0]; ?> Members</td>
		</tr>
		<tr>
		  <td class="obox">Delete<br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> All</span></td>
		  <td class="obox">Name
		  <a href="admin.php?action=members&amp;job=manage&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&amp;job=manage&amp;order=1&amp;page=<?php echo $page; ?>&amp;letter=<?php echo $letter; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Email</td>
		  <td class="obox">Posts
		  <a href="admin.php?action=members&amp;job=manage&amp;sort=posts&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&amp;job=manage&amp;sort=posts&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Residence
		  <a href="admin.php?action=members&amp;job=manage&samp;ort=location&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&amp;job=manage&amp;sort=location&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Last Visit
		  <a href="admin.php?action=members&amp;job=manage&amp;sort=lastvisit&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&amp;job=manage&amp;sort=lastvisit&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		  <td class="obox">Registered on
		  <a href="admin.php?action=members&amp;job=manage&amp;sort=regdate&amp;letter=<?php echo $letter; ?>&amp;page=<?php echo $page; ?>"><img src="admin/html/images/asc.gif" border=0 alt="Ascending"></a>
		  <a href="admin.php?action=members&amp;job=manage&amp;sort=regdate&amp;letter=<?php echo $letter; ?>&amp;order=1&amp;page=<?php echo $page; ?>"><img src="admin/html/images/desc.gif" border=0 alt="Descending"></a></td>
		</tr>
	<?php
	while ($row = $gpc->prepare($db->fetch_object($result))) {
		$row->regdate = gmdate('d.m.Y', times($row->regdate));
		if ($row->lastvisit == 0) {
			$row->lastvisit = 'Never';
		}
		else {
			$row->lastvisit = gmdate('d.m.Y H:i', times($row->lastvisit));
		}
		?>
		<tr>
		  <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row->id; ?>"></td>
		  <td class="mbox"><a title="Edit" href="admin.php?action=members&job=edit&id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a><?php echo iif($row->fullname,"<br><i>".$row->fullname."</i>"); ?></td>
		  <td class="mbox" align="center"><a href="mailto:<?php echo $row->mail; ?>">Email</a></td>
		  <td class="mbox"><a title="Recount" href="admin.php?action=members&amp;job=recount&amp;id=<?php echo $row->id; ?>"><?php echo $row->posts; ?></a></td>
		  <td class="mbox"><?php echo iif($row->location,$row->location,'-'); ?></td>
		  <td class="mbox"><?php echo $row->lastvisit; ?></td>
		  <td class="mbox"><?php echo $row->regdate; ?></td>
		</tr>
		<?php
	}
	?>
		<tr>
		  <td class="ubox" colspan="8"><span style="float: right;"><?php echo $temp; ?></span><input type="submit" name="submit" value="Delete"></td>
		</tr>
	</table>
	</form>
	 <?php if ($my->settings['admin_interface'] == 0) { ?>
	 <br class="minibr" />
	 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
	  <tr>
	   <td class="obox center">
		  <a class="button" href="admin.php?action=members&amp;job=register">Add new Member</a>
		  <a class="button" href="admin.php?action=members&amp;job=search">Search for Members</a>
		  <a class="button" href="admin.php?action=members&amp;job=inactive">Inactive Members</a>
		  <a class="button" href="admin.php?action=members&amp;job=memberrating">Memberratings</a>
		  <a class="button" href="admin.php?action=members&amp;job=merge">Merge Users</a>
		  <a class="button" href="admin.php?action=members&amp;job=recount">Recount Post Counts</a>
	   </td>
	  </tr>
	 </table>
	 <?php } ?>
	<?php
	echo foot();
}
elseif ($job == 'memberrating') {
	echo head();
	$page = $gpc->get('page', int, 1);

	$count = $db->fetch_num($db->query('SELECT COUNT(*) FROM '.$db->pre.'postratings WHERE aid != "0" GROUP BY aid'));
	$temp = pages($count[0], "admin.php?action=members&job=memberrating&amp;", 25);

	$start = $page*25;
	$start = $start-25;

	$change = array('m' => 'male', 'w' => 'female', '' => '-');

	$result = $db->query('
	SELECT u.*, avg(p.rating) AS ravg, count(*) AS rcount
	FROM '.$db->pre.'postratings AS p
		LEFT JOIN '.$db->pre.'user AS u ON p.aid = u.id
	WHERE aid != "0"
	GROUP BY aid
	ORDER BY ravg DESC
	LIMIT '.$start.',25
	', __LINE__, __FILE__);
	?>
	<form name="form" action="admin.php?action=members&job=delete" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr>
		  <td class="obox" colspan="6">Memberrating</td>
		</tr>
		<tr>
		  <td class="ubox" colspan="6"><span style="float: right;"><?php echo $temp; ?></span><?php echo $count[0]; ?> rated members</td>
		</tr>
		<tr>
		  <td class="obox">Delete<br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> All</span></td>
		  <td class="obox">Name</td>
		  <td class="obox">Rating (amount of ratings)</td>
		  <td class="obox">Email</td>
		  <td class="obox">Last Visit</td>
		  <td class="obox">Registered on</td>
		</tr>
	<?php
	while ($row = $gpc->prepare($db->fetch_object($result))) {
		$row->regdate = gmdate('d.m.Y', times($row->regdate));
		if ($row->lastvisit == 0) {
			$row->lastvisit = 'Never';
		}
		else {
			$row->lastvisit = gmdate('d.m.Y H:i', times($row->lastvisit));
		}
		$percent = round((($row->ravg*50)+50));
		?>
		<tr>
		  <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row->id; ?>"></td>
		  <td class="mbox"><a title="Edit" href="admin.php?action=members&job=edit&id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a><?php echo iif($row->fullname,"<br><i>".$row->fullname."</i>"); ?></td>
		  <td class="mbox"><img src="images.php?action=memberrating&id=<?php echo $row->id; ?>" alt="<?php echo $percent; ?>%" title="<?php echo $percent; ?>%"  /> <?php echo $percent; ?>% (<?php echo $row->rcount; ?>)</td>
		  <td class="mbox" align="center"><a href="mailto:<?php echo $row->mail; ?>">Email</a></td>
		  <td class="mbox"><?php echo $row->lastvisit; ?></td>
		  <td class="mbox"><?php echo $row->regdate; ?></td>
		</tr>
		<?php
	}
	?>
		<tr>
		  <td class="ubox" colspan="6"><span style="float: right;"><?php echo $temp; ?></span><input type="submit" name="submit" value="Delete"></td>
		</tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'recount') {
	echo head();
	$id = $gpc->get('id', int);
	if (is_id($id)) {
		$result = $db->query("SELECT id, posts FROM {$db->pre}user WHERE id = '{$id}'");
		if ($db->num_rows($result) != 1) {
			error('admin.php?action=members&job=manage', 'User not found!');
		}
		else {
			$user = $db->fetch_assoc($result);
			$posts = UpdateMemberStats($id);
			$diff = $posts - $user['posts'];
			ok('admin.php?action=members&job=manage', "Number of posts successfully recounted. The change is {$diff} posts.");
		}
	}
	else {
		$confirm = $gpc->get('confirm', int);
		if ($confirm > 0) {

			$cat_bid_obj = $scache->load('cat_bid');
			$boards = $cat_bid_obj->get();
			$id = array();
			foreach ($boards as $board) {
				if ($board['count_posts'] == 0) {
					$id[] = $board['id'];
				}
			}

			$result = $db->query("
				SELECT COUNT(*) AS new, u.posts, u.id
				FROM {$db->pre}replies AS r
					LEFT JOIN {$db->pre}user AS u ON u.id = r.name
				WHERE r.guest = '0'". iif(count($id) > 0, " AND r.board NOT IN (".implode(',', $id).")") ."
				GROUP BY u.id
			", __LINE__, __FILE__);


			$i = 0;
			while ($row = $db->fetch_assoc($result)) {
				if ($row['new'] != $row['posts']) {
					$i++;
					$db->query("UPDATE {$db->pre}user SET posts = '{$row['new']}' WHERE id = '{$row['id']}'",__LINE__,__FILE__);
				}
			}

			ok("admin.php?action=members&job=manage", "Number of posts for {$i} members successfully recounted.");
		}
		else {
			echo head();
			?>
			<table class="border">
			<tr><td class="obox">Recount post counts</td></tr>
			<tr><td class="mbox">
				<p align="center">Recounting the post count for each user can be a very time consuming task. Do you really want to proceed?</p>
				<p align="center">
					<a href="admin.php?action=members&amp;job=recount&amp;confirm=1"><img alt="Yes" border="0" src="admin/html/images/yes.gif" /> Yes</a>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="javascript: history.back(-1);"><img border="0" alt="No" src="admin/html/images/no.gif" /> No</a>
				</p>
			</td></tr>
			</table>
			<?php
			echo foot();
			?>


			<?php
		}
	}
}
elseif ($job == 'register') {
	include_once ("classes/function.profilefields.php");
	$customfields = addprofile_customfields();

	echo head();
	?>
	<form name="form2" method="post" action="admin.php?action=members&amp;job=register2">
	<table class="border">
	<tr><td class="obox" colspan="2">Add a new Member</td></tr>
	<tr>
		<td class="mbox">
			User Name:<br />
	    	<span class="stext">Min. <?php echo $config['minnamelength']; ?> chars, max. <?php echo $config['maxnamelength']; ?> chars</span>
	    </td>
		<td class="mbox">
	    	<input type="text" name="name" size="40" />
		</td>
	</tr><tr>
		<td class="mbox">
			Password:<br />
	    	<span class="stext">Min. <?php echo $config['minpwlength']; ?> chars, max. <?php echo $config['maxpwlength']; ?> chars</span>
	    </td>
	    <td class="mbox">
			<input type="password" name="pw" size="40" />
		</td>
	</tr><tr>
		<td class="mbox">
			Confirm Password:
		</td>
		<td class="mbox">
			<input type="password" name="pwx" size="40" />
		</td>
	</tr><tr>
		<td class="mbox">
	    	E-mail address:
		    <?php if ($config['sessionmails'] == 1) { ?>
			  <br /><span class="stext">Disposable e-mail accounts are not allowed!</span>
		    <?php } ?>
		</td>
		<td class="mbox">
			<input type="text" name="email" size="40" />
		</td>
	</tr>
	<?php foreach ($customfields as $row) { ?>
	<tr>
	  <td class="mbox">
	  	<?php echo $row['name']; ?>
	  	<?php if(!empty($row['description'])) { ?>
	  	  <br /><span class="stext"><?php echo $row['description']; ?></span>
	  	<?php } ?>
	  </td>
	  <td class="mbox">
	  	<?php echo $row['input']; ?>
	  </td>
	</tr>
	<?php } ?>
	<tr>
		<td class="ubox" align="center" colspan="2">
			<input accesskey="s" type="submit" name="Submit1" value="Submit" />
		</td>
	</tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'register2') {
	include_once ("classes/function.profilefields.php");

	$error = array();
	if (double_udata('name',$_POST['name']) == false) {
		$error[] = 'User Name is already in use';
	}
	if (double_udata('mail',$_POST['email']) == false) {
		$error[] = 'E-mail address is already in use';
	}
	if (strxlen($_POST['name']) > $config['maxnamelength']) {
		$error[] = 'Name is too long';
	}
	if (strxlen($_POST['name']) < $config['minnamelength']) {
		$error[] = 'Name is too short';
	}
	if (strxlen($_POST['pw']) > $config['maxpwlength']) {
		$error[] = 'Password is too long';
	}
	if (strxlen($_POST['pw']) < $config['minpwlength']) {
		$error[] = 'Password is too short';
	}
	if (strxlen($_POST['email']) > 200) {
		$error[] = 'E-mail address is too long';
	}
	if (check_mail($_POST['email']) == false) {
		$error[] = 'E-mail adress is not valid';
	}
	if ($_POST['pw'] != $_POST['pwx']) {
		$error[] = 'The two passwords are different';
	}

	// Custom profile fields
	$upquery = array();
	$query = $db->query("SELECT * FROM {$db->pre}profilefields WHERE editable != '0' AND required = '1' ORDER BY disporder");
	while($profilefield = $db->fetch_assoc($query)) {
		$profilefield['type'] = $gpc->prepare($profilefield['type']);
		$thing = explode("\n", $profilefield['type'], 2);
		$type = $thing[0];
		$field = "fid{$profilefield['fid']}";

		$value = $gpc->get($field, none);

		if((is_string($value) && strlen($value) == 0) || (is_array($value) && count($value) == 0)) {
			$error[] = 'You did not insert a value for a required additional field';
		}
		if($profilefield['maxlength'] > 0 && ((is_string($value) && strlen($value) > $profilefield['maxlength']) || (is_array($value) && count($value) > $profilefield['maxlength']))) {
			$error[] = 'You entered too many chars for one of the required additional fields';
		}

		if($type == "multiselect" || $type == "checkbox") {
			if (is_array($value)) {
				$options = implode("\n", $value);
			}
			else {
				$options = '';
			}
		}
		else {
			$options = $value;
		}
		$options = $gpc->save_str($options);
		$upquery[] = "`{$field}` = '{$options}'";
	}

	if (count($error) > 0) {
		echo head();
		error("admin.php?action=members&job=register", $error);
	}
	else {
	    $reg = time();
	    $pw_md5 = md5($_POST['pwx']);

		$db->query("INSERT INTO {$db->pre}user (name, pw, mail, regdate, confirm) VALUES ('{$_POST['name']}', '{$pw_md5}', '{$_POST['email']}', '{$reg}', '11')",__LINE__,__FILE__);
        $redirect = $db->insert_id();

		if (count($upquery) > 0) {
			$upquery[] = "`ufid` = '{$redirect}'";
			$db->query("INSERT INTO {$db->pre}userfields SET ".implode(', ', $upquery));
		}

		$com = $scache->load('memberdata');
		$cache = $com->delete();

		echo head();
        ok("admin.php?action=members&job=edit&id=".$redirect, "Member successfully added. You will be redirected to the User Editor.");
	}
}
elseif ($job == 'edit') {
	include_once ("classes/function.profilefields.php");

	echo head();

	$id = $gpc->get('id', int);

	$result = $db->query("SELECT * FROM {$db->pre}user WHERE id = '{$id}'", __LINE__, __FILE__);
	if ($db->num_rows($result) != 1) {
		error('admin.php?action=members&job=manage', 'No valid ID given.');
	}
	$user = $gpc->prepare($db->fetch_assoc($result));

	$chars = $config['maxaboutlength'];

	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();
	if (!isset($language[$user['language']]['language'])) {
		$user['language'] = $config['langdir'];
	}
	$mylanguage = $language[$user['language']]['language'];

	$loaddesign_obj = $scache->load('loaddesign');
	$design = $loaddesign_obj->get();
	if (!isset($design[$user['template']]['name'])) {
		$user['template'] = $config['templatedir'];
	}
	$mydesign = $design[$user['template']]['name'];

	// Profile
	$bday = explode('-',$user['birthday']);
	$year = gmdate('Y');
	$maxy = $year-6;
	$miny = $year-100;
	$result = $db->query("SELECT id, title, name, core FROM {$db->pre}groups WHERE guest = '0' ORDER BY admin DESC , guest ASC , core ASC");
	$random = md5(microtime());

	$customfields = admin_customfields($user['id']);
?>
<form name="form_<?php echo $random; ?>" method="post" action="admin.php?action=members&job=edit2&amp;id=<?php echo $id; ?>&amp;random=<?php echo $random; ?>">
<table class="border">
<tr><td class="obox" colspan="2">Edit member</td></tr>
<tr><td class="mbox">Nickname:</td><td class="mbox">
<input type="text" name="name_<?php echo $random; ?>" size="40" value="<?php echo $user['name']; ?>" />
</td></tr>
<tr><td class="mbox">New password:</td><td class="mbox">
<input type="password" name="pw_<?php echo $random; ?>" size="40" value="" />
</td></tr>
<tr><td class="mbox" valign="top">Group(s):<br />
<span class="stext">Multiple groups possible. Separate multiple ids with commas!</span>
</td><td class="mbox">
<input type="text" name="groups" id="groups" size="40" value="<?php echo $user['groups']; ?>" />
<br />
<table class="inlinetable">
<tr>
<th>ID</th>
<th>Internal group name</th>
<th>Public group title</th>
</tr>
<?php while ($row = $gpc->prepare($db->fetch_assoc($result))) { ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['title']; ?></td>
</tr>
<?php } ?>
</table>
</td></tr>
<tr><td class="mbox">Real name:</td><td class="mbox">
<input type="text" name="fullname" id="fullname" size="40" value="<?php echo $user['fullname']; ?>" />
</td></tr>
<tr><td class="mbox">Email address:</td><td class="mbox">
<input type="text" name="email" id="email" size="40" value="<?php echo $user['mail']; ?>" />
</td></tr>
<tr><td class="mbox">Location:</td><td class="mbox">
<input type="text" name="location" id="location" size="40" value="<?php echo $user['location']; ?>" />
</td></tr>
<tr><td class="mbox">Gender:</td><td class="mbox">
<select size="1" name="gender">
	<option value="">Not specified</option>
	<option<?php echo iif($user['gender'] == 'm',' selected="selected"'); ?> value="m">Male</option>
	<option<?php echo iif($user['gender'] == 'w',' selected="selected"'); ?> value="w">Female</option>
</select>
</td></tr>
<tr><td class="mbox">Birthday:</td><td class="mbox">
  <select size="1" name="birthday">
  <option value="00">--</option>
	<?php
	for ($i=1;$i<=31;$i++) {
		echo "<option value='".leading_zero($i)."'".iif($bday[2] == $i, ' selected="selected"').">".$i."</option>\n";
	}
	?>
  </select>.
  <select size="1" name="birthmonth">
  <option value="00">--</option>
	<?php
	for ($i=1;$i<=12;$i++) {
		echo "<option value='".leading_zero($i)."'".iif($bday[1] == $i, ' selected="selected"').">".$i."</option>\n";
	}
	?>
  </select>
  <select size="1" name="birthyear">
  <option value="0000">----</option>
	<?php
	for ($i=$maxy;$i>=$miny;$i--) {
		echo "<option value='".$i."'".iif($bday[0] == $i, ' selected="selected"').">".$i."</option>\n";
	}
	?>
  </select>
</td></tr>
<tr><td class="mbox">Homepage:</td><td class="mbox">
<input type="text" name="hp" id="hp" size="40" value="<?php echo $user['hp']; ?>" />
</td></tr>
<tr><td class="mbox">ICQ:</td><td class="mbox">
<input type="text" name="icq" id="icq" size="40" value="<?php echo iif(!empty($user['icq']), $user['icq']); ?>" />
</td></tr>
<tr><td class="mbox">AOL- &amp; Netscape-Messenger:</td><td class="mbox">
<input type="text" name="aol" id="aol" size="40" value="<?php echo $user['aol']; ?>" />
</td></tr>
<tr><td class="mbox">Yahoo-Messenger:</td><td class="mbox">
<input type="text" name="yahoo" id="yahoo" size="40" value="<?php echo $user['yahoo']; ?>" />
</td></tr>
<tr><td class="mbox">MSN- &amp; Windows-Messenger</td><td class="mbox">
<input type="text" name="msn" id="msn" size="40" value="<?php echo $user['msn']; ?>" />
</td></tr>
<tr><td class="mbox">Jabber:</td><td class="mbox">
<input type="text" name="jabber" id="jabber" size="40" value="<?php echo $user['jabber']; ?>" />
</td></tr>
<tr><td class="mbox">Skype</td><td class="mbox">
<input type="text" name="skype" id="skype" size="40" value="<?php echo $user['skype']; ?>" />
</td></tr>
<?php foreach ($customfields['1'] as $row1) { ?>
<tr><td class="mbox"><?php echo $row1['name'] . iif(!empty($row1['description']), '<br /><span class="stext">'.$row1['description'].'</span>'); ?></td>
<td class="mbox"> <?php echo $row1['input']; ?></td></tr>
<?php } ?>
<tr><td class="ubox" align="center" colspan="2"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>

<br class="minibr" />
<table class="border">
<tr><td class="obox">Signature</td></tr>
<tr><td class="mbox" align="center"><textarea name="signature" rows="4" cols="110"><?php echo $user['signature']; ?></textarea></td></tr>
<tr><td class="ubox" align="center"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
<br class="minibr" />

<table class="border">
<tr><td class="obox" colspan="2">Change avatar</td></tr>
<tr>
<td class="mbox">Add new avatar with URL:</td>
<td class="mbox"><input type="text" name="pic" id="pic" size="70" value="<?php echo $user['pic']; ?>" /></td>
</tr>
<tr><td class="ubox" colspan="2" align="center"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
<br class="minibr" />

<table class="border">
<tr><td class="obox" colspan="2">Edit options</td></tr>
<tr><td class="mbox">Time zone:</td><td class="mbox">
<select id="temp" name="temp">
	<option selected="selected" value="<?php echo $user['timezone']; ?>">keep time zone (GMT <?php echo $user['timezone']; ?>)</option>
	<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>
	<option value="-11">(GMT -11:00) Midway-Ilands, Samoa</option>
	<option value="-10">(GMT -10:00) Hawaii</option>
	<option value="-9">(GMT -09:00) Alaska</option>
	<option value="-8">(GMT -08:00) Tijuana, Los Angeles, Seattle, Vancouver</option>
	<option value="-7">(GMT -07:00) Arizona, Denver, Salt Lake City, Calgary</option>
	<option value="-6">(GMT -06:00) Mexico-City, Saskatchewan, Central-amerika</option>
	<option value="-5">(GMT -05:00)  Bogot&aacute;, Lima, Quito, Indiana (East), New York, Toronto</option>
	<option value="-4">(GMT -04:00) Caracas, La Paz, Montreal, Quebec, Santiago</option>
	<option value="-3.5">(GMT -03:30) Newfoundland</option>
	<option value="-3">(GMT -03:00) Brasilia, Buenos Aires, Georgetown, Greenland</option>
	<option value="-2">(GMT -02:00) Middle atlantic</option>
	<option value="-1">(GMT -01:00) Azores, Cape verde islands</option>
	<option value="0">(GMT) Casablance, Monrovia, Dublin, Edinburgh, Lissabon, London</option>
	<option value="+1">(GMT +01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna, Paris</option>
	<option value="+2">(GMT +02:00) Athens, Istanbul, Minsk, Cairo, Jerusalem</option>
	<option value="+3">(GMT +03:00) Bagdad, Moscow, Nairobi</option>
	<option value="+3.5">(GMT +03:30) Teheran</option>
	<option value="+4">(GMT +04:00) Muskat, Tiflis</option>
	<option value="+4.5">(GMT +04:30) Kabul</option>
	<option value="+5">(GMT +05:00) Islamabad</option>
	<option value="+5.5">(GMT +05:30) Kalkutta, New-Delhi</option>
	<option value="+5.75">(GMT +05:45) Katmandu</option>
	<option value="+6">(GMT +06:00) Almaty, Novosibirsk, Dhaka</option>
	<option value="+6.5">(GMT +06:30) Rangun</option>
	<option value="+7">(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
	<option value="+8">(GMT +08:00) Ulan Bator, Singapur, Peking, Hongkong</option>
	<option value="+9">(GMT +09:00) Irkutsk, Osaka, Sapporo, Tokyo, Seoul</option>
	<option value="+9.5">(GMT +09:30) Adelaide, Darwin</option>
	<option value="+10">(GMT +10:00) Brisbane, Canberra, Melbourne, Sydney, Vladivostok</option>
	<option value="+11">(GMT +11:00) Solomon, New Caledonia</option>
	<option value="+12">(GMT +12:00) Auckland, Wellington, Fiji Islands, Kamchatka</option>
</select>
</td></tr>
<tr><td class="mbox">Contribution editor:</td><td class="mbox">
<select id="opt_0" name="opt_0">
	<option<?php echo iif($user['opt_textarea'] == 0,' selected="selected"'); ?> value="0">Simple editor</option>
	<option<?php echo iif($user['opt_textarea'] == 1,' selected="selected"'); ?> value="1">Advanced editor</option>
</select>
</td></tr>
<tr><td class="mbox">Sending an email by receiving a PN?</td><td class="mbox">
<input id="opt_1" type="checkbox" name="opt_1" <?php echo iif($user['opt_pmnotify'] == 1,' checked="checked"'); ?> value="1" />
</td></tr>
<tr><td class="mbox">Hide topics with bad ratings?</td><td class="mbox">
<input id="opt_2" type="checkbox" name="opt_2" <?php echo iif($user['opt_hidebad'] == 1,' checked="checked"'); ?> value="1" />
</td></tr>
<tr><td class="mbox">How should your email shown to the members?</td><td class="mbox">
<select id="opt_3" name="opt_3">
	<option<?php echo iif($user['opt_hidemail'] == 0,' selected="selected"'); ?> value="0">show the e-mail encrypted + provide form</option>
	<option<?php echo iif($user['opt_hidemail'] == 1,' selected="selected"'); ?> value="1">do not show the e-mail + provide no form</option>
	<option<?php echo iif($user['opt_hidemail'] == 2,' selected="selected"'); ?> value="2">do not show the e-mail + provide form</option>
</select>
</td></tr>
<tr><td class="mbox">Which design would you like to use?</td><td class="mbox">
<select id="opt_4" name="opt_4">
	<option selected="selected" value="<?php echo $user['template']; ?>">keep design</option>
	<?php foreach ($design as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	<?php } ?>
</select>
</td></tr>
<tr><td class="mbox">Which language would you like to use?</td><td class="mbox">
<select id="opt_5" name="opt_5">
	<option selected="selected" value="<?php echo $user['language']; ?>">keep language</option>
	<?php foreach ($language as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['language']; ?></option>
	<?php } ?>
</select>
</td></tr>
<?php foreach ($customfields['2'] as $row1) { ?>
<tr><td class="mbox"><?php echo $row1['name'] . iif(!empty($row1['description']), '<br /><span class="stext">'.$row1['description'].'</span>'); ?></td>
<td class="mbox"> <?php echo $row1['input']; ?></td></tr>
<?php } ?>
<?php foreach ($customfields['0'] as $row1) { ?>
<tr><td class="mbox"><?php echo $row1['name'] . iif(!empty($row1['description']), '<br /><span class="stext">'.$row1['description'].'</span>'); ?></td>
<td class="mbox"> <?php echo $row1['input']; ?></td></tr>
<?php } ?>
<tr><td class="ubox" colspan="2" align="center"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
<br class="minibr" />

<table class="border">
<tr><td class="obox">Change personal site</td></tr>
<tr><td class="mbox" align="center"><textarea name="comment" id="comment" rows="15" cols="110"><?php echo $user['about']; ?></textarea></td></tr>
<tr><td class="ubox" align="center"><input accesskey="s" type="submit" name="Submit1" value="Submit" /></td></tr>
</table>
</form>
<?php
	echo foot();
}
elseif ($job == 'edit2') {
	include_once ("classes/function.profilefields.php");

	echo head();
	$loaddesign_obj = $scache->load('loaddesign');
	$cache = $loaddesign_obj->get();

	$loadlanguage_obj = $scache->load('loadlanguage');
	$cache2 = $loadlanguage_obj->get();

	$keys_int = array('id', 'birthday', 'birthmonth', 'birthyear', 'opt_0', 'opt_1', 'opt_2', 'opt_3', 'opt_4', 'opt_5');
	$keys_str = array('groups', 'fullname', 'email', 'location', 'icq', 'gender', 'hp', 'aol', 'yahoo', 'msn', 'jabber', 'signature', 'pic', 'temp', 'comment', 'skype');
	foreach ($keys_int as $val) {
		$query[$val] = $gpc->get($val, int);
	}
	foreach ($keys_str as $val) {
		$query[$val] = $gpc->get($val, str);
	}

	$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE id = '.$query['id']);
	if ($db->num_rows($result) != 1) {
		error('admin.php?action=members&job=manage', 'No valid ID given.');
	}
	$user = $gpc->prepare($db->fetch_assoc($result));

	$random = $gpc->get('random', none);
	$name = $gpc->get('name_'.$random, str);
	if (empty($name)) {
		$query['name'] = $user['name'];
	}
	else {
		$query['name'] = $name;
	}
	$query['pw'] = $gpc->get('pw_'.$random, str);

	$query['hp'] = trim($query['hp']);
	if (strtolower(substr($query['hp'], 0, 4)) == 'www.') {
		$query['hp'] = "http://{$query['hp']}";
	}

	$error = array();
	if (strxlen($query['comment']) > $config['maxaboutlength']) {
		$error[] = 'Personal site has too many characters';
	}
	if (check_mail($query['email']) == false) {
		 $error[] = 'No valid e-mail address given';
	}
	if (strxlen($query['name']) > $config['maxnamelength']) {
		$error[] = 'Name has too many characters';
	}
	if (strxlen($query['name']) < $config['minnamelength']) {
		$error[] = 'Name has too less characters';
	}
	if (strxlen($query['email']) > 200) {
		$error[] = 'E-mail address has too many characters (max. 200 characters)';
	}
	if ($user['mail'] != $_POST['email'] && double_udata('mail', $_POST['email']) == false) {
		 $error[] = $lang->phrase('email_already_used');
	}
	if (strxlen($query['signature']) > $config['maxsiglength']) {
		$error[] = 'Signature has too many characters';
	}
	if (strxlen($query['hp']) > 254) {
		$error[] = 'Homepage has too many characters';
	}
	if (!check_hp($query['hp'])) {
		$query['hp'] = '';
	}
	if (strxlen($query['location']) > 50) {
		$error[] = 'Residence has too many characters (max. 50 characters)';
	}
	if ($query['gender'] != 'm' && $query['gender'] != 'w' && $query['gender'] != '') {
		$error[] = "Gender is not valid";
	}
	if ($query['birthday'] > 31) {
		$error[] = "Day of birth is not valid";
	}
	if ($query['birthmonth'] > 12) {
		$error[] = "Month of birth is not valid";
	}
	if (($query['birthyear'] < gmdate('Y')-120 || $query['birthyear'] > gmdate('Y')) && $query['birthyear'] != 0 ) {
		$error[] = "Year of birth is not valid";
	}
	if (strxlen($query['fullname']) > 128) {
		$error[] = "Civil name has too many characters";
	}
	if (intval($query['temp']) < -12 && intval($query['temp']) > 12) {
		$error[] = 'You have not select a valid time zone';
	}
	if (!isset($cache[$query['opt_4']])) {
		$error[] = 'Invalid design selected';
	}
	if (!isset($cache2[$query['opt_5']])) {
		$error[] = 'Invalid language selected';
	}
	if (!empty($query['pic']) && preg_match('/^(http:\/\/|www.)([\wäöüÄÖÜ@\-_\.]+)\:?([0-9]*)\/(.*)$/', $query['pic'])) {
		$query['pic'] = checkRemotePic($query['pic'], $query['id']);
		switch ($query['pic']) {
			case REMOTE_INVALID_URL:
				$error[] = 'Avatar: Invalid URL given';
				$query['pic'] = '';
			break;
			case REMOTE_CLIENT_ERROR:
				$error[] = 'Avatar: Could not retrieve avatar from server';
				$query['pic'] = '';
			break;
			case REMOTE_FILESIZE_ERROR:
				$error[] = 'Avatar: Filesize exceeeded';
				$query['pic'] = '';
			break;
			case REMOTE_IMAGE_HEIGHT_ERROR:
				$error[] = 'Avatar: Height is too high';
				$query['pic'] = '';
			break;
			case REMOTE_IMAGE_WIDTH_ERROR:
				$error[] = 'Avatar: Width is too high';
				$query['pic'] = '';
			break;
			case REMOTE_EXTENSION_ERROR:
				$error[] = 'Avatar: Invalid extension/file type';
				$query['pic'] = '';
			break;
			case REMOTE_IMAGE_ERROR:
				$error[] = 'Avatar: Could not parse image';
				$query['pic'] = '';
			break;
		}
	}
	elseif (empty($query['pic']) || !file_exists($query['pic'])) {
		$query['pic'] = '';
	}

	if (count($error) > 0) {
		error('admin.php?action=members&job=edit&id='.$query['id'], $error);
	}
	else {
		// Now we create the birthday...
		if (empty($query['birthmonth']) || empty($query['birthday'])) {
			$query['birthmonth'] = 0;
			$query['birthday'] = 0;
			$query['birthyear'] = 0;
		}
		if (empty($_POST['birthyear'])) {
			$query['birthyear'] = 1000;
		}
		$query['birthmonth'] = leading_zero($query['birthmonth']);
		$query['birthday'] = leading_zero($query['birthday']);
		$query['birthyear'] = leading_zero($query['birthyear'], 4);
		$bday = $query['birthyear'].'-'.$query['birthmonth'].'-'.$query['birthday'];

		$query['icq'] = str_replace('-', '', $query['icq']);
		if (!is_id($query['icq'])) {
			$query['icq'] = 0;
		}

		if (!empty($query['pw']) && strlen($query['pw']) >= $config['minpwlength']) {
			$md5 = md5($query['pw']);
			$update_sql = ", pw = '{$md5}' ";
		}
		else {
			$update_sql = ' ';
		}

		admin_customsave($query['id']);

		$db->query("UPDATE {$db->pre}user SET groups = '".saveCommaSeparated($query['groups'])."', timezone = '".$query['temp']."', opt_textarea = '".$query['opt_0']."', opt_pmnotify = '".$query['opt_1']."', opt_hidebad = '".$query['opt_2']."', opt_hidemail = '".$query['opt_3']."', template = '".$query['opt_4']."', language = '".$query['opt_5']."', pic = '".$query['pic']."', about = '".$query['comment']."', icq = '".$query['icq']."', yahoo = '".$query['yahoo']."', aol = '".$query['aol']."', msn = '".$query['msn']."', jabber = '".$query['jabber']."', birthday = '".$bday."', gender = '".$query['gender']."', hp = '".$query['hp']."', signature = '".$query['signature']."', location = '".$query['location']."', fullname = '".$query['fullname']."', skype = '".$query['skype']."', mail = '".$query['email']."', name = '".$query['name']."'".$update_sql." WHERE id = '".$user['id']."' LIMIT 1",__LINE__,__FILE__);
		ok("admin.php?action=members&job=manage", 'Datas saved successful!');
	}
}
elseif ($job == 'delete') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	if (in_array($my->id, $delete)) {
		$mykey = array_search($my->id, $delete);
		unset($delete[$mykey]);
	}
	if (count($delete) > 0) {
		$did = implode(',', $delete);
		$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE id IN ('.$did.')');
		$olduserdata = file_get_contents('data/deleteduser.php');
		while ($user = $gpc->prepare($db->fetch_assoc($result))) {
			// Step 1: Write Data to File with old Usernames
			$olduserdata .= "\n".$user['id']."\t".$user['name'];
			$olduserdata = trim($olduserdata);
			// Step 2: Delete all pms
			$db->query("DELETE FROM {$db->pre}pm WHERE pm_to IN ({$did})");
			// Step 3: Search all old posts by an user, and update to guests post
			$db->query("UPDATE {$db->pre}replies SET name = '".$user['name']."', email = '".$user['mail']."' WHERE name = '".$user['id']."' AND email = ''");
			// Step 4: Search all old topics by an user, and update to guests post
			$db->query("UPDATE {$db->pre}topics SET name = '".$user['name']."' WHERE name = '".$user['id']."'");
			$db->query("UPDATE {$db->pre}topics SET last_name = '".$user['name']."' WHERE last_name = '".$user['id']."'");
			// Step 5: Delete pic
			removeOldImages('uploads/pics/', $user['id']);
		}
		$filesystem->file_put_contents('data/deleteduser.php', $olduserdata);
		// Step 6: Delete all abos
		$db->query("DELETE FROM {$db->pre}abos WHERE mid IN ({$did})");
		// Step 8: Delete as mod
		$db->query("DELETE FROM {$db->pre}moderators WHERE mid IN ({$did})");
		$delete = $gpc->get('delete', arr_int);
		// Step 9: Set uploads from member to guests-group
		$db->query("UPDATE {$db->pre}uploads SET mid = '0' WHERE mid IN ({$did})");
		// Step 10: Set post ratings from member to guests-group I
		$db->query("UPDATE {$db->pre}postratings SET mid = '0' WHERE mid IN ({$did})");
		// Step 11: Set post ratings from member to guests-group II
		$db->query("UPDATE {$db->pre}postratings SET aid = '0' WHERE aid IN ({$did})");
		// Step 12: Delete user himself
		$db->query("DELETE FROM {$db->pre}user WHERE id IN ({$did})");
		$anz = $db->affected_rows();
		// Step 13: Delete user's custom profile fields
		$db->query("DELETE FROM {$db->pre}userfields WHERE ufid IN ({$did})");

		$cache = $scache->load('memberdata');
		$cache = $cache->delete();

		ok('javascript:history.back(-1);', $anz.' members deleted');
	}
	else {
		error('javascript:history.back(-1);', 'No valid specifiation given.');
	}

}
elseif ($job == 'banned') {
	echo head();
	$bannedip = file('data/bannedip.php');
	$memberdata_obj = $scache->load('memberdata');
	$memberdata = $memberdata_obj->get();
	?>
<form name="form" method="post" action="admin.php?action=members&amp;job=ban_delete">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="7">
    <span class="right"><a class="button" href="admin.php?action=members&amp;job=ban_add">Ban User/IP</a></span>
   	Banned Members and IP-Adresses
   </td>
  </tr>
  <tr>
   <td class="ubox" width="2%">DEL</td>
   <td class="ubox" width="17%">User/IP</td>
   <td class="ubox" width="17%">Banned by</td>
   <td class="ubox" width="12%">Banned on</td>
   <td class="ubox" width="12%">Ban will be lifted on</td>
   <td class="ubox" width="12%">Time remaining</td>
   <td class="ubox" width="28%">Reason</td>
  </tr>
  <?php
  foreach ($bannedip as $row) {
  	$row = explode("\t", rtrim($row, "\r\n"), 6);
  	if ($row[0] == 'ip') {
  		$data = '<span class="right stext">IP</span><a href="admin.php?action=members&amp;job=ips&amp;ipaddress='.$row[1].'">'.$row[1].'</a>';
  	}
  	elseif ($row[0] == 'user') {
  		if (isset($memberdata[$row[1]]) == true) {
  			$data = '<a href="admin.php?action=members&amp;job=edit&amp;id='.$row[1].'">'.$memberdata[$row[1]].'</a>';
  		}
  		else {
  			$data = '<em>N/A</em>';
  		}

  	}
  	else {
  		continue;
  	}

	if (isset($memberdata[$row[3]]) == true) {
		$row[3] = '<a href="admin.php?action=members&amp;job=edit&amp;id='.$row[3].'">'.$memberdata[$row[3]].'</a>';
	}
	else {
		$row[3] = '<em>N/A</em>';
	}

  	$sec = $row[2] - time();
  	if ($row[2] == 0) {
  		$diff = '-';
  	}
  	elseif ($sec >= 60) {
	  	$days = floor($sec/(60*60*24));
	  	$sec = $sec - $days*60*60*24;
	  	$hours = floor($sec/(60*60));
	  	$sec = $sec - $hours*60*60;
	  	$mins = floor($sec/60);
	  	$sec = $sec - $mins*60;
	  	$diff = "{$days}d {$hours}h {$sec}m";
  	}
  	else {
  		$diff = "<em>Expired</em>";
  	}

  	$row[2] = intval($row[2]);
  	if ($row[2] > 0) {
  		$row[2] = gmdate('d.m.Y H:i', times($row[2]));
  	}
  	else {
  		$row[2] = 'Never';
  	}

  	$row[4] = gmdate('d.m.Y H:i', times($row[4]));
  	?>
  <tr>
   <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row[0]; ?>#<?php echo $row[1]; ?>" /></td>
   <td class="mbox"><?php echo $data; ?></td>
   <td class="mbox"><?php echo $row[3]; ?></td>
   <td class="mbox"><?php echo $row[4]; ?></td>
   <td class="mbox"><?php echo $row[2]; ?></td>
   <td class="mbox"><?php echo $diff; ?></td>
   <td class="mbox"><?php echo htmlspecialchars($row[5]); ?></td>
  </tr>
  <?php } ?>
  <tr>
   <td class="ubox" colspan="7" align="center"><input type="submit" name="Submit" value="Lift bans"></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'ban_add') {
	echo head();
	$b = file_get_contents('data/bannedip.php');
	?>
<form name="form" method="post" action="admin.php?action=members&amp;job=ban_add2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="2">Ban an User or an IP-Address</td>
  </tr>
  <tr>
   <td class="mbox" width="40%">User Name or IP-Address:</td>
   <td class="mbox" width="60%">
   	<input type="text" name="data" size="60" /><br />
   	Data above is: <input type="radio" name="type" value="user" checked="checked" />User Name&nbsp;&nbsp;&nbsp;&nbsp;
   	<input type="radio" name="type" value="ip" />IP-Adress
   	</td>
  </tr>
  <tr>
   <td class="mbox" width="40%">
   	Lift Ban after:<br />
   	<span class="stext">Select the length of the ban here. The ban will be lifted at the time specified.</span>
   </td>
   <td class="mbox" width="60%">
   	<select name="until">
	<option value="0" selected="selected">Permanent - Never Lift Ban</option>
	<option value="D_1">1 Day</option>
	<option value="D_2">2 Days</option>
	<option value="D_3">3 Days</option>
	<option value="D_4">4 Days</option>
	<option value="D_5">5 Days</option>
	<option value="D_6">6 Days</option>
	<option value="D_7">1 Week</option>
	<option value="D_14">2 Weeks</option>
	<option value="D_21">3 Weeks</option>
	<option value="M_1">1 Month</option>
	<option value="M_2">2 Months</option>
	<option value="M_3">3 Months</option>
	<option value="M_4">4 Months</option>
	<option value="M_5">5 Months</option>
	<option value="M_6">6 Months</option>
	<option value="M_12">1 Year</option>
	<option value="M_24">2 Years</option>
   	</select>
   	</td>
  </tr>
  <tr>
   <td class="mbox" width="40%">Reason to show the user:</td>
   <td class="mbox" width="60%"><input type="text" name="reason" size="60" /></td>
  </tr>
  <tr>
   <td class="ubox" colspan="2" align="center"><input type="submit" name="Submit" value="Add"></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'ban_add2') {
	echo head();

	$data = $gpc->get('data', none);
	$type = strtolower($gpc->get('type', none));
	$until = $gpc->get('until', none);
	$reason = $gpc->get('reason', none);

	$error = array();
	if ($type == 'ip') {
		if (!preg_match("/[0-9]{1,3}\.[0-9]{1,3}(\.[0-9]{0,3})?(\.[0-9]{0,3})?/", $data)) {
			$error[] = 'The specified IP-Address is not correct';
		}
	}
	elseif ($type == 'user') {
		$result = $db->query("SELECT id FROM {$db->pre}user WHERE name = '{$data}' LIMIT 1", __LINE__, __FILE__);
		if ($db->num_rows($result) == 0) {
			$error[] = 'No user with the name "'.$data.'" found.';
		}
		else {
			$user = $db->fetch_assoc($result);
			if ($user['id'] == $my->id) {
			//	$error[] = 'You can not ban yourself.';
			}
			else {
				$data = $user['id'];
			}$data = $user['id'];
		}
	}
	else {
		$error[] = 'The specified data and/or type is nor correct.';
	}
	if (!(is_numeric($until) && intval($until) === 0)) { // WTF? $until != 0 won't work?!
		$until = explode('_', $until);
		$until[0] = strtoupper($until[0]);
		if (($until[0] != 'D' && $until[0] != 'M') || !isset($until[1])) {
			$error[] = 'The time is not valid.';
		}
	}

	$banned = file('data/bannedip.php');
	$file = array();
	foreach ($banned as $line) {
		$row = rtrim($line, "\r\n");
		$file[] = $row;
		$row = explode("\t", $row, 6);
		if ($row[0] == $type && strcasecmp($row[1], $data) == 0) {
			$error[] = 'The User Name or IP-Adress is already banned.';
		}
	}

	if (count($error) > 0) {
		error('admin.php?action=members&job=ban_add', $error);
	}

	if ($until[0] == 'D') {
		$until = strtotime('+'.$until[1].' day'.iif($until[1] > 0, 's'));
	}
	elseif ($until[0] == 'M') {
		$until = strtotime('+'.$until[1].' month'.iif($until[1] > 0, 's'));
	}

	$new = array(
		$type,
		$data,
		$until,
		$my->id,
		time(),
		str_replace(array("\r", "\n", "\t"), ' ', $reason)
	);
	$file[] = implode("\t", $new);

	$filesystem->file_put_contents('data/bannedip.php', implode("\n", $file) );

	ok('admin.php?action=members&job=banned', iif($type == 'ip', 'IP-Address', 'User').' has been banned successfully.');
}
elseif ($job == 'ban_delete') {
	echo head();
	$delete = $gpc->get('delete', arr_none);
	if (array_empty($delete) == true) {
		error('admin.php?action=members&job=banned', 'You have not selected anything to delete.');
	}
	$banned = file('data/bannedip.php');
	$file = array();
	foreach ($banned as $line) {
		$add = true;
		$row = explode("\t", rtrim($line, "\r\n"), 6);
		foreach ($delete as $del) {
			$del = explode("#", $del, 2);
			if ($del[0] == $row[0] && $del[1] == $row[1]) {
				$add = false;
			}
		}
		if ($add == true && empty($line) == false) {
			$file[] = $line;
		}
	}
	$filesystem->file_put_contents('data/bannedip.php', implode("\n", $file) );
	ok('admin.php?action=members&job=banned', 'IP-addresses have been saved successful.');
}
elseif ($job == 'inactive') {
	echo head();
	$year =  time()-60*60*24*365;
	$two_month =  time()-60*60*24*30*2;
	?>
<form name="form" method="post" action="admin.php?action=members&amp;job=inactive2">
 <table class="border">
  <tr>
   <td class="obox" colspan="3">
	<span class="right">
	  <a class="button" href="admin.php?action=members&amp;job=search">Search Members</a>
	</span>
   Inactive Members</td>
  </tr>
  <tr>
   <td class="mbox">Posts:</td>
   <td class="mbox" align="center">&lt;</td>
   <td class="mbox"><input type="text" name="posts" size="3" value="10" />. </td>
  </tr>
  <tr>
   <td class="mbox">Date of registry:</td>
   <td class="mbox" align="center">&lt;</td>
   <td class="mbox">
   <input type="text" name="regdate[1]" size="3" value="" />.
   <input type="text" name="regdate[2]" size="3" value="" />.
   <input type="text" name="regdate[3]" size="5" value="" /> (DD. MM. YYYY)
   </td>
  </tr>
  <tr>
   <td class="mbox">Last visit:</td>
   <td class="mbox" align="center">&lt;</td>
   <td class="mbox">
   <input type="text" name="lastvisit[1]" size="3" value="<?php echo gmdate('d', times($two_month)); ?>" />.
   <input type="text" name="lastvisit[2]" size="3" value="<?php echo gmdate('m', times($two_month)); ?>" />.
   <input type="text" name="lastvisit[3]" size="5" value="<?php echo gmdate('Y', times($two_month)); ?>" /> (DD. MM. YYYY)
   </td>
  </tr>
  <tr>
   <td class="mbox">Status:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><select size="1" name="confirm">
	  <option selected="selected" value="">Whatever</option>
	  <option value="11">Activated</option>
	  <option value="10">User has to activate the account per e-mail</option>
	  <option value="01">User account has to be activated by the admin</option>
	  <option value="00">User has neither from the admin nor per e-mail been activated</option>
	</select></td>
  </tr>
  <tr>
   <td class="ubox" align="center" colspan="4"><input type="submit" value="Submit"></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'inactive2') {
	echo head();

	define('DONT_CARE', md5(microtime()));

	$fields = 	array(
		'name' => array('User Name', str, null),
		'mail' => array('E-mail', str, null),
		'posts' => array('Posts', int, '<'),
		'regdate' => array('Registration', arr_int, '<'),
		'lastvisit' => array('Last Visit', arr_int, '<'),
		'confirm' => array('Status', none, '=')
	);
	$keys = array_keys($fields);


	$sqlwhere = array();
	$input = array();
	foreach ($fields as $key => $data) {
		$value = $gpc->get($key, $data[1], DONT_CARE);
		if ($key == 'regdate' || $key == 'lastvisit') {
			if (is_array($value) && array_sum($value) != 0) { // for php version >= 5.1.0
				$input[$key] =  @mktime(0, 0, 0, intval($value[2]), intval($value[1]), intval($value[3]));
				if ($input[$key] == -1 || $input[$key] == false) { // -1 for php version < 5.1.0, false for php version >= 5.1.0
					$input[$key] = DONT_CARE;
				}
			}
			else {
				$input[$key] = DONT_CARE;
			}
		}
		else {
			if (!isset($_REQUEST[$key])) {
				$_REQUEST[$key] = null;
			}
			if (empty($_REQUEST[$key]) && $_REQUEST[$key] != '0') {
				$input[$key] = DONT_CARE;
			}
			else {
				$input[$key] = $value;
			}
		}

		if ($input[$key] != DONT_CARE) {
			$sqlwhere[] = " `{$key}` {$fields[$key][2]} '{$input[$key]}' ";
		}
	}

	if (count($sqlwhere) > 0) {
		$query = 'SELECT id, '.implode(',', $keys).' FROM '.$db->pre.'user WHERE '.implode(' AND ', $sqlwhere).' ORDER BY name';
		$result = $db->query($query, __LINE__, __FILE__);
		$count = $db->num_rows($result);
	}
	else {
		$count = 0;
	}
	?>
	<form name="form" action="admin.php?action=members" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr>
		  <td class="obox" colspan="9">
		<span style="float: right;">
		  <a class="button" href="admin.php?action=members&amp;job=search">Search Members</a>
		</span>
		  Search inactive members
		  </td>
		</tr>
		<?php if ($count == 0) { ?>
		<tr>
		  <td class="mbox" colspan="9">No inactive members found.</td>
		</tr>
		<?php } else { ?>
			<tr>
			  <td class="ubox" colspan="9"><?php echo $count; ?> inactive members found.</td>
			</tr>
			<tr>
			  <td class="obox center">Select<br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> All</span></td>
			  <td class="obox center">Edit</td>
			  <?php foreach ($keys as $key) { ?>
			  <td class="obox"><?php echo $fields[$key][0]; ?></td>
			  <?php } ?>
			</tr>
			<?php
			while ($row = $gpc->prepare($db->fetch_assoc($result))) {
				if (empty($row['lastvisit'])) {
					$row['lastvisit'] = 'Never';
				}
				else {
					$row['lastvisit'] = gmdate('d.m.Y H:i', times($row['lastvisit']));
				}
				if (isset($row['regdate'])) {
					$row['regdate'] = gmdate('d.m.Y', times($row['regdate']));
				}
				if (isset($row['confirm'])) {
				  	if ($row['confirm'] == "11") { $row['confirm'] = 'Activated'; }
				  	elseif ($row['confirm'] == "10") { $row['confirm'] = 'User has to activate the account per e-mail'; }
				  	elseif ($row['confirm'] == "01") { $row['confirm'] = 'User account has to be activated by the admin'; }
				  	elseif ($row['confirm'] == "00") { $row['confirm'] = 'User has neither from the admin nor per e-mail been activated'; }
				}
			?>
			<tr>
			  <td class="mbox center"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
			  <td class="mbox center"><a class="button" href="admin.php?action=members&amp;job=edit&amp;id=<?php echo $row['id']; ?>">Edit</a></td>
			  <?php foreach ($keys as $key) { ?>
			  <td class="mbox"><?php echo $row[$key]; ?></td>
			  <?php } ?>
			</tr>
			<?php } ?>
			<tr>
			  <td class="ubox" colspan="9">
			  	<select name="job">
			  		<option value="delete">Delete</option>
			  		<option value="emaillist">Export E-mail Addresses</option>
			  		<option value="newsletter">Send Newsletter</option>
			  	</select> <input type="submit" name="submit" value="Go">
			  </td>
			</tr>
		<?php } ?>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'search') {
	echo head();

	$loaddesign_obj = $scache->load('loaddesign');
	$design = $loaddesign_obj->get();

	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();

	$result = $db->query("SELECT id, title, name FROM {$db->pre}groups WHERE guest = '0' ORDER BY admin DESC , guest ASC , core ASC");
	?>
<form name="form" method="post" action="admin.php?action=members&job=search2">
 <table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
  <tr>
   <td class="obox" colspan="4">
	<span style="float: right;">
	  <a class="button" href="admin.php?action=members&amp;job=inactive">Inactive Members</a>
	</span>
   Search for members</td>
  </tr>
  <tr>
	<td class="mbox" width="50%" colspan="4">
	<b>Help:</b>
	<ul>
	<li>You can type "%" and "_" as wildcards into the keyword. An "_" replaces one single character, a "%" replaces any characters. The wildcards can only be used with the relational operators <b>!=</b> and <b>=</b>.</li>
	<li>
	<b>=</b> means <i>equal</i>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>&lt;</b> means <i>less than</i>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>&gt;</b> means <i>greater than</i>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<b>!=</b> means <i>not equal</i>.</li>
	</ul>
	</td>
  </tr>
  <tr>
   <td class="mbox" colspan="2">Exactness:</td>
   <td class="mbox" colspan="2">
   <input type="radio" name="type" value="0"> <b>or</b> (at least one of the input have to lead to a match)<br>
   <input type="radio" name="type" value="1" checked="checked">  <b>and</b> (the whole input have to lead to a match)
   </td>
  </tr>
  <tr>
   <td class="ubox" width="40%">&nbsp;</td>
   <td class="ubox" width="5%">Relational operator</td>
   <td class="ubox" width="50%">&nbsp;</td>
   <td class="ubox" width="5%">Show</td>
  </tr>
  <tr>
   <td class="mbox">ID:</td>
   <td class="mbox" align="center"><select size="1" name="compare[id]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="id" size="12"></td>
   <td class="mbox"><input type="checkbox" name="show[id]" value="1" checked>Yes</td>
  </tr>
  <tr>
   <td class="mbox">Nickname:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="name" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[name]" value="1" checked>Yes</td>
  </tr>
  <tr>
   <td class="mbox">E-mail address:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="mail" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[mail]" value="1" checked></td>
  </tr>
  <tr>
   <td class="mbox">Date of registry:</td>
   <td class="mbox" align="center"><select size="1" name="compare[regdate]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="regdate[1]" size="3">. <input type="text" name="regdate[2]" size="3">. <input type="text" name="regdate[3]" size="5"> (DD. MM. YYYY)</td>
   <td class="mbox"><input type="checkbox" name="show[regdate]" value="1" checked></td>
  </tr>
  <tr>
   <td class="mbox">Posts:</td>
   <td class="mbox" align="center"><select size="1" name="compare[posts]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="posts" size="10"></td>
   <td class="mbox"><input type="checkbox" name="show[posts]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Civil name:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="fullname" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[fullname]" value="1" checked></td>
  </tr>
  <tr>
   <td class="mbox">Homepage:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="hp" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[hp]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Residence:</td>
   <td class="mbox" align="center"><select size="1" name="compare[location]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><input type="text" name="location" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[location]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Gender:</td>
   <td class="mbox" align="center"><select size="1" name="compare[gender]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select name="gender" size="1">
   <option selected="selected" value="">Whatever</option>
   <option value="x">Not specified</option>
   <option value="m">Male</option>
   <option value="w">Female</option>
   </select></td>
   <td class="mbox"><input type="checkbox" name="show[gender]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Birthday:</td>
   <td class="mbox" align="center"><select size="1" name="compare[birthday]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="birthday[1]" size="3">. <input type="text" name="birthday[2]" size="3">. <input type="text" name="birthday[3]" size="5"> (DD. MM. YYYY)</td>
   <td class="mbox"><input type="checkbox" name="show[birthday]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Last visit:</td>
   <td class="mbox" align="center"><select size="1" name="compare[lastvisit]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="lastvisit[1]" size="3">. <input type="text" name="lastvisit[2]" size="3">. <input type="text" name="lastvisit[3]" size="5"> (DD. MM. YYYY)</td>
   <td class="mbox"><input type="checkbox" name="show[lastvisit]" value="1" checked></td>
  </tr>
  <tr>
   <td class="mbox">ICQ-number:</td>
   <td class="mbox" align="center"><select size="1" name="compare[icq]">
	  <option value="-1">&lt;</option>
	  <option value="0" selected="selected">=</option>
	  <option value="1">&gt;</option>
	</select></td>
   <td class="mbox"><input type="text" name="icq" size="12"></td>
   <td class="mbox"><input type="checkbox" name="show[icq]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Yahoo-ID:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="yahoo" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[yahoo]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">AOL-name:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="aol" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[aol]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">MSN-address:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="msn" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[msn]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Jabber-address:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="jabber" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[jabber]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Skype-name:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><input type="text" name="skype" size="50"></td>
   <td class="mbox"><input type="checkbox" name="show[skype]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Time zone:</td>
   <td class="mbox" align="center"><select size="1" name="compare[timezone]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select name="timezone">
	<option selected="selected" value="">whatever</option>
	<option value="-12">(GMT -12:00) Eniwetok, Kwajalein</option>
	<option value="-11">(GMT -11:00) Midway-Ilands, Samoa</option>
	<option value="-10">(GMT -10:00) Hawaii</option>
	<option value="-9">(GMT -09:00) Alaska</option>
	<option value="-8">(GMT -08:00) Tijuana, Los Angeles, Seattle, Vancouver</option>
	<option value="-7">(GMT -07:00) Arizona, Denver, Salt Lake City, Calgary</option>
	<option value="-6">(GMT -06:00) Mexico-City, Saskatchewan, Central-amerika</option>
	<option value="-5">(GMT -05:00)  Bogot&aacute;, Lima, Quito, Indiana (East), New York, Toronto</option>
	<option value="-4">(GMT -04:00) Caracas, La Paz, Montreal, Quebec, Santiago</option>
	<option value="-3.5">(GMT -03:30) Newfoundland</option>
	<option value="-3">(GMT -03:00) Brasilia, Buenos Aires, Georgetown, Greenland</option>
	<option value="-2">(GMT -02:00) Middle atlantic</option>
	<option value="-1">(GMT -01:00) Azores, Cape verde islands</option>
	<option value="0">(GMT) Casablance, Monrovia, Dublin, Edinburgh, Lissabon, London</option>
	<option value="+1">(GMT +01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna, Paris</option>
	<option value="+2">(GMT +02:00) Athens, Istanbul, Minsk, Cairo, Jerusalem</option>
	<option value="+3">(GMT +03:00) Bagdad, Moscow, Nairobi</option>
	<option value="+3.5">(GMT +03:30) Teheran</option>
	<option value="+4">(GMT +04:00) Muskat, Tiflis</option>
	<option value="+4.5">(GMT +04:30) Kabul</option>
	<option value="+5">(GMT +05:00) Islamabad</option>
	<option value="+5.5">(GMT +05:30) Kalkutta, New-Delhi</option>
	<option value="+5.75">(GMT +05:45) Katmandu</option>
	<option value="+6">(GMT +06:00) Almaty, Novosibirsk, Dhaka</option>
	<option value="+6.5">(GMT +06:30) Rangun</option>
	<option value="+7">(GMT +07:00) Bangkok, Hanoi, Jakarta</option>
	<option value="+8">(GMT +08:00) Ulan Bator, Singapur, Peking, Hongkong</option>
	<option value="+9">(GMT +09:00) Irkutsk, Osaka, Sapporo, Tokyo, Seoul</option>
	<option value="+9.5">(GMT +09:30) Adelaide, Darwin</option>
	<option value="+10">(GMT +10:00) Brisbane, Canberra, Melbourne, Sydney, Vladivostok</option>
	<option value="+11">(GMT +11:00) Solomon, New Caledonia</option>
	<option value="+12">(GMT +12:00) Auckland, Wellington, Fiji Islands, Kamchatka</option>
</select>	</td>
   <td class="mbox"><input type="checkbox" name="show[timezone]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Groups:</td>
   <td class="mbox" align="center">=</td>
   <td class="mbox"><select size="3" name="groups" multiple="multiple">
	  <option selected="selected" value="">whatever</option>
	  <?php while ($row = $gpc->prepare($db->fetch_assoc($result))) { ?>
		<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	  <?php } ?>
	</select></td>
   <td class="mbox"><input type="checkbox" name="show[groups]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Design:</td>
   <td class="mbox" align="center"><select size="1" name="compare[template]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select name="template">
	<option selected="selected" value="">whatever</option>
	<?php foreach ($design as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
	<?php } ?>
</select></td>
   <td class="mbox"><input type="checkbox" name="show[template]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Language:</td>
   <td class="mbox" align="center"><select size="1" name="compare[language]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select name="language">
	<option selected="selected" value="">whatever</option>
	<?php foreach ($language as $row) { ?>
	<option value="<?php echo $row['id']; ?>"><?php echo $row['language']; ?></option>
	<?php } ?>
</select></td>
   <td class="mbox"><input type="checkbox" name="show[language]" value="1"></td>
  </tr>
  <tr>
   <td class="mbox">Status:</td>
   <td class="mbox" align="center"><select size="1" name="compare[confirm]">
	  <option value="0" selected="selected">=</option>
	  <option value="2">!=</option>
	</select></td>
   <td class="mbox"><select size="1" name="confirm">
	  <option selected="selected" value="">whatever</option>
	  <option value="11">Activated</option>
	  <option value="10">User has to activate the account per e-mail</option>
	  <option value="01">User account has to be activated by the admin</option>
	  <option value="00">User has neither from the admin nor per e-mail been activated</option>
	</select></td>
   <td class="mbox"><input type="checkbox" name="show[confirm]" value="1"></td>
  </tr>
  <tr>
   <td class="ubox" align="center" colspan="4"><input type="submit" value="Submit"></td>
  </tr>
 </table>
</form>
	<?php
	echo foot();
}
elseif ($job == 'search2') {
	echo head();

	define('DONT_CARE', md5(microtime()));
	$fields = 	array(
		'id' => array('ID', int),
		'name' => array('User Name', str),
		'mail' => array('E-mail', str),
		'regdate' => array('Registration', arr_int),
		'posts' => array('Posts', int),
		'fullname' => array('Civil Name', str),
		'hp' => array('Homepage', str),
		'location' => array('Residence', str),
		'gender' => array('Gender', str),
		'birthday' => array('Birthday', arr_none),
		'lastvisit' => array('Last Visit', arr_int),
		'icq' => array('ICQ', int),
		'yahoo' => array('Yahoo', str),
		'aol' => array('AOL', str),
		'msn' => array('MSN', str),
		'skype' => array('Skype', str),
		'jabber' => array('Jabber', str),
		'timezone' => array('Timezone', int),
		'groups' => array('Groups', arr_int),
		'template' => array('Design', int),
		'language' => array('Language', int),
		'confirm' => array('Status', none)
	);
	$change = array('m' => 'male', 'w' => 'female', '' => '-');

	$loaddesign_obj = $scache->load('loaddesign');
	$design = $loaddesign_obj->get();

	$loadlanguage_obj = $scache->load('loadlanguage');
	$language = $loadlanguage_obj->get();

	$type = $gpc->get('type', int);
	if ($type == 0) {
		$sep = ' OR ';
	}
	else {
		$sep = ' AND ';
	}

	$compare = $gpc->get('compare', arr_int);
	foreach ($compare as $key => $cmp) {
		if ($cmp == -1) {
			$compare[$key] = '<';
		}
		elseif ($cmp == 1) {
			$compare[$key] = '>';
		}
		elseif ($cmp == 2) {
			$compare[$key] = '!=';
		}
		else {
			$compare[$key] = '=';
		}
	}
	$show = $gpc->get('show', arr_none);
	$show = array_keys($show);
	$show = array_intersect($show, array_keys($fields));
	$sqlkeys = array_unique(array_intersect(array_merge($show, array('id', 'name')), array_keys($fields)));
	$sqlwhere = array();
	$input = array();
	foreach ($fields as $key => $data) {
		$value = $gpc->get($key, none);
		if (is_array($value)) {
			$value = implode('', $value);
		}
		if (strpos($value, '%') !== false || strpos($value, '_') !== false) {
			$value = $gpc->get($key, none, DONT_CARE);
			$value = $gpc->save_str($value);
		}
		else {
			$value = $gpc->get($key, $data[1], DONT_CARE);
		}
		if ($key == 'regdate' || $key == 'lastvisit') {
			if (is_array($value) && array_sum($value) != 0) { // for php version >= 5.1.0
				$input[$key] =  @mktime(0, 0, 0, intval($value[2]), intval($value[1]), intval($value[3]));
				if ($input[$key] == -1 || $input[$key] == false) { // -1 for php version < 5.1.0, false for php version >= 5.1.0
					$input[$key] = DONT_CARE;
				}
			}
			else {
				$input[$key] = DONT_CARE;
			}
		}
		elseif ($key == 'birthday') {
			$value[1] = intval(trim($value[1]));
			if ($value[1] < 1 || $value[1] > 31) {
				$value[1] = '%';
			}
			$value[2] = intval(trim($value[2]));
			if ($value[2] < 1 || $value[2] > 12) {
				$value[2] = '%';
			}
			if (strlen($value[3]) == 2) {
				if ($value[3] > 40) {
					$value[3] += 1900;
				}
				else {
					$value[3] += 2000;
				}
			}
			else {
				$value[3] = intval(trim($value[3]));
			}
			if ($value[3] < 1900 || $value[3] > 2100) {
				$value[3] = '%';
			}
			if ($value[1] == '%' && $value[2] == '%' && $value[3] == '%') {
				$input[$key] = DONT_CARE;
			}
			else {
				$input[$key] = $value[3].'-'.$value[2].'-'.$value[1];
			}
		}
		elseif ($key == 'gender') {
			if (empty($value)) {
				$input[$key] = DONT_CARE;
			}
			elseif ($value == 'x') {
				$input[$key] = '';
			}
			else {
				$input[$key] = $value;
			}
		}
		else {
			if (!isset($_REQUEST[$key])) {
				$_REQUEST[$key] = null;
			}
			if (empty($_REQUEST[$key]) && $_REQUEST[$key] != '0') {
				$input[$key] = DONT_CARE;
			}
			else {
				$input[$key] = $value;
			}
		}

		if (!isset($compare[$key])) {
			$compare[$key] = '=';
		}

		if ($input[$key] != DONT_CARE) {
			if (strpos($input[$key], '%') !== false || strpos($input[$key], '_') !== false) {
				if ($compare[$key] == '=') {
					$compare[$key] = 'LIKE';
				}
				elseif ($compare[$key] == '!=') {
					$compare[$key] = 'NOT LIKE';
				}
			}
			$sqlwhere[] = " `{$key}` {$compare[$key]} '{$input[$key]}' ";
		}
	}
	$colspan = count($show) + 1;

	if (count($sqlwhere) > 0) {
		$query = 'SELECT '.implode(',',$sqlkeys).' FROM '.$db->pre.'user WHERE '.implode($sep, $sqlwhere).' ORDER BY name';
		$result = $db->query($query, __LINE__, __FILE__);
		$count = $db->num_rows($result);
	}
	else {
		$count = 0;
	}
	?>
	<form name="form" action="admin.php?action=members" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr>
		  <td class="obox" colspan="<?php echo $colspan; ?>"><b>Search for members</b></td>
		</tr>
		<?php if ($count == 0) { ?>
		<tr>
		  <td class="mbox" colspan="<?php echo $colspan; ?>">No member found.</td>
		</tr>
		<?php } else { ?>
			<tr>
			  <td class="ubox" colspan="<?php echo $colspan; ?>"><?php echo $count; ?> members found.</td>
			</tr>
			<tr>
			  <td class="obox">Select<br /><span class="stext"><input type="checkbox" onclick="check_all('delete[]');" name="all" value="1" /> All</span></td>
			  <?php foreach ($show as $key) { ?>
			  <td class="obox"><?php echo $fields[$key][0]; ?></td>
			  <?php } ?>
			</tr>
			<?php
			while ($row = $gpc->prepare($db->fetch_assoc($result))) {
				if (isset($row['lastvisit'])) {
					$row['lastvisit'] = gmdate('d.m.Y H:i', times($row['lastvisit']));
				}
				if (isset($row['regdate'])) {
					$row['regdate'] = gmdate('d.m.Y', times($row['regdate']));
				}
				if (empty($row['icq'])) {
					$row['icq'] = '-';
				}
				if (empty($row['timezone'])) {
					$row['timezone'] = $config['timezone'];
				}
				if (isset($row['gender'])) {
					$row['gender'] = $change[$row['gender']];
				}
				if (!isset($row['birthday']) || intval($row['birthday']) == 0) {
					$row['birthday'] = '-';
				}
				else {
					$bd = explode('-', $row['birthday']);
					$bd = array_reverse($bd);
					if ($bd[2] <= 1000) {
						$bd[2] = 0;
						$row['birthday'] = "{$bd[0]}.{$bd[1]}.";
					}
					else {
						$row['birthday'] = implode('.', $bd);
					}
				}
				if (isset($row['template']) && isset($design[$row['template']])) {
					$row['template'] = $design[$row['template']]['name'];
				}
				if (isset($row['language']) && isset($language[$row['language']])) {
					$row['language'] = $language[$row['language']]['language'];
				}
				if (isset($row['confirm'])) {
				  	if ($row['confirm'] == "11") { $row['confirm'] = 'Activated'; }
				  	elseif ($row['confirm'] == "10") { $row['confirm'] = 'User has to activate the account per e-mail'; }
				  	elseif ($row['confirm'] == "01") { $row['confirm'] = 'User account has to be activated by the admin'; }
				  	elseif ($row['confirm'] == "00") { $row['confirm'] = 'User has neither from the admin nor per e-mail been activated'; }
				}
			?>
			<tr>
			  <td class="mbox"><input type="checkbox" name="delete[]" value="<?php echo $row['id']; ?>"></td>
			  <?php foreach ($show as $key) { ?>
			  <td class="mbox"><a href="admin.php?action=members&job=edit&id=<?php echo $row['id']; ?>"><?php echo $row[$key]; ?></a></td>
			  <?php } ?>
			</tr>
			<?php } ?>
			<tr>
			  <td class="ubox" colspan="<?php echo $colspan; ?>">
			  	<select name="job">
			  		<option value="delete">Delete</option>
			  		<option value="emaillist">Export E-mail Addresses</option>
			  		<option value="newsletter">Send Newsletter</option>
			  	</select> <input type="submit" name="submit" value="Go">
			  </td>
			</tr>
		<?php } ?>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'disallow') {
	echo head();
	$delete = $gpc->get('delete', arr_int);
	if (count($delete) > 0) {
		$did = implode(',', $delete);
		$db->query("DELETE FROM {$db->pre}user WHERE id IN ({$did}) AND confirm != '11'");
		$anz = $db->affected_rows();
		$db->query("DELETE FROM {$db->pre}userfields WHERE ufid IN ({$did})");

		$cache = $scache->load('memberdata');
		$cache = $cache->delete();

		ok('admin.php?action=members&job=activate', $anz.' members deleted');
	}
	else {
		error('admin.php?action=members&job=activate', 'Keine gültige Angabe gemacht.');
	}
}
elseif ($job == 'activate') {
	echo head();

	$result = $db->query('SELECT * FROM '.$db->pre.'user WHERE confirm != "11" ORDER BY regdate DESC');
	?>
	<form name="form" action="admin.php?action=members&job=disallow" method="post">
	<table class="border" border="0" cellspacing="0" cellpadding="4" align="center">
		<tr>
		  <td class="obox" colspan="4">Moderate &amp; Unlock Members</td>
		</tr>
		<tr>
		  <td class="ubox" width="30%">Name</td>
		  <td class="ubox" width="10%">Email</td>
		  <td class="ubox" width="15%">Registered</td>
		  <td class="ubox" width="45%">Status (<input type="checkbox" onchange="check_all('delete[]')" /> All)</td>
		</tr>
	<?php
	while ($row = $gpc->prepare($db->fetch_object($result))) {
		$row->regdate = gmdate('d.m.Y', times($row->regdate));
		if ($row->lastvisit == 0) {
			$row->lastvisit = 'Never';
		}
		else {
			$row->lastvisit = gmdate('d.m.Y', times($row->lastvisit));
		}
		?>
		<tr>
		  <td class="mbox"><a title="Edit" href="admin.php?action=members&job=edit&id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a></td>
		  <td class="mbox" align="center"><a href="mailto:<?php echo $row->mail; ?>">Email</a></td>
		  <td class="mbox"><?php echo $row->regdate; ?></td>
		  <td class="mbox"><ul>
		  <?php if ($row->confirm == '00' || $row->confirm == '01') { ?>
		  <li><strong><a href="admin.php?action=members&job=confirm&id=<?php echo $row->id; ?>">Confirm User</a></strong></li>
		  <?php } if ($row->confirm == '00' || $row->confirm == '10') { ?>
		  <li>User has to activate the account per e-mail [<a href="admin.php?action=members&job=confirm2&id=<?php echo $row->id; ?>">Activate User completely</a>]</li>
		  <?php } ?>
		  <li>Delete user: <input type="checkbox" name="delete[]" value="<?php echo $row->id; ?>"></li>
		  </ul></td>
		</tr>
		<?php
	}
	?>
		<tr>
		  <td class="ubox" colspan="4" align="center"><input type="submit" name="submit" value="Delete selected User"></td>
		</tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'confirm') {
	echo head();

	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, name, confirm, mail FROM '.$db->pre.'user WHERE id = "'.$id.'" LIMIT 1');
	$row = $db->fetch_assoc($result);

	if ($row['confirm'] == '00') {
		$confirm = '10';
	}
	else {
		$confirm = '11';
	}

	$db->query('UPDATE '.$db->pre.'user SET confirm = "'.$confirm.'" WHERE id = "'.$row['id'].'" LIMIT 1', __LINE__, __FILE__);

	// Send Mail
	$row = $gpc->plain_str($row);
	$content = $lang->get_mail('admin_confirmed');
	xmail(array('0' => array('mail' => $row['mail'])), array(), $content['title'], $content['comment']);

	ok('admin.php?action=members&job=activate', 'Member has been confirmed!');
}
elseif ($job == 'confirm2') {
	echo head();

	$id = $gpc->get('id', int);
	$result = $db->query('SELECT id, name, mail FROM '.$db->pre.'user WHERE id = "'.$id.'" LIMIT 1');
	$row = $db->fetch_assoc($result);

	$db->query("UPDATE {$db->pre}user SET confirm = '11' WHERE id = '{$id}' LIMIT 1", __LINE__, __FILE__);

	$row = $gpc->plain_str($row);
	$content = $lang->get_mail('admin_confirmed');
	xmail(array('0' => array('mail' => $row['mail'])), array(), $content['title'], $content['comment']);

	ok('admin.php?action=members&job=activate', 'Member has been activated completely!');
}
elseif ($job == 'ips') {
	$username = $gpc->get('username', str);
	$ipaddress = $gpc->get('ipaddress', str);
	$userid = $gpc->get('id', int);

	echo head();
	if (!empty($username)) {
		$result = $db->query("SELECT id, name FROM {$db->pre}user WHERE name = '{$username}' LIMIT 1", __LINE__, __FILE__);
		$userinfo = $db->fetch_assoc($result);
		$userid = $userinfo['id'];
		if (!is_id($userid)) {
			error('admin.php?action=members&job=ip', 'Invalid user specified!');
		}
	}

	if (!empty($ipaddress) || $userid > 0) {
		if (!empty($ipaddress)) {
			if (check_ip($ipaddress)) {
				$hostname = @gethostbyaddr($ipaddress);
			}
			if (empty($hostname) || $hostname == $ipaddress) {
				$hostname = 'Could not resolve Hostname';
			}
			$users = $db->query("SELECT DISTINCT u.id, u.name, r.ip FROM {$db->pre}replies AS r, {$db->pre}user AS u  WHERE u.id = r.name AND r.ip LIKE '{$ipaddress}%' AND r.ip != '' ORDER BY u.name", __LINE__, __FILE__);
			?>
			<table align="center" class="border">
			<tr>
				<td class="obox">IP Address search for IP Address &quot;<?php echo $ipaddress; ?>&quot;</td>
			</tr>
			<tr>
				<td class="ubox">
				<a href="http://ripe.net/fcgi-bin/whois?searchtext=<?php echo $ipaddress; ?>" target="_blank" title="Visit ripe.net for more information"><?php echo $ipaddress; ?></a>: <b><?php echo htmlspecialchars($hostname); ?></b>
				</td>
			</tr>
			<tr>
				<td class="mbox">
				<ul>
				<?php while ($user = $db->fetch_assoc($users)) { ?>
					<li style="padding: 3px;">
					<a href="admin.php?action=members&amp;job=edit&amp;id=<?php echo $user['id']; ?>"><b><?php echo $user['name']; ?></b></a> &nbsp;&nbsp;&nbsp;
					<a href="admin.php?action=members&amp;job=iphost&amp;ip=<?php echo $user['ip']; ?>" title="Resolve Address"><?php echo $user['ip']; ?></a> &nbsp;&nbsp;&nbsp;
					<a class="button" href="admin.php?action=members&amp;job=ips&amp;id=<?php echo $user['id']; ?>&amp;username=<?php echo urlencode($user['name']); ?>">View other IP Addresses for this User</a>
					</li>
					<?php
				}
				if ($db->num_rows($users) == 0) {
					?>
					<li>No matches found!</li>
					<?php
				}
				?>
				</ul>
				</td>
			</tr>
			</table>
			<br />
		<?php } if ($userid > 0) { ?>
			<table align="center" class="border">
			<tr>
				<td class="obox">Search IP Addresses for Username &quot;<?php echo $userinfo['name']; ?>&quot;</td>
			</tr>
			<tr>
				<td class="mbox">
				<ul>
				<?php
				$ips = $db->query("SELECT DISTINCT ip FROM {$db->pre}replies WHERE name = '{$userid}' AND ip != '{$ipaddress}' AND ip != '' ORDER BY ip", __LINE__, __FILE__);
				while ($ip = $db->fetch_assoc($ips)) {
					?>
					<li style="padding: 3px;">
					<a href="admin.php?action=members&job=iphost&amp;ip=<?php echo $ip['ip']; ?>" title="Resolve Address"><?php echo $ip['ip']; ?></a> &nbsp;&nbsp;&nbsp;
					<a class="button" href="admin.php?action=members&amp;job=ips&amp;ipaddress=<?php echo $ip['ip']; ?>">Find more Users with this IP Address</a>
					</li>
					<?php
				}
				if ($db->num_rows($ips) == 0) {
					?>
					<li>No matches found!</li>
					<?php
				}
				?>
				</ul>
				</td>
			</tr>
			</table>
			<br />
			<?php
		}
	}
	?>
	<form action="admin.php?action=members&amp;job=ips" method="post">
	<table align="center" class="border">
	<tr>
		<td class="obox" colspan="2">Search IP Addresses</td>
	</tr>
	<tr>
		<td class="mbox">Find Users by IP Address<br /><span class="stext">You may enter a partial IP Address</span></td>
		<td class="mbox"><input type="text" name="ipaddress" value="<?php echo $ipaddress; ?>" size="35" /></td>
	</tr>
	<tr>
		<td class="mbox">Find IP Addresses matching a Username</td>
		<td class="mbox"><input type="text" name="username" value="<?php echo $username; ?>" size="35" /></td>
	</tr>
	<tr>
		<td class="ubox" colspan="2" align="center"><input type="submit" value="Find" /></td>
	</tr>
	</table>
	</form>
	<?php
	echo foot();
}
elseif ($job == 'iphost') {
	$ip = $gpc->get('ip', str);
	if (check_ip($ip)) {
		$resolvedip = @gethostbyaddr($ip);
	}
	if (empty($resolvedip) || $resolvedip == $ip) {
		$host = '<i>Not Available</i>';
	}
	else {
		$host = htmlspecialchars($resolvedip);
	}
	echo head();
	?>
	<table align="center" class="border">
	<tr>
		<td class="obox" colspan="2">Resolve IP Address</td>
	</tr>
	<tr>
		<td class="mbox">IP Address</td>
		<td class="mbox"><a href="http://ripe.net/fcgi-bin/whois?searchtext=<?php echo $ip; ?>" target="_blank"><?php echo $ip; ?></a></td>
	</tr>
	<tr>
		<td class="mbox">Host Name</td>
		<td class="mbox"><?php echo $host; ?></td>
	</tr>
	</table>
	<?php
	echo foot();
}
?>
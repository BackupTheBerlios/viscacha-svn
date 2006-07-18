<mail>
	<title>{@config->fname}: Password-Request</title>
	<comment>Dear {%user->name},
	
you have requested to reset your password because you have forgotten your password. 
If you did not request this, please ignore it. It will expire and become useless in 24 hours time.

To reset your password, please visit the following page: 
$vboptions[bburl]/login.php?a=pwd&u={%user->id}&fid={$fid}

When you visit that page, your password will be reset, and the new password will be emailed to you. 

Best regards,
your {@config->fname} team
{@config->furl}</comment>
</mail>
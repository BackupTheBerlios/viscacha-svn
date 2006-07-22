<mail>
	<title>{@config->fname}: Password request</title>
	<comment>Dear {@user->name},

You are receiving this email because you have requested a new password be sent for your account on {@config->fname}. 

Your new password is: {$pw}

Best regards,
your {@config->fname} team
{@config->furl}</comment>
</mail>
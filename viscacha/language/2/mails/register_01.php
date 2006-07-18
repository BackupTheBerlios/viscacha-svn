<mail>
	<title>{@config->fname}: Confirmation email</title>
	<comment>Dear {@_POST->name},

You just registered at the board "{@config->fname}".

The board administrator has to confirm your registration before you can login. We will send you an activation email if your account gets activated!

Best regards,
your {@config->fname} team
{@config->furl}
</comment>
</mail>
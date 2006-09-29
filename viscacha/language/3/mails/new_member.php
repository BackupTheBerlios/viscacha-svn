<mail>
	<title>{@config->fname}: Neues Mitglied im Forum "{@config->fname}"</title>
	<comment>Hallo,

soeben hat sich ein neues Mitglied registriert. Das Mitglied heißt "{@_POST->name}".
Du findest das Profil des Mitglieds hier:
{@config->furl}/profile.php?id={$redirect}

Mit freundlichen Grüßen,
Dein {@config->fname} Team
{@config->furl}</comment>
</mail>
<mail>
	<title>{@config->fname}: Best�tigung deiner Passwortanfrage</title>
	<comment>Hallo {@user->name},

Du erh�lst diese E-Mail, weil Du (oder jemand der sich als Du ausgibt) ein neues Passwort f�r deinen Account auf {@config->fname} angefordert hat. Wenn Du kein neues Passwort angefordert hast, ignoriere diese E-Mail bitte. Im Falle, dass Du weitere unerw�nschte E-Mails dieser Sorte bekommen solltest, wende dich bitte an den Administrator.

Um ein neues Passwort zu erhalten, musst Du diese E-Mail best�tigen. Um dies zu tun, klicke bitte den Link unterhalb. Wenn Du diese Seite besuchst, wird dein Passwort ge�ndert und diese neue Passwort wird Dir dann per E-Mail zugeschickt.

{@config->furl}/log.php?action=pwremind3&id={@user->id}&fid={$confirmcode}

Mit freundlichen Gr��en,
dein {@config->fname} Team
{@config->furl}</comment>
</mail>
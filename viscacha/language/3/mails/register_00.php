<mail>
	<title>{@config->fname}: Aktivierungsemail</title>
	<comment>Hallo {@_POST->name},

Du hast dich soeben erfolgreich im Forum "{@config->fname}" registriert.

Um die Registrierung zu best�tigen, besuche bitte den folgenden Link:
{@config->furl}/register.php?action=confirm&id={$redirect}&fid={$confirmcode}

Der Forenverwalter (Administrator) muss Deine Registrierung auch erst noch best�tigen, bevor Du dich einloggen kannst. Du erh�lst dann eine weitere Best�tigungsemail von uns!

Mit freundlichen Gr��en,
Dein {@config->fname} Team
{@config->furl}
</comment>
</mail>
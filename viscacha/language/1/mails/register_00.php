<mail>
	<title>{@config->fname}: Aktivierungsemail</title>
	<comment>Hallo {@_POST->name},

Sie haben sich soeben erfolgreich im Forum "{@config->fname}" registriert.

Um die Registrierung zu best�tigen, besuchen Sie bitte den folgenden Link:
{@config->furl}/register.php?action=confirm&id={$redirect}&fid={$confirmcode}

Der Forenverwalter (Administrator) muss Ihre Registrierung auch erst noch best�tigen, bevor Sie sich einloggen k�nnen. Sie erhalten dann eine weitere Best�tigungsemail von uns!

Mit freundlichen Gr��en,
Ihr {@config->fname} Team
{@config->furl}
</comment>
</mail>
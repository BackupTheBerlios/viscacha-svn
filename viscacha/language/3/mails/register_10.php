<mail>
	<title>{@config->fname}: Aktivierungsemail</title>
	<comment>Hallo {@_POST->name},

du hast dich soeben erfolgreich im Forum "{@config->fname}" registriert.

Um die Registrierung zu best�tigen, besuche bitte den folgenden Link:
{@config->furl}/register.php?action=confirm&id={$redirect}&fid={$confirmcode}
Danach kannst Du dich mit Deinen Nutzerdaten im Forum anmelden.

Mit freundlichen Gr��en,
Dein {@config->fname} Team
{@config->furl}
</comment>
</mail>
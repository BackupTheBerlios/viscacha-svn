<mail>
	<title>{@config->fname}: Neuer Beitrag im Thema "{@row->topic}"</title>
	<comment>Hallo {@row->name}

Sie haben das Thema "{@row->topic}" abonniert.
Zu diesem Thema gibt es neue Beitr�ge. Der letzte Beitrag stammt von {@row->last_name}.

Hier k�nnen Sie das Thema einsehen: 
{@config->furl}/showtopic.php?id={@row->id}

Mit freundlichen Gr��en
Ihr {@config->fname} Team
{@config->furl}
____________________________________________
Um keine E-Mailbenachrichtigungen mehr zu erhalten, besuchen Sie bitte Ihre Themen-Abonnement-Verwaltung:
{@config->furl}/editprofile.php?action=abos</comment>
</mail>
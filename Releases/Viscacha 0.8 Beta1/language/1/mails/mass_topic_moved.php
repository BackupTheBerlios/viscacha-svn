<mail>
	<title>{@$config->fname}: Thema "{@old->topic}" wurde verschoben</title>
	<comment>Hallo {@old->name}. 

Das von Ihnen gestartete Thema "{@old->topic}" wurde soeben verschoben. Sie finden es nun unter: 
{@config->furl}/showtopic.php?id={$id}

Mit freundlichen Gr��en
Ihr {@$config->fname} Team
{@config->furl}</comment>
</mail>
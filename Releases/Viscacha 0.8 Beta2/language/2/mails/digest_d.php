<mail>
	<title>{@config->fname}: New posting for topic "{@row->topic}"</title>
	<comment>Hello {@row->name}

You subscribed the topic "{@row->topic}".
New postings are available. The last entry is from {@row->last_name}.

Here you can view the topic:: 
{@config->furl}/showtopic.php?id={@row->id}

Best regards,
your {@config->fname} team
{@config->furl}
____________________________________________
To disable notifications, visit your topic subscription administration:
{@config->furl}/editprofile.php?action=abos</comment>
</mail>
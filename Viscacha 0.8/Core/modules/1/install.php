$db->query("ALTER TABLE `{$db->pre}topics` ADD FULLTEXT `topic` (`topic`)");
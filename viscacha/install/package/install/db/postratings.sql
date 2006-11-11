CREATE TABLE `{:=DBPREFIX=:}postratings` (
  `mid` mediumint(7) NOT NULL default '',
  `aid` mediumint(7) NOT NULL default '',
  `tid` int(10) NOT NULL default '',
  `pid` int(10) NOT NULL default '',
  `rating` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `mid` (`mid`,`pid`)
) TYPE=MyISAM;

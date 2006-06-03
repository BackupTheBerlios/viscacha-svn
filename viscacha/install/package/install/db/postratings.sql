CREATE TABLE `{:=DBPREFIX=:}postratings` (
  `mid` mediumint(7) NOT NULL,
  `aid` mediumint(7) NOT NULL,
  `tid` int(10) NOT NULL,
  `pid` int(10) NOT NULL,
  `rating` enum('-1','1') NOT NULL,
  UNIQUE KEY `mid` (`mid`,`pid`)
) TYPE=MyISAM;

#
# TABLE STRUCTURE FOR: idp_wflogins
#

DROP TABLE IF EXISTS `idp_wflogins`;

CREATE TABLE `idp_wflogins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hitID` int(11) DEFAULT NULL,
  `ctime` double(17,6) unsigned NOT NULL,
  `fail` tinyint(3) unsigned NOT NULL,
  `action` varchar(40) NOT NULL,
  `username` varchar(255) NOT NULL,
  `userID` int(10) unsigned NOT NULL,
  `IP` binary(16) DEFAULT NULL,
  `UA` text,
  PRIMARY KEY (`id`),
  KEY `k1` (`IP`,`fail`),
  KEY `hitID` (`hitID`)
) ENGINE=InnoDB AUTO_INCREMENT=13918 DEFAULT CHARSET=utf8;

INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11888, 19997, '1602758721.789706', 1, 'loginFailInvalidUsername', '[login]', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ[ÿøR', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11889, 19999, '1602759084.846018', 1, 'loginFailInvalidUsername', 'demo', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ\rLﬂ', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11890, 20000, '1602759135.227563', 1, 'loginFailInvalidUsername', '[login]', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ¿@W»', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11891, 20001, '1602759278.203387', 1, 'loginFailInvalidUsername', 'member', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇê[jl', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11892, 20002, '1602759284.378651', 1, 'loginFailInvalidUsername', 'david', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ-@c§', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11893, 20003, '1602759523.184846', 1, 'loginFailInvalidUsername', 'laura', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇã;V/', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11894, 20004, '1602759585.934048', 1, 'loginFailInvalidUsername', '[login]', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇÄ«ŒÈ', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11895, 20005, '1602759635.403268', 1, 'loginFailInvalidUsername', 'andrew', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ≠Ï®r', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11896, 20007, '1602759903.458551', 1, 'loginFailInvalidUsername', 'test111', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ>JÏ', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11897, 20008, '1602759926.022596', 1, 'loginFailInvalidUsername', 'hello', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇGÌ;', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11898, 20009, '1602760025.264966', 1, 'loginFailInvalidUsername', 'john', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇã	˜6', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11899, 20010, '1602760148.907224', 1, 'loginFailInvalidUsername', 'dev', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇπ7U', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11900, 20011, '1602760310.894613', 1, 'loginFailInvalidUsername', 'quantri', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ\"j/v', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11901, 20012, '1602760326.467698', 1, 'loginFailInvalidUsername', 'admin', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇgíÑ', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11902, 20014, '1602760653.405930', 1, 'loginFailInvalidUsername', 'preview', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ.eß^', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11903, 20015, '1602760684.250468', 1, 'loginFailInvalidUsername', 'cliente', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇÑîôú', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11904, 20016, '1602760791.511245', 1, 'loginFailInvalidUsername', 'admin', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇh∆ù∏', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11905, 20017, '1602760845.033318', 1, 'loginFailInvalidUsername', 'editeur', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ3Kã', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11906, 20018, '1602760967.661234', 1, 'loginFailInvalidUsername', 'xxx', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇgó…', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11907, 20020, '1602761134.367888', 1, 'loginFailInvalidUsername', 'root', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇïÅ45', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11908, 20021, '1602761282.473956', 1, 'loginFailInvalidUsername', 'dev', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇœ∂', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11909, 20022, '1602761346.201507', 1, 'loginFailInvalidUsername', 'test', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇùı≠A', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11910, 20023, '1602761482.555990', 1, 'loginFailInvalidUsername', 'user', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇß≥Gx', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11911, 20024, '1602761700.334449', 1, 'loginFailInvalidUsername', 'test1', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇùıc\Z', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11912, 20025, '1602761778.848144', 1, 'loginFailInvalidUsername', 'admin', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ1ÍG!', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11913, 20027, '1602761984.708154', 1, 'loginFailInvalidUsername', 'admindemo', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ≤O∏π', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11914, 20028, '1602762071.273989', 1, 'loginFailInvalidUsername', 'tester', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇé]—¿', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11915, 20029, '1602762151.063171', 1, 'loginFailInvalidUsername', 'user', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇgÿ∆', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11916, 20030, '1602762252.607252', 1, 'loginFailInvalidUsername', 'test', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇß¨„R', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11917, 20033, '1602762528.841948', 1, 'loginFailInvalidUsername', 'admin', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇé]8`', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11918, 20034, '1602762660.221596', 1, 'loginFailInvalidUsername', 'admin', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ3˛,∞', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11919, 20035, '1602762680.997128', 1, 'loginFailInvalidUsername', 'admin', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇQ◊', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11920, 20036, '1602762898.713417', 1, 'loginFailInvalidUsername', '[login]', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇ¨\\ó', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');
INSERT INTO `idp_wflogins` (`id`, `hitID`, `ctime`, `fail`, `action`, `username`, `userID`, `IP`, `UA`) VALUES (11921, 20037, '1602762951.064764', 1, 'loginFailInvalidUsername', '[login]', 0, '\0\0\0\0\0\0\0\0\0\0ˇˇÜ—’-', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:62.0) Gecko/20100101 Firefox/62.0');



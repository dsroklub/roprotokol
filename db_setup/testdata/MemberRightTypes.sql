INSERT INTO `MemberRightType` (`member_right`, `description`, `arg`, `showname`, `predicate`, `active`)
VALUES
('kajak_b','kajakret B',NULL,'kajakret B','have kajak-ret B',1),
('8','otter',NULL,'otterret','have otterret',1),
('8cox','otter styrmand',NULL,'otter-styrmandsret','være otter-styrmand',1),
('competition','kaproere',NULL,'kaproer','være kaproer',1),
('cox','styrmand',NULL,'styrmandsret','være styrmand',1),
('coxtheory','styrmandsteori',NULL,'styrmandsteori','have styrmandsteori',1),
('instructor','instruktør',NULL,'instruktørret','være instruktør',1),
('kajak','kajak',NULL,'kajakret A','have kajakret A',1),
('kanin','kanin',NULL,'kanin','være kanin',1),
('langturøresund','Langure på Øresund',NULL,'Øresund langtursret','have øresund langtursret',0),
('longdistance','langtursstyrmand',NULL,'langtursstyrmandsret','være langtursstyrmand',1),
('longdistancetheory','langtursstyrmandsteori',NULL,'langtursteori','have langtursteori',1),
('motorboat','motorbåd',NULL,'motorbådsret','have motorbådsret',1),
('rowright','roret',NULL,'roret','have roret',1),
('sculler','sculler',NULL,'scullerret','have scullerret',1),
('skærgård','skærgården',NULL,'skærgårdsret','have skærgårdsret',0),
('svava','svava',NULL,'svavaret','have svavaret',1),
('swim400','kan svømme 400m',NULL,'svømme 400m','kunne svømme 400m',1),
('instructor','instruktør inrigger','row','instruktørret','være instruktør',1),
('instructor','instruktør, sculler','outrigger','instruktørret','være instruktør',1),
('instructor','instruktør, kajak','kajak','instruktørret','være instruktør',1),
('surfski','Surfski-ret',NULL,'surfski-ret','have surfski-ret',1),
('developer','udvikler','admin','udvikler','udvikle',0),
('wrench','har ikke deltaget i vintervedligehold 2015/2016','2016','Mangler vintervedligehold','mangle vintervedligehold',1),
('wrench','har ikke deltaget i vintervedligehold 2016/2017','2017','Mangler vintervedligehold','mangle vintervedligehold',1);

INSERT INTO MemberRightType (member_right, description, arg, showname, predicate, active)
VALUES
('remote_access','fjernadgang','roprotokol','roprotokol fjernadgang','have tilladelse til at bruge roprotokollen udefra',1),
('event','forum-oprettet','fora','opette nye fora','kunne oprette fora',1);

update MemberRightType set arg='helårs' WHERE member_right='kajak_b';
update MemberRightType set arg='sommer' WHERE member_right='kajak';

UPDATE MemberRights SET argument="sommer" WHERE MemberRight="kajak";
UPDATE MemberRights SET argument="helårs" WHERE MemberRight="kajak_b";


-- to here
UPDATE MemberRightType SET ARG="" WHERE arg IS NULL;
ALTER TABLE MemberRightType MODIFY COLUMN arg varchar(200) NOT NULL DEFAULT "";
ALTER TABLE MemberRightType ADD PRIMARY KEY (member_right,arg);


ALTER TABLE MemberRights MODIFY COLUMN Acquired datetime NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE MemberRights ADD CONSTRAINT fk_MemberRights_MemberRight FOREIGN KEY (MemberRight) REFERENCES MemberRightType (member_right) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE MemberRights DROP PRIMARY KEY;

DELETE FROM MemberRights WHERE EXISTS (SELECT 'x' FROM (SELECT * FROM MemberRights) as m WHERE MemberRights.MemberRight=m.MemberRight AND MemberRights.member_id=m.member_id AND MemberRights.argument=m.argument AND MemberRights.Acquired<m.Acquired) ;


ALTER TABLE MemberRights ADD PRIMARY KEY (member_id,MemberRight,argument);


SET optimizer_switch = 'derived_merge=off';

DELETE FROM MemberRights WHERE MemberRight="kajak" AND member_id IN (SELECT member_id from (SELECT member_id FROM MemberRights WHERE MemberRight='kajak_b') as c );



UPDATE MemberRightType set member_right="kajak" WHERE member_right="kajak_b";
UPDATE MemberRightType SET predicate="have kajakret" WHERE member_right="kajak";
UPDATE MemberRightType SET showname="kajakret" WHERE member_right="kajak";

 DELETE FROM MemberRights WHERE Acquired="1997-07-14 00:00:00" AND MemberRight="rowright" AND member_id in (SELECT member_id FROM (SELECT member_id FROM MemberRights WHERE MemberRight="rowright" AND Acquired>"1997-07-15 00:00:00") as c);

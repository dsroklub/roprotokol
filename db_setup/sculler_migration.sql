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

SET optimizer_switch = 'derived_merge=off';

DELETE FROM MemberRights WHERE MemberRight="kajak" AND member_id IN (SELECT member_id from (SELECT member_id FROM MemberRights WHERE MemberRight='kajak_b') as c );

UPDATE MemberRightType set member_right="kajak" WHERE member_right="kajak_b";
UPDATE MemberRightType SET predicate="have kajakret" WHERE member_right="kajak";
UPDATE MemberRightType SET showname="kajakret" WHERE member_right="kajak";

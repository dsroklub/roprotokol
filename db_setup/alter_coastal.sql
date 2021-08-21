INSERT INTO MemberRights SELECT member_id,'coastal',NOW(),'',NULL,NOW() from MemberRights WHERE MemberRight='svava';
update MemberRightType SET active=0 WHERE member_right='svava';

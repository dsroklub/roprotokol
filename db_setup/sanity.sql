SELECT * FROM Member
WHERE id IN (SELECT  member_id FROM MemberRights WHERE MemberRight="cox")
  AND NOT id IN (SELECT  member_id FROM MemberRights WHERE MemberRight="rowright")


-- MemberID reuse
select * from Member WHERE EXISTS (SELECT 'x' FROM Member m where m.MemberID=Member.MemberID AND m.id<> Member.id) ORDER BY Member.MemberID;

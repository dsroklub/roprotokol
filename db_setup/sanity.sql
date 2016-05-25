SELECT * FROM Member
WHERE id IN (SELECT  member_id FROM MemberRights WHERE MemberRight="cox")
  AND NOT id IN (SELECT  member_id FROM MemberRights WHERE MemberRight="rowright")

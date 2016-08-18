BEGIN;

UPDATE Member
Set LastName=Replace(LastName,"  "," ");

UPDATE Member
Set FirstName=Replace(FirstName,"  "," ");


UPDATE Member kanin, Member m,TripMember
    SET TripMember.member_id=m.id
    WHERE TripMember.member_id=kanin.id AND 
    kanin.MemberID LIKE "k%" AND kanin.FirstName=m.FirstName AND kanin.LastName=m.LastName
          AND m.id!=kanin.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" 
   AND NOT EXISTS (SELECT 'x' FROM Member mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id); 

UPDATE Member kanin, Member m,Damage
    SET Damage.ResponsibleMember=m.id
    WHERE Damage.ResponsibleMember=kanin.id AND 
    kanin.MemberID LIKE "k%" AND kanin.FirstName=m.FirstName AND kanin.LastName=m.LastName
          AND m.id!=kanin.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" 
   AND NOT EXISTS (SELECT 'x' FROM Member mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id); 



UPDATE Member kanin, Member m,Damage
    SET Damage.RepairerMember=m.id
    WHERE Damage.RepairerMember=kanin.id AND 
    kanin.MemberID LIKE "k%" AND kanin.FirstName=m.FirstName AND kanin.LastName=m.LastName
          AND m.id!=kanin.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" 
   AND NOT EXISTS (SELECT 'x' FROM Member mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id); 


UPDATE IGNORE Member kanin, Member m, MemberRights
    SET MemberRights.member_id=m.id
    WHERE MemberRights.member_id=kanin.id AND 
        kanin.MemberID LIKE "k%" AND kanin.FirstName=m.FirstName AND kanin.LastName=m.LastName AND
        m.id!=kanin.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" AND
        NOT EXISTS (SELECT 'x' FROM (SELECT * FROM MemberRights) as mr WHERE mr.member_id=m.id AND mr.MemberRight=MemberRights.MemberRight AND mr.argument=MemberRights.argument AND mr.member_id!=MemberRights.member_id) AND
        NOT EXISTS (SELECT 'x' FROM Member mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id); 


UPDATE Member kanin, Member m, reservation
    SET reservation.Member=m.id
    WHERE reservation.Member=kanin.id AND 
    kanin.MemberID LIKE "k%" AND LOWER(kanin.FirstName)=LOWER(m.FirstName) AND LOWER(kanin.LastName)=LOWER(m.LastName) 
          AND m.id!=kanin.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" 
   AND NOT EXISTS (SELECT 'x' FROM Member mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id); 

UPDATE Member kanin, Member m, reservation
    SET reservation.CancelledBy=m.id
    WHERE reservation.CancelledBy=kanin.id AND 
    kanin.MemberID LIKE "k%" AND kanin.FirstName=m.FirstName AND kanin.LastName=m.LastName 
          AND m.id!=kanin.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" 
   AND NOT EXISTS (SELECT 'x' FROM Member mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id); 

DELETE FROM Member
WHERE
    Member.MemberID LIKE "k%" AND 
    EXISTS (SELECT 'x' FROM (SELECT * FROM Member) as  m
    WHERE  LOWER(Member.FirstName)=LOWER(m.FirstName) AND LOWER(Member.LastName)=LOWER(m.LastName) 
          AND m.id!=Member.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" 
          AND NOT EXISTS (SELECT 'x' FROM (SELECT * FROM Member) as  mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id)); 


COMMIT;

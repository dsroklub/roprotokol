SELECT m1.RemoveDate as udmeldt,m1.MemberId,m2.MemberID as gæsteID, CONCAT(m1.FirstName," ",m1.LastName) as navn1, CONCAT(m2.FirstName," ",m2.LastName) as navn2, MAX(OutTime) as sidsteTur
FROM Member m1, Member m2, TripMember,Trip
 WHERE
 TripMember.TripID=Trip.id AND
 m2.id = TripMember.member_id AND
 m1.FirstName = m2.FirstName AND
 m1.LastName =  m2.LastName AND
 m1.FirstName != "" AND
 m1.MemberID NOT Like 'g%' AND
 m1.MemberID NOT Like 'k%' AND
(m2.MemberID Like 'g%' OR m2.MemberID Like 'k%') AND
m1.RemoveDate IS NOT NULL
GROUP BY m1.id,m2.id;

 


SELECT m1.RemoveDate as udmeldt,m1.MemberId,mg.MemberID as gæsteID, CONCAT(m1.FirstName," ",m1.LastName) as navn1, MAX(OutTime) as sidsteTur
FROM Member m1, Member mg, TripMember,Trip
 WHERE
 TripMember.TripID=Trip.id AND
 mg.id = TripMember.member_id AND
 SOUNDEX(m1.FirstName) = SOUNDEX(mg.FirstName) AND
 SOUNDEX(m1.LastName) =  SOUNDEX(mg.LastName) AND
 m1.FirstName != "" AND
 m1.MemberID NOT Like 'g%' AND
 m1.MemberID NOT Like 'k%' AND
(mg.MemberID Like 'g%' OR mg.MemberID Like 'k%') AND
m1.RemoveDate IS NOT NULL
GROUP BY m1.id,mg.id
HAVING m1.RemoveDate<MAX(OutTime);


 


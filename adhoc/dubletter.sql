SELECT m1.MemberId,m2.MemberID,CONCAT(m1.FirstName," ",m1.LastName) as navn
FROM Member m1, Member m2
WHERE
 m1.FirstName = TRIM(TRIM(CHAR(9) FROM m2.FirstName)) AND
 m1.LastName = TRIM(TRIM(CHAR(9) FROM m2.LastName)) AND
 m1.FirstName != "" AND
 m1.MemberID NOT Like 'g%' AND
 m1.MemberID NOT Like 'k%' AND
 m1.id>m2.id;
 
 



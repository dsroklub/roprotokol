
$s='SELECT m.FirstName,m.LastName,kanin.MemberID as kaninnummer,m.MemberID, MIN(tk.OutTime) as first_kanin_tur, MIN(tm.OutTime) as first_medlem_tur,m.JoinDate  
    FROM  Member kanin, Member m, Trip tk, Trip tm,TripMember tmk, TripMember tmm
    WHERE kanin.MemberID LIKE "k%" AND kanin.FirstName=m.FirstName AND kanin.LastName=m.LastName
          AND m.id!=kanin.id AND m.MemberID NOT LIKE "k%" AND m.MemberID NOT LIKE "g%" 
          AND tmm.TripID=tm.id AND tmm.member_id=m.id
          AND tmk.TripID=tk.id AND tmk.member_id=kanin.id
   AND NOT EXISTS (SELECT 'x' FROM Member mm WHERE mm.MemberID NOT LIKE "k%" AND mm.FirstName=m.FirstName AND mm.LastName=m.LastName AND m.id!=mm.id) 
   GROUP BY kanin.id,m.id
   ORDER BY m.FirstName,m.LastName';


$all='
    SELECT kanin.FirstName,kanin.LastName,kanin.MemberID as kaninnummer, MIN(tk.OutTime) as first_kanin_tur
    FROM  Member kanin, Trip tk, TripMember tmk
    WHERE kanin.MemberID REGEXP "[KNX].*"
          AND tmk.TripID=tk.id AND tmk.member_id=kanin.id
   GROUP BY kanin.id
   ORDER BY kanin.FirstName,kanin.LastName
';
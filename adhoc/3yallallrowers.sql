SELECT Concat(FirstName," ",LastName) as name, MemberID, IFNULL(SUM(Meter)/1000,0) AS ikke_ls_langturskm_3aar
FROM Member INNER JOIN
       (TripMember  INNER JOIN  Trip ON TripMember.TripID=Trip.id AND
          DATE_SUB(CURDATE(),INTERVAL 3 YEAR)<Trip.OutTime) ON
       TripMember.member_id=Member.id  AND Trip.OutTime AND Trip.TripTypeID=3
    WHERE Member.RemoveDate IS NULL AND NOT EXISTS
       (SELECT 'c' FROM MemberRights WHERE MemberRight="longdistance" AND MemberRights.member_id=Member.id)
    GROUP BY Member.id
    ORDER BY ikke_ls_langturskm_3aar DESC,Member.FirstName;

SELECT Concat(FirstName," ",LastName) as name, MemberID, IFNULL(SUM(Meter)/1000,0) as cox_langturskm_3aar
FROM MemberRights, Member LEFT JOIN
       (TripMember  INNER JOIN  Trip ON TripMember.TripID=Trip.id AND
          DATE_SUB(CURDATE(),INTERVAL 3 YEAR)<Trip.OutTime) ON
       TripMember.member_id=Member.id  AND Trip.OutTime AND Trip.TripTypeID=3
    WHERE MemberRights.member_id=Member.id AND MemberRight="longdistance"  AND Member.RemoveDate IS NULL
    GROUP BY Member.id
    ORDER BY cox_langturskm_3aar DESC,Member.FirstName;

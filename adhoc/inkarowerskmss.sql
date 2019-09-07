
SELECT YEAR(Trip.OutTime) as år,CONCAT(FirstName,' ',LastName) as roer,SUM(Meter)/1000 as tidlig_km
FROM Trip,TripType, TripMember,season,Member
WHERE TripType.id=Trip.TripTypeID AND TripMember.TripID=Trip.id AND TripMember.member_id=Member.id AND TripType.Name="Inriggerkaproning" AND YEAR(Trip.OutTime)=season.season AND Trip.OutTime < summer_start
GROUP BY YEAR(Trip.OutTime),Member.id
ORDER BY år,tidlig_km
;

SELECT YEAR(Trip.OutTime) as år,CONCAT(FirstName,' ',LastName) as roer,SUM(Meter)/1000 as tidlig_km,TripType.Name
FROM Trip,TripType, TripMember,season,Member
WHERE TripType.id=Trip.TripTypeID AND TripMember.TripID=Trip.id AND TripMember.member_id=Member.id AND YEAR(Trip.OutTime)=season.season AND Trip.OutTime < summer_start
AND EXISTS (SELECT 'x' FROM TripType tt,TripMember tm,Trip ti WHERE tm.member_id=Member.id AND tm.TripID=ti.id AND ti.TripTypeID=tt.id AND tt.Name="Inriggerkaproning" AND YEAR(Trip.OutTime)=YEAR(ti.OutTime))
GROUP BY YEAR(Trip.OutTime),Member.id,TripType.Name
ORDER BY år,roer,tidlig_km
;

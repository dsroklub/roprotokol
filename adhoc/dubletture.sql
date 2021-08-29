 SELECT Boat.Name,Trip.Destination,TIMEDIFF(InTime,OutTime), Trip.id as trid_id, OutTime,InTime
 FROM Trip,Boat
 WHERE TIMEDIFF (InTime,OutTime)< 1200 AND YEAR(OutTime)=YEAR(NOW()) AND
 EXISTS (SELECT 'x' FROM Member, Trip t1, TripMember tm1, TripMember tm2 WHERE tm1.TripID = t1.id AND tm2.TripID=Trip.id and t1.id>Trip.id AND tm1.member_id=Member.id and tm2.member_id=Member.id AND DATE(Trip.OutTime)=Date(t1.OutTime))
 AND Boat.id=BoatID
 ;

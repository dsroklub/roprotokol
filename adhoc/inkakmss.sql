SELECT YEAR(Trip.OutTime) as år,SUM(Meter)/1000 as km
FROM Trip,TripType WHERE TripType.id=Trip.TripTypeID AND TripType.Name="Inriggerkaproning"
GROUP BY YEAR(Trip.OutTime);

SELECT YEAR(Trip.OutTime) as år,SUM(Meter)/1000 as tidlig_km
FROM Trip,TripType, season
WHERE TripType.id=Trip.TripTypeID AND TripType.Name="Inriggerkaproning" AND YEAR(Trip.OutTime)=season.season AND Trip.OutTime < summer_start
GROUP BY YEAR(Trip.OutTime);

SELECT YEAR(Trip.OutTime) as år,MONTH(Trip.OutTime) as måned,SUM(Meter)/1000 as tidlig_km
FROM Trip,TripType, season
WHERE TripType.id=Trip.TripTypeID AND TripType.Name="Inriggerkaproning" AND YEAR(Trip.OutTime)=season.season AND Trip.OutTime < summer_start
GROUP BY YEAR(Trip.OutTime),MONTH(Trip.OutTime) ORDER BY YEAR(Trip.OutTime),MONTH(Trip.OutTime);

SELECT YEAR(Trip.OutTime) as år,DAYNAME(Trip.OutTime) as ugedag,SUM(Meter)/1000 as tidlig_km
FROM Trip,TripType, season
WHERE TripType.id=Trip.TripTypeID AND TripType.Name="Inriggerkaproning" AND YEAR(Trip.OutTime)=season.season AND Trip.OutTime < summer_start
GROUP BY YEAR(Trip.OutTime),DAYNAME(Trip.OutTime) ORDER BY YEAR(Trip.OutTime),tidlig_km DESC,DAYNAME(Trip.OutTime);

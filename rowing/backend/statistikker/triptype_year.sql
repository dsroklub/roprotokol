SELECT TripType.Name,ROUND(SUM(Meter/1000)) as Km ,count('x') as ture FROM Trip,TripType
WHERE TripType.id=Trip.TripTypeID AND YEAR(Trip.OutTime)=YEAR(NOW())
GROUP BY TripType.id ORDER BY Km DESC
;

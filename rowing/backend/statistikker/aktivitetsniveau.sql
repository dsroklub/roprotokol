select YEAR(OutTime) as Season,FORMAT(SUM(Meter/1000),1),count('x') FROM Trip GROUP BY YEAR(OutTime);

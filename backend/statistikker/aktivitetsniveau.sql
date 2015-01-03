select Season,FORMAT(SUM(Meter/1000),1),count('x') FROM Trip group by Season;

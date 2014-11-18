-- set global group_concat_max_len = 9000;

SELECT '{' as "" FROM dual;

SELECT 
     CONCAT('"',GruppeID,'" : [',
	              GROUP_CONCAT(
               	        CONCAT('{"id":"',BådID,'"'),
               	        CONCAT(',"name":"',Båd.Navn,'"'),
 	       	        CONCAT(',"spaces":',Båd.Pladser),
               	        CONCAT(',"status":"OK"}')
          ),'],') as "" FROM Båd,Gruppe   WHERE GruppeID=FK_GruppeID GROUP BY GruppeID;

SELECT '"999":[]}' as "" FROM dual;


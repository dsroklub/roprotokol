set global group_concat_max_len = 50000;

SELECT 
       CONCAT("{",
	              GROUP_CONCAT(
		        '"',BådID,'" : ',
               	        CONCAT('{"id":"',BådID,'"'),
               	        CONCAT(',"name":"',Båd.Navn,'"'),
 	       	        CONCAT(',"spaces":',Båd.Pladser),
               	        CONCAT(',"status":"OK"}')
          )
   ,'}') as "" FROM Båd,Gruppe WHERE GruppeID=FK_GruppeID;

#SELECT BådID,Båd.Navn,Båd.Pladser FROM Båd,Gruppe WHERE GruppeID=FK_GruppeID;


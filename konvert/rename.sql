RENAME TABLE Båd TO Boat,
       Bådindstilling TO BoatSpec,
       BådKategori TO BoatCategory,
       Fejl_system TO Error_system,
       Fejl_tur TO Error_trip,
       Gruppe TO Boat_types,
       Kajak_typer TO Kayak_types,
       Kommentar TO Comment,
       LåsteBåde TO LockedBoats,
       Medlem TO Member,
       Skade TO Damage;

ALTER TABLE Boat CHANGE BådID BoatID INT;
       

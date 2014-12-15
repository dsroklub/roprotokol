DELETE FROM MemberRights;
INSERT INTO MemberRights (MemberID,MemberRight,Acquired,argument)
SELECT MemberID, 'motorboat' as MemberRight, Motorboat Acquired, NULL as argument FROM tblMembersSportData WHERE Motorboat NOT LIKE "t%" UNION
SELECT MemberID, 'motorboat','1915-01-01', Motorboat FROM tblMembersSportData WHERE Motorboat LIKE "t%"  UNION
SELECT MemberID, 'rowright', Roret, NULL FROM tblMembersSportData WHERE Roret IS NOT NULL UNION
SELECT MemberID, 'coxtheory', NULL, TeoretiskStyrmandKursus FROM tblMembersSportData WHERE TeoretiskStyrmandKursus IS NOT NULL UNION
SELECT MemberID, 'cox', NULL, Styrmand FROM tblMembersSportData WHERE Styrmand IS NOT NULL UNION
SELECT MemberID, 'longdistancetheory',NULL, TeoretiskLangtursStyrmandKursus FROM tblMembersSportData WHERE TeoretiskLangtursStyrmandKursus IS NOT NULL UNION
SELECT MemberID, 'skærgård',NULL, Skaergaard FROM tblMembersSportData WHERE Skaergaard IS NOT NULL UNION
SELECT MemberID, 'langturøresund',NULL, Langtur_Oeresund FROM tblMembersSportData WHERE Langtur_Oeresund IS NOT NULL UNION
SELECT MemberID, 'longdistance',NULL, Langtur FROM tblMembersSportData WHERE Langtur IS NOT NULL UNION
SELECT MemberID, '8', Ormen, NULL FROM tblMembersSportData WHERE Ormen IS NOT NULL UNION
SELECT MemberID, 'svava', Svava,NULL FROM tblMembersSportData WHERE Svava IS NOT NULL UNION
SELECT MemberID, 'sculler', Sculler,NULL FROM tblMembersSportData WHERE Sculler IS NOT NULL UNION
SELECT MemberID, 'kajak', Kajak, NULL FROM tblMembersSportData WHERE Kajak IS NOT NULL UNION
SELECT MemberID, '2kajak', Kajak_2, NULL FROM tblMembersSportData WHERE Kajak_2 IS NOT NULL UNION
SELECT MemberID, 'swim400', Swim_400,NULL FROM tblMembersSportData WHERE Swim_400 IS NOT NULL UNION
SELECT MemberID, 'instructor', RoInstruktoer, 'row' FROM tblMembersSportData WHERE RoInstruktoer IS NOT NULL UNION
SELECT MemberID, 'instructor', StyrmandInstruktoer, 'cox' FROM tblMembersSportData WHERE StyrmandInstruktoer IS NOT NULL UNION
SELECT MemberID, 'instructor', ScullerInstruktoer, 'sculler' FROM tblMembersSportData WHERE ScullerInstruktoer IS NOT NULL UNION
SELECT MemberID, 'instructor', KajakInstruktoer,'kajak' FROM tblMembersSportData WHERE KajakInstruktoer IS NOT NULL UNION
SELECT MemberID, 'competition', NULL, Kaproer FROM tblMembersSportData WHERE Kaproer IS NOT NULL UNION
SELECT MemberID, 'notes', '1916-01-01', GROUP_CONCAT(CONCAT(COALESCE(diverse1,""),COALESCE(diverse2,"")))  FROM tblMembersSportData WHERE diverse1 IS NOT NULL OR diverse2 IS NOT NULL GROUP BY MemberID;

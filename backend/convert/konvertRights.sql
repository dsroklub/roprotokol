DELETE FROM MemberRights;
INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument)
SELECT Member.id, 'motorboat' as MemberRight, Motorboat Acquired, NULL as argument FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Motorboat NOT LIKE "t%" UNION
SELECT Member.id, 'motorboat','1915-01-01', Motorboat FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Motorboat LIKE "t%"  UNION
SELECT Member.id, 'rowright', Roret, NULL FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Roret IS NOT NULL UNION
SELECT Member.id, 'coxtheory', NULL, TeoretiskStyrmandKursus FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND TeoretiskStyrmandKursus IS NOT NULL UNION
SELECT Member.id, 'cox', NULL, Styrmand FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Styrmand IS NOT NULL UNION
SELECT Member.id, 'longdistancetheory',NULL, TeoretiskLangtursStyrmandKursus FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND TeoretiskLangtursStyrmandKursus IS NOT NULL UNION
SELECT Member.id, 'skærgård',NULL, Skaergaard FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Skaergaard IS NOT NULL UNION
SELECT Member.id, 'langturøresund',NULL, Langtur_Oeresund FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Langtur_Oeresund IS NOT NULL UNION
SELECT Member.id, 'longdistance',NULL, Langtur FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Langtur IS NOT NULL UNION
SELECT Member.id, '8', Ormen, NULL FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Ormen IS NOT NULL UNION
SELECT Member.id, 'svava', Svava,NULL FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Svava IS NOT NULL UNION
SELECT Member.id, 'sculler', Sculler,NULL FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Sculler IS NOT NULL UNION
SELECT Member.id, 'kajak', Kajak, NULL FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Kajak IS NOT NULL UNION
SELECT Member.id, '2kajak', Kajak_2, NULL FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Kajak_2 IS NOT NULL UNION
SELECT Member.id, 'swim400', Swim_400,NULL FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Swim_400 IS NOT NULL UNION
SELECT Member.id, 'instructor', RoInstruktoer, 'row' FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND RoInstruktoer IS NOT NULL UNION
SELECT Member.id, 'instructor', StyrmandInstruktoer, 'cox' FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND StyrmandInstruktoer IS NOT NULL UNION
SELECT Member.id, 'instructor', ScullerInstruktoer, 'sculler' FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND ScullerInstruktoer IS NOT NULL UNION
SELECT Member.id, 'instructor', KajakInstruktoer,'kajak' FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND KajakInstruktoer IS NOT NULL UNION
SELECT Member.id, 'competition', NULL, Kaproer FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND Kaproer IS NOT NULL UNION
SELECT Member.id, 'notes', '1916-01-01', GROUP_CONCAT(CONCAT(COALESCE(diverse1,""),COALESCE(diverse2,"")))  FROM tblMembersSportData,Member WHERE Member.MemberID=tblMembersSportData.MemberID AND diverse1 IS NOT NULL OR diverse2 IS NOT NULL GROUP BY Member.MemberID;

INSERT INTO Member ( MemberID, LastName, FirstName,JoinDate,RemoveDate, Email, ShowEmail, Birthday, Gender,KommuneKode,CprNo )
  SELECT DISTINCTROW tMem.MemberID,
                     tMem.LastName,
                     tMem.FirstName,
                     tMem.JoinDate,
                     tMem.RemoveDate,
                     tMem.E_mail,
                     tMem.OnAddressList,
		     tMem.Birthdate,
		     tMem.KommuneKode,
		     tMem.CprNo,
		     CASE tMem.Sex WHEN 'm' THEN 0 WHEN 'f' THEN 1 ELSE NULL END
  FROM tblMembersToRoprotokol tMem
  WHERE (((tMem.RemoveDate) IS NULL) AND MemberID NOT IN (SELECT MemberID From Member))
  ORDER BY tMem.MemberID;

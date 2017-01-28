
UPDATE Member,tblMembersToRoprotokol
SET
Member.KommuneKode=tblMembersToRoprotokol.KommuneKode,
Member.CprNo=tblMembersToRoprotokol.CprNo
    WHERE CAST(tblMembersToRoprotokol.MemberID AS CHAR) =Member.MemberID;

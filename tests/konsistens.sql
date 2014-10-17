select BådID,Båd.Navn,Båd.Pladser, Gruppe.Pladser FROM Båd,Gruppe WHERE GruppeID=FK_GruppeID AND Båd.Pladser != Gruppe.Pladser ;
-- +--------+----------+---------+---------+
-- | BådID  | Navn     | Pladser | Pladser |
-- +--------+----------+---------+---------+
-- |     50 | Mjølner  |       5 |       4 |
-- |    200 | Hermes   |       4 |       5 |
-- +--------+----------+---------+---------+
-- Vi skal fjerne Pladser fra Båd
-- Både i samme gruppe skal også have samme antal sæder.

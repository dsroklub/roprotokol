#!/usr/bin/python
# -*- coding: utf-8 -*-
import MySQLdb
import random
import math


numrowers=1000

db = MySQLdb.connect(host="localhost", # your host, usually localhost
                     user="roprotokol", # your username
                     passwd="roprotokol",
                     charset='utf8',
                      db="roprotokol") # name of the data base
cur = db.cursor() 

#cur.execute("SELECT * FROM Medlem")

fnames=["Agnes","Agnete","Alberte","Amalie","Amanda","Andrea","Ane","Anette","Anna","Anne","Annemette","Annette","Asta","Astrid","Benedikte","Bente","Benthe","Berit","Beth","Bettina","Birgit","Birgitte","Birte","Birthe","Bodil","Britta","Camilla","Carina","Caroline","Catrine","Cecilie","Charlotte","Clara","Connie","Conny","Dagmar","Dagny","Daniella","Dina","Ditte","Doris","Dorte","Dorthe","Edith","Elin","Elisabeth","Ella","Ellen","Elna","Else","Elsebeth","Emilie","Emily","Emma","Erna","Esmarelda","Ester","Filippa","Frederikke","Freja","Frida","Gerda","Gertrud","Gitte","Grete","Grethe","Gundhild","Gunhild","Gurli","Hanne","Heidi","Helen","Helle","Henriette","Herdis","Iben","Ida","Inge","Ingelise","Inger","Ingrid","Irma","Isabella","Janne","Janni","Jannie","Jasmin","Jenny","Joan","Johanne","Jonna","Josefine","Josephine","Julie","Jytte","Karen","Karin","Karina","Karla","Karoline","Katcha","Katja","Katrine","Kirsten","Kirstin","Kirstine","Klara","Kristina","Kristine","Laura","Lea","Lena","Lene","Leonora","Line","Liva","Lona","Lone","Lotte","Louise","Lærke","Maiken","Maja","Majken","Malene","Malou","Maren","Margit","Margrethe","Maria","Marianne","Marie","Marlene","Mathilde","Maya","Merete","Merethe","Mette","Mia","Michala","Michelle","Mie","Mille","Mimi","Minna","Nadia","Naja","Nana","Nanna","Nanni","Natasha","Natasja","Nete","Nicoline","Nina","Nora","Ofelia","Olga","Olivia","Patricia","Paula","paulina","Pernille","Pia","Ragna","Ragnhild","Randi","Rebecca","Regitze","Rikke","Rita","Ritt","Ronja","Rosa","Ruth","Sabine","Sandra","Sanne","Sara","Sarah","Selma","Signe","Sigrid","Sille","Simone","Sine","Sofia","Sofie","Solveig","Solvej","Sonja","Sophie","Stina","Stine","Susanne","Sussanne","Sussie","Sys","Sørine","Søs","Tammy","Tanja","Thea","Tilde","Tina","Tine","Tove","Trine","Ulla","Ulrike","Ursula","Vera","Victoria","Viola","Vivian","Winnie","Xenia","Yasmin","Yda","Yrsa","Yvonne","Zahra","Zara","Åse","Adam","Albert","Aksel","Alex","Alexander","Alf","Allan","Alvin","Anders","André","Andreas","Anton","Arne","Asger","August","Benjamin","Benny","Bent","Bertil","Bertram","Birger","Bjarne","Bo","Bob","Bobby","Boe","Boris","Borris","Brian","Bruno","Bøje","Børge","Carl","Carlo","Carsten","Casper","Christen","Christian","Christoffer","Christopher","Claus","Clavs","Curt","Dan","Daniel","Danny","Dave","David","Dennis","Ebbe","Einar","Einer","Elias","Emil","Eric","Erik","Erling","Ernst","Esben","Finn","Flemming","Frank","Frans","Freddy","Frede","Frederik","Frode","Georg","George","Gert","Gorm","Gunnar","Gunner","Gustav","Hans","Helge","Henrik","Henry","Herbert","Herman","Hjalte","Holger","Hugo","Ib","Ivan","Iver","Jack","Jacob","Jakob","James","Jan","Jano","Jarl","Jean","Jens","Jeppe","Jesper","Jim","Jimmy","Joachim","Joakim","Johan","Johannes","John","Johnnie","Johnny","Jon","Jonas","Jonathan","Julius","Jørgen","Karl","Karlo","Karsten","Kaspar","Kasper","Keld","Ken","Kenn","Kenneth","Kenny","Kent","Kim","Kjeld","Klaus","Klavs","Kristian","Kurt","Kåre","Lars","Lasse","Laurits","Laus","Laust","Leif","Lennarth","Lucas","Ludvig","Mads","Magnus","Malthe","Marcus","Marius","Mark","Martin","Mathias","Matthias","Michael","Mik","Mikael","Mike","Mikkel","Mogens","Morten","Nick","Nicklas","Niels","Nikolai","Nils","Noah","Ole","Olfert","Oliver","Oscar","Oskar","Osvald","Otto","Ove","Palle","Patrick","Paw","Peder","Per","Pete","Peter","Paul","Philip","Poul","Preben","Ragnar","Rasmus","Richard","Richardt","Robert","Robin","Rolf","Ron","Ronni","Ronnie","Rune","Sebastian","Silas","Simon","Steen","Stefan","Sten","Stig","Svenning","Søren","Tage","Thomas","Tim","Timmy","Tobias","Tom","Tommy","Tonny","Torben","Troels","Uffe","Ulf","Ulrik","Vagn","Valdemar","Verner","Victor","Villads","Werner","William","Yngve","Zacharias","Ziggy","Øivind","Øjvind","Øyvind","Aage","Åge"]

lnames=["Jensen","Nielsen","Hansen","Pedersen","Andersen","Christensen","Larsen","Sørensen","Rasmussen","Jørgensen","Petersen","Madsen","Kristensen","Olsen","Thomsen","Christiansen","Poulsen","Johansen","Møller","Mortensen","Bendtsen","Wilhelmsen"]

cur.execute("DELETE FROM Medlem")
cur.execute("DELETE FROM TripMember")
cur.execute("DELETE FROM Trip")

m=dict()

for fid in range(1, 1000) :
    fname=fnames[random.randrange(0, len(fnames)-1)]
    lname=lnames[random.randrange(0, len(lnames)-1)]
    cur.execute("INSERT INTO Medlem (MedlemID, Medlemsnr, Fornavn, Efternavn) VALUES ("+str(fid)+','+'"'+str(fid+2000)+'","'+fname+'","'+lname+'");')
    m[fid]=fname+' A. '+lname
    cur.execute("SELECT BådID, Pladser FROM Båd")

boats=cur.fetchall()

for tid in range(1, 4000):
    boat=boats[random.randrange(0, len(boats)-1)]
    bid=boat[0]
    pladser=int(boat[1])
    destination=random.randrange(1, 19)
    triptype=random.randrange(1, 12)
    q="INSERT INTO Trip (TripID, Season, BoatID,Destination,Meter,TripTypeID,DESTID, OutTime) VALUES ("+str(tid)+',2014,'+str(bid)+',"et eller andet sted",'+str(random.randrange(500,50000))+','+str(triptype)+','+str(destination)+',"2014-05-14 02:02:03")';
    print q
    cur.execute(q);
    for d in range(1,pladser):
        rower=int(math.sqrt(random.randrange(0, (numrowers-1)**2)))
        qm='INSERT INTO TripMember (TripID,Season,Seat,MemberID,MemberName,CreatedDate) VALUES ('+str(tid)+',2014,'+str(d)+','+str(rower)+',"'+m[rower]+'","2014-04-14 00:00:00")'
        print qm
        cur.execute(qm)



db.commit()
db.close
#for row in cur.fetchall() :
#    print row[0]

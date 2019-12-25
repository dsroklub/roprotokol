#!/usr/bin/python
# -*- coding: utf-8 -*-
import MySQLdb
import random
import math
import os.path
import sys
import time
from datetime import tzinfo, timedelta, datetime
now=datetime.now()
 
numrowers=1000
if len(sys.argv)>1:
    rodb=sys.argv[1]
    dbuser=roprotokol
else:
    rodb="fakeprotokol"
    dbuser="fake"

print "using db: "+rodb

random.seed(42)
# Password is read from file sectret.db
# but do not write it in this file
pwfile=os.path.dirname(sys.argv[0])+'/../../config.ini'
print "checking db pw file "+pwfile
if os.path.exists(pwfile):
    # FIXME, do not assume line 2, check for left side dbpassword
    dbpw=(open(pwfile).readlines()[1].split('='))[1].strip()
    print "using PASSWORD #"+dbpw+"#"
    db= MySQLdb.connect(host="localhost",  user=dbuser, passwd=dbpw,charset='utf8', db=rodb)
else:
    db= MySQLdb.connect(host="localhost",  user="fake", charset='utf8', db=rodb)

cur = db.cursor()

#cur.execute("SELECT * FROM Member")

destinations=["No dest","Bellevue","Charlottenlund","Flakfortet","Hellerup","Kanalen","Knud","Langelinie","Margretheholms havn","Opfyldningen nord","Rungsted","Skodsborg","Skovshoved","Slusen","Strandmøllen",
              "Svanemøllehavnen","Tuborg havn","Tårbæk","Vedbæk","Langt Væk","London"];

fnames=["Agnes","Agnete","Alberte","Amalie","Amanda","Andrea","Ane","Anette","Anna","Anne","Annemette","Annette","Asta","Astrid","Benedikte","Bente","Benthe","Berit","Beth","Bettina","Birgit","Birgitte","Birte","Birthe","Bodil","Britta","Camilla","Carina","Caroline","Catrine","Cecilie","Charlotte","Clara","Connie","Conny","Dagmar","Dagny","Daniella","Dina","Ditte","Doris","Dorte","Dorthe","Edith","Elin","Elisabeth","Ella","Ellen","Elna","Else","Elsebeth","Emilie","Emily","Emma","Erna","Esmarelda","Ester","Filippa","Frederikke","Freja","Frida","Gerda","Gertrud","Gitte","Grete","Grethe","Gundhild","Gunhild","Gurli","Hanne","Heidi","Helen","Helle","Henriette","Herdis","Iben","Ida","Inge","Ingelise","Inger","Ingrid","Irma","Isabella","Janne","Janni","Jannie","Jasmin","Jenny","Joan","Johanne","Jonna","Josefine","Josephine","Julie","Jytte","Karen","Karin","Karina","Karla","Karoline","Katcha","Katja","Katrine","Kirsten","Kirstin","Kirstine","Klara","Kristina","Kristine","Laura","Lea","Lena","Lene","Leonora","Line","Liva","Lona","Lone","Lotte","Louise","Lærke","Maiken","Maja","Majken","Malene","Malou","Maren","Margit","Margrethe","Maria","Marianne","Marie","Marlene","Mathilde","Maya","Merete","Merethe","Mette","Mia","Michala","Michelle","Mie","Mille","Mimi","Minna","Nadia","Naja","Nana","Nanna","Nanni","Natasha","Natasja","Nete","Nicoline","Nina","Nora","Ofelia","Olga","Olivia","Patricia","Paula","paulina","Pernille","Pia","Ragna","Ragnhild","Randi","Rebecca","Regitze","Rikke","Rita","Ritt","Ronja","Rosa","Ruth","Sabine","Sandra","Sanne","Sara","Sarah","Selma","Signe","Sigrid","Sille","Simone","Sine","Sofia","Sofie","Solveig","Solvej","Sonja","Sophie","Stina","Stine","Susanne","Sussanne","Sussie","Sys","Sørine","Søs","Tammy","Tanja","Thea","Tilde","Tina","Tine","Tove","Trine","Ulla","Ulrike","Ursula","Vera","Victoria","Viola","Vivian","Winnie","Xenia","Yasmin","Yda","Yrsa","Yvonne","Zahra","Zara","Åse","Adam","Albert","Aksel","Alex","Alexander","Alf","Allan","Alvin","Anders","André","Andreas","Anton","Arne","Asger","August","Benjamin","Benny","Bent","Bertil","Bertram","Birger","Bjarne","Bo","Bob","Bobby","Boe","Boris","Borris","Brian","Bruno","Bøje","Børge","Carl","Carlo","Carsten","Casper","Christen","Christian","Christoffer","Christopher","Claus","Clavs","Curt","Dan","Daniel","Danny","Dave","David","Dennis","Ebbe","Einar","Einer","Elias","Emil","Eric","Erik","Erling","Ernst","Esben","Finn","Flemming","Frank","Frans","Freddy","Frede","Frederik","Frode","Georg","George","Gert","Gorm","Gunnar","Gunner","Gustav","Hans","Helge","Henrik","Henry","Herbert","Herman","Hjalte","Holger","Hugo","Ib","Ivan","Iver","Jack","Jacob","Jakob","James","Jan","Jano","Jarl","Jean","Jens","Jeppe","Jesper","Jim","Jimmy","Joachim","Joakim","Johan","Johannes","John","Johnnie","Johnny","Jon","Jonas","Jonathan","Julius","Jørgen","Karl","Karlo","Karsten","Kaspar","Kasper","Keld","Ken","Kenn","Kenneth","Kenny","Kent","Kim","Kjeld","Klaus","Klavs","Kristian","Kurt","Kåre","Lars","Lasse","Laurits","Laus","Laust","Leif","Lennarth","Lucas","Ludvig","Mads","Magnus","Malthe","Marcus","Marius","Mark","Martin","Mathias","Matthias","Michael","Mik","Mikael","Mike","Mikkel","Mogens","Morten","Nick","Nicklas","Niels","Nikolai","Nils","Noah","Ole","Olfert","Oliver","Oscar","Oskar","Osvald","Otto","Ove","Palle","Patrick","Paw","Peder","Per","Pete","Peter","Paul","Philip","Poul","Preben","Ragnar","Rasmus","Richard","Richardt","Robert","Robin","Rolf","Ron","Ronni","Ronnie","Rune","Sebastian","Silas","Simon","Steen","Stefan","Sten","Stig","Svenning","Søren","Tage","Thomas","Tim","Timmy","Tobias","Tom","Tommy","Tonny","Torben","Troels","Uffe","Ulf","Ulrik","Vagn","Valdemar","Verner","Victor","Villads","Werner","William","Yngve","Zacharias","Ziggy","Øivind","Øjvind","Øyvind","Aage","Åge"]

lnames=["Jensen","Nielsen","Hansen","Pedersen","Andersen","Christensen","Larsen","Sørensen","Rasmussen","Jørgensen","Petersen","Madsen","Kristensen","Olsen","Thomsen","Christiansen","Poulsen","Johansen","Møller","Mortensen","Bendtsen","Wilhelmsen"]

cur.execute("DELETE FROM TripMember")
cur.execute("DELETE FROM Trip")
cur.execute("DELETE FROM Member")
cur.execute("DELETE FROM MemberRights")

m=dict()

for fid in range(1, 1000) :
    fname=fnames[random.randrange(0, len(fnames)-1)]
    lname=lnames[random.randrange(0, len(lnames)-1)]
    rndrights=random.randrange(0, 100)
    pastdays=timedelta(days=random.randrange(0, 1000))
    rdate=now-pastdays
    mid=str(fid+2000)
    if (rndrights<10):
        mid='k'+str(fid)
    print("INSERT INTO Member (id, MemberID, FirstName, LastName) VALUES ("+str(fid)+','+'"'+mid+'","'+fname+'","'+lname+'");')
    cur.execute("INSERT INTO Member (id, MemberID, FirstName, LastName) VALUES ("+str(fid)+','+'"'+mid+'","'+fname+'","'+lname+'");')
    if (rndrights>15):
        print "INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"rowright","'+rdate.strftime("%Y-%m-%d")+'","");'
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"rowright","'+rdate.strftime("%Y-%m-%d")+'","");')
    if (rndrights>50):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"cox","'+rdate.strftime("%Y-%m-%d")+'","");')
    if (rndrights>133):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"wrench","'+rdate.strftime("%Y-%m-%d")+'","");')
    if (rndrights>77):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"longdistance","'+rdate.strftime("%Y-%m-%d")+'","");')
    if (rndrights>85):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"instructor","'+rdate.strftime("%Y-%m-%d")+'","row");')
    rndrights=random.randrange(0, 100)
    if (rndrights>90):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"instructor","'+rdate.strftime("%Y-%m-%d")+'","sculler");')
    if (rndrights>92):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"instructor","'+rdate.strftime("%Y-%m-%d")+'","kajak");')
    if (rndrights>92):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"instructor","'+rdate.strftime("%Y-%m-%d")+'","svava");')
    if (rndrights>20):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"competition","'+rdate.strftime("%Y-%m-%d")+'","");')
    if (rndrights>90):
        cur.execute("INSERT INTO MemberRights (member_id,MemberRight,Acquired,argument) VALUES ("+str(fid)+',"notes","'+rdate.strftime("%Y-%m-%d")+'","der er noget lumsk med ham");')

    m[fid]=fname+' A. '+lname
    cur.execute("SELECT Boat.id, Seatcount FROM Boat,BoatType Where BoatType.Name=Boat.boat_type;")

boats=cur.fetchall()

print "we have " +str(len(boats))+ " boats"
for tid in range(1, 4000):
    pastdays=timedelta(days=random.randrange(0, 1000))
    rtime=now-pastdays
    rtime.replace(hour=random.randrange(7, 20),minute=random.randrange(0, 59))
    outtime=rtime.strftime('"%Y-%m-%d %H:%M:%S"')
    rtime=rtime+timedelta(minutes=random.randrange(0, 320))
    intime=rtime.strftime('"%Y-%m-%d %H:%M:%S"')

    boat=boats[random.randrange(0, len(boats)-1)]
    if tid == 3999:
        intime='NULL'
        boat=boats[len(boats)-1]
    bid=boat[0]
    pladser=int(boat[1])
    destination=random.randrange(1, 19)
    triptype=random.randrange(1, 12)
    dayspast=random.randrange(0, 800)
    q="INSERT INTO Trip (id, BoatID,Destination,Meter,TripTypeID,DESTID, OutTime, InTime) VALUES ("+str(tid)+','+str(bid)+',"'+str(destinations[destination])+'",'+str(random.randrange(500,50000))+','+str(triptype)+','+str(destination)+','+outtime+', '+intime+')';
    print q
    cur.execute(q);
    for d in range(0,pladser):
        rower=int(math.sqrt(random.randrange(0, (numrowers-1)**2)))
        qm='INSERT INTO TripMember (TripID,Seat,member_id,CreatedDate) VALUES ('+str(tid)+','+str(d)+','+str(rower)+',"2016-01-12 00:00:00")'
        print qm
        cur.execute(qm)
db.commit()
db.close
#for row in cur.fetchall() :
#    print row[0]

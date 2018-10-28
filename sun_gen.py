#!/usr/bin/python

import random, MySQLdb, array, math, uuid, string

comm =[[],[]]
generator = 0

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_suns(cur):

    sql_query = ("SELECT SUN_CLASS, Commonness FROM sun_classes")
    cur.execute(sql_query)
    suns = cur.fetchall()

    for row in suns:
        comm[0].append(row[0])
        comm[1].append(int(row[1]*100000))

    return comm

def find_sun(comm):

    sunclass = sum(([t] * w for t, w in zip(*comm)), [])
    sun = random.choice(sunclass)
    
    print("Success! Found new sun")
    print("Sun Class is: %s") % sun

    return sun

def create_sun(cur, sun):

    sql_query = ("SELECT SUN_CLASS, Temp_min, Temp_max, Mass_min, Mass_max, Radius_min, Radius_max, Luminosity_min, Lumiosity_max FROM sun_classes WHERE SUN_CLASS='%s'" % sun)

    cur.execute(sql_query)
    suns = cur.fetchall()

    for row in suns:
        print("Importing Meta data...") 
        print row

    print("Generating attributes for Sun...")

    temp = random.randint(row[1],row[2])
    mass = round((random.uniform(row[3],row[4])),2)
    radius = round((random.uniform(row[5],row[6])),2)
    lumi = round((random.uniform(row[7],row[8])),2)

    print("Sun temperature is %s K, Mass is %s, Radius is %s, Luminosity is %s" % (temp, mass, radius, lumi))

    return temp, mass, radius, lumi

def sun_name():

    letters = ''.join(random.choice('FXGJAKSDF') for i in range(5))
    digits = random.randint(10000,99999)
    name = "4FX-%s-%d" % (letters,digits)
        
    print("Sun's name is now %s") % name

    return name

def sun_id():
    
    sunid = str(uuid.uuid4())

    print("Sun's ID is now %s") % sunid

    return sunid

def write_sun(sunid, sun, name, temp, mass, radius, lumi, db):

    sql_query = "INSERT INTO suns (SUN_ID,SUN_CLASS,Sun_Name,Sun_Temp,Sun_Mass,Sun_Radius,Sun_Luminosity) VALUES (%s,%s,%s,%s,%s,%s,%s)" 
    val = sunid, sun, name, temp, mass, radius, lumi
    
    cur.execute(sql_query, val)
    
    db.commit()

    print("New Sun created and stored in Database!")

    return db


collect_suns(cur)

while generator < 2:
    sun = find_sun(comm)
    temp, mass, radius, lumi = create_sun(cur, sun)
    name = sun_name()
    sunid = sun_id()
    write_sun(sunid, sun, name, temp, mass, radius, lumi, db)
    
    generator += 1
    print("Currently %d suns created!" % generator)
    print("Starting new round!")
    print("----------------------")
    print("")

db.close()



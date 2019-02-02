#!/usr/bin/python

# first step: creating n suns

import random, MySQLdb, array, math, uuid, string
from progress.bar import Bar

comm =[[],[]]
suns = [[],[],[],[],[],[],[],[],[],[]]
sunclass = []
temp = []
mass = []
radius = []
lumi = []
name = []
sunid = []
lst = []
agemax = []
age = []

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_parameter():

    sql_query = "SELECT Stars FROM universe"
    cur.execute(sql_query)
    res = cur.fetchone()
    
    n = int(res[0])

    print("Stars to calculate: %d") % n

    return n

def collect_suns():

    sql_query = ("SELECT SUN_CLASS, Temp_min, Temp_max, Mass_min, Mass_max, Radius_min, Radius_max, Luminosity_min, Lumiosity_max, Commonness FROM sun_classes")
    cur.execute(sql_query)
    res = cur.fetchall()

    for row in res:
        
        suns[0].append(row[0])
        suns[1].append(row[1])
        suns[2].append(row[2])
        suns[3].append(row[3])
        suns[4].append(row[4])
        suns[5].append(row[5])
        suns[6].append(row[6])
        suns[7].append(row[7])
        suns[8].append(row[8])
        suns[9].append(int(row[9]*100000))

def find_sun(suncls):

    suncls = random.choice(suncls)

    sunclass.append(str(suncls))

def create_sun():

    i = suns[0].index(sunclass[-1])
    
    # random
    mc = round((random.uniform(suns[3][i],suns[4][i])),2)
    t = random.randint(suns[1][i],suns[2][i])       
        
    # calc
    agemax = math.pow(mc/m0,-2.5)
    age = agemax * random.uniform(0.1,0.9)
    l = math.pow(mc,3.5)    
    
    if mc < 1.66 * m0:
        r = 1.06 * math.pow(mc,0.945)
    else
        r = 1.33 * math.pow(mc,0.555)
    
    temp.append(str(t))
    mass.append(str(mc))
    radius.append(str(r))
    lumi.append(str(l))
    agemax.append(str(agemax)
    age.append(str(age))
    
def sun_age(m0, t0, mc):
    
    tc = math.pow(mc/m0,-2.5) * t0
    tmax = tc / t0
    print(tmax)
    

def sun_name():

    letters = ''.join(random.choice('FXGJAKSDF') for i in range(5))
    digits = random.randint(10000,99999)
    comb = "4FX-%s-%d" % (letters,digits)
        
    name.append(str(comb))

def sun_id():
    
    sunid.append(str(uuid.uuid4()))

def write_sun(lst):

    for i in range(len(sunid)):
        
        lst.append("('"+sunid[i]+"','"+sunclass[i]+"','"+name[i]+"','"+temp[i]+"','"+mass[i]+"','"+radius[i]+"','"+lumi[i]+"')")

    lst = ",".join([str(i) for i in lst])

    sql_query = "INSERT INTO suns (SUN_ID,SUN_CLASS,Sun_Name,Sun_Temp,Sun_Mass,Sun_Radius,Sun_Luminosity) VALUES  %s" % lst
    cur.execute(sql_query)
    
    db.commit()

    print("\nNew suns created and stored in Database! Total: %d") % len(sunid)

    return db


print("Sun generator started...")
print("----------------------")

n = collect_parameter()
collect_suns()

suncls = sum(([t] * w for t, w in zip(suns[0],suns[9])), [])

bar = Bar('Processing', max=n)

for i in range(n):
    
    find_sun(suncls)
    create_sun()
    sun_name()
    sun_id()
    bar.next()

write_sun(lst)

#print("Generating %d suns...") % n

#for i in range(n):
 #   sun = find_sun(comm)
  #  temp, mass, radius, lumi = create_sun(cur, sun)
   # name = sun_name()
    #sunid = sun_id()
    #write_sun(sunid, sun, name, temp, mass, radius, lumi, db)

db.close()



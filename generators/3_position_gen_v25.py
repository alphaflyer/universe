#!/usr/bin/python

# step 3: giving systems positions

import random, MySQLdb, array, math, string, collections
from progress.bar import Bar

systems = []
posx = []
posy = []
posz = []
v = []
lst = []

log =[[],[]]

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_unimax(cur):

    sql_query = "SELECT Size FROM universe"
    cur.execute(sql_query)
    res = cur.fetchone()
    
    print("Size of universe is %s lightyears") % res

    unimax = int(res[0])
    return unimax

def collect_systems(cur):

    sql_query = "SELECT SYSTEM_ID FROM systems"
    cur.execute(sql_query)
    res = cur.fetchall()
    
    for row in res:
        systems.append(row[0])

    print("Collected %d systems from database") % len(systems)


def positions():

    xpos = random.choice(xlg)       #random.randrange((unimax/8),unimax,1) # var pos between center (incl restricted area of 1/8 of size in the center) and max
    alpha = random.randrange(0,360)
    ypos = xpos * math.sin(math.radians(alpha))
    xpos = xpos * math.cos(math.radians(alpha))

    v = math.hypot(xpos, ypos)
    zmax = unimax / 10

    zpos = random.randrange(-zmax, zmax)
    zpos = zpos * min(1,(300 / v))   # tricky: to get a nice shape, i need to var the z position to the distance from center (det. by vector v)

    posx.append(str(xpos))
    posy.append(str(ypos))
    posz.append(str(zpos))

def write_sys(lst):

    x = 0   

    print("\nAttaching coordinates...")

    for arrcount in range(len(posx)):
        
        lst.append("('"+systems[x]+"','"+posx[x]+"','"+posy[x]+"','"+posz[x]+"')")
        x = x + 1

    lst = ",".join([str(x) for x in lst])

    sql_query = "INSERT INTO system_positions (SYSTEM_ID,Position_X,Position_Y,Position_Z) VALUES  %s" % lst
    cur.execute(sql_query)
    
    db.commit()

    print("New positions created and stored in Database! Total: %d") % len(systems)

    return db

def xlog():
    n = 1
    for i in range(unimax):         # builds array with nearest pos to center is often and farest item is rare
        n += 1
        log[0].append(unimax - i)
        #log[1].append(int(math.log10(n)))
        log[1].append(n)

    xlg = sum(([t] * w for t, w in zip(*log)), [])  # list for random choice with multi elemts for near and less elemtents for far
    return xlg


print("System generator v2.5 started...")
print("----------------------")

unimax = collect_unimax(cur)
collect_systems(cur)
xlg = xlog()

bar = Bar('Processing', max=len(systems))

for i in range(len(systems)):

    positions()
              
    bar.next() 

write_sys(lst)

db.close()





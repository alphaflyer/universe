#!/usr/bin/python

# step 3: giving systems positions

import random, MySQLdb, array, math, string, collections

systems = []
posx = []
posy = []
posz = []
lst = []

log =[[],[]]

unimax = 1000   #lightyears diameter - more or less logic - should be fetched from DB 

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_systems(cur):

    sql_query = "SELECT SYSTEM_ID FROM systems"
    cur.execute(sql_query)
    res = cur.fetchall()
    
    for row in res:
        systems.append(row[0])

    print("Collected %d systems from database") % len(systems)

    return systems



def positions():

    xpos = random.choice(xlg)       #random.randrange((unimax/8),unimax,1) # var pos between center (incl restricted area of 1/8 of size in the center) and max
    alpha = random.randrange(0,360)
    ypos = xpos * math.sin(alpha)
    xpos = xpos * math.cos(alpha)

    zpos = random.choice(xlg) / 10
    

    #if xpos < (unimax*7/8) or ypos < (unimax*7/8):

        #zpos = random.randrange((unimax*-1)/10,unimax/10, 1)
    
    #else:

        #zpos = (xpos - unimax*7/8) * math.cos(random.randrange(0,360))

    posx.append(str(xpos))
    posy.append(str(ypos))
    posz.append(str(zpos))

        
def array():

    for x in range(len(systems)):
        positions() 


def random_pos():

    while len(arrx) > 0:

        keyx = random.randrange(0,len(arrx))
        posx.append(str(arrx[keyx]))
        arrx.remove(arrx[keyx])
        

        keyy = random.randrange(0,len(arry))
        posy.append(str(arry[keyy]))
        arry.remove(arry[keyy])

        keyz = random.randrange(0,len(arrz))
        posz.append(str(arrz[keyz]))
        arrz.remove(arrz[keyz])
    
    print "Coordinates generated."

    return posx, posy, posz
          

def sys_array():

    print("Building arrays...")

    for arrcount in range(rowcount):

        
        def system_name():

            letters = ''.join(random.choice('ASDFLOM') for i in range(5))
            digits = random.randint(10000,99999)
            name = "SYS-%s-%d" % (letters,digits)
        
            print("System's name is now %s") % name

            return name
        
        def system_id():
    
            sysid = str(uuid.uuid4())

            print("Sys's ID is now %s") % sysid

            return sysid

        name = system_name()
        sysid = system_id()
        
        namearray.append(name)
        sysarray.append(sysid)        
  
    print("Finished. Added %d Systems to sysarray and %d Names to namearray.") % (len(sysarray), len(namearray))
    
    return namearray, sysarray


def write_sys(posx, posy, posz, lst):

    x = 0   

    print("Attaching coordinates...")

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


def xposnew(xlg):

    xpos.append(random.choice(xlg))

    return xpos



    




print("System generator started...")
print("----------------------")

collect_systems(cur)
xlg = xlog()
array()
    #random_pos()
#write_sys(posx, posy, posz, lst)

db.close()
#xpos = []

#for i in range(unimax):
#    xpos = xposnew(xlg)
#xpos.sort(reverse = True)
#for i in range(0, unimax, 1):
#    print("%d,%d") % (i, xpos.count(i))




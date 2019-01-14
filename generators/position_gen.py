#!/usr/bin/python

import random, MySQLdb, array, math, uuid, string

systems = []
arrx = []
arry = []
arrz = []
posx = []
posy = []
posz = []
lst = []

rowcount = 500 # rows to fetch

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_systems(cur):

    sql_query = ("SELECT SYSTEM_ID FROM systems LIMIT %d") % rowcount
    cur.execute(sql_query)
    res = cur.fetchall()

    
    for row in res:
        systems.append(row[0])

    print("Collected %d systems from database") % len(systems)

   
    return systems

def positions():

    i = rowcount/2*-1

    for x in range((1+i*2)*-1):
                
        arrx.append(i)
        arry.append(i)
        arrz.append(i)
        i += 1

    

    #for x in range(center)

     #   arrx.remove(i)

    print arrx
    print len(arrx)

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

    print("New systems created and stored in Database! Total: %d") % rowcount

    return db


print("System generator started...")
print("----------------------")

collect_systems(cur)
positions()
random_pos()
write_sys(posx, posy, posz, lst)

db.close()



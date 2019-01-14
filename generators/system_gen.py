#!/usr/bin/python

import random, MySQLdb, array, uuid, string

suns = []
sysarray = []
namearray = []
lst = []

rowcount = 10000 # rows to fetch

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_suns(cur):

    sql_query = ("SELECT SUN_ID FROM suns LIMIT %d") % rowcount
    cur.execute(sql_query)
    res = cur.fetchall()

    
    for row in res:
        suns.append(row[0])

    print("Collected %d suns from database") % len(suns)

   
    return suns

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


def write_sys(sysarray, suns, namearray, lst):

    x = 0   

    print("Binding sun to system...")

    for arrcount in range(rowcount):
        lst.append("('"+sysarray[x]+"','"+suns[x]+"','"+namearray[x]+"')")
        x = x+1

    lst = ",".join([str(x) for x in lst])

    sql_query = "INSERT INTO systems (SYSTEM_ID,SUN_ID,System_Name) VALUES  %s" % lst

    cur.execute(sql_query)
    
    db.commit()

    print("New systems created and stored in Database! Total: %d") % rowcount

    return db


print("System generator started...")
print("----------------------")
collect_suns(cur)
sys_array()
write_sys(sysarray, suns, namearray, lst)

db.close()



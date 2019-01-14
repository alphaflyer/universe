#!/usr/bin/python

# step 2: creating systems with suns

import random, MySQLdb, array, uuid, string
from progress.bar import Bar

suns = []
sysarray = []
namearray = []
lst = []

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_suns(cur):

    sql_query = ("SELECT SUN_ID FROM suns")
    cur.execute(sql_query)
    res = cur.fetchall()
    
    for row in res:
        suns.append(row[0])

    print("Collected %d suns from database") % len(suns)

def system_id():
    
    sysid = str(uuid.uuid4())

    sysarray.append(sysid) 

    #print("Sys's ID is now %s") % sysid

def system_name():

    letters = ''.join(random.choice('ASDFLOM') for i in range(5))
    digits = random.randint(10000,99999)
    name = "SYS-%s-%d" % (letters,digits)

    namearray.append(name)
        
    #print("System's name is now %s") % name   
   
def sys_array():
    
    
  
    print("Finished. Added %d Systems to sysarray and %d Names to namearray.") % (len(sysarray), len(namearray))
    
    return namearray, sysarray


def write_sys(lst):

    for i in range(len(namearray)):

        lst.append("('"+sysarray[i]+"','"+suns[i]+"','"+namearray[i]+"')")

    lst = ",".join([str(i) for i in lst])

    sql_query = "INSERT INTO systems (SYSTEM_ID,SUN_ID,System_Name) VALUES  %s" % lst

    cur.execute(sql_query)
    
    db.commit()

    print("\nNew systems created and stored in Database! Total: %d") % len(namearray)

    return db


print("System generator started...")
print("----------------------")
collect_suns(cur)

bar = Bar('Processing', max=len(suns))

for i in range(len(suns)):

    system_name()
    system_id()
              
    bar.next() 

write_sys(lst)

db.close()



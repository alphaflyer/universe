#!/usr/bin/python

import MySQLdb, math

systems = [[],[],[],[]]
lst = []
i = 0
unimax = 0

db = MySQLdb.connect('localhost','universe','F7V4Z50Jl7HyKFI8','universe')
cur = db.cursor()

def collect_unimax(cur):

    sql_query = ("SELECT Size FROM universe")
    
    cur.execute(sql_query)
    res = cur.fetchone()

    unimax = float(res[0])

    return unimax

def collect_suns(cur):

    sql_query = ("SELECT SYSTEM_ID, Position_X, Position_Y, Position_Z FROM system_positions")# LIMIT 30")

    cur.execute(sql_query)
    res = cur.fetchall()
    
    for row in res:
        systems[0].append(row[0])
        systems[1].append(row[1])
        systems[2].append(row[2])
        systems[3].append(row[3])

    return systems

def vector3(i):
    
    vector = math.sqrt(systems[1][i]**2 + systems[2][i]**2 + (systems[3][i])**2)
    
    return vector

def angle_speed(unimax, vector):

    velocity = unimax / vector # degrees

    return velocity
        
def pos_x(i, vector, velocity):

    x = systems[1][i] * math.cos(math.radians(velocity)) - systems[2][i] * math.sin(math.radians(velocity))
  
    return x

def pos_y(i, vector, velocity):

    y = systems[1][i] * math.sin(math.radians(velocity)) + systems[2][i] * math.cos(math.radians(velocity))

    return y

def pos_array(i, x, y):

    lst.append("('"+systems[0][i]+"','"+str(x) +"','"+ str(y) +"','"+str(systems[3][i])+"')")

    return lst

def iteration(i):

        for i in range(len(systems[0])): 

                #print("System-ID: %s") % systems[0][i] 
                vector = vector3(i)
                #print("  Vector is %s") % vector
                velocity = angle_speed(unimax, vector)
                #print ("  Velocity is %s") % velocity
                x = pos_x(i, vector, velocity)
                y = pos_y(i, vector, velocity)
                #print("  Coordinates where x = %s y = %s z = %s") % (systems[1][i], systems[2][i], systems[3][i])
                #print("  Coordinates are now x = %s y = %s z = %s") % (x, y, systems[3][i])
                lst = pos_array(i, x, y)
                #print("----------------------")
        
        write_sys(cur, lst)
        print("New Coordinates stored in Database! Total: %d") % len(systems[0])
        print("----------------------")
    
def write_sys(cur, lst):

    x = 0   

    #print("Importing new Coordinates...")

    lst = ",".join([str(x) for x in lst])

    sql_query = "INSERT INTO system_positions(SYSTEM_ID,Position_X,Position_Y,Position_Z) VALUES %s ON DUPLICATE KEY UPDATE Position_X = VALUES(Position_X), Position_Y = VALUES(Position_Y), Position_Z = VALUES(Position_Z)" % lst
    cur.execute(sql_query)
    
    db.commit()
    db.close()

    return db

def timed():

        for a in range(20):
                unimax = collect_unimax(cur)
                print "Univerum size: %s" % unimax
                collect_suns(cur)
                print("Collected %d systems from database") % len(systems[0])
                print("----------------------") 
                iteration(i)
                print systems 
                          
                time.sleep(2)

unimax = collect_unimax(cur)
print "Univerum size: %s" % unimax
collect_suns(cur)
print("Collected %d systems from database") % len(systems[0])
print("Calculating...")
#print("----------------------") 
iteration(i)




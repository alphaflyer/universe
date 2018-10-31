import time, threading, MySQLdb

# Startbudget
metal = 100
lithium = 100
chrystal = 100
neon = 100

# Multiplikator
m_metal = 2
m_lithium = 1
m_chrystal = 0.5
m_neon = 0.1

# Timer
t = 2  #sec


def res():
    
    global metal
    global lithium
    global chrystal
    global neon
    global m_metal
    global m_lithium
    global m_chrystal
    global m_neon
    global t

    metal = metal + m_metal
    lithium = lithium + m_lithium
    chrystal = chrystal + m_chrystal
    neon = neon + m_neon
        
    print("---------------------------")
    print("Metal is at %d units" % (metal))
    print("Lithium is at %d units" % (lithium))
    print("Chrystal is at %d units" % (chrystal))
    print("Neon is at %d units" % (neon))
    print("---------------------------")
    
    db = MySQLdb.connect('localhost','res','1234','universe')

    cur = db.cursor()

    sql_query = ("UPDATE planets SET Metal=%d,Lithium=%d,Chrystal=%d,Neon=%d WHERE ID=1" % (metal,lithium,chrystal,neon))
    cur.execute(sql_query)
    db.commit()

    db.close()    
    
    threading.Timer(t, res).start()

res()


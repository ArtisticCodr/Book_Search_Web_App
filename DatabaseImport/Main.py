from prefix import Prefix
import mysql.connector


def ucitajKnjige(prefixi, mydb):
    dbcursor = mydb.cursor()
    
    print('--------------------------------------------------')
    f = open('d:/fajlovi/knjige.txt', 'r', encoding="utf8")
    line = f.readline()
    line = line[1:]
    
    knjiga = 0
    while line and (knjiga < 100):
        polja = line.split(chr(30))
        
        for polje in polja:
            indikator = polje[3:5]
            indikator = indikator.replace(' ', '#')
            pd = ""
            oz = False
            for c in polje[5:]:
                if oz is True:
                    pd += c
                    pd += ']'
                    oz = False
                    continue
                
                if c == chr(31):
                    pd += '['
                    oz = True
                    continue
                pd += c
              
            podpolja = polje.split(chr(31))
            podpolja = podpolja[1:]
            polje = polje[:3]
            
            sql = "INSERT INTO PoljaData (polje, knjiga, podpolja, indikator) VALUES (%s, %s, %s, %s)"
            val = (polje, knjiga, pd, indikator)
            dbcursor.execute(sql, val)
            mydb.commit()
            # print(polje, indikator, pd)
            
            for podpolje in podpolja:
                value = podpolje[1:]
                podpolje = podpolje[:1]
                if value != "" and not(value.isspace()):
                    for prefix in prefixi:
                        if prefix.polje == polje and prefix.podpolje == podpolje:
                            sql = "INSERT INTO KnjigeData (knjiga, prefix, labela, polje, podpolje, value) VALUES (%s, %s, %s, %s, %s, %s)"
                            val = (knjiga, prefix.oznaka, prefix.labela, prefix.polje, prefix.podpolje, value)
                            dbcursor.execute(sql, val)
                            mydb.commit()
        
        line = f.readline()
        
        print("inserted knjiga: ", knjiga)
        knjiga += 1
    f.close()


"""
kreiramo objekte za svaki prefix
kako bi kasnije knjige mapirali po datim prefixima
"""


def getPrefixObj(mydb):
    dbcursor = mydb.cursor()
    f = open('d:/fajlovi/prefiksi.txt', 'r')
    line = f.readline()
    prefixi = []
    
    while line:
        # iskljucujemo komentare
        if not(len(line) > 8 or len(line) < 7): 
            p = Prefix()
            p.oznaka = line[:2]
            p.polje = line[3:6]
            p.podpolje = line[6:7]
            prefixi.append(p)
        line = f.readline()

    f.close()
    
    f = open('d:/fajlovi/PrefixNames_sr.properties', 'r', encoding="utf8")
    line = f.readline()
    line = line[1:]
    
    while line:
        oznaka = line[:2]
        labela = line[3:-1]
                
        for p in prefixi:
            if p.oznaka == oznaka:
                p.labela = labela
        line = f.readline()
    
    for p in prefixi:
        if(p.labela == ''):
            p.labela = p.oznaka
        
        sql = "INSERT INTO prefixi (prefix, label) VALUES (%s, %s)"
        val = (p.oznaka, p.labela)
        dbcursor.execute(sql, val)
        mydb.commit()
    
    '''
    for p in prefixi:
        print(p.oznaka, p.polje, p.podpolje, p.labela)
    '''
    
    return prefixi


mydb = mysql.connector.connect(
    # host="hadziserver.ddns.net",
    host="192.168.0.111",
    user="admin",
    passwd="sljiva3388",
    database="SkriptBiblioteka"
)
ucitajKnjige(getPrefixObj(mydb), mydb)
print('finished!')

import sqlite3 as sql
from sqlobject import *

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    
    # The database file must exists prior to using it with SqlObject...
    # the sqlite.connect function does not the directory hierarchy...
    
    #path = """C:/Documents%20and%20Settings/Jean-Lou%20Dupont/mm/db/maps.sqlite"""
    
    #fspath = "c:/test.sqlite"
    #path = '///c|test.sqlite'
  
    fspath = "C:\Documents and Settings\Jean-Lou Dupont\mm\db\maps.sqlite"
    path   = "C|/Documents and Settings/Jean-Lou Dupont/mm/db/maps.sqlite"
  
    # WINDOWS
    #   //  /C|  path
    #   
    # LINUX
    #   // path
    #   e.g. // /home/root/...
    
    db=sql.connect(fspath)
    db.close()
    
    sqlhub.processConnection = connectionForURI('sqlite://%s' % path)
 
    print 'start:'
 
    class Test(SQLObject):
        fname = StringCol()
        
        
    Test.createTable(ifNotExists=True)
    
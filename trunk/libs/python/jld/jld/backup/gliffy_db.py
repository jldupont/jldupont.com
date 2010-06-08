""" Gliffy DB class
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: gliffy_db.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

import os
import datetime

from sqlobject import *
import sqlite3 as sql

import jld.api as api
import jld.tools.db as db

# =============================================
class Diagrams(SQLObject):
    """ Gliffy diagrams database table
    """
    # diagram id
    did      = StringCol()
    # etag - serves as revision id
    etag     = StringCol()
    #datetime timestamp at which the entry was added to the database
    added    = DateTimeCol()
    #datetime timestamp at which the entry was exported to the filesystem
    exported = DateTimeCol()
    
    _attributesToVerify = ['did', 'exported', 'etag']

    @classmethod
    def getToExportList(cls):
        """ Returns a list of entries that aren't exported yet
        """
        # the whole list must be returned
        return cls.select( ) #cls.q.exported == None )
    
    @classmethod
    def getAll(cls):
        """ Returns all the entries
            @return: SQLObject list 
        """
        list = []
        all = cls.select(orderBy=DESC(cls.q.added))
        try:           
            for one in all:
                entry = cls._formatOne( one )
                list.append( entry )
        except:
            pass # no elements
                    
        return list
    
    @classmethod
    def _formatOne(cls, entry):
        """ Format an SQLObject result object (entry)
            to a dictionary object.
        """
        result = {}
        result['did']      = entry.did
        result['added']    = entry.added
        result['etag']     = entry.etag
        result['exported'] = entry.exported
        return result
    
    @classmethod
    def updateFromList(cls, list):
        """ Updates the database from the specified list.
            Used solely during the ''import'' command.
            
            @param list: the list of ids
            @return: tuple( total, updated, created )
        """
        total = len(list);
        updated = 0;  
        created = 0;
        for did in list:
            dgs = cls.select( cls.q.did == did )
            #post already exists?
            try:    diagram = dgs[0]
            except: diagram = None
                
            if (diagram is None):
                entry = {'did':did}
                created = created + 1
                cls._createOne( entry )
            
            #can't really update for now
            #else:
            #    if (cls._updateOne(entry, diagram)):
            #        updated = updated  + 1
                    
        return (total, updated, created)

    @classmethod
    def _createOne(cls, entry):
        """ Creates one post entry
        """
        Diagrams(did=entry['did'], 
                 exported=None,
                 etag=None,
                 added=datetime.datetime.now() )
        
    @classmethod
    def _updateOne(cls, entry, diagram):
        """Processes one entry: verifies if the entry needs updating
            @param entry: the entry from Client API
            @param diagram:  the sqlobject
            
            @return: True if the entry needed updating
        """
        needsUpdate = False
        for att in cls._attributesToVerify:
            local  = getattr(diagram, att)
            remote = entry[att]
            #print "att[%s] local[%s] remote[%s]" % (att, local, remote)
            if (local != remote):
                needsUpdate = True
                break
            
        if (needsUpdate):
            diagram.set( did=entry['did'],
                         etag=entry['etag'],
                         added=entry['added'],
                         exported=entry['exported'] )           
                
        return needsUpdate
            
# ==============================================        

class Db(db.BaseSQLObjectDb2):
    def __init__(self, filepath):
        db.BaseSQLObjectDb2.__init__(self, filepath)  
    def initTable(self, connection):
        Diagrams._connection = connection
        Diagrams.createTable(ifNotExists=True)


# ==============================================
# ==============================================


if __name__ == "__main__":
    """ Tests
    """
    db = Db(':memory:')

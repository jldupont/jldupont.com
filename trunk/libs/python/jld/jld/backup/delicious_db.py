""" Delicious DB class
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: mindmeister_db.py 708 2008-12-04 13:40:35Z JeanLou.Dupont $"

import os
import datetime

from sqlobject import *
import sqlite3 as sql

import jld.api as api
import jld.tools.mos as mos
import jld.tools.db as db

# =============================================

class Posts(SQLObject):
    """ Delicious Posts database
    """
    # Delicious attributes
    #_posts_fields = ['href', 'hash', 'description', 'tag', 'time', 'extended']
    href         = StringCol()
    hash         = StringCol()
    description  = UnicodeCol()
    time         = StringCol() #don't want to have conversion here...
    tag          = StringCol()
    tag1         = StringCol()
    tag2         = StringCol()
    tag3         = StringCol()
    # internal attribute
    changed      = DateTimeCol() #timestamp of last detected modification
    
    _attributesToVerify = ['href', 'hash', 'description','time', 'tag']

    @classmethod
    def getChangedList(cls, dt):
        """ Returns a list of entries changed since 'dt'
            @param dt: the datetime timestamp to compare against
        """
        return cls.select(OR(cls.q.changed > dt, cls.q.changed == None))
    
    @classmethod
    def getAll(cls, tag = None):
        """ Returns all the entries
            @param tag: filters by tag
            @return: SQLObject list 
        """
        list = []
        if (tag):
            all = cls.select(OR( Posts.q.tag1 == tag, Posts.q.tag2 == tag, Posts.q.tag3 == tag ), 
                             orderBy=DESC(Posts.q.changed))
        else:
            all = cls.select(orderBy=DESC(Posts.q.changed))
        
        try:   
            for one in all:
                entry = cls._formatOne( one )
                list.append( entry )
        except:
            pass
        return list
    
    @classmethod
    def _formatOne(cls, entry):
        """ Format an SQLObject result object (entry)
            to a dictionary object.
        """
        result = {}
        result['href'] = entry.href
        result['hash'] = entry.hash
        result['description'] = entry.description
        result['time'] = entry.time
        result['tag'] = entry.tag
        result['tag1'] = entry.tag1
        result['tag2'] = entry.tag2
        result['tag3'] = entry.tag3
        result['changed'] = entry.changed
        return result
    
    @classmethod
    def updateFromList(cls, list):
        """ Updates the database from the specified list
            @param list: the list of dict entries
            @return: tuple( total, updated, created )
        """
        total = len(list);
        updated = 0;  
        created = 0;
        for entry in list:
            hash = entry['hash']
            posts = cls.select( cls.q.hash == hash )
            
            #post already exists?
            try:    post = posts[0]
            except: post = None
                
            if (post is None):
                created = created + 1
                cls._createOne( entry )
            else:
                if (cls._updateOne(entry, post)):
                    updated = updated  + 1
                    
        return (total, updated, created)

    @classmethod
    def _extractTags(cls, list):
        """ Extracts the first 3 tags from a list
            @param list: string list
            @return: tuple of 3 items
        """
        if (not list):
            return (None, None, None)
        bits  = list.split(' ',3)
        sbits = bits[0:3]
        cbits = ['' for i in range(0,3-len(sbits))]
        if (cbits):
            sbits.extend(cbits)
        return sbits
        
    @classmethod
    def _createOne(cls, entry):
        """ Creates one post entry
        """
        tag1,tag2,tag3 = cls._extractTags( entry['tag'] )
        Posts(   hash=entry['hash'], 
                 href=entry['href'],
                 description=entry['description'], 
                 tag=entry['tag'],
                 tag1=tag1,
                 tag2=tag2,
                 tag3=tag3,
                 time=entry['time'],
                 changed=datetime.datetime.now() )
        
    @classmethod
    def _updateOne(cls, entry, post):
        """Processes one entry: verifies if the entry needs updating
            @param entry: the entry from Delicious Client API
            @param post:  the sqlobject
            
            @return: True if the entry needed updating
        """
        needsUpdate = False
        for att in cls._attributesToVerify:
            local  = getattr(post, att)
            remote = entry[att]
            #print "att[%s] local[%s] remote[%s]" % (att, local, remote)
            if (local != remote):
                needsUpdate = True
                break
            
        if (needsUpdate):
            tag1, tag2, tag3 = cls._extractTags( entry['tag'] )
            post.set( href=entry['href'],
                     hash=entry['hash'], 
                     tag=entry['tag'],
                     tag1=tag1,
                     tag2=tag2,
                     tag3=tag3,                     
                     description=entry['description'],
                     time=entry['time'],
                     changed=datetime.datetime.now() )           
                
        return needsUpdate
            
# ==============================================
class Updates(SQLObject):
    """ Table for keeping track of the I{last update} indicator from Delicious """

    username = StringCol()
    last     = StringCol()

    @classmethod
    def update(cls, username, last):
        """ Creates an entry
        """
        c = cls.select( cls.q.username == username )
        try: current = c[0]
        except: current = None
        if (current):
            current.set(username = username, last=last)
            return
        else:
            Updates( username=username, last=last )

    @classmethod
    def getLatest(cls, username):
        result = cls.select( cls.q.username == username )
        try:
            entry = result[0]
        except:
            entry = None
        
        if entry:
            return entry.last
        
        return None

# ==============================================        

class Db(db.BaseSQLObjectDb):
    """ Db class; used to bootstrap SQLObject 
    """
    def __init__(self, filepath):
        db.BaseSQLObjectDb.__init__(self, filepath)
        
    def initTable(self):
        #table already exists ... no big deal
        Posts.createTable(ifNotExists=True)
        Updates.createTable(ifNotExists=True)
    
# ==============================================
# ==============================================


if __name__ == "__main__":
    """ Tests
    """
    db = Db(':memory:')

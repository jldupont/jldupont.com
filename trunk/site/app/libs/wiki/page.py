"""
    Page
    
    @author: Jean-Lou Dupont
    
"""

__all__     = ['Page']
__version__ = "$Id: page.py 662 2008-11-26 02:34:18Z JeanLou.Dupont $"
__author__  = "Jean-Lou Dupont"

import datetime
import os
import sys
import logging
import pickle
import uuid

from google.appengine.api import datastore
from google.appengine.api import datastore_types
from google.appengine.api import memcache

import libs.wiki.title

class Page(object):
    """Abstraction for a Wiki page.
    """
    def __init__(self, title, entity=None):
        self.title = title
        self.entity = entity
        
        #Existing page
        if entity:
            self.content = entity['content']
            self.created = entity['created']
            self.modified = entity['modified']
            try:    self.cache_control = entity['cache_control']
            except: self.cache_control = 0
            try:    self.etag = entity['etag']
            except: self.etag = str( uuid.uuid1() )
                
        #new one
        else:
            now = datetime.datetime.now()
            self.content = None
            self.created = now
            self.modified = now
            self.cache_control = 0
            self.etag = str( uuid.uuid1() )

    def entity(self):
        return self.entity

    def key(self):
        if (self.entity is not None):
            return self.entity.key()
        return None
        
    def save(self, cacheOnly = False, timeout = 600 ):
        """Creates or edits this page in the datastore."""
        now = datetime.datetime.now()
        if self.entity is not None:
            entity = self.entity
        else:
            self.entity = datastore.Entity('Page')
            self.entity['created'] = now
                        
        self.entity['title'] = str( self.title )
        
        #make sure we are unicode-safe 
        uStr = unicode( self.content, "latin-1" )
        
        self.entity['content'] = datastore_types.Text( uStr )    
        self.entity['modified'] = now
        self.entity['etag'] = self.etag
        
        self.saveInCache( timeout )
        
        if (not cacheOnly):
            logging.info("Page.save: saving in datastore");
            return datastore.Put(self.entity)

    def saveInCache(self, timeout = 600):
        pickled = pickle.dumps(self.entity)
        result = memcache.set('ds/page/'+str( self.title ), pickled, timeout )
        logging.info( "Page.saveInCache: title: [%s] result[%i]" % ( str(self.title), result ) )

    @staticmethod
    def load(title, cacheOnly = False, cacheAlso = True):
        """Loads the page with the given title.
        """
        #logging.info("Page.load: retrieving page[%s]" % str(title) )
        page = Page.getFromCache(title)
        if (page is not None):
            #logging.debug("Page.load: page[%s] from CACHE" % str(title) )
            return page

        if (not cacheOnly):
            p = Page.getFromStore(title, cacheAlso)
            #if (p is not None):
            #    logging.debug("Page.load: page[%s] from DATASTORE" % str(title) )
            return p
        
        logging.warn("Page.load: page[%s] NOT FOUND" % str(title) )
        return None

    def valid(self):
        return self.entity != None

    @staticmethod
    def exists(title):
        """Returns true if the page with the given title exists in the datastore."""
        return Page.load(title).entity != None

    @staticmethod
    def getFromCache(title):
        pickled = memcache.get('ds/page/' + str( title ) )
        if (pickled is not None):
            entity = pickle.loads(pickled)
            if (entity is not None):
                #logging.info("Page.getFromCache: loaded from memcache & unpickled page [%s]" % str( title ) )
                return Page(title, entity)
        return None
    
    @staticmethod
    def getFromStore(title, cacheAlso = True):
        query = datastore.Query('Page')
        query['title ='] = str( title )
        entities = query.Get(1)
        
        if len(entities) < 1:
            return Page(title)

        #logging.info("Page.getFromStore: loaded page [%s]" % str( title ) )
        page = Page(title, entities[0])
        
        if (cacheAlso):
            page.saveInCache()
        
        return page
        
    @staticmethod
    def deleteFromCache(title):
        """ 
        @param title Title instance
        @return result bool    
        """
        return memcache.delete('ds/page/' + str(title) ) == 2

    @staticmethod
    def isCached(title):
        pickled = memcache.get('ds/page/' + str( title ) )
        return pickled is not None
    
    @staticmethod
    def isStored(title):
        query = datastore.Query('Page')
        query['title ='] = str( title )
        entities = query.Get(1)
        
        return len(entities) > 0
    
        
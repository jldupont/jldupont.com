#!/usr/bin/env python
""" Delicious API
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: delicious.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"
__all__ = ['Client']

import os
import sys
import base64
import time
import urllib2

# ASSUME THAT THE REQUIRED LIBS are available
# RELATIVE to this script => simplified install
levelsUp = 3
path = os.path.abspath( __file__ )
while levelsUp>0:
    path = os.path.dirname( path )
    levelsUp = levelsUp - 1    
sys.path.append( path )

import jld.api as api
import jld.api.delicious_objects as delObjects


# =================================================================            
# ================================================================= 


class Waiter(object):
    """ Serves as throttle to play nice with the terms of use.
        This class does not need to be directly manipulated by
        a client of this library.
    """
    def __init__(self):
        self.lastAccess = None
        
    def wait(self, secondsToWait = 2):
        #first time around... don't need to way :-)
        if (self.lastAccess is None):
            self.lastAccess = time.time()
            return
        
        now = time.time()
        diff = now - self.lastAccess + 1
        
        if (diff < secondsToWait):
            time.sleep( diff )
            
        self.lastAccess = time.time()
            
# =================================================================            
# ================================================================= 
        
        
class Api(object):
    """ Low-level API to Delicious
    """
    _API = 'https://api.del.icio.us/v1/%s'
    
    def __init__(self, username, password):
        """
        """
        self.waiter = Waiter()
        self.username = username
        self.password = password
        self.auth = "Basic %s" % ( base64.encodestring("%s:%s" % (self.username, self.password)).strip() )
        
    def do_method(self, query, **args):
        """ Generic method handler.
            @param query: the query method string
            @param args: the argument list 
        """
        param = api.versaUrlEncode( args, True )
        frag = '?' + param if param else ''
        url = self._API % query + frag
        return self._get(url)
        
    def _get(self, url):
        """ All GET operations go through here
        """
        # respect terms of use i.e. throttle
        self.waiter.wait()
        
        try:
            req = urllib2.Request(url)
            req.add_header('Authorization', self.auth)
            response = urllib2.urlopen(req)
        except Exception,e:
            raise api.ErrorNetwork(e, {'code':e.code})
        
        data = response.read()
        response.close()
        return data


# =================================================================            
# ================================================================= 



class Client(object):
    """ Higher level interface to Delicious
    """
    def __init__(self, username, password):
        self.api = Api(username, password)

    def getLastUpdate(self):
        """ Retrieves the time of the last update
            Method: https://api.del.icio.us/v1/posts/update
            @return: an B{Update} object
        """
        raw = self.api.do_method('posts/update')
        return delObjects.Update( raw )        

    def getAllBundles(self):
        """ Retrieves all bundles
            Method: https://api.del.icio.us/v1/tags/bundles/all
            @return: a list of B{Bundle} objects
        """
        raw = self.api.do_method('tags/bundles/all')
        return delObjects.Bundles(raw)
        
    def getBundle(self, name):
        """ Returns a specific bundle
            Method: https://api.del.icio.us/v1/tags/bundles/all?bundle=NAME
            @param name: the input bundle name 
            @return: a B{Bundle} object
        """
        raw = self.api.do_method('tags/bundles/all', bundle=name)
        return delObjects.Bundle(raw)
        
    def getAllTags(self):
        """ Retrieves all tags
            Method: https://api.del.icio.us/v1/tags/get
            @return: list of B{Tags} objects
        """
        raw = self.api.do_method('tags/get')
        return delObjects.Tags(raw)

    def getPosts(self, tag = None, url = None, hashes = None, dt = None, meta = 'yes'):
        """ Retrieves a specific post
            Method: https://api.del.icio.us/v1/posts/get
            @param tag: optional tag (serves as filter)
            @param url: optional url (serves as filter)
            @param hashes: optional hash list (filter)
            @param dt: optional date (filter)
            @param meta: optional meta parameter    
            @return: a B{Post} object
        """
        raw = self.api.do_method('posts/get', tag=tag, url=url, hashes=hashes, dt=dt, meta=meta )
        return delObjects.Posts(raw)

    def getPostsAll(self, tag = None, start = None, results = None, fromdt = None, todt = None, meta = 'yes'):
        """ Retrieves a list of posts
            Method: https://api.del.icio.us/v1/posts/get
            @param tag: optional tag (serves as filter)
            @param start: optional start (serves as filter)
            @param results: optional results (serves as filter)
            @param fromdt: optional ''from date'' list (filter)
            @param todt: optional ''to date'' list (filter)
            @param meta: optional meta parameter    
            @return: a B{Post} object
        """
        raw = self.api.do_method('posts/all', tag=tag, start=start, results=results, fromdt=fromdt, todt=todt, meta=meta )
        return delObjects.Posts(raw)

    def getRecentPosts(self, tag = None, count = 100):
        """ Retrieves the recent posts up to COUNT
            and filtered by TAG.
            Method: https://api.del.icio.us/v1/posts/recent
            @param tag: optional filter by tag
            @param count: optional limit count
            @return: list of B{Post} objects  
        """
        if (tag):
            raw = self.api.do_method('posts/recent', tag=tag, count=count)
        else:
            raw = self.api.do_method('posts/recent', count=count)
            
        return delObjects.Posts(raw)
        
    def getAllHashes(self):
        """ Retrieves the list of all hashes
            Method: https://api.del.icio.us/v1/posts/all?hashes
            @return: list of B{Hashes} objects
        """
        raw = self.api.do_method('posts/all?hashes')
        return delObjects.Hashes(raw)
    
# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import os
    username = os.environ['DELICIOUS_USERNAME']
    password = os.environ['DELICIOUS_PASSWORD']
    c = Client(username, password)

    u = c.getLastUpdate()
    print u
    
    b = c.getBundle('my-stuff')
    print b
    
    bs = c.getAllBundles()
    print bs
    
    ts = c.getAllTags()
    print ts
    
    ps = c.getRecentPosts(count=2)
    print ps
    
    ps = c.getRecentPosts(tag='php')
    print ps
    
    ps = c.getPosts(tag='php')
    print ps
    
    #possibly long list!
    #hs = c.getAllHashes()
    #print hs
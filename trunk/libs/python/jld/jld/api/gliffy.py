#!/usr/bin/env python
""" Gliffy API
    @author: Jean-Lou Dupont
    
    >>> c = Client()
    >>> result, etag = c.head(1472486)
    >>> print result, etag # doctest:+ELLIPSIS
    True...
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: gliffy.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

import re
import httplib
import jld.api as api

# Patterns for extracting the diagram ID
# ======================================
_rawpatterns = [
             '/publish/(\d+)/',
             '/pubdoc/(\d+)/',
             '/publish/(\d+)$',
             '/pubdoc/(\d+)$',
             ]
_patterns = []
for p in _rawpatterns:
    _patterns.append( re.compile( p ) )

def extractIdFromURI(uri):
    """ Extracts the diagram ID from a URI
    
        >>> tests = [ \
        'http://www.gliffy.com/publish/1553333/', \
        'http://www.gliffy.com/pubdoc/1554222/L.jpg',]
        >>> for t in tests: \
        print extractIdFromURI( t )
        1553333
        1554222
        
        @param uri: the uri
        @return: the diagram id        
    """
    for p in _patterns:
        g = p.search( uri )
        try:    id = g.group(1)
        except: id = None
        if (id is not None):
            return id
    return None

def extractIdFromListOfURI(list):
    """ Extracts the diagram ids from a list of URI
    
    >>> list = ['http://www.gliffy.com/pubdoc/123/L.jpg','http://www.gliffy.com/pubdoc/456/L.jpg']
    >>> result = extractIdFromListOfURI(list)
    >>> print result
    ['123', '456']
    >>> list = None
    >>> result = extractIdFromListOfURI(list)
    >>> print result
    []
    
        @param list: a list of URI
        @return: a list of diagram ids    
    """
    result = []
    list = list if list else []
    for item in list:
        result.append( extractIdFromURI(item) )
    return result

# Representations
# ===============
_api = 'www.gliffy.com'
_representations = {
                    'large': '/pubdoc/%s/L.jpg',
                    'medium':'/pubdoc/%s/M.jpg',
                    'small': '/pubdoc/%s/S.jpg',
                    'thumb': '/pubdoc/%s/T.jpg',
                    }

def representations(id):
    """ Generator for representations
            
>>> for r in representations(123): print r
/pubdoc/123/L.jpg
/pubdoc/123/S.jpg
/pubdoc/123/M.jpg
/pubdoc/123/T.jpg

        @param id: the source diagram id 
        @return: URI to target representation
"""
    for k,v in _representations.iteritems():
        yield v % id

class Client(object):
    """ Client API to Gliffy
    """
    def head(self, did):
        """ Performs an HTTP HEAD command on the specified diagram resource
            @param did: diagram id
            @return: result, etag
        """
        host, uri = self._generateURI( did )
        req = httplib.HTTPConnection(host)
        req.request("HEAD", uri)
        try:
            response = req.getresponse()
            req.close()
        except:
            raise api.ErrorNetwork()
        
        if (response.status != 200):
            return False, None

        headers = response.getheaders()        
        etag = self._extractEtag(headers)
        
        return True, etag
        
    def get(self, did):
        """ Performs an HTTP GET command on the specified diagram resource
            @param did: diagram id
            @return: result, etag, data 
        """
        host, uri = self._generateURI( did )
        req = httplib.HTTPConnection(host)
        req.request("GET", uri)
        try:
            response = req.getresponse()
        except:
            raise api.ErrorNetwork()
        
        if (response.status != 200):
            return False, None, None

        headers = response.getheaders()        
        etag = self._extractEtag(headers)
        
        try:
            data = response.read()
            req.close()
        except:
            raise api.ErrorProtocol()
        
        return True, etag, data
        
    def _generateURI(self, did, representation = 'large'):
        """ Generates the URL for a specified diagram + representation
            @param did: diagram id
            @param representation: one of large, medium, small, thumb
            @return: HOST, URI 
        """
        return _api, _representations[representation] % did
        
    def _extractEtag(self, headers):
        etag = None
        for header in headers:
            if header[0] == 'etag':
                etag = header[1]
                break
        
        return etag
        
# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod()

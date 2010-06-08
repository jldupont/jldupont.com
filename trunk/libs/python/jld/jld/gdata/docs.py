"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: docs.py 708 2008-12-04 13:40:35Z JeanLou.Dupont $"

__all__ = [ 'RawDoc', 'ResultDoc' ]

from BeautifulSoup import BeautifulSoup

# Google AppEngine
try:
    from google.appengine.api import urlfetch
    def fetcher(self, url):
        return urlfetch.fetch(url)
    
# Normal
except:
    import urllib2
    def fetcher(url):
        socket = urllib2.urlopen(url)
        return socket.read()

# Exception classes
import jld.api as api

class RawDoc(object):
    """  Retrieves a Google Documents 'doc' in raw format 
    """
    
    _api = "http://docs.google.com/RawDocContents?action=fetch&justBody=false&revision=_latest&editMode=false&docID=%s"
    
    def __init__(self, docid = None):
        """
        """
        self.docid = docid

    def fetch(self, docid = None):
        """ Retrieves and processes
        """
        did = docid if docid else self.docid
        url = self._api % did
        
        try:
            raw = fetcher( url )
        except Exception,e:
            raise api.ErrorNetwork('network error')
        
        return self._process(raw)
        
    def _process(self, content):
        
        raw = BeautifulSoup(content)
        
        body = raw.body
        if (body is None):
            raise api.ErrorProtocol('missing_element', {'element':'body'})

        body = map( lambda X: str(X), body.contents  )
        body = "".join( body )

        revision = raw.body['revision']
        if (revision is None):
            raise api.ErrorProtocol('missing_attribute', {'attribute':'revision'})            

        style = raw.style
        if (style is None):
            raise api.ErrorProtocol('missing_element', {'element':'style'})
        
        return ResultDoc( unicode( style ), unicode( body ), unicode( revision ) )

class ResultDoc(object):
    """ Result doc
    """
    def __init__(self, style, body, revision):
        self.style = style
        self.body  = body
        self.revision = revision
        

"""
    WRONG DOCUMENT ID, RESULT:
    <body class=app id="DocAction"  onload="DoPageLoad();" topmargin=0> ...
    
    GOOD DOCUMENT ID, RESULT:
    <body onload="DoPageLoad();" revision="dgstxrxv_138fh5wphfc:24"> ...
     
"""

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """

    h = RawDoc('dgstxrxv_138fh5wphfc')
    result = h.fetch()
    
    print result.body
    print result.revision
    #print result.style

"""    
    for line in result.body:
        if (line is None):
            continue
        line = str( line ).lstrip()
        if (not len(line)):
            continue
        print "** "+str(line)
"""     
    
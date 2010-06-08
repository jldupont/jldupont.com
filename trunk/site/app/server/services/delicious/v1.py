"""
 @author Jean-Lou Dupont
 
1) GET /https://api.del.icio.us/v1/tags/get

<?xml version='1.0' standalone='yes'?>
<tags>
  <tag count="1" tag=".net" />
  <tag count="23" tag="API" />
  <tag count="1" tag="AmazonWebServices" />
  <tag count="1" tag="BT" />
  <tag count="1" tag="Brain" />
  <tag count="1" tag="Bugzilla" />
  <tag count="6" tag="CDN" />
  <tag count="3" tag="DNS" />
</tags>

2) GET /https://api.del.icio.us/v1/posts/get
<?xml version='1.0' standalone='yes'?>
<posts dt="2008-07-27" tag="" user="jldupont">
  <post href="http://code.google.com/appengine/docs/urlfetch/fetchfunction.html" 
        description="The fetch Function - Google App Engine - Google Code" 
        extended="  Annotated link http://www.diigo.com/bookmark/http%3A%2F%2Fcode.google.com%2Fappengine%2Fdocs%2Furlfetch%2Ffetchfunction.html" 
        hash="d316d09707bdbc3d5ebcb9d8625326de" 
        others="2" 
        tag="no_tag" 
        time="2008-07-27T01:55:23Z" />
        
  <post href="http://docs.python.org/tut/node7.html" 
        description="5. Data Structures" 
        extended="  Annotated link http://www.diigo.com/bookmark/http%3A%2F%2Fdocs.python.org%2Ftut%2Fnode7.html" 
        hash="071baa9c494c6f44886a69262c981455" 
        others="81" 
        tag="no_tag" 
        time="2008-07-27T01:52:47Z" />
</posts>

3) GET /https://api.del.icio.us/v1/tags/bundles/all
<?xml version='1.0' standalone='yes'?>
<bundles>
  <bundle name="php" tags="PEAR-CHANNEL PHING PHPUnit pear php phpdoc" />
  <bundle name="software" tags="ajax eclipse jquery programming subversion" />
  <bundle name="my-stuff" tags="my-diagrams my-mindmaps" />
  <bundle name="telecomm" tags="IP ITU ITU-T atca companies ethernet fcoe semiconductor utca" />
</bundles>
"""

"""
 @author Jean-Lou Dupont
"""
import os
import sys
import logging

import wsgiref.handlers
from google.appengine.ext import webapp
from google.appengine.api import urlfetch

class ServiceDelicious( webapp.RequestHandler ):
    """
        /services/delicious/(.*)/(.*)
        
        /services/delicious/update
        
        /services/delicious/tags/get
        
        /services/delicious/posts/all
        /services/delicious/posts/dates
        /services/delicious/posts/get
        /services/delicious/posts/recent
        
        /services/delicious/tags/bundles/all
    """
    
    deliciousAPI = "https://api.del.icio.us/v1/"
    
    def __init__(self):
        pass

    methods = [ 'update', 'tags', 'posts', 'bundles' ]
    
    def get( self, method, action ):
        if ( method not in self.methods ):
            self.response.set_status( 400 );
            return
        queryString = self.request.query_string
        self.response.headers['WWW-Authenticate'] = 'Basic realm="jldupont/services/delicious"';
        try:
            self.response.headers['Authorization'] = self.request.headers['Authorization'] 
        except:
            self.response.set_status( 401 );
            return
        
        url = self.deliciousAPI+method+"/"+action+"?"+queryString
        response = urlfetch.fetch( url=url, method='GET', headers= { "Authorization":self.request.headers['Authorization'] } );
        self.response.headers['Content-Type'] = 'text/xml; charset=utf-8'     
        self.response.headers = response.headers
        self.response.out.write( response.content )
        self.response.set_status( response.status_code )
  
        #Can't use this
        #self.response.headers['Connection'] = 'close' 
        
#/**
# *  Initialize http handler
# */
def main():
  application = webapp.WSGIApplication([('/services/delicious/v1/(.*?)/(.*)', ServiceDelicious)], debug=True)
  wsgiref.handlers.CGIHandler().run(application)

# Bootstrap
#  It all starts here
if __name__ == "__main__":
    main()
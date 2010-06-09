"""
    Stats service
    
    Proxy Marshaller for CheeseShop Pypi's XMLRPC interface
    
    @author Jean-Lou Dupont
"""
import os
import sys
import logging
import xmlrpclib

import wsgiref.handlers
from google.appengine.ext import webapp
from google.appengine.api import urlfetch

import libs.markup as markup
import libs.webapi as webapi

import libs.datastore.counter as counter

class ServiceStats( webapi.WebApi ):
    """\
    Help
    ====
    
     uri: **/services/stats/[stype]/[page]**
    
    - Supported stat types (stype): $stypes
    
    Testing
    =======
    
    For a working example, use  http://$host/services/stats/image/test
     
    """
    _png_mime   = "image/png"
    
    _stypes = ['image',]
    
    def __init__(self):
        webapi.WebApi.__init__(self)
    
    def get( self, stype = None, page = None):

        if stype is None or stype == '':
            params = {'stypes':self._stypes, 'host':os.environ['HTTP_HOST']}
            return self.showServiceHelp( params )
                
        resolved_method = "method_%s" % stype

        try:
            getattr(self, resolved_method)( page )
        except Exception,e:
            self._output(500, e)
            return

    # =================================================
    # HELP
    # =================================================        
    def _help_method(self, method):
        help =  """\
                **Error**: unsupported type [$stype]
                
                For more information, consult Help_
                
                .. _Help: /services/stats/
                """
        params = {'stype':method}
        self.showHelp(help, params, True)

    # =================================================
    # METHODS
    # =================================================

    _default_image      = "/res/img/favicon.png"
    _default_image_mime = "image/png" 

    def method_image(self, page):
        """\
        **Usage**:  /services/stats/image/[page-name]
        """
        c = counter.Counter( page )
        c.increment()
        count = c.get_count()
        
        ip = self.request.remote_addr
        logging.info("Counter page[%s] count[%s] IP[%s]" % (page, count,ip) )
        
        self.response.headers['Cache-Control'] = "must-revalidate"
        self.response.headers['Content-Type'] = self._default_image_mime
        self.redirect(self._default_image)
    


_urls = [ 
          ('/services/stats/(.*?)/(.*?)',            ServiceStats),  
          ('/services/stats/(.*?)',                  ServiceStats),          
         ]                        
        
#/**
# *  Initialize http handler
# */
def main():
  application = webapp.WSGIApplication(_urls, debug=True)
  wsgiref.handlers.CGIHandler().run(application)

# Bootstrap
if __name__ == "__main__":
    main()

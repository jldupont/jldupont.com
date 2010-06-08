"""
 @author Jean-Lou Dupont
"""
import os
import sys
import logging

import wsgiref.handlers
from google.appengine.ext import webapp

class ServiceEcho( webapp.RequestHandler ):
    
    def __init__(self):
        pass

    def get( self, key ):
        remote_addr = self.request.remote_addr
        logging.info('[echo][IP: '+remote_addr+'[key: '+key+']')
        if ( key == 'jldupont' ):
            self.response.out.write('[IP:'+remote_addr+']');

#/**
# *  Initialize http handler
# */
def main():
  application = webapp.WSGIApplication([('/services/echo/(.*?)/', ServiceEcho)], debug=True)
  wsgiref.handlers.CGIHandler().run(application)

# Bootstrap
#  It all starts here
if __name__ == "__main__":
    main()
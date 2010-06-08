"""
 @author Jean-Lou Dupont
"""
import os
import sys
import logging

import wsgiref.handlers
from google.appengine.ext import webapp
from google.appengine.api import users

class ServiceAccess( webapp.RequestHandler ):
    
    def __init__(self):
        pass

    def get( self, dest_url ):
        remote_addr = self.request.remote_addr
        logging.info('[access][IP: '+remote_addr +']')
        
        user = users.get_current_user()
        login_url = users.create_login_url( dest_url )
        logout_url = users.create_logout_url( dest_url )
        
        if (user):        
            self.response.out.write( "logout:"+ logout_url );
        else:
            self.response.out.write( "login:"+ login_url );

#/**
# *  Initialize http handler
# */
def main():
  application = webapp.WSGIApplication([('/services/access/urls/(.*?)', ServiceAccess)], debug=True)
  wsgiref.handlers.CGIHandler().run(application)

# Bootstrap
#  It all starts here
if __name__ == "__main__":
    main()
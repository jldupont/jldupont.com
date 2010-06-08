"""
    Debian handler

    @author: Jean-Lou Dupont
"""

import os
import sys
import logging
import wsgiref.handlers
from google.appengine.ext import webapp

__all__=["main"]


class Debian( webapp.RequestHandler ):
    
    def get(self, page=None):
        self.response.set_status( 404 )
       

_URLS = [   ('/(.*)', Debian)
         ]

def main():
    logging.info("debian")
    application = webapp.WSGIApplication(_URLS, debug=False)
    wsgiref.handlers.CGIHandler().run(application)

# Bootstrap
#  It all starts here
if __name__ == "__main__":
    main()


"""
 @author Jean-Lou Dupont
"""
import os
import sys
import logging

import wsgiref.handlers
from google.appengine.ext import webapp
from google.appengine.api import urlfetch

class ServiceMM( webapp.RequestHandler ):
    
    _api = "http://www.mindmeister.com/home/converttoimage?id=%s&img_format=%s"
    
    _formats = {'png':'image/png', 'gif':'image/gif'}
    
    def __init__(self):
        pass

    def get( self, format, id ):
        if (format not in self._formats):
            self.response.out.write('unsupported format[%s]' % format);
            return
        
        mime = self._formats[format]
        
        try:
            res = self.fetch(format, id)
        except:
            self.response.out.write('map with id[%s] not found/available (or timeout occured)' % id);
            self.response.set_status(404)
            return
        
        ip = self.request.remote_addr
        self.response.headers["Content-Type"] = mime
        self.response.set_status(200)
        self.response.out.write( res );
        logging.info('ip[%s] mm format[%s] id[%s]' % (ip, format, id))
        
    def fetch(self, format, id):
        url = self._api % (id, format)
        res = urlfetch.fetch( url )
        return res.content
        
#/**
# *  Initialize http handler
# */
def main():
  application = webapp.WSGIApplication([('/mm/(.*?)/(.*?)', ServiceMM)], debug=True)
  wsgiref.handlers.CGIHandler().run(application)

# Bootstrap
#  It all starts here
if __name__ == "__main__":
    main()

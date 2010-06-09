"""
    Service Pypi RSS
    
    @author Jean-Lou Dupont
    
    Conditions to account for:
    ==========================
    * network error
    * Pypi RPC error
    * package not found
    * package with no releases
    * package with no release X
    * Pypi RPC API change eg. the 'downloads' attribute is not found
    * Wrong format requested
    * ALL OTHERS
"""
import os
import sys
import logging
from types import *

curdir=os.path.dirname(__file__)
sys.path.insert(0, curdir)

import wsgiref.handlers
from google.appengine.ext import webapp

import libs.webapi as webapi
import libs.pypi.proxy as proxy
import libs.system as system
from libs.system.datetimeutils import datetimeToRFC822

import rss as feed
import messages as msg

class ServiceMessageOutput(object):
    """ Wrapper used along with Exception Handler
    """
    def __init__(self, msg_output, code_output, code=500):
        self.code = code
        self.msg_output = msg_output
        self.code_output = code_output
        
    def out(self, msg):
        self.msg_output(msg)
        
    def after_out(self):
        """ Called after the 'out' method has been used.
        """
        self.code_output(self.code)



class MethodRssResult(object):
    def __init__(self, unchanged, result, etag):
        self.unchanged = unchanged
        self.result = result
        self.etag = etag
        self.release = None
        self.downloads = None

        

class ServicePypiRss( webapi.WebApi ):
    """\
    Help
    ====
    
     uri: **/services/pypirss/[format]/[package-name]**
    
    - Supported formats: $formats
    
    More information for a method can be obtained, e.g.:
     
     http://$host/services/pypirss/rss/jld/
     
    Testing
    =======
    
    For a working example, use  http://$host/services/pypirss/rss/jld
     
    """
    _formats = [ 'rss' ]
    
    _feedTemplate = feed.prepareFeedTemplate()
    
    
    
    def __init__(self):
        webapi.WebApi.__init__(self)

        
    def get( self, format = None, package_name = None ):

        if format is None or format == '':
            params = {'formats':self._formats, 'host':os.environ['HTTP_HOST']}
            return self.showServiceHelp( params )
        
        if (format not in self._formats):
            self._help_format(format)
            return
                
        if package_name is None or package_name=='':
            self._help_package()
            return
        
        # I know this is confusing...
        resolved_method = "method_%s" % format
        
        ip = self.request.remote_addr
            
        #if_modified_since, if_none_match = self.getConditionalHeaders()
        condHeaders = self.getConditionalHeaders()
        if_none_match = condHeaders[1]
            
        try:
            result = getattr(self, resolved_method)( if_none_match, package_name )
        except Exception, e:
            logging.error( "EXCEPTION type[%s] [%s]" % (type(e),e) )
            result = None
        
        if result is None:
            self.response.set_status(500)
            return

        if result.unchanged:
            logging.info("ip[%s] pkg[%s] UNCHANGED" % (ip, package_name))
            self._output(304, '', "application/rss+xml")
            return

        self.doBaseResponse(result.result, result.etag, "application/rss+xml", 200)
        logging.info("ip[%s] match[%s] pkg[%s] release[%s] downloads[%s]" % (ip, if_none_match, package_name, result.release, result.downloads))        

    # =================================================
    # HELP
    # =================================================
    def _help_format(self, format):
        help =  """\
                **Error**: unsupported format [$format]
                
                For more information, consult Help_
                
                .. _Help: /services/pypirss/
                """
        params = {'format':format}
        self.showHelp(help, params, True)

    def _help_package(self):
        help =  """\
                **Error**: package name must be specified
                
                For more information, consult Help_
                
                .. _Help: /services/pypirss/
                """
        self.showHelp(help, convert=True)

        
    # =================================================
    # METHODS
    # =================================================

    def method_rss(self, ref_etag, package):
        """\
        **Usage**:  /services/pypirss/rss/[package-name]
        """
        try:
            latest, downloads, last_update = proxy.getLatestDownloads(package)           
        except Exception,e:
            self._handleException(e)
            return None

        itemGUID = "[%s][%s][%s]" % (package, latest, downloads)
        
        
        
        # same as before?
        etag = ref_etag.strip('"')
        
        #logging.info("etag(%s) itemGUID(%s)" % (etag, itemGUID))
        
        try:
            if etag == itemGUID:
                return MethodRssResult(True, '', etag)
        except:
            pass
        
        pubDate = datetimeToRFC822(last_update)

        params = {'package':package, 
                  'release':latest, 
                  'downloads':downloads,
                  'itemPubDate': pubDate,
                  'itemGUID': itemGUID
                  }
        res = self._feedTemplate.produce(params, [params])   
        
        result = MethodRssResult(False, res, itemGUID)
        result.release = latest
        result.downloads = downloads
        
        return result    

    # =================================================
    # HANDLERS
    # =================================================
    def _handleException(self, exc):
        msgOutput = ServiceMessageOutput(self.response.out.write, 
                                              self.response.set_status)
        
        #setup an exception handler... just in case
        excHandler = system.ExceptionHandler(msg.messages, 
                                             msg.message_template, 
                                             output=msgOutput)
        
        excHandler.handleException(exc)
      

_urls = [ 
          ('/services/pypirss/(.*?)/(.*?)', ServicePypiRss),
          ('/services/pypirss/(.*?)',       ServicePypiRss),  
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

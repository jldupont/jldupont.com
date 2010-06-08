"""
    Service Pypi
    
    Proxy Marshaller for CheeseShop Pypi's XMLRPC interface
    
    @author Jean-Lou Dupont
"""
import os
import sys
import logging
import xmlrpclib
from types import *

import wsgiref.handlers
from google.appengine.ext import webapp
from google.appengine.api import urlfetch

#import import_wrapper

import libs.xmlrpc as gaexmlrpc
import libs.simplejson as json
import libs.markup as markup
import libs.webapi as webapi

import libs.pypi.api as pypiapi

class ServicePypi( webapi.WebApi ):
    """\
    Help
    ====
    
     uri: **/services/pypi/[format]/[method]/[package-name]/[package-version][?callback=CALLBACK]**
    
    - Supported methods: $methods
    - Supported formats: $formats
    - JSONP callback: [optional] use the *?callback* parameter
    
    More information for a method can be obtained, e.g.:
     
     http://$host/services/pypi/json/package_releases/
     
    Testing
    =======
    
    For a working example, use  http://$host/services/pypi/json/package_releases/jld?callback=cb
     
    """
    _formats = [ 'json' ]
    _mimes   = { 'json':'text/javascript' }
    
    _server = pypiapi.PypiClient()
    
    def __init__(self):
        webapi.WebApi.__init__(self)
    
    def get( self, format = None, method = None, package_name = None, version = None ):

        if format is None or format == '':
            params = {'methods':self._prefix_methods, 'formats':self._formats, 'host':os.environ['HTTP_HOST']}
            return self.showServiceHelp( params )
        
        if (format not in self._formats):
            self._help_format(format)
            return
        
        if (method not in self._prefix_methods ):
            self._help_method(method)
            return
        
        callback = self.request.get("callback")
        
        mime = self._mimes[format]
        
        resolved_method = "method_%s" % method
        
        ip = self.request.remote_addr
        
        parameters = {}
        
        if package_name:
            parameters['package_name'] = package_name
            
        if version:
             parameters['version'] = version

        try:
            data, freshness = getattr(self, resolved_method)( **parameters )
            #logging.info( data )
            self._prepareForJson(data)
            res = json.dumps( data )
            
        except TypeError,e:
            logging.error( e )
            return self._help_method_parameters(method)
        
        except Exception, e:
            logging.error( "EXCEPTION type[%s] [%s]" % (type(e),e) )
            self.response.set_status(500)
            return

        if callback:
            res = "%s(%s)" % (callback, res)
        
        self._output(200, res, mime)
        logging.info("ip[%s] method[%s] pkg[%s] version[%s] data[%s] freshness[%s]" % (ip, method, package_name, version, data, freshness))        

    def _prepareForJson(self, data):
        """ Go through the list of dict and make sure every item can be serialized.
            Every object of non-primitive type will be __str__.
            Currently, only DateTime class objects are found.
        """
        if type(data) is not ListType:
            return
        
        for list_entry in data:
            if type(list_entry) is not DictType:
                continue
            
            for key,value in list_entry.iteritems():
                t = type( list_entry[key] )
                it = t is InstanceType
                if (it):
                    list_entry[key] = str(list_entry[key])
                #logging.info("key[%s] type[%s] it[%s]" % (key,t,it) )

    # =================================================
    # HELP
    # =================================================
    def _help_format(self, format):
        help =  """\
                **Error**: unsupported format [$format]
                
                For more information, consult Help_
                
                .. _Help: /services/pypi/
                """
        params = {'format':format}
        self.showHelp(help, params, True)
        
    def _help_method(self, method):
        help =  """\
                **Error**: unsupported method [$method]
                
                For more information, consult Help_
                
                .. _Help: /services/pypi/
                """
        params = {'method':method}
        self.showHelp(help, params, True)

    def _help_method_parameters(self, method):
        ""
        doc = self.getDoc(method)
        self._output(200, doc, "text/html")

    # =================================================
    # METHODS
    # =================================================

    def method_package_releases(self, package_name):
        """\
        **Usage**:  /services/pypi/[format]/package_releases/[package-name]
        """
        return self._server.getPackageReleases(package_name)
    
    def method_release_urls(self, package_name, version):
        """\
        **Usage**:  /services/pypi/[format]/release_urls/[package-name]/[package-version]
        """        
        return self._server.getReleaseUrls(package_name, version)
    
    def method_release_data(self, package_name, version):
        """\
        **Usage**:  /services/pypi/[format]/release_data/[package-name]/[package-version]
        """        
        return self._server.getReleaseData(package_name, version)


_urls = [ 
          ('/services/pypi/(.*?)/(.*?)/(.*?)/(.*?)', ServicePypi),
          ('/services/pypi/(.*?)/(.*?)/(.*?)',       ServicePypi),
          ('/services/pypi/(.*?)/(.*?)',             ServicePypi),
          ('/services/pypi/(.*?)',                   ServicePypi),  
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

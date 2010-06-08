#!/usr/bin/env python
""" Pypi API

    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: api.py 897 2009-03-28 00:18:03Z JeanLou.Dupont $"

import os
import logging

import xmlrpclib

try:
    import libs.xmlrpc as gaexmlrpc
    import libs.cache as cache    
except:
    pass

from libs.tools.env import *

class PypiCall(object):
    
    def __init__(self, method=None):
        self.method = method
        self._initServer()
    
    def _initServer(self):
        """ Dev vs. Hosted vs. no-GAE
        """
        if isGAE():
            self._server = xmlrpclib.ServerProxy('http://pypi.python.org/pypi', gaexmlrpc.GAEXMLRPCTransport())
        else:
            self._server = xmlrpclib.ServerProxy('http://pypi.python.org/pypi')
    
    def __getattr__(self, name):
        try:
            return object.__getattr__(self, name)
        except AttributeError:
            return PypiCall(name)
            
    def __call__(self, *args, **kargs):
        return getattr(self._server, self.method)( *args, **kargs )


_package_releases_ttl     = 60*60
_package_release_data_ttl = 5*60
_package_release_urls_ttl = 5*60 
    
            
class PypiClient(object):
    """
    >>> s = PypiClient()
    >>> print s.getPackageReleases("jld") # doctest:+ELLIPSIS
    [...]
    >>> print s.getReleaseData("jld", "0.0.39")
    {'maintainer': None, 'maintainer_email': None, 
    'cheesecake_code_kwalitee_id': None, 
    'keywords': None, 'author': 'Jean-Lou Dupont', 
    'author_email': 'python@jldupont.com', 
    'download_url': 'UNKNOWN', 'platform': 'UNKNOWN', 
    'version': '0.0.39', 'obsoletes': [], 'provides': [], 
    'cheesecake_documentation_id': None, '_pypi_hidden': 0, 
    'description': [...] 
    '_pypi_ordering': 14, 
    'classifiers': ['Development Status :: 3 - Alpha', 'Intended Audience :: Developers', 'License :: Public Domain', 'Operating System :: Microsoft :: Windows', 'Operating System :: POSIX', 'Programming Language :: Python', 'Topic :: Software Development :: Libraries :: Python Modules'], 
    'name': 'jld', 
    'license': 'UNKNOWN', 
    'summary': "Jean-Lou Dupont's Python Library - WEB API & command line tools", 
    'home_page': 'http://www.jldupont.com/doc/lib/jld/', 
    'stable_version': None, 
    'requires': [], 
    'cheesecake_installability_id': None}
    
    >>> print s.getReleaseUrls("jld", "0.0.39")
     [{'has_sig': False, 'upload_time': <DateTime '20090325T01:56:29' at 1182f58>, 'comment_text': '', 'python_version': '2.5', 'url': 'http://pypi.python.org/packages/2.5/j/jld/jld-0.0.39-py2.5.egg', 'md5_digest': '158528cf014be209356c019a8de9ac6a', 'downloads': 5, 'filename': 'jld-0.0.39-py2.5.egg', 'packagetype': 'bdist_egg', 'size': 194946}]
    """
    _server = PypiCall()
    
    @cache.memoize('/pypi/package_releases/', report_freshness = True, ttl = _package_releases_ttl)
    def getPackageReleases(self, package_name):
        ""
        return self._server.package_releases(package_name, True)
    
    @cache.memoize('/pypi/release_data/', report_freshness = True, ttl = _package_release_data_ttl)
    def getReleaseData(self, package_name, version):
        ""
        return self._server.release_data(package_name, version)
    
    @cache.memoize('/pypi/release_urls/', report_freshness = True, ttl = _package_release_urls_ttl)
    def getReleaseUrls(self, package_name, version):
        ""
        return self._server.release_urls(package_name, version)
        



def computeTotalDownloads(data):
    """
    >>> liste = [{'downloads':10}, {'downloads':11}, {'downloads':12}]
    >>> print computeTotalDownloads(liste)
    33
    """
    def add(x,y):
        try:    xt = x.get('downloads',0)
        except: xt = x
        try:    yt = y.get('downloads',0)
        except: yt = y
        return xt+yt
        
    return reduce(add, data)
    
    
    


# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod()

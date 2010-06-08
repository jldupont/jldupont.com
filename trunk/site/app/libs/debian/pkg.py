"""
    @author: Jean-Lou Dupont
"""
import logging
import gzip
try:    from cStringIO import StringIO
except: from StringIO import StringIO
from google.appengine.api import memcache
from google.appengine.api import urlfetch


repo="http://epapi.googlecode.com/svn/dists/stable/main/binary-i386/Packages.gz"

def fetch(url):
    """
    Fetches a "Packages.gz" and decompresses it
    
    @return: ("ok", data)  |  ("error", Reason) 
    """
    result=urlfetch.fetch(url=repo)
    if result.stats_code != 200:
        return ("error", "HTTP GET failed")
    
    try:    fh=StringIO( str(result.content) )
    except: return ("error", "failed to create memory file")
    
    try:
        gzh=gzip.GzipFile(fileobj=fh)
        data=gzh.readlines(-1) # all the lines
    except:
        return ("error", "cannot decompress")
    finally:
        gzh.close()
    
    return ("ok", data)

    
def cachedFetch(url, timeout=15*60):
    """
    Fetch with caching layer
    
    @return: ("ok", data) | ("error", Reason)
    """
    key="/debian/pkggz/%s" % url
    
    data=memcache.get(key)
    if data is not None:
        return ("ok", data)
    
    (code, value)=fetch(url)
    if code=="ok":
        memcache.set(key, value,time=timeout)
        
    return (code, value)


def extractEntries(pkg_data):
    """
    Generator for iterating over packages.gz data
    
    @param pkg_data: [string()] of packages.gz data
    
    @return:  {'Package':Package, ...} 
    """
    current={}
    for line in pkg_data:
        (key, _sep, value)=line.partition(":")
        
        if (key=="\n"): 
            yield current
            current={}
            
        if (_sep!=":"): 
            continue
        else:
            current[key]=value

        
        
        


if __name__=="__main__":

    pkg_data=['Package: epapi\n', 
         'Version: 0.3\n', 
         'Architecture: i386\n', 
         'Maintainer: Jean-Lou Dupont <epapi@jldupont.com>\n', 
         'Filename: dists/stable/main/binary-i386/epapi_0.3-1_i386.deb\n', 
         'Size: 246022\n', 
         'MD5sum: 5c013f7510f79782bbe24e9972f055a7\n', 
         'Section: Other\n', 
         'Priority: optional\n', 
         'Description: Erlang Port driver API shared library\n', 
         ' EPAPI is a shared library for implementing C/C++ port driver clients.\n', 
         ' .\n', 
         '\n', 
         
         'Package: epapi\n', 'Version: 0.4\n', 'Architecture: i386\n', 
         'Maintainer: Jean-Lou Dupont <epapi@jldupont.com>\n', 
         'Depends: python (>= 2.5), dbus (>= 1.2.12)\n', 
         'Filename: dists/stable/main/binary-i386/epapi_0.4-1_i386.deb\n', 
         'Size: 51430\n', 'MD5sum: 8dfa184fc30a981adb8436e1dc480981\n', 
         'Section: devel\n', 'Priority: optional\n', 
         'Description: Erlang Port driver API shared library\n', 
         ' EPAPI is a shared library for implementing C/C++ port driver clients.\n', 
         ' .\n', '\n', 
         
         'Package: epapi\n', 
         'Version: 0.1\n', 
         'Architecture: i386\n', 
         'Maintainer: Jean-Lou Dupont <epapi@jldupont.com>\n', 
         'Filename: dists/stable/main/binary-i386/epapi_0.1-1_i386.deb\n', 
         'Size: 87790\n', 
         'MD5sum: 4cbb208235ca1d57e192ada393a1fd07\n', 
         'Section: Other\n', 
         'Priority: optional\n', 
         'Description: Erlang Port driver API shared library\n', 
         ' EPAPI is a shared library for implementing C/C++ port driver clients.\n', 
         ' .\n', 
         '\n', 
         
         'Package: epapi\n', 
         'Version: 0.7\n', 
         'Architecture: i386\n', 
         'Maintainer: Jean-Lou Dupont <epapi@jldupont.com>\n', 
         'Depends: python (>= 2.5), dbus (>= 1.2.12)\n', 
         'Filename: dists/stable/main/binary-i386/epapi_0.7-1_i386.deb\n', 
         'Size: 53362\n', 
         'MD5sum: 6ca8f436e7831ce3c1eb133bdef24415\n', 
         'Section: devel\n', 
         'Priority: optional\n', 
         'Description: Erlang Port driver API shared library\n', 
         ' EPAPI is a shared library for implementing C/C++ port driver clients.\n', 
         ' .\n', 
         '\n', 
         
         'Package: epapi\n', 
         'Version: 0.5\n', 
         'Architecture: i386\n', 
         'Maintainer: Jean-Lou Dupont <epapi@jldupont.com>\n', 
         'Depends: python (>= 2.5), dbus (>= 1.2.12)\n', 
         'Filename: dists/stable/main/binary-i386/epapi_0.5-1_i386.deb\n', 
         'Size: 51358\n', 'MD5sum: 425cff124bfc13d279b5054601f17528\n', 
         'Section: devel\n', 'Priority: optional\n', 
         'Description: Erlang Port driver API shared library\n', 
         ' EPAPI is a shared library for implementing C/C++ port driver clients.\n', 
         ' .\n', '\n', 
         
         'Package: epapi\n', 
         'Version: 0.2\n', 
         'Architecture: i386\n', 
         'Maintainer: Jean-Lou Dupont <epapi@jldupont.com>\n', 
         'Filename: dists/stable/main/binary-i386/epapi_0.2-1_i386.deb\n', 
         'Size: 169012\n', 
         'MD5sum: 8c8ef6e07b759e57803771d62bc97553\n', 
         'Section: Other\n',
          'Priority: optional\n', 
         'Description: Erlang Port driver API shared library\n', 
         ' EPAPI is a shared library for implementing C/C++ port driver clients.\n', 
         ' .\n', '\n'
         ]

    for entry in extractEntries(pkg_data):
        print entry
    
        
    
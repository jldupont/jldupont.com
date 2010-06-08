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


def main():
    logging.info("repo fetcher called!")
    pkg=memcache.get("pkg")
    if pkg is None:
        result=urlfetch.fetch(url=repo)
        if result.status_code == 200:
            pkg=result.content
            memcache.add("pkg", pkg)
        else:
            logging.error("Cannot fetch Packages.gz from repo")
            return
    #logging.info( str(pkg) )
    fh=StringIO(str(pkg))
    #f=open(fp, "r")
    #print f.readlines()
    gziph=gzip.GzipFile(fileobj=fh)
    data=gziph.readlines(-1)
    logging.info( data )
    
    
    #print data
    


if __name__ == "__main__":
    main()

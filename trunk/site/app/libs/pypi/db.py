#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
import logging

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: db.py 898 2009-04-13 13:11:46Z JeanLou.Dupont $"

from google.appengine.ext import db

import libs.cache as cache


class PackageReleaseData(db.Model):
    """
    Pypi package release data
    """
    name        = db.StringProperty(required=True)
    release     = db.StringProperty(required=True)
    downloads   = db.IntegerProperty(default=0)
    last_update = db.DateTimeProperty()


@cache.memoize('/pypi/db/oldest_packages/', ttl= 5*60, report_freshness=False)
def getRecentPackage(count=100):
    """
    Retrieves the recent updates
    """
    q = PackageReleaseData.all()
    q.order("-last_update")
    
    result_set = q.fetch(count)

    return result_set


def getPackageData(name, count=1000):
    """
    Retrieves a package's release data
    ordered by descending release
    """
    q = PackageReleaseData.all()
    q.filter("name =", name)
    q.order("-release")
    
    return q.fetch(count)


def getNextInList(list, filters=None):
    """
    Retrieves the first ''item'' from the list
    that isn't listed in ``filters``
    """
    if not list:
        return (None, filters)
    
    filtered = filter(lambda X: X not in filters, list)
    
    try:    item = filtered.pop(0)
    except: item = None
    
    return (item, filters) 



@cache.memoize('/pypi/db/release_data/', ttl= 15*60, report_freshness=False)
def getPackageReleaseData(name, release):
    """ 
    Database look-up with memcaching
    """
    q = PackageReleaseData.all()
    q.filter("name =", name)
    q.filter("release =", release)
    result = q.fetch(1)
    
    try:
        return result[0]
    except Exception,e:
        pass
        #logging.error(">%s" % e)
    
    return None



def setPackageReleaseData(name, release, downloads, last_update, entity=None):
    """ 
    Stores package release data
    """
    if entity is None:
        d = PackageReleaseData(name=name, release=release, downloads=downloads, last_update=last_update)
        d.put()
    else:
        entity.downloads = downloads
        entity.last_update = last_update
        entity.put()
    
        
        

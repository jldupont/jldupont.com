#!/usr/bin/env python
""" Serves as proxy to Pypi

    Caches package release data through
    a combination of memcache & datastore.

    @author: Jean-Lou Dupont
"""
import logging

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: proxy.py 879 2009-03-11 17:59:37Z JeanLou.Dupont $"

__all__ = ['getPackageReleases', 'getPackageReleaseData']

# Exceptions raised by this module
# ================================
__errors = ['error_package_not_found',
            'error_package_releases', 
            'error_package_release_data_datastore_access',
            'error_package_release_data',
            'error_package_release_data_downloads', ]

import datetime as datetime

import api as api
import db as db


class ProxyException(Exception):
    def __init__(self, msg, params = None):
        Exception.__init__(self, msg)
        self.params = params

def getLatestDownloads(name):
    """ Retrieves the latest download count for latest release of package.
    """ 
    liste = getPackageReleases(name)
    try:    latest = liste[0]
    except:
        raise ProxyException("error_package_not_found", {"name":name})
    
    last_update, downloads = getPackageReleaseData(name, latest)
    return [latest, downloads, last_update]

def getPackageReleases(name):
    """ Gets the available releases for a given package.
        The release list is not recorded in the datastore.
    """
    try:
        liste, freshness = api.PypiClient().getPackageReleases(name)
    except Exception,e:
        raise ProxyException("error_package_releases", {"exc":e, 'name':name} )

    return liste
        
def getPackageReleaseData(name, release, ttl=60*60):
    """ Get the available data for a given [package;version].
        The retrieved data is stored in the datastore.
        
        1) Looks in memcache/datastore
        2) Looks on Pypi directly
        
        @return: [last_update, downloads]
    """
    
    # First, try the datastore
    try:
        entity = db.getPackageReleaseData(name, release)
    except Exception,e:
        raise ProxyException("error_package_release_data_datastore_access", {"name":name,"release":release, "exc:":e} )
    
    now = datetime.datetime.now()
    
    # fresh enough?
    if entity is not None:
        delta = now - entity.last_update
        if (delta.seconds < ttl):
            return [entity.last_update, entity.downloads]
    
    # Retrieve from Pypi
    try:
        data, freshness = api.PypiClient().getReleaseUrls(name, release)
    except Exception,e:
        raise ProxyException("error_package_release_data", {"name":name,"release":release, "exc:":e} )
    
    # Extract the needed data
    try:
        downloads = data[0]['downloads']
    except Exception,e:
        raise ProxyException("error_package_release_data_downloads", {"name":name,"release":release,"exc:":e} )

    # Update the datastore
    last_update = now
    db.setPackageReleaseData(name, release, downloads, last_update, entity=entity)
    
    return [last_update, downloads]

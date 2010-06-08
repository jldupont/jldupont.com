""" Django filesystem based template loader with memcache
    @author: Jean-Lou Dupont
"""
import os
import logging

IS_REMOTE = not os.environ.get('SERVER_SOFTWARE').startswith('Dev')

_DEBUG = not IS_REMOTE

from google.appengine.api import memcache
from django.conf import settings
from django.template import TemplateDoesNotExist

__all__ = ['get_template_sources','load_template_source' ]

_CACHE_TIMEOUT = 0
_CACHE_TEMPLATE_KEY_PATH    = "/templates/file/path/%s"
_CACHE_TEMPLATE_KEY_CONTENT = "/templates/file/content/%s"
_CACHE_TEMPLATE_KEY_TS      = "/templates/file/ts/%s"

# OPTIONAL CONFIGURATION
# ======================
_LISTE = settings.TEMPLATE_ALLOWED_EXTENSIONS
_EXTENSIONS = _LISTE if _LISTE else []

# =============================================
"""
ROOT = None
try:
    test = __file__
    while True:
        last = test
        new_test = os.path.dirname( test )
        os.path.getmtime( new_test )
        if (test == new_test):
            break
        test = new_test
except:
    ROOT = last

logging.info('ROOT [%s]' % ROOT)
"""

def get_template_sources(template_name, template_dirs=None):
    
    if not template_dirs:
        template_dirs = settings.TEMPLATE_DIRS
        
    for template_dir in template_dirs:
        for extension in _EXTENSIONS:
            if (template_name.endswith(extension)):
                yield os.path.join(template_dir, template_name)
            else:
                yield os.path.join(template_dir, template_name + extension)

def load_template_source(template_name, template_dirs=None):
    if (not _DEBUG):
        try:
            cached_template      = memcache.get(_CACHE_TEMPLATE_KEY_CONTENT % template_name)        
            cached_template_ts   = memcache.get(_CACHE_TEMPLATE_KEY_TS % template_name)        
            cached_template_path = memcache.get(_CACHE_TEMPLATE_KEY_PATH % template_name)
            if (cached_template and cached_template_ts and cached_template_path):
                ts = os.path.getmtime(cached_template_path)           
                if (ts == cached_template_ts):
                    #logging.info( 'got from memcache [%s]' % template_name )
                    return (cached_template, template_name)
        except:
            logging.debug('filesystem miss [%s]' % template_name)  
    tried = []
    
    for filepath in get_template_sources(template_name, template_dirs):        
        #logging.info('processing template_name[%s]' % template_name)
        #part1     = filepath.rpartition(os.sep)
        #lastPart  = part1[2]
        #part2     = part1[0].rpartition(os.sep)
        #firstPart = part2[2]
        #path = firstPart + os.sep + lastPart
        
        #logging.info('processing template_name[%s] path[%s]' % (template_name, filepath))
        try:
            tpl = (open(filepath,'r').read(), filepath)
            ts  = os.path.getmtime(filepath)
            
            memcache.set(_CACHE_TEMPLATE_KEY_TS % template_name, ts, _CACHE_TIMEOUT )            
            memcache.set(_CACHE_TEMPLATE_KEY_PATH % template_name, filepath, _CACHE_TIMEOUT )
            memcache.set(_CACHE_TEMPLATE_KEY_CONTENT % template_name, tpl[0], _CACHE_TIMEOUT )
            #logging.info( 'saved in memcache [%s]' % template_name )
            return tpl
        except Exception,e:
            tried.append(filepath)
            logging.debug('filesystem_template_loader/load_template_source: Exception [%s] type[%s]' % (e,type(e)))
    if tried:
        error_msg = "Tried %s" % tried
    else:
        error_msg = "Your TEMPLATE_DIRS setting is empty. Change it to point to at least one template directory."
        
    logging.debug('NOT FOUND [%s]' % template_name)
    raise TemplateDoesNotExist, error_msg

load_template_source.is_usable = True


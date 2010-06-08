#!/usr/bin/env python
""" Delicious objects
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: delicious_objects.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

from xml.dom import minidom

import jld.api as api


__sample__tags = """
<?xml version="1.0" encoding="UTF-8"?>
<tags>
  <tag count="1" tag=".net"/>
  <tag count="19" tag="AI"/>
  <tag count="2" tag="AOP"/>
  <tag count="1" tag="AmazonWebServices"/>
  <tag count="1" tag="BT"/>
  <tag count="1" tag="Brain"/>
<tags>
"""
def Tags(raw):
    """ Response Processor for B{Tags} objects
    """
    try:
        e = minidom.parseString(raw).documentElement
        
        liste = []
        ts = e.getElementsByTagName('tag')
        for t in ts:
            count = t.getAttribute('count')
            tag   = t.getAttribute('tag')
            liste.append( (tag,count) )
    except Exception,e:
        raise api.ErrorProtocol( 'msg:expecting', {'param':'tag'} )

    return liste


__sample_recent = """
<?xml version="1.0" encoding="UTF-8"?>
<posts user="jldupont" tag="">
  <post    href="http://www.gliffy.com/publish/1553434/" 
            hash="94bd15480886834d104b24178dcc3e48" 
            description="BasicInterfaces" 
            tag="my-diagrams" 
            time="2008-12-01T20:36:01Z" 
            extended=""/>
  <post href="http://dev.chromium.org/developers/design-documents/extensions" hash="b02e129cdaa64cbbb54252cfbfebeeee" description="Extensions " tag="chromium, google" time="2008-12-01T19:31:46Z" extended=""/>
</posts>
"""

#<post href="http://www.gliffy.com/publish/1553333/" hash="77b18cae662d6935ca223de83607795d" description="AccessPoint" tag="my-diagrams" time="2008-12-08T19:10:03Z" extended=""/>
# Hash parameter is not a function of the TAG nor the DESCRIPTION fields

_posts_fields = ['href', 'hash', 'description', 'tag', 'time', 'extended']
def Posts(raw):
    """ Response Processor for B{Posts} objects
    """
    try:
        e = minidom.parseString(raw).documentElement
        
        liste = []
        ps = e.getElementsByTagName('post')
        for p in ps:
            entry = {}
            for key in _posts_fields:
                value = p.getAttribute(key)
                entry[key] = value
            liste.append( entry )
    except Exception,e:
        raise api.ErrorProtocol( 'msg:expecting', {'param':'post'} )

    return liste


#no matching posts
__sample_recent2 = """
https://api.del.icio.us/v1/posts/recent?count=100&tag=business333
<?xml version="1.0" encoding="UTF-8"?>
<posts user="jldupont" tag="business333"/>
"""

__sample_update = """
<?xml version="1.0" encoding="UTF-8"?>
<update time="2008-12-01T20:36:01Z" inboxnew="0"/>
"""
def Update(raw):
    """ Response Processor for B{Update} objects
    """
    try:
        e = minidom.parseString(raw).documentElement
        time = e.getAttribute('time')
        inbox = e.getAttribute('inboxnew')
    except Exception,e:
        raise api.ErrorProtocol( 'msg:expecting', {'param':'update'} )

    return (time,inbox) 

__sample_bundle = """
<?xml version="1.0" encoding="UTF-8"?>
<bundles>
  <bundle name="my-stuff" tags="my-diagrams my-mindmaps"/>
</bundles>
"""
def Bundle(raw):
    """ Response processor for B{Bundle} object
    """
    try:
        e = minidom.parseString(raw).documentElement
        b = e.getElementsByTagName('bundle')[0]
        name = b.getAttribute('name')
        tags = b.getAttribute('tags')
    except Exception,e:
        raise api.ErrorProtocol( 'msg:expecting', {'param':'bundle'} )

    return (name, tags)

__sample_bundles = """
<?xml version="1.0" encoding="UTF-8"?>
<bundles>
  <bundle name="my-stuff" tags="my-diagrams my-mindmaps"/>
  <bundle name="php" tags="php pear PHING phpdoc PHPUnit PEAR-CHANNEL"/>
  <bundle name="programming" tags="gae GWT python programming"/>
  <bundle name="software" tags="ajax jquery eclipse subversion programming"/>
  <bundle name="telecomm" tags="IP ITU atca fcoe utca ITU-T ethernet companies semiconductor"/>
</bundles>
"""

def Bundles(raw):
    """ Response processor for B{Bundles} objects
    """
    try:
        e = minidom.parseString(raw).documentElement
        
        liste = []
        bs = e.getElementsByTagName('bundle')
        for b in bs:
            name = b.getAttribute('name')
            tags = b.getAttribute('tags')
            liste.append( (name,tags) )
    except Exception,e:
        raise api.ErrorProtocol( 'msg:expecting', {'param':'bundle'} )

    return liste

__sample_hashes = """
<posts>
    <post meta="957d3e3b5e04fbe250f995cb663ad271" url="94bd15480886834d104b24178dcc3e48"/>
    <post meta="cf811c206b21654979dc1db885647381" url="b02e129cdaa64cbbb54252cfbfebeeee"/>
</posts>
"""
def Hashes(raw):
    """ Response processor B{Hashes} objects
    """ 
    try:
        e = minidom.parseString(raw).documentElement
        
        liste = []
        ps = e.getElementsByTagName('post')
        for p in ps:
            meta = p.getAttribute('meta')
            url  = p.getAttribute('url')
            liste.append( (url, meta) )
    except Exception,e:
        raise api.ErrorProtocol( 'msg:expecting', {'param':'post'} )

    return liste

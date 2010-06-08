"""
    Retrieves Feedburner Awareness Data in RSS format
 
    @author Jean-Lou Dupont
"""
import os
import sys
import logging

import wsgiref.handlers
import xml.dom.minidom
import time
from time import strftime, strptime
from string import Template

from google.appengine.ext import webapp
from google.appengine.api import urlfetch

_tpl = """<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <atom:link href="http://www.jldupont.com/services/feedburner/aw/$feed_id/" rel="self" type="application/rss+xml" />
        
        <title>www.jldupont.com</title>
        <description>Feedburner Awareness Data Feed Service</description>
        <link>http://www.jldupont.com/services/feedburner/aw/$feed_id/</link>

        <item>
            <title>$itemTitle</title>
            <description>Circulation: $itemCirculation, hits: $itemHits, reach: $itemReach</description>
            <link>$itemLink</link>
            <pubDate>$itemPubDate</pubDate>
            <guid isPermaLink='false'>$itemGuid</guid>
        </item>

    </channel>
</rss>
"""

class ServiceAw( webapp.RequestHandler ):
    """
        Feedburner Awareness Data
    
        Usage:  /services/feedburner/aw/feed-id/
            where "feed-id" would match: http://feeds.feedburner.com/feed-id/
    """
    
    #api = 'https://api.feedburner.com/awareness/1.0/GetFeedData?uri=http://feeds.feedburner.com/%s';
    api = 'https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=%s';
    
    _template = Template(_tpl)
    
    def __init__(self):
        pass

    def head(self, feed_id):
        user_agent = self.request.headers['User-Agent']
        
        try:    if_modified_since = self.request.headers['If-Modified-Since']
        except: if_modified_since = ''
        
        try:    if_none_match     = self.request.headers['If-None-Match']
        except: if_none_match     = ''        
        
        logging.info( "FeedId [%s] UA[%s]" % (feed_id, user_agent) )
        logging.info( "HEAD request: If-Modified-Since (%s), If-None-Match (%s)" %  (if_modified_since, if_none_match) )
        
        [rawPage, page] = self.getPage( feed_id )
        if page is None:
            self.response.set_status( 404 );
            return
        
        params = self.extractParams( feed_id, rawPage, page )
        self.response.headers['ETag'] = '"' + params[ 'etag' ] + '"'
        self.response.headers['Last-Modified'] = params[ 'itemPubDate' ]
        self.response.headers['Content-Type'] = "application/rss+xml"
        self.response.set_status( 200 );  

    def get( self, feed_id ):
        
        user_agent = self.request.headers['User-Agent']
        

        try:    if_modified_since = self.request.headers['If-Modified-Since']
        except: if_modified_since = ''
        try:    if_none_match     = self.request.headers['If-None-Match']
        except: if_none_match     = ''
        
        logging.info( "FeedId [%s] UA[%s]" % (feed_id, user_agent) )
        logging.info( "GET request: FeedId (%s) If-Modified-Since (%s), If-None-Match (%s)" %  (feed_id, if_modified_since, if_none_match) )
        
        [rawPage, page] = self.getPage( feed_id )
        if page is None:
            return
        
        params = self.extractParams( feed_id, rawPage, page )
        if (params is None):
            self.response.out.write( 'Error: is the feed valid?' )
            self.response.set_status( 404 );
            return
            
        self.printPage( params )

    def getRawPage(self, feed_id ):

        try:
            rawPage = urlfetch.fetch( self.api % feed_id )
            if ( rawPage.status_code != 200 ):
                return None
        except:
            rawPage = None
            
        return rawPage
    
    def getPage( self, feed_id ):
        rawPage = self.getRawPage(feed_id)
        if (rawPage is None):
            return [None,None];
        page = xml.dom.minidom.parseString( rawPage.content )
        return [ rawPage.content, page ]

    def extractParams( self, feed_id, rawPage, page ):
        
        try:
            feed = page.getElementsByTagName( 'feed' )
        
            id   = feed[0].getAttribute( 'id' )
            uri  = feed[0].getAttribute( 'uri' )
            entry= feed[0].getElementsByTagName( 'entry' )
            date = entry[0].getAttribute( 'date' )
            circ = entry[0].getAttribute( 'circulation' )
            hits = entry[0].getAttribute( 'hits' )
            reach= entry[0].getAttribute( 'reach' )

        except:
            error = page.getElementsByTagName( 'err' )
            code  = error[0].getAttribute( 'code' )
            logging.error( "aw: error code(%s)" % code )
            return None

        link = 'http://feeds.feedburner.com/%s' % uri

        sDate   = strptime( date, "%Y-%m-%d" )
        pubDate = strftime( "%a, %d %b %Y %H:%M:%S +0000", sDate )
        pubTime = strftime( "%H:%M:%S", time.localtime() )


        etag = "%s-%s-%s-%s" % (feed_id, circ, hits, reach)

        params = {  'feed_id':         feed_id,
                    'itemTitle':       uri,
                    'itemLink' :       link,
                    'itemCirculation': circ, 
                    'itemHits':        hits, 
                    'itemReach':       reach, 
                    'itemPubDate':     pubDate,
                    'rawData' :        rawPage,
                    'etag':            str( etag ),
                    'itemGuid':        uri + '-' + date + '-' + pubTime
                }
        return params 

    def printPage(self, params ):
        
        self.response.headers['ETag'] = '"' + params[ 'etag' ] + '"'
        self.response.headers['Last-Modified'] = params[ 'itemPubDate' ]
        self.response.headers['Content-Type'] = "application/rss+xml"
        self.response.set_status( 200 );
        
        # template replacement & rendering
        page = self._template.substitute( params )
        self.response.out.write( page )

#/**
# *  Initialize http handler
# */
def main():
  application = webapp.WSGIApplication([('/services/feedburner/aw/(.*?)', ServiceAw)], debug=True)
  wsgiref.handlers.CGIHandler().run(application)

# Bootstrap
#  It all starts here
if __name__ == "__main__":
    main()
    
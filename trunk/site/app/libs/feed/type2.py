#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: type2.py 871 2009-03-10 01:34:52Z JeanLou.Dupont $"

from string import Template
from libs.template import ExTemplate

class FeedRss(object):
    """ Base class for feeds.
        Works in two steps:
        1) use 'prepare' method for substituting with ^^ escape
        2) use 'produce' method for substituting with $ escape
    """
    def __init__(self, base_feed_template, base_item_template):
        self.base_feed_template = base_feed_template
        self.base_item_template = base_item_template
        
        self.feed_template = None
        self.item_template = None
        
    def prepare(self, feed_params, item_params):
        """ Performs the base preparation of the templates.
            The parameters preceded with ^^ escape sequence
            are substituted. 
        """
        f = ExTemplate( self.base_feed_template ).substitute(feed_params)
        self.feed_template = Template(f)
        
        i = ExTemplate( self.base_item_template ).substitute(item_params)
        self.item_template = Template(i)
        
    
    def produce(self, feed_params, item_params_list):
        """
            feed_params: dictionary of feed level parameters
            item_params_list: list of dictionaries, each entry corresponds to an item in the feed
        """        
        result = ''
        for params in item_params_list:
            result = result + self.item_template.substitute( params )
           
        feed_params['items'] = result
        feed = self.feed_template.substitute( feed_params ) 
        
        return feed


# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    feed_template = """<?xml version="1.0" encoding="UTF-8" ?>
    <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
        <channel>
            <atom:link href="^^feedLink" rel="self" type="application/rss+xml" />
            
            <title>^^feedTitle</title>
            <description>^^feedDescription</description>
            <link>^^feedLink</link>
    
            $items
    
        </channel>
    </rss>
    """
    
    item_template = """
            <item>
                <title>^^itemTitle</title>
                <description>$itemDescription</description>
                <link>^^itemLink</link>
                <pubDate>$itemPubDate</pubDate>
                <guid isPermaLink='false'>$itemGUID</guid>
            </item>
    """
    
    f=FeedRss( feed_template, item_template )
    base_feed = {'feedLink':"Feed Link", 'feedTitle':"Feed Title", 
                 'feedDescription':'Feed Description' }
    base_item = {'itemTitle':'Item Title', 'itemLink':'Item Link'}
    
    f.prepare( base_feed, base_item )
    item = {'itemDescription':'Item Description', 'itemPubDate':'Item Publication Date',
            'itemGUID':'Item GUID'}
    items = [item,]
    
    print f.produce({}, items)
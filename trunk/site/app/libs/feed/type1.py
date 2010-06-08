#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: type1.py 871 2009-03-10 01:34:52Z JeanLou.Dupont $"

from string import Template

import rss as rss

class MetaFeedRss(type):
    ""
    def __init__(cls, name, bases, ns):
        cls.feed_template = Template( rss.feed_template )
        cls.item_template = Template( rss.item_template )

class FeedRss(object):
    """ Base class for feeds
    """
    __metaclass__ = MetaFeedRss
    
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
    f = FeedRss()
    fp = {'feedLink': "feed link", 'feedTitle':"Feed Title", 'feedDescription':"Feed Description"}
    item = {"itemTitle":"Item Title","itemDescription":"Item Description", 
            "itemLink":"Item Link", 'itemPubDate':'Item Publication Date',
            "itemGUID":"Item GUID"}
    fi = [item,]
    
    print f.produce(fp, fi)
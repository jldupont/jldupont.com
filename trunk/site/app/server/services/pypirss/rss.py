#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: rss.py 881 2009-03-11 22:12:52Z JeanLou.Dupont $"

__all__ = ['prepareFeedTemplate',]

from string import Template
from libs.feed.type0 import FeedRss

_feed_template = """<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <atom:link href="http://www.jldupont.com/services/pypirss/rss/$package" rel="self" type="application/rss+xml" />
        
        <title>Pypi Statistics for Package [$package]</title>
        <description>RSS feed for PYPI package [$package]</description>
        <link>http://www.jldupont.com/services/pypirss/rss/$package</link>

        $items

    </channel>
</rss>
"""

_item_template = """
        <item>
            <title>Pypi statistics for [$package]</title>
            <description>Release[$release] Downloads[$downloads]</description>
            <link>http://pypi.python.org/pypi/$package</link>
            <pubDate>$itemPubDate</pubDate>
            <guid isPermaLink='false'>$itemGUID</guid>
        </item>
"""

def prepareFeedTemplate():
    return FeedRss( Template(_feed_template), 
                    Template(_item_template) )

#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: rss.py 867 2009-03-09 17:43:46Z JeanLou.Dupont $"

__all__ = ['feed_template', 'item_template']

feed_template = """<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <atom:link href="$feedLink" rel="self" type="application/rss+xml" />
        
        <title>$feedTitle</title>
        <description>$feedDescription</description>
        <link>$feedLink</link>

        $items

    </channel>
</rss>
"""

item_template = """
        <item>
            <title>$itemTitle</title>
            <description>$itemDescription</description>
            <link>$itemLink</link>
            <pubDate>$itemPubDate</pubDate>
            <guid isPermaLink='false'>$itemGUID</guid>
        </item>
"""
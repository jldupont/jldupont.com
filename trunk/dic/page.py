import os

import xml.dom.minidom
from xml.dom.minidom import Node

class Page(object):
    """
        Simple abstraction for an xml based entry
        in Wiktionary database dump
    """
    
    def __init__(self, string):
        self.doc = xml.dom.minidom.parseString( string )
        
    def getTitle(self):
        title = self.doc.getElementsByTagName("title")
        return str( title )
    
    def getText(self):
        revision = self.doc.getElementsByTagName("revision")
        text = revision.getElementsByTagName('text')
        return str( text )
            
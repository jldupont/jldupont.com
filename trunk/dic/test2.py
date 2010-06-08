import sys
import os
import xml.sax

import PageParser

path = os.path.dirname( __file__ ) + '/output.csv'
fhandler = open( path, 'w' )

parser = xml.sax.make_parser(  )
handler = PageParser.PageHandler( fhandler )

parser.setContentHandler(handler)
parser.parse("wk.xml")

#Count: 759745, Skip: 10771
print "Count: %i, Skip: %i" % (handler.count, handler.skip)

fhandler.close()
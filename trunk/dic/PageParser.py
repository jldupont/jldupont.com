"""
    Wiktionary
    
    ==lang==
    ===type===
    
    {{en-noun}}
        
"""
import xml.sax.handler
import re

 
class PageHandler(xml.sax.handler.ContentHandler):
    """
        Assumes no child elements to the supported elements
    """
    _elements = ("title", "text")
    
    def __init__(self, fhandler):
        self.mapping = {}
        self.count = 0
        self.skip = 0
        self.doBuffering = False
        self.title = None
        self.fhandler = fhandler
 
    def startElement(self, name, attributes):
        self.buffer = ""
        if (name in PageHandler._elements):
            self.doBuffering = True
 
    def characters(self, data):
        if self.doBuffering:
          self.buffer += data
 
    def endElement(self, name):
        self.doBuffering = False
        
        if (name=="title"):
            if (self.buffer.find(':')==-1):
                try:    self.title = self.buffer.encode( 'latin-1' )
                except: self.title = None
            else:
                self.title = None
            return
        
        if name=="text":
            if (self.title is not None):                
                self.count += 1
                self.mapping[ name ] = self.buffer
                
                o = TextHandler.extract( self.buffer )
                if (len(o)):
                    try:
                        for i in o:
                            if (i[0].find(',') == -1 and i[1].find(',')==-1 ):
                                ligne = "%s, %s, %s\n" % ( self.title, i[0], i[1] ) 
                                print ligne
                                self.fhandler.write( ligne )
                            else:
                                self.skip = self.skip +1
                        #print "%s, %s" % (self.title, str(o) )
                    except:
                        self.skip = self.skip +1

class TextHandler(object):
    
    _blacklist= {u'Etymology':True,     u'Pronunciation':True, 
                u'Translations':True,   u'Abbreviation':True, 
                u'Symbol':True,         u'Translingual': True,
                u'Synonyms': True,      u'Antonyms': True,
                u'Quotations': True,    u'Hyphenation': True,
                u'Conjugation': True,   u'References': True,
                u'Particle': True,      u'Anagrams': True,
                u'Declension': True,    u'Interlingua': True,
                u'Homophones': True,    u'Compounds': True,
                u'Etymology1': True,    u'Etymology2': True, 
                u'Trivia':True,         u'Shorthand':True,
                u'Gregg':True,          u'Preposition':True,
                }
    
    _langlist= {u'english': True,   u'french': True, 
                u'italian':True,    u'spanish': True,
                u'finnish':True,    u'swedish':True, 
                u'german': True,    u'kurdish':True,
                u'croatian':True,   u'afrikaans':True,
                u'czech':True,      u'catalan':True,
                u'irish':True,      u'dutch':True,
                u'latin':True,      u'japanese':True,
                u'bosnian':True,    u'turkish':True,
                u'portuguese':True, u'serbian':True,
                u'hungarian':True,  u'romanian':True,
                u'esperanto':True,  u'egyptian':True,
                u'polish':True,     u'danish':True,
             }
    
    _lang = re.compile( "==(\w+)=="   )
    _type = re.compile( "===(\w+)===" )
    
    @staticmethod
    def extract(input):
        #split by newline
        t = input.split("\n")

        output = []
        currentLang = None
        for line in t:
            l = TextHandler._lang.search( line )
            t = TextHandler._type.search( line )
            if (t is not None):
                f = TextHandler.filter( t.group(1) )
                if (f is not None):
                    output.append( (currentLang, f) )
                    continue
            
            if (l is not None):
                e = TextHandler.filter( l.group(1) )
                if (e is not None):
                    currentLang = e
                    
                                    
        return output
            
    @staticmethod
    def filter(input):
        try:
            TextHandler._blacklist[input]
            return
        except:
            return input
        
            
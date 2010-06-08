""" Gliffy list pretty printer
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: gliffy_printer.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

import jld.tools.printer as printer

class Gliffy_Printer(printer.BasePrettyPrinter):
    """ Base class """
    def __init__(self, msgs):
        printer.BasePrettyPrinter.__init__(self)
        self.msgs = msgs
        
    def table_header(self, tpl_item = None):
        """Prints a table header"""
        result = ''
        for field in self._fields:
            result = result + field + ' , '
        
        print '==='    
        print result.rstrip(' ,')
        print '==='
    
    def line(self, entry):
        """Prints one line"""
        result = ''
        for field in self._fields:
            if (field in entry):
                f = entry[field]
                v = f if f else ''
                result = result + unicode( v ) + ' , '
            
        print result.rstrip(' ,') 
       
class Gliffy_Printer_Diagrams(Gliffy_Printer):
    """ Prints tags related info
    """
    _fields = [ 'did', 'added', 'exported' ]

    def __init__(self, msgs):
        Gliffy_Printer.__init__(self, msgs)

    def header(self):
        """Prints a header"""
        print self.msgs.render( 'tbl_header_diagrams' )

    def footer(self):
        """Prints a footer"""
        print self.msgs.render( 'tbl_footer_diagrams' )
    
class Gliffy_Printer_Config(Gliffy_Printer):
    """ Prints config related info
    """
    
    def __init__(self, msgs, obj):
        Gliffy_Printer.__init__(self, msgs)
        self._fields = getattr(obj, '_configParams')

    def header(self):
        """Prints a header"""
        print self.msgs.render( 'tbl_header_config' )

    def footer(self):
        """Prints a footer"""
        print self.msgs.render( 'tbl_footer_config' )

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """

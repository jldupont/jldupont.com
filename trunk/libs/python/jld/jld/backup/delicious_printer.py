""" Delicious list pretty printer
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: mindmeister_printer.py 708 2008-12-04 13:40:35Z JeanLou.Dupont $"
__msgs__ = ['tbl_header_maps', 'tbl_footer_maps', 'tbl_header_export', 'tbl_footer_export', ]

import jld.tools.printer as printer

class Delicious_Printer(printer.BasePrettyPrinter):
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
       
class Delicious_Printer_Posts(Delicious_Printer):
    """ Prints tags related info
    """
    _fields = [ 'href', 'tag', 'tag1', 'tag2', 'tag3' ]

    def __init__(self, msgs):
        Delicious_Printer.__init__(self, msgs)

    def header(self):
        """Prints a header"""
        print self.msgs.render( 'tbl_header_posts' )

    def footer(self):
        """Prints a footer"""
        print self.msgs.render( 'tbl_footer_posts' )
    
class Delicious_Printer_Config(Delicious_Printer):
    """ Prints config related info
    """
    
    def __init__(self, msgs, obj):
        Delicious_Printer.__init__(self, msgs)
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

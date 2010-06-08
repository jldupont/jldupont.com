""" MindMeister Maps list pretty printer
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: mindmeister_printer.py 708 2008-12-04 13:40:35Z JeanLou.Dupont $"
__msgs__ = ['tbl_header_maps', 'tbl_footer_maps', 'tbl_header_export', 'tbl_footer_export', ]

import jld.tools.printer as printer

class MM_Printer(printer.BasePrettyPrinter):

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
                result = result + str( v ) + ' , '
            
        print result.rstrip(' ,') 
       
class MM_Printer_Maps(MM_Printer):
    """ Prints map related info
    """
    _fields = [ 'mapid', 'title', 'exported' ]

    def __init__(self, msgs):
        MM_Printer.__init__(self, msgs)

    def header(self):
        """Prints a header"""
        print self.msgs.render( 'tbl_header_maps' )

    def footer(self):
        """Prints a footer"""
        print self.msgs.render( 'tbl_footer_maps' )
    
            
class MM_Printer_Export(MM_Printer):
    """ Prints map export related info
    """
    _fields = [ 'image/png', 'freemind' ]
    
    def __init__(self, msgs):
        MM_Printer.__init__(self, msgs)

    def header(self):
        """Prints a header"""
        print self.msgs.render( 'tbl_header_export' )

    def footer(self):
        """Prints a footer"""
        print self.msgs.render( 'tbl_footer_export' )

    def line(self, entry):
        """Prints one line"""
        result = ''
        for field in self._fields:
            if (field in entry):
                f = entry[field]
                v = f if f else ''
                print v.rstrip(' ,') 


class MM_Printer_Config(MM_Printer):
    """ Prints map related info
    """
    
    def __init__(self, msgs, obj):
        MM_Printer.__init__(self, msgs)
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

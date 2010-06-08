#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: exception.py 881 2009-03-11 22:12:52Z JeanLou.Dupont $"

__all__ = ['ExceptionHandler',]

import logging
from string import Template

class ExceptionHandler( object ):
    """ Handles exceptions:
        - message lookup
        - template filling
        - template rendering
    """
    def __init__(self, messages, template, output=None, 
                 template_class = Template,
                 logger_function = logging.debug ):
        """
            messages: string dictionary
            template: string template
            output:   output function
            template_class: string template class
                            useful when, for example, the delimiter must be changed.
        """
        self.messages = messages
        self.template = template_class( template )
        self.output = output
        self.logger_function = logger_function
        
    def setOutput(self, output):
        self.output = output
        
    def handleException(self, exc, add_params={}, output=None):
        """ Exception Handler
        
            add_params: additional parameters, normally template specific
        """
        # extract msg_id and template parameters
        msg_tpl = self._getMessageFromId( exc.message )
        params  = exc.params if hasattr(exc, 'params') else {}

        # Combine the dictionaries
        params.update( add_params )

        if msg_tpl:
            # First, render the message string
            msg = Template( msg_tpl ).safe_substitute( params )
            
            # insert the message in the template parameters
            params['msg'] = msg
            
            # render template... but don't crash if we are missing 
            # some parameters
            res    = self.template.safe_substitute( params )
        else:
            res = str(exc)
        
        # send complete message to output handler
        self._doOutput(res, output)
        
        # help debugging process
        self._processForMissingParameters( exc, res )

    def _doOutput(self, res, output=None):
        """ Performs the output through the 'out' method
            of the specified 'output' object. If a
            "after_out" method is present, it is called-back.
        """
        if output is not None:
            output.out( res )
            cb = output
        else:
            self.output.out( res )
            cb = self.output
        
        if hasattr(cb, 'after_out'):
            cb.after_out()    

    def _getMessageFromId(self, msg_id):
        """ Retrieves, if possible, a message text
            from a supplied msg_id.
        """
        return self.messages.get(msg_id, '')

    def _processForMissingParameters(self, exc, buffer):
        """ Logs missing parameters from buffer
            i.e. looks for unfilled template delimiters
            
            The matching process is crude and could be
            improved... but let's not get too fancy!
        """
        delimiter = self.template.delimiter
        try:    found=buffer.find( delimiter )
        except: found=-1
        if found <> -1:
            self.logger_function("Template parameter(s) missing: exception class[%s] message[%s]" % (type(exc), str(exc)))
            
# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    class MyException(Exception):
        def __init__(self, msg, params=None):
            Exception.__init__(self, msg)
            self.params = params

    messages = {'error_mine':"This is my error[$error] [$missing]"}
    template = """<Template>$msg  [$additional]</Template>""" 
    
    class MyOutputter(object):
        def out(self, msg):
            print msg
        def logger(self, msg):
            print "logging: %s" % msg
            
    
    def tests():
        """
        >>> e = MyException('error_mine',{'error':'stupid'})
        >>> o = MyOutputter()
        >>> h = ExceptionHandler(messages,template,o, logger_function=o.logger)
        >>> h.handleException(e,{'additional':'some-additional'})
        <Template>This is my error[stupid] [$missing]  [some-additional]</Template>
        logging: Template parameter(s) missing: exception class[<class '__main__.MyException'>]
        """ 
    
    import doctest
    doctest.testmod()

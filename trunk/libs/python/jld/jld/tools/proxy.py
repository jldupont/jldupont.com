#!/usr/bin/env python
""" Proxy metaclass
    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: proxy.py 890 2009-03-19 13:35:50Z JeanLou.Dupont $"


__all__ = ['ProxyCallback', 'ProxyBaseClass']


        
class ProxyCallback(object):
    """ Proxy callback helper class
    """
    __all__ = ['callback','event']
    
    def __init__(self, callback, event):
        self.callback = callback
        self.event    = event

    def __call__(self, *pargs, **kargs):
        return self.callback( self.event, *pargs, **kargs)




class ProxyBaseClass(object):
    """ Proxy base class

        This class serves as a 'proxy' between 
        events (method calls) and a receiving target. 
        
        The events are trapped through the use of the 
        magic method __getattr__: a prefix can
        be configured for these methods.    
    """
    def __init__(self, target, prefix = 'event_'):
        self.target = target
        self.prefix = prefix
               
    def __getattr__(self, name):
        """ Traps the events (method calls)
        
            Performs verification against the configured prefix.
        """
        if not name.startswith(self.prefix):
            raise AttributeError('ProxyBaseClass: name[%s] undefined' % name)
        
        return ProxyCallback(self.target, event=name)

    def wireEventSources(self, source, liste):
        """ Wires the event sources to this proxy
        
            If this class is used to contain an object which
            generates events, the said events can be configured
            through this method to point to the proxy.
            
            The parameter 'liste' is defined:
                [(attachMethodName1, eventName), (attachMethodName2, eventName), ...]
        """
        for attachMethodName, eventName in liste:
            # retrieve attach method
            methodInstance  = self._getMethodInstance(source, attachMethodName)
            
            # generate event method instance local to this proxy
            eventMethodName = self.prefix + eventName
            eventMethodInstance = self._getMethodInstance(self, eventMethodName)
            
            # wire the event source to the proxy
            try:
                methodInstance( eventMethodInstance )
            except:
                raise RuntimeError('ProxyBaseClass: exception whilst trying to wire source[%s] with event[%s]' % (type(source), eventName) ) 
            
        
    def _getMethodInstance(self, source, name):
        try:
            methodInstance = getattr(source, name)
        except:
            raise RuntimeError("ProxyBaseClass: can't find attach method[%s]" % name)
        
        return methodInstance



# ==============================================
# ==============================================



if __name__ == "__main__":
    """ Tests
    """

    class TestProxy(ProxyBaseClass):
        def __init__(self, target):
            ProxyBaseClass.__init__(self, target)
            
    def TestTarget(event, *pargs, **kargs):
        print "TestTarget: event[%s] pargs[%s] kargs[%s]" % (event, pargs, kargs)
    
    
    def tests1():
        """
        >>> p = TestProxy( TestTarget )
        >>> p.event_A()
        TestTarget: event[event_A] pargs[()] kargs[{}]
        >>> p.event_B('p_param1')
        TestTarget: event[event_B] pargs[('p_param1',)] kargs[{}]
        >>> p.event_C(kparam1="kparam1")
        TestTarget: event[event_C] pargs[()] kargs[{'kparam1': 'kparam1'}]
        """
    
    # ===========================================================================
    # ===========================================================================
    
    
    
    
    class TestEventSource(object):
        def __init__(self):
            self.table = {}
        
        def attachEventA(self, callback):
            self.table['a'] = callback

        def attachEventB(self, callback):
            self.table['b'] = callback

        def __call__(self, event, *pargs, **kargs):
            return self.table[event](*pargs, **kargs)
            
    
    class TestProxy2(ProxyBaseClass):
        
        liste = [('attachEventA', 'a'),('attachEventB','b')]
        
        def __init__(self, target):
            ProxyBaseClass.__init__(self, target)
            self.source = TestEventSource()
            self.wireEventSources(self.source, self.liste)
            
        def generate(self, event, *pargs, **kargs):
            return self.source(event, *pargs, **kargs)
    

    
    def tests2():
        """
        >>> p2 = TestProxy2( TestTarget )
        >>> p2.generate('a')
        TestTarget: event[event_a] pargs[()] kargs[{}]
        >>> p2.generate('a', "param1")
        TestTarget: event[event_a] pargs[('param1',)] kargs[{}]
        >>> p2.generate('b', kparam1="kparam1")
        TestTarget: event[event_b] pargs[()] kargs[{'kparam1': 'kparam1'}]
        """
    
    import doctest
    doctest.testmod()

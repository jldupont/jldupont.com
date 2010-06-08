class A:
    def m(self, *args, **keys):
        
        print "args length= %i" % len(args)
        print "keys length= %i" % len(keys)
        
        for a in args:
            print "a=%s" % a
        for i in keys:
            print "i=%s" % i


class Test(object):
    
    def __init__(self, p1=None, p2=None, **kwargs):
        
        self.__dict__.update( kwargs )


def test1():
    """
    >>> t=Test()
    >>> t.__dict__
    {}
    >>> t2=Test(p1="v1")
    >>> t2.__dict__
    {}
    >>> t3=Test(other="other")
    >>> t3.__dict__
    {'other': 'other'}
    """

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import doctest
    doctest.testmod(optionflags=doctest.ELLIPSIS)
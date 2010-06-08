class X:
    def m(cls, p):
        print "X: cls=%s , p=%s" % (cls,p)

    # required for X.m() to work
    m = classmethod(m)
    
    
class Y(X):
    def m(cls,p):
        print "Y: cls=%s , p=%s" % (cls,p)
        X.m(p)
        
    # required for X.m() to work
    m = classmethod(m)
        
if (__name__ == "__main__" ):
    x=X()
    x.m(666)
    X.m(999)
    print ""
    y=Y()
    print "y.m(333): %s" % y.m(333)
    print "Y.m(555): %s" % Y.m(555)
    
"""
cls=__main__.X , y=666
cls=__main__.X , y=999
"""


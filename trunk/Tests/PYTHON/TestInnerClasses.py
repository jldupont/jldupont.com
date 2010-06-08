"""
    Test Inner Classes
"""

class X:
    def __init__(self):
        print "X::__init__"
        
    class Y(object):
        def __init__(self):
            print "Y::__init__"
            


if (__name__ == "__main__" ):
    x = X()
    x.Y()
    
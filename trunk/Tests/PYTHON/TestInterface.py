class A:
    def __init__(self):
        print "A.__init__"

class B:
    def __init__(self):    
        print "B.__init__"

class X(A,B):
    def __init__(self):
        A.__init__(self)
        B.__init__(self)

if (__name__ == "__main__" ):
    x=X()
    
    
"""
A.__init__
B.__init__
"""

"""
    Instance level attribute
    takes precedence over
    Class level attribute
"""

class Test(object):
    
    def method1(self, p1):
        print "method1"
        
class Test2(object):
    field = 'class level field'
    field2 = 'class level field2'
    
    def __init__(self):
        self.field = 'instance level field'
        
        
if (__name__ == "__main__" ):
    t= Test()
    
    print t.method1.im_class.__name__
    #print t.method1.__class__
    
    t2 = Test2()
    print t2.field
    
    print Test2.field
    
    print t2.field2
    
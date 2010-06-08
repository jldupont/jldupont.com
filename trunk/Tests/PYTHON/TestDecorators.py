from decorator import decorator

def debug(target, *a, **k):
    print type(target)
    print a
    print k
    
    def my_wrapper(target, *args, **kwargs):
        print "args: " + str( args )
        print "kwargs: " + str( kwargs )
        return target( *args, **kwargs )
    
    return decorator(my_wrapper)(target)

class Test(object):
    
    def __init__(self):
        print "__init__"
        self.attribute1 = 'init done'
    
    @debug
    def method1(self, param1, param2 = 0 ):
        print "method1:"
        print "param1[%s] param2[%s] attribute1[%s]" % (param1, param2, self.attribute1)
        

if (__name__ == "__main__" ):
    t = Test()
    t.method1( 'just a string', param2 = 'second parameter' )
    
   
result = """
<type 'function'>
()
{}
__init__
args: (<__main__.Test object at 0x00B62550>, 'just a string', 'second parameter')
kwargs: {}
method1:
param1[just a string] param2[second parameter] attribute1[init done]
"""

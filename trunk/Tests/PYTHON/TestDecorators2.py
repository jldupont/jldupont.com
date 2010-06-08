from decorator import decorator

def debug(target, *a, **k):
    print type(target)
    print "name: %s" % target.__name__
    #print a
    #print k
    
    def wrapper(target, *aw, **awk ):
        print "wrapper aw:"
        print aw[1]
        print "wrapper awk:"
        print awk
        return target(*aw, **awk)
    
    return decorator(wrapper)(target)
    
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
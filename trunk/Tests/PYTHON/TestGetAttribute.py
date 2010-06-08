class X(object):
    def __getattribute__(self, attr, **args):
        print "method: %s" % attr
        
        def generic(**args):
            print str(args)
            
        return generic
        
if __name__ == "__main__":
    x=X()
    
    x.any_method(a=1,b=2,c=3)
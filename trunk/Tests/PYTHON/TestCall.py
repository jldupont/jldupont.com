class X(object):
    
    def __call__(self, *args):
        print "X::__call__"
        
class Y(X):
    pass

if __name__=="__main__":
    y=Y()
    
  
    y.does_not_exists()
    
    
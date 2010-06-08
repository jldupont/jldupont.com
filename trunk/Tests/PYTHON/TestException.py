
import sys

def test():
    while True:
        try:
            raise Exception("the exception!")
        except:
            raise RuntimeError('caught exception!')
        
        
try:
    test()
except Exception,e:
    print ">%s" % str(e)
    sys.exit(1)
    
sys.exit(0)

"""
 Test for the __new__ attribute
"""
import sys

class Test(object):
    def __new__(cls):
        print "__new__ called with cls[%s]" % (cls);
        
        
if __name__ == "__main__":
  t = Test()
  
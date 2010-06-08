"""
 Test for the class initialization
"""
import sys

class Test():
    def __init__(self):
        print "__init__ called";
        
    
    print "end of class definition"
        
        
if __name__ == "__main__":
  t = Test()
  
    
"""
Result:
    
end of class definition
__init__ called
   
"""

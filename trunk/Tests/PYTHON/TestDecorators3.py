"""
    @author: Jean-Lou Dupont
"""
from types import *

def makelist(target):
    def wrapper(*arg):
        obj = arg[0]
        if (type(obj) is not ListType):
            obj = [obj,]
        return target(obj)
    return wrapper
        
@makelist
def test(obj):
    for o in obj:
        print "item: %s" % o

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    not_liste = "not_liste"
    liste = ["key1", "key2"]
    
    test(not_liste)
    test(liste)
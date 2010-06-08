class Test:
    def __init__(self):
        print "__init__ called"
        
    def __invert__(self):
        print "__invert__ called"
        
if (__name__ == "__main__" ):
    t = Test()
    ~t

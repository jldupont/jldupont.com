class Y(object):
    
    def __init__(self):
        self.container = {}
    
    def __missing__(self, key):
        print "missing key[%s]" % key
    
    def __getitem__(self, key):
        print "getitem key[%s]" % key
        return self.container[key]
    
    def __setitem__(self, key, value):
        print "setitem key[%s] value[%s]" % (key,value)
        self.container[key] = value
        
    def __contains__(self, key):
        print "contains key[%s]" % key
        return (key in self.container)
    
if __name__ == "__main__":

    y=Y()
    y['key1']='value1'
    
    print y['key1']
    
    print ('key2' in y)
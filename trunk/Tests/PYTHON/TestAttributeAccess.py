import random

class X(object):
    def __get__(self, object, var):
        print "class [%s].__get__ var[%s]" % (self.__class__, var)
       
    def __getattr__(self, var): 
        print "class [%s].__getattr__ var[%s]" % (self.__class__, var)
        if (var == 'info'):
            raise AttributeError()
            
class Y(object):
    def __getattr__(self, var): 
        print "class [%s].__getattr__ var[%s]" % (self.__class__, var)
    
    def __getattribute__(self, var): 
        #print "class [%s].__getattribute__ var[%s]" % (self.__class__, var)
        return object.__getattribute__(self, var)
         
class Z(object):
    def __init__(self, var):
        self.var = var
    
    def __get__(self, instance, owner):
        print "class [%s].__get__ instance[%s] owner[%s]" % (self.__class__, instance, owner)
        return "Z with var[%i]" % self.var
    
class Game(object):
    z1 = Z(1)
    z2 = Z(2)
    
    def __init__(self, var):
        self.var = var
        
    def __repr__(self):
        return "Game with var[%i]" % self.var
    
class A(object):
    
    def __init__(self):
        self.var = random.random()
        
    def __getattribute__(self, attr):
        """Gets ALWAYS called, even for methods
        """
        print "class A.__getattribute__ attr[%s]" % attr
        return object.__getattribute__(self, attr)
    
    def showVar(self):
        """ Does this trigger __getattribute__ ? YES """
        print "->var=" + str( self.var )
        
    
if __name__ == "__main__":
    x = X()
    x.x
        
    y = Y()
    y.y
        
    print Game.z1
    print Game.z2
    
    g = Game(666)
    print g.z1
    print g.z2
    
    print Game(777).z1
    print Game(888).z2
    print "---------------"
    a = A()
    print "---------------"
    a.showVar()
    print "---------------"
    print a.var
    print "---------------"
    a.some_nonexistent_method()
    
    
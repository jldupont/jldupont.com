"""
Delicious/post
@author: Jean-Lou Dupont

GET /https://api.del.icio.us/v1/posts/get

<?xml version='1.0' standalone='yes'?>
<posts dt="2008-07-27" tag="" user="jldupont">
  <post href="http://code.google.com/appengine/docs/urlfetch/fetchfunction.html" 
        description="The fetch Function - Google App Engine - Google Code" 
        extended="  Annotated link http://www.diigo.com/bookmark/http%3A%2F%2Fcode.google.com%2Fappengine%2Fdocs%2Furlfetch%2Ffetchfunction.html" 
        hash="d316d09707bdbc3d5ebcb9d8625326de" 
        others="2" 
        tag="no_tag" 
        time="2008-07-27T01:55:23Z" />
        
  <post href="http://docs.python.org/tut/node7.html" 
        description="5. Data Structures" 
        extended="  Annotated link http://www.diigo.com/bookmark/http%3A%2F%2Fdocs.python.org%2Ftut%2Fnode7.html" 
        hash="071baa9c494c6f44886a69262c981455" 
        others="81" 
        tag="no_tag" 
        time="2008-07-27T01:52:47Z" />
</posts>

"""

from Delicious import Tag

class Post(Object):
    """
    Delicious/Post
    Represents a "post" object from Delicious API
    """
    def __init__(self):
        Object.__init__(self)
        self.href = ""
        self.description = ""
        self.extended = ""
        self.hash = ""
        self.others = ""
        self.tag = Tag()
        self.time = ""
        
        
if (__name__ == "__main__" ):
    p = Post()
    
"""
Delicious/tag
@author: Jean-Lou Dupont

GET /https://api.del.icio.us/v1/tags/get

<?xml version='1.0' standalone='yes'?>
<tags>
  <tag count="1" tag=".net" />
  <tag count="23" tag="API" />
  <tag count="1" tag="AmazonWebServices" />
  <tag count="1" tag="BT" />
  <tag count="1" tag="Brain" />
  <tag count="1" tag="Bugzilla" />
  <tag count="6" tag="CDN" />
  <tag count="3" tag="DNS" />
</tags>

"""

class Tag(Object):
    """
    Delicious/Tag
    Represents a "tag" object from Delicious API
    """
    def __init__(self):
        Object.__init(self)
        self.tag = ""
        self.count = 0
        
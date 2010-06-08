import os

from mako.template import Template
from mako.lookup import TemplateLookup

_dir = os.path.dirname( __file__ )

mylookup = TemplateLookup(directories=_dir)

file = open('tpl.html')
text = file.read()
file.close()

mytemplate = Template(text=text, lookup=mylookup)
print mytemplate.render(name="Jean-Lou")

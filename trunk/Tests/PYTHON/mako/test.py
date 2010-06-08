import os

from mako.template import Template
from mako.lookup import TemplateLookup

_dir = os.path.dirname( __file__ )

mylookup = TemplateLookup(directories=_dir)

mytemplate = Template(filename="tpl.html", lookup=mylookup)
print mytemplate.render(name="Jean-Lou")

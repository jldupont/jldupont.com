""" Jean-Lou Dupont's Python Library
    EGG setup
    
"""
_DEBUG = False

import sys
import os.path
from setuptools import setup, find_packages

this_file_path = os.path.abspath( __file__ )
this_path = os.path.dirname( this_file_path  )
sys.path.append( this_path )

import jld as jld

version = jld.__version__
egg_name = 'jld-%s-py2.5.egg' % version

#Find /tags by recursing downwards
path = os.path.abspath( __file__ )
while True:
    path = os.path.dirname( path )
    tags_path = path + os.sep + 'tags'
    if (os.path.exists(tags_path)):
        break

#Base URL
base_url = 'http://jldupont.googlecode.com/svn/tags/eggs/%s'

#Compute source path of egg
#==========================
source_egg_path = this_path + os.sep + 'dist' + os.sep + egg_name 
dest_egg_path = tags_path + os.sep + 'eggs'
 
#Compute documentation path & URI
#================================
doc_fragment = 'doc/%s/index.html' % version
doc_path = tags_path + os.sep + 'eggs' + os.sep + 'doc' + os.sep + version
doc_url = base_url % doc_fragment
doc_path_latest = os.path.join(tags_path, 'eggs', 'doc', 'latest')

URL = 'http://jldupont.googlecode.com/svn/tags/eggs/%s' % egg_name

print "URL: %s" % URL

_packages = ['jld',
             'jld.api', 
            'jld.backup', 
            'jld.cmd', 
            'jld.cmd_g2',
            'jld.cmd_g2.transmission',
            'jld.registry',
            'jld.template',  
            'jld.tools'
            ]

_scripts = [ 
            'jld/backup/scripts/mm.py',
            'jld/backup/scripts/mm.bat', 
            'jld/backup/scripts/mm',
            
            'jld/backup/scripts/dlc.py',
            'jld/backup/scripts/dlc.bat', 
            'jld/backup/scripts/dlc',
            
            'jld/backup/scripts/glf.py',
            'jld/backup/scripts/glf.bat', 
            'jld/backup/scripts/glf', 
            
            'jld/cmd/pypre.py',
            'jld/cmd/nsvn.py',
            
            'jld/scripts/trns.py',            
            ]

_dependencies = []
for p in _packages:
    print "Processing package[%s] for dependencies" % p
    __import__(p)
    mod = sys.modules[p]
    deps = getattr(mod,'__dependencies__')
    if (len(deps)>0):
        _dependencies = _dependencies + deps

#some redundancies....
print "Dependencies:", 
print _dependencies

if (not _DEBUG):
    setup(
        name = "jld",
        description      = jld.__desc__,
        author_email     = jld.__email__,
        author           = jld.__author__,
        url              = jld.__doc_url__,
        long_description = jld.__long_desc__,
        version          = jld.__version__,
        package_data     = {'':['*.*']},
        packages         = _packages,
        scripts          = _scripts,
        classifiers      = jld.__classifiers__,
        install_requires = _dependencies,
        zip_safe         = False,
    )

import shutil

# Copy to tags directory
if (not _DEBUG):
    print 'copying to tags directory'
    shutil.copy(source_egg_path, dest_egg_path)

# Documentation
def gen_doc():
    #go one level down to please epydoc
    cur =  os.path.dirname( __file__ )
    jld = cur + os.sep + 'jld'
    os.chdir(jld)
    
    print 'generating documentation'
    pkgs = ' '.join( _packages )
    cmd = """epydoc.py --html -v --output="%s" %s""" % (doc_path, pkgs)
    os.system(cmd)

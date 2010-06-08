"""
    Jean-Lou Dupont's Python library
    
    Installation
    ============
      Use I{easy_install jld} to install this library.
    
    Command-Line Tools
    ==================
      The following tools will be installed as scripts:
       - pypre.py [preprocessor based on Mako]
       - nsvn.py  [nuke svn]
       - dlc.py   [delicious backup]
       - mm.py    [mindmeister backup]
       - glf.py   [gliffy backup]
       - trns.py  [BT Transmission auto-stop upon completion]
    
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "0.0.39"
__email__   = "python@jldupont.com"
__url__     = "http://www.jldupont.com/"
__desc__    = """ Jean-Lou Dupont's Python Library - WEB API & command line tools"""
__doc_url__ = "http://www.jldupont.com/doc/lib/jld/"

#
# must be in reStructuredText format (see http://docutils.sf.net/)
#
__long_desc__ = """

Content
=======

This library contains the following utilities:
  
* |M| MindMeister_ mindmaps Backup command line: for exporting mindmaps from MindMeister_ in FreeMind format
  
  * EventManager script interface
  
* |D| Delicious_ API & Backup command line utility

* |G| Gliffy_ API & Backup command line utility

* Command-Line tools

  * pypre_   (preprocessor based on Mako_ , e.g. useful for pre-processing Apache config files)
  
  * nsvn_    (nuke svn: removes svn directories, e.g. useful under Windows where .dot files are difficult to handle)
  
  * trns_    (Transmission Bittorent client e.g. useful for auto-stopping downloads & post-processing)
 
* Cross-platform registry (Windows: uses the win32 registry, Linux: uses a filesystem path) 

Updates
-------

Follow project news on |T| Twitter_ or subscribe to the |R| RSS_ feed.

Changelog
---------

**0.0.39**

* better exception handling for all command line tools: trns.py used to hang cron when web service wasn't available

**0.0.38**

* mm: Corrected missing information message when performing 'auth' command 

.. _trns: http://www.jldupont.com/doc/lib/jld/trns

.. _pypre: http://www.jldupont.com/doc/lib/jld/pypre

.. _nsvn: http://www.jldupont.com/doc/lib/jld/nsvn

.. _Mako: http://www.makotemplates.org/

.. _MindMeister: http://www.mindmeister.com/

.. _Gliffy: http://www.gliffy.com/

.. _Delicious: http://www.delicious.com/

.. _Twitter: http://twitter.com/jld_prj_news

.. _RSS: http://twitter.com/statuses/user_timeline/19252523.rss

.. |D| image:: http://www.jldupont.com/res/img/delicious.jpg
.. |G| image:: http://www.jldupont.com/res/img/gliffy.png
.. |M| image:: http://www.jldupont.com/res/img/mm.png
.. |T| image:: http://www.jldupont.com/res/img/twitter.png
.. |R| image:: http://www.jldupont.com/res/img/rss.png

"""

__dependencies__ = [] #listed throughout the individual modules

__classifiers__ = [
    'Development Status :: 3 - Alpha',
    'Intended Audience :: Developers',
    'License :: Public Domain',
    'Programming Language :: Python',
    'Topic :: Software Development :: Libraries :: Python Modules',
    'Operating System :: Microsoft :: Windows',
    'Operating System :: POSIX',
    ]

# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import os
    import webbrowser
    
    from docutils.core import publish_parts
    
    parts = publish_parts(source=__long_desc__, writer_name="html4css1")
    
    rendered_page = parts["html_title"] + parts["html_subtitle"] + parts["fragment"]

    cpath = os.path.dirname( __file__ )
    path = os.path.join(cpath, 'long_desc.html')
     
    file = open( path, "w" )
    file.write( rendered_page )
    file.close()

    url = "file://%s" % path
    webbrowser.open_new(url)

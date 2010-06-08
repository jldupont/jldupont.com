nsvn
=====
This command-line utility is useful for deleting an entire *svn* directory hierarchy from a root directory. 
This capability is especially appreciable under Windows since the the *dot* **.svn** files are not easy to manage. 
This component is part of the python library jld_.

Usage Example
-------------
By default, *nsvn* will *nuke* the **.svn** directories from the current directory topdown. 


 nsvn.py c:/some/directory
 
The **-F** option must be used to effect the deletion else only the potential target directory list will be displayed.

 nsvn.py -F
 

.. LINKS
.. =====
.. _jld: /doc/lib/jld/
.. _Mako: http://www.makotemplates.org/
.. _Apache: http://httpd.apache.org/

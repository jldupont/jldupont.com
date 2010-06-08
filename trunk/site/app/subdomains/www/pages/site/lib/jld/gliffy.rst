Gliffy Diagrams Backup Utility
==============================

This command-line utility serves to backup diagrams produced with the online diagramming tool Gliffy.com_.
This tool requires the companion dlc.py_ utility from the same library.
This component is part of the python library jld_.

Usage Example
-------------
The diagrams must be bookmarked through Delicious_ and tagged with a user chosen tag e.g. *my-diagrams* .
The diagrams must of course be public; use the *share* menu option under Gliffy.

Once the diagram is made public, open the link to the public web page of the diagram:
the link should resemble the pattern
 
 http://www.gliffy.com/publish/1468960/

The next step is to bookmark the diagram through Delicious with the chosen tag, in this case *my-diagrams* .

The backup process is easily then put in place:

 glf.py import my-diagrams
 
 glf.py export

 
Example diagram from the link above:

.. image:: http://www.gliffy.com/pubdoc/1468960/L.jpg



.. LINKS
.. =====
.. _jld: /doc/lib/jld/
.. _Gliffy: /doc/lib/jld/gliffy
.. _Gliffy.com: http://www.gliffy.com/
.. _dlc.py: /doc/lib/jld/delicious
.. _Delicious: http://www.delicious.com/

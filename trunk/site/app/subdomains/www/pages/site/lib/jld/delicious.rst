Delicious Bookmarks Backup Utility
==================================

This command-line utility serves 2 purposes:

1. backup the bookmarks
2. act as database for the Gliffy_ backup tool

This component is part of the python library jld_.

Usage Example
-------------
The *username* and *password* of the Delicious account must be configured. 
Once entered, these parameters are kept in the local registry.

 dlc.py -u USERNAME -p PASSWORD listconfig
 
As per the usage terms of Delicious, full database updates should be kept to a minimum.
To this end, the command *updatedb* allows for updating the database with the recent posts on a more frequent basis.

 dlc.py updatedb
 
Once a day, a complete synchronization can be performed:

 dlc.py updatedbfull
 

.. LINKS
.. =====
.. _jld: /doc/lib/jld/
.. _Gliffy: /doc/lib/jld/gliffy

Transmission Bittorent Client
-----------------------------

This command-line utility serves to post-process torrents once they are finished downloading.
It interfaces with the Bittorent client transmission_ through a JSON/HTTP interface.
The tool can *auto-stop* torrents and launch a *post-processing* script. 

This component is part of the python library jld_.

Usage Example
-------------
1. Configure the URI of the Transmission server's WEB interface

 trns.py -s SERVER_URI -p SERVER_PORT

2. Use the *reportstatus* command:

 trns.py -a -l -e reportstatus
 
The above command will *auto-stop* (-a option) any completed torrent, log the event (-l option)
and finally write a *.completed* file (-e option) in the download directory for each completed torrent.

.. LINKS
.. =====
.. _transmission: http://www.transmissionbt.com/
.. _jld: /doc/lib/jld/

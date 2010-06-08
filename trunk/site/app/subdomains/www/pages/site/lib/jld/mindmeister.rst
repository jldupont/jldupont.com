MindMeister Map Backup Utility
------------------------------

This command-line utility serves to backup *mindmaps* diagrams produced with the online diagramming tool MindMeister.com_.
This component is part of the python library jld_.

Usage Example
-------------
1. The first step is to acquire an API key through http://www.mindmeister.com/services/api.

2. Once the **API key** and **Secret** parameters are handy, an authorization token must be acquired through the command-line *mm.py*.
This is accomplished by using

 mm.py -s SECRET -k API_KEY auth

A web-browser window will open up and MindMeister will ask for credentials in order to authorize the application.

3. Next, the local database must be updated on a regular basis in order for the tool to export the mindmaps timely:

 mm.py updatedb
 
4. Finally, export the database according to the latest database update:

 mm.py export
 
Various other commands are available by using the *-h* option.

Usage when no web-browser is available
--------------------------------------

In some cases, a web-browser might not be available: this poses a problem since the authentication process required by 
MindMeister forces this. In order to use the tool, the *authorization token* can be acquired through another computer
equiped with a browser and the resulting *authorization token* can be fed to *mm.py* by:

 mm.py showauthtoken
 
The above command will list the *authorization token* from the setup through which the token was acquired, whilst:

 mm.py setauthtoken
 
will be used to configure the setup where the command-line tool will be hosted.  


.. LINKS
.. =====
.. _jld: /doc/lib/jld/
.. _Gliffy: /doc/lib/jld/gliffy
.. _MindMeister.com: http://www.mindmeister.com/
.. _dlc.py: /doc/lib/jld/delicious
.. _Delicious: http://www.delicious.com/


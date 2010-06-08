pypre
=====
This command-line utility is a *preprocessor* based on the Mako_ template engine. 
This component is part of the python library jld_.

Usage Example
-------------
I actually devised this utility to fill a need: manage my Apache_ configuration files more effectively.
I simply have to write *templates* such as this one::

	#Mediawiki with "pretty-url"
	<VirtualHost ``*``:80>
	   ServerName wiki.jldupont.com
	 
	   DocumentRoot /var/wiki1_13_3
	 
	   <% root="/var/wiki1_13_3" %>
	 
	   Alias /Favicon.ico ${root}/favicon.ico
	   Alias /favicon.ico ${root}/favicon.ico
	
	   #base URL
	   Alias /skins/    "${root}/skins/"
	   Alias /images/   "${root}/images/"
	   Alias /config/   "${root}/config/"
	   Alias /index.php "${root}/index.php"
	
	   RewriteEngine On

	   RewriteRule ^/robots.txt - [L]
	   RewriteRule ^/favicon.ico - [L]
	
	   #/wiki URL
	   RewriteRule ^/(images|skins)/ - [L]
	   RewriteRule ^/config/         - [L]
	   RewriteRule ^/(.+)$           /index.php?title=$1 [PT,L,QSA]
	
	   #base URL
	   RewriteRule ^/(images|skins)/     - [L]
	   RewriteRule ^/config/             - [L]
	   RewriteRule ^/(.+)$               /index.php?title=$1 [PT,L,QSA]
	</VirtualHost>

The above *template* file can thus be processed by **pypre**:

 pypre.py wiki.tpl > wiki
 

.. LINKS
.. =====
.. _jld: /doc/lib/jld/
.. _Mako: http://www.makotemplates.org/
.. _Apache: http://httpd.apache.org/

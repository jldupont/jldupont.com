"""
    Demultiplexes requests based on the sub-domain
    
    Defaults to 'www'

    @author Jean-Lou Dupont
"""
import os
import sys
import logging


### DO NOT MODIFY BELOW THIS LINE ###
### ----------------------------- ###

def main():
    root = os.path.split(__file__)[0]
    sys.path.insert(0, os.path.join(root, 'subdomains'))
    sys.path.insert(0, os.path.join(root, 'libs'))
    sys.path.insert(0, os.path.join(root, 'server'))

    host=os.environ["HTTP_HOST"]
    (subdomain,_sep, domain)=host.partition(".")
    if (_sep!="."):
        subdomain="www"

    try:
        #logging.info("subdomain: %s" % subdomain)
        #print sys.path
        handler=__import__("subdomains.%s" % subdomain, fromlist=["subdomains",])
        handler.main()
    except Exception,e:
        logging.error("subdomain not found: <%s> Exception<%s>" % (subdomain, str(e)))
        handler=__import__("subdomains.www", fromlist=["subdomains",])
        handler.main()

if __name__ == "__main__":
    main()


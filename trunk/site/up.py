#!/usr/bin/env python
import os

email = os.environ['GAE_EMAIL']
cmd = "/opt/google/appengine_1.3.4/appcfg.py update --email=%s app/" % email
os.system(cmd)

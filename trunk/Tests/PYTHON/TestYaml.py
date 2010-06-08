#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestYaml.py 852 2009-02-12 20:37:58Z JeanLou.Dupont $"

import yaml

# ==============================================
# ==============================================

y1 = """
american:
  - Boston Red Sox
  - Detroit Tigers
  - New York Yankees
national:
  - New York Mets
  - Chicago Cubs
  - Atlanta Braves
"""

y1s = yaml.load(y1)
print y1s
#{'american': ['Boston Red Sox', 'Detroit Tigers', 'New York Yankees'], 'national': ['New York Mets', 'Chicago Cubs', 'Atlanta Braves']}


y2 = """
- [name        , hr, avg  ]
- [Mark McGwire, 65, 0.278]
- [Sammy Sosa  , 63, 0.288]
"""
y2s = yaml.load(y2)
print y2s
#[['name', 'hr', 'avg'], ['Mark McGwire', 65, 0.27800000000000002], ['Sammy Sosa', 63, 0.28799999999999998]]

y3 = """
null: ~
true: boolean
false: boolean
string: '12345'
"""
y3s = yaml.load(y3)
print y3s
#{None: None, True: 'boolean', 'string': '12345', False: 'boolean'}


y4 = """
-
 device: default
 name: homemon
"""
y4s = yaml.load(y4)
print y4s
#[{'device': 'default', 'name': 'homemon'}]


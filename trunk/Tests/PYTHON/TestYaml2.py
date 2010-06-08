#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: TestYaml2.py 853 2009-02-13 01:27:18Z JeanLou.Dupont $"

import yaml

# ==============================================
# ==============================================

tests = [
#{'american': ['Boston Red Sox', 'Detroit Tigers', 'New York Yankees'], 'national': ['New York Mets', 'Chicago Cubs', 'Atlanta Braves']}
"""
american:
  - Boston Red Sox
  - Detroit Tigers
  - New York Yankees
national:
  - New York Mets
  - Chicago Cubs
  - Atlanta Braves
"""
,
"""
- [name        , hr, avg  ]
- [Mark McGwire, 65, 0.278]
- [Sammy Sosa  , 63, 0.288]
"""
,
"""
null: ~
true: boolean
false: boolean
string: '12345'
"""
,
#[{'device': 'default', 'name': 'homemon'}]
"""
-
 device: default
 name: homemon
""",
"""
-
  name: Mark McGwire
  hr:   65
  avg:  0.278
-
  name: Sammy Sosa
  hr:   63
  avg:  0.288
"""
,
"""
Mark McGwire: {hr: 65, avg: 0.278}
Sammy Sosa: {
    hr: 63,
    avg: 0.288
  }
"""
,
#{'hr': ['Mark McGwire', 'Sammy Sosa'], 'rbi': ['Sammy Sosa', 'Ken Griffey']}
"""
hr:
  - Mark McGwire
  # Following node labeled SS
  - &SS Sammy Sosa
rbi:
  - *SS # Subsequent occurrence
  - Ken Griffey
"""
,
#[{'item': 'Super Hoop', 'quantity': 1}, {'item': 'Basketball', 'quantity': 4}, {'item': 'Big Shoes', 'quantity': 1}]
"""
- item    : Super Hoop
  quantity: 1
- item    : Basketball
  quantity: 4
- item    : Big Shoes
  quantity: 1
"""
,
#{'homemon': 
#  {'inputs': 
#    {'I1': ['input1', 'open', 'close'], 'I0': ['input0', 'open', 'close'], 'I3': ['input3', 'open', 'close'], 'I2': ['input2', 'open', 'close'], 'I5': ['input5', 'open', 'close'], 'I4': ['input4', 'open', 'close'], 'I7': ['input7', 'open', 'close'], 'I6': ['input6', 'open', 'close']}, 
#   'outputs': {'O7': ['output7', False, True], 'O6': ['output6', False, True], 'O5': ['output5', False, True], 'O4': ['output4', False, True], 'O3': ['output3', False, True], 'O2': ['output2', False, True], 'O1': ['output1', False, True], 'O0': ['output0', False, True]}}, 'devices': {'default': 'homemon'}}
"""
# Naming physical devices
devices:
 default: homemon

# Naming I/O & associated states
# State list order: [0,1]
homemon:
 inputs: 
  I0: [input0, open, close]
  I1: [input1, open, close]
  I2: [input2, open, close]
  I3: [input3, open, close]
  I4: [input4, open, close]
  I5: [input5, open, close]
  I6: [input6, open, close]    
  I7: [input7, open, close]  
 outputs:
  O0: [output0, off, on]
  O1: [output1, off, on]
  O2: [output2, off, on]
  O3: [output3, off, on]
  O4: [output4, off, on]
  O5: [output5, off, on]
  O6: [output6, off, on]
  O7: [output7, off, on]
"""
,
#[['homemon', 'I0', 'input0'], ['homemon', 'I1', 'input1']]
"""
- [&D homemon, I0, input0]
- [*D, I1, input1]
"""
]
for test in tests:
    r = yaml.load(test)
    print r

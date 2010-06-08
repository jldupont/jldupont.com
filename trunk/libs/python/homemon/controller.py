#!/usr/bin/env python
""" Controller class for home_monitoring

    @author: Jean-Lou Dupont
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: controller.py 897 2009-03-28 00:18:03Z JeanLou.Dupont $"

import sys

try:
    import pyfse
except:
    raise RuntimeError( "homemon: requires package 'pyfse'. This package can be obtained @ Pypi (easy_install pyfse)" )
    

__all__ = ['HomeMonController',]


class HomeMonController(Controller):
    def __init__(self, table, actions):
        Controller.__init__(self, table, actions)

# (current_state, event) : (next_state, action)
# =============================================
_transition_table = {
         
    # Startup            
    ('', None): ('state_WAIT_ATTACH', None),

    # Waiting for Phidget Attach event
    ('state_WAIT_ATTACH','ikit_attach'):    'state_ATTACHED',

    # Phidget is attached... waiting for some changes
    ('state_ATTACHED', 'ikit_attach'):      'state_ATTACHED',
    ('state_ATTACHED', 'ikit_error'):       ('state_ERROR',),       
    ('state_ATTACHED', 'ikit_input'):       ('state_ATTACHED',      'do_input_change'),
    ('state_ATTACHED', 'ikit_output'):      ('state_ATTACHED',      'do_output_change'),
    ('state_ATTACHED', 'ikit_sensor'):      ('state_ATTACHED',      None),
    ('state_ATTACHED', 'ikit_detach'):      ('state_WAIT_ATTACH',   'do_detach'),    
    
    # Error handling
    ('state_ERROR', None):                   '',
    
    # 
    ('state_WAIT', ''):'',
    ('state_WAIT', ''):'',
    ('state_WAIT', ''):'',
    ('state_WAIT', ''):'',
}


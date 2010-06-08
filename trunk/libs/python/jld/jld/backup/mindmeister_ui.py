""" MindMeister User Interface
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: mindmeister_ui.py 793 2009-01-13 18:26:52Z JeanLou.Dupont $"

import jld.tools.cmd_ui as ui

class MM_UI(ui.UIBase):
    """ Handles user interface
    """
    _map = {
        'jld.api.ErrorPopen':           { 'msg': 'error_eventmgr',  'help': 'help_eventmgr', },
        'jld.api.ErrorConfig':          { 'msg': 'error_defaults',  'help': 'help_defaults', },
        'jld.api.ErrorDb':              { 'msg': 'error_db',        'help': 'help_db', },
        'jld.api.ErrorAuth':            { 'msg': 'error_auth',      'help': 'help_auth', },
        'jld.api.ErrorNetwork':         { 'msg': 'error_network',   'help': 'help_network', },
        'jld.api.ErrorAccess':          { 'msg': 'error_access',    'help': 'help_access', },
        'jld.api.ErrorMethod':          { 'msg': 'error_method',    'help': 'help_method', },
        'jld.api.ErrorValidation':      { 'msg': 'error_validation','help': 'help_validation', },
        'jld.api.ErrorProtocol':        { 'msg': 'error_protocol',  'help': 'help_protocol', },
        'jld.api.ErrorInvalidCommand':  { 'msg': 'error_command',   'help': 'help_command', },
        'jld.registry.exception.RegistryException':{ 'msg': 'error_registry',  'help_win': 'help_registry_win', 'help_nix':'help_registry_nix' },
    }
    
# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import jld.backup.mindmeister_messages as msg
    
    msgs = msg.MM_Messages()
    ui = MM_UI( msgs )
    
    import jld.registry.exception as regExc
    e = regExc.RegistryException('test')
    
    ui.handleError( e )
    
    class NotDefined(Exception):
        pass
    
    nd = NotDefined('not defined...')
    ui.handleError( nd )
    
    ea = api.ErrorAuth()
    ui.handleError( ea )
    
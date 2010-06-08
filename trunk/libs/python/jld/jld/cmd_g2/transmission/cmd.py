#!/usr/bin/env python
"""
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: cmd.py 835 2009-01-20 14:35:48Z JeanLou.Dupont $"

import os

from jld.cmd_g2 import BaseCmd
import jld.tools.printer as printer

# imports are checked & errors raised later
try:
    import transmission
except:
    pass
        

class TransmissionCmd(BaseCmd):

    _status_completed = 8  # "seeding", see "transmission.h"

    def __init__(self):
        BaseCmd.__init__(self)
        self.config_server = None
        self.config_port   = None
        self.config_syslog = None
        self.config_quiet  = None
        self.config_eventmgr = None
        self.config_autostop = None
        
        self._checkDependencies()

    def cmd_listconfig(self, *args):
        """Lists the configuration"""       
        msg = BaseCmd.cmd_listconfig( self, *args )
        print msg
    
    @printer.condprinter('config_quiet')
    def cmd_list(self, *args):
        """Lists the current torrents"""
        
        c = self._getClient()
        torrents = c.list()
        liste = ListTorrent( torrents )
        
        p = ListPrinter(self.msgs, list=liste.iterlist())
        return p.run()

    def cmd_reportstatus(self, *args):
        """Reports on the current status of the active torrents (logged) (event)"""
        
        client  = self._getClient()
        torrents= client.list()
        session = client.get_session()
        dld     = session.fields['download_dir']
        
        details   = self._getDetails(torrents)
        completed_id, completed_name = self._getCompletedList(details)
        
        if self.config_autostop:
            stopped = len(completed_id)
            self._performStop(client, completed_id)
        else:
            stopped = 0

        completed = len(completed_id)
                
        if (self.config_export):
            exported = completed
            self._doCompletedReporting(dld, completed_name)
        else:
            exported = 0

        params = {'completed':completed, 'stopped':stopped, 'exported':exported, 'download_dir':dld }
                
        msg = self.msgs.render( 'log_report_status', params)
        self.logger.info( msg )
        
        code = self._fireEvent(self.config_eventmgr, params)
        #return code

    # =================================
    # PRIVATE
    # =================================
    def _doCompletedReporting(self, download_dir, liste):
        ""
        for name in liste:
            self._writeCompletedReport(download_dir, name)
        
    def _writeCompletedReport(self, download_dir, name):
        """Writes a "completed status" report file.
            This consists in a zero-length file with the .completed extension.
        """
        name_completed = "%s.completed" % name
        path = os.path.join(download_dir, name_completed)
        
        #just "touch" the target file
        open(path, "w").close()
    
    def _getDetails(self, torrents):
        """Returns the name & status of each active torrents"""
        details = {}
        for id,torrent in torrents.iteritems():
            status = torrent.fields['status']
            name   = torrent.fields['name']
            details[id] = {'name':name, 'status':status}
        return details
    
    def _getCompletedList(self, detailed_list):
        """Returns 2 lists (id,name) of the completed torrents"""
        completed_id   = []
        completed_name = []
        for id, entry in detailed_list.iteritems():
            if entry['status'] == self._status_completed:
                completed_name.append(entry['name'])
                completed_id.append(id)
        return completed_id, completed_name
    
    def _performStop(self, client, list):
        "Stops (remove) the listed torrents"
        for id in list:
            client.remove( id )
    
    def _checkDependencies(self):
        if "transmission" not in globals():
            import jld.tools.exceptions as exceptions
            raise exceptions.ErrorMissingDependency('error_missing_dependency', {'location':'pypi','dep':'transmission'} )                   

    def _getClient(self):
        return transmission.transmission.Client(address=self.config_server, port=self.config_port)



##################################################################################
##################################################################################



class ListTorrent(object):
    def __init__(self, list):
        self.list = list
        
    def iterlist(self):
        for id, torrent in self.list.iteritems():
            yield str(torrent)
            
    
class ListPrinter(printer.MessagePrinter):
    def __init__(self, msgs, list):
        printer.MessagePrinter.__init__(self, msgs, 'list_', list=list)
        
    def line(self, entry):
        self.buffer =  self.buffer + entry + "\n"

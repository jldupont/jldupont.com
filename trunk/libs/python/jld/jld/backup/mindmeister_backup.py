""" Backup for MindMeister mindmaps
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: mindmeister_backup.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"
__msgs__ = ['error_init_folder', 'error_create_folder', 'frob_not_acquired', 'error_cant_write', ]

import sys
import os
import logging
import datetime
import urllib2
from stat import *

import jld.api as api
from   jld.cmd import BaseCmd, EventMgr
import jld.registry as reg
import jld.api.mindmeister as mm
import jld.api.mindmeister_response as mmr
import jld.backup.mindmeister_messages as msg
import jld.backup.mindmeister_printer as printer
import jld.backup.mindmeister_db as db
import jld.tools.mos as mos

# ========================================================================================

class Backup(BaseCmd):
    """MindMeister Backup class
    """
    _regDomain = 'mindmeister'
    
    _configParams = ['secret', 'api_key', 'export_path', 'export_maxnum', 'db_path', 'eventmgr_path']
    
    def __init__(self):
        BaseCmd.__init__(self)
        self.logger = None
        self.quiet = False
        self.secret = None
        self.api_key = None
        self.auth_token = None
        self.export_path_init = None
        self.export_path = None
        self.export_maxnum = None
        self.eventmgr_path = None
        self.mm = None
        self.db_path = None        
        self.db = None
        
        self.msgs = msg.MM_Messages()
        self.r = reg.Registry()

    # =========================================================
    # Iteration & Dict access interfaces
    #  Used for the command ''listconfig''
    # =========================================================
    def __contains__(self, key):
        return key in self._configParams
    
    def __getitem__(self, key):
        return getattr(self, key)
    
    def __setitem(self, key, value):
        setattr(self, key, value)
    
    def __iter__(self):
        self.iter = True
        return self
    
    def next(self):
        if (self.iter):
            self.iter = False
            return self
        else:
            raise StopIteration
    
    # =========================================================
    # =========================================================
        
    def cmd_listconfig(self, *args):
        """Lists the configuration"""
        pp = printer.MM_Printer_Config( self.msgs, self )
        if (not self.quiet):
            pp.run( self )
    
    def cmd_auth(self, *args):
        """Generates an authentication URL and opens a browser instance for the user"""
        #get a new 'frob'
        self._initMM()
        raw = self.mm.do(method='mm.auth.getFrob')
        res = mmr.MM_Response_getFrob(raw)
        
        #keep this frob in order to retrieve an authentication token later on
        self.r.setKey(self._regDomain, 'frob', res.frob, cond = True)
        
        # Write permission is required for the export operation;
        # there is a bug on MindMeister's side...
        url = self.mm.gen_auth_url('write', res.frob)
        msg = self.msgs.render('cmd_auth', {'frob':res.frob})
        self.logger.info(msg)
        
        import webbrowser
        webbrowser.open_new(url)
            
    def cmd_listmaps(self, *args):
        """ List the latest maps """
        self._prepareAuthorizedCommand()
        all = self.mm.getAllMaps()
        pp = printer.MM_Printer_Maps( self.msgs )
        if (not self.quiet):
            pp.run( all )
            print self.msgs.render('report_maps', {'total':len(all)})
        
    def cmd_listdb(self, *args):
        """List the database content"""
        self._initDb()
        all = db.Maps.getAll()
        pp = printer.MM_Printer_Maps( self.msgs )
        if (not self.quiet):
            pp.run( all )   
            print self.msgs.render('report_maps', {'total':len(all)})     
        
    def cmd_showauthtoken(self, *args):
        """Show the current auth_token"""
        auth_token = self.r.getKey(self._regDomain, 'auth_token')
        print self.msgs.render( 'show_auth_token', {'auth_token':auth_token} )
        
    def cmd_setauthtoken(self, *args):
        """Sets the auth_token parameter"""
        try:
            token = args[0][0]
        except:
            raise api.ErrorValidation( 'missing_param', {'param':'auth_token'} )
        
        self.r.setKey(self._regDomain, 'auth_token', token)
        
        
    def cmd_test(self, *args):
        """Test: for development/debugging purpose only"""
        self._initDb()

    def cmd_getexport(self, *args):
        """List the export details of one mapid"""
        try:    mapid=args[0][0]
        except: raise api.ErrorValidation( 'missing_param', {'param':'mapid'} )
        
        self._prepareAuthorizedCommand()      
        details = self.mm.getMapExport(mapid)
        pp = printer.MM_Printer_Export( self.msgs ) 
        if (not self.quiet):      
            pp.run( details )

    def cmd_exportlist(self, *args):
        """Lists the complete export list"""
        self._initDb()
        full_list = db.Maps.getExportList()
        pp = printer.MM_Printer_Maps( self.msgs )
        if (not self.quiet):
            pp.run( full_list )

    def cmd_updatedb(self, *args):
        """ Updates the local database with the latest list of maps (logged) (event)"""
        self._prepareAuthorizedCommand()
        all = self.mm.getAllMaps()
        
        self._initDb()
        report = db.Maps.updateFromList( all )
        params = {'cmd':'updatedb', 'total':report[0],'updated': report[1], 'new': report[2]}
        msg=self.msgs.render2('report_update', params)
        self.logger.info( msg )
        self._fireEvent(self.eventmgr_path, params)
                
    def cmd_export(self, *args):
        """Export (retrieves from MindMeister) up to 'export_maxnum' mindmaps which need refreshing (logged) (event)"""
        self._initDb()
        full_list = db.Maps.getToExportList()

        self._init_export_folder()
        
        cnt = self.export_maxnum
        self._prepareAuthorizedCommand()
        stack_result = []
        success = 0
        failure = 0
        total = 0
        for map in full_list:
            self.logger.info('Exporting title[%s] mapid[%s]' % (map.title, map.mapid))
            #SqlObject SelectResults has no 'len' method...
            total = total + 1
            ts = datetime.datetime.now()
            timestamp = "%s%s%s%s%s%s" % (ts.year, ts.month, ts.day, ts.hour, ts.minute, ts.second)                            
            res = self._exportOne( map.mapid, ts, timestamp )

            if (res is True):
                success = success + 1
                self._updateDbOne( map )
            else:
                failure = failure + 1
            
            # record result
            stack_result.append((map.mapid, res))
            
            cnt = cnt - 1
            if (cnt==0):
                break
                
        params = {'cmd':'export', 'total': total, 'successes': success, 'failures': failure}
        msg = self.msgs.render2('report_export', params)
        self.logger.info(msg)
        self._fireEvent(self.eventmgr_path, params)
                 
    def cmd_deletedb(self, *args):
        """Deletes the database"""
        self._deleteDb()
        
    # =========================================================
    # =========================================================

    def _exportOne(self, mapid, ts, timestamp):
        """ Exports one map
        """
        try:
            details = self.mm.getMapExport(mapid)
            url  = details[0]['freemind']
            data = self._fetchOne(mapid, url)
            self._writeOne(mapid, data, timestamp)
        except Exception,e:
            return e
        
        return True
        
    def _fetchOne(self, mapid, url):
        """ Fetches one map
        """
        params = {'id':mapid}
        signed_url = self.mm.sign_url(url, params)
        try:
            response = urllib2.urlopen(signed_url)
        except Exception,e:
            raise api.ErrorNetwork(e) 
        return response.read()
        
    def _updateDbOne(self, map ):
        """ Updates the database
        """
        try:
            map.exported = map.modified
        except Exception,e:
            raise api.ErrorDb('msg:error_update_db',{})
        
        
    def _writeOne(self, mapid, data, timestamp):
        """ Writes one map to the export folder
        """
        path = self._genFilePath(mapid, timestamp)
        try:
            fh = open( path, 'w' )
            fh.write( data )
            fh.close()
        except Exception,e:
            raise api.ErrorFile('msg:cant_write', {'path':path})        
        
    def _genFilePath(self, mapid, timestamp):
        """ Generates a filepath related
            to an export map.
        """ 
        dir = self.export_path_init + os.sep + mapid
        self._create_map_export_folder(dir)
        
        return dir + os.sep + mapid + '_' +timestamp + '.mm'
        
    # =========================================================
    # =========================================================
    def _create_map_export_folder(self, dir):
        """
        """
        mos.createDirIfNotExists(dir)
    
    
    def _init_export_folder(self):
        """ Creates the export folder IF it does not
            already exists
        """
        if (not self.export_path_init):
            self.export_path_init = mos.replaceHome( self.export_path )

        rep = mos.existsDir(self.export_path_init)
        if (rep):
            return
               
        if (rep is False):
            self._create_export_folder()
                    
        rep = mos.existsDir(self.export_path_init)       
        if (rep is False):
            raise api.ErrorConfig('msg:error_init_folder')            
            
    def _create_export_folder(self):
        """ Creates the export folder
        """
        try:    
            os.makedirs(self.export_path_init)
        except: 
            raise api.ErrorConfig('msg:error_create_folder')
        return  True
            
    # =========================================================
    # =========================================================
    
    def _prepareAuthorizedCommand(self):
        """Prepares for an authorized command.
        """
        self._initMM()
        auth_token = self.r.getKey(self._regDomain, 'auth_token')
        
        #If there is no auth_token,
        # check for an existing frob and try to retrieve a token
        if (auth_token is None):
            frob = self.r.getKey(self._regDomain, 'frob')
            if (frob is None):
                raise api.ErrorAuth("msg:frob_not_acquired")
            auth_token = self._getAuthToken(frob)
        
        # token turns out to be invalid, help kickstart a re-authentication
        if (not self.mm.checkToken(auth_token)):   
            self.r.setKey(self._regDomain, 'auth_token', None)
            self.r.setKey(self._regDomain, 'frob', None)
            return

        self.mm.auth_token = auth_token
        
    def _getAuthToken(self, frob = None):
        """ Retrieves an authentication token.
            This method can only provide meaningful result
            when valid secret, api_key and frob are handy.
        """
        self._initMM()

        if (frob is None):
            frob = self.r.getKey(self._regDomain, 'frob')
        raw = self.mm.do(method='mm.auth.getToken', frob=frob)
        res = mmr.MM_Response_getAuthToken(raw)
           
        self.r.setKey(self._regDomain, 'auth_token', res.auth_token)
        
        return res.auth_token

    def _deleteDb(self):
        path = mos.replaceHome( self.db_path )
        db.Db.deleteDb(path)
        
    # LAZY INITIALIZERS
    # =================

    def _initMM(self):
        if (self.mm is None):
            self.mm = mm.MM_Client(self.secret, self.api_key)

    def _initDb(self):
        if (self.db is None):
            path = mos.replaceHome( self.db_path )
            self.db = db.Db( path )


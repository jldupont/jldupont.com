""" Backup for Gliffy diagrams
"""
__author__  = "Jean-Lou Dupont"
__version__ = "$Id: gliffy_backup.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

__msgs__ = ['error_init_folder', 'error_create_folder', 'frob_not_acquired', 'error_cant_write', ]

import sys
import os
import logging
import datetime
import urllib2
from stat import *

import jld.api as api
from   jld.cmd import BaseCmd
import jld.registry as reg
import jld.tools.mos as mos

import jld.api.gliffy as glf

import jld.backup.gliffy_messages as msg
import jld.backup.gliffy_printer as printer

import jld.backup.gliffy_db    as glfdb
import jld.backup.delicious_db as dlcdb


# ========================================================================================

class Backup(BaseCmd):
    """Gliffy Backup class
    """
    _regDomain = 'gliffy'
    
    # configuration parameters expected from the Delicious command-line ui
    # used also by the Printer
    _configParams = ['export_path', 'export_maxnum', 'glf_db_path', 'dlc_db_path']
    
    def __init__(self):
        BaseCmd.__init__(self)
        
        self.gliffy = None
        self.logger = None
        self.quiet = False
        self.dlc_db_path = None
        self.glf_db_path = None
        self.dlc_db = None        
        self.glf_db = None
        self.export_path = None
        self.export_path_init = False
        self.export_maxnum = None
        
        self.msgs = msg.Gliffy_Messages()
        self.r = reg.Registry()

    # =========================================================
    # COMMANDS
    # =========================================================
        
    def cmd_listconfig(self, *args):
        """Lists the configuration"""
        pp = printer.Gliffy_Printer_Config( self.msgs, self )
        if (not self.quiet):
            pp.run( self )
    
    def cmd_listdb(self, *args):
        """ Lists the current entries in the database """
        self._initDb()
        all = glfdb.Diagrams.getAll()
        pp = printer.Gliffy_Printer_Diagrams( self.msgs )
        if (not self.quiet):
            pp.run( all )
            print 'Total[%s]' % len(all)
    
    def cmd_import(self, *args):
        """ Imports the diagram ids' from the Delicious database (logged) """
        try:    tag = args[0][0]
        except: raise api.ErrorValidation( 'missing_param', {'param':'tag'} )

        self._initDb()
        all = dlcdb.Posts.getAll( tag )
        self._doImport(all)        
            
    def cmd_export(self, *args):
        """ Exports the diagrams to the export folder (logged) """
        
        # Verify export folder
        self._init_export_folder()
        
        # Get Export list
        #  This is a preliminary list of work to be done:
        #  the gliffy API must be queried for getting
        #  the up-to-date list. The 'etag' metadata is used.
        self._initDb()
        plist = glfdb.Diagrams.getToExportList()        
        p2list = self._buildIdEtagList(plist)
        all = self._buildListToExport(p2list)

        total = len (all) 
        successes = 0 
        failures = 0
        # Export
        for diagram, did, etag in all:
            ts = datetime.datetime.now()
            timestamp = "%s%s%s%s%s%s" % (ts.year, ts.month, ts.day, ts.hour, ts.minute, ts.second)
            result, etag = self._exportOne(did, timestamp)
            if result: 
                self._updateOne(diagram, etag, ts)
                successes = successes + 1
            else:
                failures  = failures + 1 
            
        msg = self.msgs.render2('report_export', {'total':total, 'successes':successes, 'failures': failures} )
        self.logger.info(msg)
            
    def cmd_deletedb(self, *args):
        """Deletes the database"""
        self._deleteDb()
        
    # =========================================================
    # HELPERS
    # =========================================================
    def _buildIdEtagList(self, list):
        """ Builds a diagram id+etag list from a diagram object list
            @param list: the database diagram object list
            @return: output diagram list consisting of (diagram, id,etag)
        """
        result = []
        list = list if list else []
        for diagram in list:
            result.append( (diagram, diagram.did,diagram.etag) )
        return result
        
    def _buildListToExport(self, list):
        """ Builds the export list
            @param list: the input diagram id+etag list
            @return: the processed by diagram id list
        """
        self._initGliffy()
        result = []
        for item in list:
            diagram, did, cetag = item
            code, etag = self.gliffy.head(did)
            if (code):
                if (cetag != etag):
                    result.append( (diagram, did, etag) )
        return result
    
    def _updateOne(self, diagram, etag, timestamp):
        """ Updates the database diagram entry """
        try:
            diagram.etag = etag
            diagram.exported = timestamp
        except Exception,e:
            raise api.ErrorDb('msg:error_update_db', {})
        
    def _exportOne(self, did, timestamp):
        """ Exports one diagram to the export folder
            @param did: diagram id
            @return: result, etag  
        """
        msg = self.msgs.render('report_export_one', {'id':did})
        self.logger.info(msg)
        
        #get just the 'large' representation URI
        code, etag, data = self.gliffy.get(did)

        #write it
        if (code):
            self._writeOne(did, data, timestamp)
            
        return code, etag
                
    def _writeOne(self, did, data, timestamp):
        """ Writes one diagram to the export folder
        """
        path = self._genFilePath(did, timestamp)
        try:
            fh = open( path, 'wb' )
            fh.write( data )
            fh.close()
        except Exception,e:
            raise api.ErrorFile('msg:cant_write', {'path':path})        

    def _genFilePath(self, did, timestamp):
        """ Generates a filepath related
            to an export diagram.
            Assumes a 'jpg' extension.
        """ 
        dir = self.export_path_init + os.sep + did
        mos.initPath(dir)
        
        return dir + os.sep + did + '_' +timestamp + '.jpg'

    def _extractHref(self,list):
        """ Extracts the URI from the HREF entries in the list
        """
        result = []
        list = list if list else []
        for item in list:
            result.append( item['href'] )
        return result
        
    def _doImport(self, list):
        """ Imports the diagram ids' from the list into the database
            @param list: diagram list 
        """
        try:    uris = self._extractHref(list)
        except: raise api.ErrorProtocol('msg:error_expecting_href')

        ids = glf.extractIdFromListOfURI(uris)
        total, updated, created = glfdb.Diagrams.updateFromList(ids)
        msg = self.msgs.render2('report_import', {'total':total, 'updated':updated, 'created':created })
        self.logger.info(msg)
    
    def _deleteDb(self):
        path = mos.replaceHome( self.glf_db_path )
        glfdb.Db.deleteDb(path)
        
    def _init_export_folder(self):
        """ Creates the export folder IF it does not
            already exists
        """
        if (not self.export_path_init):
            self.export_path_init = mos.initPath( self.export_path )

        
    # LAZY INITIALIZERS
    # =================

    def _initGliffy(self):
        if (self.gliffy is None):
            self.gliffy = glf.Client()

    def _initDb(self):
        if (self.glf_db is None):
            glf_path = mos.replaceHome( self.glf_db_path )
            self.glf_db = glfdb.Db( glf_path )
        
        if (self.dlc_db is None):
            dlc_path = mos.replaceHome( self.dlc_db_path )
            self.dlc_db = dlcdb.Db( dlc_path )


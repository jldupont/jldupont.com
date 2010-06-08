#!/usr/bin/env python
""" Db tools
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: db.py 795 2009-01-13 19:42:53Z JeanLou.Dupont $"

import os
from sqlobject import *
import sqlite3 as sql

import jld.api as api
import jld.tools.mos as mos 

def formatSqliteURI( path ):
    """ Formats a filesystem path
        to a valid Sqlite URI.
        Under Windows, the backward slash \
        must be replaced with forward slash /
        as well as replacing the semicolon :
        to a pipe character |.
        For a complete URI, just prepend sqlite:///
        
        @param path: the input filesystem path
        @return: the formatted Sqlite URI 
    """
    if (path is ':memory:'):
        return path
    return  path.replace('\\','/').replace(':','|')



class BaseSQLObjectDb(object):
    """ Base SQLObject Database Initialization class
    """
    
    def __init__(self, filepath):
        """ Constructor
            @param filepath: filesystem path of the database file 
        """
        # verify that the db file exists first
        self._createDb(filepath)

        #on windows, watch-out for the \
        #which are interpreted as escape code by SqlObject
        #also, the : on windows should be switched to |        
        sqlobject_filepath = formatSqliteURI(filepath)

        try:
            if (filepath == ':memory:'):
                connection_string = 'sqlite:/:memory:'
            else:
                connection_string = 'sqlite:///' + sqlobject_filepath
            connection = connectionForURI( connection_string )
            sqlhub.processConnection = connection
        except Exception,e:
            raise api.ErrorDb( e, {'file':filepath} )

        #table already exists ... no big deal
        self.initTable()
        
    def initTable(self):
        """ Strategy Method: initializes the tables 
            of this database
            
            E.g. Diagrams.createTable(ifNotExists=True)
        """
        raise Exception('must sub-class')
        
    @classmethod
    def deleteDb(cls, filepath):
        """ Deletes the corresponding filesystem db file
        """
        try:    os.remove(filepath)
        except: pass
    
    def _createDb(self, filepath):
        """ Handles the creation, if necessary,
            of the database file. The tricky part
            being the creation of the folder hierarchy
            (if required).
        """
        if (filepath != ':memory:'):
            try:
                mos.createPathIfNotExists(filepath)
            except Exception,e:
                print e
                raise api.ErrorDb( e, {'file':filepath} )
        
        # lastly, try to create the db
        try:
            dbfile = sql.connect( filepath )
            dbfile.close()
        except Exception,e:
            raise api.ErrorDb( e, {'file':filepath} )
       
class BaseSQLObjectDb2(object):
    """ Base SQLObject Database Initialization class
    """
    
    def __init__(self, filepath):
        """ Constructor
            @param filepath: filesystem path of the database file 
        """
        # verify that the db file exists first
        self._createDb(filepath)

        #on windows, watch-out for the \
        #which are interpreted as escape code by SqlObject
        #also, the : on windows should be switched to |        
        sqlobject_filepath = formatSqliteURI(filepath)

        try:
            if (filepath == ':memory:'):
                connection_string = 'sqlite:/:memory:'
            else:
                connection_string = 'sqlite:///' + sqlobject_filepath
            connection = connectionForURI( connection_string )
            self.hub = dbconnection.ConnectionHub()
            self.hub.processConnection = connection
        except Exception,e:
            print e
            raise api.ErrorDb( e, {'file':filepath} )

        #table already exists ... no big deal
        self.initTable(connection)
        
    def initTable(self, connection):
        """ Strategy Method: initializes the tables 
            of this database
            
            E.g. Diagrams.createTable(ifNotExists=True)
        """
        raise Exception('must sub-class')
        
    @classmethod
    def deleteDb(cls, filepath):
        """ Deletes the corresponding filesystem db file
        """
        try:    os.remove(filepath)
        except: pass
    
    def _createDb(self, filepath):
        """ Handles the creation, if necessary,
            of the database file. The tricky part
            being the creation of the folder hierarchy
            (if required).
        """
        if (filepath != ':memory:'):
            try:
                mos.createPathIfNotExists(filepath)
            except Exception,e:
                print e
                raise api.ErrorDb( e, {'file':filepath} )
        
        # lastly, try to create the db
        try:
            dbfile = sql.connect( filepath )
            dbfile.close()
        except Exception,e:
            raise api.ErrorDb( e, {'file':filepath} )
       

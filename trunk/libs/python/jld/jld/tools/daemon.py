#!/usr/bin/env python
""" 
    Base class for daemons
    @author: Jean-Lou Dupont
"""

__author__  = "Jean-Lou Dupont"
__version__ = "$Id: daemon.py 861 2009-02-27 15:14:29Z JeanLou.Dupont $"

import os
import sys
import signal
import time


def defaultLogger(name):
    """ Default logger factory
    """
    import logging
    import logging.handlers
    formatter = logging.Formatter("%(levelname)s - %(asctime)s - %(message)s")
    
    _file = "/var/log/%s.log" % name
    hdlr = logging.handlers.TimedRotatingFileHandler( _file )
    hdlr.setFormatter( formatter )
    logger = logging.Logger( name )
    logger.addHandler(hdlr)
    return logger
    
# ==============================================

class BaseDaemonException(Exception):
    def __init__(self, msg, params = None):
        Exception.__init__(self, msg)
        self.msg = msg
        self.params = params

# ==============================================

class BaseDaemon(object):
    """ Base class for daemon
        Implements commands: start, stop, restart, run
        Writes PID info in /var/run/$name.log
    """
    _UMASK   = 0
    _WORKDIR = "/"
    _MAXFD   = 1024
    
    _REDIRECT_TO = os.devnull if hasattr(os,"devnull") else "/dev/null"
    
    def __init__(self, name, loggerFactory = defaultLogger):
        """ @param name: name of the daemon
            @param loggerFactory: a function acting as factory that creates a logger instance
        """
        self.name = name
        self.loggerFactory = loggerFactory
        self.logger = None
        self.pidfile = "/var/run/%s.pid" % name

    def _createLogger(self):
        if (self.logger):
            return
        self.logger = self.loggerFactory(self.name)

    def _format(self, msg):
        """ Performs a first pass formatting of log messages.
        """
        return "[%s] - %s" % (self.name, msg)

    def logdebug(self, msg):
        """Debug"""
        self._createLogger()
        msg = self._format(msg)
        if (self.logger):
            self.logger.debug(msg)
    def loginfo(self, msg):
        """Info"""
        self._createLogger()
        msg = self._format(msg)
        if (self.logger):
            self.logger.info(msg)
    def logwarning(self, msg):
        """Warning"""
        self._createLogger()
        msg = self._format(msg)
        if (self.logger):
            self.logger.warning(msg)
    def logerror(self, msg):
        """Error"""
        self._createLogger()
        msg = self._format(msg)
        if (self.logger):
            self.logger.error(msg)

    def findPID(self):
        """ Returns the I{pid} of the currently running instance of this daemon class. 
        """
        pf = None
        try:
            pf = open(self.pidfile,'r')
            pid = int( pf.read().strip() )
        except Exception,e:
            pid = None
        finally:
            if (pf):
                pf.close()            
        return pid

    def _writePID(self):
        pf = None
        pid = str( os.getpid() )
        try:
            pf = open(self.pidfile,'w+')
            pf.write('%s\n' % pid)
        except Exception,e:
            self.logerror("cannot write to pidfile [%s]" % self.pidfile)
        finally:
            if (pf):
                pf.close()            

    def _delPID(self):
        try:
            os.remove(self.pidfile)
        except:
            pass

    def start(self):
        """ Tries to start the daemon
            Returns the PID of the first child
        """
        pid = self.findPID()
        if (pid):
            raise BaseDaemonException('daemon_exists',{'pid':pid})

        #===============
        return self.daemonize()

    def stop(self):
        """ Tries to stop the daemon
        """
        pid = self.findPID()
        if (pid):
            self.loginfo('Stopping pid[%s]' % pid)
            self._kill( pid )
            self._delPID()
        else:
            raise BaseDaemonException('cant_find_pid',{})

    def restart(self):
        self.stop()
        self.start()

    def _kill(self, pid):
        try:
            while True:
                os.kill(pid, signal.SIGTERM)
                time.sleep(0.1)
        except OSError,e:
            if ('No such process' in str(e)):
                self.loginfo('Killed pid[%s]' % pid)
            else:
                self.logerror('Cannot kill pid[%s]' % pid)

    def daemonize(self):
        """ Creates the daemon
        """
        try:
            pid = os.fork()
        except Exception,e:
            raise BaseDaemonException('cant_fork',{'msg':str(e)})
        
        #in the parent
        if (pid > 0):
            self.logdebug('Created 1st child')
            return pid
        
        # === from this point, the parent is returned ===
        # ===============================================
        

        # 1st child
        # =========
        self._createLogger() # do we need that??
        self.logdebug('Daemonize: child 1: start')
        
        os.setsid()
        try:
            pid = os.fork()
        except Exception,e:
            self.logerror('Daemonize: cannot fork 2nd time')
            sys.exit(1)

        # == Exit from a 2nd parent ==
        if (pid>0):
            sys.exit(0)

        # ==== DAEMON PROCESS STARTS HERE ===
        # ===================================
        self._createLogger()
        self.logdebug('Daemonize: daemon: start')

        os.chdir( self._WORKDIR )
        os.umask( self._UMASK )
       
        """
        import resource
        maxfd = resource.getrlimit( resource.RLIMIT_NOFILE )[1]
        if (maxfd == resource.RLIM_INFINITY):
            maxfd = self._MAXFD
                
        for fd in range(0, maxfd):
            try:
                os.close(fd)
            except:
                pass
            
        os.open(self._REDIRECT_TO, os.O_RDWR)
        os.dup2(0,1)
        os.dup2(0,2)
        """
        self.logdebug('Daemonize: writing PID')
        self._writePID()
        self.logdebug('Daemonize: issuing run()')
        
        self.before_run()
        self.run()

    def before_run(self):
        """ Called before actually passing control to run().
            Should be subclassed if required.
        """
        pass
        
    def run(self):
        """ Run - to subclass
        """
        self.loginfo('Default run()')        
        while True:
            signal.pause()


# ==============================================
# ==============================================

if __name__ == "__main__":
    """ Tests
    """
    import logging
    import logging.handlers
    
    if (len(sys.argv) != 2):
        print "usage: daemon cmd"
        sys.exit(0)
    
    _logfile = '/var/log/daemon_test.log'
        
    cmds = ['start', 'stop', 'restart']
    daemon = BaseDaemon( 'daemon_test', defaultLogger )
    
    cmd = sys.argv[1]
    if (not cmd in cmds):
        print "unknown command"
        sys.exit(0)
    
    try:
        getattr( daemon, cmd )()
    except Exception,e:
        print "msg: [%s]" % e

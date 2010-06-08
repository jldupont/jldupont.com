"""
    @author: Jean-Lou Dupont
    
    Processing Steps:
    
    - Fetch Repo Version (e.g. ETag information)
        - IF same  THEN pass
        - IF newer THEN download `Packages.gz`
            - WRITE new version in 'DebianRepoVersions'
            - EXTRACT each package entry into 'DebianPackageEntries'
            - Once finished, UPDATE state in 'DebianRepoVersions'
    
    - Each change results in an entry being created in 'DebianVirtualRepository'
        - ACCUMULATE the most up-to-date _and_ complete 'DebianRepoVersions' in
            the table 'DebianVirtualRepositoryVersion'
        - When a complete set is available in 'DebianVirtualRepositoryVersion',
            UDPDATE 'active' field in 'DebianVirtualRepository'

    
    AGENTS:
    =======
    
        - SourceRepoChecker
        
        - VR_Aggregator
        
        - VR_Releaser

    
"""

from google.appengine.ext import db

class DebianRepos(db.Model):
    """
    Debian Repository Information  -- CONFIGURED
    
    List of source Repos forming the Virtual Repository.
    """
    url=          db.StringListProperty(required=True)
    component=    db.StringListProperty(required=True)
    distribution= db.StringListProperty(required=True)
    

## ================================================================================
## ================================================================================
## ================================================================================



class DebianRepoVersions(db.Model):
    """
    Debian Repository Version Information
    
    Each row in this table corresponds to a version
    of the source repository.  An 'entry_count' is 
    maintained to track the number of entries written
    in the table `DebianPackageEntries'. 
    
    The field 'state' is used to record when all the
    individual entries from the source `Packages.gz` file
    of the repo have been written to `DebianPackageEntries`.
    
    When all `Packages.gz` file is processed for a
    specific version, the `state` field corresponding to the
    (source repo; version) is updated.  From this point,
    the next stage can progress.    
    """
    timestamp   = db.DateProperty(required=True)
    repo        = db.ReferenceProperty(DebianRepo, required=True)
    version     = db.StringListProperty(required=True)
    entry_count = db.IntegerProperty(default=0)
    state       = db.StringListProperty(default=None)

   
    
class DebianPackageEntries(db.Model):
    """
    Debian Package Entries
    
    Each row corresponds to a package from a specific
    source repository of a specific version.
    
    The field `repo_entry` is used to correlate a specific
    version of the source repository where the present entry
    is sourced from.
    
    The field `data` corresponds to the textual entry
    associated with the package entry taken from the
    `Packages.gz` file from the source Repository.
    """   
    repo_entry      = db.ReferenceProperty(DebianRepoVersions, required=True)
    package_name    = db.StringListProperty(required=True)
    package_version = db.StringListProperty(required=True)
    data            = db.StringListProperty(default=None)



## ======================================================================================
## ======================================================================================
## ======================================================================================


      

class DebianVirtualRepositoryVersion(db.Model):
    """
    Virtual Repository Information
    
    Each row in this table makes up an association 
    between a source repository package list and
    a virtual repository package version. In other words,
    each VR Version is made up of a list of specific
    packages from the source repositories.
    """
    vr_version = db.ReferenceProperty(DebianVirtualRepository, required=True)
    repo       = db.ReferenceProperty(DebianRepoVersions, required=True)




class DebianVirtualRepository(db.Model):
    """
    Virtual Repository State Information

    When a new source repo version is detected,
    an entry is added to this table.
    
    When a complete version of the VR is ready, a row
    is appended to this table.
    """
    timestamp  = db.DateProperty(required=True)
    active     = db.BooleanProperty(default=False)

    
    
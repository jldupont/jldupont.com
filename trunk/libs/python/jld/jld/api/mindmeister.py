"""MindMeister WEB Service API
"""
__author__ = "Jean-Lou Dupont"
__version__= "$Id: mindmeister.py 742 2008-12-16 00:34:09Z JeanLou.Dupont $"

import binascii
import md5
import urllib2
import jld.api as api

import jld.api.mindmeister_response as mmr

class MM(object):
    """MindMeister WEB API
    """
    
    _generic = "%s&api_key=%s&api_sig=%s&auth_token=%s"
    _auth = "http://www.mindmeister.com/services/auth?api_key=%s&perms=%s&frob=%s&api_sig=%s"
    _api  = "http://www.mindmeister.com/services/rest?%s"
    
    _perms = ['read', 'write', 'delete']
    
    def __init__(self, secret, api_key, auth_token = None):
        self.secret = secret
        self.api_key = api_key
        self.auth_token = auth_token
        
    def api_sig(self, params):
        """Generates the api_sig parameter
        """
        sorted_list = api.alphaOrderParams( params )
        liste = api.concatenateParams( sorted_list )
        string = "%s%s" % (self.secret, liste)
        sign = md5.new(string)
        return binascii.hexlify( sign.digest() )
       
    def sign_url(self, url, params):
        """ Signs an url
            Useful for export url generated
            by the method mm.maps.export
        """
        params['api_key'] = self.api_key
        params['auth_token'] = self.auth_token
        sorted_list = api.alphaOrderParams( params )
        liste = api.concatenateParams( sorted_list )
        string = "%s%s" % (self.secret, liste)
        sign = md5.new(string)
        sig = binascii.hexlify( sign.digest() )
        return self._generic % (url, self.api_key, sig, self.auth_token)
       
    def gen_auth_url(self, perm, frob):
        """ Generates the authentication URL used to point
            the user to.
            Client must call the mm.auth.getFrob before
            trying to generate an authentication URL.
        """
        if perm not in self._perms:
            raise RuntimeException("invalid permission")
        
        params = {  'api_key':  self.api_key,
                    'frob':     frob,
                    'perms':    perm
        }
        sign = self.api_sig(params)
        
        return self._auth % (self.api_key, perm, frob, sign) 

    def do(self, **args):
        """ Executes an arbitrary method
        """
        if (self.auth_token is not None):
            args['auth_token'] = self.auth_token
        
        args['api_key'] = self.api_key    
        sig = self.api_sig( args )
        args['api_sig'] = sig
        
        params = api.formatParams( args )
        url = self._api % params
        try:
            response = urllib2.urlopen(url)
        except Exception,e:
            raise api.ErrorNetwork(e) 
        data = response.read()
        response.close()
        return data

    def do_network_error(self):
        raise api.ErrorNetwork('do_network_error')

# ===================================================================================

class MM_Client(MM):
    """High-level interface to MindMeister"""
    
    def __init__(self, secret, api_key, auth_token = None):
        MM.__init__( self, secret, api_key, auth_token )
               
    def getAllMaps(self):
        """ Retrieves a current list of all maps
            @raise jld.api.Error*: exception
        """
        per_page = 100;  pages = 0
        total = 0;  count = 0;  page = 1;  maps = []
        while True:
            batch = self.getMapsPage( page, per_page)
            maps.extend( batch.maps )
            pages = int( batch.pages )
            count = count + int( batch.count )
            total = int( batch.total )
            page = page + 1
            if (count>=total):
                break
        return maps
        
    def getMapsPage(self, page, per_page):
        """ Retrieves one page of the map list
            @raise jld.api.Error*: exception
        """
        raw = self.do(method='mm.maps.getList', auth_token = self.auth_token, 
                      page = page, per_page = per_page)
        res = mmr.MM_Response_getList(raw)
        return res
        
    def checkToken(self, auth_token):
        """Verifies the validity of an authorization token
        """
        raw = self.do(method='mm.auth.checkToken', auth_token = auth_token)
        res = mmr.MM_Response_getAuthToken(raw)
        return res.auth_token==auth_token

    def getMapExport(self, mapid):
        """ mm.maps.export method
        """
        raw = self.do(method='mm.maps.export', auth_token=self.auth_token, map_id=mapid)
        res = mmr.MM_Response_getMapExport(raw)
        return res.exports

# ===================================================================================
    
if __name__ == "__main__":
    """Examples as per MindMeister https://www.mindmeister.com/services/api
    """
   
    params = { 'yxz':'foo' ,'feg':'bar', 'abc':'baz' }
    
    mm = MM('DEADBEEF','c4f64204ca7ee60dd11da6d568b2b199')
    
    print mm.api_sig(params)  # should be 75178b3c27252027ae97b9a5eb36ce41
    print "##############"
    #https://www.mindmeister.com/services/rest?
    # api_key=c4f64204ca7ee60dd11da6d568b2b199
    # &method=mm.test.echo
    # &api_sig=b46d4eabe4e18cd7fcab2de42d743f2d
    mm2 = MM('','')
    ret = mm2.do(method='mm.test.echo')
    
    import mindmeister_response as rep
    res = rep.MM_ResponseBasic(ret)
    print res.code
    print ret
    print res.error
    
    #print mm2.do(method='mm.maps.getPublicList')
    
    
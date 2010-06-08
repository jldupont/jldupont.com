""" User Preferences
    @author: Jean-Lou Dupont
"""

from google.appengine.api import users

class UserPrefs(db.Expando):
    user = UserProperty()


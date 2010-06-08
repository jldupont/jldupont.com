import pkg_resources

my_data = pkg_resources.resource_string("BeautifulSoup", "BeautifulSoup.pyc")

print my_data

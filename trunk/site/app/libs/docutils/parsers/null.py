# $Id: null.py 767 2008-12-20 20:35:04Z JeanLou.Dupont $
# Author: Martin Blais <blais@furius.ca>
# Copyright: This module has been placed in the public domain.

"""A do-nothing parser."""

from docutils import parsers


class Parser(parsers.Parser):

    """A do-nothing parser."""

    supported = ('null',)

    config_section = 'null parser'
    config_section_dependencies = ('parsers',)

    def parse(self, inputstring, document):
        pass

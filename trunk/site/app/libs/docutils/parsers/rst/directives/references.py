# $Id: references.py 767 2008-12-20 20:35:04Z JeanLou.Dupont $
# Authors: David Goodger <goodger@python.org>; Dmitry Jemerov
# Copyright: This module has been placed in the public domain.

"""
Directives for references and targets.
"""

__docformat__ = 'reStructuredText'

from docutils import nodes
from docutils.transforms import references
from docutils.parsers.rst import Directive
from docutils.parsers.rst import directives


class TargetNotes(Directive):

    """Target footnote generation."""

    option_spec = {'class': directives.class_option}

    def run(self):
        pending = nodes.pending(references.TargetNotes)
        pending.details.update(self.options)
        self.state_machine.document.note_pending(pending)
        nodelist = [pending]
        return nodelist

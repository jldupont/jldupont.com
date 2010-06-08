import cmd
import string, sys

class CLI(cmd.Cmd):

    def __init__(self):
        cmd.Cmd.__init__(self)
        self.prompt = '> '

    def do_hello(self, arg):
        print "hello again", arg, "!"
        sys.exit(1)

    def help_hello(self):
        print "syntax: hello [message]",
        print "-- prints a hello message"

    def do_quit(self, arg):
        sys.exit(1)

    def help_quit(self):
        print "syntax: quit",
        print "-- terminates the application"
        
    def do_EOF(self, line):
        return True        

    # shortcuts
    do_q = do_quit

#
# try it out
if __name__=="__main__":
    cli = CLI().cmdloop()

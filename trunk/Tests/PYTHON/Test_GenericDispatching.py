class Generic(object):
    def __init__(self):
        self.func_list = []

    def __call__(self, *args, **kwargs):
        result = []

        for check, func in self.func_list:
            try:
                if check is None or check(*args, **kwargs):
                    result.append(func(*args, **kwargs))
            except TypeError:
                pass

        return result

    def attach(self, func, check=None):
        self.when(check)(func)

    def when(self, check=None):
        def inner(func):
            self.func_list.append((check, func))
            return self

        return inner

if __name__ == "__main__":
    prnt = Generic()

    @prnt.when(lambda string: string == "fredrik")
    def _(string, times=1):
        print "my name "*times

    @prnt.when(lambda string: string == "holmstrom")
    def _(string, times=1):
        print "my surname "*times

    @prnt.when(lambda string, times: string == "holmstrom" and times == 2)
    def _(string, times):
        print "my surname "*times

    def _():
        print "foo bar baz"

    prnt.attach(_, lambda: 1==1) # silly, will always pass

    prnt("holmstrom", times=2)
    prnt("fredrik")
    prnt()
    
import sys

class Demux(object):
    
    fileNameTpl = "dic_%s.csv"
    
    def __init__(self):
        self.files = {}
        
    def write(self, parts, lang):
        if (lang not in self.files):
            self.openFile(lang)

        result = ','.join(parts)
        result += "\n"

        self.files[lang].write( result )
                        
    def openFile(self, lang):
        self.files[lang] = open( self.fileNameTpl % lang, "w")

    def close(self):
        for file in self.files:
            self.files[file].close()
            

input = "o.csv"
ifile = open( input, "r" )
demux = Demux()

done = False
while not done:
    line = ifile.readline()
    if line is "":
        done = True
        continue

    #strip and split
    parts = line.rstrip("\n").split(",")
    
    #more stripping
    parts = map(lambda x:x.lstrip(), parts)

    lang = parts[1]
    demux.write(parts, lang)
    
ifile.close()
demux.close()

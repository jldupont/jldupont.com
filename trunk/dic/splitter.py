import os
import sys

type_blacklist = {}

type_map = {    'Verb':         'v',
                'Noun':         'n',
                'Adverb':       'adv',
                'Interjection': 'intj',
                'Adjective':    'adj',
                'Conjunction':  'conj',
                'Article':      'a',
                'Contraction':  'contr',
                'Pronoun':      'p',
                'Inflexion':    'inf',
                'Inflection':   'inf',
                'Prefix':       'p',
                'Suffix':       's',
                'Numeral':      'num',
                #'':'',
                #'':'',
            }

lang_map = {    'English':      'en',
                'French':       'fr',
                'German':       'de',
                'Italian':      'it',
                'Spanish':      'es',
                'Swedish':      'sv',
                'Latin':        'la',
                'Dutch':        'nl',
                #'Japanese':     'ja',
                #'Indonesian':   'id',
                #'Bosnian':      'bs',
                #'Croatian':     'hr',
                #'Irish':        'ga',
                #'Romanian':     'ro',
                #'Czech':        'cs',
                #'Esperanto':    'eo',
                #'Catalan':      'ca',
                #'':'',
                #'':'',
            }


input = "output.csv"
output = "o.csv"
leftovers = "leftovers.csv"

done = False

ifile = open(input,"r")
lfile = open(leftovers,"w")
ofile = open(output, "w")

while not done:
    line = ifile.readline()
    if line is "":
        done = True
        continue
    
    proc = True
    
    #strip and split
    parts = line.rstrip("\n").split(",")
    
    #more stripping
    parts = map(lambda x:x.lstrip(), parts)
    
    # parts
    #  0         1      2
    #  keyword   lang   type
    lang = parts[1]
    type = parts[2]
    
    # language compression
    if (lang in lang_map):
        parts[1] = lang_map[lang]
    else:
        proc = False
    
     # type remapping i.e. compression
    if (type in type_map):
        parts[2] = type_map[type]
    else:
        proc = False
        
    result = ','.join(parts)
    result += "\n"
        
    if (not proc):
        lfile.write( result )
        print parts
    else:
        ofile.write( result )
        
#END
ifile.close()
ofile.close()
lfile.close()

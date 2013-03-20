#!/usr/bin/python
# -*- coding: utf-8 -*-

#
# Get MSRC documents from MSRC REST and attempt to annotate them
# against Bioportal
#

import msrcrest, restkit

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

API_KEY = '2b8a2949-2c4f-48db-a884-de9cf1e35bcc'
EMAIL   = 'caseyamcl@gmail.com'

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def bioportalAnnotate():
    testText = "I am researching the topic of military suicide which involves sleep disorders and drug abuse. That is suicide for ya"

    # Structure containing parameters
    # See: http://www.bioontology.org/wiki/index.php/Annotator_User_Guide
    params = {
        'longestOnly':'false',
        'wholeWordOnly':'true',
        'withContext':'true',
        'filterNumber':'true', 
        'stopWords':'',
        'withDefaultStopWords':'true', 
        'isStopWordsCaseSenstive':'false', 
        'minTermSize':'3', 
        'scored':'true',  
        'withSynonyms':'true', 
        'ontologiesToExpand':'', #Empty means 'all'  (mesh VirtualID is 1351)
        'ontologiesToKeepInResult':'1351', #Empty means all   (if useVirtualOntologyId=true, use that field)
        'isVirtualOntologyId':'true', 
        'semanticTypes':'',  #T017,T047,T191&" #T999&"
        'levelMax':'0',
        'mappingTypes':'null', 
        'textToAnnotate':testText, 
        'format':'xml',  #Output formats (one of): xml, tabDelimited, text  
        'apikey':API_KEY,
    }    

    result = restkit.request('http://rest.bioontology.org/obs/annotator/submit/' + EMAIL, method='POST', body=params)
    return result.body_string()

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if __name__ == "__main__":
    print bioportalAnnotate()
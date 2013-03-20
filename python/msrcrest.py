#!/usr/bin/python
# -*- coding: utf-8 -*-

#
# Go to the MSRC site, get the abstracts for all documents,
# and retrieve them one at a time
#

import restkit
import json, argparse

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def getDocs(limit=None, ids=None):
    '''Get multiple documents as a list from MSRC API'''

    #Setup REST Client
    client = restkit.Resource('https://msrc.idiginfo.org/documents')
    
    #If IDS not specified, assume all
    if ids == None:
        result   = client.get('/');
        json_obj = json.loads(result.body_string())
        ids = list(json_obj['document_ids'])

    #Slice IDs
    if limit != None and len(ids) > limit:
        ids = ids[:limit]    

    items = list()

    for docId in ids:
        items.append(getDoc(str(docId)))

    return items

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def getDoc(docId):
    '''Get single document from MSRC API'''

    req_str  = 'https://msrc.idiginfo.org/documents/{0}'.format(docId) 
    result   = restkit.request(req_str)
    json_obj = json.loads(result.body_string())
    return json_obj

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def runCli():
    '''Wrapper to accept CLI arguments as options.  Prints output as JSON'''
    
    parser = argparse.ArgumentParser(description='Simple REST Client for MSRC Documents in JSON')
    parser.add_argument('--limit', type=int, help='Optionally limit the number of items retrieved')
    parser.add_argument('--ids', type=str, help='Optionally specify ids of documents to retrieve (comma separated)')

    args = parser.parse_args()
    ids = list(i.strip() for i in args.ids.split(',')) if (args.ids != None) else None

    items = getDocs(limit=args.limit, ids=ids)
    print json.dumps({'numitems': len(items), 'items': items})

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if __name__ == "__main__":
    runCli()
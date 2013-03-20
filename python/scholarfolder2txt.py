#!/usr/bin/python
# -*- coding: utf-8 -*-

#
# Scholar2Text - Python App for converting Scholarly PDFs to Text
# 
# Directory Iterator for PDFs
#
# Author: Casey McLaughlin
# License: GPLv2 (see LICENSE.md)
#

import scholar2txt, sys, os

def runAnalysisFromCli():

    #Input folder
    inFolder = os.path.abspath(sys.argv[1])

    #Output folder
    try:
        outFolder = sys.argv[2]
    except IndexError:
        outFolder = '.'
    outFolder = os.path.abspath(outFolder)

    iterateThroughFolder(inFolder, outFolder)

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def iterateThroughFolder(inFolder, outFolder):
    for root, _, files in os.walk(inFolder):
        for f in files:
            fullPath = os.path.join(root, f)
            baseName, fileExtension = os.path.splitext(f)
            outFileName = os.path.join(outFolder, baseName + '.txt')

            if (fileExtension.lower() == '.pdf'):

                print 'Processing ' + f + '...',

                res = scholar2txt.runPdfAnalysis(fullPath)

                if (res != False):
                    outFile = open(outFileName, 'w')
                    outFile.write(res)
                    outFile.close()
                    print 'done'
                else:
                    print 'FAIL'


#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if __name__ == "__main__":
    runAnalysisFromCli()
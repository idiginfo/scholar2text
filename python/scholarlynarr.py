#!/usr/bin/python
# -*- coding: utf-8 -*-

#
# Scholar2Text - Python App for converting Scholarly PDFs to Text
# 
# Converts a single PDF to text
#
# Author: Casey McLaughlin
# License: GPLv2 (see LICENSE.md)
#

import re, os, sys, nltk, numpy

def combineNarrative(txt):
    '''Attempts to combine the narrative of text by remove extraneous blocks'''

    #Split by paragraphs (two linebreaks in a row)
    paragraphs = txt.split("\n\n")

    outParagraphs = []

    for p in paragraphs:
        if isNarrative(p):
            outParagraphs.append(re.sub(r"\n", ' ', p))
      
    return "\n\n".join(outParagraphs)

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def isNarrative(paragraph):
    '''Attempts to determine if a given paragraph is part of the narrative'''

    #Words that indicate typical heading phrases and therefore
    #exempt from further scrutiny
    exemptWords = ('methods', 'method', 'abstract', 'result', 'results', 
                    'conclusion', 'analysis', 'aim', 'sample', 'procedures',
                    'population', 'measurement instruments', 'discussion',
                    'conclusion',' background', 'ethics', 'demographics',
                    'introduction')

    #These words almost always signify auxiliary text within one-liners
    blackwords = ('', '\xa9', 'copyright') #xa9 is copyright symbol

    #These words usually indicate a figure caption
    blackStartWords = ('table', 'figure', 'illustration', 'ill')

    # ------

    #Strip surrounding whitespace
    paragraph = paragraph.strip()

    #Split into lines
    lines = list(w.strip() for w in paragraph.split("\n") if w.strip() != '')

    #If there were just a bunch of empty lines, return false
    if len(lines) == 0:
        return False

    #Get set of words in paragraph
    nltkObj = nltk.Text(nltk.word_tokenize(paragraph))
    paragraphWordSet = set(w.lower() for w in nltkObj)

    #Tests...
    #If single line or two lines and contains exempt word, return true
    if len(lines) <= 1 and len(set(exemptWords).intersection(paragraphWordSet)) > 0:
        return True

    #If single line and less than 35, return false
    if len(lines) == 1 and len(paragraph) < 35:
        return False

    #If single line or two lines and contains black word, return false
    if len(lines) <= 2 and len(set(blackwords).intersection(paragraphWordSet)) > 0:
        return False

    #If average line length is less than 20, return false
    if numpy.mean(list(len(l) for l in lines)) < 20:
        return False

    return True

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def runCli():
    try:
        inFile = os.path.abspath(sys.argv[1])
        with open(inFile) as f:
            content = f.read()
        print combineNarrative(content)
    except IOError:
        print "Could not read from file: " + inFile
    except IndexError:
        print "You must specify a filename"

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if __name__ == "__main__":
    runCli()
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

import sys, subprocess, re, argparse, operator
import nltk, scholarlynarr

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def runPdfAnalysis(pdfFile, extract_narrative=True):
    '''Main processor'''

    #Check file exists
    with open(pdfFile): pass

    #Array of different word-boundry settings for the pdf2txt CLI tool
    wordBoundries = (0.1, 0.2, 0.3, 0.4, 0.5)

    #Array to contain raw texts
    rawTexts = {}

    #Run PDFMiner to convert to text five times
    #Dehyphenate while we are at it
    for wb in wordBoundries:
        rt = getProcOutput(['pdf2txt', '-t' ,'text', '-W', str(wb), '-A', pdfFile])
        rawTexts[wb] = dehyphenate(rt)

    #Array to hold wordtext counts
    validWordCounts = {}

    #Foreach rawText, determine which has the highest frequency of valid words and output that
    for wb in rawTexts:
        wc = detectNormalWordCount(rawTexts[wb])
        validWordCounts[wb] = wc;

    #Which validword count is the top?  Use that from rawTexts (http://goo.gl/zHbec)
    wcMatch = max(validWordCounts.iteritems(), key=operator.itemgetter(1))[0]

    #Return the version with the highest number of matches
    #If the highest match is less than 300, the document wasn't parsable
    if (validWordCounts[wcMatch] > 300):
        return scholarlynarr.combineNarrative(rawTexts[wcMatch])
    else:
        return False

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def detectNormalWordCount(txt):
    ''' Detect word count for the extracted PDF text by comparing it with a known word list'''

    #Test for words corpa, or download it if it doesn't exit
    try:
        testwords = nltk.corpus.words.words()
        del testwords
    except LookupError:
        nltk.download(info_or_id='words')

    #Get an English dictionary (could substitute with MOBY if we want)
    english_vocab = set(w.lower() for w in nltk.corpus.words.words() if len(w) > 5)

    #Get a wordset
    text = nltk.Text(nltk.word_tokenize(txt))
    word_set = set(w.lower() for w in text if w.isalpha())

    #Get the intersection
    return len(english_vocab.intersection(word_set))

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def dehyphenate(txt):
    '''de-hyphenate text using regex'''
    return re.sub(r"(\w)-([\n|\r\n|\r])(\w)", r'\1\3', txt)

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def getProcOutput(exe):
    '''Concatenate lines from external process output'''

    out = ''
    for line in runProcess(exe):
        out += line
    return out

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def runProcess(exe):
    '''Run an external process'''

    p = subprocess.Popen(exe, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
    while(True):
      retcode = p.poll() #returns None while subprocess is running
      line = p.stdout.readline()
      yield line
      if(retcode is not None):
        break

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

def runAnalysisFromCli():
    '''Wrapper to accept CLI arguments as options'''
    
    parser = argparse.ArgumentParser(description='Analyze a Scholarly PDF into text')
    parser.add_argument('filename', type=str, help='filename to process')
    parser.add_argument('--skipnarr', help='optionally skip narrative extraction', action='store_true')

    args = parser.parse_args()

    runParser = True if args.skipnarr == False else False
    print runPdfAnalysis(args.filename, runParser)

#~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if __name__ == "__main__":
    runAnalysisFromCli()
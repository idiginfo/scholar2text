Scholar2Text

Converts PDFs of scholarly journal articles to text.  This is very much
a work-in-progress, and is not yet ready for prime-time.

Trying it out:

1. Install Python-Numpy, Python-NLTK and Python PDFMiner libraries (sudo apt-get install python-nltk python-pdfminer python-numpy)
3. To convert a single document, run `python scholar2txt.py [YOUR-PDF-ARTICLE.pdf]`
4. To iterate over a directory, run `python scholarfolder2txt.py [INPUTFOLDER] [OUTPUTFOLDER]`
5. To skip narrative extraction, use the `--skipnarr` CLI argument


Limitations:

1. Only converts English articles
2. It isn't done yet!!

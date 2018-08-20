#!/bin/sh

mysql -B -u roprotokol roprotokol < seasonrowertrips.sql  >  seasonrowertrips.tsv
mysql -B -u roprotokol roprotokol < seasontrips.sql  >  seasontrips.tsv

ssconvert --merge-to seasontrips.xlsx seasontrips.tsv seasonrowertrips.tsv 

#ssconvert -O 'separator=\t' seasontrips.tsv seasonrowertrips.tsv seasontrips.xlsx
#ssconvert -O 'separator=\t' seasonrowertrips.csv seasonrowertrips.xlsx

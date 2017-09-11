#!/bin/bash
#

DIRECTORY="/media/file-server/1000 National/Financials/Xero to CiviCRM"
INCOMING="$DIRECTORY/From Xero"
OUTGOING="$DIRECTORY/To CiviCRM"

cd `dirname $0`

shopt -s nullglob

for i in "$INCOMING"/*.csv
do
	BASE=`basename "$i"`
	./xero-bank-transaction.php "$i" "$DIRECTORY/lookup.csv" "$OUTGOING/$BASE" > "$OUTGOING/$BASE.log"
	mv "$i" "$i.processed"
done

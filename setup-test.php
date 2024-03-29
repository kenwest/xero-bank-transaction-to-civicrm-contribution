#!/usr/bin/php
<?php

$input = fopen('/tmp/1-xero-report.csv', 'w');
$columns = array('Date','Description', 'Reference', 'Debit', 'Credit', 'Gross', 'GST', 'Account Code', 'Account', 'Department', 'Secondary Tag');
fputcsv($input, $columns);
$columns = array('','', '', '', '', '', '', '', '', '', '');
fputcsv($input, $columns);
$columns = array('Receive Money','', '', '', '', '', '', '', '', '', '');
fputcsv($input, $columns);
$columns = array('20 Jan 2015', 'John Smith - 1000 - donation', '', '', '1,000.00', '1,000.00', '0.00', '200','Regular Giving', 'SYD', '');
fputcsv($input, $columns);
$columns = array('20 Jan 2015', 'John Smith - 1000 - event', '', '', '100.00', '100.00', '0.00', '200','Event ticket sales', 'NAT', '');
fputcsv($input, $columns);
$columns = array('20 Jan 2015', 'John Smith - 10x0 - donation', '', '', '100.00', '100.00', '0.00', '200','Regular Giving', 'ADE', '');
fputcsv($input, $columns);
$columns = array('20 Jan 2015', 'John Smith - 1000 - donation', 'INV-0001', '', '100.00', '100.00', '0.00', '200','Product Income', 'MEL', 'Support Andrew');
fputcsv($input, $columns);
$columns = array('20 Jan 2015', 'John Smith - 1000', '', '', '1,000.00', '1,000.00', '0.00', '200','Regular Giving', 'SYD', '');
fputcsv($input, $columns);
$columns = array('Total Receive Money','', '', '', '', '', '', '', '', '', '');
fputcsv($input, $columns);
$columns = array('Ignored','', '', '', '', '', '', '', '', '', '');
fputcsv($input, $columns);
fclose($input);

$lookup = fopen('/tmp/2-lookup.csv', 'w');
$columns = array('Lines with two columns are interpreted as Variable/Value pairs');
fputcsv($lookup, $columns);
$columns = array('Lines with three columns are interpreted as Variable/Key/Value pairs');
fputcsv($lookup, $columns);
$columns = array('Date',0);
fputcsv($lookup, $columns);
$columns = array('Description',1);
fputcsv($lookup, $columns);
$columns = array('Gross',5);
fputcsv($lookup, $columns);
$columns = array('Account',8);
fputcsv($lookup, $columns);
$columns = array('City',9);
fputcsv($lookup, $columns);
$columns = array('2nd Category',10);
fputcsv($lookup, $columns);
$columns = array('Start Bank Transactions','Receive Money');
fputcsv($lookup, $columns);
$columns = array('End Bank Transactions','Total Receive Money');
fputcsv($lookup, $columns);
$columns = array('Currency','AUD');
fputcsv($lookup, $columns);
$columns = array('CiviCRM City','ADE', 'Adelaide');
fputcsv($lookup, $columns);
$columns = array('CiviCRM City','BRI', 'Brisbane');
fputcsv($lookup, $columns);
$columns = array('CiviCRM City','MEL', 'Melbourne');
fputcsv($lookup, $columns);
$columns = array('CiviCRM City','PER', 'Perth');
fputcsv($lookup, $columns);
$columns = array('CiviCRM City','SYD', 'Sydney');
fputcsv($lookup, $columns);
$columns = array('CiviCRM City','Default', '');
fputcsv($lookup, $columns);
$columns = array('Source','EFT');
fputcsv($lookup, $columns);
$columns = array('Status','Completed');
fputcsv($lookup, $columns);
$columns = array('Financial Type','Default', 'Donation');
fputcsv($lookup, $columns);
$columns = array('Financial Type','Event ticket sales','Event Fee');
fputcsv($lookup, $columns);
$columns = array('Financial Type','Product income','Sales');
fputcsv($lookup, $columns);
$columns = array('Instrument','EFT');
fputcsv($lookup, $columns);
$columns = array('Pledge','No');
fputcsv($lookup, $columns);
$columns = array('Suppress Xero invoice','Yes');
fputcsv($lookup, $columns);
fclose($lookup);

echo "To test, run ...\n";
echo "  ./xero-bank-transaction.php /tmp/1-xero-report.csv /tmp/2-lookup.csv /tmp/3-civicrm-import.csv\n";
echo "  more /tmp/3-civicrm-import.csv\n";
echo "    - line 1 should be ignored\n";
echo "    - line 2 should be ignored\n";
echo "    - line 3 should be ignored\n";
echo "    - line 4 should create an output line for a Donation - comma in amount is removed\n";
echo "    - line 5 should create an output line for an Event Fee - also City is empty\n";
echo "    - line 6 should fail because it has no Contact Id in the Description\n";
echo "    - line 7 should create an output line for Sales - 2nd category is in Source\n";
echo "    - line 8 should create an output line for a Donation - where the Contact Id is at the end of the Description column\n";
echo "    - line 9 should be ignored\n";
echo "    - line 10 should be ignored\n";

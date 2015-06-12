#!/usr/bin/php
<?php

/*
 * OVERVIEW
 *
 * Read the lookup file as a CSV
 *  Lines with two columns are interpreted as Variable/Value pairs
 *  Lines with three columns are interpreted as Variable/Key/Value pairs
 *
 * Read the input file as a CSV
 *  Skip lines before and including the 'Receive Money' line
 *  Insert a header line in the output
 *  Repeat for each subsequent line...
 *   Get Id from Name
 *   Get Date, Amount
 *   Assume Currency is 'AUD'
 *   Assume Source is 'Xero bank transaction'
 *   Assume Status is 'Completed'
 *   Assume Financial Type is 'Donation' unless we can tell it is 'Event Fee' or 'Sale'
 *   Assume Payment Instrument is 'EFT'
 *   Assume Pledge is 'No'
 *   Assume Suppress Xero Invoice is 'Yes'
 *   Put Id, Date, Amount, Currency, Source, Status, Financial Type, Payment Instrument, Pledge, suppress Xero invoice
 *  Continue until there is a blank line
 * Exit
 */

$input = fopen($argv[1], 'r');
$lookup = fopen($argv[2], 'r');
$output = fopen($argv[3], 'w');

if ($input === FALSE || $lookup === FALSE || $output === FALSE) {
  return;
}

$variables = array();
while ( ($columns = fgetcsv($lookup)) !== FALSE ) {
  if (empty($columns[0]) || empty($columns[1])) {
    continue;
  } elseif (empty($columns[2])) {
    $variables[$columns[0]] = $columns[1];
  } else {
    if (!isset($variables[$columns[0]])) {
      $variables[$columns[0]] = array();
    }
    $variables[$columns[0]][$columns[1]] = $columns[2];
  }
}

$writing = FALSE;
$lineNumber = 1;
while ( ($columns = fgetcsv($input)) !== FALSE ) {
  if ($writing) {
    if (empty($columns[0])) {
      $writing = FALSE;
    } elseif ($columns[2] != '') {
      echo 'WARNING on line ' . $lineNumber . ': the Reference field is not empty - this is an invoice - ignoring' . "\n";
    } else {
      $line = array();
      $line[] = date_format(date_create_from_format('d M Y', $columns[0]), 'Y-m-d');
      if (preg_match('/ - ([[:digit:]]+) - /', $columns[1], $matches) && count($matches) == 2) {
        $line[] = $matches[1];
      } else {
        echo 'WARNING on line ' . $lineNumber . ': could not get a Contact Id from the Description - leaving it blank' . "\n";
        $line[] = '';
      }
      $line[] = $columns[5];
      $line[] = $variables['Currency'];
      $line[] = $columns[9];
      $line[] = $variables['Source'];
      $line[] = $variables['Status'];
      if (isset($variables['Financial Type Override'][$columns[8]])) {
        $line[] = $variables['Financial Type Override'][$columns[8]];
      } else {
        $line[] = $variables['Financial Type'];
      }
      $line[] = $variables['Instrument'];
      $line[] = $variables['Pledge'];
      $line[] = $variables['Suppress Xero invoice'];
      fputcsv($output, $line);
    }
  } else {
    if ($columns[0] == 'Receive Money') {
      $writing = TRUE;
      fputcsv($output, array('Date', 'Contact Id', 'Amount', 'Currency', 'City', 'Source', 'Status', 'Financial Type', 'Instrument', 'Pledge', 'Suppress Xero Invoice'));
    }
  }
  $lineNumber++;
}


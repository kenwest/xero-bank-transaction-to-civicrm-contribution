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
 *   Get Id from Description
 *   Get Date, Amount
 *   Assume Currency is 'AUD'
 *   Get City and Purpose from the Xero tracking categories
 *   Assume Source is 'Xero bank transaction' concatenated with Account
 *   Assume Status is 'Completed'
 *   Assume Financial Type is 'Donation' unless we can tell it is 'Event Fee' or 'Sale' (derived from Account)
 *   Assume Payment Instrument is 'EFT'
 *   Assume Pledge is 'No'
 *   Assume Suppress Xero Invoice is 'Yes'
 *   Put Date, Id, Amount, Currency, City, Purpose, Source, Status, Financial Type, Payment Instrument, Pledge, suppress Xero invoice
 *  Continue until there is a line containing 'Total Receive Money'
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
  if (!isset($columns[0]) || !isset($columns[1])) {
    continue;
  } elseif (!isset($columns[2])) {
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
    if ($columns[lookup($variables, 'Date')] == lookup($variables, 'End Bank Transactions')) {
      $writing = FALSE;
    } else {
      $line = array();
      $line[] = date_format(date_create_from_format('d M Y', $columns[lookup($variables, 'Date')]), 'Y-m-d');
      if (preg_match('/ - ([[:digit:]]+)( - |$)/', $columns[lookup($variables, 'Description')], $matches) && count($matches) == 3) {
        $line[] = $matches[1];
        if (is_numeric($columns[lookup($variables, 'Gross')])) {
          $line[] = $columns[lookup($variables, 'Gross')];
        }
        else {
          $line[] = str_replace(',', '', $columns[lookup($variables, 'Gross')]);
        }
        $line[] = $variables['Currency'];
        $line[] = lookup($variables, 'CiviCRM City', $columns[lookup($variables, 'City')]);
        $line[] = $columns[lookup($variables, '2nd Category')];
        $line[] = $variables['Source'] . ' - ' . $columns[lookup($variables, 'Account')];
        $line[] = $variables['Status'];
        $line[] = lookup($variables, 'Financial Type', $columns[lookup($variables, 'Account')]);
        $line[] = $variables['Instrument'];
        $line[] = $variables['Pledge'];
        $line[] = $variables['Suppress Xero invoice'];
        fputcsv($output, $line);
      } else {
        echo 'ERROR on line ' . $lineNumber . ': could not get a Contact Id from the Description - skipping' . "\n";
      }
    }
  } else {
    if ($columns[lookup($variables, 'Date')] == lookup($variables, 'Start Bank Transactions')) {
      $writing = TRUE;
      fputcsv($output, array('Date', 'Contact Id', 'Amount', 'Currency', 'City', 'Purpose', 'Source', 'Status', 'Financial Type', 'Instrument', 'Pledge', 'Suppress Xero Invoice'));
    }
  }
  $lineNumber++;
}

function lookup($variables, $name, $index = NULL, $default = NULL) {
  if (isset($variables[$name])) {
    if (isset($index) && is_array($variables[$name])) {
      return lookup($variables[$name], $index, NULL, $default);
    }
    else {
      return $variables[$name];
    }
  }
  elseif (isset($variables['Default'])) {
    return $variables['Default'];
  }
  else {
    return $default;
  }
}
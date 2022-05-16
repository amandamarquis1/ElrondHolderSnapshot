<?php
  /* Request latest information from Elrond API. */
  $ch = curl_init();
  
  $project = '';
  curl_setopt($ch, CURLOPT_URL, "https://api.elrond.com/collections/" . $project . "/nfts/count");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  /* Cleanup resources. */
  $output = curl_exec($ch);
  $json = json_decode($output);
  
  curl_setopt($ch, CURLOPT_URL, "https://api.elrond.com/nfts/" . $project . "-*/accounts?size=" . $json);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  /* Cleanup resources. */
  $output = curl_exec($ch);

  /* Parse JSON data. */
  $json = json_decode($output);

  $holders = array();
  $marketplaces = array();
  
  foreach($json as $res) {
    $address = $res->address;
    if (str_contains($address, 'erd1qqq')) {
      if (isset($marketplaces[$address]['count']))
        $marketplaces[$address]['count'] += 1;
      else {
        $marketplaces[$address]['count'] = 1;
		  }
    }
    else {
      if (isset($holders[$address]['count']))
        $holders[$address]['count'] += 1;
      else {
        $holders[$address]['count'] = 1;
		  }
    }
  }
  arsort($holders);
  arsort($marketplaces);
  $count_holders = count($holders);
  $count_marketplaces = count($marketplaces);
  
  echo "there are " . $count_holders . " holder addresses holding " . $project . "<br>";
  echo "there are " . $count_marketplaces . " marketplaces holding NFTs on sale for " . $project . "<br>";
  
  $all_addresses = array_merge($holders, $marketplaces);
  $count = count($all_addresses);
  
  echo "there are " . $count . " addresses in total (holder and marketplace) holding " . $project . "<br>";
  
  $snapshot = fopen('./snapshot-' . $project .'.csv', 'w');
  fputcsv($snapshot, array('Address', 'NFT Count'));
  foreach ($all_addresses as $address => $count) {
    fputcsv($snapshot, array($address, $count['count']));
  }


//TODO: Add array of marketplace SC's to add SC listing information to the csv

//Currently: prints holders, marketplaces, and combined in the browser when ran
// Now creates a CSV instead of a JSON file for more optimal viewing
?>

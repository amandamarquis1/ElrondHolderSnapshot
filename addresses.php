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

  $count = count($json);
  $addr = array();
  
  foreach($json as $res) {
    if (isset($addr[$res->address]['count']))
      $addr[$res->address]['count'] += 1;
    else {
      $addr[$res->address]['count'] = 1;
		}
  }
  arsort($addr);
  file_put_contents('./snapshot-' . $project .'.json', json_encode($addr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
?>
  
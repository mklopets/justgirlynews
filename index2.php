<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	
</body>
</html>

<?php

$url = 'http://www.postimees.ee/rss/';

function fetch($url) {
	$rss = new DOMDocument();
	$rss->load($url);

	$feed = array();

	foreach ($rss->getElementsByTagName('item') as $node) {
		$item = array(
			'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
			'description' => $node->getElementsByTagName('description')->item(0)->nodeValue
		);
		echo '<br>asdasd ' . $node->getElementsByTagName('media:group')->item(0)->nodeValue . '<br>';
		$feed[] = $item;
	}
	return $feed;
}

$feed = fetch($url);

$limit = 5;
for($x = 0; $x < $limit; $x++) {
	echo '*' . $feed[$x]['title'] . '<br>' . $feed[$x]['description'];
	echo '<br><br><br>';
}

?>
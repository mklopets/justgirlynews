<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>justgirlynews</title>
	<link rel="stylesheet" href="main.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="favicon.png">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>

<?php

$counter_path = ("hitcounter.txt");
@$hits = file($counter_path);
$hits[0] ++;
@$fp = fopen($counter_path , "w");
@fputs($fp , "$hits[0]");
@fclose($fp);



require 'simple_html_dom.php';


// BEGIN SETTINGS

$url = 'http://www.postimees.ee/';
$GLOBALS['desc_title'] = true; // whether or not to display description in <a title=""> attribute
$GLOBALS['links_enable'] = true; // whether or not to make headlines <a> tags
$GLOBALS['new_window'] = true; // whether or not to open links in new tab/window

$GLOBALS['justgirly'] = 'justgirlynews'; // justgirlythings style subheading text

$GLOBALS['inappropriate'] = ['suri', 'surm', 'laip', 'vägistami', 'õnnetus', 'alko']; // array of words commonly found in _sad_ news :(

error_reporting(0);

// END SETTINGS

function fetch($url) {
	$html = file_get_html($url);
	if (!$html) {
		die('<br><br>Failed to retrieve news.<br><br>');
	}
	$articles = array();

	foreach($html->find('article') as $article) {
		// remove number of comments from <h1>
		@$article->find('span[class=frontComments]')[0]->innertext = '';
		$title = trim($article->find('h1')[0]->plaintext);
		if (!$title)
			continue;

		$justwhat = $GLOBALS['justgirly'];
		// check for sad news in order to not be a jerk about death
		$title_lc = strtolower($title);
		foreach($GLOBALS['inappropriate'] as $word) {
			if (strpos($title_lc, $word) !== FALSE) {
				$justwhat = 'justsadnews';
				break;
			}
		}


		// get url to article
		// criteria: <a class="frontUrl">
		
		if (!$link = $article->find('a')[0]->href) {
			$link = '#';
		}
		

		// get article's description
		// criteria: <p itemprop="description">
		$description = '';
		foreach(@$article->find('p') as $desc) {
			if ($desc->itemprop == 'description') {
				$description = $desc->plaintext;
			}
		}


		// skip article if image is too small
		$img = @$article->find('img')[0];
		$width = @$img->width;
		$height = @$img->height;

		if ($width && $height) {
			if ($width < 300 && $height < 10) {
				// soft skip over
				continue;
			}
	
			if ($width < 180 || $height < 180) {
				// hard skip
				continue;
			}
		}

		// get image url
		$img_src = trim(@$img->{'data-src'});
		$img = "<img src='{$img_src}' />";
		
		if (strlen($img_src) < 5) {
			// invalid image
			continue;
		}

		$article = array(
			'title' => $title,
			'img_src' => $img_src,
			'link' => $link,
			'desc' => $description,
			'justwhat' => $justwhat
		);

		$articles[] = $article; // append to array
	}

	return $articles;
}


function print_articles($articles, $limit) {
	/*
	 * prints n articles from associative array
	 *
	 * set $limit to even number or it will be automatically changed
	*/

	if ($limit % 2 != 0) {
		$limit = 2 * floor($limit / 2);
	}


	// ensure that id doesn't access out-of-bounds articles
	if (count($articles) < $limit) {
		$limit = count($articles);
	}

	// prints image containers
	echo '<div class="articles">';
	for ($i = 0; $i < $limit; $i++) {
		$article = $articles[$i];

		$img_src = $article['img_src'];

		echo "<article style=\"background-image: url('{$img_src}'); background-repeat: no-repeat; background-size: cover;\">";
		echo "</article>\n\n";
		// echo "<h1>{$title}</h1>";
	}
	echo '</div>';

	// prints text


	echo '<div class="titles">';
	for ($i = 0; $i < $limit; $i++) {
		$article = $articles[$i];

		$title = $article['title'];
		$link = $article['link'];
		$desc = $article['desc'];
		$justwhat = $article['justwhat'];

		$description = ($GLOBALS['desc_title']) ? "title='{$desc}'" : '';
		$target_attr = ($GLOBALS['new_window']) ? "target='_blank'" : '';

		$a1 = ($GLOBALS['links_enable']) ? "<a href='{$link}' {$description} {$target_attr}>" : '';
		$a2 = ($GLOBALS['links_enable']) ? '</a>' : '';

		echo '<div class="title">';
		echo "<h1>{$a1}{$title}{$a2}</h1>";
		echo "<h2>{$justwhat}</h2>";
		echo "</div>\n\n";
	}
	echo '</div>';
}


$articles = fetch($url);


print_articles($articles, 20);

?>

<footer>
	<span class="info">By Marko Klopets. Data from <a href="http://www.postimees.ee">Postimees</a>.</span>
	<span class="links">
		<a href="https://github.com/mogalful/justgirlynews" target="_blank">Github</a>
		<a href="#">Contact</a>
	</span>
</footer>

<div class="arrow"></div>
 </body>
</html>
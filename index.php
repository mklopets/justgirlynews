<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>justgirlynews</title>
	<link rel="stylesheet" href="main.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<?php

require 'simple_html_dom.php';


// BEGIN SETTINGS

$url = 'http://www.postimees.ee/';
$GLOBALS['desc_title'] = true; // whether or not to display description in title attribute
$GLOBALS['links_enable'] = true; // whether or not to make titles <a> tags
$GLOBALS['new_window'] = true;

$GLOBALS['justgirly'] = 'justgirlynews';


// END SETTINGS

function fetch($url) {
	$html = file_get_html($url);

	$articles = array();

	foreach($html->find('article') as $article) {
		$title = trim($article->find('h1')[0]->plaintext);
		if (!$title)
			continue;

		// get url to article
		// criteria: <a class="frontUrl">
		$link = '#';
		foreach(@$article->find('a') as $link) {
			if ($link->class == 'frontUrl') {
				$link = $link->href;
			}
		}

		// get article's description
		// criteria: <p itemprop="description">
		$description = '';
		foreach(@$article->find('p') as $desc) {
			if ($desc->itemprop == 'description') {
				$description = $desc->plaintext;
			}
		}

		// unappend comment number from title
		$len = strlen($title);
		while (is_numeric($title[$len - 1])) {
			$title = substr($title, 0, $len - 1);
			$len--;
		}

		// skip article if image is too small
		$img = @$article->find('img')[0];
		$width = @$img->width;
		$height = @$img->height;

		if ($width < 250 && $height < 180) {
			// soft skip over
			continue;
		}

		if ($width < 180 || $height < 180) {
			// hard skip
			continue;
		}


		// get image url
		$img_src = trim(@$img->{'data-src'});
		$img = "<img src='{$img_src}' />";
		

		$article = array(
			'title' => $title,
			'img_src' => $img_src,
			'link' => $link,
			'desc' => $description
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

		echo "<article style=\"background-image: url('{$img_src}')\">";
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

		$description = ($GLOBALS['desc_title']) ? "title='{$desc}'" : '';
		$target_attr = ($GLOBALS['new_window']) ? "target='_blank'" : '';

		$a1 = ($GLOBALS['links_enable']) ? "<a href='{$link}' {$target_attr} {$description}>" : '';
		$a2 = ($GLOBALS['links_enable']) ? '</a>' : '';

		echo '<div class="title">';
		echo "<h1>{$a1}{$title}{$a2}</h1>";
		echo "<h2>{$GLOBALS['justgirly']}</h2>";
		echo '</div>';
	}
	echo '</div>';
}


$articles = fetch($url);


print_articles($articles, 20);

?>

<footer>
	<span class="info">By Marko Klopets. Data from <a href="http://www.postimees.ee">Postimees</a>.</span>
	<span class="links">
		<a href="https://github.com/mogalful/justgirlynews">Github</a>
		<!-- <a href="#">Contact</a> -->
	</span>
</footer>

</body>
</html>
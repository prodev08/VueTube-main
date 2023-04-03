<?php
/**
 * Get Taxonomies
 * 
 * // TODO: Move this into the Base or Util class
 */

require_once('../api-setup.php');

$styles_stmt = $pdo->query("SELECT DISTINCT(style_id), S.style_name AS `name` FROM channel_styles AS CS
							LEFT JOIN styles AS S USING(style_id)");

$styles = [];
while ($row = $styles_stmt->fetch())
{
	$styles[] = $row;
}

$topic_stmt = $pdo->query("SELECT DISTINCT(topic_id), T.topic_name AS `name` FROM channel_topics AS CT
							LEFT JOIN topics AS T USING(topic_id)");


$topics = [];
while ($row = $topic_stmt->fetch())
{
	$topics[] = $row;
}

echo json_encode(
	array(
		'styles' => $styles,
		'topics' => $topics
		)
);
exit;
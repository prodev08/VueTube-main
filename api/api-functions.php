<?php 
/**
 * Various helper functions used throughout the API
 * 
 * TODO: Move these classes into a Base class which all other classes inherit
 */

 /**
  * Get the current subdomain string
  *
  * @return string
  */
function get_current_subdomain(){
	$host = explode('.', $_SERVER['HTTP_HOST']);
	//$subdomain = $host[0];
	return $host[0];
}


/**
 * Seach a text string and convert all urls with links
 *
 * @param string $s
 * @return string
 */
function displayTextWithLinks($s) {
	return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank" rel="nofollow">$1</a>', $s);
}

/**
 * Automatically wrap a url with an anchor tag
 *
 * @param string $str
 * @param boolean $popup
 * @return string
 */
function AutoLinkUrls($str, $popup = FALSE){
	if (preg_match_all("#(^|\s|\()((http(s?)://)|(www\.))(\w+[^\s\)\<]+)#i", $str, $matches)){
		$pop = ($popup == TRUE) ? " target=\"_blank\" " : "";
		for ($i = 0; $i < count($matches['0']); $i++){
			$period = '';
			if (preg_match("|\.$|", $matches['6'][$i])){
				$period = '.';
				$matches['6'][$i] = substr($matches['6'][$i], 0, -1);
			}
			$str = str_replace($matches['0'][$i],
					$matches['1'][$i].'<a href="http'.
					$matches['4'][$i].'://'.
					$matches['5'][$i].
					$matches['6'][$i].'"'.$pop.'>http'.
					$matches['4'][$i].'://'.
					$matches['5'][$i].
					$matches['6'][$i].'</a>'.
					$period, $str);
		}
	}
	
	return $str;
}


/**
 * Get the limit and offset to use in PDO queries
 * 
 * TODO: Should we move this into a Base / Utility class?
 * 
 * @param array $search 
 * @var $search[limit] int A custom limit
 * @var $search[offset] int A custom offset
 * 
 * @return array A $params array with :limit and :offset set
 */
function jb_get_limit_and_offset_params($search = [], $default_limit = 30){
	$params = [];
	$params[':limit'] = ! empty($search['limit']) && is_numeric($search['limit'])?$search['limit']:$default_limit;
	$params[':offset'] = ! empty($search['offset']) && is_numeric($search['offset'])?$search['offset']*$params[':limit']:0;

	return $params;
}


/**
 * Prepare a video for returning to the front end by cleaning quotes and formatting description
 *
 * TODO: Should we move this into a Base / Utility class?
 * 
 * @param object $video A video object from the DB
 * @return object The prepared video object
 */
function jb_prepare_video(object $video){
	$video->title = html_entity_decode($video->title, ENT_QUOTES);

	if(! empty($video->description)){
		$video->description = formatDescription($video->description);
	}

	if(! empty($video->time)){
		$video->time = jb_format_yt_time($video->time);
	}

	return $video;
}

/**
 * Format the goofy youtube time into a more readable time format
 *
 * @param string $time 00:00:00 time format
 * @return string
 */
function jb_format_yt_time($time){
	$vals  = explode(':',$time);

	if(empty($vals[0]))
	return '';
	else if ( $vals[0] == 0 || empty($vals[2]) )
	$time = (int)$vals[0] . ':' . $vals[1];
	else
	$time = (int)$vals[0] . ':' . $vals[1] . ':' . $vals[2];

	return $time;
}


/**
 * This wraps blocks of text (delimited by \n) in p tags (similar to nl2br)
 * 
 * @author Scott Dover <sdover102@gmail.com>
 * @param str
 * @return str
 */
function nl2p($string) {
	/* Explode based on new-line */
	$string_parts = explode("\n", $string);

	/* Wrap each block in a p tag */
	$string = '<p>' . implode('</p><p>', $string_parts) . '</p>';	

	/* Return the string with empty paragraphs removed */
	return str_replace("<p></p>", '', $string);	
}


/**
 * Format the text returned from youtube for display
 *
 * @param string $string
 * @return string
 */
function formatDescription($string){
	//$string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
	$string = AutoLinkUrls($string); // displayTextWithLinks
	$string = nl2p($string);
	return $string;
}
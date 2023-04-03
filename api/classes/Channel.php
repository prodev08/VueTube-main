<?php 
/**
 * Video paramaters and functionality
 * 
 */

class Channel {
	protected $pdo;
	protected $YT_KEY;
	protected $table = 'channels';

	public int $video_id;
	public string $youtube_id;
	public int $channel_id;
	public string $title;
	public string $description;
	public string $short_description;
	public string $tags;
	public string $date;
	public string $last_updated;
	public string $created;

	/**
	 * Construct our Video class
	 *
	 * @param dbconn $pdo
	 * @param string $video_id
	 */
	public function __construct($pdo, $YT_KEY){
		$this->pdo = $pdo;
		$this->YT_KEY = $YT_KEY;
	}


	/**
	 * Record a view of a channel
	 * 
	 * NOTE: We might not need / want this since we can just query all of the videos related to a channel for views.
	 *
	 * @param integer $channel_id
	 * @return void
	 */
	public function record_view(int $channel_id){
		$insert_stmt = $this->pdo->prepare("INSERT INTO channel_views (`channel_id`, `view_count`) VALUES (:channel_id, 1)
								ON DUPLICATE KEY UPDATE `view_count` = view_count+1");

		$insert_stmt->execute(['channel_id' => $channel_id]);
	}


	/**
	 * Return a list of channels 
	 *
	 * @return array
	 */
	public function get_channel_list($columns = ''){
		if(empty($columns)){
			$columns = 'youtube_id, channel_id, title, img_url, facebook, instagram, patreon, tiktok, twitter, twitch, website';
		}

		try{
			$channel_stmt = $this->pdo->query("SELECT {$columns} FROM channels ORDER BY title ASC");

			$channels = [];
			while ($channel = $channel_stmt->fetchObject())
			{	
				$channel->styles = $this->get_channel_styles($channel->channel_id);
				$channel->topics = $this->get_channel_topics($channel->channel_id);
				$channels[] = $channel;
			}

			return $channels;

		} catch (PDOException $e) {
			return false;
		}
	}

	/**
	 * Prepare a channel array for inserting / updating the db
	 *
	 * @param array $channel
	 * @return array
	 */
	public function prepare_channel_for_db(array $channel){
		$default_channel = [
			'description' => '',
			'facebook' => '', 
			'instagram' => '', 
			'patreon' => '', 
			'tiktok' => '', 
			'twitter' => '', 
			'twitch' => '', 
			'website' => ''
		];

		// Need to return some info about what is missing
		if(empty($channel['youtube_id'])) return false;
		if(empty($channel['title'])) return false;

		// Get the channel thumbnail url from YouTube
		$channel_info = $this->get_channel_info($channel['youtube_id']);

		if(! empty($channel_info)){
			$channel['description'] = $channel_info['description'];
			$channel['img_url'] = $channel_info['thumbnail'];
		}

		if(empty($channel['img_url'])) return false;

		// Merge our defaults so we know we have the params we need
		$channel = array_merge( $default_channel, $channel );

		unset($channel['styles']);
		unset($channel['topics']);
		unset($channel['focus']);

		unset($channel['channel_id']);

		return $channel;
	}


	/**
	 * Insert a channel into the database
	 * 
	 * @param array $channel An associative array of channel information
	 * 
	 * @var $channel[youtube_id] string, required The YouTube ID ex: UCHAK6CyegY22Zj2GWrcaIxg
	 * @var $channel[title] string, required The channel Title
	 * @var $channel[img_url] string, required The img url of this channel
	 * @var $channel[facebook] string, The facebook url
	 * @var $channel[instagram] string, The instagram url
	 * @var $channel[patreon] string, The patreon url
	 * @var $channel[tiktok] string, The tiktok url
	 * @var $channel[twitter] string, The twitter url
	 * @var $channel[twitch] string, The twitch url
	 * @var $channel[website] string, The website url
	 */
	public function insert_channel($channel){

		$styles = $channel['styles'];
		$topics = $channel['topics'];
		//$focus = $channel['focus'];

		$channel = $this->prepare_channel_for_db($channel);


		// TODO: Setup validation here to ensure all the URLs are actually the urls they claim to be. ie: facebook points to facebook.
		try{
			$insert_stmt = $this->pdo->prepare("INSERT INTO {$this->table} (`youtube_id`,
																`title`, 
																`description`, 
																`img_url`,
																`facebook`,
																`instagram`,
																`patreon`,
																`tiktok`,
																`twitter`,
																`twitch`,
																`website`)
											VALUES (:youtube_id,
													:title, 
													:description, 
													:img_url,
													:facebook,
													:instagram,
													:patreon,
													:tiktok,
													:twitter,
													:twitch,
													:website)");

			$insert_stmt->execute($channel);

			$new_channel = $this->get_channel_by_yt_id($channel['youtube_id'], 'channel_id');

			if($new_channel){
				if(! empty($styles) || ! empty($topics)){
					$this->update_channel_styles_and_topics($new_channel->channel_id, $styles, $topics);
				}

				return $new_channel->channel_id;
			}


		} catch (PDOException $e) {
			if(23000 == $e->getCode()){
				// Duplicate Key. For now if we get a duplicate key we are just ignoring it and calling it all good
				return true;
			}

			// print_r($e->getCode());
			print_r($e->getMessage());
			return false;
		}
	}


	/**
	 * Update a channel in the database
	 * 
	 * @param array $channel An associative array of channel information
	 * 
	 * @var $channel[youtube_id] string, required The YouTube ID ex: UCHAK6CyegY22Zj2GWrcaIxg
	 * @var $channel[title] string, required The channel Title
	 * @var $channel[img_url] string, required The img url of this channel
	 * @var $channel[facebook] string, The facebook url
	 * @var $channel[instagram] string, The instagram url
	 * @var $channel[patreon] string, The patreon url
	 * @var $channel[tiktok] string, The tiktok url
	 * @var $channel[twitter] string, The twitter url
	 * @var $channel[twitch] string, The twitch url
	 * @var $channel[website] string, The website url
	 */
	public function update_channel($channel){

		$styles = $channel['styles'];
		$topics = $channel['topics'];
		//$focus = $channel['focus'];

		$channel = $this->prepare_channel_for_db($channel);

		// TODO: Setup validation here to ensure all the URLs are actually the urls they claim to be. ie: facebook points to facebook.
		try{
			$insert_stmt = $this->pdo->prepare("UPDATE {$this->table} SET
												`title` = :title, 
												`img_url` = :img_url,
												`description` = :description,
												`facebook` = :facebook, 
												`instagram` = :instagram, 
												`patreon` = :patreon, 
												`tiktok` = :tiktok, 
												`twitter` = :twitter, 
												`twitch` = :twitch, 
												`website` = :website
												WHERE youtube_id = :youtube_id");

			$insert_stmt->execute($channel);

			$new_channel = $this->get_channel_by_yt_id($channel['youtube_id'], 'channel_id');

			if($new_channel){
				if(! empty($styles) || ! empty($topics)){
					$this->update_channel_styles_and_topics($new_channel->channel_id, $styles, $topics);
				}

				return $new_channel->channel_id;
			}

		} catch (PDOException $e) {
			//print_r($e->getCode());
			print_r($e->getMessage());
			return false;
		}
	}

	/**
	 * Clear out any styles / topics associated with a channel
	 *
	 * @param integer $channel_id
	 * @return bool
	 */
	public function clear_styles_and_topics(int $channel_id){
		try{
			$style_stmt = $this->pdo->prepare("DELETE FROM channel_styles WHERE channel_id = :channel_id");
			$style_stmt->execute(['channel_id' => $channel_id]);

			$topic_stmt = $this->pdo->prepare("DELETE FROM channel_topics WHERE channel_id = :channel_id");
			$topic_stmt->execute(['channel_id' => $channel_id]);

			return true;
		} catch (PDOException $e) {

			// print_r($e->getCode());
			//print_r($e->getMessage());
			return false;
		}
	}

	/**
	 * Update a channels styles and topics
	 *
	 * @param integer $channel_id
	 * @param array $styles
	 * @param array $topics
	 * @return void
	 */
	public function update_channel_styles_and_topics(int $channel_id, array $styles, array $topics){
		$this->clear_styles_and_topics($channel_id);

		if(! empty($styles)){
			$this->insert_channel_styles($styles, $channel_id);
		}

		if(! empty($topics)){
			$this->insert_channel_topics($topics, $channel_id);
		}
		
	}


	/**
	 * Insert Channel Styles
	 *
	 * @param array $style_ids An array of style IDs to associate with this channel
	 * @param int $channel_id The channel id to associate with these styles
	 * @return void
	 */
	public function insert_channel_styles(array $style_ids, int $channel_id){
		if(empty($style_ids) || ! is_array($style_ids)) return;

		try{
			foreach($style_ids as $style_id){
				// We are using ON DUPLICATE KEY UPDATE to essentially ignore duplicates which would throw an exception and stop all other inserts
				$channel_stmt = $this->pdo->prepare("INSERT INTO channel_styles (`channel_id`, `style_id`) 
												VALUES (:channel_id, :style_id)
												ON DUPLICATE KEY UPDATE 
												`style_id` = :dstyle_id");

				$channel_stmt->execute(['channel_id' => $channel_id, 'style_id' => $style_id, 'dstyle_id' => $style_id]);
			}
		} catch (PDOException $e) {
			//return false;
		}
	}


	
	/**
	 * Insert Channel Topics
	 *
	 * @param array $topic_ids An array of topic IDs to associate with this channel
	 * @param int $channel_id The channel id to associate with these topics
	 * @return void
	 */
	public function insert_channel_topics(array $topic_ids, int $channel_id){
		if(empty($topic_ids) || ! is_array($topic_ids)) return;

		try{
			foreach($topic_ids as $topic_id){
				// We are using ON DUPLICATE KEY UPDATE to essentially ignore duplicates which would throw an exception and stop all other inserts
				$channel_stmt = $this->pdo->prepare("INSERT INTO channel_topics (`channel_id`, `topic_id`) 
												VALUES (:channel_id, :topic_id)
												ON DUPLICATE KEY UPDATE 
												`topic_id` = :dtopic_id");

				$channel_stmt->execute(['channel_id' => $channel_id, 'topic_id' => $topic_id, 'dtopic_id' => $topic_id]);
			}

		} catch (PDOException $e) {
			//return false;
		}
	}



	/**
	 * Get channel information from the database by YouTube ID
	 *
	 * @param string $youtube_id YouTube channel ID
	 * @param string $columns The columns to retreive
	 * @return object
	 */
	public function get_channel_by_yt_id(string $youtube_id, $columns = '*'){
		try{
			$channel_stmt = $this->pdo->prepare("SELECT {$columns} FROM channels WHERE youtube_id = :youtube_id");

			$channel_stmt->execute(['youtube_id' => $youtube_id]);

			return $channel_stmt->fetchObject();

		} catch (PDOException $e) {
			return false;
		}
	}


	/**
	 * Get an array of style_ids from a channel
	 *
	 * @param integer $channel_id
	 * @return array
	 */
	public function get_channel_styles(int $channel_id){
		try{
			$style_stmt = $this->pdo->prepare("SELECT style_id FROM channel_styles 
											WHERE `channel_id` = :channel_id");

			$style_stmt->execute(['channel_id' => $channel_id]);

			$style_ids = [];
			while ($style = $style_stmt->fetchObject())
			{
				$style_ids[] = $style->style_id;
			}

			return $style_ids;
		} catch (PDOException $e) {
			return [];
		}
	}


	/**
	 * Get an array of topic_ids from a channel
	 *
	 * @param integer $channel_id
	 * @return array
	 */
	public function get_channel_topics(int $channel_id){
		try{
			$topic_stmt = $this->pdo->prepare("SELECT topic_id FROM channel_topics 
											WHERE `channel_id` = :channel_id");

			$topic_stmt->execute(['channel_id' => $channel_id]);

			$topic_ids = [];
			while ($topic = $topic_stmt->fetchObject())
			{
				$topic_ids[] = $topic->topic_id;
			}

			return $topic_ids;
		} catch (PDOException $e) {
			return [];
		}
	}


	/**
	 * Get an array of channel_ids related to the queried channel
	 *
	 * @param int $channel_id
	 * @return array
	 */
	public function get_related_channels(int $channel_id){

		$style_ids = $this->get_channel_styles($channel_id);

		$topic_ids = $this->get_channel_topics($channel_id);

		$channel_ids = [];
		if(! empty($style_ids)){

			try{
				// What channels share these styles or topics?
				$related_stmt = $this->pdo->query("SELECT DISTINCT(channel_id) FROM channel_styles 
													LEFT JOIN channel_topics USING(channel_id)
													WHERE `style_id` IN (".implode(',',$style_ids).")
														OR `topic_id` IN (".implode(',',$topic_ids).")");

				while ($channel = $related_stmt->fetchObject())
				{
					$channel_ids[] = $channel->channel_id;
				}

				return $channel_ids;
			} catch (PDOException $e) {
				return $channel_ids;
			}
		}

		return $channel_ids;
	}


	/**
	 * Get channel information from the database by YouTube ID
	 *
	 * @param int $channel_id Channel ID
	 * @param string $columns The columns to retreive
	 * @return object
	 */
	public function get_channel_by_channel_id(int $channel_id, $columns = '*'){
		try{
			$channel_stmt = $this->pdo->prepare("SELECT {$columns} FROM channels WHERE channel_id = :channel_id");

			$channel_stmt->execute(['channel_id' => $channel_id]);

			return $channel_stmt->fetchObject();

		} catch (PDOException $e) {
			return false;
		}
	}


	/**
	 * Get this Channels thumbnail and set it
	 * 
	 * @param int $post_id The ID of the post to get the featured image for
	 * @return string the URL of the post thumbnail
	 */
	public function get_channel_info($youtube_id){

		if(empty($youtube_id )) return;

		$url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id='.$youtube_id.'&key='.$this->YT_KEY;

		$result = @file_get_contents($url);

		// If we have a result, cache the info for 1 month.
		if(! empty($result)){
			$channel_obj = json_decode( $result );

			$info = [];

			// print_r($channel_obj->items[0]->snippet);
			if(! empty($channel_obj->items[0]->snippet->description)){
				$info['description'] = $channel_obj->items[0]->snippet->description;
			}else{
				$info['description'] = '';
			}

			$info['thumbnail'] = $channel_obj->items[0]->snippet->thumbnails->default->url;

			return $info;
		}

		return [];
	}


	/**
	 * Get the last X videos from a channel and store them for later
	 *
	 * @param int $channel_id The YouTube channel ID
	 * @param int $max_videos The maximum number of videos 
	 * @return array
	 */
	public function get_channel_videos($channel_id, $max_videos = 120){

		$videos = [];

		if(! empty($channel_id)){
			$done = false;
			$safety = 20;
			$channel_obj = null;
			$max_results = min($max_videos, 20);

			$count = 0;
			while(! $done){
				$count++;

				$url = 'https://www.googleapis.com/youtube/v3/search?key='.$this->YT_KEY.'&channelId='.$channel_id.'&part=snippet&order=date&maxResults='.$max_results;

				if($channel_obj && ! empty($channel_obj->nextPageToken)){
					$url .= '&pageToken='.$channel_obj->nextPageToken;
				}

				$result = @file_get_contents($url);

				if($result){

					$channel_obj = json_decode( $result );

					$videos = array_merge($videos, $channel_obj->items);

					if(count($videos) >= $max_videos
					|| $count > $safety){
						$done = true;
						break;
					}
				}else{
					$done = true;
					break;
				}
			}

		}

		return $videos;
	}

	/**
	 * Search our channels based on search parameters
	 *
	 * @param array $search
	 * @var $search[style] int A style_id to search
	 * @var $search[topic] int A topic_id to search
	 * @var $search[s] string String to search channel titles
	 * @var $search[rand] bool shoudl this be a random order?
	 * 
	 * @param int $limit The numnber of videos to return 
	 * @return array
	 */
	public function search($search = [], $limit = 20, $columns = ''){

		if(empty($columns)){
			$columns = 'youtube_id, C.channel_id, title, img_url, facebook, instagram, patreon, tiktok, twitter, twitch, website';
		}

		$params = jb_get_limit_and_offset_params($search, $limit);

		$search_query = "SELECT {$columns} FROM channels AS C ";

		if(! empty($search['style'])){
			$search_query .= " LEFT JOIN channel_styles AS CS ON CS.channel_id = C.channel_id ";
		}

		if(! empty($search['topic'])){
			$search_query .= " LEFT JOIN channel_topics AS CT ON CT.channel_id = C.channel_id ";
		}

		$search_query .= " WHERE 1=1 ";

		if(! empty($search['s'])){
			// TODO: We may want to also do a search for videos with titles or tags which match this value and return DISTINCT(channel_id). 
			// 		That way it isn't just channel's being searched

			$search_query .= " AND title LIKE :title ";
			$params[':title'] = '%'.$search['s'].'%';
		}

		if(! empty($search['style'])){
			$search_query .= " AND CS.style_id = :style ";
			$params[':style'] = $search['style'];
		}

		if(! empty($search['topic'])){
			$search_query .= " AND CT.topic_id = :topic ";
			$params[':topic'] = $search['topic'];
		}

		if(! empty($search['rand'])){
			$search_query .= " ORDER BY RAND() ";
		}else{
			$search_query .= " ORDER BY title ASC ";
		}

		$search_query .= " LIMIT :offset, :limit";

		try{
			$channel_stmt = $this->pdo->prepare($search_query);

			$channel_stmt->execute($params);
		} catch (PDOException $e) {
			//echo $e->getMessage();
			return false;
		}

		$channels = [];
		while ($channel = $channel_stmt->fetchObject())
		{
			//$channel->img_name = str_replace(' ', '-', $channel->title).'.png';

			$channel->videos = $this->get_channel_videos_by_channel_id($channel->channel_id);
			
			$channels[] = $channel;
		}
		
		

		return $channels;
	}


	/**
	 * Get a channels videos by the channel id
	 *
	 * @param integer $channel_id
	 * @param integer $limit
	 * @param string $columns Custom columns to retreive
	 * 
	 * @return array An array of videos prepared for display
	 */
	public function get_channel_videos_by_channel_id(int $channel_id, int $limit = 20, $columns = 'video_id, youtube_id, channel_id, title, `date`, `time`'){

		$videos = [];
		try{
			$video_stmt = $this->pdo->query("SELECT {$columns} FROM videos WHERE channel_id = {$channel_id} LIMIT {$limit}");

			while ($video = $video_stmt->fetchObject())
			{
				$videos[] = jb_prepare_video($video);
			}

			return $videos;
			
		} catch (PDOException $e) {
			return false;
		}

		return $videos;
	}


	

	
}
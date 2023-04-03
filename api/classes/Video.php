<?php 
/**
 * Video paramaters and functionality
 * 
 */

class Video {
	protected $pdo;
	protected $YT_KEY;
	protected $table = 'videos';

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

	protected $User;


	/**
	 * Construct our Video class
	 *
	 * @param dbconn $pdo
	 * @param string $video_id
	 */
	public function __construct($pdo, $YT_KEY){
		$this->pdo = $pdo;
		$this->YT_KEY = $YT_KEY;

		$this->User = new User($this->pdo);
	}

	/**
	 * Record a view of a video
	 *
	 * @param integer $video_id
	 * @return void
	 */
	public function record_view(int $video_id){
		// Record this video's view
		$insert_stmt = $this->pdo->prepare("INSERT INTO video_views (`video_id`, `view_count`) VALUES (:video_id, 1)
		ON DUPLICATE KEY UPDATE `view_count` = view_count+1");

		$insert_stmt->execute(['video_id' => $video_id]);
	}


	/**
	 * Search videos based on search parameters
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
			$columns = 'V.video_id, V.youtube_id, V.channel_id, V.title, V.`date`';
			// , C.youtube_id AS channel_youtube, C.title AS channel_title
		}

		$params = jb_get_limit_and_offset_params($search, $limit);

		$search_query = "SELECT * FROM videos AS V ";

		// if(! empty($_GET['style'])){
		// 	$search_query .= " LEFT JOIN channel_styles AS CS ON CS.channel_id = C.channel_id ";
		// }

		// if(! empty($_GET['topic'])){
		// 	$search_query .= " LEFT JOIN channel_topics AS CT ON CT.channel_id = C.channel_id ";
		// }

		$search_query .= " WHERE 1=1 ";

		if(! empty($_GET['s'])){
			$search_query .= " AND ( title LIKE :title OR tags LIKE :tags) ";
			$params[':title'] = '%'.$_GET['s'].'%';
			$params[':tags'] = '%#'.$_GET['s'].',%';
		}

		if(! empty($_GET['channel_id'])){
			$search_query .= " AND channel_id = :channel_id ";
			$params[':channel_id'] = $_GET['channel_id'];
		}

		// if(! empty($_GET['style'])){
		// 	$search_query .= " AND CS.style_id = %d ", $_GET['style'];
		// }

		// if(! empty($_GET['topic'])){
		// 	$search_query .= " AND CT.topic_id = %d ", $_GET['topic'];
		// }

		if(! empty($_GET['rand'])){
			$search_query .= " ORDER BY RAND() ";
		}else{
			$search_query .= " ORDER BY V.title ASC ";
		}

		$search_query .= " LIMIT :offset, :limit";

		$video_stmt = $this->pdo->prepare($search_query);

		$video_stmt->execute($params);

		$videos = [];
		while ($video = $video_stmt->fetchObject())
		{
			$videos[] = jb_prepare_video($video);
		}

		return $videos;
	}

	
	/**
	 * Insert a video into the database
	 * 
	 * @param array $video An associative array of video information
	 * 
	 * @var $video[youtube_id] string, required The YouTube ID ex: QMpkFde3euA
	 * @var $video[channel_id] int, required The ID of the related channel
	 * @var $video[title] string, required The video Title
	 * @var $video[tags] string, The video's tags in a single comma delimited string. ex: #example, #example2
	 * @var $video[description] string, The video description directly from youtube without HTML or formatting
	 * @var $video[date] required string, The date this video was posted to YouTube
	 */
	public function insert_video($video){

		$default_video = [
			'tags' => '', 
			'description' => '', 
			'short_description' => ''
		];

		

		// Need to return some info about what is missing
		if(empty($video['youtube_id'])) return false;
		if(empty($video['channel_id'])) return false;
		if(empty($video['title'])) return false;
		if(empty($video['date'])) return false;

		$video['title'] = str_replace('&#39;',"'",$video['title']);

		// Merge our defaults so we know we have the params we need
		$video = array_merge( $default_video, $video );
		
		$columns = array(
			'youtube_id', 'channel_id', 'title', 'tags', 'description', 'short_description', 'date', 'time', 'raw'
		);

		$insert_string = "INSERT INTO {$this->table} (`".implode('`,`', $columns)."`) 
		VALUES (:".implode(',:', $columns).") 
		ON DUPLICATE KEY UPDATE ";
		foreach($columns as $column){
			$video[$column.'2'] = ! empty($video[$column])?$video[$column]:'';
			$insert_string .= '`'.$column.'` = :'.$column.'2,';
		}

		$insert_string = rtrim($insert_string, ',');
		
		try{

			$insert_stmt = $this->pdo->prepare($insert_string);
			$insert_stmt->execute($video);

			return true;
		} catch (PDOException $e) {
			if(23000 == $e->getCode()){
				// Duplicate Key. For now if we get a duplicate key we are just ignoring it and calling it all good
				return true;
			}

			// print_r($insert_string);
			// print_r($video);

			print_r($e->getCode());
			print_r($e->getMessage());
			return false;
		}
	}


	/**
	 * Convert the returned channel items into video arrays for saving
	 *
	 * @param array $items An array of channel_obj->items returned from youtube API
	 * @param int $channel_id The id of this channel in our database
	 * @return array An array of videos
	 */
	function channel_items_to_videos($items, $channel_id){
		
		$videos = array();

		if($items){
			foreach($items as $item){
				if(empty($item->id->videoId)) continue;
				$videos[] = array(
					'youtube_id' => $item->id->videoId,
					'channel_id' => $channel_id,
					'title' => $item->snippet->title,
					//$short_description = $video->snippet->description;
					'short_description' => $item->snippet->description,//sanitize_text_field(addslashes($item->snippet->description)),
					//'tags' => '#'.implode(',#',$item->snippet->tags).',',
					'date' => date('Y-m-d', strtotime($item->snippet->publishTime))
				);
			}
		}

		return $videos;
	}

	function covtime($youtube_time){
		if($youtube_time) {
			$start = new DateTime('@0'); // Unix epoch
			$start->add(new DateInterval($youtube_time));
			$youtube_time = $start->format('H:i:s');
		}
		
		return $youtube_time;
	}  


	/**
	 * Get a single video row
	 * 
	 * @param int $video_id The ID of the video in question
	 * @param string $columns The columns in question
	 * 
	 * @return object Video row
	 */
	public function get_video_info($video_id, $columns = '*'){

		$video_stmt = $this->pdo->prepare("SELECT {$columns} FROM videos WHERE video_id = :video_id");
		$video_stmt->execute(['video_id' => $video_id]);
		$video = $video_stmt->fetchObject();

		return $video;
	}


	/**
	 * Get YouTube information for a single video
	 *
	 * @param string $youtube_id AKA This Video's youtube ID
	 * @return object Returns object if successfull or false on failure
	 */
	public function get_video_info_from_yt($youtube_id){

		$url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id='.$youtube_id.'&key='.$this->YT_KEY;
		
		$result = @file_get_contents($url);

		if($result){
			$result_obj = json_decode( $result );
			// TODO: setup check on what we return?
			
			return $result_obj->items[0];
		}else{
			return false;
		}
	}


	/**
	 * Return an array of video detail information formated for use in the DB.
	 *
	 * @param object $video_info The information returned from youTube in the get_video_info_from_yt function
	 * @return array
	 */
	public function get_video_detail($video_info){
		$video = array();
		if(! empty($video_info)){
			$video['description'] = $video_info->snippet->description?$video_info->snippet->description:'';
			$video['time'] = $this->covtime($video_info->contentDetails->duration);

			if($video['time']){
				$video['time'] = jb_format_yt_time($video['time']);
			}

			$video['raw'] = json_encode($video_info);

			if(! empty($video_info->tags)){
				$video['tags'] = '#'.implode(',#',$video_info->snippet->tags).',';
			}

		}

		return $video;
	}


	/**
	 * Insert an array of videos in the database
	 *
	 * @param array $videos
	 * @return void
	 */
	public function insert_videos($videos){
		if(empty($videos)) return false;

		foreach($videos as $video){

			// The video information returned from the channel is only a brief description.
			$video_info = $this->get_video_info_from_yt($video['youtube_id']);
			$video_details = $this->get_video_detail($video_info);
			$video = array_merge($video, $video_details);
			
			// TODO: should probably send some kind of error if a video doesn't update?
			$result = $this->insert_video($video);

			if(! $result) return false;
		}

		return true;
	}


	/**
	 * GEt a list of videos with some related channel information
	 *
	 * @param array $search
	 * @var $search[channel_id] int, required A channel_id to search by
	 * 
	 * @param integer $limit
	 * @param string $columns
	 * @return array
	 */
	public function get_video_list($search = [], $limit = 20, $columns = ''){

		if(empty($search['channel_id'])) return [];

		if(empty($columns)){
			$columns = 'V.video_id, V.youtube_id, V.channel_id, V.title, V.`date`, V.`time`, C.youtube_id AS channel_youtube, C.title AS channel_title';
		}
		
		$params = jb_get_limit_and_offset_params($search, $limit);

		$video_query = "SELECT {$columns} FROM videos AS V 
						LEFT JOIN channels AS C ON V.channel_id = C.channel_id
						WHERE V.channel_id = :channel_id ";

		if(! empty($search['s'])){
			$video_query .= " AND title LIKE :search "; //, $_GET['s'].'%';
			$params[':search'] = $search['s'];
		}

		$video_query .= " LIMIT :offset, :limit";

		$video_stmt = $this->pdo->prepare($video_query);

		$params[':channel_id'] = $search['channel_id'];

		$video_stmt->execute($params);

		$videos = [];
		while ($video = $video_stmt->fetchObject())
		{
			$videos[] = jb_prepare_video($video);
		}

		return $videos;
	}


	/**
	 * Get a video with associated user information, such as Liked, Watch Later, etc.
	 *
	 * @param string $youtube_id
	 * @param int $user_id The user that has interacted with this video
	 * @return object
	 */
	public function get_video_with_user_info(string $youtube_id, int $user_id){

		if($user_id){
			try{
				$video_stmt = $this->pdo->prepare("SELECT V.*, C.youtube_id AS channel_youtube, L.liked_id AS isLiked, WL.watch_id AS isSaved FROM videos AS V 
						LEFT JOIN channels AS C ON V.channel_id = C.channel_id
						LEFT JOIN liked AS L ON V.video_id = L.video_id AND L.user_id = :user_id_1
						LEFT JOIN watch_later AS WL ON V.video_id = WL.video_id AND WL.user_id = :user_id_2
						WHERE V.`youtube_id` = :youtube_id");
				
				$video_stmt->execute(['user_id_1' => $user_id, 'user_id_2' => $user_id, 'youtube_id' => $youtube_id]);
				
				$video = $video_stmt->fetchObject();

				$video = jb_prepare_video($video);

				return $video;
			} catch (PDOException $e) {
				return false;
			}
		}

		return false;
	}

	/**
	 * Get a video from the database based on the youtube_id
	 *
	 * @param string $youtube_id
	 * @return object
	 */
	public function get_video_by_yt_id(string $youtube_id){
		if(empty($youtube_id)) return false;

		try{
			$video_stmt = $this->pdo->prepare("SELECT V.*, C.youtube_id AS channel_youtube FROM videos AS V 
				LEFT JOIN channels AS C ON V.channel_id = C.channel_id
				WHERE V.`youtube_id` = :youtube_id");

			$video_stmt->execute(['youtube_id' => $youtube_id]);

			$video = $video_stmt->fetchObject();

			$video = jb_prepare_video($video);

			return $video;
		} catch (PDOException $e) {
			return false;
		}

		return false;
	}


	/**
	 * Get a random list of videos related to an array of channels
	 *
	 * @param array $channel_ids
	 * @param integer $limit
	 * @return array An array of videos
	 */
	public function get_videos_in_channels(array $channel_ids, $limit = 30){

		try{
			$video_stmt = $this->pdo->prepare("SELECT V.video_id, V.youtube_id, V.channel_id, V.title, V.`date`, C.youtube_id AS channel_youtube, C.title AS channel_title FROM videos AS V 
									LEFT JOIN channels AS C ON V.channel_id = C.channel_id
									WHERE V.`channel_id` IN (".implode(',',$channel_ids).")
									ORDER BY RAND()
									LIMIT :limit");

			$video_stmt->execute(['limit' => $limit]);

			$videos = [];
			while ($video = $video_stmt->fetchObject())
			{
				$videos[] = jb_prepare_video($video);
			}

			return $videos;
		} catch (PDOException $e) {
			return [];
		}

		return [];
	}

}
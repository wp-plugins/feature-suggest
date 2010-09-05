<?php

// This is a smart class extended by all object types (Suggestion, Vote).
// Its purpose is to provide the _get magic method.
class Entity {
	private $data = array();

	public function __construct($arr = array())	{
		if(!empty($arr)){
			// The $arr array is passed only when we manually
			// create an object of this class in ajax.php
			$this->data = $arr;
		}
	}

	public function __get($property) {
		// This is a magic method that is called if we
		// access a property that does not exist.
		if(array_key_exists($property,$this->data)){
			return $this->data[$property];
		}
		return NULL;
	}
}

class Suggestion extends Entity {
	public function __toString() {
		// This is a magic method which is called when
		// converting the object to string:
		return '
		<li id="s'.$this->id.'">
			<div class="vote '.($this->has_voted ? 'inactive' : 'active').'">
				<span class="up"></span>
				<span class="down"></span>
			</div>

			<div class="text"><a href="'.$this->permalink.'" title="'.$this->title.'">'.$this->title.'</a></div>
			<div class="rating">'.(int)$this->rating.'</div>
		</li>';
	}

	public function save() {
		// insert a new 'suggestion' post then get its id and permalink
		$new_post = array(
			'post_title' => $this->title,
			'post_content' => $this->content,
			'post_status' => 'publish',
			'post_date' => date('Y-m-d H:i:s'),
			'post_type' => 'suggestion'
		);

		$this->id = wp_insert_post($new_post);
		$this->permalink = get_permalink($this->id);
		add_post_meta($this->id, 'rating', 0, true);
	}
}

class Vote extends Entity {
	private $meta_keys = array('votes_up', 'votes_down', 'rating');

	public function __construct($arr = array()) {
		parent::__construct($arr);
		// after this we should have $votes_up, $votes_down and $rating set
		foreach( $this->meta_keys as $key ) {
			$this->$key = get_post_meta($this->id, $key, true);
			if( empty($this->$key) ) $this->$key = 0;
		}
	}

	public function __toString() {
		return 'vote_' . md5($this->id . $this->ip . date('Y-m-d'));
	}

	public function exists() {
		$vote_meta = get_post_meta($this->id, (string)$this, true);
		return ! empty($vote_meta); // if the key exists, the user has voted
	}

	public function updateMeta() {
		// update the values
		if($this->v == 1) {	$this->votes_up++; } else { $this->votes_down++; }
		$this->rating = $this->rating + $this->v;

		// and save them
		foreach( $this->meta_keys as $key ) {
			update_post_meta($this->id, $key, $this->$key);
		}
	}

	public function save() {
		return add_post_meta($this->id, (string)$this, true, true);
	}
}

?>
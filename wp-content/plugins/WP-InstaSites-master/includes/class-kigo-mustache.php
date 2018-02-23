<?php


class Kigo_Mustache_Loader_By_Name implements Mustache_Loader {

	public $templates;

	public function __construct( $templates ) {
		$this->templates = $templates;
	}

	public function load( $name ){
		if(
			!preg_match( '#<script\s+id=["\']' . $name . '["\'][^>]*?>(.*?)<\/script>#s', $this->templates, $matches ) ||
			!is_array( $matches ) ||
			count( $matches ) != 2 ||
			!is_string( $matches[1] )
		) {
			return '';
		}

		return $matches[1];
	 }
}

<?php

namespace MG_Core\modules;

abstract class Base {
	private $id;
	private $name;
	private $description;
	private $version;
	private $author;
	private $author_uri;

	/**
	 * @return mixed
	 */
	public function get_author_uri() {
		return $this->author_uri;
	}

	/**
	 * @param mixed $author_uri
	 * @return Base
	 */
	public function set_author_uri( $author_uri ) {
		$this->author_uri = $author_uri;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_author() {
		return $this->author;
	}

	/**
	 * @param mixed $author
	 *
	 * @return Base
	 */
	public function set_author( $author ) {
		$this->author = $author;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @param mixed $version
	 *
	 * @return Base
	 */
	public function set_version( $version ) {
		$this->version = $version;

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param mixed $module_name
	 *
	 * @return Base
	 */
	public function set_name( $module_name ) {
		$this->name = $module_name;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @param mixed $module_description
	 *
	 * @return Base
	 */
	public function set_description( $module_description ) {
		$this->description = $module_description;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param mixed $module_id
	 *
	 * @return Base
	 */
	public function set_id( $module_id ) {
		$this->id = $module_id;

		return $this;
	}

	/**
	 * Kicks off the module. This is where all actions should take.
	 */
	public function run() {

	}
}
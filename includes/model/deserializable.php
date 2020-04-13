<?php

namespace cm\includes\model;

interface Deserializable {
	/**
	 * @param array $input
	 *
	 * @return static
	 */
	public static function deserialize($input = array());
}

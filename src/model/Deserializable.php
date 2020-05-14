<?php

namespace appsaloon\cm\model;

interface Deserializable {
	/**
	 * @param array $input
	 *
	 * @return static
	 */
	public static function deserialize($input = array());
}

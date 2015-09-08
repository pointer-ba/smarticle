<?php

namespace PointerBa\Smarticle;

class Parser {

	/**
	 * @var array
	 *
	 * Items that will no longer be parsed (items that match the format but do not implement a ParsableInterface
	 */
	protected $ignoredMatches = [];

	/**
	 * @param $match
	 * @return string|null
	 *
	 * Renders html based on the renderHtml($id) method of resource class
	 * If no result is found, renders null and appends the match to the ignored matches
	 */
	protected function renderHtml($match)
	{
		$trimmed = substr($match, 2, strlen($match) - 4);

		$pieces = explode(':', $trimmed);

		if (count($pieces) == 2)
		{
			$class = $pieces[0];

			if (class_exists($class) && in_array('PointerBa\Smarticle\ParsableInterface', class_implements($class)))
			{
				$object = new $class;

				return $object->renderHtml($pieces[1]);
			}
		}

		$this->ignoredMatches[] = $match;

		return null;
	}

	/**
	 * @param $data
	 * @return array
	 *
	 * Fetches matches in current content based on format {{Namespace\Resource:identifier}}
	 */
	protected function getMatches($data)
	{
		preg_match_all("/\{\{([^}]+)\}\}/", $data, $matches);

		$real_matches = array_unique($matches[0]);

		foreach ($real_matches as $key => $match)
			if (in_array($match, $this->ignoredMatches))
				unset($real_matches[$key]);

		return $real_matches;
	}


	/**
	 * @param $data
	 * @return mixed
	 *
	 * Parses given content and returns content with rendered embedded views
	 * This method will recursively parse embedded content
	 */
	public function parse($data)
	{
		$matches = $this->getMatches($data);

		while (!empty($matches))
		{
			foreach ($matches as $match)
				if ($replacement = $this->renderHtml($match))
					$data = str_replace($match, $replacement, $data);

			$matches = $this->getMatches($data);
		}

		return $data;
	}

}
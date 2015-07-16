<?php

namespace PointerBa\Smarticle;

trait ParsingEloquentTrait {

    /**
     * @param $field
     * @return mixed
     *
     * Eloquent implementation of accessField method for ParsingInterface
     */
	public function accessField($field)
	{
		return $this->getAttribute($field);
	}

    /**
     * @param string $field
     * @return mixed
     *
     * Eloquent implementation of parse method for ParsingInterface
     */
	public function parse($field = 'content')
	{
		return (new Parser)->parse($this->accessField($field));
	}

}
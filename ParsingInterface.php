<?php

namespace PointerBa\Smarticle;

interface ParsingInterface {

    /**
     * @param $field
     * @return mixed
     *
     * Accessor for class field that needs to be parsed
     */
	public function accessField($field);

    /**
     * @return mixed
     *
     * The actual parsing for field
     */
	public function parse($field);
	
}
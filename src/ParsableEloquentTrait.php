<?php

namespace PointerBa\Smarticle;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

trait ParsableEloquentTrait {

    /**
     * @var null
     *
     * Custom html view for parsing ("CLASS_NAME/_html") by default
     */
	protected $renderHtmlView = null;

    /**
     * @param $id
     * @return Model|null
     *
     * Eloquent implementation for getInstance method for ParsableInterface
     * Returns a model instance based on identifier
     */
	public function getInstance($id)
	{
        if (class_exists($repoClassName = "App\\Repositories\\" . class_basename($this) . "Repository"))
            return (new $repoClassName)->find($id);

		return $this->find($id);
	}

    /**
     * @param $id
     * @return null|string
     *
     * Returns a rendered view for a given identifier of a resource
     */
	public function renderHtml($id)
	{
		if ($instance = $this->getInstance($id))
		{
			$class = explode('\\', get_class($this));
			$class_name = $class[count($class) - 1];

			$view = strtolower($this->renderHtmlView ?: $class_name . '/_html');

            view($view, [strtolower($class_name) => $instance])->render();

			return (string) view($view, [strtolower($class_name) => $instance]);
		}

		return null;
	}

    /**
     * @param $query
     * @param string $key
     *
     * adds a parsableKey to the select
     */
	public function scopeWithParsableKey($query, $key = 'parsableKey')
	{
		$class = get_class($this);
		$pk = $this->getKeyName();
		$table = $this->getTable();

		$class = str_replace('\\', '\\\\\\', $class);

		$query->addSelect(DB::raw("CONCAT(\"\{\{{$class}:\", {$table}.{$pk}, \"\}\}\") AS `{$key}`"));
	}

}
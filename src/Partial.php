<?php

namespace PointerBa\Smarticle;

use App\ModelParser\ParsableInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;

class Partial implements ParsableInterface {

    /**
     * Path of editable custom partials
     */
    const CUSTOM_PATH = '../resources/views/partials/custom/';

    /**
     * @var string
     *
     * Name of partial
     */
    public $name;

    /**
     * @var string
     *
     * Content of partial
     */
    public $content;

    /**
     * @param $name
     * @return string
     *
     * Make partial filename based on name
     */
    protected static function filenameFromName($name)
    {
        return static::CUSTOM_PATH . "{$name}.blade.php";
    }

    /**
     * @param $filename
     * @return string
     *
     * Make partial name based on filename
     */
    protected static function nameFromFilename($filename)
    {
        $fragments = explode('/', $filename);

        $nameWithExtension = $fragments[count($fragments) - 1];

        return basename($nameWithExtension, '.blade.php');
    }

    /**
     * @param null $name
     *
     * make a new partial based on its name
     */
    public function __construct($name = null)
    {
        if ($name)
        {
            if (file_exists($path = static::filenameFromName($name)))
            {
                $this->name = $name;
                $this->content = file_get_contents($path);
            }

            else
                throw new ModelNotFoundException;
        }
    }

    /**
     * @param $name
     * @return Partial
     *
     * find partial by name
     */
    public static function find($name)
    {
        return new static($name);
    }

    /**
     * @param $name
     * @param $data
     * @return Partial
     *
     * Update partial content based on it's name
     */
    public static function update($name, $data)
    {
        $partial = static::find($name);

        $partial->content = isset($data['content']) ? $data['content'] : null;

        return $partial->save();
    }

    /**
     * @return Collection
     *
     * get all custom partials as an Eloquent Collection
     */
    public static function allCustomPartials()
    {
        $partials = new Collection;

        $files = File::files(static::CUSTOM_PATH);

        foreach ($files as $file)
        {
            $partial = new Partial;

            $partial->name = static::nameFromFilename($file);
            $partial->content = file_get_contents($file);

            $partials->add($partial);
        }

        return $partials;
    }

    /**
     * @return $this
     *
     * saves current content to file
     */
    public function save()
    {
        file_put_contents(static::filenameFromName($this->name), $this->content);

        return $this;
    }

    /**
     * @param $id
     * @return string
     *
     * Implemented ParsableInterface
     */
    public function getInstance($id) 
    {
        return "partials.{$id}";
    }

    /**
     * @param $id
     * @return \Illuminate\View\View
     *
     * Implemented ParsableInterface
     */
    public function renderHtml($id)
    {
        return view($this->getInstance($id));
    }

}
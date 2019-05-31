<?php
/**
 * Created by PhpStorm.
 * User: nnicu
 * Date: 13.05.2019
 * Time: 20:37
 */

class View
{
    protected $file;
    protected $data;
    protected $title = "";

    /**
     * View constructor.
     * @param $view_file
     * @param $view_data
     */
    public function __construct($view_file, $view_data)
    {
        $this->file = $view_file;
        $this->data = $view_data;
        $this->title = ucfirst(dirname($view_file));
    }

    public function render()
    {
        if (file_exists(VIEW . $this->file . '.phtml')) {
            include VIEW . $this->file . '.phtml';
        }
        else
        {
            http_response_code(404);
            include( VIEW . '404.phtml');
        }
    }
}
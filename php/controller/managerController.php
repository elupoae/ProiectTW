<?php
/**
 * User: Nicu Neculache
 * Date: 13.05.2019
 * Time: 23:51
 */

class managerController extends Controller
{
    public function index()
    {
        $this->view('manager' . DIRECTORY_SEPARATOR . 'index');
        $this->view->render();
    }
}
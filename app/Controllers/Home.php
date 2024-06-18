<?php 
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;
use App\Models\Model;

class Home
{
    public function index(): string
    {
        Model::Init();

        $view = IndexView::Begin();
        $view .= IndexView::Home();
        $view .= IndexView::End();

        return $view;
    }
}

<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;

class Tata
{
    public function tata(): string
    {
        $view = IndexView::Begin();
        $view.=IndexView::StartTitle('Tatabányai depó főoldal');
       
        $view .= IndexView::End();

        return $view;
    }
}
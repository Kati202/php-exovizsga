<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;

class Halas
{
    public function halas(): string
    {
        $view=IndexView::Begin();
        $view.=IndexView::StartTitle('Kiskunhalasi depó főoldal');
        $view.=IndexView::End();
        return $view;
    }
}
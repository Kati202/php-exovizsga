<?php
namespace App\Controllers;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

use App\Views\IndexView;

class Nyerges
{
    public function nyerges(): string
    {
       
        $view=IndexView::Begin();
        $view.=IndexView::StartTitle('Nyergesújfalui depó főoldal');
        
        $view.=IndexView::End();
        return $view;
    }
}
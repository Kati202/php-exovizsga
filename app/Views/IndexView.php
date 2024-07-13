<?php
namespace App\Views;
use App\Config;

class IndexView
{
    public static function Begin()
    {
        return self::loadView('template/begin');
    }
    
    public static function Home()
    {
        return self::loadView('template/home');
    }

    public static function End()
    {
        return self::loadView('template/end');
    }
    public static function Depos()
    {
        return self::loadView('template/depo');
    }

    public static function StartTitle($title) 
    {
        return '<h1>' . htmlspecialchars($title) . '</h1>';
    }
    public static function OpenSection($title)
	{
		return '<section ><h3 class=>'. $title .'</h3>';
	}
    public static function CloseSection()
	{
		return '</section>';
	}
    public static function renderDeleteConfirmationForm($actionUrl, $ids)
    {
        return '<form method="post" action="' . $actionUrl . '">
         <input type="hidden" name="deleteCarId" value="' . $ids . '">
         <label>Biztosan törlöd?</label>
         <button type="submit" name="confirmDelete">Igen, törlöm</button>
         </form>';
    }
    public static function CreateInput($text, $name)
    {
        return '<div>
                    <label for="'. $name .'">'. $text .'</label>
                    <input type="text" name="'. $name .'" id="'. $name .'">
                </div>';
    }
    public static function CreateHiddenInput($name,$value)
    {
        return'<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.$value.'">
        ';
    }
    public static function CreateInputValue($label, $name, $value = '')
    {
        return '<div>
                    <label for="' . $name . '">' . $label . '</label>
                    <input type="text" name="' . $name . '" id="' . $name . '" value="' . htmlspecialchars($value) . '">
                </div>';
    }
    public static function ShowDepoButton()
    {
     $html = '<form method="post" action="' . Config::HALAS_URL_DEPO  . '">';
     $html .= '<button type="submit" name="showDepo">Depó adatok megtekintése</button>';
     $html .= '</form>';
 
     return $html;
    } 
    

    private static function loadView($viewPath)
    {
        ob_start();
        include __DIR__ . '/' . $viewPath . '.php';
        return ob_get_clean();
    }
}
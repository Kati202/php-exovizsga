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
    public static function File()
    {
        return self::loadView('template/file');
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
    public static function CreateInput($text, $name, $placeholder = '')
   {
    if ($name === 'total_addresses' || $name === 'delivered_addresses' || $name === 'final_return' || $name === 'live_return') {
        return '<div>
                    <label for="' . $name . '">' . $text . '</label>
                    <input type="text" name="' . $name . '" id="' . $name . '" placeholder="' . htmlspecialchars($placeholder) . '">
                </div>';
    } else {
        return '<div>
                    <label for="' . $name . '">' . $text . '</label>
                    <input type="text" name="' . $name . '" id="' . $name . '" placeholder="' . htmlspecialchars($placeholder) . '">
                </div>';
    }
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
    public static function customRound($number)
    {
    $lastDigit = $number % 10;
    if ($lastDigit <= 2) {
        return floor($number / 10) * 10; 
    } elseif ($lastDigit <= 6) {
        return floor($number / 10) * 10 + 5; 
    } else {
        return ceil($number / 10) * 10; 
    }
    }
    
    public static function Login()
    {
        return self::loadView('template/login');
    }

    private static function loadView($viewPath)
    {
        ob_start();
        include __DIR__ . '/' . $viewPath . '.php';
        return ob_get_clean();
    }
}
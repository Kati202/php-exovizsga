<?php
namespace App\Views;

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

    public static function StartTitle($title) 
    {
        return '<h1>' . htmlspecialchars($title) . '</h1>';
    }
    public static function OpenSection($title)
	{
		echo '<section ><h3 class=>'. $title .'</h3>';
	}
    public static function CloseSection()
	{
		echo '</section>';
	}
    public static function renderDeleteConfirmationForm($actionUrl, $ids)
    {
        echo '<form method="post" action="' . $actionUrl . '">';
        echo '<input type="hidden" name="deleteCarId" value="' . $ids . '">';
        echo '<label>Biztosan törlöd?</label>';
        echo '<button type="submit" name="confirmDelete">Igen, törlöm</button>';
        echo '</form>';
    }

    private static function loadView($viewPath)
    {
        ob_start();
        include __DIR__ . '/' . $viewPath . '.php';
        return ob_get_clean();
    }
}
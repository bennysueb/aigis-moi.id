<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\MenuItem;

class MenuComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $publicMenuItems = MenuItem::whereNull('parent_id')
            ->with('children') // Eager load submenus
            ->orderBy('order')
            ->get();

        $view->with('publicMenuItems', $publicMenuItems);
    }
}

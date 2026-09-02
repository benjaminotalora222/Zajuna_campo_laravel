<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SuperadminLayout extends Component
{
    public function __construct(public string $title = 'Panel') {}

    public function render()
    {
        return view('layouts.superadmin');
    }
}

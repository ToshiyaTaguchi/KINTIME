<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AttendanceActionButton extends Component
{
    public string $type;
    public string $label;
    public string $class;

    public function __construct(string $type, string $label, string $class = '')
    {
        $this->type = $type;
        $this->label = $label;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.attendance-action-button');
    }
}

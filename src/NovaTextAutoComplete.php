<?php

namespace Gkermer\TextAutoComplete;

use Laravel\Nova\Fields\Text;

class TextAutoComplete extends Text
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'text-auto-complete';

    /**
     * Set the options for the select menu.
     *
     * @param  array  $options
     * @return $this
     */
    public function items($items)
    {
        return $this->withMeta([
            'items' => collect($items ?? [])->values()->all()
        ]);
    }
}

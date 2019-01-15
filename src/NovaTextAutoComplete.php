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
     * The items callback
     *
     * @var \Closure
     */
    public $itemsCallback;

    /**
     * Set the options for the select menu.
     *
     * @param  mixed $items
     * @return $this
     */
    public function items($items)
    {
        if ($items instanceof \Closure) {
            $this->itemsCallback = $items;
        } else {
            $this->withMeta([
                'items' => collect($items ?? [])->values()->all()
            ]);
        }

        return $this;
    }
}

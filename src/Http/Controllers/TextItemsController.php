<?php

namespace Gkermer\TextAutoComplete\Http\Controllers;

use Laravel\Nova\Http\Requests\NovaRequest;

class TextItemsController
{
    public function index(NovaRequest $request)
    {
        $field = $request->findResourceOrFail()
            ->availableFields($request)
            ->firstWhere('attribute', $request->field);

        return call_user_func($field->itemsCallback, $request->search);
    }
}

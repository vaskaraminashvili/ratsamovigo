<?php

namespace App\Support;

use TallStackUi\Facades\TallStackUi;

class TallStackUiTheme
{
    public static function apply(): void
    {
        config(['ts-ui.components.button.1.round' => true]);

        $field = [
            'input.wrapper' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-2xl ring-1 focus-within:ring-2',
            'input.base' => 'dark:placeholder-dark-400 w-full rounded-2xl border-0 bg-transparent px-4 py-3 text-base text-ink ring-0 placeholder:text-bought focus:outline-hidden focus:ring-transparent',
            'input.color.base' => 'dark:ring-dark-600/50 dark:text-dark-300 text-ink ring-transparent',
            'input.color.background' => 'dark:bg-dark-800 bg-phone',
            'input.color.disabled' => 'dark:bg-dark-900 bg-phone',
            'error' => 'text-wine! ring-wine/40 placeholder:text-wine focus-within:ring-wine focus-within:placeholder:text-wine focus:ring-wine focus-within:focus:ring-wine dark:ring-wine dark:focus-within:ring-wine',
        ];

        TallStackUi::customize()->form('input')->block($field);
        TallStackUi::customize()->form('textarea')->block($field);
        TallStackUi::customize()
            ->form('number')
            ->block($field)
            ->block('buttons.left.color', 'text-ink')
            ->block('buttons.right.color', 'text-ink');

        TallStackUi::customize()
            ->form('label')
            ->block('text', 'mb-1 block text-sm font-medium text-bought')
            ->block('error', 'text-wine');

        TallStackUi::customize()
            ->form('error')
            ->block('text', 'mt-1 block text-sm text-wine');

        TallStackUi::customize()
            ->form('pin')
            ->block('input.base', 'mr-2 block rounded-2xl text-center text-lg font-medium text-ink ring-1')
            ->block('input.color.base', 'dark:border-dark-600/50 dark:text-dark-300 focus:ring-primary-600 focus-within:focus:ring-primary-600 border-transparent text-ink ring-transparent focus-within:ring-2 focus:border-0 focus:ring-2')
            ->block('input.color.background', 'dark:bg-dark-800 bg-phone')
            ->block('input.color.error', 'border-0 border-wine text-wine ring-wine/40 placeholder:text-wine focus-within:ring-wine focus-within:placeholder:text-wine focus:ring-2 focus:ring-wine focus-within:focus:ring-wine dark:ring-wine dark:focus-within:ring-wine');

        TallStackUi::customize()
            ->form('upload')
            ->block('placeholder.wrapper', 'dark:border-dark-600 dark:bg-dark-800/50 relative flex h-24 w-full cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-primary-300 bg-phone transition')
            ->block('placeholder.title', 'text-sm font-medium text-ink')
            ->block('placeholder.tip', 'mx-4 mt-2 text-center text-sm text-bought');

        TallStackUi::customize()
            ->wrapper('input')
            ->block('wrapper', 'relative rounded-2xl');

        TallStackUi::customize()
            ->wrapper('radio')
            ->block('label.text', 'cursor-pointer items-center text-sm font-medium text-ink')
            ->block('label.error', 'text-wine');
    }
}

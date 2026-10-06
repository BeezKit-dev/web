<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

test('every English language line has a Malay translation', function (string $group) {
    $englishKeys = array_keys(Arr::dot(require lang_path("en/{$group}.php")));
    $malayKeys = array_keys(Arr::dot(require lang_path("ms/{$group}.php")));

    expect($malayKeys)->toEqualCanonicalizing($englishKeys);
})->with(['auth', 'pagination', 'passwords', 'validation']);

test('validation messages are shown in Malay when the locale is ms', function () {
    app()->setLocale('ms');

    $validator = Validator::make(['email' => ''], ['email' => 'required']);

    expect($validator->errors()->first('email'))->toBe('Medan email diperlukan.');
});

test('English remains the default locale', function () {
    expect(app()->getLocale())->toBe('en')
        ->and(__('auth.failed'))->toBe('These credentials do not match our records.');
});

<?php

test('the home page redirects guests to the login page', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

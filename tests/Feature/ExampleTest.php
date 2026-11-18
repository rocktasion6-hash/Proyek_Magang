<?php

test('the application redirects the root page to login', function () {
    $this->get('/')
        ->assertRedirect(route('login'));
});

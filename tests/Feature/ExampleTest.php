<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    $this->assertTrue(in_array($response->status(), [200, 302]));
});

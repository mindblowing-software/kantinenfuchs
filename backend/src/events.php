<?php

namespace kfx;

class events {
    public $hasura;

    public function __construct($hasura) {
        $this->hasura = $hasura;
    }

    public function get_hello() {
        return '** hello events **';
    }

}

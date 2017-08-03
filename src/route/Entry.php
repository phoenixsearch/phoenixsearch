<?php

namespace pheonixsearch\route;

use pheonixsearch\core\RequestHandler;

class Entry extends AbstractEntry
{
    private $requestMethod = '';
    private $requestHandler = null;

    public function __construct()
    {
        $this->requestHandler = new RequestHandler();
    }

    public function run()
    {
        call_user_func(
            [
                $this, $this->getIndexMethod($this->requestMethod),
            ], $this->requestHandler
        );
    }
}
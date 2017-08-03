<?php

namespace pheonixsearch\route;

use pheonixsearch\core\RequestHandler;

class Entry extends AbstractEntry
{
    private $requestHandler = null;

    public function __construct()
    {
        $this->requestHandler = new RequestHandler();
        parent::__construct($this->requestHandler);
    }

    public function run()
    {
        call_user_func(
            [
                $this, $this->getIndexMethod($this->requestHandler->getRequestMethod()),
            ]
        );
    }
}
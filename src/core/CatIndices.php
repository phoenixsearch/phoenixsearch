<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.08.17
 * Time: 9:01
 */

namespace pheonixsearch\core;


use pheonixsearch\helpers\Output;

class CatIndices extends Core
{
    private $jsonArray = null;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->jsonArray = $requestHandler->getRequestBodyArray();
        parent::__construct($requestHandler);
    }

    public function getCat()
    {
        $info = $this->getInfo();
        Output::jsonInfo($info, $this->getStdFields());
    }
}
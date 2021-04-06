<?php
class Niubiz
{
    private $trace_number;
    private $brand;
    private $status;
    private $resp;

    public function __construct($trace_number = "", $brand = "", $status = "", $resp = "")
    {
        $this->trace_number = $trace_number;
        $this->brand = $brand;
        $this->status = $status;
        $this->resp = $resp;
    }
}

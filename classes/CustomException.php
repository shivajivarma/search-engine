<?php

class CustomException extends Exception
{
    public function errorMessage()
    {
        return "CustomException -> " . $this->getMessage();
    }


    public function handle()
    {
        header('Content-type: text/json', true, $this->getCode());
        $exception = new stdClass();
        $exception->message = $this->getMessage();
        $exception->code = $this->getCode();
        echo json_encode($exception);
    }
}

?>
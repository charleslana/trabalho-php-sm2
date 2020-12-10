<?php
class HttpStatus {

    public function aplicarRequisicao($numero) {
        switch($numero) {
            case '200':
                return header('HTTP/1.1 200 OK');
                break;           
            case '400':
                return header('HTTP/1.1 400 Bad Request');
                break;
            case '403':
                return header('HTTP/1.1 403 Forbidden');
                break;
            case '500':
                return header('HTTP/1.1 500 Internal Server Error');
                break;
            case '503':
                return header('HTTP/1.1 503 Service Unavailable');
                break;
        }
    }
}
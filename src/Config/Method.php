<?php

namespace Onetoweb\TrustedshopsV2\Config;

enum Method: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}

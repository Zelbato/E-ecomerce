<?php


function routes(): array
{
    return require 'routes.php';
}

function exactMatchUriInArrayRoutes($uri, $routes)
{
    if (array_key_exists($uri, $routes)) {
        return [$uri => $routes[$uri]];
    }
    return [];
}

function regularExpressionMatchArrayRoutes($uri, $routes): array
{
    return array_filter(
        $routes,
        function ($value) use ($uri) {
            $regex = str_replace('/', '\/', ltrim($value, '/'));
            return preg_match("/^$regex/", ltrim($uri, '/'));
        },
        ARRAY_FILTER_USE_KEY
    );
}


function params($uri, $matchedUri)
{
    if (!empty($matchedUri)) {
        $matchToGetParams = array_keys($matchedUri)[0];
        return array_diff(
            explode('/', ltrim($uri, '/')),
            explode('/', ltrim($matchToGetParams, '/'))
        );
    }
    return [];
}

function formatParam($uri, $params)
{
    $uri = explode('/', ltrim($uri, '/'));
    $paramsData = [];
    foreach ($params as $index => $param) {
        $paramsData[$uri[$index - 1]] = $param;
    }

    return $paramsData;
}

function router()
{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    //echo $uri;

    $routes = routes();

    // $arry1 = [
    //     'user',
    //     '1',
    //     'name',
    //     'Alexandre'
    // ];

    // $arry2 = [
    //     'user',
    //     '[0-9]+',
    //     'name',
    //     '[a-z]+'
    // ];

    // var_dump(array_diff($arry1, $arry2));
    // die();

    $matchedUri = exactMatchUriInArrayRoutes($uri, $routes);
    if (empty($matchedUri)) {
        $matchedUri = regularExpressionMatchArrayRoutes($uri, $routes);
        $params = params($uri, $matchedUri);
        $params = formatParam($uri, $params);

        // var_dump($paramsData);
        die();
    }

    // var_dump($matchedUri);
    // die();
}

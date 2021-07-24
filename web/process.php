<?php

header('Content-type: application/json');

function path(string $name): string
{
    return __DIR__ . '/f/' . $name;
}

function url(string $name): string
{
    return ($_ENV['URL'] ?? '') . '/f/' . $name;
}

if (isset($_FILES['file'])) {
    try {
        $ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

        do {
            $name = str_replace(['0', 'o'], 'x', bin2hex(random_bytes(2)));
            $ext && $name .= '.' . $ext;
        } while (file_exists(path($name)));

        move_uploaded_file($_FILES["file"]["tmp_name"], path($name));

        http_response_code(201);
        exit(json_encode([
            'name' => $name,
            'url' => url($name),
        ]));
    } catch (Exception $e) {
        error_log(json_encode($e));
        http_response_code(500);
        exit(json_encode(['error' => 'Internal Error!']));
    }
} elseif (isset($_GET['name']) && empty($_GET['name']) == false) {
    $path = path(strtolower($_GET['name']));
    if (file_exists($path)) {
        http_response_code(200);
        exit(json_encode(['url' => url($_GET['name'])]));
    }

    http_response_code(404);
    exit(json_encode(['error' => 'Not found.']));
} else {
    http_response_code(400);
    exit(json_encode(['error' => 'Bad Request!']));
}

<?php

use ElementaryFramework\WaterPipe\WaterPipe;

use ElementaryFramework\WaterPipe\HTTP\Request\Request;

use ElementaryFramework\WaterPipe\HTTP\Response\Response;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseStatus;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseHeader;

include 'WaterPipe.php';
// Create the root pipe
$root = new WaterPipe;

// Add a new route to the pipe with HTTP GET method (the home page)
$root->get("/", function (Request $req, Response $res) {
    $res->sendHtml("<b>Welcome to my web app !</b> <a href=\"/login\">Click here to login</a>");
});

// Add a new route to the pipe with HTTP GET method (the login page)
$root->get("/login", function (Request $req, Response $res) {
    $res->sendFile("./pages/login.html", ResponseStatus::OkCode);
});

// Add a new route to the pipe with HTTP POST method (the login page form validation)
$root->post("/login", function (Request $req, Response $res) {
    // Get $_POST values
    $body = $req->getBody();
    $username = $body["username"];
    $password = $body["password"];

    if (validate_username($username) && validate_password($password)) {
        // Checks if the client access this route with an AJAX request
        if ($req->isAjax()) {
            $res->sendJson(array(
                "success" => true
            ));
        } else {
            // Redirect the user to the members page
            $res->redirect("/members/{$username}");
        }
    } else {
        // Checks if the client access this route with an AJAX request
        if ($req->isAjax()) {
            $res->sendJson(array(
                "success" => false
            ));
        } else {
            // Redirect the user to the members page
            $res->redirect("/login");
        }
    }
});

// Add a new route to the pipe with HTTP GET method (the member's dashboard page)
$root->get("/members/:username", function (Request $req, Response $res) {
    $res->sendHtml("Welcome to your dashboard <b>{$req->uri['username']}</b> !");
});

// Add a new HTTP error handler (the 404 Not Found Error)
$root->error(ResponseStatus::NotFoundCode, function (Request $req, Response $res) {
    $res->sendText("404 Error: Not Found.", ResponseStatus::NotFoundCode);
});

// Finally... Run the pipe
$root->run();

<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;


// --- NOT SECURED ROUTES ---

$app->get('/', function (Request $request, Response $response, array $args) {
    return $response->withJson(['message' => 'Hello World!'], 200);
});

$app->get('/test-db', function (Request $request, Response $response, array $args) {
    $stmt = $this->db->query('SELECT * FROM user');
    return $response->withJson($stmt->fetchAll());
});

$app->get('/api/pokus', function (Request $request, Response $response, array $args) {
    try {
        $rm = new Rooms($this->db);
        $rm->add('Pokus mistnost 1', 1);
        $rm->add('Pokus mistnost 2', 1);
        return $response->withJson($rm->all());
    } catch (Exception $ex) {
        exit($ex->getMessage());
    }
});

$app->post('/api/register',
    function (Request $request,
              Response $response,
              array $args) {
    $data = $request->getParsedBody();
    if(!empty($data['login']) &&
        !empty($data['password']) &&
       !empty($data['name']) &&
        !empty($data['surname']) &&
       !empty($data['gender']) &&
        !empty($data['email'])) {
        $model = new Users($this->db);
        try {
            $model->register($data);
            return $response->withStatus(201);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    } else {
        return $response->
        withStatus(400);
    }
});

$app->post('/api/login', function(Request $request,
                                  Response $response,
                                  array $args) {
    $data = $request->getParsedBody();
    if (!empty($data['login']) && !empty($data['password'])) {

        try {
            $model = new Users($this->db);
            $user = $model->verify($data['login'], $data['password']);
            if ($user) {
                $signer = new Sha256();

                $token = (new Builder())
                    ->setIssuer('https://akela.mendelu.cz') // Configures the issuer (iss claim)
                    ->setAudience('https://akela.mendelu.cz') // Configures the audience (aud claim)
                    ->setId('mojeSuperIdTetoAplikace', true) // Configures the id (jti claim), replicating as a header item
                    ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
                    ->setNotBefore(time()) // Configures the time that the token can be used (nbf claim)
                    ->setExpiration(time() + 3600) // Configures the expiration time of the token (exp claim)
                    ->set('id', $user['id_users']) // Configures a new claim, called "uid"
                    ->set('login', $user['login']) // Configures a new claim, called "uid"
                    ->sign($signer, getenv('TOKEN_KEY')) // creates a signature using our key from .env
                    ->getToken(); // Retrieves the generated token

                return $response->withJson([
                    'token' => (string) $token
                ], 201);
            } else {
                return $response->withStatus(404);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    } else {
        return $response->withStatus(400);
    }
});


// --- FOLLOWING ROUTES ARE SECURED - REQUIRE LOGIN ---

$app->group('/api/auth', function() use ($app) {

    $app->get('/rooms', function (Request $request, Response $response, array $args) {
        $roomsModel = new Rooms($this->db);
        try {
            $data = $roomsModel->all();
            return $response->withJson($data);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->get('/rooms/{id}', function (Request $request, Response $response, array $args) {
        if(!empty($args['id'])) {
            $rm = new Rooms($this->db);
            try {
                $info = $rm->find($args['id']);
                if($info) {
                    return $response->withJson($info);
                } else {
                    return $response->withStatus(404);
                }
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });

    // Delete room by room ID
    $app->post('/delRoom', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['id'])) {
                $rm->delete($data['id']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->post('/rooms', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['title'])) {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $rm->add($data['title'], $userId, $data['lock']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->get('/messages/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
            $rm = new Messages($this->db);
            try {
                $messages = $rm->getAllByRoomId($args['roomId']);
                return $response->withJson($messages);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });

    // Load users in room by room ID
    $app->get('/users/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
            $rm = new Users($this->db);
            try {
                $users = $rm->inRoom($args['roomId']);
                return $response->withJson($users);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });

    // Load room info by room ID
    $app->get('/room/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
            $rm = new Rooms($this->db);
            try {
                $room = $rm->find($args['roomId']);
                return $response->withJson($room);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });

    // Load user profile by ID
    $app->get('/user', function (Request $request, Response $response, array $args) {
            $rm = new Users($this->db);
            try {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $userData = $rm->getById($userId);
                return $response->withJson($userData);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }

    });

    // Load all users in db
    $app->get('/usersAll', function (Request $request, Response $response, array $args) {
        $usersModel = new Users($this->db);
        try {
            $data = $usersModel->all();
            return $response->withJson($data);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Send message to db
    $app->put('/sendMessage', function (Request $request, Response $response, array $args) {
        $rm = new Messages($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['message'])) {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $rm->send($data['message'], $data['roomId'], $userId);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Send whisper to db
    $app->put('/sendWhisper', function (Request $request, Response $response, array $args) {
        $rm = new Messages($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['message'])) {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $rm->whisper($data['message'], $data['roomId'], $userId, $data['userId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Kick user by user ID from room by room ID
    $app->post('/kickUser', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId']) && !empty($data['userId'])) {
                $rm->kickUser($data['roomId'],$data['userId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Delete user from kick by ID user from room by room ID
    $app->post('/deleteKick', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId']) && !empty($data['userId'])) {
                $rm->deleteKick($data['roomId'],$data['userId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Delete user from in_room by ID room and ID user
    $app->post('/deleteUser', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId']) && !empty($data['userId'])) {
                $rm->deleteUser($data['roomId'],$data['userId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Add user by user ID to room by room ID
    $app->post('/addUser', function (Request $request, Response $response, array $args) {
        $rm = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['roomId']) && !empty($data['userId'])) {
                $rm->addUser($data['roomId'],$data['userId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Update user by user ID
    $app->put('/user', function (Request $request, Response $response, array $args) {
        $us = new Users($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['login'])) {
                $token = $request->getAttribute('token');
                $userId = $token->getClaim('id');
                $us->updateUser($userId, $data);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    //check access to room
    $app->get('/access/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
            $rm = new Rooms($this->db);
            try {
                $inRoom = $rm->findInRoom($args['roomId']);
                return $response->withJson($inRoom);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });

    // Load users from room_kick by roomId
    $app->get('/fromKick/{roomId}', function (Request $request, Response $response, array $args) {
        if(!empty($args['roomId'])) {
            $k = new Rooms($this->db);
            try {
                $fromKick = $k->getFromKick($args['roomId']);
                return $response->withJson($fromKick);
            } catch (Exception $ex) {
                $this->logger->error($ex->getMessage());
                return $response->withStatus(500);
            }
        } else {
            return $response->withStatus(400);
        }
    });

    // Load all kicks
    $app->get('/allKicks', function (Request $request, Response $response, array $args) {
        $kicks = new Rooms($this->db);
        try {
            $data = $kicks->allKicks();
            return $response->withJson($data);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    $app->put('/updateRoom', function (Request $request, Response $response, array $args) {
        $us = new Rooms($this->db);
        try {
            $data = $request->getParsedBody();
            if(!empty($data['userId'])) {
                $us->updateRoom($data['roomId'], $data['userId']);
                return $response->withStatus(201);
            } else {
                return $response->withStatus(400);
            }
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
            return $response->withStatus(500);
        }
    });

    // Place for other secured routes like GET /messages.

})->add(function(Request $request, Response $response, $next) {
    $rawToken = $request->getHeaderLine('Authorization');
    if($rawToken) {
        $token = (new Parser())->parse((string) $rawToken);

        $data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
        $data->setIssuer('https://akela.mendelu.cz');
        $data->setAudience('https://akela.mendelu.cz');
        $data->setId('mojeSuperIdTetoAplikace');

        $signer = new Sha256();

        if($token->validate($data) &&
            $token->verify($signer, getenv('TOKEN_KEY'))) {
            $request = $request->withAttribute('token', $token);
            return $next($request, $response, $token);
        }
    }
    return $response->withStatus(401);  //unauthorized
});

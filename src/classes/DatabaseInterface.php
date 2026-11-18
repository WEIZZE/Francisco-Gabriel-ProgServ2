<?php

namespace FootX;

use PDO;

interface DatabaseInterface {
    public function getPdo(): PDO;
}

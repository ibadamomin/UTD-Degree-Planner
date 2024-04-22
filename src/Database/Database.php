<?php
declare(strict_types=1);

namespace DegreePlanner\Database;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

class Database
{
    private \mysqli $conn;

    public function __construct()
    {
        $servername = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $database = $_ENV['DB_DATABASE'];

        $this->conn = mysqli_init();
        $this->conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
        $this->conn->ssl_set(null, null, "../DigiCertGlobalRootCA.crt.pem", null, null);
        $this->conn->real_connect($servername, $username, $password, $database);
        if (mysqli_connect_errno()) {
            die('Connection failed: ' . mysqli_connect_error());
        }
    }

    public function db(): \mysqli
    {
        return $this->conn;
    }


}

<?php
if ($pedidoAjax) {
    require_once "../config/config.php";
} else {
    require_once "./config/config.php";
}

class DbModel
{
    public static $conn;

    protected function connection()
    {
        if (!isset(self::$conn)) {
            self::$conn = new PDO(SGDB1, USER1, PASS1, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }

    protected function insert($table, $data)
    {
        $pdo = self::connection();
        $fields = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($values)";
        $statement = $pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value, PDO::PARAM_STR);
        }
        $statement->execute();

        return $statement;
    }

    public function consultaSimples($consulta) {
        $pdo = self::connection();
        $statement = $pdo->prepare($consulta);
        $statement->execute();
        self::$conn = null;

        return $statement;
    }


}
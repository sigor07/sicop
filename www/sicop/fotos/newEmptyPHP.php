<?php

class UserView {

    public function displayStatus( $status ) {
        if ( $status === true ) {
            echo 'Autenticado com sucesso !';
        } else {
            echo 'Falha na autenticação, tente novamente.';
        }
    }

}

class UserModelaa {

    public function authenticate() {
        $login = 123; // $_POST [ 'login' ]
        $senha = 456; // $_POST [ 'senha' ]

        try {
            $stmt = $db->prepare( 'SELECT * FROM `users` WHERE `login` = ? AND `senha` = ?' );

            $stmt->bindValue( 1, $login, PDO::PARAM_STR );
            $stmt->bindValue( 2, $senha, PDO::PARAM_STR );

            $stmt->execute();

            return $stmt->rowCount() > 0 || false;
        } catch ( PDOException $e ) {
            // ...
        }
    }

}

class UserController {

    public function authenticate() {
        $model = new UserModel ( );
        $status = $model->authenticate();

        $view = new UserView ( );
        $view->displayStatus( $status );
    }

    public function handle() {
        if ( isset( $_GET ['action'] ) ) {
            switch ( trim( $_GET ['action'] ) ) {
                case 'login':
                case 'authenticate':
                    $this->authenticate();
                    break;
            }
        }
    }
}

$user = new UserController ( );
$user->handle();
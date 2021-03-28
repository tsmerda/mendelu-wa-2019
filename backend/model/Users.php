<?php

class Users extends Model {

    function register(array $data) {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, surname, login, email, password, gender, registered) VALUES (:n, :s, :l, :e, :p, :g, NOW())');
        $p = password_hash($data['password'],
            PASSWORD_DEFAULT);
        $stmt->bindValue(':n',
            $data['name']);
        $stmt->bindValue(':s',
            $data['surname']);
        $stmt->bindValue(':l',
            $data['login']);
        $stmt->bindValue(':e',
            $data['email']);
        $stmt->bindValue(':g',
            $data['gender']);
        $stmt->bindValue(':p', $p);
        return $stmt->execute();
    }

    function getByLogin($login) {
        $stmt = $this->db->prepare('select * 
                                              FROM users WHERE login = :l');
        $stmt->bindValue(':l', $login);
        $stmt->execute();
        return $stmt->fetch();
    }

    function verify($login, $password) {
        $user = $this->getByLogin($login);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return null;
    }

    function getById($userId) {
        $stmt = $this->db->prepare('select * FROM users where id_users = :id');
        $stmt->bindValue(':id', $userId);
        $stmt->execute();
        return $stmt->fetch();
    }
    function all() {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY id_users');
        return $stmt->fetchAll();
    }

    function inRoom($roomId) {
        $stmt = $this->db->prepare('SELECT * FROM users 
                                            JOIN in_room USING (id_users)
                                            WHERE id_rooms = :id ORDER BY id_users');
        $stmt->bindValue(':id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function updateUser($userId ,array $data) {
        $stmt = $this->db->prepare('UPDATE users
                                            SET login = :l, email = :e, password = :p, name = :n, surname = :s, gender = :g
                                            WHERE id_users = :id');
        $p = password_hash($data['password'],
            PASSWORD_DEFAULT);
        $stmt->bindValue(':id', $userId);
        $stmt->bindValue(':l', $data['login']);
        $stmt->bindValue(':p', $p);
        $stmt->bindValue(':e', $data['email']);
        $stmt->bindValue(':n', $data['name']);
        $stmt->bindValue(':s', $data['surname']);
        $stmt->bindValue(':g', $data['gender']);
        return $stmt->execute();
    }
}


<?php

class Rooms extends Model {

    function add($title, $idOwner, $lock) {
        $stmt = $this->db->prepare('INSERT INTO rooms '
                . '(created, title, id_users_owner, lock)'
                . ' VALUES '
                . '(NOW(), :t, :id, :l)');
        $stmt->bindValue(':t', $title);
        $stmt->bindValue(':id', $idOwner);
        $stmt->bindValue(':l', $lock);
        return $stmt->execute();
    }

    function delete($id) {
    $stmt = $this->db->prepare('DELETE FROM rooms WHERE id_rooms = :id');
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    }

    function all() {
        $stmt = $this->db->query('SELECT * FROM rooms ORDER BY created');
        return $stmt->fetchAll();
    }

    function find($id) {
        $stmt = $this->db->prepare('SELECT * FROM rooms
                                             JOIN users ON users.id_users = rooms.id_users_owner
                                             WHERE id_rooms = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    function kickUser($roomId, $userId) {
        $stmt = $this->db->prepare('DELETE FROM in_room WHERE id_rooms = :roomId AND id_users = :userId');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();

        $stmt = $this->db->prepare('INSERT INTO room_kick '
            . '(id_users, id_rooms, created)'
            . ' VALUES '
            . '(:userId, :roomId, NOW())');
        $stmt->bindValue(':userId', $userId);
        $stmt->bindValue(':roomId', $roomId);
        $stmt->execute();
    }

    function deleteKick($roomId, $userId) {
        $stmt = $this->db->prepare('DELETE FROM room_kick WHERE id_rooms = :roomId AND id_users = :userId');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
    }

    function deleteUser($roomId, $userId) {
        $stmt = $this->db->prepare('DELETE FROM in_room WHERE id_rooms = :roomId AND id_users = :userId');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
    }

    function addUser($roomId, $userId) {
        $stmt = $this->db->prepare('INSERT INTO in_room '
            . '(id_users, id_rooms, last_message, entered)'
            . ' VALUES '
            . '(:userId, :roomId, NOW(), NOW())');
        $stmt->bindValue(':userId', $userId);
        $stmt->bindValue(':roomId', $roomId);
        return $stmt->execute();
    }

    function updateRoom($roomId, $userId) {
        $stmt = $this->db->prepare('UPDATE rooms
                                            SET id_users_owner = :id
                                            WHERE id_rooms = :idr');
        $stmt->bindValue(':idr', $roomId);
        $stmt->bindValue(':id', $userId);
        return $stmt->execute();
    }

    function findInRoom($roomId) {
        $stmt = $this->db->prepare('SELECT * FROM in_room
                                             WHERE id_rooms = :id');
        $stmt->bindValue(':id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function getFromKick($roomId) {
        $stmt = $this->db->prepare('SELECT * FROM room_kick
                                             WHERE id_rooms = :idr');
        $stmt->bindValue(':idr', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function allKicks() {
        $stmt = $this->db->prepare('SELECT * FROM room_kick');
        $stmt->execute();
        return $stmt->fetchAll();
    }

}

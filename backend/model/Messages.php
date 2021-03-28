<?php

class Messages extends Model
{

    function getAllByRoomId($roomId)
    {
        $stmt = $this->db->prepare('SELECT * FROM messages 
              JOIN users ON users.id_users = messages.id_users_from
               WHERE id_rooms = :id ORDER BY created');
        $stmt->bindValue(':id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function send($message, $roomId, $idFrom)
    {
        $stmt = $this->db->prepare('INSERT INTO messages '
            . '(id_rooms, id_users_from, id_users_to, created, message)'
            . ' VALUES '
            . '(:idr, :idf, NULL, NOW(), :m)');
        $stmt->bindValue(':idr', $roomId);
        $stmt->bindValue(':idf', $idFrom);
        $stmt->bindValue(':m', $message);
        return $stmt->execute();
    }


    function whisper($message, $roomId, $idFrom, $idTo)
    {
        $stmt = $this->db->prepare('INSERT INTO messages '
            . '(id_rooms, id_users_from, id_users_to, created, message)'
            . ' VALUES '
            . '(:idr, :idf, :idt, NOW(), :m)');
        $stmt->bindValue(':idr', $roomId);
        $stmt->bindValue(':idf', $idFrom);
        $stmt->bindValue(':idt', $idTo);
        $stmt->bindValue(':m', $message);
        return $stmt->execute();
    }
}

<?php

namespace App\Models;

use DateTimeImmutable;
use DB;

class Room
{
    public function __construct(int $roomId, string $title, string $description, int $maxMembers, $dateDB, int $gameId, string $gameName, string $gameTag, int $gamemodeId, string $gamemode)
    {
        $this->roomId = $roomId;
        $this->title = $title;
        $this->description = $description;
        $this->maxMembers = $maxMembers;
        $this->dateOfCreation = new DateTimeImmutable($dateDB);
        $this->gameId = $gameId;
        $this->gameName = $gameName;
        $this->gameTag = $gameTag;
        
        $this->gamemodeId = $gamemodeId;
        $this->gamemode = $gamemode;

        $this->getRoomMembersConstruct();
    }

    public int $roomId;
    public string $title;
    public string $description;
    public int $maxMembers;
    public DateTimeImmutable $dateOfCreation;

    public int $gameId;
    public string $gameName;
    public string $gameTag;

    public int $gamemodeId;
    public string $gamemode;

    public array $members;
    public string $owner;
    public int $ownerId;

    public array $friendList = [];

    // Construct Get Room Members
    protected function getRoomMembersConstruct()
    {
        $this->members = DB::fetch("SELECT users.username, users.idUser, users.isRoomOwner
        FROM rooms
            INNER JOIN users
            ON rooms.idRoom = users.idRoom
        WHERE users.idRoom = :currentRoom
        ORDER BY users.isRoomOwner DESC", ["currentRoom" => $this->roomId]);

        foreach ($this->members as $member) {
            if ($member["isRoomOwner"] === 1) {
                $this->owner = $member["username"];
                $this->ownerId = $member["idUser"];
            }
        }
    }

    public function getNumberOfFriend(array $allRoomsFriendlist)
    {
        foreach ($allRoomsFriendlist as $friend) {
            if ($friend["idRoom"] === $this->roomId) {
                array_push($this->friendList, $friend);
            }
        }
    }
    
    // Display Room creation time
    public function CreatedSince() : string
    {
        $date_diff = date_diff($this->dateOfCreation, new DateTimeImmutable());
        $hours = intval($date_diff->format('%H'));
        if ($hours !== 0) {
            $minutes_since_creation = "Plus d'une heure";
        } else {
            $minutes = intval($date_diff->format('%i'));
            $minutes_since_creation = 60 - ($minutes);
            $minutes_since_creation = $minutes_since_creation . " min";
        }
        return $minutes_since_creation;
    }
    
    // Return Room Member's Id
    public static function getRoomMembersId(int $roomId) : array
    {
        $roomMembers = DB::fetch("SELECT users.idUser
        FROM users
        WHERE users.idRoom = :currentRoom", ["currentRoom" => $roomId]);
        return $roomMembers;
    }

    // Check provided user room's ownership
    public static function checkUserOwnership(int $userId, int $roomId) : bool
    {
        // Get session user info
        $result = \DB::fetch("SELECT users.isRoomOwner, users.idRoom
        FROM users
        WHERE users.idUser = :idUser",
        ["idUser" => $userId]);

        // Check if 
        if (!empty($result)) {
            if ($result[0]["isRoomOwner"] == 1 and $result[0]["idRoom"] == $roomId) {
                return true;
            }
        }

        return false;
    }

    public static function createNewRoom(int $idUser , string $title , string $description, int $maxMembers, int $idGamemode)
    {
        DB::statement("INSERT INTO rooms (title, description, maxMembers, idGamemode)
        VALUES
            (:title, :description, :maxMembers, :idGamemode);", ["title" => $title, "description" => $description, "maxMembers" => $maxMembers, "idGamemode" => $idGamemode]);

        $connection = DB::getDB();

        DB::statement("UPDATE Users
        SET idRoom = :idRoom, isRoomOwner = 1
        WHERE idUser = :idUser;", ["idRoom" => $connection->lastInsertId(), "idUser" => $idUser]);
    }

    public static function modifyRoom(int $idRoom , string $title , string $description, int $maxMembers, int $idGamemode)
    {
        DB::statement("UPDATE rooms
        SET title = :title, description = :description, maxMembers = :maxMembers, idGamemode = :idGamemode
        WHERE rooms.idRoom = :idRoom",
        ["idRoom" => $idRoom, "title" => $title, "description" => $description, "maxMembers" => $maxMembers, "idGamemode" => $idGamemode]);
    }

    public static function deleteRoom(int $idRoom)
    {
        $roomMembers = self::getRoomMembersId($idRoom);

        // Remove each user's room and ownership
        foreach ($roomMembers as $member) {
            DB::statement("UPDATE users
            SET users.isRoomOwner = 0, users.idRoom = NULL
            WHERE users.idUser = :idUser",
            ["idUser" => $member["idUser"]]);
        }

        // Delete requestToJoin the room from db
        DB::statement("DELETE FROM requesttojoin
        WHERE requesttojoin.idRoom = :idRoom",
        ["idRoom" => $idRoom]);

        // Delete the room from db
        DB::statement("UPDATE rooms
        SET rooms.isEnabled = 0
        WHERE rooms.idRoom = :idRoom",
        ["idRoom" => $idRoom]);
    }

    public static function leaveRoom(int $idUser, int $idRoom)
    {
        $isOwner = self::checkUserOwnership($idUser, $idRoom);
        if ($isOwner) {
            // Retrieve all room members
            $allMembers = self::getRoomMembersId($idRoom);

            // Delete room if user is last room member
            if (count($allMembers) === 1) {
                self::deleteRoom($idRoom);
                return true;
            }

            // Determine the new room owner (get his id)
            $newOwnerId = $idUser;
            foreach ($allMembers as $member) {
                if ($member["idUser"] != $idUser) {
                    $newOwnerId = $member["idUser"];
                    break;
                }
            }

            // Appoint the new room owner
            DB::statement("UPDATE users
            SET users.isRoomOwner = 1
            WHERE users.idUser = :idUser",
            ["idUser" => $newOwnerId]);
        }
        
        // Remove initial user from his room
        DB::statement("UPDATE Users
        SET users.isRoomOwner = 0, users.idRoom = NULL
        WHERE idUser = :idUser", ["idUser" => $idUser]);
    }
    
    public static function promoteToOwner(int $idUser, int $idTarget)
    {
        // Retrieve from DB room owner and soon-to-be room owner
        $result = DB::fetch('SELECT users.idUser, users.isRoomOwner, users.idRoom
        FROM users
        WHERE idUser = :idUser1 OR idUser = :idUser2',
        ["idUser1" => $idUser, "idUser2" => $idTarget]);

        if (count($result) === 2) {
            // Checks if user is the room owner
            if (($result[0]["isRoomOwner"] and $result[0]["idUser"] == $idUser) or ($result[1]["isRoomOwner"] and $result[1]["idUser"] == $idUser)) {
                // Checks that both users are in the same room
                if ($result[0]["idRoom"] === $result[1]["idRoom"]) {
                    DB::statement("UPDATE users
                    SET users.isRoomOwner = 0
                    WHERE users.idUser = :idUser",
                    ["idUser" => $idUser]);

                    DB::statement("UPDATE users
                    SET users.isRoomOwner = 1
                    WHERE users.idUser = :idTarget",
                    ["idTarget" => $idTarget]);
                }
            }
        }
    }
    
    public static function kickFromRoom(int $idUser, int $idTarget)
    {
        // Retrieve from DB room owner and soon-to-be room owner
        $result = DB::fetch('SELECT users.idUser, users.isRoomOwner, users.idRoom
        FROM users
        WHERE idUser = :idUser1 OR idUser = :idUser2',
        ["idUser1" => $idUser, "idUser2" => $idTarget]);

        if (($result[0]["isRoomOwner"] and $result[0]["idUser"] == $idUser) or ($result[1]["isRoomOwner"] and $result[1]["idUser"] == $idUser))

        if (count($result) === 2) {
            // Checks if user is the room owner
            if (($result[0]["isRoomOwner"] and $result[0]["idUser"] == $idUser) or ($result[1]["isRoomOwner"] and $result[1]["idUser"] == $idUser)) {
                // Checks that both users are in the same room
                if ($result[0]["idRoom"] === $result[1]["idRoom"]) {
                    DB::statement("UPDATE users
                    SET users.idRoom = NULL
                    WHERE users.idUser = :idTarget",
                    ["idTarget" => $idTarget]);
                }
            }
        }
    }

    public static function RequestToJoinRoom($idUser, $idRoom)
    {
        $result = DB::fetch("SELECT rooms.idRoom
        FROM rooms
        WHERE idRoom = :idRoom",
        ["idRoom" => $idRoom]);

        if (count($result) === 1) {
            DB::statement("INSERT INTO requesttojoin 
            (idUser, idRoom)
            VALUES (:idUser, :idRoom)",
            ["idUser" => $idUser, "idRoom" => $idRoom]);
        }
    }

    public static function cancelRequestToJoinRoom($idUser, $idRoom)
    {
        $result = DB::fetch("SELECT requesttojoin.idUser
        FROM requesttojoin
        WHERE requesttojoin.idUser = :idUser AND requesttojoin.idRoom = :idRoom",
        ["idUser" => $idUser, "idRoom" => $idRoom]);

        if (count($result) === 1) {
            DB::statement("DELETE FROM requesttojoin
            WHERE requesttojoin.idUser = :idUser AND requesttojoin.idRoom = :idRoom",
            ["idUser" => $idUser, "idRoom" => $idRoom]);
        }
    }
}


?>
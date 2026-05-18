<?php
class DatabaseConnection{

    function openConnection(){
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "project08_taskhub";
        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
        if($connection->connect_error){
            die("Could not connect to the database- " . $connection->connect_error);
        }
        return $connection;
    }

    function signUp($connection, $tableName, $name, $email, $password_hash){
        $sql = "INSERT INTO $tableName (name, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password_hash);
        $result = $stmt->execute();
        return $result;
    }

    function checkEmail($connection, $tableName, $email){
        $sql = "SELECT id FROM $tableName WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function signIn($connection, $tableName, $email){
        $sql = "SELECT * FROM $tableName WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getFirstWorkspaceId($connection, $user_id){
        $sql = "SELECT workspace_id FROM workspace_members WHERE user_id = ? ORDER BY id ASC LIMIT 1";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function inviteCodeExists($connection, $tableName, $code){
        $sql = "SELECT id FROM $tableName WHERE invite_code = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function createWorkspace($connection, $tableName, $name, $description, $owner_id, $invite_code){
        $sql = "INSERT INTO $tableName (name, description, owner_id, invite_code) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssis", $name, $description, $owner_id, $invite_code);
        $result = $stmt->execute();
        if($result){
            return $connection->insert_id;
        }
        return 0;
    }

    function findWorkspaceByCode($connection, $tableName, $code){
        $sql = "SELECT * FROM $tableName WHERE invite_code = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function addWorkspaceMember($connection, $tableName, $workspace_id, $user_id){
        $sql = "INSERT IGNORE INTO $tableName (workspace_id, user_id) VALUES (?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $workspace_id, $user_id);
        $result = $stmt->execute();
        return $result;
    }

    function isWorkspaceMember($connection, $workspace_id, $user_id){
        $sql = "SELECT id FROM workspace_members WHERE workspace_id = ? AND user_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $workspace_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getWorkspacesForUser($connection, $user_id){
        $sql = "SELECT w.id, w.name FROM workspaces w INNER JOIN workspace_members wm ON wm.workspace_id = w.id WHERE wm.user_id = ? ORDER BY w.name ASC";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getWorkspaceById($connection, $tableName, $id){
        $sql = "SELECT * FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getWorkspaceMembers($connection, $workspace_id){
        $sql = "SELECT wm.id AS member_row_id, u.id AS user_id, u.name, u.email, wm.joined_at FROM workspace_members wm INNER JOIN users u ON u.id = wm.user_id WHERE wm.workspace_id = ? ORDER BY wm.joined_at ASC";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $workspace_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getWorkspaceMemberRow($connection, $member_row_id){
        $sql = "SELECT * FROM workspace_members WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $member_row_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function removeWorkspaceMember($connection, $tableName, $member_row_id){
        $sql = "DELETE FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $member_row_id);
        $result = $stmt->execute();
        return $result;
    }
}
?>

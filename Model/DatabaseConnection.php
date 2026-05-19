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

    // ===== Member 1: users + workspaces =====

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

    // ===== Member 2: projects + project_members =====

    function getProjectsForWorkspace($connection, $workspace_id, $includeArchived){
        if($includeArchived){
            $sql = "SELECT * FROM projects WHERE workspace_id = ? ORDER BY created_at DESC";
        }else{
            $sql = "SELECT * FROM projects WHERE workspace_id = ? AND is_archived = 0 ORDER BY created_at DESC";
        }
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $workspace_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getProjectById($connection, $tableName, $id){
        $sql = "SELECT * FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function createProject($connection, $tableName, $workspace_id, $name, $description, $deadline, $color_label){
        $sql = "INSERT INTO $tableName (workspace_id, name, description, deadline, color_label) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("issss", $workspace_id, $name, $description, $deadline, $color_label);
        $result = $stmt->execute();
        if($result){
            return $connection->insert_id;
        }
        return 0;
    }

    function updateProject($connection, $tableName, $id, $name, $description, $deadline, $color_label){
        $sql = "UPDATE $tableName SET name = ?, description = ?, deadline = ?, color_label = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssi", $name, $description, $deadline, $color_label, $id);
        $result = $stmt->execute();
        return $result;
    }

    function setProjectArchived($connection, $tableName, $id, $is_archived){
        $sql = "UPDATE $tableName SET is_archived = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $is_archived, $id);
        $result = $stmt->execute();
        return $result;
    }

    function deleteProject($connection, $tableName, $id){
        $sql = "DELETE FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        return $result;
    }

    function checkProjectName($connection, $workspace_id, $name){
        $sql = "SELECT id FROM projects WHERE workspace_id = ? AND name = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("is", $workspace_id, $name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getProjectMembers($connection, $project_id){
        $sql = "SELECT pm.id AS project_member_row_id, u.id AS user_id, u.name, u.email FROM project_members pm INNER JOIN users u ON u.id = pm.user_id WHERE pm.project_id = ? ORDER BY u.name ASC";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function isProjectMember($connection, $project_id, $user_id){
        $sql = "SELECT id FROM project_members WHERE project_id = ? AND user_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $project_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function addProjectMember($connection, $tableName, $project_id, $user_id){
        $sql = "INSERT IGNORE INTO $tableName (project_id, user_id) VALUES (?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $project_id, $user_id);
        $result = $stmt->execute();
        return $result;
    }

    function removeProjectMember($connection, $tableName, $project_id, $user_id){
        $sql = "DELETE FROM $tableName WHERE project_id = ? AND user_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $project_id, $user_id);
        $result = $stmt->execute();
        return $result;
    }

    function getWorkspaceMembersNotInProject($connection, $workspace_id, $project_id){
        $sql = "SELECT u.id, u.name, u.email FROM workspace_members wm INNER JOIN users u ON u.id = wm.user_id WHERE wm.workspace_id = ? AND u.id NOT IN (SELECT user_id FROM project_members WHERE project_id = ?) ORDER BY u.name ASC";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ii", $workspace_id, $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // ===== Member 3: tasks =====

    function getTasksForProject($connection, $project_id){
        $sql = "SELECT t.*, u.name AS assignee_name FROM tasks t LEFT JOIN users u ON u.id = t.assigned_to WHERE t.project_id = ? ORDER BY t.created_at DESC";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getTaskById($connection, $tableName, $id){
        $sql = "SELECT * FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getUserById($connection, $tableName, $id){
        $sql = "SELECT * FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function createTask($connection, $tableName, $project_id, $title, $description, $assigned_to, $priority, $due_date){
        $sql = "INSERT INTO $tableName (project_id, title, description, assigned_to, priority, due_date, status) VALUES (?, ?, ?, ?, ?, ?, 'todo')";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ississ", $project_id, $title, $description, $assigned_to, $priority, $due_date);
        $result = $stmt->execute();
        if($result){
            return $connection->insert_id;
        }
        return 0;
    }

    function updateTask($connection, $tableName, $id, $title, $description, $assigned_to, $priority, $due_date){
        $sql = "UPDATE $tableName SET title = ?, description = ?, assigned_to = ?, priority = ?, due_date = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssissi", $title, $description, $assigned_to, $priority, $due_date, $id);
        $result = $stmt->execute();
        return $result;
    }

    function updateTaskStatus($connection, $tableName, $id, $status){
        $sql = "UPDATE $tableName SET status = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        $result = $stmt->execute();
        return $result;
    }

    function deleteTask($connection, $tableName, $id){
        $sql = "DELETE FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        return $result;
    }

    // ===== Member 4: comments + activity_logs =====

    function getCommentsForTask($connection, $task_id){
        $sql = "SELECT c.*, u.name AS author_name FROM comments c INNER JOIN users u ON u.id = c.user_id WHERE c.task_id = ? ORDER BY c.created_at ASC";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getCommentById($connection, $tableName, $id){
        $sql = "SELECT * FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function createComment($connection, $tableName, $task_id, $user_id, $body){
        $sql = "INSERT INTO $tableName (task_id, user_id, body) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iis", $task_id, $user_id, $body);
        $result = $stmt->execute();
        if($result){
            return $connection->insert_id;
        }
        return 0;
    }

    function deleteComment($connection, $tableName, $id){
        $sql = "DELETE FROM $tableName WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        return $result;
    }

    function logActivity($connection, $tableName, $project_id, $user_id, $action_text){
        $sql = "INSERT INTO $tableName (project_id, user_id, action_text) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("iis", $project_id, $user_id, $action_text);
        $result = $stmt->execute();
        return $result;
    }

    function getActivityLogsForProject($connection, $project_id){
        $sql = "SELECT a.*, u.name AS user_name FROM activity_logs a INNER JOIN users u ON u.id = a.user_id WHERE a.project_id = ? ORDER BY a.created_at DESC LIMIT 50";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $project_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function getActivityLogsForWorkspace($connection, $workspace_id){
        $sql = "SELECT a.*, u.name AS user_name, p.name AS project_name FROM activity_logs a INNER JOIN users u ON u.id = a.user_id INNER JOIN projects p ON p.id = a.project_id WHERE p.workspace_id = ? ORDER BY a.created_at DESC LIMIT 100";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $workspace_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
}
?>

\function getProjectsForWorkspace($connection, $workspace_id, $includeArchived){
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
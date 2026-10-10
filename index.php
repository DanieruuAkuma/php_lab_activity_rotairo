<?php
include 'dbcon.php';

$message = "";
$messageType = "success";

// add user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($name !== "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare(
            "INSERT INTO users (name, email) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $name, $email);

        if ($stmt->execute()) {
            header("Location: index.php?msg=added");
            exit;
        } else {
            $message = "Error adding user.";
            $messageType = "danger";
        }
        $stmt->close();
    } else {
        $message = "Please enter a valid name and email.";
        $messageType = "danger";
    }
}

// STATUS MESSAGES
if (isset($_GET["msg"])) {
    if ($_GET["msg"] == "added") {
        $message = "User added successfully!";
    } elseif ($_GET["msg"] == "updated") {
        $message = "User updated successfully!";
    } elseif ($_GET["msg"] == "deleted") {
        $message = "User deleted successfully!";
    }
}

// DISPLAY USERS
$result = $conn->query(
    "SELECT * FROM users ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>User Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <h2 class="text-center mb-5">
        User Management System
    </h2>

    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- ADD NEW USER -->
        <div class="col-md-4">
            <h5 class="mb-4">Add New User</h5>

            <form method="POST" action="index.php">

                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           placeholder="Enter Full Name"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           placeholder="Enter Email Address"
                           required>
                </div>

                <button type="submit"
                        class="btn btn-primary w-100">
                    Submit
                </button>

            </form>
        </div>

        <!-- REGISTERED USERS -->
        <div class="col-md-8">
            <h5 class="mb-4">Registered Users</h5>

            <div class="table-responsive">
                <table class="table table-striped table-hover bg-white">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>

                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php echo (int)$row["id"]; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["name"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["email"]); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row["created_at"]); ?>
                                </td>

                                <td>
                                    <a href="edit.php?id=<?php echo (int)$row["id"]; ?>"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <a href="delete.php?id=<?php echo (int)$row["id"]; ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                No data found
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>
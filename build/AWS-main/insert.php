<?php
// Database setup
$host = "proj-db.ccr6d1e6bnfy.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "coolstrongpassword";
$db = "inventorydb";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($host, $user, $pass, $db);

// Delete action
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: inventory.html");
    exit;
}

// Insert/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $id = $_POST['id'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO items (item_name, item_quantity) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $qty);
    } else {
        $stmt = $conn->prepare("UPDATE items SET item_name = ?, item_quantity = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $qty, $id);
    }
    $stmt->execute();
    header("Location: inventory.html");
    exit;
}

// List items
if (isset($_GET['action']) && $_GET['action'] == 'view') {
    $result = $conn->query("SELECT * FROM items");
    if ($result->num_rows > 0) {
        echo "<table><tr><th>Name</th><th>Quantity</th><th>Actions</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $name = htmlspecialchars($row['item_name']);
            $qty = $row['item_quantity'];
            echo "<tr>
                    <td>$name</td>
                    <td>$qty</td>
                    <td>
                        <button class='edit-btn' onclick='setEdit($id, \"$name\", $qty)'>Edit</button>
                        <button class='delete-btn' onclick='deleteItem($id)'>Delete</button>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No items found.</p>";
    }
    exit;
}

$conn->close();
?>

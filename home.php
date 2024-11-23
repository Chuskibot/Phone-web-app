<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle adding new contact
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_contact'])) {
    // Sanitize user inputs to prevent SQL injection
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';

    // Insert new contact into the database
    $sql = "INSERT INTO contacts (user_id, name, phone, email) VALUES ('$user_id', '$name', '$phone', '$email')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Contact added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
}

// Handle deleting a contact
if (isset($_GET['delete'])) {
    $contact_id = $_GET['delete'];
    $sql = "DELETE FROM contacts WHERE id = '$contact_id' AND user_id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Contact deleted successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
}

// Fetch the user's contacts from the database
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

$sql = "SELECT * FROM contacts WHERE user_id = '$user_id' AND (name LIKE '%$search_query%' OR phone LIKE '%$search_query%') ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Google Fonts for a modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        .home-container {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            max-width: 900px;
            margin: auto;
            text-align: center;
            margin-top: 7vh;
            animation: fadeIn 1s forwards;
        }

        h1 {
            font-size: 2.8rem;
            color: #0077b6;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        h4 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .logout-btn {
            background-color: #0077b6;
            color: white;
            border: none;
            padding: 0.7rem 2rem;
            font-size: 1.1rem;
            border-radius: 25px;
            transition: background-color 0.3s ease;
            margin-top: 2rem;
        }

        .logout-btn:hover {
            background-color: #005f91;
            cursor: pointer;
        }

        .contact-form {
            margin-top: 3rem;
            margin-bottom: 3rem;
            background-color: #f1f3f5;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .contact-form input {
            border-radius: 25px;
            box-shadow: none;
            transition: all 0.3s ease;
        }

        .contact-form input:focus {
            border-color: #0077b6;
            box-shadow: 0 0 5px rgba(0, 119, 182, 0.3);
        }

        .contact-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 1rem 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .contact-info {
            flex: 1;
            margin-right: 1.5rem;
        }

        .contact-info h5 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .contact-info p {
            font-size: 1rem;
            color: #666;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .alert {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .contact-cards {
            max-height: 400px;
            overflow-y: auto;
        }

        .search-bar {
            margin-bottom: 1.5rem;
        }

        .contact-card.hidden {
            display: none;
        }

        .show-all-btn {
            margin-top: 1.5rem;
            background-color: #00b4d8;
            color: white;
            border: none;
            padding: 0.7rem 2rem;
            font-size: 1.1rem;
            border-radius: 25px;
            transition: background-color 0.3s ease;
        }

        .show-all-btn:hover {
            background-color: #008c9e;
            cursor: pointer;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="home-container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Your personalized homepage is ready.</p>

        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Add Contact Form -->
        <div class="contact-form">
            <h4>Add New Contact</h4>
            <form action="home.php" method="POST">
                <div class="mb-3">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Contact Name" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone Number" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email (Optional)">
                </div>
                <button type="submit" class="btn btn-primary w-100" name="add_contact">Add Contact</button>
            </form>
        </div>

        <!-- Search Contacts Form -->
        <form action="home.php" method="POST" class="search-bar">
            <input type="text" name="search" class="form-control" placeholder="Search contacts..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-outline-primary mt-2 w-100">Search</button>
        </form>

        <!-- Show All Contacts Button -->
        <button class="show-all-btn" onclick="toggleAllContacts()">Show All Contacts</button>

        <!-- Contact Cards -->
        <div class="contact-cards" id="contact-cards">
            <?php 
            // Display all contacts with limit of 2 initially
            $count = 0;
            while ($contact = $result->fetch_assoc()): 
                $count++;
                $hiddenClass = ($count > 2) ? 'hidden' : '';
            ?>
                <div class="contact-card <?php echo $hiddenClass; ?>">
                    <div class="contact-info">
                        <h5><?php echo $contact['name']; ?></h5>
                        <p><?php echo $contact['phone']; ?></p>
                        <?php if (!empty($contact['email'])): ?>
                            <p><?php echo $contact['email']; ?></p>
                        <?php endif; ?>
                    </div>
                    <a href="home.php?delete=<?php echo $contact['id']; ?>">
                        <button class="delete-btn">Delete</button>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Log Out Button -->
        <a href="logout.php">
            <button class="logout-btn">Log Out</button>
        </a>
    </div>

    <!-- JavaScript to toggle contact visibility -->
    <script>
        function toggleAllContacts() {
            var cards = document.querySelectorAll('.contact-card');
            cards.forEach(card => card.classList.toggle('hidden'));
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$host = 'localhost';
$db   = 'u593112326_rosca';
$user = 'root';  // Your database username
$pass = 'root';      // Your database password
$charset = 'utf8mb4';

$pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=$charset", $user, $pass);

$friends = [];
$friendRequests = [];

$stmt = $pdo->prepare("SELECT * FROM friend_requests WHERE receiver_id = ? AND status = 'PENDING'");
$stmt->execute([$_SESSION['user_id']]);
while ($row = $stmt->fetch()) {
    $friendRequests[] = ['id' => $row['id'], 'sender_id' => $row['sender_id']];
}


$stmt = $pdo->prepare("SELECT * FROM friends WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
while ($row = $stmt->fetch()) {
    $friends[] = $row['friend_id'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['acceptRequest'])) {
        $stmt = $pdo->prepare("UPDATE friend_requests SET status='ACCEPTED' WHERE id = ?");
        $stmt->execute([$_POST['request_id']]);
        
        // Add to friends table
        $stmt = $pdo->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?), (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $_POST['sender_id'], $_POST['sender_id'], $_SESSION['user_id']]);
    }

    if (isset($_POST['rejectRequest'])) {
        $stmt = $pdo->prepare("UPDATE friend_requests SET status='REJECTED' WHERE id = ?");
        $stmt->execute([$_POST['request_id']]);
    }
}
?>

<h1>Welcome to your profile</h1>

<h2>Friend Requests</h2>
<ul id="friendRequestsList">
<?php foreach ($friendRequests as $request) {
    echo "<li data-id='{$request['id']}'>User: {$request['sender_id']} 
             <button onclick='acceptRequest({$request['id']}, {$request['sender_id']})'>Accept</button>
             <button onclick='rejectRequest({$request['id']})'>Reject</button>
          </li>";
}

?>
</ul>

<h2>Friends</h2>
<ul>
<?php foreach ($friends as $friend) {
    echo "<li>User: $friend</li>";
}
?>
</ul>

<h2>Search Users</h2>
<input type="text" id="searchInput" placeholder="Search by username...">
<button onclick="searchUsers()">Search</button>

<ul id="searchResults">
</ul>

<script>
function searchUsers() {
    const searchValue = document.getElementById('searchInput').value;

    fetch('search.php?username=' + searchValue)
    .then(response => response.json())
    .then(data => {
        const resultsList = document.getElementById('searchResults');
        resultsList.innerHTML = ''; // Clear previous results
        
        data.forEach(user => {
            resultsList.innerHTML += `
                <li>
                    ${user.username} 
                    <button onclick="sendFriendRequest(${user.id})">Send Friend Request</button>
                </li>
            `;
        });
    });
}

function sendFriendRequest(userId) {
    fetch('sendRequest.php?receiver_id=' + userId)
    .then(response => {
        if (response.ok) {
            alert('Friend request sent!');
        } else {
            alert('Failed to send friend request.');
        }
    });
}

function acceptRequest(requestId, senderId) {
    fetch('profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `acceptRequest=true&request_id=${requestId}&sender_id=${senderId}`
    })
    .then(response => {
        if (response.ok) {
            alert('Friend request accepted!');
        } else {
            alert('Failed to accept friend request.');
        }
    });
}

function rejectRequest(requestId) {
    fetch('profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `rejectRequest=true&request_id=${requestId}`
    })
    .then(response => {
        if (response.ok) {
            alert('Friend request rejected!');
        } else {
            alert('Failed to reject friend request.');
        }
    });
}
</script>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include('connect.php');


$friends = [];
$friendRequests = [];

// Fetch friend requests and mutual friends
$stmt = $bdd->prepare("
SELECT fr.*, u.username,
(SELECT GROUP_CONCAT(u2.username) 
 FROM friends AS f1
 JOIN user AS u2 ON f1.friend_id = u2.id  -- Ajout de la jointure ici
 WHERE f1.user_id = fr.sender_id
 AND f1.friend_id IN (
    SELECT f2.friend_id
    FROM friends AS f2
    WHERE f2.user_id = ?)
) AS mutual_friends
FROM friend_requests AS fr 
JOIN user AS u ON fr.sender_id = u.id
WHERE fr.receiver_id = ? AND fr.status = 'PENDING'
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
while ($row = $stmt->fetch()) {
    $friendRequests[] = ['id' => $row['id'], 'sender_id' => $row['sender_id'], 'username' => $row['username'], 'mutual_friends' => $row['mutual_friends']];
}

// Fetch friends
$stmt = $bdd->prepare("
    SELECT f.*, u.username 
    FROM friends AS f 
    JOIN user AS u ON f.friend_id = u.id
    WHERE f.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
while ($row = $stmt->fetch()) {
    $friends[] = ['friend_id' => $row['friend_id'], 'username' => $row['username']];
}

// Handle post requests for accepting or rejecting friend requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['acceptRequest'])) {
        $stmt = $bdd->prepare("UPDATE friend_requests SET status='ACCEPTED' WHERE id = ?");
        $stmt->execute([$_POST['request_id']]);
        
        // Add to friends table
        $stmt = $bdd->prepare("INSERT INTO friends (user_id, friend_id) VALUES (?, ?), (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $_POST['sender_id'], $_POST['sender_id'], $_SESSION['user_id']]);
    }

    if (isset($_POST['rejectRequest'])) {
        $stmt = $bdd->prepare("UPDATE friend_requests SET status='REJECTED' WHERE id = ?");
        $stmt->execute([$_POST['request_id']]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... [Meta, Title, and other tags] ... -->
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    .text-blue {
        color: blue;
    }
</style>

</head>
<body>
    <div class="container">
        <!-- Profile -->
        <h1 class="text-center mb-4">Welcome to your profile</h1>

        <!-- Friend Requests -->
        <div class="card mb-4">
    <div class="card-header">Friend Requests</div>
    <ul class="list-group list-group-flush" id="friendRequestsList">
    <?php foreach ($friendRequests as $request) {
    $mutualFriends = explode(",", $request['mutual_friends']);
    $displayMutualFriends = count($mutualFriends) > 1 
        ? htmlspecialchars($mutualFriends[0], ENT_QUOTES, 'UTF-8') . " et <span class='text-blue'>d'autres personnes en commun</span>" 
        : htmlspecialchars($request['mutual_friends'], ENT_QUOTES, 'UTF-8');
    ?>
    <li class="list-group-item" data-id="<?php echo $request['id']; ?>">
        User: <?php echo htmlspecialchars($request['username'], ENT_QUOTES, 'UTF-8'); ?>
        <span class="mutualFriendsList"></span><span onclick="showMutualFriends(this, '<?php echo implode(", ", $mutualFriends); ?>')">Amis en commun: <?php echo $displayMutualFriends; ?></span>
        <button class="btn btn-primary btn-sm ml-2" onclick="acceptRequest(<?php echo $request['id']; ?>, <?php echo $request['sender_id']; ?>)">Accept</button>
        <button class="btn btn-danger btn-sm ml-2" onclick="rejectRequest(<?php echo $request['id']; ?>)">Reject</button>
    </li>
<?php } ?>


    </ul>
</div>

<div class="card mb-4">
    <div class="card-header">Friends</div>
    <ul class="list-group list-group-flush">
        <?php foreach ($friends as $friend) { ?>
            <li class="list-group-item">User: <?php echo htmlspecialchars($friend['username'], ENT_QUOTES, 'UTF-8'); ?></li>
        <?php } ?>
    </ul>
</div>


        <!-- Search Users -->
        <div class="card mb-4">
            <div class="card-header">Search Users</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by username..." aria-label="Recipient's username">
                    <div class="input-group-append">
                        <button class="btn btn-primary" onclick="searchUsers()">Search</button>
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush" id="searchResults">
            </ul>
        </div>
    </div>

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
    .then(response => response.json())
    .then(data => {
        if (data.status === 'error') {
            alert(data.message);
        } else {
            alert('Friend request sent!');
        }
    })
    .catch(err => {
        alert('Failed to send friend request.');
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
function showMutualFriends(element, friendList) {
    const targetSpan = element.parentNode.querySelector('.mutualFriendsList');
    targetSpan.textContent = ""; // Clear the previous content
    targetSpan.textContent = "Amis en commun: " + friendList;

    element.style.display = 'none'; // Hide the clickable text to prevent repeated clicks
}



</script>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
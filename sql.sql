

-- Table des invitations
CREATE TABLE friend_requests (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    sender_id INT(11),
    receiver_id INT(11),
    status ENUM('PENDING', 'ACCEPTED', 'REJECTED')
);

-- Table des amis (relations acceptées)
CREATE TABLE friends (
    user_id INT(11),
    friend_id INT(11)
);

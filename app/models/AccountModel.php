<?php

class AccountModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($username, $fullname, $email, $phone, $address, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $emailVerifyToken = bin2hex(random_bytes(32));

        $query = "INSERT INTO account
                  (username, fullname, email, phone, address, password, role, status, email_verified, email_verify_token)
                  VALUES
                  (:username, :fullname, :email, :phone, :address, :password, 'user', 'active', 0, :email_verify_token)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email_verify_token', $emailVerifyToken);

        return $stmt->execute() ? $emailVerifyToken : false;
    }

    public function checkUsernameExists($username)
    {
        $query = "SELECT id FROM account WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function checkEmailExists($email)
    {
        $query = "SELECT id FROM account WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getAccountByUsername($username)
    {
        $query = "SELECT * FROM account WHERE username = :username OR email = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAccountByEmail($email)
    {
        $query = "SELECT * FROM account WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAccountById($id)
    {
        $query = "SELECT
                    id,
                    username,
                    fullname,
                    email,
                    phone,
                    address,
                    avatar,
                    role,
                    status,
                    email_verified,
                    created_at
                  FROM account
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAccountByVerifyToken($token)
    {
        $query = "SELECT *
                  FROM account
                  WHERE email_verify_token = :token
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyEmail($token)
    {
        $query = "UPDATE account
                  SET email_verified = 1,
                      email_verify_token = NULL
                  WHERE email_verify_token = :token";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);

        return $stmt->execute();
    }

    public function verifyGoogleAccount($id)
    {
        $query = "UPDATE account
                  SET email_verified = 1,
                      email_verify_token = NULL
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateProfile($id, $fullname, $email, $phone, $address, $avatar = null)
    {
        if (!empty($avatar)) {
            $query = "UPDATE account
                      SET fullname = :fullname,
                          email = :email,
                          phone = :phone,
                          address = :address,
                          avatar = :avatar
                      WHERE id = :id";
        } else {
            $query = "UPDATE account
                      SET fullname = :fullname,
                          email = :email,
                          phone = :phone,
                          address = :address
                      WHERE id = :id";
        }

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if (!empty($avatar)) {
            $stmt->bindParam(':avatar', $avatar);
        }

        return $stmt->execute();
    }

    public function changePassword($id, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $query = "UPDATE account
                  SET password = :password
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function saveResetToken($email, $token, $expire)
    {
        $query = "UPDATE account
                  SET reset_token = :token,
                      reset_token_expire = :expire
                  WHERE email = :email";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expire', $expire);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    public function getAccountByResetToken($token)
    {
        $query = "SELECT *
                  FROM account
                  WHERE reset_token = :token
                  AND reset_token_expire >= NOW()
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function resetPassword($token, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $query = "UPDATE account
                  SET password = :password,
                      reset_token = NULL,
                      reset_token_expire = NULL
                  WHERE reset_token = :token";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':token', $token);

        return $stmt->execute();
    }

    public function saveRememberToken($id, $token)
    {
        $query = "UPDATE account
                  SET remember_token = :token
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getAccountByRememberToken($token)
    {
        $query = "SELECT *
                  FROM account
                  WHERE remember_token = :token
                  AND status = 'active'
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function clearRememberToken($id)
    {
        $query = "UPDATE account
                  SET remember_token = NULL
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getAllUsers()
    {
        $query = "SELECT
                    id,
                    username,
                    fullname,
                    email,
                    phone,
                    address,
                    avatar,
                    role,
                    status,
                    email_verified,
                    created_at
                  FROM account
                  ORDER BY id DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function changeRole($userId, $role)
    {
        $query = "UPDATE account
                  SET role = :role
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateUserStatus($id, $status)
    {
        $query = "UPDATE account
                  SET status = :status
                  WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteUser($id)
    {
        $query = "DELETE FROM account WHERE id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
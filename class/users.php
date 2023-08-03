<?php
class Users
{

    // Connection
    private $conn;

    // Table
    private $db_table = "users";

    // Columns
    public $id;
    public $nom;
    public $date_creation;
    public $email;
    public $tel;
    public $adresse;
    public $code_postal;
    public $ville;

    // Db connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // GET ALL
    public function getUser()
    {
        $sqlQuery = "SELECT id,nom,date_creation,email,tel,adresse,code_postal,ville FROM " . $this->db_table . "";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }

    public function searchUser($searchTerm)
    {
        $sqlQuery = "SELECT id, nom, date_creation, email, tel, adresse, code_postal, ville 
                     FROM " . $this->db_table . " 
                     WHERE nom LIKE :searchTerm";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
        $stmt->execute();
        $result = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

    public function getIdUser()
    {
        $sqlQuery = "SELECT
                         id,
            nom,
            date_creation,
            email,tel,
            adresse,
            code_postal,
            ville
                      FROM
                        " . $this->db_table . "
                    WHERE 
                       id = ?
                    LIMIT 0,1";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(1, $this->id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->nom = $dataRow['nom'];
        $this->date_creation = $dataRow['date_creation'];
        $this->email = $dataRow['email'];
        $this->tel = $dataRow['tel'];
        $this->adresse = $dataRow['adresse'];
        $this->code_postal = $dataRow['code_postal'];
        $this->ville = $dataRow['ville'];

    }

    // UPDATE
    public function updateUser()
    {
        $sqlQuery = "UPDATE
                        " . $this->db_table . "
                    SET
                        nom = :nom, 
                        email = :email, 
                        tel = :tel, 
                        adresse = :adresse, 
                        code_postal = :code_postal,
                        ville = :ville
                    WHERE 
                        id = :id";

        $stmt = $this->conn->prepare($sqlQuery);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->tel = htmlspecialchars(strip_tags($this->tel));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));
        $this->code_postal = htmlspecialchars(strip_tags($this->code_postal));
        $this->ville = htmlspecialchars(strip_tags($this->ville));

        // bind data
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":tel", $this->tel);
        $stmt->bindParam(":adresse", $this->adresse);
        $stmt->bindParam(":code_postal", $this->code_postal);
        $stmt->bindParam(":ville", $this->ville);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>

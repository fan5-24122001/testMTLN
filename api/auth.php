<?php
// Set headers to enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Other headers to allow additional HTTP methods and headers if needed
header("Access-Control-Allow-Methods: OPTIONS");

include_once '../config/database.php';
include_once '../class/users.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        getUserById($_GET['id']);
    } else {
        getUsers();
    }
} 
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['searchTerm'])) {
        searchUsers($data['searchTerm']);
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Invalid request data."));
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        updateUser();
}

function getUsers()
{
    $items = new Users($GLOBALS['db']);

    $stmt = $items->getUser();
    $itemCount = $stmt->rowCount();

    if ($itemCount > 0) {
        $employeeArr = [];
        $employeeArr["code"] = 200;
        $employeeArr["message"] = "";
        $employeeArr["datas"] = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $e = array(
                "id" => $id,
                "nom" => $nom,
                "date_creation" => $date_creation,
                "email" => $email,
                "tel" => $tel,
                "adresse" => $adresse,
                "code_postal" => $code_postal,
                "ville" => $ville
            );

            array_push($employeeArr["datas"], $e);
        }

        $employeeArr["warnings"] = array();
        echo json_encode($employeeArr);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No record found."));
    }
}

function getUserById($id)
{
    $item = new Users($GLOBALS['db']);
    $item->id = $id;
    $item->getIdUser();

    if ($item) {
        $emp_arr = array(
            "id" =>  $item->id,
            "nom" => $item->nom,
            "date_creation" =>  $item->date_creation,
            "email" => $item->email,
            "tel" =>  $item->tel,
            "adresse" => $item->adresse,
            "code_postal" =>  $item->code_postal,
            "ville" =>  $item->ville,
           
        );

        http_response_code(200);
        echo json_encode($emp_arr);
    } else {
        http_response_code(404);
        echo json_encode("User not found.");
    }
}

function updateUser()
{
    $item = new Users($GLOBALS['db']);

    $data = json_decode(file_get_contents("php://input"));

    $item->id = $data->id;
    $item->nom = $data->nom;
    $item->email = $data->email;
    $item->tel = $data->tel;
    $item->adresse = $data->adresse;
    $item->code_postal = $data->code_postal;
    $item->ville = $data->ville;
    $item->date_creation = date('Y-m-d H:i:s');

    if ($item->updateUser()) {
        echo json_encode("User data updated.");
    } else {
        echo json_encode("Data could not be updated");
    }
}

function searchUsers($searchTerm)
{
    $item = new Users($GLOBALS['db']);
    $results = $item->searchUser($searchTerm);

    if (!empty($results)) {
        $response = array(
            "code" => 200,
            "message" => "Search results",
            "datas" => $results
        );
        http_response_code(200);
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No matching users found."));
    }
}
?>

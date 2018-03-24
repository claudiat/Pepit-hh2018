<?php
ob_start();
session_start();

if (isset($_SESSION['user']) != "") {
    header("Location: index.php");
}
include_once 'dbconnect.php';

if (isset($_POST['signup'])) {

    $uname = trim($_POST['uname']); // récupérer les données et supprimer les espaces
    $email = trim($_POST['email']);
    $upass = trim($_POST['pass']);

    // hash mdp SHA256;
    $password = hash('sha256', $upass);

    // check si mail existe
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $count = $result->num_rows;

    if ($count == 0) { // si email n'ai pas encore dans la db on add user


        $stmts = $conn->prepare("INSERT INTO users(username,email,password) VALUES(?, ?, ?)");
        $stmts->bind_param("sss", $uname, $email, $password);
        $res = $stmts->execute();//recupere resulta
        $stmts->close();

        $user_id = mysqli_insert_id($conn);
        if ($user_id > 0) {
            $_SESSION['user'] = $user_id; // init la session et redirection sur index
            if (isset($_SESSION['user'])) {
                print_r($_SESSION);
                header("Location: index.php");
                exit;
            }

        } else {
            $errTyp = "danger";
            $errMSG = "Une erreur est survenue, veuillez réesayer";
        }

    } else {
        $errTyp = "warning";
        $errMSG = "Email déja utilisé";
    }

}
?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
</head>
<body>

<div class="container">

    <div id="login-form">
        <form method="post" autocomplete="off">

            <div class="col-md-12">

                <div class="form-group">
                    <h2 class="">Inscription</h2>
                </div>

                <div class="form-group">
                    <hr/>
                </div>

                <?php
                if (isset($errMSG)) {

                    ?>
                    <div class="form-group">
                        <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                            <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                        <input type="text" name="uname" class="form-control" placeholder="Identifiant" required/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                        <input type="email" name="email" class="form-control" placeholder="Email" required/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                        <input type="password" name="pass" class="form-control" placeholder="Mot de Passe" required/>
                    </div>
                </div>

                <div class="checkbox">
                    <label><input type="checkbox" id="TOS" value="This"><a href="#">J'accepte les conditions d'utilisation</a></label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn    btn-block btn-primary" name="signup" id="reg">S'inscrire</button>
                </div>

                <div class="form-group">
                    <hr/>
                </div>

                <div class="form-group">
                    <a href="login.php" type="button" class="btn btn-block btn-success" name="btn-login">Connexion</a>
                </div>

            </div>

        </form>
    </div>

</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/tos.js"></script>

</body>
</html>

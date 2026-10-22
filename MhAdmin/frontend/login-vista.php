<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login / Register MH</title>

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">


    <link rel="stylesheet" href="icon/style.css">
    <link rel="stylesheet" href="css/style2.css">


    <style>
        
    .btnlogin {
    width: 300px;
    height: 50px;
    display: block;
    margin: auto;
    margin-top: 50px;
    color: white;
    background: #cd9341;
    border-style: none;
    outline: 0px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
        }
    .btnlogin label{
    font-size: 20px;
    position: relative;
    top: -4px;
    color: #cd9341;
    }
    .btnlogin button{
    width: 300px;
    height: 50px;
    display: block;
    margin: auto;
    margin-top: 50px;
    color: white;
    background: #cd9341;
    border-style: none;
    outline: 0px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;

}
.btnlogin button:hover{
    opacity: .9;
}
    </style>
</head>

<body>

    <div class="container-form">
        <div class="header">
            <div class="logo-title">
                <img src="image/icono.png" alt="">
                <h2>MH Admin</h2>
            </div>
            <div class="menu">
                <a href="login.php">
                    <li class="module-login active" required title="Click si tienes una cuenta">Iniciar Sesion</li>
                </a>
                <a href="register.php">
                    <li class="module-register" required title="Click si no tienes una cuenta">Registrarse</li>
                </a>
            </div>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="form">
            <div class="welcome-form">
                <h1></h1>
                <h2>MH </h2>
            </div>
            <div class="user line-input">
                <label class="lnr lnr-user"></label>
                <input type="text" placeholder="Nombre Usuario" name="usuario" minlegth="5" maxlength="15">
            </div>
            <div class="password line-input">
                <label class="lnr lnr-lock"></label>
                <input type="password" placeholder="Contraseña" name="clave">
            </div>

            <?php if (!empty($error)) : ?>
                <div class="mensaje">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <button type="submit">Entrar</button>
            
        </form>
        
        <button required title="Click solo si eres administrador" onclick="window.location.href='./admin/index.php'" class="btnlogin">¿Eres administrador?</button>
       
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/script.js"></script>
</body>

</html>